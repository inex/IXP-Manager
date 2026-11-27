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

namespace IXP\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use IXP\Models\User;

/**
 * Very basic form request used to enforce authorization.
 *  - Super user can access endpoint
 *  - Cust Admin can access endpoint so long as subject user is within same customer, and has privs <= CUST ADMIN (per how /user/list works for CUSTADMIN)
 *  - Cust user not allowed access endpoint
 */
class ResetTwoFactorLockoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (is_null($user)) {
            return false;
        }

        if ($user->isSuperUser()) {
            return true;
        }

        if ($user->isCustAdmin()) {
            $subjectUser = $this->route( 'user' );
            return $subjectUser->customers()
                ->where( 'cust.id', $user->custid ) // within same customer
                ->where( 'customer_to_users.privs', '<=', User::AUTH_CUSTADMIN) // not superuser
                ->exists();
        }

        return false;
    }
}