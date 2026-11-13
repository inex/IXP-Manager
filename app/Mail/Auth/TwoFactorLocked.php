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

namespace IXP\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use IXP\Models\Customer;

/**
 * Mailable for 2FA locked out notification
 */
final class TwoFactorLocked extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly ?Customer $referToCustAdmin = null,
    ){}

    public function build(): self
    {
        return $this->markdown( 'auth/emails/two-factor-locked' )
            ->subject( config('identity.sitename' ) . " - Account Restricted" );
    }
}