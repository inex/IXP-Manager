<?php

declare(strict_types=1);

namespace Tests\Utils;

use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;
use IXP\Exceptions\ConfigurationException;
use IXP\Exceptions\GeneralException;
use IXP\Exceptions\ProcessException;
use IXP\Utils\Bgpq3;
use Tests\TestCase;

class Bgpq3Test extends TestCase
{
    public function testBgpPathRequired()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage("The IXP_IRRDB_BGPQ3_PATH does not exist - provide an absolute path, or name of an executable in \$PATH");
        new Bgpq3( "/some/file" );
    }

    public function testExecutablesInPathAllowed()
    {
        Process::fake( [
            "'bgpq3' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'" => Process::result( $this->getAsnSampleData() ),
        ] );
        $bgpq3 = new Bgpq3( "bgpq3" );
        $bgpq3->getAsnList("AS-HEANET");
        $this->assertRan("'bgpq3' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");
    }

    public function testUnknownExecutableFromPATH()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage("The IXP_IRRDB_BGPQ3_PATH provided was not found in the system \$PATH.");

        new Bgpq3( "unknowncommand" );
    }

    public function testNonFileExecutable()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage("The configured IXP_IRRDB_BGPQ3_PATH provided was not a valid executable file.");

        new Bgpq3( "/" );
    }

    public function testFileNotExecutable()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage("The configured IXP_IRRDB_BGPQ3_PATH provided was not a valid executable file.");

        new Bgpq3( "/etc/group" );
    }

    /**
     * This test ensures we load the right bgpq3 executable from the configuration when the container creates a Bgpq3
     */
    public function testAppResolvesBgpPathFromConfig()
    {
        Process::fake( [
            '*' => Process::result( $this->getAsnSampleData() )
        ] );

        app(Bgpq3::class)->getAsnList("AS-HEANET");

        $configProgram = config( 'ixp.irrdb.bgpq3.path' ) ;
        $this->assertRan("'$configProgram' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");
    }

    public function testSetPath()
    {
        Process::fake( [
            '*' => Process::result( $this->getAsnSampleData() )
        ] );

        $program = config('ixp.irrdb.bgpq3.path');
        $bgp = new Bgpq3( $program );
        $bgp->getAsnList("AS-HEANET");
        $this->assertRan("'$program' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");

        // path can be modified
        $bgp->setPath("/bin/bgpq3")
            ->getAsnList("AS-HEANET");
        $this->assertRan("'/bin/bgpq3' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");
    }

    public function testSetSources()
    {
        // no flags provided
        Process::fake( [
            '*' => Process::result( $this->getAsnSampleData() )
        ] );

        $program = config('ixp.irrdb.bgpq3.path');
        $bgp = new Bgpq3( $program );
        $bgp->getAsnList("AS-HEANET");
        $this->assertRan("'$program' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");

        // Now the -S flag is set
        $bgp->setSources("RADB")
            ->getAsnList("AS-HEANET");
        $this->assertRan("'$program' '-S' 'RADB' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");

        // can modify sources
        $bgp->setSources("RIPE")
            ->getAsnList("AS-HEANET");
        $this->assertRan("'$program' '-S' 'RIPE' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");

        // can be unset
        $bgp->setSources("")
            ->getAsnList("AS-HEANET");
        $this->assertRan("'$program' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'", 2);
    }

    public function testSetWhois()
    {
        // no -h flag
        Process::fake( [
            '*' => Process::result( $this->getAsnSampleData() )
        ] );

        $program = config('ixp.irrdb.bgpq3.path');
        $bgp = new Bgpq3( $program );
        $bgp->getAsnList("AS-HEANET");
        $this->assertRan("'$program' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");

        // now the -h flag is set
        $bgp->setWhois("whois.peeringdb.com")
            ->getAsnList("AS-HEANET");
        $this->assertRan("'$program' '-h' 'whois.peeringdb.com' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");

        // can be unset
        $bgp->setWhois("")
            ->getAsnList("AS-HEANET");
        $this->assertRan("'$program' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'", 2);
    }

    public function testGetAsnList()
    {
        // test command without specific flags..
        Process::fake( [
            '*' => Process::result( $this->getAsnSampleData() )
        ] );

        $program = config('ixp.irrdb.bgpq3.path');
        $bgp = new Bgpq3( $program );
        $this->assertEquals([112, 1213, 1921, 2128, 2850, 42310], $bgp->getAsnList("AS-HEANET"));
        $this->assertRan("'$program' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");

        // test command when invoked with whois + sources
        $bgp->setWhois("whois.radb.net")
            ->setSources("RIPE");
        $this->assertEquals([112, 1213, 1921, 2128, 2850, 42310], $bgp->getAsnList("AS-HEANET"));
        $this->assertRan("'$program' '-S' 'RIPE' '-h' 'whois.radb.net' '-3j' '-l' 'pl' '-f' '999' 'AS-HEANET'");
    }

    public function testGetAsnListJsonError()
    {
        Process::fake([
            "*" => Process::result( '{' ),
        ]);

        $bgp = new Bgpq3( config('ixp.irrdb.bgpq3.path') );
        $this->expectException(GeneralException::class);
        $this->expectExceptionMessage("Could not decode JSON response from BGPQ when fetching ASN list");
        $bgp->getAsnList("AS-HEANET");
    }

    public function testGetAsnListMissingPl()
    {
        Process::fake([
            "*" => Process::result( '{}' ),
        ]);

        $bgp = new Bgpq3( config('ixp.irrdb.bgpq3.path') );
        $this->expectException(GeneralException::class);
        $this->expectExceptionMessage("Named prefix list [pl] expected in decoded JSON but not found when fetching ASN list!");
        $bgp->getAsnList("AS-HEANET");
    }

    public function testGetPrefixListDefaultProto()
    {
        // -F emits one "network/masklen" per line (see BgpqBase::getPrefixList).
        Process::fake([
            "*" => Process::result( $this->getPrefixSampleDataF( 4 ) ),
        ]);

        $expectingPrefixes = $this->getExpectedPrefixes( 4 );

        $program = config('ixp.irrdb.bgpq3.path');
        $bgp = new Bgpq3( $program );
        $this->assertEquals( $expectingPrefixes, $bgp->getPrefixList( "AS-HEANET" ) );
        $this->assertRan("'$program' '-F' '%n/%l\n' '-m' '24' 'AS-HEANET'");

        // test command when invoked with whois + sources
        $bgp->setWhois( "whois.radb.net" );
        $bgp->setSources( "RIPE" );
        $this->assertEquals( $expectingPrefixes, $bgp->getPrefixList( "AS-HEANET" ) );
        $this->assertRan("'$program' '-S' 'RIPE' '-h' 'whois.radb.net' '-F' '%n/%l\n' '-m' '24' 'AS-HEANET'");
    }

    public function testGetPrefixListV4()
    {
        Process::fake([
            "*" => Process::result( $this->getPrefixSampleDataF( 4 ) ),
        ]);

        $program = config('ixp.irrdb.bgpq3.path');
        $bgp = new Bgpq3( $program );
        $this->assertEquals($this->getExpectedPrefixes( 4 ), $bgp->getPrefixList("AS-HEANET", 4));
        $this->assertRan("'$program' '-F' '%n/%l\n' '-m' '24' 'AS-HEANET'");
    }

    public function testGetPrefixListV6()
    {
        Process::fake([
            "*" => Process::result( $this->getPrefixSampleDataF( 6 ) ),
        ]);

        $program = config('ixp.irrdb.bgpq3.path');
        $bgp = new Bgpq3( $program );
        $this->assertEquals($this->getExpectedPrefixes( 6 ), $bgp->getPrefixList("AS-HEANET", 6));
        $this->assertRan("'$program' '-6' '-F' '%n/%l\n' '-m' '48' 'AS-HEANET'");
    }

    /**
     * getPrefixList must split on any line ending (\R) and drop empties, so trailing
     * newlines, blank lines and CR/LF from bgpq never yield phantom prefixes.
     */
    public function testGetPrefixListParsesFFormat()
    {
        Process::fake([
            "*" => Process::result( "10.0.0.0/24\r\n10.0.1.0/24\n\n192.0.2.0/24\n" ),
        ]);

        $bgp = new Bgpq3( config('ixp.irrdb.bgpq3.path') );
        $this->assertEquals(
            [ '10.0.0.0/24', '10.0.1.0/24', '192.0.2.0/24' ],
            $bgp->getPrefixList("AS-HEANET")
        );
    }

    public function testProcessFailure()
    {
        Process::fake([
            "*" => Process::result('', '', 1),
        ]);

        $program = config('ixp.irrdb.bgpq3.path');
        $bgp = new Bgpq3( $program );
        $this->expectException(ProcessException::class);
        $this->expectExceptionMessage("Error executing command with: '$program' '-F' '%n/%l\n' '-m' '24' 'AS-HEANET'" );
        $bgp->getPrefixList("AS-HEANET");
    }

    public function testLiveUsingPATH()
    {
        $bgpq3 = new Bgpq3( "bgpq3" );
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850, 42310 ], $bgpq3->getAsnList("AS-HEANET") );
    }

    public function testLiveGetAsns()
    {
        $bgp = new Bgpq3( config( 'ixp.irrdb.bgpq3.path' ) );
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850, 42310 ], $bgp->getAsnList( "AS-HEANET" ) );
    }

    public function testLiveGetPrefixesV4()
    {
        $bgp = new Bgpq3( config('ixp.irrdb.bgpq3.path') );
        $prefixes = $bgp->getPrefixList("AS-HEANET");
        $this->assertTrue(in_array("134.226.0.0/16", $prefixes));
        $this->assertTrue(in_array("140.203.0.0/16", $prefixes));
    }

    public function testLiveGetPrefixesV6()
    {
        $bgp = new Bgpq3( config('ixp.irrdb.bgpq3.path') );
        $prefixes = $bgp->getPrefixList("AS-HEANET", 6);
        $this->assertTrue(in_array("2001:770::/32", $prefixes));
        $this->assertTrue(in_array("2a01:4b0::/32", $prefixes));
    }

    /**
     * PendingProcess has the arguments provided to Process::start/Process::run
     * whereas ProcessResult has the assembled command - use that
     */
    private function assertRan(string $expectedCommand, int $times = 1): void
    {
        Process::assertRanTimes(fn (PendingProcess $proc, ProcessResult $result) => $result->command() === $expectedCommand, $times);
    }

    /**
     * Extract a list of prefixes from the test data source, for a given protocol
     * @param int $proto
     * @return array
     */
    private function getExpectedPrefixes(int $proto): array
    {
        return array_column( json_decode( file_get_contents( "data/ci/known-good/bgpq3/heanet-prefixes-v" . $proto . ".json" ), true )[ 'pl' ], 'prefix' );
    }

    private function getAsnSampleData(): string
    {
        return \file_get_contents("data/ci/known-good/bgpq3/heanet-asns.json");
    }

    private function getPrefixSampleDataF( int $proto ): string
    {
        // Reproduce bgpq3 -F '%n/%l\n' output from the known-good prefix list.
        return implode( "\n", $this->getExpectedPrefixes( $proto ) ) . "\n";
    }
}