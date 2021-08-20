<?php

namespace IXP\Listeners\Auth;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
use Log;

use Illuminate\Auth\Events\Failed as FailedEvent;

/**
 * LoginFailed Listener
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Listeners
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LoginFailed
{
    /**
     * Handle a failed login event.
     *
     * @param  FailedEvent  $e
     *
     * @return void
     */
    public function handle( FailedEvent $e ): void
    {
        // TODO: Maybe we should persist failed logs into the DB instead and create a view in the backend
        Log::warning( 'Login failed for user [' . $e->credentials[ 'username' ] . '] from IP [' . ixp_get_client_ip() . ']' );
    }
}