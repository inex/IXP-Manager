<?php

namespace IXP\Mail\PeeringManager;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use IXP\Exceptions\Mailable as MailableException;

use IXP\Http\Requests\PeeringManagerRequest;

use IXP\Models\{
    Customer,
    User
};

/**
 * Mailable for Peering manager
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yanm Robin       <yann@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\Customer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RequestPeeringManager extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Customer
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
     * @param Customer              $peer
     * @param PeeringManagerRequest $r
     *
     */
    public function __construct( Customer $peer, PeeringManagerRequest $r )
    {
        $this->peer = $peer;
        $this->prepareFromRequest( $r );
        $this->prepareBody( $r );
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
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
    protected function prepareFromRequest( PeeringManagerRequest $r ): RequestPeeringManager
    {
        if( !$r->sendtome ) {
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

        $this->subject( $r->subject );

        return $this;
    }

    /**
     * Set up Markdown body from a POST request.
     *
     * @param PeeringManagerRequest $r
     *
     * @return RequestPeeringManager
     */
    public function prepareBody( PeeringManagerRequest $r ): RequestPeeringManager
    {
        // Templating is slightly awkward here as Laravel's Mailable is built around reading the
        // body from a template file be we have it via post.
        //
        // To work around this, we'll use a temporary file in a new view namespace.

        $body          = $r->message;
        $this->tmpfile = tempnam( sys_get_temp_dir(), 'request_peering_email_' );
        $this->tmpname = basename( $this->tmpfile );
        $this->tmpfile .= '.blade.php';
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
    public function build(): self
    {
        return $this;
    }

    /**
     * Checks if we can send the email
     *
     * @param bool $sendtome
     *
     * @return void
     *
     * @throws
     */
    public function checkIfSendable( bool $sendtome ): void
    {
        if( config( "ixp.peering_manager.testmode" ) ) {
            if( !config( "ixp.peering_manager.testemail" ) ) {
                throw new MailableException( "Peering Manager test mode enabled but testemail not defined in config file." );
            }

            if( !filter_var( config( "ixp.peering_manager.testemail" ) , FILTER_VALIDATE_EMAIL ) ) {
                throw new MailableException( "Peering Manager testemail not a valid email." );
            }

            $this->to  = [];
            $this->cc  = [];
            $this->bcc = [];

            $this->to( config( "ixp.peering_manager.testemail" ), "Test Email" );

        } else {
            $user = User::find( Auth::id() );
            if( $sendtome ) {
                $this->to  = [];
                $this->cc  = [];
                $this->bcc = [];
                $this->to( $user->email, $user->username );
            } else {
                $this->to( $this->peer->peeringemail, $this->peer->name . " Peering Team" );
                $this->cc( $user->customer->peeringemail,  $user->customer->name . " Peering Team" );
                $this->replyTo( $user->customer->peeringemail,  $user->customer->name . " Peering Team" );
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