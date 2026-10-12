<?php

declare(strict_types=1);

namespace IXP\Utils;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use IXP\Contracts\IrrQuerier;
use IXP\Exceptions\ConfigurationException;
use IXP\Exceptions\ProcessException;
use IXP\Exceptions\GeneralException;

/**
 * Base class providing common functionality between bgpq3 and bgpq4
 * IrrQuerier implementations
 *
 * Used to reduce
 */
abstract class BgpqBase implements IrrQuerier
{
    /**
     * Whois server to query
     * @var string|null
     */
    protected ?string $whois;

    /**
     * Whois server sources
     * @var string|null
     */
    protected ?string $sources;

    /**
     * Path to the command to execute
     * @var string
     */
    protected string $path;

    /**
     * Name of the command being used - for logs!
     * @var string
     */
    protected string $utility;

    /**
     * @throws ConfigurationException
     */
    protected function validatePath(string $path): void
    {
        if ( file_exists( $path ) ) {
            if ( ! (is_file( $path ) && is_executable( $path ) ) ) {
                throw new ConfigurationException( 'The configured IXP_IRRDB_' . strtoupper($this->utility) . '_PATH provided was not a valid executable file.' );
            }
        } else if ( preg_match( '/^[A-Za-z0-9._-]+$/', $path ) ) {
            // see if we can find a location in the command line $PATH
            $paths = explode( PATH_SEPARATOR, getenv( "PATH" ) );

            $located = false;
            foreach ( $paths as $pathElem ) {
                if ( is_executable( rtrim( $pathElem, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $path ) ) {
                    $located = true;
                    break;
                }
            }

            if ( !$located ) {
                throw new ConfigurationException( 'The IXP_IRRDB_' . strtoupper($this->utility) . '_PATH provided was not found in the system $PATH.' );
            }
        } else {
            throw new ConfigurationException( 'The IXP_IRRDB_' . strtoupper($this->utility) . '_PATH does not exist - provide an absolute path, or name of an executable in $PATH' );
        }
    }

    /**
     * Get the IRRDB prefix list (based on route[6]: objects) for a given AS
     * number / macro and protocol.
     *
     * Returns an array of prefixes (or empty array).
     *
     * @param string $asmacro As number (of the form as1234) or AS macro
     * @param int $proto The IP protocol - 4 or 6.
     *
     * @return array The array of prefixes (or empty array).
     *
     * @throws GeneralException On a JSON decoding error
     * @throws ProcessException if process returns non-zero exit code
     *
     * @psalm-return list{0?: mixed,...}
     */
    #[\Override]
    public function getPrefixList( string $asmacro, int $proto = 4 ): array
    {
        $minSubnetSize = config( 'ixp.irrdb.min_v' . $proto . '_subnet_size' );

        $json = $this->execute( [ '-l', 'pl', '-j', '-m', $minSubnetSize, $asmacro ], $proto );
        $array = json_decode( $json, true );

        if( $array === null ){
            throw new GeneralException( "Could not decode JSON response from " . $this->utility );
        }

        if( !isset( $array[ 'pl' ] ) ){
            throw new GeneralException( "Named prefix list [pl] expected in decoded JSON but not found!" );
        }

        $prefixes = [];
        // we're going to ignore the 'exact' for now.
        foreach( $array[ 'pl' ] as $ar ){
            $prefixes[] = $ar['prefix'];
        }

        return $prefixes;
    }

    /**
     * Get the IRRDB ASN list (based on route[6]: objects) for a given AS
     * number / macro and protocol.
     *
     * Returns an array of ASNs that may appear in any as path for the
     * route paths (or empty array).
     *
     * @param string    $asmacro As number (of the form as1234) or AS macro
     * @param int       $proto The IP protocol - 4 or 6.
     *
     * @return array The array of prefixes (or empty array).
     *
     * @throws GeneralException On a JSON decoding error
     * @throws ProcessException if process returns non-zero exit code
     *
     * @psalm-return list<mixed>
     */
    #[\Override]
    public function getAsnList( string $asmacro, int $proto = 4 ): array
    {
        // -6 makes no sense with as-path (-f/-G) generation
        $json = $this->execute( [ '-3j', '-l', 'pl', '-f', '999', $asmacro ] );
        $array = json_decode( $json, true );

        if( $array === null ){
            throw new GeneralException( "Could not decode JSON response from BGPQ when fetching ASN list" );
        }

        if( !isset( $array[ 'pl' ] ) ){
            throw new GeneralException( "Named prefix list [pl] expected in decoded JSON but not found when fetching ASN list!" );
        }

        $asns = [];

        foreach( $array[ 'pl' ] as $asn ){
            $asns[] = $asn;
        }

        return $asns;
    }

    /**
     * Takes a command and protocol, appends optional parameters and
     * returns the full command to execute
     */
    protected function assembleFullCommand( array $cmd, int $proto ): array
    {
        if( $this->whois ){
            $cmd = array_merge( [ '-h', $this->whois ], $cmd );
        }

        if( $this->sources ){
            $cmd = array_merge( [ '-S', $this->sources ], $cmd );
        }

        if( $proto === 6 ){
            $cmd = array_merge( [ '-6' ], $cmd );
        }

        return array_merge( [ $this->path ], $cmd );
    }

    /**
     * @param array $cmd
     * @return string
     * @throws ProcessException
     */
    protected function executeCommand( array $cmd ): string
    {
        $process = Process::start($cmd);
        $executedCommandForLogs = $process->command();
        Log::debug('[' . $this->utility . '] executing: ' . $executedCommandForLogs );
        $process = $process->wait();

        if( $process->failed() ){
            throw new ProcessException( 'Error executing command with: ' . $executedCommandForLogs );
        }
        return $process->output();
    }

    /**
     * @param array $cmd
     * @param int $proto
     * @return string
     * @throws ProcessException
     */
    protected function execute( array $cmd, int $proto = 4 ): string
    {
        $cmd = $this->assembleFullCommand( $cmd, $proto);
        return $this->executeCommand($cmd);
    }

    /**
     * The whois server to query
     *
     * @param string $whois The whois server to query
     *
     * @return static For fluent interfaces
     */
    #[\Override]
    public function setWhois( string $whois ): static
    {
        $this->whois = $whois;
        return $this;
    }

    /**
     * The whois server sources
     *
     * @param string $sources The whois server sources
     *
     * @return static For fluent interfaces
     */
    #[\Override]
    public function setSources( string $sources ): static
    {
        $this->sources = $sources;
        return $this;
    }

    /**
     * The executable path to the utility
     *
     * @param string $path The executable path to the utility
     *
     * @return static For fluent interfaces
     */
    public function setPath( string $path ): static
    {
        $this->path = $path;

        return $this;
    }
}