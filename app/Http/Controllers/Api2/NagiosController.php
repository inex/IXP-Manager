<?php namespace IXP\Http\Controllers\Api2;

use Illuminate\Http\Request;

class NagiosController extends Controller {

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


    private function generateBirdseyeDaemonMap( $class, &$map ) {
        foreach( $class as $key => $details ) {
            if( $details['api_type'] !== 'birdseye' ) {
                continue;
            }

            $map[$key] = $details;
        }
    }

    /**
     *
     * @return Response
     */
    public function birdseyeDaemons( $vlanid = null )
    {
        $map = [];

        if( $vlanid ) {
            if( !config('lookingglass.'.$vlanid, false ) ) {
                abort( 404, "No definition in config/lookingglass.php for the provided VLAN id." );
            }

            foreach( config('lookingglass.'.$vlanid) as $class ) {
                $this->generateBirdseyeDaemonMap($class,$map);
            }
        } else {
            foreach( config('lookingglass') as $vlanid => $vlanDetails ) {
                foreach( $vlanDetails as $class ) {
                    $this->generateBirdseyeDaemonMap($class,$map);
                }
            }
        }

        return response()
                ->view('api2/nagios/birdseye-daemons', ['map' => $map, 'vlanid' => $vlanid], 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
    }


}
