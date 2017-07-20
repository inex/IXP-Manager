<?php
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

namespace IXP\Http\Controllers;

use App, D2EM;

use IXP\Services\Grapher\Graph;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

use Entities\{
    Customer         as CustomerEntity,
    Infrastructure   as InfrastructureEntity,
    VirtualInterface as VIEntity,
    Vlan             as VlanEntity
};

/**
 * Statistics Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Statistics
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StatisticsController extends Controller
{

    /**
     * Process and update request parameters for standard graph attributes: period, category, protocol, type.
     *
     * These are safe for use from the request.
     *
     * @param \Illuminate\Http\Request $r
     */
    private function processGraphParams( Request $r ) {
        $r->period   = Graph::processParameterPeriod(   $r->input( 'period',   '' ) );
        $r->category = Graph::processParameterCategory( $r->input( 'category', '' ) );
        $r->protocol = Graph::processParameterProtocol( $r->input( 'protocol', '' ) );
        $r->type     = Graph::processParameterType(     $r->input( 'type',     '' ) );
    }

    /**
     * Display all member graphs
     *
     * @return  View
     */
    public function members( Request $r ) : View {

        $grapher = App::make('IXP\Services\Grapher');
        $this->processGraphParams($r);

        // do we have an infrastructure?
        $infra = false;
        if( $r->input( 'infra' ) && ( $infra = D2EM::getRepository(InfrastructureEntity::class) ->find($r->input('infra')) ) ) {
            $targets = D2EM::getRepository( VIEntity::class )->getObjectsForInfrastructure( $infra );
        } else {
            $targets = D2EM::getRepository( CustomerEntity::class )->getCurrentActive( false, true, false );
        }


        $graphs = [];
        foreach( $targets as $t ) {
            if( $infra ) {
                $g = $grapher->virtint( $t );
            } else {
                $g = $grapher->customer( $t );
            }

            $g->setType(     Graph::TYPE_PNG )
                ->setProtocol( $r->protocol   )
                ->setCategory( $r->category   )
                ->setPeriod(   $r->period     );

            $graphs[] = $g;
        }

        return view( 'statistics/members' )->with([
            'graph'        => $graphs[0] ?? false,  // sample graph as all types/protocols/categories/periods will be the same
            'graphs'       => $graphs,
            'r'            => $r,
            'infras'       => D2EM::getRepository( InfrastructureEntity::class )->getNames(),
            'infra'        => $infra ?? false,
        ]);
    }
}
