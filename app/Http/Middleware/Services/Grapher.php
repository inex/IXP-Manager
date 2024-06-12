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
     * @param Request           $request
     * @param GrapherService    $grapher
     *
     * @return Graph
     */
    private function processParameters( Request $request, GrapherService $grapher ): Graph
    {
        // while the Grapher service stores the processed parameters in its own object, we update the $request
        // parameters here also just in case we need to final versions later in the request.

        $target = explode( '/', $request->path() );
        $target = array_pop( $target );

        $parameters = [];

        $parameters["period"]   = Graph::processParameterPeriod(   $request->period );
        $parameters["category"] = Graph::processParameterCategory( $request->category );
        $parameters["protocol"] = Graph::processParameterProtocol( $request->protocol );
        $parameters["type"]     = Graph::processParameterType(     $request->type );

        switch( $target ) {
            case 'ixp':
                $parameters["id"] = 1;
                $graph = $grapher->ixp()->setParamsFromArray( $parameters );
                break;

            case 'infrastructure':
                $infra = InfrastructureGraph::processParameterInfrastructure( (int)$request->input( 'id', 0 ) );
                $parameters["infrastructure"] = $infra->id;
                $graph = $grapher->infrastructure( $infra )->setParamsFromArray( $parameters );
                break;

            case 'vlan':
                $vlan = VlanGraph::processParameterVlan( (int)$request->input( 'id', 0 ) );
                $parameters["vlan"] = $vlan->id;
                $graph = $grapher->vlan( $vlan )->setParamsFromArray( $parameters );
                break;

            case 'trunk':
                $trunkname = TrunkGraph::processParameterTrunkname( (string)$request->input( 'id', '' ) );
                $parameters["trunkname"] = $trunkname;
                $graph = $grapher->trunk( $trunkname )->setParamsFromArray( $parameters );
                break;

            case 'corebundle':
                $corebundle = CoreBundleGraph::processParameterCoreBundle( (int)$request->input( 'id', 0 ) );
                $side       = CoreBundleGraph::processParameterSide( $request->input( 'side', 'a' ) );
                $parameters["corebundle"] = $corebundle->id;
                $parameters["side"]       = $side;
                $graph = $grapher->coreBundle( $corebundle, $side )->setParamsFromArray( $parameters );
                break;

            case 'location':
                $location = LocationGraph::processParameterLocation( (int)$request->input( 'id', 0 ) );
                $parameters["location"] = $location->id;
                $graph = $grapher->location( $location )->setParamsFromArray( $parameters );
                break;

            case 'switch':
                $switch = SwitchGraph::processParameterSwitch( (int)$request->input( 'id', 0 ) );
                $parameters["switch"] = $switch->id;
                $graph = $grapher->switch( $switch )->setParamsFromArray( $parameters );
                break;

            case 'physicalinterface':
                $physint = PhysIntGraph::processParameterPhysicalInterface( (int)$request->input( 'id', 0 ) );
                $parameters["physint"] = $physint->id;
                $graph = $grapher->physint( $physint )->setParamsFromArray( $parameters );
                break;

            case 'virtualinterface':
                $virtint = VirtIntGraph::processParameterVirtualInterface( (int)$request->input( 'id', 0 ) );
                $parameters["virtint"] = $virtint->id;
                $graph = $grapher->virtint( $virtint )->setParamsFromArray( $parameters );
                break;

            case 'customer':
                $customer = CustomerGraph::processParameterCustomer( (int)$request->input( 'id', 0 ) );
                $parameters["customer"] = $customer->id;
                $graph = $grapher->customer( $customer )->setParamsFromArray( $parameters );
                break;

            case 'vlaninterface':
                $vlanint = VlanIntGraph::processParameterVlanInterface( (int)$request->input( 'id', 0 ) );
                $parameters["vlanint"] = $vlanint->id;
                $graph = $grapher->vlanint( $vlanint )->setParamsFromArray( $parameters );
                break;

            case 'latency':
                $vli = LatencyGraph::processParameterVlanInterface( (int)$request->input( 'id', 0 ) );
                $parameters["vli"] = $vli;
                $graph = $grapher->latency( $vli )->setParamsFromArray( $parameters );
                break;

            case 'p2p':
                $srcvlanint = P2pGraph::processParameterSourceVlanInterface(      (int)$request->input( 'svli', 0 ) );
                $dstvlanint = P2pGraph::processParameterDestinationVlanInterface( (int)$request->input( 'dvli', 0 ) );
                $parameters["srcvlanint"] = $srcvlanint->id;
                $parameters["dstvlanint"] = $dstvlanint->id;
                $graph = $grapher->p2p( $srcvlanint, $dstvlanint )->setParamsFromArray( $parameters );
                break;
            default:
                abort(404, 'No such graph type');
        }
        /** @var Graph $graph */
        return $graph;
    }
}