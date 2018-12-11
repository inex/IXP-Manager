<?php

namespace Tests\Browser;

use D2EM;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

use Entities\{
    VirtualInterface    as VirtualInterfaceEntity,
    VlanInterface       as VlanInterfaceEntity,
    PhysicalInterface   as PhysicalInterfaceEntity
};

class VirtualInterfaceControllerTest extends DuskTestCase
{
    /**
     * Test the whole Interfaces functionalities (virtuel, physical, vlan)
     *
     * @return void
     *
     * @throws
     */
    public function testAddWizard()
    {
        $this->browse( function ( Browser $browser ) {

            $browser->resize(1600, 1200 )
                ->visit('/auth/login' )
                ->type('username', 'travis' )
                ->type('password', 'travisci' )
                ->press('submit' )
                ->assertPathIs('/admin' );

            $vi = $this->intTestVi( $browser );

            $this->intTestPi( $browser, $vi );

            $this->intTestVli( $browser, $vi );

            // Delete Virtual interface
            $browser->press( "#delete-vi-" . $vi->getId() )
                ->waitForText( 'Do you really want to delete this Virtual Interface?' )
                ->press( "Delete" )
                ->waitForReload()
                ->assertPathIs('/customer/overview/' . $vi->getCustomer()->getId() . '/ports' )
                ->assertSee('The Virtual Interface has been deleted successfully.' );

        });


    }
    
    /**
     * Test the Virtual interface add/edit/delete functions
     *
     * @param Browser $browser
     *
     * @return VirtualInterfaceEntity $vi
     *
     * @throws
     */
    private function intTestVi( Browser $browser ){

        $browser->visit('/interfaces/virtual/wizard-add/custid/5' )
            ->assertSee('Virtual Interface Settings' );


        // Add a new Vitural interface Via wizard form
        $browser->select('vlan',  '2' )
            ->check( 'ipv4-enabled' )
            ->waitFor( "#ipv4-area" )
            ->check( 'ipv6-enabled' )
            ->waitFor( "#ipv6-area" )
            ->select( 'switch', '2' )
            ->waitUntilMissing( "Choose a switch port" )
            ->waitForText( "Choose a switch port" )
            ->select( 'switch-port', '28' )
            ->select( 'status', '4' )
            ->select( 'speed', '1000' )
            ->select( 'duplex', 'full' )
            ->type( 'maxbgpprefix', '100' )
            ->check( 'rsclient' )
            ->check( 'irrdbfilter' )
            ->check( 'as112client' )
            ->select( 'ipv4-address', "10.2.0.22" )
            ->select( 'ipv6-address', '2001:db8:2::22' )
            ->type( 'ipv4-hostname', 'v4.example.com' )
            ->type( 'ipv6-hostname', 'v6.example.com' )
            ->type( 'ipv4-bgp-md5-secret', 'soopersecret' )
            ->type( 'ipv6-bgp-md5-secret', 'soopersecret' )
            ->check( 'ipv4-can-ping' )
            ->check( 'ipv6-can-ping' )
            ->check( 'ipv4-monitor-rcbgp' )
            ->check( 'ipv6-monitor-rcbgp' )
            ->press('Save Changes' )
            ->assertSee('New interface created!' );

        $url = explode( '/', $browser->driver->getCurrentURL() );

        // Check data in DB
        /** @var $vi VirtualInterfaceEntity */
        $this->assertInstanceOf( VirtualInterfaceEntity::class , $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( array_pop( $url ) ) );

        // check the values of the Virtual interface object
        $this->assertEquals( 5, $vi->getCustomer()->getId() );
        $this->assertEquals( "", $vi->getName() );
        $this->assertEquals( null, $vi->getMtu() );
        $this->assertEquals( false, $vi->getTrunk() );
        $this->assertEquals( null, $vi->getChannelgroup() );
        $this->assertEquals( false, $vi->getLagFraming() );
        $this->assertEquals( false,$vi->getFastLACP() );

        // check that we have 1 physical interface for the virtual interface
        $this->assertEquals( 1, count( $vi->getVlanInterfaces() ) );

        // check the values of the Vlan interface object
        $vli = $vi->getVlanInterfaces()[0];
        /** @var $vli VlanInterfaceEntity */
        $this->assertEquals( "10.2.0.22", $vli->getIPv4Address()->getAddress() );
        $this->assertEquals( "2001:db8:2::22", $vli->getIPv6Address()->getAddress() );
        $this->assertEquals( 2, $vli->getVlan()->getId() );
        $this->assertEquals( true, $vli->getIpv4enabled() );
        $this->assertEquals( true, $vli->getIpv6enabled() );
        $this->assertEquals( "v4.example.com", $vli->getIpv4hostname() );
        $this->assertEquals( "v6.example.com", $vli->getIpv6hostname() );
        $this->assertEquals( false, $vli->getMcastenabled() );
        $this->assertEquals( true, $vli->getIrrdbfilter() );
        $this->assertEquals( "soopersecret", $vli->getIpv4bgpmd5secret() );
        $this->assertEquals( "soopersecret", $vli->getIpv6bgpmd5secret() );
        $this->assertEquals( "100", $vli->getMaxbgpprefix() );
        $this->assertEquals( true, $vli->getRsclient() );
        $this->assertEquals( true, $vli->getIpv4canping() );
        $this->assertEquals( true, $vli->getIpv6canping() );
        $this->assertEquals( true, $vli->getIpv4monitorrcbgp() );
        $this->assertEquals( true, $vli->getIpv6monitorrcbgp() );
        $this->assertEquals( true, $vli->getAs112client() );
        $this->assertEquals( false, $vli->getBusyhost() );
        $this->assertEquals( null, $vli->getNotes() );
        $this->assertEquals( false, $vli->getRsMoreSpecifics() );



        // check that we have 1 physical interface for the virtual interface
        $this->assertEquals( 1, count( $vi->getPhysicalInterfaces() ) );

        /** @var $pi PhysicalInterfaceEntity */
        $pi = $vi->getPhysicalInterfaces()[0];

        // check the values of the Physical interface object
        $this->assertEquals( "GigabitEthernet4", $pi->getSwitchPort()->getName()  );
        $this->assertEquals( "switch2", $pi->getSwitchPort()->getSwitcher()->getName()  );
        $this->assertEquals( 4, $pi->getStatus() );
        $this->assertEquals( 1000, $pi->getSpeed() );
        $this->assertEquals( "full", $pi->getDuplex() );
        $this->assertEquals( null, $pi->getNotes() );
        $this->assertEquals( true, $pi->getAutoneg() );


        // Go on edit page
        $browser->visit('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee('Add/Edit Virtual Interface');

        // Check the form values
        $browser->assertSelected('cust', '5' )
            ->assertNotChecked('trunk' )
            ->assertNotChecked('lag_framing' )
            ->assertNotChecked('fastlacp' )
            ->click(        "#advanced-options" )
            ->assertInputValue('name', '' )
            ->assertInputValue('description', '' )
            ->assertInputValue('channel-group', '' )
            ->assertInputValue('mtu', '' );

        // Edit the virtual Interface with new values
        $browser->select('cust', '2')
            ->check('trunk')
            ->check('lag_framing')
            ->waitFor( "#fastlacp" )
            ->check('fastlacp')
            ->type('name', 'name-test')
            ->type('description', 'description-test')
            ->type('channel-group', '666')
            ->type('mtu', '666')
            ->press('Save Changes')
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee('Virtual Interface added/updated successfully.');

        // Check value in DB
        D2EM::refresh( $vi );

        $this->assertEquals( 2, $vi->getCustomer()->getId() );
        $this->assertEquals( "name-test", $vi->getName() );
        $this->assertEquals( 666, $vi->getMtu() );
        $this->assertEquals( true, $vi->getTrunk() );
        $this->assertEquals( 666, $vi->getChannelgroup() );
        $this->assertEquals( true, $vi->getLagFraming() );
        $this->assertEquals( true,$vi->getFastLACP() );

        // Go on edit page
        $browser->visit('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee('Add/Edit Virtual Interface');


        // Check the form with new values
        $browser->assertSelected('cust', '2' )
            ->assertChecked('trunk' )
            ->assertChecked('lag_framing' )
            ->assertChecked('fastlacp' )
            ->assertInputValue('name', 'name-test' )
            ->assertInputValue('description', 'description-test' )
            ->assertInputValue('channel-group', '666' )
            ->assertInputValue('mtu', '666' );


        // Edit the virtual Interface, uncheck all checkboxes, change value of select
        $browser->select('cust', '3' )
            ->uncheck('trunk' )
            ->uncheck('lag_framing' )
            ->uncheck('fastlacp' )
            ->press('Save Changes' )
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee('Virtual Interface added/updated successfully.' );

        // Check value in DB
        D2EM::refresh( $vi );

        $this->assertEquals( 3,     $vi->getCustomer()->getId() );
        $this->assertEquals( false, $vi->getTrunk() );
        $this->assertEquals( false, $vi->getLagFraming() );
        $this->assertEquals( false, $vi->getFastLACP() );

        // Go on edit page
        $browser->visit('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee('Add/Edit Virtual Interface' );

        // Check the form with new values
        $browser->assertSelected('cust', '3' )
            ->assertNotChecked('trunk' )
            ->assertNotChecked('lag_framing' )
            ->assertNotChecked('fastlacp' );


        // Edit the virtual Interface, check all checkboxes
        $browser->check('trunk' )
            ->check('lag_framing' )
            ->waitFor( "#fastlacp" )
            ->check('fastlacp' )
            ->press('Save Changes' )
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee('Virtual Interface added/updated successfully.' );

        // Check value in DB
        D2EM::refresh( $vi );

        $this->assertEquals( true, $vi->getTrunk() );
        $this->assertEquals( true, $vi->getLagFraming() );
        $this->assertEquals( true, $vi->getFastLACP() );

        // Go on edit page
        $browser->visit('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee('Add/Edit Virtual Interface' );

        // Check the form with new values
        $browser->assertChecked('trunk' )
            ->assertChecked('lag_framing' )
            ->assertChecked('fastlacp' );

        return $vi;
    }

    /**
     * Test the Physical interface Add/edit/delete function
     *
     * @param Browser $browser
     * @param VirtualInterfaceEntity $vi
     *
     * @throws
     */
    private function intTestPi(Browser $browser, VirtualInterfaceEntity $vi ){

        $browser->visit('/interfaces/virtual/edit/' . $vi->getId() );

        $browser->click( "#add-pi" );

        // Add a new Physical interface
        $browser->select('switch',  '2' )
            ->waitUntilMissing( "Choose a switch port" )
            ->waitForText( "Choose a switch port" )
            ->select( 'switch-port', '29' )
            ->select( 'status', '1' )
            ->select( 'speed', '10' )
            ->select( 'duplex', 'half' )
            ->check( 'autoneg-label' )
            ->type( 'notes', '### note test' )
            ->press( "Save Changes" )
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee( 'Physical Interface updated successfully.' );


        // check data in DB
        $this->assertGreaterThan( 1, $vi->getPhysicalInterfaces()->count() );

        /** @var $pi PhysicalInterfaceEntity */
        $this->assertInstanceOf( PhysicalInterfaceEntity::class , $pi = $vi->getPhysicalInterfaces()->last() );

        $this->assertEquals( 2,      $pi->getSwitchPort()->getSwitcher()->getId() );
        $this->assertEquals( 29,     $pi->getSwitchPort()->getId() );
        $this->assertEquals( 1,      $pi->getStatus() );
        $this->assertEquals( 10,     $pi->getSpeed() );
        $this->assertEquals( 'half', $pi->getDuplex() );
        $this->assertEquals( true,   $pi->getAutoneg() );
        $this->assertEquals( '### note test', $pi->getNotes() );


        $browser->click( "#edit-pi-" . $pi->getId() )
            ->assertPathIs('/interfaces/physical/edit/' . $pi->getId() . "/vintid/" . $vi->getId() )
            ->assertSee( "Edit Physical Interface" );


        // Check the form values
        $browser->assertSelected('switch', '2')
            ->waitUntilMissing( "Choose a switch port" )
            ->waitForText( "Choose a switch port" )
            ->assertSelected('switch-port', '29')
            ->assertSelected('status', '1')
            ->assertSelected('speed', '10')
            ->assertSelected('duplex', 'half')
            ->assertChecked('autoneg-label' )
            ->assertInputValue( 'notes' , '### note test' );


        // edit the Physical interface
        $browser->select('switch',  '1')
            ->waitUntilMissing( "Choose a switch port" )
            ->waitForText( "Choose a switch port" )
            ->select( 'switch-port', '2' )
            ->select( 'status', '2' )
            ->select( 'speed', '100' )
            ->select( 'duplex', 'full' )
            ->uncheck( 'autoneg-label' )
            ->type( 'notes', '### note test test' )
            ->press( "Save Changes" )
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee( 'Physical Interface updated successfully.' );

        D2EM::refresh( $pi );

        // check data in DB
        $this->assertEquals( 1,      $pi->getSwitchPort()->getSwitcher()->getId() );
        $this->assertEquals( 2,      $pi->getSwitchPort()->getId() );
        $this->assertEquals( 2,      $pi->getStatus() );
        $this->assertEquals( 100,    $pi->getSpeed() );
        $this->assertEquals( "full", $pi->getDuplex() );
        $this->assertEquals( false,  $pi->getAutoneg() );
        $this->assertEquals( '### note test test', $pi->getNotes() );


        $browser->click( "#edit-pi-" . $pi->getId() );

        $browser->assertSee( "Edit Physical Interface" );

        // Check the form values
        $browser->assertSelected('switch', '1')
            ->waitUntilMissing( "Choose a switch port" )
            ->waitForText( "Choose a switch port" )
            ->assertSelected('switch-port', '2')
            ->assertSelected('status', '2')
            ->assertSelected('speed', '100')
            ->assertSelected('duplex', 'full')
            ->assertNotChecked('autoneg-label' )
            ->assertInputValue( 'notes' , '### note test test' );


        // check all checkboxes
        $browser->check( 'autoneg-label' )
            ->press( "Save Changes" )
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee( 'Physical Interface updated successfully.' );

        D2EM::refresh( $pi );

        $this->assertEquals( true,  $pi->getAutoneg() );

        $browser->click( "#edit-pi-" . $pi->getId() );

        $browser->assertSee( "Edit Physical Interface" );

        // Check the form values
        $browser->assertChecked('autoneg-label' );

        $browser->click( "#cancel-btn" )
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee( "Add/Edit Virtual Interface" );

        // Delete physical interface
        $browser->press("#delete-pi-" . $pi->getId() )
            ->waitForText( 'Do you really want to delete this Physical Interface?' )
            ->press('Delete')
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->waitForReload()
            ->assertSee( 'The Physical Interface has been deleted successfully.' );

    }



    /**
     * Test the Vlan interface Add/edit/delete function
     *
     * @param Browser $browser
     * @param VirtualInterfaceEntity $vi
     *
     * @throws
     */
    private function intTestVli( Browser $browser, VirtualInterfaceEntity $vi ){

        $browser->visit('/interfaces/virtual/edit/' . $vi->getId() );

        $browser->click( "#add-vli" );

        $browser->assertPathIs('/interfaces/vlan/add/0/vintid/' . $vi->getId() );

        // Add a new Vlan interface
        $browser->select('vlan',  '2' )
            ->check( "mcastenabled" )
            ->check( "busyhost" )
            ->check( 'ipv6-enabled' )
            ->waitFor( "#ipv6-area" )
            ->check( 'ipv4-enabled' )
            ->waitFor( "#ipv4-area" )
            ->type( "maxbgpprefix", '30' )
            ->check( "rsclient" )
            ->check( 'irrdbfilter' )
            ->check( 'rsmorespecifics' )
            ->select( 'ipv4-address', "10.2.0.1" )
            ->select( 'ipv6-address', '2001:db8:2::1' )
            ->type( 'ipv4-hostname', 'v4.example.com' )
            ->type( 'ipv6-hostname', 'v6.example.com' )
            ->type( 'ipv4-bgp-md5-secret', 'soopersecret' )
            ->type( 'ipv6-bgp-md5-secret', 'soopersecret' )
            ->check( 'ipv4-can-ping' )
            ->check( 'ipv6-can-ping' )
            ->check( 'ipv4-monitor-rcbgp' )
            ->check( 'ipv6-monitor-rcbgp' )
            ->press('Add')
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee('Vlan Interface updated successfully.');

        // check data in DB
        $this->assertGreaterThan( 1, $vi->getVlanInterfaces()->count() );

        /** @var $vli VlanInterfaceEntity */
        $this->assertInstanceOf( VlanInterfaceEntity::class , $vli = $vi->getVlanInterfaces()->last() );

        $this->assertEquals( "10.2.0.1", $vli->getIPv4Address()->getAddress() );
        $this->assertEquals( "2001:db8:2::1", $vli->getIPv6Address()->getAddress() );
        $this->assertEquals( 2,   $vli->getVlan()->getId() );
        $this->assertEquals( true, $vli->getIpv4enabled() );
        $this->assertEquals( true, $vli->getIpv6enabled() );
        $this->assertEquals( "v4.example.com", $vli->getIpv4hostname() );
        $this->assertEquals( "v6.example.com", $vli->getIpv6hostname() );
        $this->assertEquals( true, $vli->getMcastenabled() );
        $this->assertEquals( true, $vli->getIrrdbfilter() );
        $this->assertEquals( "soopersecret", $vli->getIpv4bgpmd5secret() );
        $this->assertEquals( "soopersecret", $vli->getIpv6bgpmd5secret() );
        $this->assertEquals( "30", $vli->getMaxbgpprefix() );
        $this->assertEquals( true, $vli->getRsclient() );
        $this->assertEquals( true, $vli->getIpv4canping() );
        $this->assertEquals( true, $vli->getIpv6canping() );
        $this->assertEquals( true, $vli->getIpv4monitorrcbgp() );
        $this->assertEquals( true, $vli->getIpv6monitorrcbgp() );
        $this->assertEquals( true, $vli->getBusyhost() );
        $this->assertEquals( null, $vli->getNotes() );
        $this->assertEquals( true, $vli->getRsMoreSpecifics() );


        // Edit the Vlan Interface
        $browser->click( "#edit-vli-" . $vli->getId() )
            ->assertPathIs('/interfaces/vlan/edit/' . $vli->getId() . "/vintid/" . $vi->getId() )
            ->assertSee( "Edit VLAN Interface" );


        // Check the form values
        $browser->assertSelected('vlan', '2' )
            ->assertChecked('mcastenabled' )
            ->assertChecked('busyhost' )
            ->assertChecked('ipv6-enabled' )
            ->assertChecked('ipv4-enabled' )
            ->assertInputValue('maxbgpprefix', '30' )
            ->assertChecked('rsclient' )
            ->assertChecked('irrdbfilter' )
            ->assertChecked('rsmorespecifics' )
            ->assertSelected('ipv4-address', '10.2.0.1' )
            ->assertSelected('ipv6-address', '2001:db8:2::1' )
            ->assertInputValue( 'ipv4-hostname', 'v4.example.com' )
            ->assertInputValue( 'ipv6-hostname', 'v6.example.com' )
            ->assertInputValue( 'ipv4-bgp-md5-secret', 'soopersecret' )
            ->assertInputValue( 'ipv6-bgp-md5-secret', 'soopersecret' )
            ->assertChecked( 'ipv4-can-ping' )
            ->assertChecked( 'ipv6-can-ping' )
            ->assertChecked( 'ipv4-monitor-rcbgp' )
            ->assertChecked( 'ipv6-monitor-rcbgp' );


        // Change value of the vlan interface
        // Check the form values
        $browser->select('vlan', '1' )
            ->uncheck( "mcastenabled" )
            ->uncheck( "busyhost" )
            ->type( "maxbgpprefix", '20' )
            ->uncheck( "rsclient" )
            ->uncheck( 'irrdbfilter' )
            ->uncheck( 'rsmorespecifics' )
            ->select( 'ipv4-address', "10.1.0.1" )
            ->select( 'ipv6-address', '2001:db8:1::1' )
            ->type( 'ipv4-hostname', 'v4-2.example.com' )
            ->type( 'ipv6-hostname', 'v6-2.example.com' )
            ->type( 'ipv4-bgp-md5-secret', 'soopersecrets' )
            ->type( 'ipv6-bgp-md5-secret', 'soopersecrets' )
            ->uncheck( 'ipv4-can-ping' )
            ->uncheck( 'ipv6-can-ping' )
            ->uncheck( 'ipv4-monitor-rcbgp' )
            ->uncheck( 'ipv6-monitor-rcbgp' )
            ->press('Save Changes')
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee('Vlan Interface updated successfully.');

        D2EM::refresh( $vli );

        $this->assertEquals( "10.1.0.1", $vli->getIPv4Address()->getAddress() );
        $this->assertEquals( "2001:db8:1::1", $vli->getIPv6Address()->getAddress() );
        $this->assertEquals( 1, $vli->getVlan()->getId() );
        $this->assertEquals( true, $vli->getIpv4enabled() );
        $this->assertEquals( true, $vli->getIpv6enabled() );
        $this->assertEquals( "v4-2.example.com", $vli->getIpv4hostname() );
        $this->assertEquals( "v6-2.example.com", $vli->getIpv6hostname() );
        $this->assertEquals( false, $vli->getMcastenabled() );
        $this->assertEquals( false, $vli->getIrrdbfilter() );
        $this->assertEquals( "soopersecrets", $vli->getIpv4bgpmd5secret() );
        $this->assertEquals( "soopersecrets", $vli->getIpv6bgpmd5secret() );
        $this->assertEquals( 20, $vli->getMaxbgpprefix() );
        $this->assertEquals( false, $vli->getRsclient() );
        $this->assertEquals( false, $vli->getIpv4canping() );
        $this->assertEquals( false, $vli->getIpv6canping() );
        $this->assertEquals( false, $vli->getIpv4monitorrcbgp() );
        $this->assertEquals( false, $vli->getIpv6monitorrcbgp() );
        $this->assertEquals( false, $vli->getBusyhost() );
        $this->assertEquals( null, $vli->getNotes() );
        $this->assertEquals( false, $vli->getRsMoreSpecifics() );

        // Edit the Vlan Interface
        $browser->click( "#edit-vli-" . $vli->getId() )
            ->assertPathIs('/interfaces/vlan/edit/' . $vli->getId() . "/vintid/" . $vi->getId() )
            ->assertSee( "Edit VLAN Interface" );

        // Check the form values
        $browser->assertSelected('vlan', '1')
            ->assertNotChecked('mcastenabled')
            ->assertNotChecked('busyhost')
            ->assertChecked('ipv6-enabled')
            ->assertChecked('ipv4-enabled')
            ->assertInputValue('maxbgpprefix', '20')
            ->assertNotChecked('rsclient')
            ->assertNotChecked('irrdbfilter')
            ->assertNotChecked('rsmorespecifics')
            ->assertSelected('ipv4-address', '10.1.0.1')
            ->assertSelected('ipv6-address', '2001:db8:1::1')
            ->assertInputValue( 'ipv4-hostname', 'v4-2.example.com' )
            ->assertInputValue( 'ipv6-hostname', 'v6-2.example.com' )
            ->assertInputValue( 'ipv4-bgp-md5-secret', 'soopersecrets' )
            ->assertInputValue( 'ipv6-bgp-md5-secret', 'soopersecrets' )
            ->assertNotChecked( 'ipv4-can-ping' )
            ->assertNotChecked( 'ipv6-can-ping' )
            ->assertNotChecked( 'ipv4-monitor-rcbgp' )
            ->assertNotChecked( 'ipv6-monitor-rcbgp' );


        // Check all the checkboxes
        $browser->check( "mcastenabled" )
            ->check( "busyhost" )
            ->check( "rsclient" )
            ->check( 'irrdbfilter' )
            ->waitFor( "#div-rsmorespecifics" )
            ->check('rsmorespecifics')
            ->check( 'ipv4-can-ping' )
            ->check( 'ipv6-can-ping' )
            ->check( 'ipv4-monitor-rcbgp' )
            ->check( 'ipv6-monitor-rcbgp' )
            ->press('Save Changes')
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee('Vlan Interface updated successfully.');

        D2EM::refresh( $vli );

        $this->assertEquals( true, $vli->getMcastenabled() );
        $this->assertEquals( true, $vli->getIrrdbfilter() );
        $this->assertEquals( true, $vli->getRsclient() );
        $this->assertEquals( true, $vli->getIpv4canping() );
        $this->assertEquals( true, $vli->getIpv6canping() );
        $this->assertEquals( true, $vli->getIpv4monitorrcbgp() );
        $this->assertEquals( true, $vli->getIpv6monitorrcbgp() );
        $this->assertEquals( true, $vli->getBusyhost() );
        $this->assertEquals( true, $vli->getRsMoreSpecifics() );

        // Edit the Vlan Interface
        $browser->click( "#edit-vli-" . $vli->getId() )
            ->assertPathIs('/interfaces/vlan/edit/' . $vli->getId() . "/vintid/" . $vi->getId() )
            ->assertSee( "Edit VLAN Interface" );

        // Check the form values
        $browser->assertChecked('mcastenabled')
            ->assertChecked('busyhost')
            ->assertChecked('rsclient')
            ->assertChecked('irrdbfilter')
            ->assertChecked( 'ipv4-can-ping' )
            ->assertChecked( 'ipv6-can-ping' )
            ->assertChecked( 'ipv4-monitor-rcbgp' )
            ->assertChecked( 'ipv6-monitor-rcbgp' );

        $browser->click( "#cancel-btn" )
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->assertSee( "Add/Edit Virtual Interface" );


        // test the duplication functionality
        $browser->click( "#duplicate-vli-" . $vli->getId() )
            ->waitForText( 'Duplicate the VLAN Interface' )
            ->select( "#duplicateTo" , '2' )
            ->press('Duplicate')
            ->assertPathIs('/interfaces/vlan/duplicate/' . $vli->getId() . "/to/2" )
            ->assertSee( 'This form allows you to duplicate the selected' );

        // check that the form match with the Vlan interface informations
        $browser->assertSelected('vlan', '2')
            ->assertChecked('mcastenabled')
            ->assertChecked('busyhost')
            ->assertChecked('ipv6-enabled')
            ->assertChecked('ipv4-enabled')
            ->assertInputValue('maxbgpprefix', '20')
            ->assertChecked('rsclient')
            ->assertChecked('irrdbfilter')
            ->assertChecked('rsmorespecifics')
            ->assertSelected('ipv4-address', '10.1.0.1')
            ->assertSelected('ipv6-address', '2001:db8:1::1')
            ->assertInputValue( 'ipv4-hostname', 'v4-2.example.com' )
            ->assertInputValue( 'ipv6-hostname', 'v6-2.example.com' )
            ->assertInputValue( 'ipv4-bgp-md5-secret', 'soopersecrets' )
            ->assertInputValue( 'ipv6-bgp-md5-secret', 'soopersecrets' )
            ->assertChecked( 'ipv4-can-ping' )
            ->assertChecked( 'ipv6-can-ping' )
            ->assertChecked( 'ipv4-monitor-rcbgp' )
            ->assertChecked( 'ipv6-monitor-rcbgp' )
            ->press( "Save Changes" )
            ->assertSee( "Vlan Interface updated successfully." );;

        D2EM::refresh($vi);
        $vliDuplicated = $vi->getVlanInterfaces()->last();

        // check if the value of the duplicated Vlan interface match
        $this->assertEquals( "10.1.0.1", $vliDuplicated->getIPv4Address()->getAddress() );
        $this->assertEquals( "2001:db8:1::1", $vliDuplicated->getIPv6Address()->getAddress() );
        $this->assertEquals( "2", $vliDuplicated->getVlan()->getId() );
        $this->assertEquals( true, $vliDuplicated->getIpv4enabled() );
        $this->assertEquals( true, $vliDuplicated->getIpv6enabled() );
        $this->assertEquals( "v4-2.example.com", $vliDuplicated->getIpv4hostname() );
        $this->assertEquals( "v6-2.example.com", $vliDuplicated->getIpv6hostname() );
        $this->assertEquals( true, $vliDuplicated->getMcastenabled() );
        $this->assertEquals( true, $vliDuplicated->getIrrdbfilter() );
        $this->assertEquals( "soopersecrets", $vliDuplicated->getIpv4bgpmd5secret() );
        $this->assertEquals( "soopersecrets", $vliDuplicated->getIpv6bgpmd5secret() );
        $this->assertEquals( "20", $vliDuplicated->getMaxbgpprefix() );
        $this->assertEquals( true, $vliDuplicated->getRsclient() );
        $this->assertEquals( true, $vliDuplicated->getIpv4canping() );
        $this->assertEquals( true, $vliDuplicated->getIpv6canping() );
        $this->assertEquals( true, $vliDuplicated->getIpv4monitorrcbgp() );
        $this->assertEquals( true, $vliDuplicated->getIpv6monitorrcbgp() );
        $this->assertEquals( true, $vliDuplicated->getBusyhost() );
        $this->assertEquals( null, $vliDuplicated->getNotes() );
        //$this->assertEquals( true, $vliDuplicated->getRsMoreSpecifics() );

        // Delete Vlan interface
        $browser->press("#delete-vli-" . $vli->getId() )
            ->waitForText( 'Do you really want to delete this Vlan Interface?' )
            ->press('Delete')
            ->assertPathIs('/interfaces/virtual/edit/' . $vi->getId() )
            ->waitForReload()
            ->assertSee( 'The Vlan Interface has been deleted successfully.' );

    }
}