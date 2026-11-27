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

namespace IXP\Utils;

use Illuminate\Console\Application;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Process\InvokedProcess;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Laravel\SerializableClosure\SerializableClosure;

/**
 * ConcurrentJobRunner is a replacement for Laravels Concurrency::run method
 * which is unable to report results from a task as each task finishes.
 *
 * It uses the same internal artisan command to run serialized closures.
 *
 * Care must be taken that tasks handle all exceptions they generate.
 *
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class ConcurrentJobRunner
{
    /**
     * A timeout for the tasks to complete by
     */
    private ?int $timeout = 60;

    /**
     * Set a timeout for the tasks to complete by
     */
    public function timeout(?int $timeout): static
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Run a set of closures concurrently, and react as each one completes by calling $onTaskComplete.
     * If a timeout is set, and it is reached, then onTaskTimeout will be called with each timed out task key.
     *
     * @param \Closure|array<int|string, \Closure> $tasks  An array of \Closures or a single \Closure. Can be indexed,
     *                                                     or associative.
     * @param \Closure|null $onTaskComplete                Callback: fn($taskName, $result, $progressPercentage). If
     *                                                     $tasks is indexed or associative, $taskName is the index or key.
     * @param \Closure|null $onTaskTimeout                 Callback: fn($taskKey). If $tasks is indexed or associative,
     *                                                     $taskName is the index or key.
     * @param \Closure|null $onTaskError                   Callback: fn($taskKey, \Throwable). If $tasks is indexed or associative,
     *                                                     $taskName is the index or key. The Throwable originates from the task,
     *                                                     but was not caught and processed there.
     */
    public function run(\Closure|array $tasks, ?\Closure $onTaskComplete = null, ?\Closure $onTaskTimeout = null, ?\Closure $onTaskError = null): array
    {
        $tasks = Arr::wrap($tasks);
        $totalTasks = count($tasks);
        $artisanCommand = Application::formatCommandString('invoke-serialized-closure');

        // Keys of completed tasks go here:
        $completedTaskKeys = [];

        // Results of a task go here (keyed by task key)
        $finalResults = [];

        // Build our pending processes. We don't use Pool here because we need
        // direct access to the InvokedProcesses to monitor if they're still running.
        // Each task gets its own key, used by the child to HMAC-sign its result envelope.
        $envelopeKeys = [];
        $pendingProcesses = [];
        foreach( $tasks as $key => $task ) {
            $envelopeKeys[ $key ] = bin2hex( random_bytes( 32 ) );
            $pendingProcesses[ $key ] = Process::timeout( $this->timeout )
                ->command( $artisanCommand )
                ->path( base_path() )
                ->env( [
                    'LARAVEL_CONCURRENCY_HMAC_KEY' => $envelopeKeys[ $key ],
                    'LARAVEL_INVOKABLE_CLOSURE'    => base64_encode(
                        serialize( new SerializableClosure( $task ) )
                    ),
                ] );
        }

        // Start our processes in the background.
        /** @var InvokedProcess[] $invokedProcesses */
        $invokedProcesses = array_map( fn ( PendingProcess $pending ) => $pending->start(), $pendingProcesses );

        try {
            while ($this->anyProcessRunning($invokedProcesses)) {
                foreach ($invokedProcesses as $key => $process) {
                    $process->ensureNotTimedOut();

                    // Check if it's not running and we haven't handled it yet
                    if (!$process->running() && !in_array($key, $completedTaskKeys)) {
                        $completedTaskKeys[] = $key;

                        // Safely extract and unserialize the closure return value
                        $cmdJsonOutput = $this->processOutput($process, $envelopeKeys[$key]);

                        if (! $cmdJsonOutput['successful']) {
                            $exception = new $cmdJsonOutput['exception'](
                                ...(! empty(array_filter($cmdJsonOutput['parameters']))
                                ? $cmdJsonOutput['parameters']
                                : [$cmdJsonOutput['message']])
                            );
                            \Log::error("An unhandled exception was raised while processing concurrent jobs - tasks should ensure they catch their own exceptions");
                            if ($onTaskError) {
                                $onTaskError($key, $exception);
                            }
                            continue;
                        }

                        // success is true, so result is serialized
                        $taskOutput = unserialize($cmdJsonOutput['result']);

                        $finalResults[$key] = $taskOutput;

                        // Calculate real-time progress percentage
                        $progress = round((count($completedTaskKeys) / $totalTasks) * 100);

                        // Fire the optional feedback callback dynamically
                        if ($onTaskComplete) {
                            $onTaskComplete($key, $taskOutput, $progress);
                        }
                    }
                }

                usleep(100000); // 100ms throttle to prevent CPU pinning
            }

            // Final pass in case we missed any tasks that stopped
            foreach ($invokedProcesses as $key => $process) {
                if (!in_array($key, $completedTaskKeys)) {
                    $cmdJsonOutput = $this->processOutput($process, $envelopeKeys[$key]);

                    if (! $cmdJsonOutput['successful']) {
                        $exception = new $cmdJsonOutput['exception'](
                            ...(! empty(array_filter($cmdJsonOutput['parameters']))
                            ? $cmdJsonOutput['parameters']
                            : [$cmdJsonOutput['message']])
                        );
                        \Log::error("An unhandled exception was raised while processing concurrent jobs - tasks should ensure they catch their own exceptions");
                        if ($onTaskError) {
                            $onTaskError($key, $exception);
                        }
                        continue;
                    }

                    // success is true, so result is serialized
                    $taskOutput = unserialize($cmdJsonOutput['result']);

                    $finalResults[$key] = $taskOutput;

                    if ($onTaskComplete) {
                        $progress = round((count($finalResults) / $totalTasks) * 100);
                        $onTaskComplete($key, $taskOutput, $progress);
                    }
                }
            }

        } catch (ProcessTimedOutException $e) {
            foreach ($invokedProcesses as $key => $process) {
                if (!in_array($key, $completedTaskKeys)) {
                    if ($onTaskTimeout) {
                        $onTaskTimeout($key);
                    }
                }
            }
        }

        return $finalResults;
    }

    /**
     * Takes an InvokedProcess and decodes the returned result from invoke-serialized-closure
     *
     * If successful: [
     *     'successful' => true,
     *     'result' => 'PHP serialize()'d result as returned from task \Closure'
     * ]
     * If unsuccessful, the parameters to reconstruct the thrown exception: [
     *     'successful' => false,
     *     'exception' => get_class($e),
     *     'message' => $e->getMessage(),
     *     'file' => $e->getFile(),
     *     'line' => $e->getLine(),
     *     'parameters' => $parameters,
     * ]
     * The child writes the JSON inside a signed envelope:
     *     "\n@@LARAVEL-CONCURRENCY-V1@@<base64 json>@@<hmac-sha256 hex>@@\n"
     * Any other output (stray echoes, the gzip bytes from laravel/framework#59224) is ignored.
     * If no validly signed envelope is found, a failure result wrapping a RuntimeException is returned.
     *
     * Mirrors Laravel's ProcessDriver::parseEnvelope(), which is protected.
     */
    protected function processOutput(InvokedProcess $process, string $envelopeKey): array
    {
        $output = $process->output();
        $marker = '@@LARAVEL-CONCURRENCY-V1@@';
        $length = strlen( $output );
        $position = strrpos( $output, $marker );

        // Scan backwards so the last validly signed envelope wins
        while( $position !== false ) {
            if( preg_match( '/\G([A-Za-z0-9+\/=]+)@@([0-9a-f]{64})@@/', $output, $matches, 0, $position + strlen( $marker ) )
                && hash_equals( hash_hmac( 'sha256', $matches[1], $envelopeKey ), $matches[2] ) ) {
                $payload = base64_decode( $matches[1], true );
                $result = is_string( $payload ) ? json_decode( $payload, true ) : null;

                if( is_array( $result ) && array_key_exists( 'successful', $result ) ) {
                    return $result;
                }
            }

            $position = $position === 0 ? false : strrpos( $output, $marker, $position - $length - 1 );
        }

        return [
            'successful' => false,
            'exception'  => \RuntimeException::class,
            'message'    => 'The concurrent process did not return a valid result envelope. Output: '
                . Str::limit( trim( $output . "\n" . $process->errorOutput() ), 200 ),
            'parameters' => [],
        ];
    }

    /**
     * Helper to check if any tracked process is still running.
     *
     * @param InvokedProcess[] $processes
     * @return bool
     */
    private function anyProcessRunning(array $processes): bool
    {
        return array_any( $processes, fn(InvokedProcess $process ) => $process->running() );
    }
}