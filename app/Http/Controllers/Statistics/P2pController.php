<?php

namespace IXP\Http\Controllers\Statistics;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use IXP\Http\Controllers\Controller;
use IXP\IXP;
use IXP\Models\Aggregators\VlanInterfaceAggregator;
use IXP\Models\Customer;
use IXP\Models\Vlan;
use IXP\Services\Grapher;
use IXP\Services\Grapher\Graph;

class P2pController extends Controller
{
    /**
     * Return a list of protocols versions the customer is currently using.
     * ie, they have at least one virtual lan interface where the protocol is
     * enabled.
     *
     * @param Customer $customer
     * @return int[]
     * @throws \IXP\Exceptions\GeneralException
     */
    private function determineCustomerEnabledProtocols( Customer $customer ): array
    {
        $enabled = [];
        foreach( [IXP::IPv4, IXP::IPv6] as $protocol ) {
            if( $customer->isIPvXEnabled( $protocol ) ) {
                $enabled[] = $protocol;
            }
        }
        return $enabled;
    }

    /**
     * Convert a list of IPvX protocols into a list of protocol options for
     * Grapher. If both 4 and 6 are enabled, the 'all' option is included.
     *
     * @param int[] $protocols
     * @return string[]
     */
    private function convertProtocolsToGraphOptions( array $protocols): array
    {
        $graphProtocolOptions = [];
        if (in_array(IXP::IPv4, $protocols) && in_array(IXP::IPv6, $protocols)) {
            $graphProtocolOptions[] = Graph::PROTOCOL_ALL;
        }
        if (in_array(IXP::IPv4, $protocols)) {
            $graphProtocolOptions[] = Graph::PROTOCOL_IPV4;
        }
        if (in_array(IXP::IPv6, $protocols)) {
            $graphProtocolOptions[] = Graph::PROTOCOL_IPV6;
        }
        return $graphProtocolOptions;
    }

    /**
     * Given a grapher protocol option, expand this into a list of protocol
     * numbers.
     * @param string $protocolOption
     * @return int[]
     */
    private function protocolListFromGraphOption( string $protocolOption ): array
    {
        return match ($protocolOption) {
            Graph::PROTOCOL_ALL => [IXP::IPv4, IXP::IPv6],
            Graph::PROTOCOL_IPV4 => [IXP::IPv4],
            Graph::PROTOCOL_IPV6 => [IXP::IPv6],
        };
    }

    /**
     * This controller method returns a multip2p graph detailing acivity between
     * any-and-all of src customers virtual lan interfaces, to any-and-all of dst
     * customers virtual lan interfaces.
     * Users may:
     *  - request graphs in any protocol from ipv4, ipv6, or 'all', so long as
     *    the customer has virtual lan interfaces where the requested protocol
     *    is enabled
     *  - request graphs of packet volume or bits transferred.
     *
     * @param Request $request
     * @param int $scid
     * @param int $dcid
     * @return View
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function p2pTotals( Request $request, Customer $srcCust, Customer $dstCust ): View
    {
        if( !Auth::check() ) {
            abort( 403, "You are not authorised to view this page." );
        }

        $requestProtocol = Graph::processParameterProtocol( $request->protocol );
        $requestCategory = Graph::processParameterCategory( $request->category, true );

        $possibleProtocols = $this->convertProtocolsToGraphOptions( $this->determineCustomerEnabledProtocols( $srcCust ) );
        $renderProtocols = $this->protocolListFromGraphOption( $requestProtocol );

        $graph = App::make( Grapher::class )
            ->multiP2p( $srcCust, $dstCust )
            ->setProtocol( $requestProtocol )
            ->setCategory( $requestCategory )
        ;

        $graph->authorise();

        // Generate some description test for the graphs.
        $myPorts = [];
        $theirPorts = [];
        foreach ( $srcCust->virtualInterfaces as $vi ) {
            foreach ( $vi->vlanInterfaces as $vli ) {
                foreach ( $renderProtocols as $protocol ) {
                    if ( !$vli->ipvxEnabled($protocol) ) {
                        continue;
                    }
                    $myAddress = $protocol === 4 ? $vli->ipv4address->address : $vli->ipv6address->address;
                    $myPorts[] = "{$vli->vlan->name} - {$myAddress}";

                    foreach ( $dstCust->virtualInterfaces as $dstVi ) {
                        foreach ( $dstVi->vlanInterfaces as $dstVli ) {
                            if ( $vli->vlanid !== $dstVli->vlanid || !$dstVli->ipvxEnabled( $protocol ) ) {
                                continue;
                            }

                            $theirAddress = $protocol === 4 ? $dstVli->ipv4address->address : $dstVli->ipv6address->address;
                            $theirPorts[] = "{$dstVli->vlan->name} - {$theirAddress}";
                        }
                    }
                }
            }
        }

        return view( 'statistics/p2p-totals')->with( [
            'graph'               => $graph,
            'protocol'            => $requestProtocol,
            'category'            => $requestCategory,
            'possibleProtocols'   => $possibleProtocols,
            'srcCustomer'         => $srcCust,
            'dstCustomer'         => $dstCust,
            'myPorts'             => $myPorts,
            'theirPorts'          => $theirPorts,
        ] );
    }

    public function p2pPerVlan(Request $request, Customer $srcCust, Customer $dstCust): View
    {
        if( !Auth::check() ) {
            abort( 403, "You are not authorised to view this page." );
        }

        $requestProtocol = Graph::processParameterProtocol( $request->protocol );
        $requestCategory = Graph::processParameterCategory( $request->category, true );
        $requestPeriod   = Graph::processParameterPeriod( $request->period );

        // Determine protocols supported by the customer, and from those, generate a list of
        // options for grapher protocol.
        $graphProtocolOptions = $this->convertProtocolsToGraphOptions( $this->determineCustomerEnabledProtocols( $srcCust ) );

        // Get list of VLAN ID and Name, without duplicates.
        $vlans = Vlan::findMany(VlanInterfaceAggregator::findVlansBetweenCustomers( $srcCust, $dstCust ));

        $graphData = [];

        foreach ( $vlans as $vlan) {
            $srcVlis = $srcCust->vlanInterfaces()->where('vlaninterface.vlanid', $vlan->id)->get();
            $dstVlis = $dstCust->vlanInterfaces()->where('vlaninterface.vlanid', $vlan->id)->get();

            $haveData = false;
            $srcIps = [];
            $dstIps = [];
            foreach( $srcVlis as $svli ) {
                foreach( $dstVlis as $dvli ) {
                    if ( ( $requestProtocol === Graph::PROTOCOL_IPV4 || $requestProtocol === Graph::PROTOCOL_ALL ) &&
                        ($svli->ipvxEnabled(4) && $dvli->ipvxEnabled(4))) {
                        $haveData = true;
                        $srcIps[] = $svli->ipv4address->address;
                        $dstIps[] = $dvli->ipv4address->address;
                    }

                    if ( ( $requestProtocol === Graph::PROTOCOL_IPV6 || $requestProtocol === Graph::PROTOCOL_ALL ) &&
                        ($svli->ipvxEnabled(6) && $dvli->ipvxEnabled(6))) {
                        $haveData = true;
                        $srcIps[] = $svli->ipv6address->address;
                        $dstIps[] = $dvli->ipv6address->address;
                    }
                }
            }

            if (!$haveData) {
                continue;
            }

            $graph = App::make( Grapher::class )
                ->multiP2p( $srcCust, $dstCust )
                ->setVlan( $vlan->id )
                ->setProtocol( $requestProtocol )
                ->setCategory( $requestCategory )
                ->setPeriod( $requestPeriod )
            ;
            $graph->authorise();

            $graphData[] = [
                "title" => $vlan->name,
                "subtitle" => "Traffic between ".implode(", ", array_unique($srcIps))." (yours) and ".implode(", ", array_unique($dstIps)) . " (theirs)",
                "graph" => $graph,
            ];
        }

        return view( 'statistics/p2p-per-vlan' )->with( [
            'graphData'           => $graphData,
            'protocol'            => $requestProtocol,
            'category'            => $requestCategory,
            'period'              => $requestPeriod,
            'possibleProtocols'   => $graphProtocolOptions,
            'srcCustomer'         => $srcCust,
            'dstCustomer'         => $dstCust,
        ] );
    }
}