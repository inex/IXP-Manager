<?php

declare(strict_types=1);

namespace IXP\Http\Requests\Trait;

use Illuminate\Validation\ValidationException;

trait ValidatesFileUploads
{
    /** This trait depends on the file method exposed on FormRequests. Add abstract definition. */
    abstract public function file($key = null, $default = null);

    /**
     * Check if a file had any upload errors, and throw validation error if so
     * @throws ValidationException
     */
    public function ensureUploadIsValid( string $field): void
    {
        // Check if Laravel's file wrapper caught a PHP upload size error
        if ($this->file($field) && $this->file($field)->getError() !== UPLOAD_ERR_OK) {
            throw ValidationException::withMessages([
                $field => $this->file($field)->getErrorMessage(),
            ]);
        }
    }
}