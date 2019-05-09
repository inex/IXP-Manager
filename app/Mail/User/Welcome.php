<?php

namespace IXP\Mail\User;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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
    User as UserEntity
};

/**
 * Mailable for welcome email User
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\Customer
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Welcome extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * User
     * @var UserEntity
     */
    public $user;

    /**
     * Resend?
     * @var bool
     */
    public $resend;

    /**
     * Existing?
     * @var bool
     */
    public $existing;


    /**
     * Create a new message instance.
     *
     * @param UserEntity $user
     *
     * @param bool $resend
     *
     * @param bool $existing
     */
    public function __construct( UserEntity $user, bool $resend = false, bool $existing = false )
    {
        $this->user     = $user;
        $this->resend   = $resend;
        $this->existing = $existing;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown( $this->existing ? 'user.emails.welcome-existing' : 'user.emails.welcome' )
            ->subject( config('identity.sitename' ) . " - Your Access Details" );
        
    }
}
