<?php

namespace IXP\Http\Controllers\Statistics;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use IXP\Http\Controllers\Controller;
use IXP\IXP;
use IXP\Models\Customer;
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
    public function p2pTotals(Request $request, int $scid, int $dcid): View
    {
        if( !Auth::check() ) {
            abort( 403, "You are not authorised to view this page." );
        }

        $srcCustomer = Customer::findOrFail( $scid );
        $dstCustomer = Customer::findOrFail( $dcid );

        $requestProtocol = Graph::processParameterProtocol( $request->protocol );
        $requestCategory = Graph::processParameterCategory( $request->category, true );

        // Abort if the requested protocol is not
        $possibleProtocols = $this->convertProtocolsToGraphOptions( $this->determineCustomerEnabledProtocols( $srcCustomer ) );
        if( !in_array( $requestProtocol, $possibleProtocols ) ) {
            abort(400, "Customer does not support $requestProtocol");
        }

        $graph = App::make( Grapher::class )
            ->multiP2p( $srcCustomer, $dstCustomer )
            ->setProtocol( $requestProtocol )
            ->setCategory( $requestCategory )
        ;

        $graph->authorise();

        return view( 'statistics/p2p-totals')->with( [
            'graph'               => $graph,
            'protocol'            => $requestProtocol,
            'category'            => $requestCategory,
            'possibleProtocols'   => $possibleProtocols,
            'srcCustomer'         => $srcCustomer,
            'dstCustomer'         => $dstCustomer,
        ] );
    }

    public function p2pPerVli(Request $request, int $scid, int $dcid): View
    {
        if( !Auth::check() ) {
            abort( 403, "You are not authorised to view this page." );
        }

        $srcCustomer = Customer::findOrFail( $scid );
        $dstCustomer = Customer::findOrFail( $dcid );

        $requestProtocol = Graph::processParameterProtocol( $request->protocol );
        $requestCategory = Graph::processParameterCategory( $request->category, true );
        $requestPeriod   = Graph::processParameterPeriod( $request->period );

        // Determine protocols supported by the customer, and from those, generate a list of
        // options for grapher protocol.
        $graphProtocolOptions = $this->convertProtocolsToGraphOptions( $this->determineCustomerEnabledProtocols( $srcCustomer ) );

        // Only render graphs for the protocols requested by the user
        $renderProtocols = $this->protocolListFromGraphOption( $requestProtocol );

        $graphs = [];

        foreach ($srcCustomer->virtualInterfaces as $vi) {
            foreach ($vi->vlanInterfaces as $vli) {
                foreach ($renderProtocols as $protocol) {
                    if (!$vli->ipvxEnabled($protocol)) {
                        continue;
                    }

                    foreach ($dstCustomer->virtualInterfaces as $dstVi) {
                        foreach ($dstVi->vlanInterfaces as $dstVli) {
                            if ($vli->vlanid !== $dstVli->vlanid || !$dstVli->ipvxEnabled($protocol)) {
                                continue;
                            }

                            $graph = App::make( Grapher::class )
                                ->p2p( $vli, $dstVli )
                                ->setProtocol( IXP::IPv4 === $protocol ? Graph::PROTOCOL_IPV4 : Graph::PROTOCOL_IPV6 )
                                ->setCategory( $requestCategory )
                                ->setPeriod( $requestPeriod )
                            ;
                            $graph->authorise();

                            $graphs[] = $graph;
                        }
                    }
                }
            }
        }

        return view( 'statistics/p2p-per-vli' )->with( [
            'graphs'              => $graphs,
            'protocol'            => $requestProtocol,
            'category'            => $requestCategory,
            'period'              => $requestPeriod,
            'possibleProtocols'   => $graphProtocolOptions,
            'srcCustomer'         => $srcCustomer,
            'dstCustomer'         => $dstCustomer,
        ] );
    }
}