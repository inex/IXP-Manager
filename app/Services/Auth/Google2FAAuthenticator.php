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

namespace IXP\Services\Auth;

use PragmaRX\Google2FALaravel\Support\Authenticator as BaseAuthenticator;

/**
 * Google2FA Authenticator
 *
 * Thin wrapper around the PragmaRX authenticator that lets us ask whether the
 * current session has already passed 2FA *without* verifying (and therefore
 * without consuming a brute force attempt on) any submitted one time password.
 *
 * @category   IXP
 * @package    IXP\Services\Auth
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Google2FAAuthenticator extends BaseAuthenticator
{
    /**
     * True if the request can pass without an OTP check (2FA disabled, not activated
     * for this user, or already completed in this session).
     *
     * @return bool
     */
    public function canPassWithoutOtp(): bool
    {
        return $this->canPassWithoutCheckingOTP();
    }
}
