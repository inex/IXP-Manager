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

namespace IXP\Mail\PeeringManager;

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
use IXP\Mail\Trait\MarkdownContent;

/**
 * Mailable for Peering manager
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @author     Thomas Kerin     <thomas@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\PeeringManager
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
final class RequestPeeringManager extends Mailable
{
    use Queueable, SerializesModels, MarkdownContent;

    /**
     * Create a new message instance.
     *
     * @param Customer              $peer
     * @param PeeringManagerRequest $r
     *
     */
    public function __construct(public readonly Customer $peer, PeeringManagerRequest $r )
    {
        $this->prepareFromRequest( $r );
        $this->userMarkdown = $r->message;
    }

    /**
     * Set up recipients and subject from a POST request.
     *
     * @param PeeringManagerRequest $r
     */
    protected function prepareFromRequest( PeeringManagerRequest $r ): static
    {
        if( !$r->sendtome ) {
            // recipients
            foreach( [ "to", "cc", "bcc" ] as $p ) {
                if ( !( $field = $r->input( $p ) ) ) {
                    continue;
                }

                foreach( explode( ',', $field ) as $emaddr ) {
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
     * Checks if we can send the email
     *
     * @throws MailableException
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

        if ( strlen($this->userMarkdown) === 0) {
            throw new MailableException( "Missing markdown email body" );
        }
    }
}