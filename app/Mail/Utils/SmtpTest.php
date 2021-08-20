<?php

namespace IXP\Mail\Utils;

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
use Illuminate\Support\Facades\Mail;
/**
 * Mailable for SMTP test utility
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @package    IXP\Mail\Utils
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SmtpTest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var SwiftMail SMTP dialog logging
     */
    private $logger = null;

    /**
     * @var bool Debug flag to indicate if the SMTP dialog should be recorded
     */
    protected $debug;

    /**
     * SmtpTest constructor.
     * @param bool $debug If true, record the SMTP dialog
     */
    public function __construct( bool $debug = false )
    {
        $this->debug = $debug;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        if( $this->debug ) {
            $this->logger = new \Swift_Plugins_Loggers_ArrayLogger;
            Mail::getSwiftMailer()->registerPlugin( new \Swift_Plugins_LoggerPlugin($this->logger));
        }

        return $this->markdown( 'utils.emails.smtp-test' )
                    ->subject( 'SMTP test email from IXP Manager' );
    }

    /**
     * @return SwiftMail|null
     */
    public function logger()
    {
        return $this->logger;
    }
}
