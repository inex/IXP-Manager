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

namespace IXP\Http\Requests\Docstore;

use IXP\Models\DocstoreFile;

final class UpdateDocstoreFileRequest extends BaseDocstoreFileRequest
{
    #[\Override]
    public function authorize(): bool
    {
        $file = $this->route('file') ?? DocstoreFile::class;

        return $this->user()?->can('update', $file) ?? false;
    }

    /**
     * When updating a DocstoreFile, changing the file is optional.
     */
    #[\Override]
    public function getFileRule(): array
    {
        return ['nullable', 'file'];
    }

    #[\Override]
    public function rules(): array
    {
        $rules = parent::rules();

        return array_merge($rules, [
            'purgeLogs' => ['nullable', 'boolean'],
        ]);
    }
}