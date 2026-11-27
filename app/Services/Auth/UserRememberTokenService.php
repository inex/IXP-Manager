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

namespace IXP\Services\Auth;

use IXP\Models\User;

class UserRememberTokenService
{
    /**
     * Reset the `is_2fa_complete` flag on all of this user's remember-me tokens.
     *
     * This must be called whenever the user's 2FA secret is disabled, deleted or
     * reset (either by the user themselves or by an admin) so that a remember-me
     * cookie from a previous 2FA setup cannot be used to silently bypass 2FA for
     * a newly (re)enabled secret.
     */
    public function resetIs2faCompleteForTokens( User $user ): bool
    {
        return $user->userRememberTokens()->update( [ 'is_2fa_complete' => false ] ) >= 0;
    }

    public function markTokenIs2faComplete( User $user, #[\SensitiveParameter] string $token ): bool
    {
        return $user->userRememberTokens()->whereToken($token)->first()
            ?->record2faIsComplete() ?? false;
    }
}