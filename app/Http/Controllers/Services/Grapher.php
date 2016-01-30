<?php

namespace IXP\Http\Controllers\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use IXP\Http\Requests;
use IXP\Http\Controllers\Controller;

use Carbon\Carbon;

class Grapher extends Controller
{
    //

    public function ixp( int $id, string $period, string $category, string $protocol, string $type, string $backend = null, Request $request ): Response {

        // Route::get( 'ixp/{id}/period/{period}/category/{category}/protocol/{protocol}/type/{type}', 'Grapher@ixp' );
        // http://holmes.inex.ie/mrtg/ixp_peering-aggregate-bits.log
        // http://holmes.inex.ie/mrtg/ixp_peering-aggregate-bits-day.png

        return (new Response( file_get_contents("http://holmes.inex.ie/mrtg/ixp_peering-aggregate-{$category}-day.png") ) )
              ->header('Content-Type', 'image/png' )
              ->header( 'Expires', Carbon::now()->addMinutes(5)->toRfc1123String() );

    }
}
