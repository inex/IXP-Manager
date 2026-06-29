<?php

namespace IXP\Mail\ApiKey;

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

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use IXP\Models\User;

class ExpiringSoon extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param Collection<int, \IXP\Models\ApiKey> $apiKeys
     */
    public function __construct(
        public User $user,
        public Collection $apiKeys
    ) {}

    public function build(): self
    {
        return $this->markdown('api-key.emails.expiring-soon')
            ->subject( config('identity.sitename') . ' - API key expiry reminder' );
    }
}
