<?php namespace IXP\Http\Controllers\Api2;

use IXP\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use D2EM;
use Auth;

class Controller extends BaseController {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    protected function getApiUser( Request $request ) {
        // authenticate
        if( ( $apikey = $request->input('apikey') ) === null ) {
            abort( 401, 'API key required' );
        }

        try {
            $key = D2EM::createQuery(
                    "SELECT a FROM \\Entities\\ApiKey a WHERE a.apiKey = ?1" )
                ->setParameter( 1, $apikey )
                ->useResultCache( true, 3600, 'oss_d2u_user_apikey_' . $apikey )
                ->getSingleResult();
        } catch( \Doctrine\ORM\NoResultException $e ) {
            abort( 403, 'Valid API key required' );
        }


        Auth::login( $key->getUser() );

        $user = $key->getUser();

        $key->setLastseenAt( new \DateTime() );
        $key->setLastseenFrom( $_SERVER['REMOTE_ADDR'] );
        D2EM::flush();

        return $key->getUser();
    }

    protected function assertSuperUser( Request $request ) {
        $u = $this->getApiUser($request);

        if( !$u->isSuperUser() ) {
            abort( 403, 'Insufficent permissions' );
        }
    }

}
