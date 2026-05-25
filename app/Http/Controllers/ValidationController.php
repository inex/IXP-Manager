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
use IXP\Utils\Validation\Result;
use IXP\Utils\Validation\Software;
use IXP\Utils\Validation\ValidatorBackendFactory;
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

    public function startSubmit( ValidatorBackendFactory $validation, ConcurrentJobRunner $runner ): RedirectResponse
    {
        $jobId = Uuid::uuid4()->toString();
        $backends = $validation->buildBackends( $jobId );

        $jobs = [];
        foreach( $backends as $backend ) {
            $jobs[] = fn() => $backend->run();
        }

        Log::info( "Initialised validation job", ['job_id' => $jobId]);
        Cache::put( $this->getJobKey( $jobId ), [
            'job_id'   => $jobId,
            'started'  => Carbon::now()->getTimestamp(),
            'finished' => null,
            'progress' => 0,
            'backends' => $backends,
            'error'    => null,
        ], 1200 );

        defer( fn() => $this->runValidations( $runner, $jobs, $jobId ) );

        Log::info( "Response returned" );

        return redirect()->route( 'validation@view', [ 'id' => $jobId ] );
    }

    private function runValidations( ConcurrentJobRunner $runner, array $jobs, string $jobId )
    {
        Log::info( "Validation job starting", ['job_id' => $jobId]);
        $jobError = null;
        $unsavedResults = [];
        try {
            $runner->run( $jobs, function( $taskKey, ValidationRunner $backend, $progress ) use ( $jobId, &$unsavedResults ) {
                try {
                    Cache::lock( $this->getLockKey( $jobId ), 5 )
                        ->block( 5, fn() => $this->updateWithValidationResults( $jobId, $taskKey, $backend, $progress ) );
                } catch( LockTimeoutException $e ) {
                    // failed to acquire the lock, god knows why. build up our results
                    // and save them at once at the end
                    $unsavedResults[] = [ $taskKey, $backend, $progress ];
                }
                // todo: need to catch all exceptions here, otherwise we interrupt processing other validations
            } );
        } catch( \Exception $e ) {
            $jobError = $e;
        } finally {
            // this is our last occasion to update the job.
            // - record when the processing finished
            // - record any errors that occurred during processing
            // - if any results could not be saved (due to lock timeout), save them now
            Cache::lock( $this->getLockKey( $jobId ), 5 )
                ->block( 5, fn() => $this->finalizeJob( $jobId, $unsavedResults, $jobError ) );
        }

        Log::info( "Validation job finalized", ['job_id' => $jobId]);

    }

    private function updateWithValidationResults( string $jobId, int|string $taskKey, ValidationRunner $backend, int|float $progress ): void
    {
        Log::info( "Received results for validation job ", [ 'job_id' => $jobId, 'validation' => $backend->getName() ] );
        $blob = Cache::get( $this->getJobKey( $jobId ) );
        $blob[ 'backends' ][ $taskKey ] = $backend;
        $blob[ 'progress' ] = $progress;
        Cache::put( $this->getJobKey( $jobId ), $blob, 1200 );
    }

    /**
     * @param string $jobId
     * @param array $unsavedResults
     * @param \Exception|null $jobError
     * @return void
     */
    private function finalizeJob(string $jobId, array $unsavedResults, ?\Exception $jobError): void
    {
        Log::info( "Finalizing validation job", [ 'job_id' => $jobId ] );

        $blob = Cache::get( $this->getJobKey( $jobId ) );
        $blob[ 'finished' ] = Carbon::now()->getTimestamp();
        $blob[ 'progress' ] = 100;
        if( count( $unsavedResults ) ) {
            foreach( $unsavedResults as [ $taskKey, $backend, $progress ] ) {
                $blob[ 'backends' ][ $taskKey ] = $backend;
            }
        }
        if( $jobError ) {
            // todo: decide what to store here in error handling review
            $blob[ 'error' ] = "Error during test run: " . $jobError->getMessage();
        }
        Cache::put( $this->getJobKey( $jobId ), $blob, 1200 );
    }

    public function view(string $id): View|RedirectResponse
    {
        if ( !( $job = Cache::get( $this->getJobKey( $id ) ) ) ) {
            AlertContainer::push("A validation task with the provided ID could not be found. Start another validation instead.");
            return redirect()->route('validation@start');
        }

        return view('validation/view2', [
            'jobId' => $id,
            'job' => $job,
        ]);
    }

    public function apiResults(string $id): JsonResponse
    {
        if ( !( $job = Cache::get( $this->getJobKey( $id ) ) ) ) {
            return response()->json( [], 404 );
        }

        $complete = array_all( $job['backends'] , fn(ValidationRunner $backend) => $backend->isComplete() );

        $prioritySortedBackends = collect($job['backends'])->sortBy(fn(ValidationRunner $backend) => $backend->getPriority())->all();
        $backends = [];
        foreach ($prioritySortedBackends as $backend) {
            if ( !$backend->isComplete() ) {
                continue;
            }
            $software = array_map( fn(Software $software) => ['name' => $software->software, 'version' => $software->version ], $backend->getSoftware() );
            $results = array_map( fn(Result $result) => ['message' => $result->message, 'type' => $result->type ], $backend->getResults() );
            $backends[] = [
                'name' => $backend->getName(),
                'priority' => $backend->getPriority(),
                'software' => $software,
                'results' => $results,
            ];
        }

        return response()->json( [
            'started' => $job['started'],
            'finished' => $job['finished'],
            'complete' => $complete,
            'validations' => $backends,
        ] );
    }
}