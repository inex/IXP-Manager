<?php

namespace IXP\Http\Controllers;

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

use Redirect;

use Illuminate\Database\Eloquent\Builder;

use IXP\Models\{
    Aggregators\IpAddressAggregator,
    IPv4Address,
    IPv6Address,
    Vlan
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IPTools\{
    Network,
    Network as IPToolsNetwork
};

use IXP\Http\Requests\IpAddress\{
    DeleteByNetwork,
    Store
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * IP Address Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IpAddressController extends Controller
{
    /**
     * Return the entity depending on the protocol
     *
     * @param int       $protocol   Protocol of the IP address
     * @param boolean   $model      Do we need to return the $model ?
     *
     * @return int|string
     */
    private function processProtocol( int $protocol , bool $model = true ): int|string
    {
        if( !in_array( $protocol, [ 4,6 ] ) ) {
            abort( 404 , 'Unknown protocol');
        }

        if( $model ){
            return $protocol === 4 ? IPv4Address::class : IPv6Address::class;
        }

        return $protocol;
    }

    /**
     * Display the list of the IP Address (IPv4 or IPv6)
     *
     * @param int       $protocol   Protocol of the IP address
     * @param int|null  Vlan        ID of the vlan
     *
     * @return view
     *
     * @throws
     */
    public function list( int $protocol, int $vid = null ): View
    {
        $vlan = false;
        if( $vid ) {
            $vlan = Vlan::findOrFail( $vid );
        }

        /** @var IPv4Address|IPv6Address $ipvxModel */
        $ipvxModel = $this->processProtocol( $protocol, true );

        $ips = $ipvxModel::selectRaw( 'ip.id as id, 
                        ip.address as address,' .
            ( $protocol === 4 ? 'inet_aton(ip.address) as aton' : 'hex( inet6_aton( ip.address ) ) as aton') .
                        ",v.name AS vlan,
                        v.id as vlanid,
                        vli.id AS vliid,
                        vli.ipv{$protocol}hostname AS hostname,
                        c.name AS customer, 
                        c.id AS customerid,
                        vi.id AS viid" )
            ->from( "ipv{$protocol}address AS ip" )
            ->leftJoin( 'vlan AS v', 'v.id', 'ip.vlanid' )
            ->leftJoin( 'vlaninterface AS vli', "vli.ipv{$protocol}addressid", 'ip.id' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
            ->leftJoin( 'cust AS c', 'c.id', 'vi.custid' )
            ->when( $vlan, function( Builder $q, $vlan ) {
                return $q->where( 'v.id', $vlan->id );
            } )
            ->orderBy( 'address' )
            ->get()->toArray();

        return view( 'ip-address/list' )->with([
            'ips'                       => $vlan ? $ips : [],
            'vlans'                     => Vlan::publicOnly()->orderBy('number' )->get(),
            'protocol'                  => $protocol,
            'vlan'                      => $vlan
        ]);
    }

    /**
     * Display the form to add an IP Address (IPv4 or IPv6)
     *
     * @param int   $protocol   Protocol of the IP address
     *
     * @return view
     */
    public function create( int $protocol ): View
    {
        return view( 'ip-address/add' )->with([
            'vlans'                     => Vlan::publicOnly()->orderBy('number' )->get(),
            'protocol'                  => $this->processProtocol( $protocol, false )
        ]);
    }

    /**
     * Edit the core links associated to a core bundle
     *
     * @param   Store      $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function store( Store $r ): RedirectResponse
    {
        $vlan     = Vlan::find( $r->vlan );
        $network  = Network::parse( trim( htmlspecialchars( $r->network )  ) );
        $skip     = (bool)$r->input( 'skip',     false );
        $decimal  = (bool)$r->input( 'decimal',  false );
        $overflow = (bool)$r->input( 'overflow', false );

        if( $network->getFirstIP()->version === 'IPv6' ) {
            $sequentialAddrs = self::generateSequentialAddresses( $network, $decimal, $overflow );
            $model = IPv6Address::class;
        } else {
            $sequentialAddrs = [];
            foreach( $network as $ip ) {
                $sequentialAddrs[] = (string)$ip;
            }
            $model = IPv4Address::class;
        }

        $result = IpAddressAggregator::bulkAdd( $sequentialAddrs, $vlan, $model, $skip );

        if( !$skip && count( $result['preexisting'] ) ) {
            AlertContainer::push( "No addresses were added as the following addresses already exist in the database: "
                . implode( ', ', $result['preexisting'] ) . ". You can check <em>skip</em> below to add only the addresses "
                . "that do not already exist.", Alert::DANGER );
            return Redirect::back()->withInput();
        }

        if( count( $result['new'] ) === 0 ) {
            AlertContainer::push( "No addresses were added. " . count( $result['preexisting'] ) . " already exist in the database.",
                Alert::WARNING
            );
            return Redirect::back()->withInput();
        }

        AlertContainer::push( count( $result['new'] ) . ' new IP addresses added to <em>' . $vlan->name . '</em>. '
            . ( $skip ? 'There were ' . count( $result['preexisting'] ) . ' preexisting address(es).' : '' ),
            Alert::SUCCESS
        );

        return Redirect::to( route( 'ip-address@list', [ 'protocol' => $network->getFirstIP()->getVersion() === 'IPv6' ? '6' : '4', 'vlanid' => $vlan->id ] ) );
    }

    /**
     * Display the form to delete free IP addresses in a VLAN
     *
     * @param DeleteByNetwork    $r Instance of the current HTTP request
     * @param Vlan                          $vlan
     *
     * @return View|RedirectResponse
     *
     * @throws
     */
    public function deleteByNetwork( DeleteByNetwork $r, Vlan $vlan ): View|RedirectResponse
    {
        $ips = [];
        if( $r->network ) {
            $network  = Network::parse( trim( htmlspecialchars( $r->network )  ) );

            if( $network->getFirstIP()->version === 'IPv6' ) {
                /** @var IPv6Address $model */
                $model = IPv6Address::class;
                $sequentialAddrs = self::generateSequentialAddresses( $network, false, false );
            } else {
                /** @var IPv4Address $model */
                $model = IPv4Address::class;

                $sequentialAddrs = [];
                foreach( $network as $ip ) {
                    $sequentialAddrs[] = (string)$ip;
                }
            }

            $ips = $model::with( 'vlanInterface' )->doesntHave( 'vlanInterface' )
                ->whereIn( 'address', $sequentialAddrs )
                ->where( 'vlanid', $vlan->id )->get();

            if( $r->input( 'doDelete', false ) === "1" ) {
                $model::whereIn( 'id', $ips->pluck( 'id' ) )->delete();
                AlertContainer::push( 'IP Addresses deleted.', Alert::SUCCESS );

                return redirect( route( 'ip-address@list', [ 'protocol' => $network->getFirstIP()->version === 'IPv6' ? 6 : 4, 'vlanid' => $vlan->id ] ) );
            }
        }

        return view( 'ip-address/delete-by-network' )->with([
            'vlan'                      => $vlan,
            'network'                   => $r->network,
            'ips'                       => $ips,
        ]);
    }

    /**
     * Delete an IP address
     *
     * @param Request $r
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( Request $r, int $id ): RedirectResponse
    {
        /** @var IPv4Address|IPv6Address $ipvxModel */
        $ipvxModel  = $this->processProtocol( $r->protocol , true );
        $ip         = $ipvxModel::findOrFail( $id );

        if( $ip->vlanInterface ) {
            AlertContainer::push( 'This IP address is assigned to a VLAN interface.', Alert::DANGER );
            return redirect()->back();
        }
        $ip->delete();

        AlertContainer::push( 'The IP has been successfully deleted.', Alert::SUCCESS );
        return Redirect::to( route( "ip-address@list", [ "protocol" => $r->protocol , "vlanid" => $ip->vlanid ] ) );
    }

    /**
     * For a given IPTools library network object, generate sequential IPv6 addresses.
     *
     * There is also a `$decimal` option which only returns IPv6 addresses where the
     * last block uses only decimal numbering. This exists because typically IXs allocate
     * a customer an IPv6 address such that the last block matches the last block of the
     * IPv4 address. So, if set, the function will generate the number of addresses as
     * indicated by the CIDR block size but skip over any addresses containing
     * `a-f` characters. **NB:** the full number of addresses will be generated which means
     * this would typically overflow the subnet bound (unless $nooverflow is set).
     *
     * @param IPToolsNetwork $network
     * @param bool           $decimal
     * @param bool           $overflow
     *
     * @return array Generated addresses (string[])
     *
     * @throws
     */
    private static function generateSequentialAddresses( IPToolsNetwork $network, bool $decimal = false, bool $overflow = true ): array
    {
        $addresses = [];

        if( $decimal ) {
            $ip = $network->getFirstIP();
            $target = 2 ** ( 128 - $network->getPrefixLength() );
            $i      = 0;
            $loops  = 0;

            do {
                if( ++$loops === $target && !$overflow ) {
                    break;
                }

                if( !preg_match( '/^([0-9]+|)$/', substr( $ip, strrpos( $ip, ':' ) + 1 ) ) ) {
                    $ip = $ip->next();
                    continue;
                }

                $addresses[] = (string)$ip;
                $ip = $ip->next();
                $i++;

            } while( $i < $target );
        } else {
            foreach( $network as $ip ) {
                $addresses[] = (string)$ip;
            }
        }
        return $addresses;
    }
}