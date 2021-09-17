<?php

namespace IXP\Http\Middleware\Services;

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
use App, Closure;

use Illuminate\Http\Request;

use IXP\Services\Grapher as GrapherService;
use IXP\Services\Grapher\Graph;

use IXP\Services\Grapher\Graph\{
    Infrastructure    as InfrastructureGraph,
    Vlan              as VlanGraph,
    Switcher          as SwitchGraph,
    Location          as LocationGraph,
    CoreBundle        as CoreBundleGraph,
    Trunk             as TrunkGraph,
    PhysicalInterface as PhysIntGraph,  // member physical port
    VirtualInterface  as VirtIntGraph,  // member LAG
    Customer          as CustomerGraph, // member agg over all physical ports
    VlanInterface     as VlanIntGraph,  // member VLAN interface
    P2p               as P2pGraph,
    Latency           as LatencyGraph
};
/**
 * Middleware: Grapher
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robon       <yann@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Http\Middleware\Grapher
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Grapher
{
    /**
     * Handle an incoming request.
     *
     * @param   Request     $r
     * @param   Closure     $next
     *
     * @return mixed
     */
    public function handle( Request $r, Closure $next )
    {
        // get the grapher service
        $grapher = App::make( GrapherService::class );

        // all graph requests require a certain basic set of parameters / defaults.
        // let's take care of that here
        $graph = $this->processParameters( $r, $grapher );

        // so we know what graph we need and who's looking for it
        // let's authorise for access (this throws an exception)
        $graph->authorise();

        // For PHP unit tests, we're currently just testing for authorisation.
        // The $request->attributes->add(['graph' => $graph]); doesn't work in the tests
        // for an unknown reason so just abort here if in test mode:
        if( env( 'IXP_PHPUNIT_RUNNING', false ) ) {
            abort( 200 );
        }

        $r->attributes->add( ['graph' => $graph] );

        return $next( $r );
    }

    /**
     * All graphs have common parameters. We process these here for every request - and set sensible defaults.
     *
     * @param Request           $r
     * @param GrapherService    $grapher
     *
     * @return Graph
     */
    private function processParameters( Request $r, GrapherService $grapher ): Graph
    {
        // while the Grapher service stores the processed parameters in its own object, we update the $request
        // parameters here also just in case we need to final versions later in the request.

        $target = explode( '/', $r->path() );
        $target = array_pop( $target );

        $r->period   = Graph::processParameterPeriod(   $r->period );
        $r->category = Graph::processParameterCategory( $r->category );
        $r->protocol = Graph::processParameterProtocol( $r->protocol );
        $r->type     = Graph::processParameterType(     $r->type );

        switch( $target ) {
            case 'ixp':
                $r->id = 1;
                $graph = $grapher->ixp()->setParamsFromArray( $r->all() );
                break;

            case 'infrastructure':
                $infra = InfrastructureGraph::processParameterInfrastructure( (int)$r->input( 'id', 0 ) );
                $r->infrastructure = $infra->id;
                $graph = $grapher->infrastructure( $infra )->setParamsFromArray( $r->all() );
                break;

            case 'vlan':
                $vlan = VlanGraph::processParameterVlan( (int)$r->input( 'id', 0 ) );
                $r->vlan = $vlan->id;
                $graph = $grapher->vlan( $vlan )->setParamsFromArray( $r->all() );
                break;

            case 'trunk':
                $trunkname = TrunkGraph::processParameterTrunkname( (string)$r->input( 'id', '' ) );
                $r->trunkname = $trunkname;
                $graph = $grapher->trunk( $trunkname )->setParamsFromArray( $r->all() );
                break;

            case 'corebundle':
                $corebundle = CoreBundleGraph::processParameterCoreBundle( (int)$r->input( 'id', 0 ) );
                $side       = CoreBundleGraph::processParameterSide( $r->input( 'side', 'a' ) );
                $r->corebundle = $corebundle->id;
                $r->side       = $side;
                $graph = $grapher->coreBundle( $corebundle, $side )->setParamsFromArray( $r->all() );
                break;

            case 'location':
                $location = LocationGraph::processParameterLocation( (int)$r->input( 'id', 0 ) );
                $r->location = $location->id;
                $graph = $grapher->location( $location )->setParamsFromArray( $r->all() );
                break;

            case 'switch':
                $switch = SwitchGraph::processParameterSwitch( (int)$r->input( 'id', 0 ) );
                $r->switch = $switch->id;
                $graph = $grapher->switch( $switch )->setParamsFromArray( $r->all() );
                break;

            case 'physicalinterface':
                $physint = PhysIntGraph::processParameterPhysicalInterface( (int)$r->input( 'id', 0 ) );
                $r->physint = $physint->id;
                $graph = $grapher->physint( $physint )->setParamsFromArray( $r->all() );
                break;

            case 'virtualinterface':
                $virtint = VirtIntGraph::processParameterVirtualInterface( (int)$r->input( 'id', 0 ) );
                $r->virtint = $virtint->id;
                $graph = $grapher->virtint( $virtint )->setParamsFromArray( $r->all() );
                break;

            case 'customer':
                $customer = CustomerGraph::processParameterCustomer( (int)$r->input( 'id', 0 ) );
                $r->customer = $customer->id;
                $graph = $grapher->customer( $customer )->setParamsFromArray( $r->all() );
                break;

            case 'vlaninterface':
                $vlanint = VlanIntGraph::processParameterVlanInterface( (int)$r->input( 'id', 0 ) );
                $r->vlanint = $vlanint->id;
                $graph = $grapher->vlanint( $vlanint )->setParamsFromArray( $r->all() );
                break;

            case 'latency':
                $vli = LatencyGraph::processParameterVlanInterface( (int)$r->input( 'id', 0 ) );
                $r->vli = $vli;
                $graph = $grapher->latency( $vli )->setParamsFromArray( $r->all() );
                break;

            case 'p2p':
                $srcvlanint = P2pGraph::processParameterSourceVlanInterface(      (int)$r->input( 'svli', 0 ) );
                $dstvlanint = P2pGraph::processParameterDestinationVlanInterface( (int)$r->input( 'dvli', 0 ) );
                $r->srcvlanint = $srcvlanint->id;
                $r->dstvlanint = $dstvlanint->id;
                $graph = $grapher->p2p( $srcvlanint, $dstvlanint )->setParamsFromArray( $r->all() );
                break;
            default:
                abort(404, 'No such graph type');
        }
        /** @var Graph $graph */
        return $graph;
    }
}