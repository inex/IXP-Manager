<?php

namespace IXP\Contracts\Validation;

use IXP\Utils\Validation\FailureInfo;
use IXP\Utils\Validation\Result;
use IXP\Utils\Validation\Software;

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
     * Return whether the Validator has finished running
     */
    public function isComplete(): bool;

    /**
     * Return whether the Validator encountered af failure (Exception was thrown)
     */
    public function isFailed(): bool;

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
     * Return any uncaught \Throwable that arises while running the Validator
     */
    public function getFailureInfo(): ?FailureInfo;
}