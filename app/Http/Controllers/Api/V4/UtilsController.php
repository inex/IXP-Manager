<?php

namespace IXP\Http\Controllers\Api\V4;

use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Parsedown;


class UtilsController extends Controller {


    /**
     * Turn markdown text into HTML
     *
     * @param   Request $request
     * @return  JsonResponse JSON object with 'html' element
     */
    public function markdown( Request $request ): JsonResponse {
        $pd = new Parsedown();

        return response()->json([
            'html' => $pd->text( $request->get( 'text', '' ) )
        ]);
    }
}
