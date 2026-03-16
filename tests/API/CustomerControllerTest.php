<?php

namespace API;


use IXP\Services\PeeringDb;
use Mockery\MockInterface;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    private const string API_KEY = 'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC9';

    private function withKey(): static
    {
        return $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY );
    }

    public function testSwitches_UnknownPatchPanel()
    {
        $response = $this->withKey()->post( "api/v4/customer/switches", [
            "patch_panel_id" => 9999,
        ] );
        $response->assertStatus( 404 );
        $response->assertSeeText( "Not Found" );
    }

    public function testQueryDbWithAsn()
    {
        $heanetAsnLookupKnownGood = file_get_contents( base_path( 'data/ci/known-good/peeringdb/heanet.info.json' ) );
        $mockResponseData = json_decode( $heanetAsnLookupKnownGood, true );

        $this->partialMock( PeeringDb::class, function ( MockInterface $mock ) use ( $mockResponseData ) {
            $mock->expects( 'getNetworkByASN' )
                ->with( '1213' )
                ->andReturn( $mockResponseData );
        });

        $response = $this->withKey()->get( "api/v4/customer/query-peeringdb/asn/1213" );
        $response->assertStatus( 200 );
        $response->assertExactJson( [
            "net" => $mockResponseData
        ] );
    }

    public function testQueryDbWithAsn_Failure()
    {
        $this->partialMock( PeeringDb::class, function ( MockInterface $mock ) {
            $mock->expects( 'getNetworkByASN' )
                ->with( '0' )
                ->andReturn( false );
            $mock->error = "No network with AS0 found in PeeringDB";
            $mock->status = 404;
        });

        $response = $this->withKey()->get( "api/v4/customer/query-peeringdb/asn/0" );
        $response->assertStatus( 200 );
        $response->assertExactJson( [
            "error" => "No network with AS0 found in PeeringDB"
        ] );
    }

    public function testByVlanAndProtocol()
    {
        $this->post( "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $responseVlan1 = $this->post( "api/v4/customer/by-vlan-and-protocol", [
            'vlanid' => 1,
            'protocol' => 4
        ] );

        $responseVlan1->assertStatus( 200 );
        $jsonVlan1 = $responseVlan1->json();
        $this->assertArrayHasKey( "listCustomers", $jsonVlan1 );
        $this->assertCount( 3, $jsonVlan1['listCustomers'] );

        $keyedByIdVlan1 = collect( $jsonVlan1['listCustomers'] )->keyBy( 'id' );
        $this->assertArrayHasKey( 2, $keyedByIdVlan1 );
        $this->assertEquals("HEAnet", $keyedByIdVlan1[2]['name']);
        $this->assertArrayHasKey( 3, $keyedByIdVlan1 );
        $this->assertEquals("PCH DNS", $keyedByIdVlan1[3]['name']);
        $this->assertArrayHasKey( 4, $keyedByIdVlan1 );
        $this->assertEquals( "AS112", $keyedByIdVlan1[4]['name'] );

        $responseVlan2 = $this->post( "api/v4/customer/by-vlan-and-protocol", [
            'vlanid' => 2,
            'protocol' => 4
        ] );

        $responseVlan2->assertStatus( 200 );
        $jsonVlan2 = $responseVlan2->json();
        $this->assertArrayHasKey( "listCustomers", $jsonVlan2 );
        $this->assertCount( 3, $jsonVlan2['listCustomers'] );

        $keyedByIdVlan2 = collect( $jsonVlan2['listCustomers'] )->keyBy( 'id' );
        $this->assertArrayHasKey( 2, $keyedByIdVlan2 );
        $this->assertEquals( "HEAnet", $keyedByIdVlan2[2]['name'] );
        $this->assertArrayHasKey( 4, $keyedByIdVlan2 );
        $this->assertEquals( "AS112", $keyedByIdVlan2[4]['name'] );

        $responseVlanAll = $this->post( "api/v4/customer/by-vlan-and-protocol", [
            'protocol' => 4
        ] );

        $responseVlanAll->assertStatus( 200 );
        $jsonVlanAll = $responseVlanAll->json();
        $this->assertArrayHasKey( "listCustomers", $jsonVlanAll );
        $this->assertCount( 4, $jsonVlanAll['listCustomers'] );

        $keyedById = collect( $jsonVlanAll['listCustomers'] )->keyBy( 'id' );
        $this->assertArrayHasKey( 2, $keyedById );
        $this->assertEquals( "HEAnet", $keyedById[2]['name'] );
        $this->assertArrayHasKey( 3, $keyedById );
        $this->assertEquals( "PCH DNS", $keyedById[3]['name'] );
        $this->assertArrayHasKey( 4, $keyedById );
        $this->assertEquals( "AS112", $keyedById[4]['name'] );
        $this->assertArrayHasKey( 5, $keyedById );
        $this->assertEquals( "Imagine", $keyedById[5]['name'] );
    }

    public function testByVlanAndProtocol_VlanNotFound()
    {
        $this->post( "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->post( "api/v4/customer/by-vlan-and-protocol", [
            'vlanid'   => 999,
            'protocol' => 4
        ] );

        $response->assertStatus( 404 );
    }

    public function testByVlanAndProtocol_ProtocolNotFound()
    {
        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->post( "api/v4/customer/by-vlan-and-protocol", [
            'vlanid' => 1,
            'protocol' => 'oops'
        ] );
        $response->assertStatus( 404 );
    }
}