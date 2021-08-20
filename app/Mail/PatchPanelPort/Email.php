<?php

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

namespace IXP\Mail\PatchPanelPort;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use IXP\Exceptions\Mailable as MailableException;

use IXP\Http\Requests\EmailPatchPanelPort as EmailPatchPanelPortRequest;

use IXP\Models\PatchPanelPort;

/**
 * Mailable for patch panel emails
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin      <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @package    IXP\Mail\PatchPanelPort
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Email extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var PatchPanelPort
     */
    public $ppp;

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
     * @param PatchPanelPort $ppp
     */
    public function __construct( PatchPanelPort $ppp )
    {
        $this->ppp    = $ppp;

        if( $c = $this->ppp->customer ) {
            $this->to( $c->nocemail, $c->abbreviatedName . ' NOC' );
        }

        $this->bcc( env( 'IDENTITY_SUPPORT_EMAIL' ), env( 'IDENTITY_NAME') . ' Operations' );
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
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this;
    }

    /**
     * Get the email addresses for to / cc / bcc
     *
     * @param string $recipientClass
     *
     * @return array Array of email addresses
     */
    public function getRecipientEmails( string $recipientClass ): array
    {
        assert( in_array( $recipientClass, ['to','cc','bcc'] ) );

        $a = [];
        foreach( $this->$recipientClass as $t ) {
            $a = [ $t['address'] ];
        }

        return $a;
    }

    /**
     * Get the subject for an email
     *
     * @return string The subject for an email
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Get the email's body
     *
     * For this we assume Markdown and return the template as is (with
     * rendered data).
     *
     * @return string The Email's body
     */
    public function getBody(): string
    {
        return view( $this->tmpl )->with( $this->buildViewData() )->render();
    }

    /**
     * Set up recipients and subject from a POST request.
     *
     * @param EmailPatchPanelPortRequest $request
     *
     * @return void
     */
    public function prepareFromRequest( EmailPatchPanelPortRequest $request ): void
    {
        // in the constructor, we add the NOC address of the customer (if we have one)
        // to the recipients. This is for presetting the To: field in the HTML form.
        // we wipe there here and just use what was submitted in the form.
        $this->to = [];

        // recipients
        foreach( [ 'to', 'cc', 'bcc' ] as $r ) {
            $hasFn = 'has' . ucfirst( $r );
            foreach( explode(',', $request->input('email_' . $r ) ) as $emaddr ) {
                $email = trim( $emaddr );
                if( filter_var( $email, FILTER_VALIDATE_EMAIL ) && !$this->$hasFn( $email ) ) {
                    $this->$r($email);
                }
            }
        }

        $this->subject( $request->email_subject );
    }

    /**
     * Set up Markdown body from a POST request.
     *
     * @param EmailPatchPanelPortRequest $request
     *
     * @return void
     */
    public function prepareBody( EmailPatchPanelPortRequest $request ): void
    {
        // Templating is slightly awkward here as Laravel's Mailable is built around reading the
        // body from a template file be we have it via post.
        //
        // To work around this, we'll use a temporary file in a new view namespace.

        $body          = $request->email_text;
        $this->tmpfile = tempnam( sys_get_temp_dir(), 'ppp_email_' );
        $this->tmpname = basename( $this->tmpfile );
        $this->tmpfile .= '.blade.php';
        file_put_contents( $this->tmpfile, "@component('mail::message')\n\n" . $body . "\n\n@endcomponent\n" );
        view()->addNamespace('ppp_emails', sys_get_temp_dir() );
        $this->markdown( 'ppp_emails::' . $this->tmpname );
    }

    /**
     * Checks if we can send the email
     *
     * @return void
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