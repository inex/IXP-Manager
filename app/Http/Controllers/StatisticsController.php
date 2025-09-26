<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use App, Carbon\Carbon;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Auth;

use Illuminate\Auth\Access\AuthorizationException;

use IXP\Exceptions\Services\Grapher\ParameterException;
use IXP\Http\Requests\StatisticsP2pRequest;
use IXP\IXP;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use Illuminate\View\View;

use IXP\Exceptions\Services\Grapher\GraphCannotBeProcessedException;
use IXP\Http\Requests\StatisticsRequest;

use IXP\Models\{Aggregators\TrafficDailyPhysIntAggregator,
    Aggregators\VirtualInterfaceAggregator,
    Aggregators\VlanInterfaceAggregator,
    CoreBundle,
    Customer,
    Infrastructure,
    Location,
    P2pDailyStats,
    PhysicalInterface,
    Switcher,
    TrafficDaily,
    TrafficDailyPhysInt,
    User,
    VirtualInterface,
    Vlan,
    VlanInterface};

use IXP\Services\Grapher\Graph;
use IXP\Services\Grapher;

use IXP\Services\Grapher\Graph\{
    Customer as CustomerGraph
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Statistics Controller
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StatisticsController extends Controller
{
    /**
     * Process and update request parameters for standard graph attributes: period, category, protocol, type.
     *
     * These are safe for use from the request.
     *
     * @param StatisticsRequest $request
     *
     * @return void
     */
    private function processGraphParams( StatisticsRequest $request ): void
    {
        $request->period   = Graph::processParameterPeriod( $request->period );
        $request->category = Graph::processParameterCategory( $request->category );
        $request->protocol = Graph::processParameterProtocol( $request->protocol );
        $request->type     = Graph::processParameterType( $request->type );
    }

    /**
     * Show overall IXP graphs
     *
     * @param string $category Category of graph to show (e.g. bits / pkts)
     *
     * @return View
     *
     * @throws
     */
    public function ixp( string $category = Graph::CATEGORY_BITS ) : View
    {
        $category = Graph::processParameterCategory( $category, true );

        $graph = App::make( Grapher::class )
            ->ixp()->setType( Graph::TYPE_PNG )
            ->setProtocol( Graph::PROTOCOL_ALL )->setCategory( $category );

        $graph->authorise();

        return view( 'statistics/ixp' )->with( [
            'graph'    => $graph,
            'category' => $category,
        ] );
    }

    /**
     * Show IXP infrastructure graphs
     *
     * @param Infrastructure|null   $infra      Infrastructure to show the graph of
     * @param string                $category   Category of graph to show (e.g. bits / pkts)
     *
     * @return View
     *
     * @throws
     */
    public function infrastructure( ?Infrastructure $infra = null, string $category = Graph::CATEGORY_BITS ) : View
    {
        $infras     = Infrastructure::select( [ 'name', 'id' ] )
            ->orderBy( 'name' )->get();

        $inf        = $infra ?? $infras->first();
        $category   = Graph::processParameterCategory( $category, true );

        $graph      = App::make( Grapher::class )
            ->infrastructure( $inf )->setType( Graph::TYPE_PNG )
            ->setProtocol( Graph::PROTOCOL_ALL )->setCategory( $category );

        $graph->authorise();

        return view( 'statistics/infrastructure' )->with( [
            'infras'   => $infras,
            'infra'    => $inf,
            'graph'    => $graph,
            'category' => $category,
        ] );
    }

    /**
     * Show Vlan (sflow) graphs
     *
     * @param Vlan|null $vlan VLAN to show the graph of
     * @param string $protocol IPv4/6
     * @param string $category
     *
     * @return View
     *
     * @throws ParameterException
     * @throws BindingResolutionException
     */
    public function vlan( ?Vlan $vlan = null, string $protocol = Graph::PROTOCOL_IPV4, string $category = Graph::CATEGORY_BITS ) : View
    {
        $vlans   = Vlan::publicOnly()
            ->where( 'peering_matrix', true )
            ->orWhere( 'peering_manager', true )
            ->orderBy( 'name' )->get();

        if( !$vlans->count() ) {
            abort( 404, 'No VLANs available for graphing' );
        }

        $protocol   = Graph::processParameterRealProtocol( $protocol );
        $category   = Graph::processParameterCategory( $category, true );
        $vl         = $vlan ?? $vlans->first();
        $graph      = App::make( Grapher::class )
            ->vlan( $vl )->setType( Graph::TYPE_PNG )
            ->setProtocol( $protocol )->setCategory( $category );

        try {
            $graph->backend();
        } catch( GraphCannotBeProcessedException $e ) {
            abort( 404, 'No backend available to process VLAN graphs' );
        }

        $graph->authorise();

        return view( 'statistics/vlan' )->with( [
            'vlans'    => $vlans,
            'vlan'     => $vl,
            'graph'    => $graph,
            'protocol' => $protocol,
            'category' => $category,
        ] );
    }

    /**
     * Show IXP switch graphs
     *
     * @param Switcher|null $switch Switch to show the graph of
     * @param string $category Category of graph to show (e.g. bits / pkts)
     *
     * @return View
     * @throws BindingResolutionException
     * @throws ParameterException
     */
    public function switch( ?Switcher $switch = null, string $category = Graph::CATEGORY_BITS ) : View
    {
        $switches = Switcher::where( 'active', true )
            ->orderBy( 'name' )->get()
            ->keyBy( 'id' );

        $category   = Graph::processParameterCategory( $category, true );
        $s          = $switch ?? $switches->first();
        $graph      = App::make( Grapher::class )
            ->switch( $s )->setType( Graph::TYPE_PNG )
            ->setProtocol( Graph::PROTOCOL_ALL )->setCategory( $category );

        $graph->authorise();

        return view( 'statistics/switch' )->with([
            'switches'  => $switches,
            'switch'    => $s,
            'graph'     => $graph,
            'category'  => $category,
        ]);
    }

    /**
     * Show IXP location graphs
     *
     * @param Location|null $location Location§ to show the graph of
     * @param string $category Category of graph to show (e.g. bits / pkts)
     *
     * @return View
     * @throws BindingResolutionException
     * @throws ParameterException
     */
    public function location( ?Location $location = null, string $category = Graph::CATEGORY_BITS ) : View
    {
        $locations = Location::orderBy( 'name' )->get()
            ->keyBy( 'id' );

        $category   = Graph::processParameterCategory( $category, true );
        $l          = $location ?? $locations->first();
        $graph      = App::make( Grapher::class )
            ->location( $l )->setType( Graph::TYPE_PNG )
            ->setProtocol( Graph::PROTOCOL_ALL )->setCategory( $category );

        $graph->authorise();

        return view( 'statistics/location' )->with([
            'locations' => $locations,
            'location'  => $l,
            'graph'     => $graph,
            'category'  => $category,
        ]);
    }

    /**
     * Show IXP trunk graphs
     *
     * @param  string|null  $trunk  ID of the trunk to show the graph of
     * @param  string  $category  Category of graph to show (e.g. bits / pkts)
     *
     * @return RedirectResponse|View
     *
     * @throws ParameterException
     */
    public function trunk( ?string $trunk = null, string $category = Graph::CATEGORY_BITS ): RedirectResponse|View
    {
        if( !is_array( config('grapher.backends.mrtg.trunks') ) || !count( config('grapher.backends.mrtg.trunks') ) ) {
            AlertContainer::push(
                "Trunk graphs have not been configured. Please see <a target='_blank' href=\"https://docs.ixpmanager.org/latest/grapher/introduction/\">this documentation</a> for instructions.",
                Alert::DANGER
            );
            return redirect('');
        }

        // get the available graphs
        $images = [];
        $graphs = [];
        foreach( config('grapher.backends.mrtg.trunks') as $g ) {
            $images[]               = $g[ 'name' ];
            $graphs[ $g['name'] ]   = $g[ 'title' ];
        }

        if( !in_array( $trunk, $images, true ) ) {
            $trunk = $images[ 0 ];
        }

        $graph = App::make( Grapher::class )
            ->trunk( $trunk )->setType( Graph::TYPE_PNG )
            ->setProtocol( Graph::PROTOCOL_ALL )->setCategory( Graph::CATEGORY_BITS );

        $graph->authorise();

        return view( 'statistics/trunk' )->with( [
            'graphs'    => $graphs,
            'trunkid'   => $trunk,
            'graph'     => $graph,
            'category'  => $category,
        ] );
    }

    /**
     * Display all member graphs
     *
     * @param  StatisticsRequest  $r
     *
     * @return View
     *
     * @throws ParameterException
     */
    public function members( StatisticsRequest $r ): View
    {
        if( !CustomerGraph::authorisedForAllCustomers() ) {
            abort( 403, "You are not authorised to view this member's graphs." );
        }

        $grapher = App::make( Grapher::class );
        $this->processGraphParams( $r );

        // do we have an infrastructure or vlan?
        $vlan = $infra = false;

        if( $r->infra ) {
            if( $infra = Infrastructure::find( $r->infra ) ) {
                $targets = VirtualInterfaceAggregator::getForInfrastructure( $infra );
            } else {
                $targets = Customer::currentActive( true, false )->get();
            }
            $r->protocol = Graph::PROTOCOL_ALL;
        } else if( $r->vlan && ( $vlan = Vlan::find( $r->vlan ) ) ) {
            if( !in_array( $r->protocol, Graph::PROTOCOLS_REAL, true ) ) {
                $r->protocol = Graph::PROTOCOL_IPV4;
            }
            $targets = VlanInterfaceAggregator::forVlan( $vlan, IXP::protocolTextToInt( $r->protocol ) );
        } else {
            $targets = [];
        }

        $graphs = [];
        foreach( $targets as $t ) {
            if( !$t->isGraphable() ) {
                continue;
            }

            if( $infra ) {
                $g = $grapher->virtint( $t );
            } else if( $vlan ) {
                $g = $grapher->vlanint( $t );
            } else {
                $g = $grapher->customer( $t );
            }

            /** @var Graph $g */
            $g->setType(     Graph::TYPE_PNG )
                ->setProtocol( $r->protocol   )
                ->setCategory( $r->category   )
                ->setPeriod(   $r->period     );

            $g->authorise();
            $graphs[] = $g;
        }

        return view( 'statistics/members' )->with([
            'graph'         => $graphs[ 0 ] ?? false,  // sample graph as all types/protocols/categories/periods will be the same
            'graphs'        => $graphs,
            'r'             => $r,
            'infras'        => Infrastructure::orderBy( 'name' )->get(),
            'infra'         => $infra ?? false,
            'vlans'         => Vlan::publicOnly()->orderBy('number')->get(),
            'vlan'          => $vlan ?? false,
        ]);
    }

    /**
     * Display all graphs for a member
     *
     * @param StatisticsRequest     $r
     * @param Customer|null $cust the member
     *
     * @return RedirectResponse|View
     *
     */
    public function member( StatisticsRequest $r, ?Customer $cust = null ): RedirectResponse|View
    {
        if( !$cust && Auth::check() ) {
            $cust = Auth::getUser()->customer;
        }

        if( !$cust ) {
            abort( 403, "You are not authorised to view this member's graphs." );
        }

        $grapher = App::make( Grapher::class );

        // if the customer is authorised, then so too are all of their virtual and physical interfaces:
        try {
            $grapher->customer( $cust )->authorise();
        } catch( AuthorizationException $e ) {
            abort( 403, "You are not authorised to view this member's graphs." );
        }

        if( !$cust->hasInterfacesConnectedOrInQuarantine() ) {
            AlertContainer::push("This customer has no graphable interfaces (i.e. no physical interfaces in quarantine or connected)", Alert::WARNING);
            return redirect()->back();
        }

        return view( 'statistics/member' )->with([
            "c"                     => $cust->load(  [
                'virtualinterfaces.physicalInterfaces.switchPort.switcher',
                'virtualinterfaces.physicalInterfaces.switchPort.switcher.cabinet.location',
            ] ),
            "grapher"               => $grapher,
            "category"              => Graph::processParameterCategory( $r->category ),
            "period"                => Graph::processParameterPeriod( $r->period ),
        ]);
    }

    /**
     * Display Aggregate/LAG/Port for all periods (day/week/month/year)
     *
     * @param  StatisticsRequest  $r
     * @param  string  $type  type
     * @param  integer  $typeid  ID of type
     *
     * @return  View
     *
     * @throws ParameterException
     */
    public function memberDrilldown( StatisticsRequest $r, string $type, int $typeid ): View
    {
        switch( strtolower( $type ) ) {
            case 'agg':
                $c      = Customer::findOrFail( $typeid );
                $graph  = App::make( Grapher::class )->customer( $c );
                break;
            case 'vi':
                $vi     = VirtualInterface::findOrFail( $typeid );
                $c      = $vi->customer;
                $graph  = App::make( Grapher::class )->virtint( $vi );
                break;
            case 'pi':
                $pi     = PhysicalInterface::findOrFail( $typeid );
                $c      = $pi->virtualInterface->customer;
                $graph  = App::make( Grapher::class )->physint( $pi );
                break;
            default:
                abort( 404, 'Unknown graph type' );
        }

        /** @var Graph $graph */
        $graph->setCategory( Graph::processParameterCategory( $r->category ) );
        $graph->authorise();

        return view( 'statistics/member-drilldown' )->with( [
            'c'     => $c,
            'graph' => $graph,
        ]);
    }

    /**
     * Show latency graphs
     *
     * @param  VlanInterface  $vli
     * @param  string  $protocol
     *
     * @return View|RedirectResponse
     *
     * @throws ParameterException|AuthorizationException
     */
    public function latency( VlanInterface $vli, string $protocol ): View|RedirectResponse
    {
        $protocol = Graph::processParameterProtocol( $protocol );

        $graph = App::make( Grapher::class )->latency( $vli )->setProtocol( $protocol );
        $graph->authorise();

        $fnEnabled = strtolower( $protocol ) . 'enabled';
        $fnCanping = strtolower( $protocol ) . 'canping';
        $fnAddress = strtolower( $protocol ) . 'address';

        if( !$vli->$fnEnabled || !$vli->$fnCanping ) {
            AlertContainer::push(
                "Protocol or ping not enabled on the requested interface",
                Alert::WARNING
            );
            return redirect( route( "statistics@member", [ "id" => $vli->virtualInterface->customer->id ] ) );
        }

        return view( 'statistics/latency' )->with([
            'c'         => $vli->virtualInterface->customer,
            'vli'       => $vli,
            'ip'        => $vli->$fnAddress->address,
            'protocol'  => $protocol,
            'graph'     => $graph,
        ]);
    }




    /**
     * sFlow Peer to Peer statistics
     *
     * @param Request  $request
     * @param Customer|null  $customer
     *
     * @return (Customer|VlanInterface|\Illuminate\Support\Collection|mixed|null|string)[]
     *
     * @throws ParameterException
     *
     * @psalm-return array{c: Customer|null, category: null|string, period: null|string, protocol: null|string, srcVlis: mixed, srcVli: VlanInterface|mixed, dstVlis: \Illuminate\Support\Collection, dstVli: VlanInterface|null}
     */
    public function p2pPrepare( Request $request, ?Customer $customer, ?VlanInterface $dstVli = null ): array
    {
        if( !$customer ) {
            $customer = Auth::user()->customer;
        }

        $requestCategory = Graph::processParameterCategory( $request->category, true );
        $requestPeriod   = Graph::processParameterPeriod( $request->period );
        $requestProtocol = Graph::processParameterRealProtocol( $request->protocol );

        // on the p2ps page, we need to provide a dropdown of all possible source vlis for this customer
        $srcVlis = VlanInterface::select( [ 'vli.*' ] )
            ->from( 'vlaninterface AS vli' )
            ->join( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->join( 'cust AS c', 'c.id', 'vi.custid' )
            ->join( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->where( 'c.id', $customer->id )
            ->where( 'vli.' . $requestProtocol . 'enabled', '1' )
            ->with( [ 'vlan' ] )
            ->orderBy( 'v.number' )->get()->keyBy( 'id' );

        // Find the possible VLAN interfaces that this customer has for the given IXP
        if( !count( $srcVlis ) ) {
            throw new ParameterException("There were no interfaces available for the given criteria.");
        }

        // svli will hold a source vli selected by the user on the p2ps page or else a default
        /** @var VlanInterface $srcVli */
        $srcVli = $srcVlis[ $srcVlis->first()->id ];
        if( ( $svlid = $request->svli ) && isset( $srcVlis[ $svlid ] ) ) {
            $srcVli = $srcVlis[ $svlid ];
        }

        // is the requested protocol supported? (we have no protocols on private vlans)
        if( !$srcVli->vlan->private && !$srcVli->ipvxEnabled( $requestProtocol ) ) {
            throw new ParameterException(Graph::resolveProtocol( $requestProtocol ) . " is not supported on the requested VLAN interface." );
        }

        // Now find the possible other VLAN interfaces that this customer could exchange traffic with
        // (as well as removing the source vli)
        $dstVlis = VlanInterfaceAggregator::forVlan( $srcVli->vlan );
        unset( $dstVlis[ $srcVli->id ] );

        if( !$dstVlis->count() ) {
            throw new ParameterException("There were no destination interfaces available for traffic exchange for the given criteria." );
        }

        if( $dstVli ) {
            if( $srcVli->vlan->id !== $dstVli->vlan->id ) {
                // otherwise, is there an appropriate dstVLi on srcVli->vlan?
                foreach( $dstVli->virtualInterface->customer->virtualInterfaces as $vi ) {
                    foreach( $vi->vlanInterfaces as $vli ) {
                        if( $vli->vlan->id === $srcVli->vlan->id ) {
                            $dstVli = $vli;
                        }
                    }
                }
            }

            if( $srcVli->vlan->id !== $dstVli->vlan->id ) {
                // okay, bounce back to p2p overview page
                throw new ParameterException( "No valid destination customer in this peering LAN" );
            }
        }


        return [
            'c'                => $customer,
            'category'         => $requestCategory,
            'period'           => $requestPeriod,
            'protocol'         => $requestProtocol,
            'srcVlis'          => $srcVlis,
            'srcVli'           => $srcVli,
            'dstVlis'          => $dstVlis,
            'dstVli'           => $dstVli,
        ];
    }



    /**
     * sFlow Peer to Peer statistics
     *
     * @param  Request  $request
     * @param  Customer|null  $customer
     *
     * @return RedirectResponse|View
     *
     * @throws ParameterException
     */
    public function p2ps( Request $request, ?Customer $customer = null ): RedirectResponse|View
    {
        try {
            $data = $this->p2pPrepare( $request, $customer );
        } catch( ParameterException $e ) {
            AlertContainer::push( $e->getMessage(), Alert::WARNING );
            return redirect()->back();
        }

        // for larger IXPs, it's quite intensive to display all the graphs - decide if we need to do this or not
        $showGraphs       = $request->input( 'show_graphs', config('grapher.backends.sflow.show_graphs_on_index_page') ? 'show' : 'hide' ) === 'show';
        $orderBy          = $request->input( 'order_by', 'traffic' );

        // ordering is by customer name by default due to VlanInterfaceAggregator::forVlan()
        if( $orderBy !== 'name' ) {
            $trafficByPeerId = P2pDailyStats::latestTotalTraffic( $data['c']);

            /** @var VlanInterface $dstVli */
            foreach( $data['dstVlis'] as $idx => $dstVli) {
                $data['dstVlis'][$idx]->total_traffic = $trafficByPeerId[ $dstVli->virtualInterface->custid ] ?? 0;
            }

            $data['dstVlis'] = $data['dstVlis']->sortByDesc( 'total_traffic', SORT_NUMERIC );
        }


        // authenticate on one of the graphs
        $graph = App::make( Grapher::class )
            ->p2p( $data['srcVli'], $data['dstVlis'][ $data['dstVlis']->first()->id ] )
            ->setProtocol( $data['protocol'] )
            ->setCategory( $data['category'] )
            ->setPeriod( $data['period'] );
        $graph->authorise();

        return view( 'statistics/p2ps', array_merge( $data, [
            'graph'            => $graph,
            'orderBy'          => $orderBy,
            'showGraphs'       => $showGraphs,
        ]) );
    }

    /**
     * sFlow Peer to Peer statistics
     *
     * @param  Request  $request
     * @param  VlanInterface $srcVli
     * @param VlanInterface $dstVli
     * @return RedirectResponse|View
     * @throws ParameterException
     */
    public function p2pPost( Request $request ): RedirectResponse|View
    {
        // svli and dvli via top of page post form.
        // svli may have changed and so dvli may not be appropriate anymore
        $srcVli = VlanInterface::findOrFail($request->svli);
        $dstVli = VlanInterface::findOrFail($request->dvli);

        return $this->p2p( $request, $srcVli, $dstVli );
    }

    /**
     * sFlow Peer to Peer statistics
     *
     * @param  Request  $request
     * @param  VlanInterface $srcVli
     * @param VlanInterface $dstVli
     * @return RedirectResponse|View
     * @throws ParameterException
     */
    public function p2p( Request $request, VlanInterface $srcVli, VlanInterface $dstVli ): RedirectResponse|View
    {
        try {
            $data = $this->p2pPrepare( $request, $srcVli->virtualInterface->customer, $dstVli );
            $srcVli = $data['srcVli'];
            $dstVli = $data['dstVli'];
        } catch( ParameterException $e ) {
            AlertContainer::push( $e->getMessage(), Alert::WARNING );
            return redirect()->back();
        }

        if( !$srcVli->ipvxEnabled( $data['protocol'] ) || !$dstVli->ipvxEnabled( $data['protocol'] ) ) {
            AlertContainer::push( Graph::resolveProtocol( $data['protocol'] ) . " is not supported on the requested VLAN interfaces.", Alert::WARNING );
            return redirect( route('statistics@p2ps-get', ['customer' => $srcVli->virtualInterface->custid ] ) );
        }

        $possibleProtocols = [];
        foreach( Graph::PROTOCOL_REAL_DESCS as $p => $desc ) {
            if( $srcVli->ipvxEnabled($p) && $dstVli->ipvxEnabled($p) ) {
                $possibleProtocols[$p] = $desc;
            }
        }

        $graph = App::make( Grapher::class )
            ->p2p( $srcVli, $dstVli )
            ->setProtocol( $data['protocol'] )
            ->setCategory( $data['category'] )
            ->setPeriod( $data['period'] );

        $graph->authorise();

        return view( 'statistics/p2p-single', array_merge( $data, [
            'graph'             => $graph,
            'dstVli'            => $dstVli,
            'possibleProtocols' => $possibleProtocols,
        ]) );
    }



    /**
     * Show p2p stats for a given customer and day.
     *
     * @param Request $r
     *
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|RedirectResponse
     *
     * @throws
     */
    public function p2pTable( Request $r ): View|RedirectResponse
    {
        if( !Auth::check() ) {
            abort( 403, "You are not authorised to view this page." );
        }

        if( !Auth::user()->isSuperUser() ) {
            $r->merge( [ 'custid' => Auth::user()->custid ] );
        }

        $days = P2pDailyStats::select('day')->distinct('day')->orderBy('day','desc')->get()->pluck('day')->toArray();

        if( empty( $days ) ) {
            AlertContainer::push( "The P2P daily stats database table is empty.", Alert::WARNING );
            return redirect( route('statistics@member', ['cust' => $r->custid ] ) );
        }

        if( !$r->day || !in_array( $r->day, $days ) ) {
            $r->merge( [ 'day' => $days[0] ] );
        }

        $customers = Customer::currentActive(true,true,true)
            ->get()->keyBy('id');

        $stats = [];
        if( $r->custid ) {
            $stats = P2pDailyStats::with('peer')
                ->where('day', $r->day)->where('cust_id', $r->custid )->get();
        }

        return view( 'statistics/p2p-table' )->with( [
            'day'          => $r->day,
            'days'         => $days,
            'stats'        => $stats,
            'customers'    => $customers,
            'c'            => $r->custid ? $customers[$r->custid] : false,
        ] );
    }






    /**
     * Show daily traffic for customers in a table.
     *
     * @param Request $r
     *
     * @return View
     *
     * @throws
     */
    public function leagueTable( Request $r ): View
    {
        $metrics = [
            'Total'   => 'data',
            'Max'     => 'max',
            'Average' => 'average',
        ];

        $metric = $r->input( 'metric', $metrics['Total'] );
        if( !in_array( $metric, $metrics, true ) ) {
            $metric = $metrics[ 'Total' ];
        }

        $tday = $r->day;
        if( !preg_match( '/^\d\d\d\d\-\d\d\-\d\d$/', $tday ) ) {
            $tday = Carbon::now()->format( 'Y-m-d');
        }

        $day        = Carbon::createFromFormat( 'Y-m-d', $tday );
        $category   = Graph::processParameterCategory( $r->category );

        return view( 'statistics/league-table' )->with( [
            'metric'       => $metric,
            'metrics'      => $metrics,
            'day'          => $day,
            'category'     => $category,
            'trafficDaily' => TrafficDaily::loadTraffic( $day, $category ),
        ] );
    }








    /**
     * Display graphs for a core bundle
     *
     * @param  StatisticsRequest    $r
     * @param  CoreBundle           $cb  the core bundle
     *
     * @return RedirectResponse|View
     *
     * @throws ParameterException
     */
    public function coreBundle( StatisticsRequest $r, CoreBundle $cb ): RedirectResponse|View
    {
        /** @var User $us */
        $us = Auth::getUser();

        $category = Graph::processParameterCategory( $r->input( 'category' ) );
        $graph    = App::make( Grapher::class )
            ->coreBundle( $cb )->setCategory( $category )
            ->setSide( $r->input( 'side', 'a' ) );

        // if the customer is authorised, then so too are all of their virtual and physical interfaces:
        try {
            $graph->authorise();
        } catch( AuthorizationException $e ) {
            abort( 403, "You are not authorised to view this graph." );
        }

        return view( 'statistics/core-bundle' )->with([
            "cbs"                   => CoreBundle::active()->get(),
            "cb"                    => $cb,
            "graph"                 => $graph,
            "category"              => $category,
            "categories"            => Auth::check() && $us && $us->isSuperUser() ? Graph::CATEGORY_DESCS : Graph::CATEGORIES_BITS_PKTS_DESCS,
        ]);
    }

    /**
     * Show utilisation of member ports
     *
     * @param StatisticsRequest $r
     *
     * @return View
     */
    public function utilisation( StatisticsRequest $r ): View
    {
        $metrics = [
            'Max'     => 'max',
            'Total'   => 'data',
            'Average' => 'average',
        ];

        $metric = $r->input( 'metric', $metrics['Max'] );

        if( !in_array( $metric, $metrics, true ) ) {
            $metric = $metrics[ 'Max' ];
        }

        $days =  TrafficDailyPhysInt::select( [ 'day' ] )
            ->distinct( 'day' )
            ->orderBy( 'day', 'desc')->get()->pluck( 'day' )->toArray();

        if( count( $days ) ) {
            $day = $r->day;
            if( !in_array( $day, $days, true ) ) {
                $day = $days[ 0 ];
            }
        } else {
            $day = null;
        }

        $vid = false;
        if( $r->vlan && ( $vlan = Vlan::find( $r->vlan ) ) ) {
            $vid = $vlan->id;
        }

        $category = Graph::processParameterCategory( $r->category );
        $period   = Graph::processParameterPeriod( $r->period, Graph::PERIOD_MONTH );

        return view( 'statistics/utilisation' )->with( [
            'metric'       => $metric,
            'metrics'      => $metrics,
            'day'          => $day,
            'days'         => $days,
            'category'     => $category,
            'period'       => $period,
            'tdpis'        => $day ? TrafficDailyPhysIntAggregator::loadTraffic( $day, $category, $period, $vid ) : [],
            'vlans'        => Vlan::publicOnly()->orderBy('number')->get(),
            'vlan'         => $vid,
        ] );
    }
}