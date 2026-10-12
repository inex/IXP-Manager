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

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use IXP\Http\Requests\Trait\ValidatesFileUploads;
use IXP\Models\User;
use IXP\Rules\Sha256MatchesFile;

abstract class BaseDocstoreFileRequest extends FormRequest
{
    use ValidatesFileUploads;

    /**
     * Policy check is required, define abstract so we remember!
     */
    abstract public function authorize(): bool;

    /**
     * Return the implementation-specific file rule depending on whether it's store or update`
     */
    abstract public function getFileRule(): array;

    /**
     * @throws ValidationException
     */
    #[\Override]
    protected function prepareForValidation(): void
    {
        $this->ensureUploadIsValid( 'uploadedFile' );
    }

    /**
     * note: to keep psalm quiet, we define the optional purgeLogs here, even though it's only present in UpdateDocstoreFileRequest.
     *
     * @psalm-return array{description: string, docstore_directory_id: string, min_privs: string, purgeLogs?: list{'nullable', 'boolean'}, name: string, sha256: array<array-key, mixed>, uploadedFile: array<array-key, mixed>}
     */
    public function rules(): array
    {
        return [
            'name'                  => 'required|max:100',
            'description'           => 'nullable|string',
            'uploadedFile'          => $this->getFileRule(),
            'sha256'                => [ 'nullable', 'size:64', new Sha256MatchesFile( 'uploadedFile' ) ],
            'min_privs'             => 'required|integer|in:' . implode( ',', array_keys( User::$PRIVILEGES_TEXT_ALL ) ),
            'docstore_directory_id' => 'nullable|integer|exists:docstore_directories,id',
        ];
    }
}