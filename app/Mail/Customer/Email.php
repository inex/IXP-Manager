<?php

namespace IXP\Mail\Customer;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Illuminate\Contracts\Queue\ShouldQueue;

use Entities\{
    Customer as CustomerEntity
};
use IXP\Http\Requests\Customer\WelcomeEmail;

/**
 * Mailable for Customer
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yanm Robin       <yann@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\Customer
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Email extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var CustomerEntity
     */
    public $cust;

    /**
     * @var string
     */
    public $subject;

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
     * @param CustomerEntity $cust
     */
    public function __construct( CustomerEntity $cust ) {
        $this->cust    = $cust;
        $this->from( config('identity.email'), config('identity.name') );
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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this;
    }

    /**
     * Get the subject for an email
     *
     * @return string The subject for an email
     */
    public function getSubject(): string {
        return $this->subject;
    }

    /**
     * Get the email's body
     *
     * For this we assume Markdown and return the template as is (with
     * rendered data).
     *
     * @return string The Email's body
     * @throws
     */
    public function getBody(): string {
        return view( $this->tmpl )->with( $this->buildViewData() )->render();
    }

    /**
     * Set up recipients and subject from a POST request.
     *
     * @param \IXP\Http\Requests\WelcomeEmail $request
     */
    public function prepareFromRequest( WelcomeEmail $request ) {
        // recipients
        foreach( [ 'to', 'cc', 'bcc' ] as $r ) {
            $hasFn = 'has' . ucfirst( $r );
            foreach( explode(',', $request->input( $r ) ) as $emaddr ) {
                $email = trim( $emaddr );
                if( filter_var( $email, FILTER_VALIDATE_EMAIL ) && !$this->$hasFn( $email ) ) {
                    $this->$r($email);
                }
            }
        }

        $this->subject( $request->input('subject') );
    }

    /**
     * Set up Markdown body from a POST request.
     *
     * @param \IXP\Http\Requests\WelcomeEmail $request
     */

    public function prepareBody( WelcomeEmail $request ) {
        // Templating is slightly awkward here as Laravel's Mailable is built around reading the
        // body from a template file be we have it via post.
        //
        // To work around this, we'll use a temporary file in a new view namespace.

        $body          = $request->input('message');
        $this->tmpfile = tempnam( sys_get_temp_dir(), 'welcome_email_' );
        $this->tmpname = basename( $this->tmpfile );
        $this->tmpfile = $this->tmpfile . '.blade.php';
        file_put_contents( $this->tmpfile, "@component('mail::message')\n\n" . $body . "\n\n@endcomponent\n" );
        view()->addNamespace('welcome_emails', sys_get_temp_dir() );
        $this->markdown( 'welcome_emails::' . $this->tmpname );
    }

    /**
     * Checks if we can send the email
     * @throws MailableException
     */
    public function checkIfSendable() {
        if( !count( $this->to ) ) {
            throw new MailableException( "No valid recipients" );
        }

        if( !view()->exists( $this->markdown ) ) {
            throw new MailableException( "Could not create / load temporary template" );
        }
    }
}
