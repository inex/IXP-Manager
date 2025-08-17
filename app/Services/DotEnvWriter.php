<?php

namespace IXP\Services;

/*
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 *
 * Based on code from https://github.com/MirazMac/DotEnvWriter with MIT license.
 *
 * A PHP library to write values to .env files.
 *
 */


use InvalidArgumentException;
use const LOCK_EX;

class DotEnvWriter
{
    /**
     * The .env file content
     *
     * @var        string
     */
    protected string $content = "";

    /**
     * Path to the .env file
     *
     * @var        string|null
     */
    protected ?string $sourceFile = null;

    /**
     * Parsed variables, just for reference, not properly type-casted
     *
     * @var        array
     */
    protected array $variables = [];

    /**
     * Stores if a change was made
     *
     * @var        bool
     */
    protected bool $changed = false;

    /**
     * Constructs a new instance.
     *
     * @param string|null $sourceFile The environment path
     * @throws     InvalidArgumentException  If the file is missing
     */
    public function __construct( ?string $sourceFile = null )
    {
        if( $sourceFile === null ) {
            $sourceFile = base_path( '.env' );
        }

        if( !file_exists($sourceFile) || !is_readable($sourceFile) ) {
            throw new InvalidArgumentException("File '$sourceFile' does not exist or is not readable");
        }

        if( !is_writable($sourceFile) ) {
            throw new InvalidArgumentException("File '$sourceFile' is not writable");
        }

        $this->sourceFile = $sourceFile;
        $this->content = file_get_contents( $sourceFile );
        $this->parse();
    }

    /**
     * Set the value of an environment variable,
     * updated if exists and enabled if disabled,
     * added if it doesn't exist
     *
     * @param string $key The key
     * @param string $value The value
     * @param string|null $description The description (remarked line before variable, only on new variable)
     *
     * @throws InvalidArgumentException  If a new key contains invalid characters
     */
    public function set( string $key, string $value, string|null $description = null ): static
    {
        // If the key exists, replace its value
        if( $lineId = $this->findVariable( $key ) ) {
            $this->variables[ $lineId ][ "value" ] = $this->formatValue( $value );
            $this->variables[ $lineId ][ "status" ] = true;
            $this->variables[ $lineId ][ "changed" ] = true;
            $this->changed = true;
        } else {
            if( $description ) {
                $this->variables[] = [
                    "key"     => null,
                    "value"   => "# " . $description,
                    "status"  => false,
                    "changed" => true,
                ];
            }
            $this->variables[] = [
                "key"     => $key,
                "value"   => $this->formatValue( $value ),
                "status"  => true,
                "changed" => true,
            ];
            $this->changed = true;
        }

        return $this;
    }

    /**
     * Set more values at once
     *
     * @param array $values The values as key => value pairs
     */
    public function setValues( array $values ): static
    {
        foreach( $values as $key => $value ) {
            $this->set( $key, $value );
        }

        return $this;
    }

    /**
     * Delete an environment variable if present
     *
     * @param string $key The key
     * @param bool $removeDescription Remove the description before the variable
     */
    public function delete( string $key, bool $removeDescription = false ): static
    {
        if( $lineId = $this->findVariable( $key ) ) {
            unset( $this->variables[ $lineId ] );
            if( $removeDescription && $this->variables[ $lineId - 1 ][ "key" ] === null ) {
                unset( $this->variables[ $lineId - 1 ] );
            }
            $this->changed = true;
        }

        return $this;
    }

    /**
     * Comment out an environment variable if present
     *
     * @param string $key The key
     */
    public function disable( string $key ): static
    {
        if( $lineId = $this->findVariable( $key ) ) {
            $this->variables[ $lineId ][ "status" ] = false;
            $this->variables[ $lineId ][ "changed" ] = true;
            $this->changed = true;
        }

        return $this;
    }

    /**
     * Uncomment out an environment variable if present
     *
     * @param string $key The key
     */
    public function enable( string $key ): static
    {
        if( $lineId = $this->findVariable( $key ) ) {
            $this->variables[ $lineId ][ "status" ] = true;
            $this->variables[ $lineId ][ "changed" ] = true;
            $this->changed = true;
        }

        return $this;
    }



    /**
     * States if one or more values has changed
     *
     * @return     bool
     */
    public function hasChanged(): bool
    {
        return $this->changed;
    }

    /**
     * Returns the id of the variable array or the full content for a variable is present
     *
     * @param string $key The key
     * @param bool $full Give full content of the found variable
     *
     * @return array|false|int
     *
     * @psalm-return array<int, mixed>|false|int
     */
    public function get( string $key, bool $full = false ): array|false|int
    {
        $lineId = $this->findVariable( $key );
        if ($full) {
            if($lineId !== false) {
                return [$lineId => $this->variables[ $lineId ]];
            } else {
                return false;
            }
        } else {
            return $lineId;
        }
    }

    /**
     * Returns all full variable collection parsed
     *
     * @return     array
     */
    public function getAll(): array
    {
        return $this->variables;
    }

    /**
     * Returns the variable collection without empty lines, key as env key
     *
     * @return array[]
     *
     * @psalm-return array<array{value: mixed, status: mixed}>
     */
    public function getVariables(): array
    {
        $varList = [];
        foreach($this->variables as $var) {
            if(!is_null($var["key"])) {
                $varList[$var["key"]] = ["value" => $var["value"], "status" => $var["status"]];
            }
        }
        return $varList;
    }

    /**
     * Write the contents to the env file
     *
     * @param bool $force By default, we only write when something has changed, but you can force to write the file
     *
     * @return void
     */
    public function write( bool $force = false ): void
    {
        if( $this->hasChanged() || $force ) {

            $content = "";
            foreach($this->variables as $lineId => $variable) {
                if(is_null($variable["key"])) {
                    $pre = "";
                    if( !str_starts_with( $variable[ "value" ], "#") && trim($variable[ "value" ]) !== '' ) {
                        $pre = "# ";
                    }
                    $content .= $pre.$variable["value"]."\n";
                } else if($variable["status"] === false) {
                    $content .= "# ".$variable["key"]."=".$this->escapeValue($variable["value"])."\n";
                } else {
                    $content .= $variable["key"]."=".$this->escapeValue($variable["value"])."\n";
                }
            }
            file_put_contents($this->sourceFile, $content, LOCK_EX);
        }
    }

    /**
     * Check if a variable exists or not
     *
     * @param string $key The key
     * @return     bool
     */
    public function exists( string $key ): bool
    {
        return in_array( $key, array_column( $this->variables, 'key' ) );
    }

    /**
     * Find a variable line id in the $variables array
     *
     * @param string $key
     * @return false|int
     */
    protected function findVariable( string $key ): false|int
    {
        $result = false;
        foreach($this->variables as $index => $variable) {
            if($variable["key"] === $key) { $result = $index; break; }
        }
        return $result;
    }

    /**
     * Determines whether the specified key is valid name for .env files.
     *
     * @param string $key The key
     *
     * @return     bool
     */
    protected function isValidName( string $key ): bool
    {
        return (bool)preg_match( '/^[\w\.]+$/', $key );
    }

    /**
     * Parses the environment file line by line and store the variables
     */
    protected function parse(): void
    {
        $lines = preg_split( '/\r\n|\r|\n/', $this->content );

        foreach( $lines as $line ) {

            $line = trim($line);

            if( mb_strlen( $line ) && !( mb_strpos( $line, '#' ) === 0 ) ) {
                [ $key, $value ] = explode( '=', $line );
                $this->variables[] = [
                    "key"     => $key,
                    "value"   => $this->formatValue( $value ),
                    "status"  => true,
                    "changed" => false,
                ];
            } else {
                $validVariable = preg_match( "/^#\s{0,}(\w+)=(.+)$/", $line, $matches );
                if( $validVariable ) {
                    $this->variables[] = [
                        "key"     => $matches[ 1 ],
                        "value"   => $this->formatValue( $matches[ 2 ] ),
                        "status"  => false,
                        "changed" => false,
                    ];
                } else {
                    $this->variables[] = [
                        "key"     => null,
                        "value"   => $line,
                        "status"  => false,
                        "changed" => false,
                    ];
                }
            }
        }
    }

    /**
     * Strips quotes from the values when reading
     *
     * @param string $value The value
     * @return     string
     */
    protected function stripQuotes( string $value ): string
    {
        return preg_replace( '/^(\'(.*)\'|"(.*)")$/u', '$2$3', $value ) ?? '';
    }

    /**
     * Formats the value for human friendly output
     *
     * @param string $value The value
     * @return     string
     */
    protected function formatValue( string $value ): string
    {
        $value = trim( explode( '#', trim( $value ) )[ 0 ] );

        return htmlspecialchars($this->stripQuotes( $value ), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Escapes the value before writing to the contents
     *
     * @param string $value The value
     * @return     string
     */
    protected function escapeValue( string $value ): string
    {
        if( $value !== '' ) {
            $value = '"' . htmlspecialchars_decode( $value ) . '"';
        }

        return $value;
    }
}