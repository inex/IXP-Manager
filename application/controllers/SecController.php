<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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
 * SecController
 *
 * @author
 * @version
 */

/**
 * SEC - Simple Event Correlator
 *
 * @author barryo
 *
 */
class SecController extends Zend_Controller_Action
{

    /**
     * The bootstrap
     */
    protected $_bootstrap;

    /**
     * Logger object
     */
    protected $_logger;

    /**
     * Config object
     */
    protected $_config;

    /**
     * Config object
     */
    protected $_session;

    /**
     * View object
     */
    protected $_view;

    /**
     * Clickatell object
     */
    private $_clickatell = null;

    /**
     * Override the Zend_Controller_Action's constructor (which is called
     * in function anyway).
     *
     * @param object $request See Parent class constructer
     * @param object $response See Parent class constructer
     * @param object $invokeArgs See Parent class constructer
     */
    public function __construct(
            Zend_Controller_Request_Abstract  $request,
            Zend_Controller_Response_Abstract $response,
            array $invokeArgs = null )
    {
        // get the bootstrap object
        $this->_bootstrap = $invokeArgs['bootstrap'];

        // and from the bootstrap, we can get other resources:
        $this->_config   = $this->_bootstrap->getApplication()->getOptions();
        $this->_session  = $this->_bootstrap->getResource( 'namespace' );
        $this->_view     = $this->_bootstrap->getResource( 'view' );

        // call the parent's version where all the Zend magic happens
        parent::__construct( $request, $response, $invokeArgs );
    }


    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        // TODO Auto-generated SecController::indexAction() default action
        echo "No such event defined\n";
    }

    /**
     * Handle BGP MD5 Authentication errors from the route collector
     *
     * This inserts a row into the sec_event table and optionally notifies the customer
     * and / or IXP operations.
     *
     * Example test commands:
     *
     *  echo "day=10&hour=10&minute=36&second=15&router=rc1&month=Feb&type=BGP_AUTH&ip=193.242.111.37" | ./sec-processor.php
     *
     * See sec.port_updown.* configuration options in application.ini.
     */
    public function bgpAuthAction()
    {

        // Log the event
        $this->getLogger()->notice( "{$this->_session->type} {$this->_session->cust['shortname']} - "
            . "{$this->_session->router} - {$this->_session->ip['address']}"
        );

        // Record it in the database
        $se = new SecEvent;
        $se['Cust']          = $this->_session->cust;
        $se['type']          = $this->_session->type;
        $se['recorded_date'] = $this->_session->date;
        $se['message']     = "Bad or no BGP MD5 digest from {$this->_session->ip['address']} connecting to " . $this->_config['identity']['orgname'] . " router collector";
        $se->save();

        $this->_view->params = $this->_session;

        if( $this->_config['sec']['bgp_auth']['alert_customers']
                && $this->_session->user->getOrSetGetPreference( SecEvent::TYPE_BGP_AUTH, 1 ) )
        {
            $mail = new Zend_Mail();
            $mail->addTo( $this->_config['identity']['testemail'] /* $this->_session->cust['nocemail']*/, "NOC of {$this->_session->cust['name']}" );
            $mail->setSubject( '[' . $this->_config['identity']['orgname'] . ' Autobot] BGP MD5 Auth Issue with ' . $this->_config['identity']['orgname'] . ' Route Collector' );
            $this->_sendMail( $mail, 'sec/cust_bgp_auth.tpl', true, false, $this->_config['sec']['bgp_auth']['cc_opsauto'] );
        }

        if( $this->_config['sec']['bgp_auth']['alert_operations'] )
        {
            $this->_view->customer_notified = $this->_config['sec']['bgp_auth']['alert_customers'];

            $mail = new Zend_Mail();

            $mail->setSubject( "BGP Auth Issue with rc1 :: {$this->_session->cust['shortname']} :: "
                . "{$this->_session->ip['address']}"
            );

            $this->_sendMail( $mail, 'sec/ops_bgp_auth.tpl', true, true );
        }

    }



    /**
     * Handle Port and Line Protocol Up/Down events
     *
     * This inserts a row into the sec_event table and optionally notifies the customer
     * and / or IXP operations.
     *
     * Example test commands:
     *
     *  echo "day=10:hour=10:minute=36:second=15:switch=sw01:month=Feb:type=PORT_UPDOWN:state=down:port=GigabitEthernet1/1" | ./sec-processor.php
     *
     * See sec.port_updown.* configuration options in application.ini.
     */
    public function portUpdownAction()
    {
        // FIXME Handle more that core and customer ports!

        // Log the event
        if( $this->_session->isCorePort )
        {
            $this->getLogger()->notice( "{$this->_session->type} CORE LINK - "
                . "{$this->_session->switch['name']} - {$this->_session->port}"
            );
        }
        else
        {
            $this->getLogger()->notice( "{$this->_session->type} {$this->_session->cust['shortname']} - "
                . "{$this->_session->switch['name']} - {$this->_session->switchPort['name']} "
            );
        }

        // Record it in the database
        $se = new SecEvent;
        $se['Cust']          = $this->_session->cust;
        $se['SwitchTable']   = $this->_session->switch;
        $se['Switchport']    = $this->_session->switchPort;
        $se['type']          = $this->_session->type == 'PORT_UPDOWN' ? SecEvent::TYPE_PORT_UPDOWN : SecEvent::TYPE_LINEPROTO_UPDOWN;
        $se['recorded_date'] = $this->_session->date;


        if( $this->_session->isCorePort )
        {
            $se['message']     = 'CORE LINK: ' . ( $this->_session->type == 'PORT_UPDOWN' ? 'Port' : 'Line protocol' )
                . ' ' . strtoupper( $this->_session->state ) . ": {$this->_session->port} "
                . "of switch {$this->_session->switch['name']}";
        }
        else
        {
            $se['message']     = ( $this->_session->type == 'PORT_UPDOWN' ? 'Port' : 'Line protocol' )
                . ' ' . strtoupper( $this->_session->state ) . ": {$this->_session->switchPort['name']} "
                . "of switch {$this->_session->switch['name']}";
        }

        $se->save();

        $this->_view->params = $this->_session;

        if( $this->_config['sec']['port_updown']['alert_customers']
            && !$this->_session->isCorePort
            && $this->_session->user->getOrSetGetPreference( SecEvent::TYPE_PORT_UPDOWN, 1 ) )
        {
            $mail = new Zend_Mail();
            $mail->addTo( $this->_config['identity']['testemail'] /* $this->_session->cust['nocemail']*/, "NOC of {$this->_session->cust['name']}" );
            $mail->setSubject( '[' . $this->_config['identity']['orgname'] . ' Autobot] ALERT: Your Port / Line Protocol is ' . strtoupper( $this->_session->state ) );
            $this->_sendMail( $mail, 'sec/cust_port_updown.tpl', true, false, $this->_config['sec']['security_violation']['cc_opsauto'] );
        }

        if( $this->_config['sec']['port_updown']['alert_operations'] || $this->_session->isCorePort )
        {
            $this->_view->customer_notified = $this->_config['sec']['port_updown']['alert_customers'];

            $mail = new Zend_Mail();

            if( $this->_session->isCorePort )
            {
                $mail->setSubject( 'CORE LINK ' . strtoupper( $this->_session->state )
                    . " :: {$this->_session->switch['name']} {$this->_session->port}"
                );

                // Send SMS to Operators!
                foreach( $this->_config['sec']['pagers'] as $pager )
                {
                    $this->_getClickatell()->send( $pager,
                        "CORE PORT {$this->_session->switch['name']} {$this->_session->port} " . strtoupper( $this->_session->state )
                    );
                }
            }
            else
            {
                $mail->setSubject( "Port/Line Protocol Up/Down :: {$this->_session->cust['shortname']} :: "
                    . "{$this->_session->switch['name']} {$this->_session->switchPort['name']}"
                );
            }

            $this->_sendMail( $mail, 'sec/ops_port_updown.tpl', true, true );
        }

    }



    /**
     * Handle Security Violation events
     *
     * This inserts a row into the sec_event table and optionally notifies the customer
     * and / or IXP operations.
     *
     * Example test command:
     *
     *    echo "day=10:hour=10:minute=36:second=15:switch=sw01:month=Feb:type=SECURITY_VIOLATION:mac=0011.93fb.3040:port=GigabitEthernet1/1" | ./sec-processor.php
     *
     * See sec.security_violation.* configuration options in application.ini.
     */
    public function securityViolationAction()
    {

	    ///////////////////////////////////////////////////////
        // wonder who manufactured the device that caused this violation?

	    $smac = strtoupper( $this->_session->mac );
	    $smac = substr( $smac, 0, 2 ) . '-' . substr( $smac, 2, 2 ) . '-' . substr( $smac, 5, 2 );

        $manufacturer = 'Unknown';
	    $fp = fopen( APPLICATION_PATH . '/../data/oui.txt', 'r' );
	    while( $oui = fgets( $fp ) )
	    {
	        if( substr( $oui, 0, 8 ) == $smac )
                $manufacturer = trim( substr( $oui, strpos( $oui, '(hex)' ) + 5 ) );
	    }
	    fclose( $fp );

        // Log the event
        $this->getLogger()->notice( "SECURITY VIOLATION {$this->_session->cust['shortname']} - "
            . "{$this->_session->switch['name']} - {$this->_session->switchPort['name']} "
            . "MAC: {$this->_session->mac} Manufactur: $manufacturer"
        );

        // Record it in the database
        $se = new SecEvent;
        $se['Cust']          = $this->_session->cust;
        $se['SwitchTable']   = $this->_session->switch;
        $se['Switchport']    = $this->_session->switchPort;
        $se['type']          = SecEvent::TYPE_SECURITY_VIOLATION;
        $se['recorded_date'] = $this->_session->date;

        $se['message']     = "Security violation occurred, caused by MAC address {$this->_session->mac} "
            . "(manufacturer: {$manufacturer}) on port {$this->_session->switchPort['name']} "
            . "of switch {$this->_session->switch['name']}";

        $se->save();

        $this->_view->params = $this->_session;
        $this->_view->manufacturer = $manufacturer;

        if( $this->_config['sec']['security_violation']['alert_customers']
            && $this->_session->user->getOrSetGetPreference( SecEvent::TYPE_SECURITY_VIOLATION, 1 ) )
        {
	        $mail = new Zend_Mail();
            $mail->addTo( $this->_config['identity']['testemail'] /* $this->_session->cust['nocemail']*/, "NOC of {$this->_session->cust['name']}" );
            $mail->setSubject( "[" . $this->_config['identity']['orgname'] . " Autobot] Port Security Violation" );
            $this->_sendMail( $mail, 'sec/cust_security_violation.tpl', true, false, $this->_config['sec']['security_violation']['cc_opsauto'] );
        }

        if( $this->_config['sec']['security_violation']['alert_operations'] )
        {
            $this->_view->customer_notified = $this->_config['sec']['security_violation']['alert_customers'];

            $mail = new Zend_Mail();

            $mail->setSubject( "Port Security Violation :: {$this->_session->cust['shortname']} :: "
                . "{$this->_session->switch['name']} {$this->_session->switchPort['name']}"
            );

            $this->_sendMail( $mail, 'sec/ops_security_violation.tpl', true, true );
        }

    }

    /**
     * A utility function to send mails.
     *
     * In this SEC controller, we'll be sending a lot of mails based on alerts. This
     * utility function should reduce duplicate code.
     *
     * @param Zend_Mail $mail A Zend_Mail object (with a subject set for example)
     * @param string $tpl The template to use for the email content
     * @param bool $fops Add an identity.autobot from address
     * @param bool $tops Add an identity.autobot to address
     * @param bool $cops Add an identity.autobot CC address
     */
    private function _sendMail( $mail, $tpl, $fops = false, $tops = false, $cops = false )
    {
        $options = $this->_bootstrap->getApplication()->getOptions();

        $mail->setBodyText(
            $this->_view->render( $tpl )
        );

        if( $fops )
            $mail->setFrom( $options['identity']['autobot']['email'], $options['identity']['autobot']['name'] );

        if( $tops )
            $mail->addTo(   $options['identity']['autobot']['email'], $options['identity']['autobot']['name'] );

        if( $cops )
            $mail->addCc(   $options['identity']['autobot']['email'], $options['identity']['autobot']['name'] );

        return $mail->send();
    }

    /**
     * Get a Clicktell object to send SMS'
     *
     * @return INEX_SMS_Clickatell The Clickatell object
     */
    private function _getClickatell()
    {
        if( $this->_clickatell === null )
            $this->_clickatell = new INEX_SMS_Clickatell(
                $this->_config['sms']['clickatell']['username'],
                $this->_config['sms']['clickatell']['password'],
                $this->_config['sms']['clickatell']['api_id']
            );

        return $this->_clickatell;
    }
    
    /**
     * Get the logger object (and bootstrap it if not already done)
     *
     * @return Zend_Log The log object
     */
    protected function getLogger()
    {
        if( $this->_logger === null )
            $this->_logger = $this->_bootstrap->getResource( 'logger' );
                                                                
        return $this->_logger;
    }
                                                                                
}

