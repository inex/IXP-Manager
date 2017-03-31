<?php

namespace IXP\Http\Controllers\Api\V4;

use D2EM;


use Entities\{
    VlanInterface as VlanInterfaceEntity
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Repositories\Layer2Address as Layer2AddressRepository;


class VlanInterfaceController extends Controller {


    /**
     * get all Layer2Address for a VlanInterface
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @return  JsonResponse
     */
    public function getL2A( int $id ) : JsonResponse{

        if( !( $id ) || !( $vli =  D2EM::getRepository( VlanInterfaceEntity::class )->find( $id ) ) ){
            return abort( 404 );
        }

        /** @var VlanInterfaceEntity $vli */
        return response()->json( $vli->l2aArray( true ) );

    }
}