<?php namespace IXP\Http\Controllers\Api;

use Illuminate\Http\Request;

class SflowReceiverController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct( Request $request )
    {
        parent::__construct();
        $this->assertSuperUser($request);
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function pretagMap()
    {
    }

}
