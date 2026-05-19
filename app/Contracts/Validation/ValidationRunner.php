<?php

namespace IXP\Contracts\Validation;

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
     * Get the name of the validator being run
     */
    public function getName(): string;

    /**
     * Get the priority of the validator being run
     */
    public function getPriority(): int;

    /**
     * Create a and execute the validator instance so results are available
     */
    public function run(): static;

    /**
     * @return bool
     */
    public function isComplete(): bool;

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
}