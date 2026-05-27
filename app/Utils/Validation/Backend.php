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

namespace IXP\Utils\Validation;

use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\ValidationRunner;
use IXP\Contracts\Validation\Validator;

/**
 * Backend.
 *  - Used by Validators to report their results (using ValidationBackend)
 *  - Invoked by tests to run the validation and report results (via ValidationRunner)
 *
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class Backend implements ValidationBackend, ValidationRunner
{
    public function __construct( private(set) string $validatorClass ) { }

    /**
     * @var Software[]
     */
    private array $software = [];

    /**
     * @var Result[]
     */
    private array $results = [];

    /**
     * whether the test suite has completed
     */
    private bool $complete = false;

    /**
     * Store details about any raised exception
     */
    private ?FailureInfo $failureInfo = null;

    /**
     * Cached validator instance
     */
    private ?Validator $validator = null;

    public function getValidator(): Validator
    {
        if (null === $this->validator) {
            $this->validator = new $this->validatorClass();
        }
        return $this->validator;
    }

    public function isComplete(): bool
    {
        return $this->complete;
    }

    public function isFailed(): bool
    {
        return $this->failureInfo != null;
    }

    public function getFailureInfo(): ?FailureInfo
    {
        return $this->failureInfo;
    }

    public function run(): static
    {
        try {
            $this->getValidator()->run($this);
        } catch (\Throwable $e) {
            $this->validatorFailure($e);
        }
        $this->complete = true;
        return $this;
    }

    public function getSoftware(): array
    {
        return $this->software;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function software(string $name, string $version): void
    {
        $this->software[] = new Software($name, $version);
    }

    public function debug( string $message): void
    {
        $this->results[] = new Result($message, ResultType::Debug);
    }

    public function info( string $message): void
    {
        $this->results[] = new Result($message, ResultType::Info);
    }

    public function warning(string $message): void
    {
        $this->results[] = new Result($message, ResultType::Warning);
    }

    public function error(string $message): void
    {
        $this->results[] = new Result($message, ResultType::Error);
    }

    private function validatorFailure(\Throwable $e): void
    {
        $this->failureInfo = FailureInfo::fromThrowable($e);
    }
}