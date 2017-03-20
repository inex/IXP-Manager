<?php

namespace IXP\Http\Controllers\Api\V4;

use D2EM;

use Entities\{
    Customer,
    PatchPanel
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller {

    /**
     * Get the switches for a customer
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $request instance of the current HTTP request
     * @return  array switches [id => name]
     */
    public function switches(Request $request, int $id): JsonResponse{
        if( !($customer = D2EM::getRepository( Customer::class )->find( $id ) ) ){
            abort( 404, 'No such customer' );
        }

        if( !($patchPanel = D2EM::getRepository( PatchPanel::class )->find( $request->input('patch_panel_id') ) ) ){
            abort( 404, 'No such patch panel' );
        }

        $switches = [];
        foreach($customer->getVirtualInterfaces() as $vi){
            foreach($vi->getPhysicalInterfaces() as $pi){
                $switch = $pi->getSwitchPort()->getSwitcher();
                if($switch->getCabinet()->getLocation()->getId() == $patchPanel->getCabinet()->getLocation()->getId()){
                    $switches[$switch->getId()] = $switch->getName();
                }
            }
        }
        return response()->json(['switchesFound' => boolval(count($switches)), 'switches' => $switches]);
    }

}