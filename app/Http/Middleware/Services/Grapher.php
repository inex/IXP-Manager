<?php

namespace IXP\Http\Middleware\Services;

use Closure;
use Grapher as GrapherFacade;

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
        // all graph requests require a certain basic set of parameters / defaults.
        // let's take care of that here
        $this->processParameters( $request );

        return $next($request);
    }

    /**
     * All graphs have common parameters. We process these here for every request - and set sensible defaults.
     *
     * @param \Illuminate\Http\Request  $request
     */
    private function processParameters( Request $request ) {

        // while the Grapher service stores the processed parameters in its own object, we update the $request
        // parameters here also just in case we need to final versions later in the request.
        $request->ixp      = GrapherFacade::processParameterIXP(      $request->input( 'ixp',      0  ) );
        $request->period   = GrapherFacade::processParameterPeriod(   $request->input( 'period',   '' ) );
        $request->category = GrapherFacade::processParameterCategory( $request->input( 'category', '' ) );
        $request->protocol = GrapherFacade::processParameterProtocol( $request->input( 'protocol', 0  ) );
        $request->type     = GrapherFacade::processParameterType(     $request->input( 'type',     '' ) );

        // future extension
        // $request->period_from = GrapherFacade::processParameterPeriod($request->input( 'period', '' ) );
        // $request->period_to   = GrapherFacade::processParameterPeriod($request->input( 'period', '' ) );
    }

}
