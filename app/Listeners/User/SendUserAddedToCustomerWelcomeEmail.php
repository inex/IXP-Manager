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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace IXP\Listeners\User;

use Illuminate\Support\Facades\Log;
use Mail;

use IXP\Events\User\UserAddedToCustomer as UserAddedToCustomerEvent;

use IXP\Mail\User\UserAddedToCustomer as UserAddedToCustomerMailable;

/**
 * SendUserAddedToCustomerWelcomeEmail Listener
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Listeners\User
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
final class SendUserAddedToCustomerWelcomeEmail
{
    public function handle( UserAddedToCustomerEvent $e ): void
    {
        Mail::to( $e->c2u->user->email )->send( new UserAddedToCustomerMailable( $e->c2u ) );
        Log::notice( "Emailing user " . $e->c2u->user->username . " to notify they were added to customer " . $e->c2u->customer->name );
    }
}