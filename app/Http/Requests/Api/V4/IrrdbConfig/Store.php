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

namespace IXP\Http\Requests\Api\V4\IrrdbConfig;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use IXP\Models\PatchPanelPort;
use IXP\Models\User;

/**
 * @property-read string|null $source
 * @property-read string|null $host
 * @property-read string|null $notes
 */
final class Store extends FormRequest
{
    /**
     * Determine if the user is authorised to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $us */
        $us = Auth::user();
        return $us->isSuperUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     *
     * @psalm-return array{host: 'nullable|string|max:255', source: 'required|string|max:255', notes: 'nullable|string'}
     */
    public function rules(): array
    {
        return [
            'host' => 'nullable|string|max:255',
            'source' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}