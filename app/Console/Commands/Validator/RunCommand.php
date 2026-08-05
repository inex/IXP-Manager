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

namespace IXP\Console\Commands\Validator;

use IXP\Console\Commands\Command;
use IXP\Contracts\Validation\ValidationRunner;
use IXP\Utils\ConcurrentJobRunner;
use IXP\Utils\Validation\Backend;
use IXP\Utils\Validation\Result;
use IXP\Utils\Validation\ResultType;
use IXP\Utils\Validation\Software;
use IXP\Utils\Validation\ValidationRunnerFactory;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

/**
 * Artisan command to run system validator routines
 *
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "validator:run
                                {--timeout=30     : Set a task timeout in seconds }
                                {--log-level=suggest  : Select the lowest log level to print. In ascending order: debug, info, suggest, warning, error }
    ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Run system validation routines";

    /**
     * List of result types which we are interested in seeing displayed.
     * @var ResultType[]
     */
    private array $logResultTypes;

    /**
     * @var ValidationRunner[]
     */
    private array $runners;

    /**
     * For summary purposes, count the number of result types
     */
    private array $resultTypeCount;

    /**
     * For summary purposes, count the number of validators which failed to complete
     */
    private int $failureCount = 0;

    /**
     * For summary purposes, count the number of timed out validators
     */
    private int $timedOutCount = 0;

    /**
     * Begin running validation suite.
     *
     * @throws \ReflectionException
     */
    public function handle( ValidationRunnerFactory $validation, ConcurrentJobRunner $jobRunner ): int
    {
        if (ResultType::tryFrom(strtoupper($this->option('log-level'))) === null) {
            $this->line("Unknown log level '{$this->option('log-level')}'");
            return 1;
        }
        $this->logResultTypes = $this->resultTypesToRender(ResultType::from(strtoupper($this->option('log-level'))));
        $this->initEmptyResultsSummary();
        $this->runners = $validation->getRunners();

        $jobs = [];
        foreach ( $this->runners as $runner) {
            $jobs[] = fn() => $runner->run();
        }

        $jobRunner
            ->timeout( (int) ( $this->option('timeout') ?: 30 ) )
            ->run( $jobs, $this->runnerReturned(...), $this->runnerTimedOut(...), $this->runnerFailure(...) );

        $this->printResultsFromRunners();

        $this->line("");
        $this->line("Log level: " . $this->option('log-level'));
        $this->line("Validations summary: " . $this->buildResultsSummary());

        if ( ( $failedRunners = array_filter( $this->runners, fn( ValidationRunner $runner ) => $runner->isFailed() ) ) ) {
            $this->line("");
            $this->line("<comment>The following validations encountered errors which prevented them from finishing:</comment>");
            foreach ($failedRunners as $failedRunner) {
                $failure = $failedRunner->getFailureInfo();
                $exceptionMessage = sprintf( " - '%s' encountered %s at %s:%d:\n%s",
                    $failedRunner->getValidator()->getName(), $failure->class, $failure->file, $failure->line , $failure->message);
                $this->line($exceptionMessage);
                $this->line("");
            }
        }

        if ( ( $timedOutRunners = array_filter($this->runners, fn( ValidationRunner $runner ) => $runner->isTimedOut() ) ) ) {
            $this->line("The following validations timed out before reporting their results");
            foreach ($timedOutRunners as $timedOutRunner) {
                $this->line(" * {$timedOutRunner->getValidator()->getName()}");
            }
        }

        return 0;
    }

    /**
     * Called when task successfully completes.
     */
    private function runnerReturned( int|string $taskKey, ValidationRunner $runner, float $progress ): void
    {
        // Update runners with test results
        $this->runners[$taskKey] = $runner;

        // Record result type, or failure count
        foreach ( $runner->getResults() as $result ) {
            $this->recordResultType( $result->type );
        }
        $this->failureCount += $runner->isFailed() ? 1 : 0;
        // do we want a progress bar or something?
    }

    /**
     * Called when a task is detected as having timed out. No information
     * about state returned, we just mark it as timed out.
     */
    private function runnerTimedOut( int|string $timedOutKey ): void
    {
        $this->runners[$timedOutKey]->markTimedOut();
        $this->timedOutCount++;
    }

    /**
     * Called when an UNHANDLED error bubbles up. ValidationRunner already catches all
     * errors raised by a Validator, so we are talking extremely unusual circumstances.
     * This should not happen in normal operation, however, we have code to handle
     * it because we don't want an unexpected failure to affect the running/reporting of
     * other validators.
     */
    private function runnerFailure( int|string $taskKey, \Throwable $exception ): void
    {
        $this->runners[$taskKey]->validatorFailure($exception);
        $this->failureCount++;
    }
    /**
     * Print a table of software versions, and runner results
     */
    private function printResultsFromRunners(): void
    {
        // Create a copy of only the completed runners, sorted by Validator priority
        $runners = collect($this->runners)
            ->filter( fn( ValidationRunner $b ) => $b->isComplete() || $b->isTimedOut() )
            ->sortBy( fn( ValidationRunner $b ) => $b->getValidator()->getPriority() )
            ->values();

        // Build list of software
        $softwareList = collect($runners)
            ->flatMap( fn( ValidationRunner $runner ) => $runner->getSoftware() )
            ->map(     fn( Software $software) => [ $software->software, $software->version ] )
            ->all();

        if (count($softwareList)) {
            new Table($this->output)
                ->setHeaders( [ 'Software', 'Version' ] )
                ->setStyle( 'default' )
                ->setRows( $softwareList )
                ->render();
        }

        $resultsRows = collect($runners)->flatMap(function ( ValidationRunner $runner, $index) use ($runners) {
            $results = collect($runner->getResults());
            $validatorName = $runner->getValidator()->getName();

            // Build rows containing the output for each runner
            if ($runner->isTimedOut()) {
                $rows = [[$validatorName, null, "<comment>The validator timed out before it reported any results</comment>"]];
            } else if ($results->isEmpty()) {
                $rows = [[$validatorName, null, "<comment>The validator did not report any results</comment>"]];
            } else {
                // Filter out irrelevant result types (based on log level setting) and return results
                $rows = $results
                    ->filter( fn ($result) => in_array( $result->type, $this->logResultTypes ) )
                    ->values()
                    ->map(fn (Result $result, $key ) => [
                        $key === 0 ? $validatorName : null,
                        $result->type->name,
                        $result->message
                    ])->all();

                // There _were_ results, but our log level excluded all of them. Provide an explanation.
                if ( count($rows) === 0 ) {
                    $rows[] = [$validatorName, null, "<comment>No results reached the log-level threshold</comment>"];
                }
            }

            if ( $runner->getFailureInfo() ) {
                $rows[] = [null, null, "<comment>This validator failed to complete due to an error</comment>"];
            }

            // Append a separator if it's not the last runner in the list
            if ($index < count($runners) - 1) {
                $rows[] = [new TableSeparator(['colspan' => 3])];
            }

            return $rows;
        })->all();

        new Table($this->output)
            ->setHeaders( [ 'Validator', 'Result', 'Message' ] )
            ->setStyle( 'default' )
            ->setRows( $resultsRows )
            ->setHeaderTitle( 'System Validation' )
            ->setColumnMaxWidth( 0, 20 )
            ->setColumnMaxWidth( 2, 80 )
            ->render();
    }

    /**
     * Assuming that ResultTypes are defined in the order of increasing severity,
     * take min log level, and only return result types with that level or higher.
     */
    private function resultTypesToRender(ResultType $minLevel): array
    {
        return array_slice(ResultType::cases(), array_search($minLevel, ResultType::cases()));
    }

    /**
     * Initialise the results counts array, which keeps a count of the number of results for each type
     */
    private function initEmptyResultsSummary(): void
    {
        $this->resultTypeCount = array_fill_keys(array_map(fn( $enum) => $enum->value, ResultType::cases()), 0);
    }

    /**
     * Increase the result count for the given ResultType
     */
    private function recordResultType(ResultType $type): void
    {
        $this->resultTypeCount[$type->value]++;
    }

    /**
     * Summarise the result counts.
     */
    private function buildResultsSummary(): string
    {
        $summary = collect($this->resultTypeCount)
            ->filter(fn(int $count) => $count > 0)
            ->implode(function(int $count, string $type): string {
                return strtolower($type) . ": {$count}";
            }, ", " ) . ".";

        if ($this->failureCount > 0) {
            $summary .= " " . $this->failureCount . " " . $this->pluralize("validator", $this->failureCount) . " failed due to an error.";
        }
        if ($this->timedOutCount > 0) {
            $summary .= " " . $this->timedOutCount . " " . $this->pluralize("validator", $this->timedOutCount) .  " timed out.";
        }
        return $summary;
    }

    private function pluralize(string $text, int $count): string
    {
        return $count === 1 ? $text : $text . "s";
    }
}
