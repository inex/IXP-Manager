<?php

namespace IXP\Mail\PeeringManager;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Entities\{
    Customer as CustomerEntity
};

use IXP\Exceptions\Mailable as MailableException;

use IXP\Http\Requests\PeeringManagerRequest;

use Auth;

/**
 * Mailable for Peering manager
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yanm Robin       <yann@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\Customer
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RequestPeeringManager extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var CustomerEntity
     */
    public $peer;

    /**
     * @var PeeringManagerRequest
     */
    public $r;

    /**
     * @var string The template to use to create the email
     */
    protected $tmpl;

    /**
     * @var string Temporary view file
     */
    protected $tmpfile;

    /**
     * @var string Temporary view name
     */
    protected $tmpname;

    /**
     * Create a new message instance.
     *
     * @param CustomerEntity $peer
     * @param PeeringManagerRequest $r
     *
     */
    public function __construct( CustomerEntity $peer, PeeringManagerRequest $r ) {
        $this->peer = $peer;
        $this->prepareFromRequest($r);
        $this->prepareBody($r);
    }

    /**
     * Destructor
     */
    public function __destruct() {
        // remove temporary file if it exists
        if( $this->tmpfile && file_exists( $this->tmpfile ) ){
            @unlink( $this->tmpfile );
        }
    }


    /**
     * Set up recipients and subject from a POST request.
     *
     * @param PeeringManagerRequest $r
     *
     * @return RequestPeeringManager
     */
    protected function prepareFromRequest( PeeringManagerRequest $r ) {

        if( !$r->input( "sendtome" ) ) {
            // recipients
            foreach( [ "to", "cc", "bcc" ] as $p ) {
                foreach( explode(',', $r->input( $p ) ) as $emaddr ) {
                    $email = trim( $emaddr );
                    if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                        $this->$p( $email );
                    }
                }
            }
        }

        $this->subject( $r->input('subject') );

        return $this;
    }

    /**
     * Set up Markdown body from a POST request.
     *
     * @param PeeringManagerRequest $r
     *
     * @return RequestPeeringManager
     */
    public function prepareBody( PeeringManagerRequest $r )
    {
        // Templating is slightly awkward here as Laravel's Mailable is built around reading the
        // body from a template file be we have it via post.
        //
        // To work around this, we'll use a temporary file in a new view namespace.

        $body          = $r->input('message');
        $this->tmpfile = tempnam( sys_get_temp_dir(), 'request_peering_email_' );
        $this->tmpname = basename( $this->tmpfile );
        $this->tmpfile = $this->tmpfile . '.blade.php';
        file_put_contents( $this->tmpfile, "@component('mail::message')\n\n" . $body . "\n\n@endcomponent\n" );
        view()->addNamespace('request_peering_emails', sys_get_temp_dir() );
        $this->markdown( 'request_peering_emails::' . $this->tmpname );
        return $this;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this;
    }

    /**
     * Checks if we can send the email
     *
     * @param boolean $sendtome
     *
     * @throws MailableException
     */
    public function checkIfSendable( $sendtome ) {
        if( config( "ixp.peering_manager.testmode" ) ) {

            if( !config( "ixp.peering_manager.testemail" ) ) {
                throw new MailableException( "Peering Manager test mode enabled but testemail not defined in config file." );
            } else {
                if( !filter_var( config( "ixp.peering_manager.testemail" ) , FILTER_VALIDATE_EMAIL ) ) {
                    throw new MailableException( "Peering Manager testemail not a valid email." );
                } else {
                    $this->to  = [];
                    $this->cc  = [];
                    $this->bcc = [];

                    $this->to( config( "ixp.peering_manager.testemail" ), "Test Email" );
                }
            }

        } else {

            if( $sendtome ) {
                $this->to  = [];
                $this->cc  = [];
                $this->bcc = [];
                $this->to( Auth::getUser()->getEmail(), Auth::getUser()->getFormattedName() );
            } else {
                $this->to( $this->peer->getPeeringemail(), $this->peer->getName() . " Peering Team" );
                $this->cc( Auth::getUser()->getCustomer()->getPeeringemail(),  Auth::getUser()->getCustomer()->getName() . " Peering Team" );
                $this->replyTo( Auth::getUser()->getCustomer()->getPeeringemail(),  Auth::getUser()->getCustomer()->getName() . " Peering Team" );
            }
        }

        if( !count( $this->to ) ) {
            throw new MailableException( "No valid recipients" );
        }

        if( !view()->exists( $this->markdown ) ) {
            throw new MailableException( "Could not create / load temporary template" );
        }
    }
}
