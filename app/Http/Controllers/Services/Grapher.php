<?php

namespace IXP\Http\Controllers\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use IXP\Http\Requests;
use IXP\Http\Controllers\Controller;

use Carbon\Carbon;

class Grapher extends Controller
{
    /**
     * the grapher service
     * @var \IXP\Services\Grapher
     */
    private $grapher;


    /**
     * Constructor
     */
    public function __construct( \IXP\Services\Grapher $grapher ) {
        $this->grapher = $grapher;
    }

    /**
     * Grapher accessor
     * @return \IXP\Services\Grapher
     */
    private function grapher(): \IXP\Services\Grapher {
        return $this->grapher;
    }

    //public function ixp( int $id, string $period, string $category, string $protocol, string $type, Request $request, string $backend = null ): Response {
    public function ixp( Request $request ): Response {

        // Route::get( 'ixp/{id}/period/{period}/category/{category}/protocol/{protocol}/type/{type}', 'Grapher@ixp' );
        // http://holmes.inex.ie/mrtg/ixp_peering-aggregate-bits.log
        // http://holmes.inex.ie/mrtg/ixp_peering-aggregate-bits-day.png

        dd( $this->grapher()->category() );

        return (new Response( file_get_contents("http://holmes.inex.ie/mrtg/ixp_peering-aggregate-{$category}-day.png") ) )
              ->header('Content-Type', 'image/png' )
              ->header( 'Expires', Carbon::now()->addMinutes(5)->toRfc1123String() );

    }
}
