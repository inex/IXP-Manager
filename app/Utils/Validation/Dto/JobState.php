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

namespace IXP\Utils\Validation\Dto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use IXP\Contracts\Validation\ValidationRunner;

/**
 * JobState contains the state for a validation run, so it can be persisted
 * and have subsequent updates take place. It contains meta information about
 * the task (time started, time finished, progress) as well as wrapping the
 * state about each validation
 */
class JobState implements \JsonSerializable
{
    private(set) ?int $finishedAt = null;
    private(set) int|float $progress = 0;

    public function __construct(
        public readonly string $jobId,
        public readonly int    $startedAt,
        /** @var ValidationRunner[] */
        private(set) array           $runners = [],
    ) {}

    /**
     * Create an instance of JobState for the provided $runners, identified
     * by $jobId
     * @param string $jobId
     * @param ValidationRunner[] $runners
     * @return JobState
     */
    public static function create(string $jobId, array $runners = []): self
    {
        return new self(
            jobId: $jobId,
            startedAt: Carbon::now()->getTimestamp(),
            runners: $runners,
        );
    }

    /**
     * Build a cache key for the provided $jobId
     */
    public static function getCacheKey(string $jobId): string
    {
        return "validation:job:$jobId";
    }

    /**
     * Adds final info once all a job's validators have finished
     */
    public function finalizeCompletedJob(): void
    {
        $this->finishedAt = Carbon::now()->getTimestamp();
        $this->progress = 100;
    }

    /**
     * Used to mark a validator as having timed out
     */
    public function markTaskTimedOut( int|string $taskKey ): void
    {
        Log::warning( "Marking Validation task as timed out", [
            'job_id' => $this->jobId,
            'task' => $taskKey,
            'validation' => $this->runners[ $taskKey ]->getValidator()->getName()
        ] );

        $this->runners[ $taskKey ]->markTimedOut();
    }

    /**
     * Used to record that an unhandled exception has caused a ValidationRunner to fail
     */
    public function markTestFailed( int|string $taskKey, \Throwable $t ): void
    {
        Log::warning( "Validation task produced an unhandled exception", [
            'job_id' => $this->jobId,
            'task' => $taskKey,
            'validation' => $this->runners[ $taskKey ]->getValidator()->getName()
        ] );

        $this->runners[ $taskKey ]->validatorFailure($t);
    }

    /**
     * Record state of a completed ValidationRunner
     */
    public function recordValidatorResults( int|string $taskKey, ValidationRunner $runner, int|float $progress ): void
    {
        Log::debug( "Recording results of completed validation job", [
            'job_id' => $this->jobId,
            'taskkey' => $taskKey,
            'validation' => $runner->getValidator()->getName()
        ] );

        $this->runners[ $taskKey ] = $runner;
        $this->progress = $progress;
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        $runners = collect($this->runners);

        return [
            'started'     => $this->startedAt,
            'finished'    => $this->finishedAt,
            'progress'    => $this->progress,
            'complete'    => $runners
                ->every( fn(ValidationRunner $runner) => $runner->isComplete() || $runner->isTimedOut() ),
            'validations' => $runners
                ->sortBy( fn(ValidationRunner $runner) => $runner->getValidator()->getPriority() )
                ->map( fn(ValidationRunner $runner) => $runner->toReport() )
                ->values()
                ->all(),
        ];
    }
}