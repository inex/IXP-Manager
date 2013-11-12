<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
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

        $customers = $this->getD2R( '\\Entities\\Customer' )->getCurrentActive( false, true, true );

        $asns = [];
        foreach( $customers as $c )
            $asns[ $c->getAutsys() ] = [ 
                'asmacro' => $c->resolveAsMacro( 4, 'AS' ),
                'name'    => $c->getName()
            ];
        
        ksort( $asns, SORT_NUMERIC );
        $this->view->asns = $asns;
        
        $content = $this->view->render( 'rir/tmpl/' . $tmpl . '.tpl' );
        
        if( $email )
            $this->emailRIR( $tmpl, $content, $email, $this->getParam( 'force', false ) );
        else
            echo $content;
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
                    ->setSubject( "Changes to {$tmpl} - KEYWORDS: diff" );
        
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
