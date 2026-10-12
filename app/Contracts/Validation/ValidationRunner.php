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

namespace IXP\Contracts\Validation;

use IXP\Services\Validation\Dto\FailureInfo;
use IXP\Services\Validation\Dto\Result;
use IXP\Services\Validation\Dto\Software;
use IXP\Services\Validation\Dto\ValidationReport;

/**
 * ValidationRunner is the interface for creating the underlying validator, performing
 * the validation, and returning the results.
 *
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
interface ValidationRunner
{
    /**
     * Return the underlying Validator instance
     */
    public function getValidator(): Validator;

    /**
     * Execute the validator instance so results are available
     */
    public function run(): static;

    /**
     * Return whether the Validator has finished either by returning
     * successfully, or, due to an unexpected error. This method does not
     * return true if the test timed out!
     */
    public function isComplete(): bool;

    /**
     * Return whether the Validator encountered af failure (Exception was thrown)
     */
    public function isFailed(): bool;

    /**
     * Return any uncaught \Throwable that arises while running the Validator.
     * The runner may still contain results.
     */
    public function getFailureInfo(): ?FailureInfo;

    /**
     * Set any exceptions reported while processing this job.
     */
    public function validatorFailure(\Throwable $e): void;

    /**
     * Return whether the Validation has been marked as timed out (usually by
     * the invoking process for record keeping)
     */
    public function isTimedOut(): bool;

    /**
     * Mark the Validation has having timed out. No results are expected.
     */
    public function markTimedOut(): void;

    /**
     * Get the software version information reported by the validation
     * @return Software[]
     */
    public function getSoftware(): array;

    /**
     * Get the information reported by the validation
     * @return Result[]
     */
    public function getResults(): array;

    /**
     * Produce a ValidationReport object based on the current state
     */
    public function toReport(): ValidationReport;

}