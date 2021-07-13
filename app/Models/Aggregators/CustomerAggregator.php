<?php

namespace IXP\Models\Aggregators;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use DB, Exception;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\{
    Builder,
};

use IXP\Models\{
    CoreBundle,
    Customer,
    PeeringManager,
    Vlan
};
use Illuminate\Support\Collection;

/**
 * IXP\Models\Aggregators\CustomerAggregator
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $type
 * @property string|null $shortname
 * @property int|null $autsys
 * @property int|null $maxprefixes
 * @property string|null $peeringemail
 * @property string|null $nocphone
 * @property string|null $noc24hphone
 * @property string|null $nocfax
 * @property string|null $nocemail
 * @property string|null $nochours
 * @property string|null $nocwww
 * @property int|null $irrdb
 * @property string|null $peeringmacro
 * @property string|null $peeringpolicy
 * @property string|null $corpwww
 * @property \Illuminate\Support\Carbon|null $datejoin
 * @property \Illuminate\Support\Carbon|null $dateleave
 * @property int|null $status
 * @property int|null $activepeeringmatrix
 * @property int|null $lastupdatedby
 * @property string|null $creator
 * @property int|null $company_registered_detail_id
 * @property int|null $company_billing_details_id
 * @property string|null $peeringmacrov6
 * @property string|null $abbreviatedName
 * @property string|null $MD5Support
 * @property int|null $reseller
 * @property int $isReseller
 * @property int $in_manrs
 * @property int $in_peeringdb
 * @property int $peeringdb_oauth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\AtlasMeasurement[] $AtlasMeasurementsDest
 * @property-read int|null $atlas_measurements_dest_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\AtlasMeasurement[] $AtlasMeasurementsSource
 * @property-read int|null $atlas_measurements_source_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\AtlasProbe[] $AtlasProbes
 * @property-read int|null $atlas_probes_count
 * @property-read \IXP\Models\CompanyBillingDetail|null $companyBillingDetail
 * @property-read \IXP\Models\CompanyRegisteredDetail|null $companyRegisteredDetail
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ConsoleServerConnection[] $consoleServerConnections
 * @property-read int|null $console_server_connections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\Contact[] $contacts
 * @property-read int|null $contacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\CustomerEquipment[] $customerEquipments
 * @property-read int|null $customer_equipments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\CustomerNote[] $customerNotes
 * @property-read int|null $customer_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\CustomerToUser[] $customerToUser
 * @property-read int|null $customer_to_user_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\DocstoreCustomerDirectory[] $docstoreCustomerDirectories
 * @property-read int|null $docstore_customer_directories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\DocstoreCustomerFile[] $docstoreCustomerFiles
 * @property-read int|null $docstore_customer_files_count
 * @property-read \IXP\Models\IrrdbConfig|null $irrdbConfig
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\IrrdbPrefix[] $irrdbPrefixes
 * @property-read int|null $irrdb_prefixes_count
 * @property-read \IXP\Models\Logo|null $logo
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPortHistory[] $patchPanelPortHistories
 * @property-read int|null $patch_panel_port_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPort[] $patchPanelPorts
 * @property-read int|null $patch_panel_ports_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\RouteServerFilter[] $peerRouteServerFilters
 * @property-read int|null $peer_route_server_filters_count
 * @property-read \Illuminate\Database\Eloquent\Collection|PeeringManager[] $peers
 * @property-read int|null $peers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|PeeringManager[] $peersWith
 * @property-read int|null $peers_with_count
 * @property-read Customer|null $resellerObject
 * @property-read \Illuminate\Database\Eloquent\Collection|Customer[] $resoldCustomers
 * @property-read int|null $resold_customers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\RouteServerFilter[] $routeServerFilters
 * @property-read int|null $route_server_filters_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\RsPrefix[] $rsPrefixes
 * @property-read int|null $rs_prefixes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\CustomerTag[] $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\TrafficDaily[] $trafficDailies
 * @property-read int|null $traffic_dailies_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\VirtualInterface[] $virtualInterfaces
 * @property-read int|null $virtual_interfaces_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\VlanInterface[] $vlanInterfaces
 * @property-read int|null $vlan_interfaces_count
 * @method static Builder|Customer active()
 * @method static Builder|Customer addressesForVlan(int $vlanid, int $cust, int $protocol)
 * @method static Builder|Customer associate()
 * @method static Builder|Customer current()
 * @method static Builder|Customer currentActive(bool $trafficing = false, bool $externalOnly = false, bool $connected = true)
 * @method static Builder|Customer internal()
 * @method static Builder|CustomerAggregator newModelQuery()
 * @method static Builder|CustomerAggregator newQuery()
 * @method static Builder|Customer notDeleted()
 * @method static Builder|CustomerAggregator query()
 * @method static Builder|Customer resellerOnly()
 * @method static Builder|Customer trafficking()
 * @method static Builder|CustomerAggregator whereAbbreviatedName($value)
 * @method static Builder|CustomerAggregator whereActivepeeringmatrix($value)
 * @method static Builder|CustomerAggregator whereAutsys($value)
 * @method static Builder|CustomerAggregator whereCompanyBillingDetailsId($value)
 * @method static Builder|CustomerAggregator whereCompanyRegisteredDetailId($value)
 * @method static Builder|CustomerAggregator whereCorpwww($value)
 * @method static Builder|CustomerAggregator whereCreatedAt($value)
 * @method static Builder|CustomerAggregator whereCreator($value)
 * @method static Builder|CustomerAggregator whereDatejoin($value)
 * @method static Builder|CustomerAggregator whereDateleave($value)
 * @method static Builder|CustomerAggregator whereId($value)
 * @method static Builder|CustomerAggregator whereInManrs($value)
 * @method static Builder|CustomerAggregator whereInPeeringdb($value)
 * @method static Builder|CustomerAggregator whereIrrdb($value)
 * @method static Builder|CustomerAggregator whereIsReseller($value)
 * @method static Builder|CustomerAggregator whereLastupdatedby($value)
 * @method static Builder|CustomerAggregator whereMD5Support($value)
 * @method static Builder|CustomerAggregator whereMaxprefixes($value)
 * @method static Builder|CustomerAggregator whereName($value)
 * @method static Builder|CustomerAggregator whereNoc24hphone($value)
 * @method static Builder|CustomerAggregator whereNocemail($value)
 * @method static Builder|CustomerAggregator whereNocfax($value)
 * @method static Builder|CustomerAggregator whereNochours($value)
 * @method static Builder|CustomerAggregator whereNocphone($value)
 * @method static Builder|CustomerAggregator whereNocwww($value)
 * @method static Builder|CustomerAggregator wherePeeringdbOauth($value)
 * @method static Builder|CustomerAggregator wherePeeringemail($value)
 * @method static Builder|CustomerAggregator wherePeeringmacro($value)
 * @method static Builder|CustomerAggregator wherePeeringmacrov6($value)
 * @method static Builder|CustomerAggregator wherePeeringpolicy($value)
 * @method static Builder|CustomerAggregator whereReseller($value)
 * @method static Builder|CustomerAggregator whereShortname($value)
 * @method static Builder|CustomerAggregator whereStatus($value)
 * @method static Builder|CustomerAggregator whereType($value)
 * @method static Builder|CustomerAggregator whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomerAggregator extends Customer
{

    /**
     * Get All customer by vlan and protocol
     *
     * @param int|null $vlanid
     * @param int|null $protocol
     *
     * @return array
     */
    public static function getByVlanAndProtocol( int $vlanid = null, int $protocol = null ): array
    {
        return self::select( [ 'c.id', 'c.name' ] )
            ->from( 'cust AS c' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.custid', 'c.id' )
            ->leftJoin( 'vlaninterface AS vli', 'vli.virtualinterfaceid', 'vi.id' )
            ->leftJoin( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->leftJoin( 'routers AS r', 'r.vlan_id', 'v.id' )
            ->where( 'vli.rsclient', true )
            ->when( $protocol, function( Builder $q, $protocol ) {
                return $q->where( 'r.protocol', $protocol )
                    ->where( "vli.ipv{$protocol}enabled", true );
            }, function( $query ) {
                return $query->where( function( $q ) {
                    $q->where( 'r.protocol', 4 )
                        ->orWhere( 'r.protocol', 6 );
                } )->where( function( $q ) {
                    $q->where( 'vli.ipv4enabled', true )
                        ->orWhere( 'vli.ipv6enabled', true );
                } );
            } )->when( $vlanid, function( Builder $q, $vlanid ) {
                return $q->where( 'v.id', $vlanid );
            } )->distinct( 'c.id' )->orderBy( 'c.name', 'asc' )->get()->toArray();
    }

    /**
     * Build an array of data for the peering matrice
     *
     * Sample return:
     *
     *     [
     *         "me" => [    "id" => 69
     *                       "name" => "3 Ireland's"
     *                       "shortname" => "three"
     *                       "autsys" => 34218
     *                       "maxprefixes" => 100
     *                       "peeringemail" => "io.ip@three.co.uk"
     *                       "peeringpolicy" => "open"
     *                       "vlaninterfaces" => [
     *                               10 => [
     *                                   0 => [
     *                                           "ipv4enabled" => true
     *                                           "ipv6enabled" => false
     *                                           "rsclient" => true
     *                                       ]
     *                               ]
     *
     *                          ]
     *
     *                  ]
     *
     *         "potential" => [
     *               12041 => false
     *               56767 => false
     *               196737 => false
     *          ]
     *
     *          "potential_bilat" => [
     *               12041 => true
     *               56767 => true
     *               196737 => false
     *          ]
     *
     *          "peered" => [
     *               12041 => true
     *               56767 => true
     *               196737 => false
     *          ]
     *
     *          "peered" => [
     *               12041 => false
     *               56767 => false
     *               196737 => false
     *          ]
     *
     *         "peers" => [
     *               60 => [
     *                   "id" => 44
     *                   "custid" => 69
     *                   "peerid" => 60
     *                   "email_last_sent" => null
     *                   "emails_sent" => 0
     *                   "peered" => false
     *                   "rejected" => false
     *                   "notes" => ""
     *                   "created" => DateTime
     *                   "updated" => DateTime
     *                   "email_days" => -1
     *               ]
     *           ]
     *
     *          "custs" => [
     *               12041 => [
     *                   "id" => 146
     *                   "name" => "Afilias"
     *                   "shortname" => "afilias"
     *                   "autsys" => 12041
     *                   "maxprefixes" => 500
     *                   "peeringemail" => "peering@afilias-nst.info"
     *                   "peeringpolicy" => "open"
     *                   "vlaninterfaces" => [...]
     *                   "ispotential" => true
     *                   10 => [
     *                   4 => 1
     *                   ]
     *               ]
     *               56767 => [...]
     *               196737 => [...]
     *
     *     ]
     *
     * @param Customer  $cust   Current customer
     * @param Vlan[]    $vlans  Array of Vlans
     * @param array     $protos Array of protos
     *
     * @return array|null
     *
     */
    public static function getPeeringManagerArrayByType( Customer $cust, $vlans, array $protos ): ?array
    {
        if( !count( $vlans ) ) {
            return null;
        }

        $bilat = [];
        foreach( $vlans as $vlan ) {
            foreach( $protos as $proto ) {
                $bilat[ $vlan->number ][ $proto ] = BgpSessionDataAggregator::getPeers( $vlan->id, $proto );
            }
        }
        $vlanNumbers = Vlan::select( ['id', 'number'] )->get()->keyBy( 'id' )->toArray();

        $custs = Customer::currentActive( true, true, false )->with( 'vlanInterfaces' )->get()->keyBy( 'autsys' )->toArray();

        $potential = $potential_bilat = $peered = $rejected = [];

        foreach( $custs as $index => $value ){
            $vlanInterface = [];
            foreach( $value[ 'vlan_interfaces' ] as $i => $vli ){
                $vlanInterface[ $vlanNumbers[ $vli[ 'vlanid' ] ][ 'number' ] ][] = $vli;
            }

            $custs[ $index ][ 'vlan_interfaces' ] = $vlanInterface;
        }

        if( isset( $custs[ $cust->autsys ] ) ){
            $me = $custs[ $cust->autsys ];
            unset( $custs[ $cust->autsys ] );
        } else {
            $me = null;
        }

        foreach( $custs as $c ) {
            $custs[ $c[ 'autsys' ] ][ 'ispotential' ] = false;
            foreach( $vlans as $vlan ) {
                if( isset( $me[ 'vlan_interfaces' ][ $vlan->number ] ) ) {
                    if( isset( $c[ 'vlan_interfaces' ][$vlan->number] ) ) {
                        foreach( $protos as $proto ) {
                            if( $me[ 'vlan_interfaces' ][ $vlan->number ][ 0 ][ "ipv{$proto}enabled" ] && $c[ 'vlan_interfaces' ][ $vlan->number ][ 0 ][ "ipv{$proto}enabled" ] ) {
                                if( isset( $bilat[ $vlan->number ][ 4 ][ $me['autsys' ] ][ 'peers' ] ) && in_array( $c[ 'autsys' ], $bilat[ $vlan->number ][ 4 ][ $me[ 'autsys' ] ][ 'peers' ] ) ){
                                    $custs[ $c[ 'autsys' ] ][ $vlan->number ][$proto] = 2;
                                } else if( $me[ 'vlan_interfaces' ][ $vlan->number ][ 0 ][ 'rsclient' ] && $c[ 'vlan_interfaces' ][ $vlan->number ][ 0 ][ 'rsclient' ] ){
                                    $custs[ $c[ 'autsys' ] ][ $vlan->number ][ $proto ] = 1;
                                    $custs[ $c[ 'autsys' ] ][ 'ispotential' ] = true;
                                } else {
                                    $custs[ $c[ 'autsys' ] ][ $vlan->number ][ $proto ] = 0;
                                    $custs[ $c[ 'autsys' ] ][ 'ispotential' ] = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach( $custs as $c ) {
            $peered[          $c[ 'autsys' ] ] = false;
            $potential_bilat[ $c[ 'autsys' ] ] = false;
            $potential[       $c[ 'autsys' ] ] = false;
            $rejected[        $c[ 'autsys' ] ] = false;

            foreach( $vlans as $vlan ) {
                foreach( $protos as $proto ) {
                    if( isset( $c[ $vlan->number ][ $proto ] ) ) {
                        switch( $c[ $vlan->number ][ $proto ] ) {
                            case 2:
                                $peered[ $c[ 'autsys' ] ] = true;
                                break;
                            case 1:
                                $peered[          $c[ 'autsys' ] ] = true;
                                $potential_bilat[ $c[ 'autsys' ] ] = true;
                                break;
                            case 0:
                                $potential[       $c[ 'autsys' ] ] = true;
                                $potential_bilat[ $c[ 'autsys' ] ] = true;
                                break;
                        }
                    }
                }
            }
        }
        $peers = PeeringManager::selectRaw(
            'pm.id AS id, c.id AS custid, p.id AS peerid,
                pm.email_last_sent AS email_last_sent, pm.emails_sent AS emails_sent,
                pm.peered AS peered, pm.rejected AS rejected, pm.notes AS notes,
                pm.created_at AS created_at, pm.updated_at AS updated_at'
        )->from( 'peering_manager AS pm' )
            ->leftJoin( 'cust AS c', 'c.id', 'pm.custid')
            ->leftJoin( 'cust AS p', 'p.id', 'pm.peerid')
            ->where( 'c.id', $cust->id )
            ->get()->keyBy( 'peerid' )->toArray();

        foreach( $peers as $i => $p ) {
            // days since last peering request email sent
            if( !$p[ 'email_last_sent' ] ){
                $peers[ $i ][ 'email_days' ] = -1;
            } else {
                $email_last_sent = new Carbon( $p['email_last_sent']);
                $peers[ $i ][ 'email_days' ] = floor( ( time() - $email_last_sent->getTimestamp() ) / 86400 );
            }
        }

        foreach( $custs as $c ) {
            if( isset( $peers[ $c[ 'id' ] ] ) ) {
                if( isset( $peers[ $c[ 'id' ] ][ 'peered' ] ) && $peers[ $c[ 'id' ] ][ 'peered' ] ) {
                    $peered[            $c[ 'autsys' ] ] = true;
                    $rejected[          $c[ 'autsys' ] ] = false;
                    $potential[         $c[ 'autsys' ] ] = false;
                    $potential_bilat[   $c[ 'autsys' ] ] = false;
                } else if( isset( $peers[ $c[ 'id' ] ][ 'rejected' ] ) && $peers[ $c[ 'id' ] ][ 'rejected' ] ) {
                    $peered[            $c['autsys' ] ] = false;
                    $rejected[          $c['autsys' ] ] = true;
                    $potential[         $c['autsys' ] ] = false;
                    $potential_bilat[   $c['autsys' ] ] = false;
                }
            }
        }

        return [    "me"                => $me,
                    "potential"         => $potential,
                    "potential_bilat"   => $potential_bilat,
                    "peered"            => $peered,
                    "rejected"          => $rejected,
                    "peers"             => $peers,
                    "custs"             => $custs,
                    "bilat"             => $bilat,
                    "vlan"              => $vlans ,
                    "protos"            => $protos
        ];
    }

    /**
     * Delete the customer.
     *
     * Related models are mostly handled by 'ON DELETE CASCADE'.
     *
     * @param Customer $cust The customer Object
     *
     * @return bool
     *
     * @throws
     */
    public static function deleteObject( Customer $cust ): bool
    {
        try {
            DB::beginTransaction();

            // Delete Customer Logo
            if( $logo = $cust->logo ) {
                if( file_exists( $logo->fullPath() ) ) {
                    @unlink( $logo->fullPath() );
                }
                $logo->delete();
            }

            // delete contact to contact group links
            $contacts = $cust->contacts();

            DB::table( 'contact_to_group' )
                ->whereIn( 'contact_id', $contacts->get()->pluck( 'id' )->toArray(),)
                ->delete();

            $cust->contacts()->delete();

            foreach( $cust->customerToUser as $c2u ) {

                // Delete User Logins
                $c2u->userLoginHistories()->delete();

                $user = $c2u->user;
                $nbCust = $user->customerToUser()->count();
                // Delete Customer2User
                $c2u->delete();

                // Delete User, if that user only have the customer that we want to delete
                if( $nbCust === 1 ) {
                    $user->delete();
                    $user->refresh();
                } elseif( $user->custid === $cust->id ) {
                    if( $c = $user->customerToUser()->where( 'customer_id', '!=', $cust->id )->first() ){
                        $newAssignCust = $c->customer_id;
                    }

                    $user->custid = $newAssignCust ?? null;
                    $user->save();
                }
            }

            // Delete the Core Bundle
            $cbs = CoreBundle::select( 'cb.*' )->from( 'corebundles AS cb' )
                ->leftJoin( 'corelinks AS cl', 'cl.core_bundle_id', 'cb.id' )
                ->leftJoin( 'coreinterfaces AS ci', 'ci.id', 'cl.core_interface_sidea_id' )
                ->leftJoin( 'physicalinterface AS pi', 'pi.id', 'ci.physical_interface_id' )
                ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'pi.virtualinterfaceid' )
                ->where( 'vi.custid', $cust->id )->distinct()->get();

            foreach( $cbs as $cb ){
                /** @var $cb CoreBundle */
                $cb->deleteObject();
            }
            $cust->tags()->detach();
            
            $cust->delete();

            $cust->companyBillingDetail()->delete();
            $cust->companyRegisteredDetail()->delete();

            DB::commit();
        } catch( Exception $e ) {
            DB::rollBack();
            throw $e;
        }

        return true;
    }

    /**
     * Get atlas probes for a given customer and protocol.
     *
     * @param int       $protocol
     * @param int|null  $vlanid
     * @param array     $includeCust
     * @param string    $orderBy
     * @param int|null  $limit
     *
     * @return Collection
     */
    public static function withProbesForProtocol( int $protocol = 4, int $vlanid = null, array $includeCust = [], string $orderBy = 'name', int $limit = null ): Collection
    {
        $enabled = $protocol === 4 ? 'v4_enabled' : 'v6_enabled';

        return self::select('cust.*' )
            ->LeftJoin( 'atlas_probes', 'cust.id', 'atlas_probes.cust_id' )
            ->when( $vlanid , function( Builder $q, $vlanid ) {
                return $q->join( 'virtualinterface AS vi', 'cust.id', 'vi.custid' )
                    ->join( 'vlaninterface AS vli', 'vi.id','vli.virtualinterfaceid' )
                    ->where( 'vli.vlanid', $vlanid );
            })
            ->where( 'atlas_probes.' . $enabled , true )
            ->when( count( $includeCust ) , function( Builder $q) use ( $includeCust ) {
                return $q->whereIn( 'atlas_probes.cust_id',  $includeCust );
            })->when( $limit , function( Builder $q, $limit ) {
                return $q->limit( $limit );
            })->distinct()->orderBy( 'cust.' . $orderBy )
            ->get()->keyBy( 'autsys' );
    }
}