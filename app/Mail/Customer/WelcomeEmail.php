<?php

namespace IXP\Mail\Customer;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Exceptions\Mailable as MailableException;

use IXP\Http\Requests\Customer\WelcomeEmail as WelcomeEmailRequest;

use IXP\Models\Customer;

/**
 * Mailable for Customer
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yanm Robin       <yann@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\Customer
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Customer
     */
    public $c;

    /**
     * @var WelcomeEmailRequest
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
     * @param Customer              $c
     * @param WelcomeEmailRequest   $r
     */
    public function __construct( Customer $c, WelcomeEmailRequest $r )
    {
        $this->c = $c;
        $this->r = $r;
        $this->prepareFromRequest($r);
        $this->prepareBody($r);
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
     * @param WelcomeEmailRequest $r
     *
     * @return WelcomeEmail
     */
    protected function prepareFromRequest( WelcomeEmailRequest $r ): self
    {
        // recipients
        foreach( [ 'to', 'cc', 'bcc' ] as $p ) {
            $hasFn = 'has' . ucfirst( $p );
            foreach( explode(',', $r->input( $p ) ) as $emaddr ) {
                $email = trim( $emaddr );
                if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                    $this->$p($email);
                }
            }
        }

        $this->subject( $r->subject );
        return $this;
    }

    /**
     * Set up Markdown body from a POST request.
     *
     * @param WelcomeEmailRequest $r
     *
     * @return WelcomeEmail
     */
    public function prepareBody( WelcomeEmailRequest $r ): self
    {
        // Templating is slightly awkward here as Laravel's Mailable is built around reading the
        // body from a template file be we have it via post.
        //
        // To work around this, we'll use a temporary file in a new view namespace.

        $body          = $r->message;
        $this->tmpfile = tempnam( sys_get_temp_dir(), 'welcome_email_' );
        $this->tmpname = basename( $this->tmpfile );
        $this->tmpfile = $this->tmpfile . '.blade.php';
        file_put_contents( $this->tmpfile, "@component('mail::message')\n\n" . $body . "\n\n@endcomponent\n" );
        view()->addNamespace('welcome_emails', sys_get_temp_dir() );
        $this->markdown( 'welcome_emails::' . $this->tmpname );
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
     * @throws MailableException
     */
    public function checkIfSendable(): void
    {
        if( !count( $this->to ) ) {
            throw new MailableException( "No valid recipients" );
        }

        if( !view()->exists( $this->markdown ) ) {
            throw new MailableException( "Could not create / load temporary template" );
        }
    }
}