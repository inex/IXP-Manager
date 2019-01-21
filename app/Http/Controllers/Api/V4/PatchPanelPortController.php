<?php

namespace IXP\Http\Controllers\Api\V4;

use Auth, D2EM, Storage;

use Entities\{
    PatchPanelPort              as PatchPanelPortEntity,
    PatchPanelPortHistory       as PatchPanelPortHistoryEntity,
    PatchPanelPortFile          as PatchPanelPortFileEntity,

    PhysicalInterface           as PhysicalInterfaceEntity
};



use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;



/**
 * PatchPanelPortController
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PatchPanelPortController extends Controller {


    /**
     * Get the details of a patch panel port
     *
     * @param   int $id    The ID of the patch panel port to query
     * @param   bool $deep Return a deep array by including associated objects
     * @return  JsonResponse JSON customer object
     */
    public function detail( int $id, bool $deep = false ): JsonResponse {

        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find( $id ) ) ) {
            abort( 404, 'No such patch panel port' );
        }

        return response()->json( $ppp->jsonArray($deep) );
    }

    /**
     * Get extra details of a patch panel port
     *
     * @param   int $id    The ID of the patch panel port to query
     * @return  JsonResponse JSON customer object
     */
    public function detailDeep( int $id ): JsonResponse {
        return $this->detail( $id, true );
    }


}
