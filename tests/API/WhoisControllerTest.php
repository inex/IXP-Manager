<?php

namespace Tests\API;

use IXP\Services\PeeringDb;
use IXP\Utils\Whois\Whois;
use IXP\Utils\Whois\WhoisResolver;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class WhoisControllerTest extends TestCase
{
    public function testAsnInexPdb(): void
    {
        $this->mock(PeeringDb::class, function (MockInterface $mock) {
            $mock->expects('getNetworkByAsn')->with('2128')
                ->andReturn(json_decode(file_get_contents('data/ci/known-good/peeringdb/getnetwork.inex.json'), associative: true));
            $mock
                ->expects('netAsAscii')
                ->andReturn(file_get_contents('data/ci/known-good/peeringdb/netAsAscii.inex.txt'));
        });

        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/aut-num/2128' );
        $response->assertStatus( 200 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $this->assertEquals(file_get_contents('data/ci/known-good/peeringdb/netAsAscii.inex.txt'), $response->getContent());
    }

    public function testAsnInexWhois(): void
    {
        $this->mock(PeeringDb::class, function (MockInterface $mock) {
            $mock->expects('getNetworkByAsn')->with('2128')
                ->set('status', 404)
                ->set('error', "No network with AS2128 found in PeeringDB")
                ->andReturn(false);
        });

        $this->mock(WhoisResolver::class, function (MockInterface $mock) {
            $mock->expects('get')->with('asn2')
                ->andReturn(
                    Mockery::mock(Whois::class, function (MockInterface $mock) {
                        $mock->expects('whois')->with('AS2128')
                            ->andReturn("AS Name\nINEX Internet Neutral Exchange Association Company Limited By Guarantee, IE\n");
                    })
                );
        });

        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/aut-num/2128' );
        $response->assertStatus( 200 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $this->assertEquals("ASN not registered in PeeringDB. Trying ". config( 'ixp_api.whois.asn2.host' ) . ":\n\nAS Name\nINEX Internet Neutral Exchange Association Company Limited By Guarantee, IE\n", $response->getContent());
    }

    public function testAsnNotFound(): void
    {
        $this->mock(PeeringDb::class, function (MockInterface $mock) {
            $mock->expects('getNetworkByAsn')->with('99999999')
                ->set('status', 500)
                ->set('error', "Server Error")
                ->andReturn(false);
        });

        $this->mock(WhoisResolver::class, function (MockInterface $mock) {
            $mock->expects('get')->with('asn2')
                ->andReturn(
                    Mockery::mock(Whois::class, function (MockInterface $mock) {
                        $mock->expects('whois')->with('AS99999999')
                            ->andReturn("AS Name\nNO_NAME\n");
                    })
                );
        });

        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/aut-num/99999999' );
        $response->assertStatus( 200 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $this->assertEquals("Querying PeeringDB failed:\n\nError:Server Error\n\nTrying ". config( 'ixp_api.whois.asn2.host' ) . ":\n\nAS Name\nNO_NAME\n", $response->getContent());
    }

    public function testPrefix(): void
    {
        $this->mock(WhoisResolver::class, function (MockInterface $mock) {
            $mock->expects('get')->with('prefix')
                ->andReturn(
                    Mockery::mock(Whois::class, function (MockInterface $mock) {
                        $mock->expects('whois')->with('8.8.8.8/32')
                            ->andReturn(file_get_contents('data/ci/known-good/whois-prefix.8.8.8.8.txt'));
                    })
                );
        });

        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/prefix-whois/8.8.8.8/32' );
        $response->assertStatus( 200 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $this->assertEquals(file_get_contents('data/ci/known-good/whois-prefix.8.8.8.8.txt'), $response->getContent());
    }

    public function testPrefixWithoutMask(): void
    {
        $this->mock(WhoisResolver::class, function (MockInterface $mock) {
            $mock->expects('get')->with('prefix')
                ->andReturn(
                    Mockery::mock(Whois::class, function (MockInterface $mock) {
                        $mock->expects('whois')->with('8.8.8.8')
                            ->andReturn(file_get_contents('data/ci/known-good/whois-prefix.8.8.8.8.txt'));
                    })
                );
        });

        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/prefix-whois/8.8.8.8' );
        $response->assertStatus( 200 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $this->assertEquals(file_get_contents('data/ci/known-good/whois-prefix.8.8.8.8.txt'), $response->getContent());
    }

}