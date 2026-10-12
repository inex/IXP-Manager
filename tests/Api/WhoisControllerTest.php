<?php

namespace Tests\Api;

use IXP\Models\Asn;
use IXP\Services\PeeringDb;
use IXP\Utils\Whois\Whois;
use IXP\Utils\Whois\WhoisResolver;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class WhoisControllerTest extends TestCase
{
    protected array $deleteModels = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteModels = [];
    }

    protected function tearDown(): void
    {
        foreach ($this->deleteModels as $model) {
            $model->delete();
        }
        unset($this->deleteModels);
        parent::tearDown();
    }

    public function testAsnLookup(): void
    {
        $this->deleteModels[] = Asn::create(['asn' => 2128, 'name' => 'INEX Internet Neutral Exchange Association Company Limited By Guarantee, IE', 'class' => 'Unknown', 'country_code' => 'IE']);

        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/aut-num/2128' );
        $response->assertStatus( 200 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $expecting  = "Number  : 2128\n";
        $expecting .= "Name    : INEX Internet Neutral Exchange Association Company Limited By Guarantee, IE\n";
        $expecting .= "Class:  : Unknown\n";
        $expecting .= "Country : IE\n";
        $this->assertEquals($expecting, $response->getContent());
    }

    public function testAsnNotFound(): void
    {
        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/aut-num/99999999' );
        $response->assertStatus( 404 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $this->assertEquals("ASN not found in store", $response->getContent());
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