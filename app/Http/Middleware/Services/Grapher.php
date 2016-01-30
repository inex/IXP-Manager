<?php

namespace IXP\Http\Middleware\Services;

use Closure;
use Illuminate\Http\Request;

use IXP\Contracts\Grapher as GrapherContract;

use IXP\Exceptions\Services\Grapher\{BadBackendException,CannotHandleRequestException};

class Grapher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Requests passing through this middleware are requests for graph images / logs / data.
        // This middleware must reolve the appropriate backend that can process the request.

        // if a specific backend has been requested, go straight there:
        dd( $request->route()->parameters() );
        if( $request->input( 'backend' ) ) {
            $backend = $this->getBackend( $request->input('backend') );
            if( !$backend->canHandle( $request ) ) {
                throw new CannotHandleRequestException('Requested backend cannot handle this request');
            }
        } else {
            die( 'FIXME BABY ONE MORE TIME' );
            $backend = $this->resolveBackend( $request );
        }



        return $next($request);
    }

    /**
     * Return the required grapher for the specified backend
     *
     * The given backend is validated via `resolveBackend()`.
     * @see IXP\Http\Middleware\Services\Grapher::resolveBackend()
     *
     * @param string $backend A specific backend to return.
     * @return \IXP\Contracts\Grapher
     */
    private function getBackend( string $backend ): GrapherContract {
        return App::make( config( 'grapher.providers.' . $this->validateBackend( $backend ) ) );
    }

    /**
     * As we allow multiple graphing backends, we need to be able to validate them
     *
     * @throws IXP\Exceptions\Services\Grapher\BadBackendException
     * @param string $backend
     * @return string
     */
    protected function validateBackend( string $backend ): string {
        if( !in_array($backend,config('grapher.backend') ) ) {
            throw new BadBackendException( 'No graphing provider enabled (see configs/grapher.php) for ' . $backend );
        }

        return $backend;
    }

}
