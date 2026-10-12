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

namespace IXP\Http\Requests\DocstoreCustomer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use IXP\Http\Requests\Trait\ValidatesFileUploads;
use IXP\Models\DocstoreCustomerFile;
use IXP\Models\User;
use IXP\Rules\Sha256MatchesFile;

final class StoreDocstoreCustomerFileRequest extends FormRequest
{
    use ValidatesFileUploads;

    /**
     * @throws ValidationException
     */
    #[\Override]
    protected function prepareForValidation(): void
    {
        $this->ensureUploadIsValid( 'uploadedFile' );
    }

    public function authorize(): bool
    {
        return $this->user()?->can( 'create', DocstoreCustomerFile::class ) ?? false;
    }

    /**
     * @psalm-return array{description: 'nullable|string', docstore_customer_directory_id: 'nullable|integer|exists:docstore_customer_directories,id', min_privs: non-falsy-string, name: 'required|max:100', sha256: list{'nullable', 'size:64', Sha256MatchesFile}, uploadedFile: list{'required', 'file'}}
     */
    public function rules(): array
    {
        return [
            'name'                  => 'required|max:100',
            'description'           => 'nullable|string',
            'uploadedFile'          => ['required', 'file'],
            'sha256'                => [ 'nullable', 'size:64', new Sha256MatchesFile( 'uploadedFile' ) ],
            'min_privs'             => 'required|integer|in:' . implode( ',', array_keys( User::$PRIVILEGES ) ),
            'docstore_customer_directory_id' => 'nullable|integer|exists:docstore_customer_directories,id',
        ];
    }
}