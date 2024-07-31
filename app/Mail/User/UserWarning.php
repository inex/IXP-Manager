<?php

namespace IXP\Mail\User;

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
use IXP\Events\User\UserWarning as UserWarningEvent;

/**
 * Mailable for warning email User
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @category   User
 * @package    IXP\Mail\User
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserWarning extends Mailable
{
    use Queueable, SerializesModels;

    /**
     *
     * @var UserWarningEvent
     */
    public UserWarningEvent $event;

    public string $warningTitle;
    public string $errorMessage;

    /**
     * Create a new message instance.
     *
     * @param UserWarningEvent $e
     *
     * @return void
     */
    public function __construct( UserWarningEvent $e )
    {
        $this->event = $e;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        $this->warningTitle = $this->event->getTitle();
        $this->errorMessage = $this->event->getErrorMessage();

        return $this->markdown( 'user.emails.warning' )->subject( config('identity.sitename' ) . " - Warning" );
    }
}