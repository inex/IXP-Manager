<?php

declare(strict_types=1);

namespace IXP\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use IXP\Contracts\Validation\ValidationRunner;
use IXP\Utils\ConcurrentJobRunner;
use IXP\Utils\Validation\CallToActionLink;
use IXP\Utils\Validation\Result;
use IXP\Utils\Validation\Software;
use IXP\Utils\Validation\ValidationRunnerFactory;
use IXP\Utils\View\Alert\Container as AlertContainer;
use Ramsey\Uuid\Uuid;

class ValidationController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private function getJobKey( string $jobId ): string
    {
        return "validation:job:$jobId";
    }

    private function getLockKey( string $jobId ): string
    {
        return "validation:lock:$jobId";
    }

    public function startForm(): View
    {
        return view( 'validation/start-form' );
    }

    public function startSubmit( ValidationRunnerFactory $validation, ConcurrentJobRunner $runner ): RedirectResponse
    {
        $jobId = Uuid::uuid4()->toString();
        $backends = $validation->getRunners();

        $jobs = [];
        foreach( $backends as $backend ) {
            $jobs[] = fn() => $backend->run();
        }

        Log::info( "Initialised validation job", ['job_id' => $jobId]);
        Cache::store('file')->put( $this->getJobKey( $jobId ), [
            'job_id'   => $jobId,
            'started'  => Carbon::now()->getTimestamp(),
            'finished' => null,
            'progress' => 0,
            'backends' => $backends,
            'error'    => null,
        ], 1200 );

        defer( fn() => $this->runValidations( $runner, $jobs, $jobId ) );

        return redirect()->route( 'validation@view', [ 'id' => $jobId ] );
    }

    /**
     * Take a list of \Closures and run them concurrently. Results are written to the cache as they come in.
     * If we fail to acquire the lock, save the update for the finalization step.
     *
     * todo: do we need locks when this orchestration code is gathering the results and writing
     * to the cache in a single thread?
     */
    private function runValidations( ConcurrentJobRunner $runner, array $jobs, string $jobId )
    {
        Log::debug( "Validation job starting", ['job_id' => $jobId]);

        $unsavedResults      = [];
        $unsavedTimedOutJobs = [];
        $fatalErrors         = [];

        $runner->timeout(30);

        try {
            $runner->run( $jobs, function( $taskKey, ValidationRunner $backend, $progress ) use ( $jobId, &$unsavedResults ) {
                try {
                    Cache::store('file')->lock( $this->getLockKey( $jobId ), 5 )
                        ->block( 5, fn() => $this->updateWithValidationResults( $jobId, $taskKey, $backend, $progress ) );
                } catch( LockTimeoutException $e ) {
                    // as we failed to acquire the lock keep the result so we can apply it during finalizeJob
                    $unsavedResults[] = [ $taskKey, $backend, $progress ];
                }
            }, function( $taskKey ) use ( $jobId, &$unsavedTimedOutJobs ) {
                try {
                    Cache::store('file')->lock( $this->getLockKey( $jobId ), 5 )
                        ->block( 5, fn() => $this->updateTaskMarkTimedOut( $jobId, $taskKey ) );
                } catch( LockTimeoutException $e ) {
                    // as we failed to acquire the lock keep the result so we can apply it during finalizeJob
                    $unsavedTimedOutJobs[] = [ $taskKey ];
                }
            }, function ($taskKey, \Throwable $exception) use ($jobId, &$fatalErrors) {
                try {
                    Cache::store('file')->lock( $this->getLockKey( $jobId ), 5 )
                        ->block( 5, fn() => $this->updateWithFatalError( $jobId, $taskKey, $exception ) );
                } catch( LockTimeoutException $e ) {
                    // as we failed to acquire the lock keep the result so we can apply it during finalizeJob
                    $fatalErrors[] = [ $taskKey, $exception ];
                }
            } );
        } finally {
            try {
                Cache::store('file')->lock( $this->getLockKey( $jobId ), 5 )
                    ->block( 10, fn() => $this->finalizeJob( $jobId, $unsavedResults, $unsavedTimedOutJobs, $fatalErrors ) );
            } catch ( LockTimeoutException $e ) {
                \Log::warning("Failed to finalize validation job. It appears if the lock is still held by something.");
            }
        }

        Log::info( "Validation job finalized", ['job_id' => $jobId]);
    }

    /**
     * Mark this validation has having timed out.
     */
    private function updateTaskMarkTimedOut( string $jobId, int|string $taskKey ): void
    {
        $blob = Cache::store('file')->get( $this->getJobKey( $jobId ) );
        Log::warning( "Marking Validation task as timed out", [ 'job_id' => $jobId, 'task' => $taskKey, 'validation' => $blob['backends'][$taskKey]->getValidator()->getName() ] );
        $blob[ 'backends' ][ $taskKey ]->markTimedOut();
        Cache::store('file')->put( $this->getJobKey( $jobId ), $blob, 1200 );
    }

    private function updateWithFatalError(string $jobId, int|string $taskKey, \Throwable $e): void
    {
        $blob = Cache::store('file')->get( $this->getJobKey( $jobId ) );
        Log::warning( "Validation task produced an unhandled exception", [ 'job_id' => $jobId, 'task' => $taskKey, 'validation' => $blob['backends'][$taskKey]->getValidator()->getName() ] );
        $blob[ 'backends' ][ $taskKey ]->validatorFailure($e);
        Cache::store('file')->put( $this->getJobKey( $jobId ), $blob, 1200 );
    }

    /**
     * Take the returned ValidationRunner and serialize it to cache. In this method, it is
     * either completed successfully, or we caught an exception.
     */
    private function updateWithValidationResults( string $jobId, int|string $taskKey, ValidationRunner $backend, int|float $progress ): void
    {
        $blob = Cache::store('file')->get( $this->getJobKey( $jobId ) );
        Log::debug( "Recording results of completed validation job", [ 'job_id' => $jobId, 'taskkey' => $taskKey, 'validation' => $backend->getValidator()->getName() ] );
        $blob[ 'backends' ][ $taskKey ] = $backend;
        $blob[ 'progress' ] = $progress;
        Cache::store('file')->put( $this->getJobKey( $jobId ), $blob, 1200 );
    }

    /**
     * @param string $jobId
     * @param array $unsavedResults
     * @param array $unsavedTimedOutJobs
     * @param \Exception|null $jobError
     * @return void
     */
    private function finalizeJob(string $jobId, array $unsavedResults, array $unsavedTimedOutJobs, array $fatalErrors): void
    {
        Log::debug( "Finalizing validation job", [ 'job_id' => $jobId ] );

        $blob = Cache::store('file')->get( $this->getJobKey( $jobId ) );
        $blob[ 'finished' ] = Carbon::now()->getTimestamp();
        $blob[ 'progress' ] = 100;
        foreach( $unsavedResults as [ $taskKey, $backend, $progress ] ) {
            $blob[ 'backends' ][ $taskKey ] = $backend;
        }
        foreach ( $unsavedTimedOutJobs as $taskKey ) {
            $blob[ 'backends' ][ $taskKey ]->markTimedOut();
        }
        foreach( $fatalErrors as [$taskKey, $exception] ) {
            $blob[ 'backends' ][ $taskKey ]->validatorFailure($exception);
        }
        Cache::store('file')->put( $this->getJobKey( $jobId ), $blob, 1200 );
    }

    public function view(string $id): View|RedirectResponse
    {
        if ( !Cache::store('file')->get( $this->getJobKey( $id ) ) ) {
            AlertContainer::push("A validation task with the provided ID could not be found. Start another validation instead.");
            return redirect()->route('validation@start');
        }

        return view('validation/view2', [
            'jobId' => $id,
        ]);
    }
}