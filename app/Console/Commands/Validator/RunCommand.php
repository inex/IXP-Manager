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
use IXP\Utils\Validation\ResultType;
use IXP\Utils\Validation\Software;
use IXP\Utils\Validation\ValidationRunnerFactory;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;

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
                                {--timeout=30     : Set a task timeout in seconds}
                                {--simple-output  : Print results in sequence, don't use tables or refreshing}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Run system validation routines";

    /**
     * @var Backend[]
     */
    private array $backends;

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
    public function handle( ValidationRunnerFactory $validation, ConcurrentJobRunner $runner ): int
    {
        $this->initEmptyResultsSummary();
        $this->backends = $validation->getRunners();

        $jobs = [];
        foreach ($this->backends as $backend) {
            $jobs[] = fn() => $backend->run();
        }

        if ($this->option('simple-output')) {
            $outputFn = $this->basicResultsOutput();
        } else {
            $tablesSection = $this->output->getOutput()->section();
            $outputFn = $this->refreshResultsOutput($tablesSection);
        }

        $runner->timeout( (int) ( $this->option('timeout') ?: 30 ) );
        $runner->run($jobs, function ( $taskKey, ValidationRunner $backend, $progress ) use ($outputFn) {
            // Update backends with test results
            $this->backends[$taskKey] = $backend;

            // Record result type, or failure count
            foreach ($backend->getResults() as $result) {
                $this->recordResultType($result->type);
            }
            $this->failureCount += $backend->isFailed() ? 1 : 0;

            // Call output function to render an update
            $outputFn($taskKey, $backend, $progress);
        }, function ($timedOutKey) {
            // Mark process as timed out:
            $backend = $this->backends[$timedOutKey];
            $backend->markTimedOut();
            $this->timedOutCount++;
        }, function ($taskKey, \Throwable $exception) use (&$fatalError) {
            // Mark task as failed due to unhandled exception:
            $backend = $this->backends[$taskKey];
            $backend->validatorFailure($exception);
            $this->failureCount++;
        });

        $summary = $this->buildResultsSummary();

        $this->line("Validations summary: " . $summary);

        if ( ( $failed = array_filter( $this->backends, fn( ValidationRunner $runner ) => $runner->isFailed() ) ) && !empty( $failed ) ) {
            $this->line("");
            $this->line("<comment>The following validations encountered errors which prevented them from finishing:</comment>");
            foreach ($failed as $backend) {
                $failure = $backend->getFailureInfo();
                $exceptionMessage = sprintf( " - '%s' encountered %s at %s:%d:\n%s",
                    $backend->getValidator()->getName(), $failure->class, $failure->file, $failure->line , $failure->message);
                $this->line($exceptionMessage);
                $this->line("");
            }
        }

        if ( ( $timedOut = array_filter($this->backends, fn( ValidationRunner $runner ) => $runner->isTimedOut() ) ) && !empty( $timedOut ) ) {
            $this->line("The following validations timed out before reporting their results");
            foreach ($timedOut as $backend) {
                $this->line(" * {$backend->getValidator()->getName()}");
            }
        }

        return 0;
    }

    /**
     * Return a closure for basic results output: prints each backend as it finishes
     */
    private function basicResultsOutput(): \Closure
    {
        return function ( $taskKey, ValidationRunner $backend, $progress ) {
            $this->line("[{$progress}%] Finished: <comment>{$backend->getValidator()->getName()}</comment>");
            if ( count( $backend->getSoftware() ) ) {
                $this->line(" * Software");
                foreach( $backend->getSoftware() as $software ) {
                    $this->line("  - {$software->software} {$software->version}");
                }
            }

            if ( count( $backend->getResults() ) ) {
                $this->line(" * Results");
                foreach( $backend->getResults() as $result) {
                    $this->line("  - [{$result->type->value}] $result->message");
                }
            }

            if ( ($failure = $backend->getFailureInfo() ) ) {
                $exceptionMessage = sprintf("%s at %s:%d: %s",
                    $failure->class, $failure->file, $failure->line , $failure->message);
                $this->line(" * Failure: " . $exceptionMessage);
            }
        };
    }

    /**
     * Return a closure for a refreshing results output. Prints tables of software and validator
     * results that update with each completed validator
     */
    private function refreshResultsOutput(ConsoleSectionOutput $section): \Closure
    {
        return function ( $taskKey, Backend $backend, $progress ) use ($section) {
            $section->clear();

            // Create a copy of only the completed backends, sorted by Validator priority
            $backends = collect($this->backends)
                ->filter( fn( ValidationRunner $b ) => $b->isComplete() || $b->isTimedOut() )
                ->sortBy( fn( ValidationRunner $b ) => $b->getValidator()->getPriority() )
                ->values();

            // Build list of software
            $softwareList = collect($backends)
                ->flatMap( fn( ValidationRunner $backend ) => $backend->getSoftware() )
                ->map(     fn( Software $software) => [ $software->software, $software->version ] )
                ->all();

            if (count($softwareList)) {
                new Table($section)
                    ->setHeaders( [ 'Software', 'Version' ] )
                    ->setStyle( 'default' )
                    ->setRows( $softwareList )
                    ->render();
            }

            $resultsRows = collect($backends)->flatMap(function (ValidationRunner $backend, $index) use ($backends) {
                $results = collect($backend->getResults());
                $validatorName = $backend->getValidator()->getName();

                // 1. Generate the rows for this specific backend
                if ($backend->isTimedOut()) {
                    $rows = [[$validatorName, null, "<comment>The validator timed out before it reported any results</comment>"]];
                } else if ($results->isEmpty()) {
                    $rows = [[$validatorName, null, "<comment>The validator did not report any results</comment>"]];
                } else {
                    $rows = $results->map(fn ($result, $key) => [
                        $key === 0 ? $validatorName : null,
                        $result->type->name,
                        $result->message
                    ])->all();
                }

                if ($backend->getFailureInfo()) {
                    $rows[] = [null, null, "<comment>This validator failed to complete due to an error</comment>"];
                }

                // 2. Append a separator if it's not the last backend in the list
                if ($index < count($backends) - 1) {
                    $rows[] = [new TableSeparator(['colspan' => 3])];
                }

                return $rows;
            })->all();

            new Table($section)
                ->setHeaders( [ 'Validator', 'Result', 'Message' ] )
                ->setStyle( 'default' )
                ->setRows( $resultsRows )
                ->setHeaderTitle( 'System Validation' )
                ->setColumnMaxWidth( 0, 20 )
                ->setColumnMaxWidth( 2, 80 )
                ->render();
        };
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
