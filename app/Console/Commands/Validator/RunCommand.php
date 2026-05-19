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
use IXP\Utils\Validation\ValidatorBackendFactory;
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
    protected $signature = 'validator:run {--timeout=60} {--refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Run system validation routines";

    /**
     * @var ValidationRunner[]
     */
    private array $backends;

    /**
     * Associative array of ResultType values to counts. Tracks number of results of each type.
     * @var array
     */
    private array $resultCounts;

    /**
     * Begin running validation suite.
     *
     * @throws \ReflectionException
     */
    public function handle( ValidatorBackendFactory $validation, ConcurrentJobRunner $runner): int
    {
        $jobID = (string) UUID::uuid4();

        $this->initEmptyResultsSummary();
        $this->backends = $validation->buildBackends($jobID);

        $jobs = [];
        foreach ($this->backends as $backend) {
            $jobs[] = fn() => $backend->run();
        }

        if ($this->option('refresh')) {
            $tablesSection = $this->output->getOutput()->section();
            $outputFn = $this->refreshResultsOutput($tablesSection);
        } else {
            $outputFn = $this->basicResultsOutput();
        }

        $runner->timeout( (int) ($this->option('timeout') ?: 60));
        $runner->run($jobs, function ( $taskKey, ValidationRunner $backend, $progress ) use ($outputFn) {
            foreach ($backend->getResults() as $result) {
                $this->recordResultType($result->type);
            }
            $outputFn($taskKey, $backend, $progress);
        });

        $summary = $this->buildResultsSummary();

        $this->info("Validations completed. " . $summary);

        return 0;
    }

    /**
     * Return a closure for basic results output: prints each backend as it finishes
     */
    private function basicResultsOutput(): \Closure
    {
        return function ( $taskKey, ValidationRunner $backend, $progress ) {
            $this->line("[{$progress}%] Finished: <comment>{$backend->getName()}</comment>");
            if (count($backend->getSoftware())) {
                $this->line(" * Software");
                foreach( $backend->getSoftware() as $software ) {
                    $this->line("  - {$software->software} {$software->version}");
                }
            }

            if (count($backend->getResults())) {
                $this->line(" * Results");
                foreach( $backend->getResults() as $result) {
                    $this->line("  - [{$result->type->value}] $result->message");
                }
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

            // Update backends with test results
            $this->backends[$taskKey] = $backend;

            // Create a copy of only the completed backends, sorted by Validator priority
            $backends = collect($this->backends)
                ->filter( fn( ValidationRunner $b ) => $b->isComplete() )
                ->sortBy( fn( ValidationRunner $b ) => $b->getPriority() )
                ->values();

            // Build list of software
            $softwareList = collect($backends)
                ->flatMap( fn( ValidationRunner $backend ) => $backend->getSoftware() )
                ->map(     fn( Software $software) => [ $software->software, $software->version ] )
                ->all();

            if (count($softwareList)) {
                $this->tablify($section, ['Software', 'Version'], $softwareList);
            }

            $resultsRows = collect($backends)->flatMap(function (ValidationRunner $backend, $index) use ($backends) {
                $results = collect($backend->getResults());
                $validatorName = $backend->getName();

                // 1. Generate the rows for this specific backend
                $rows = $results->isEmpty()
                    ? [[$validatorName, null, null]]
                    : $results->map(fn ($result, $key) => [
                        $key === 0 ? $validatorName : null,
                        $result->type->name,
                        $result->message
                    ])->all();

                // 2. Append a separator if it's not the last backend in the list
                if ($index < count($backends) - 1) {
                    $rows[] = [new TableSeparator(['colspan' => 3])];
                }

                return $rows;
            })->all();

            $this->tablify($section, ['Validator', 'Result', 'Message'], $resultsRows, 'System Validation');
        };
    }

    private function tablify(OutputInterface $output, array $headers, array $rows, ?string $title = null): void
    {
        $table = new Table($output)
            ->setHeaders( $headers )
            ->setStyle('default')
            ->setRows( $rows );
        if ($title) {
            $table->setHeaderTitle($title);
        }
        $table->render();
    }

    /**
     * Initialise the results counts array, which keeps a count of the number of results for each type
     */
    private function initEmptyResultsSummary(): void
    {
        $this->resultCounts = array_fill_keys(array_map(fn($enum) => $enum->value, ResultType::cases()), 0);
    }

    /**
     * Increase the result count for the given ResultType
     */
    private function recordResultType(ResultType $type): void
    {
        $this->resultCounts[$type->value]++;
    }

    /**
     * Summarise the result counts.
     */
    private function buildResultsSummary(): string
    {
        return collect($this->resultCounts)
            ->filter(fn(int $count) => $count > 0)
            ->implode(function(int $count, string $type): string {
                $style = $this->getStyleForResultType($type);
                return "<$style>" . strtolower($type) . ": {$count}</$style>";
            }, ", " );
    }

    /**
     * Return the style to use for the given ResultType
     */
    private function getStyleForResultType(string $type): string
    {
        return match( ResultType::from($type) ) {
            ResultType::Ok       => "info",
            ResultType::Error    => "comment",
            ResultType::Failure  => "error",
        };
    }
}
