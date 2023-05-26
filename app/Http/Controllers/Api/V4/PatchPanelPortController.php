<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\JsonResponse;

use IXP\Models\PatchPanelPort;

/**
 * PatchPanelPortController
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PatchPanelPortController extends Controller
{
    /**
     * Get the details of a patch panel port
     *
     * @param   PatchPanelPort  $ppp    The ID of the patch panel port to query
     * @param   bool            $deep   Return a deep array by including associated objects
     *
     * @return  JsonResponse JSON customer object
     */
    public function detail( PatchPanelPort $ppp, bool $deep = false ): JsonResponse
    {
        return response()->json(
            PatchPanelPort::where( 'id', $ppp->id )
            ->with( 'PatchPanelPortFiles' )
            ->when( $deep, function( Builder $q ) {
                return $q->with( 'PatchPanel', 'SwitchPort.physicalInterface' );
            } )
            ->first()
        );
    }

    /**
     * Get extra details of a patch panel port
     *
     * @param   PatchPanelPort $ppp   Patch panel port to query
     *
     * @return  JsonResponse JSON customer object
     */
    public function detailDeep( PatchPanelPort $ppp ): JsonResponse
    {
        return $this->detail( $ppp, true );
    }
}
