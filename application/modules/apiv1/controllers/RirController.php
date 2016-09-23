<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
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
 */


/**
 * Controller: API V1 RIR controller
 * 
 * @see https://github.com/inex/IXP-Manager/wiki/RIR-Objects
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Apiv1_RirController extends IXP_Controller_API_V1Action
{
    public function preDispatch()
    {
        $this->assertUserPriv( \Entities\User::AUTH_SUPERUSER );
        Zend_Controller_Action_HelperBroker::removeHelper( 'viewRenderer' );
    }

    /**
     * @see https://github.com/inex/IXP-Manager/wiki/RIR-Objects
     * @throws Zend_Controller_Action_Exception
     */
    public function updateObjectAction()
    {
        if( !$tmpl = $this->getParam( 'tmpl', false ) )
            throw new Zend_Controller_Action_Exception( 'You must specify a RIR template to update', 412 );

        // sanitise template name
        $tmpl = preg_replace( '/[^\da-z_\-]/i', '', $tmpl );
        
        if( !$this->view->templateExists( 'rir/tmpl/' . $tmpl . '.tpl' ) )
            throw new Zend_Controller_Action_Exception( 'The specified RIR template does not exist', 412 );
        
        $email = $this->getParam( 'email', false );

        // populate the template variables
        $this->view->customers = $customers = OSS_Array::reindexObjects( 
                OSS_Array::reorderObjects( $this->getD2R( '\\Entities\\Customer' )->getConnected( false, false, true ), 'getAutsys', SORT_NUMERIC ), 
                'getId' 
        );
        
        $this->view->asns      = $this->generateASNs( $customers );
        $this->view->rsclients = $this->generateRouteServerClientDetails( $customers );
        $this->view->protocols = [ 4, 6 ];
        
        
        $content = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", 
            $this->view->render( 'rir/tmpl/' . $tmpl . '.tpl' )
        );
        
        if( $email )
            $this->emailRIR( $tmpl, $content, $email, $this->getParam( 'force', false ) );
        else
            echo $content;
    }

    /**
     * Gather and create the IXP customer ASN details. 
     * 
     * Returns an associate array indexed by ordered ASNs of active external trafficing customers:
     * 
     *     [
     *         [65500] => [
     *                        ['name']    => Customer Name
     *                        ['asmacro'] => AS-CUSTOMER
     *                    ],
     *                    ...
     *     ]
     *     
     * @param \Entities\Customer[] $customers Array of all active external trafficing customers
     * @return array Associate array indexed by ordered ASNs 
     */
    private function generateASNs( $customers )
    {
        $asns = [];
        foreach( $customers as $c )
            $asns[ $c->getAutsys() ] = [
                'asmacro' => $c->resolveAsMacro( 4, 'AS' ),
                'name'    => $c->getName()
            ];
        
        ksort( $asns, SORT_NUMERIC );
        return $asns;
    }
    

    /**
     * Gather up route server client information for building RIR objects
     *
     * Returns an array of the form:
     *
     *     [
     *         [ vlans ] => [
     *             [ $vlanid ] => [
     *                 [servers] => [   // route server IP addresses by protocol
     *                     [4] => [
     *                         [0] => 193.242.111.8
     *                         ...
     *                     ],
     *                     [6] => [
     *                         ...
     *                     ]
     *                 ]
     *             ],
     *             [ $another_vlanid ] => [
     *                 ...
     *             ],
     *             ...
     *         ],
     *         [clients] => [
     *             [$customer_asn] => [
     *                 [id] => customer id,
     *                 [ vlans ] => [
     *                     [ vlanid ] => [
     *                         [$vlan_interface_id] => [    // customer's IP addresses by protocol
     *                             [4] => 193.242.111.xx
     *                             [6] => 2001:7f8:18::xx
     *                         ],
     *                         ...   // if the user has more than one VLAN interface on this VLAN
     *                     ],
     *                     ...
     *                 ],
     *             ],
     *         ],
     *     ]
     *
     * @param \Entities\Customer[] $customers
     * @return array As defined above
     */
    private function generateRouteServerClientDetails( $customers )
    {
        // get the public peering VLANs
        $vlans = $this->getD2R( '\\Entities\\Vlan' )->getAndCache( \Repositories\Vlan::TYPE_NORMAL );
    
        $rsclients = [];
    
        foreach( $vlans as $vlan )
        {
            foreach( [ 4, 6 ] as $proto )
            {
                // get the available route servers
                $servers = $vlan->getRouteServers( $proto );
    
                if( !count( $servers ) )
                    continue;
    
                $rsclients[ 'vlans' ][ $vlan->getId() ]['servers'][ $proto ] = [];
    
                foreach( $servers as $server )
                    $rsclients[ 'vlans' ][ $vlan->getId() ]['servers'][ $proto ][] = $server['ipaddress'];
                 
                foreach( $vlan->getVlanInterfaces() as $vli )
                {
                    if( !$vli->getRsclient() )
                        continue;

                    $oneConnectedInterface = false;
                    foreach( $vli->getVirtualInterface()->getPhysicalInterfaces() as $pi )
                    {
                        if( $pi->getStatus() == \Entities\PhysicalInterface::STATUS_CONNECTED )
                        {
                            $oneConnectedInterface = true;
                            break;
                        }
                    }
                    
                    if( !$oneConnectedInterface )
                        continue;
                        
                    $cust = $vli->getVirtualInterface()->getCustomer();

                    if( $cust->getStatus() != \Entities\Customer::STATUS_NORMAL )
                        continue;
                    
                    // is this customer still active?
                    if( !isset( $customers[ $cust->getId() ] ) )
                        continue;
    
                    if( !isset( $rsclients['clients'][ $cust->getAutsys() ] ) )
                    {
                        $rsclients['clients'][ $cust->getAutsys() ]['id'] = $cust->getId();
                        $rsclients['clients'][ $cust->getAutsys() ]['vlans'] = [];
                    }

                    $fnEnabled = "getIpv{$proto}enabled";
    
                    if( $vli->$fnEnabled() )
                    {
                        $fnIpaddress = "getIPv{$proto}Address";
                        $rsclients['clients'][ $cust->getAutsys() ]['vlans'][ $vlan->getId() ][ $vli->getId() ][ $proto ] = $vli->$fnIpaddress()->getAddress();
                    }
                }
            }
        }

        ksort( $rsclients['clients'], SORT_NUMERIC );
        return $rsclients;
    }
    
    /**
     * Send an email to RIPE / RIR as per the address provided 
     * 
     * Keeps a local cached file which, it it exists and is the same as the newly generated content
     * (and force was requested), will prevent the email from being sent unnecessarily.
     * 
     * @param string $tmpl The name of the template file for the email; used to name the local cache file
     * @param string $content The generated contect for the RIR update email
     * @param string $email The destination for the email - e.g. auto-dbm@ripe.net
     * @param bool $force Force the email to be sent even if the local cache is the same
     * @throws Zend_Controller_Action_Exception
     * @see https://github.com/inex/IXP-Manager/wiki/RIR-Objects
     */
    private function emailRIR( $tmpl, $content, $email, $force = false )
    {
        if( !Zend_Validate::is( $email, 'EmailAddress' ) )
            throw new Zend_Controller_Action_Exception( 'Invalid email address specified', 412 );
        
        // if we're not forcing, check to see if the object has changed from the previous version
        if( !$force && ( $last = file_get_contents( APPLICATION_PATH . '/../var/cache/rir-' . $tmpl . '.txt' ) ) )
            if( $last == $content )
                return;

        // record the contents for the next time
        file_put_contents( APPLICATION_PATH . '/../var/cache/rir-' . $tmpl . '.txt', $content );
        
        $mailer = $this->getMailer()
                    ->setBodyText( $content )
                    ->addTo( $email )
                    ->setFrom( $this->_options['identity']['autobot']['email'], $this->_options['identity']['autobot']['name'] )
                    ->setSubject( "Changes to {$tmpl} via IXP Manager" );
        
        try
        {
            $mailer->send();
        }
        catch( Zend_Mail_Exception $e )
        {
            throw new Zend_Controller_Action_Exception( 'Could not send email: ' . $e->getMessage(), 412 );
        }
                    
    }
}
