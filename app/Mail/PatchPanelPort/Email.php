<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Mail\PatchPanelPort;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use IXP\Exceptions\Mailable as MailableException;

use IXP\Http\Requests\EmailPatchPanelPort as EmailPatchPanelPortRequest;

use IXP\Mail\Trait\MarkdownContent;
use IXP\Models\PatchPanelPort;

/**
 * Mailable for patch panel emails
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin      <yann@islandbridgenetworks.ie>
 * @author     Thomas Kerin    <thomas@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @package    IXP\Mail\PatchPanelPort
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Email extends Mailable
{
    use Queueable, SerializesModels, MarkdownContent;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string The template of the email which we are building - specified in implementing classes
     */
    protected $tmpl;

    /**
     * Create a new message instance.
     *
     * @param PatchPanelPort $ppp
     */
    public function __construct(public readonly PatchPanelPort $ppp )
    {
        if( $c = $this->ppp->customer ) {
            $this->to( $c->nocemail, $c->abbreviatedName . ' NOC' );
        }

        $this->bcc( config( 'identity.support_email'), config( 'identity.name' ) . ' Operations' );
    }

    /**
     * Get the email addresses for to / cc / bcc
     *
     * @param string $recipientClass
     *
     * @return array Array of email addresses
     *
     * @psalm-return list{0?: mixed}
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
     * Populate the email template with view data. This is used to show the email
     * to the user in advance of sending.
     *
     * @return string The Email's body
     */
    public function getPopulatedEmailTemplate(): string
    {
        return view( $this->tmpl )->with( $this->buildViewData() )->render();
    }

    /**`
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
            if ( !( $field = $request->input( 'email_' . $r ) ) ) {
                continue;
            }

            foreach( explode(',', $field ) as $emaddr ) {
                $email = trim( $emaddr );
                if( filter_var( $email, FILTER_VALIDATE_EMAIL ) && !$this->$hasFn( $email ) ) {
                    $this->$r($email);
                }
            }
        }

        $this->subject( $request->email_subject );

        $this->userMarkdown = $request->email_text;
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

        if ( strlen($this->userMarkdown) === 0) {
            throw new MailableException( "Missing markdown email body" );
        }
    }
}