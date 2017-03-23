<?php

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


namespace IXP\Mail\PatchPanelPort;

use Entities\PatchPanelPort as PatchPanelPortEntity;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
//use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Mailable for patch panel emails
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @package    IXP\Mail\PatchPanelPort
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Email extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var PatchPanelPortEntity
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
     * Create a new message instance.
     *
     * @param PatchPanelPortEntity $ppp
     */
    public function __construct( PatchPanelPortEntity $ppp ) {
        $this->ppp    = $ppp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    abstract public function build();

    /**
     * Get the default recipient(s) for these emails
     *
     * @return array Array of email addresses
     */
    public function getRecipients(): array {
        if( $this->ppp->getCustomer() ) {
            return [ $this->ppp->getCustomer()->getNocemail() ];
        }

        return [];
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
     */
    public function getBody(): string {
        return view( $this->tmpl, $this->buildViewData() )->render();
    }
}
