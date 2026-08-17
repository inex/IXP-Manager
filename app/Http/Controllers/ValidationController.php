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

namespace IXP\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use IXP\Contracts\Validation\ValidationRunner;
use IXP\Utils\ConcurrentJobRunner;
use IXP\Services\Validation\Dto\JobState;
use IXP\Services\Validation\ValidationRunnerFactory;
use IXP\Utils\View\Alert\Container as AlertContainer;
use Ramsey\Uuid\Uuid;

class ValidationController
{
    /**
     * Show the from to start off validations
     */
    public function startForm(): View
    {
        return view( 'validation/start-form' );
    }

    /**
     * This endpoint creates a new validation job, starts the tasks in the background,
     * and redirects to the
     */
    public function startSubmit( ValidationRunnerFactory $validation, ConcurrentJobRunner $runner ): RedirectResponse
    {
        $jobState = JobState::create( Uuid::uuid4()->toString(), $validation->getRunners() );
        Log::info( "Initialised validation job", [ 'job_id' => $jobState->jobId ] );

        $this->persistJobState( $jobState );
        defer( fn() => $this->runValidations( $runner, $jobState ) );

        return redirect()->route( 'validation@view', [ 'id' => $jobState->jobId ] );
    }

    /**
     * Serializes the job state instance and persists to cache
     */
    private function persistJobState( JobState $jobState ): void
    {
        Cache::store('file')->put( JobState::getCacheKey( $jobState->jobId ), $jobState, 1200 );
    }

    /**
     * Runs jobs in JobState, updating its state and persisting to cache.
     */
    private function runValidations( ConcurrentJobRunner $runner, JobState $jobState ): void
    {
        Log::debug( "Validation job starting", ['job_id' => $jobState->jobId]);

        $runner->timeout(20);

        $jobs = [];
        foreach( $jobState->runners as $backend ) {
            $jobs[] = fn() => $backend->run();
        }

        try {
            $runner->run( $jobs, function( int|string $taskKey, ValidationRunner $backend, int|float $progress ) use ( $jobState ) {
                $jobState->recordValidatorResults($taskKey, $backend, $progress);
                $this->persistJobState( $jobState );
            }, function( int|string $taskKey ) use ( $jobState ) {
                $jobState->markTaskTimedOut( $taskKey );
                $this->persistJobState( $jobState );
            }, function ( int|string $taskKey, \Throwable $throwable) use ( $jobState ) {
                $jobState->markTestFailed( $taskKey, $throwable );
                $this->persistJobState( $jobState );
            } );
        } finally {
            $jobState->finalizeCompletedJob();
            $this->persistJobState( $jobState );
        }

        Log::info( "Validation job finalized", [ 'job_id' => $jobState->jobId ] );
    }

    public function view(string $id): View|RedirectResponse
    {
        if ( !Cache::store( 'file' )->get( JobState::getCacheKey( $id ) ) ) {
            AlertContainer::push( "A validation task with the provided ID could not be found. Start another validation instead." );
            return redirect()->route( 'validation@start' );
        }

        return view( 'validation/view', [
            'jobId' => $id,
        ] );
    }
}