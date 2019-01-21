<?php
/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace IXP\Http\Controllers\Api\V4;

use D2EM;

use Entities\{
    PatchPanel              as PatchPanelEntity,
    PatchPanelPort          as PatchPanelPortEntity
};

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * PatchPanelController
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PatchPanelController extends Controller {


    /**
     * Get all the patch panel ports available for use in the current patch panel
     *
     * @param   int     $id ID of the patch panel
     * @params  Request $request instance of the current HTTP request
     * @return  JsonResponse
     */
    public function getFreePatchPanelPort( Request $request, int $id ) {

        /** @var PatchPanelEntity $pp */
        if( !( $pp = D2EM::getRepository( PatchPanelEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        $listPorts = D2EM::getRepository( PatchPanelPortEntity::class )->getAvailablePorts( $pp->getId(), [$request->input('pppId' )] ) ;

        return response()->json(['listPorts' => $listPorts]);
    }


}
