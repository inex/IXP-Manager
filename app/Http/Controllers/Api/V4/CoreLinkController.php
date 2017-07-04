<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Entities\{
    CoreLink        as CoreLinkEntity,
    CoreInterface   as CoreInterfaceEntity,
    SwitchPort      as SwitchPortEntity
};

/**
 * SwitcherController API Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class CoreLinkController extends Controller {

    /**
     * Delete a Core link
     *
     * Delete the associated core interface/ physical interface
     * Change the type of the switch ports to UNSET
     *
     * @param  int $id ID of the core link to delete
     * @return  JsonResponse
     */
    public function delete( int $id ) {

        /** @var CoreLinkEntity $cl */
        if( !( $cl = D2EM::getRepository( CoreLinkEntity::class )->find( $id ) ) ) {
            abort( 404 );
        }

        foreach( $cl->getCoreInterfaces() as $ci ){
            /** @var CoreInterfaceEntity $ci */
            $pi = $ci->getPhysicalInterface();
            $sp = $pi->getSwitchPort();

            $sp->setType( SwitchPortEntity::TYPE_UNSET );

            D2EM::remove( $pi );
            D2EM::remove( $ci );
        }
        D2EM::remove( $cl );
        D2EM::flush();

        return response()->json( [ 'success' => true ] );
    }


}