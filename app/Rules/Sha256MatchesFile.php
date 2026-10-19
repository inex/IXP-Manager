<?php
/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace IXP\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * This class provides a validation rule for checking if a SHA-256 hash matches the hash of a specified file field.
 *
 * It does not make it mandatory to provide the sha256, or the file - this requirement must be enforced elsewhere.
 */
class Sha256MatchesFile implements ValidationRule, DataAwareRule
{
    /**
     * Request data via DataAwareRule
     * @var array
     */
    private array $data = [];

    public function __construct(
        private readonly string $fileField,
    ) {
        if ($this->fileField === '') {
            throw new \InvalidArgumentException('File field name cannot be empty');
        }
    }

    #[\Override]
    public function setData( array $data ): self
    {
        $this->data = $data;
        return $this;
    }

    #[\Override]
    public function validate( string $attribute, mixed $value, Closure $fail ): void
    {
        $file = $this->data[$this->fileField] ?? null;

        $haveValidFile = $file instanceof UploadedFile && $file->isValid();
        if( $value && $haveValidFile && $value !== hash_file( 'sha256', $file->getRealPath() ) ) {
            $fail( 'The sha256 checksum calculated on the server does not match the one you provided.' );
        }
    }
}