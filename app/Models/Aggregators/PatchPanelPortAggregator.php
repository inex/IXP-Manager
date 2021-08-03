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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use IXP\Models\PatchPanelPort;


/**
 * IXP\Models\Aggregators\PatchPanelPortAggregator
 *
 * @property-read \IXP\Models\Customer $customer
 * @property-read PatchPanelPort $duplexMasterPort
 * @property-read Collection|PatchPanelPort[] $duplexSlavePorts
 * @property-read int|null $duplex_slave_ports_count
 * @property-read \IXP\Models\PatchPanel $patchPanel
 * @property-read Collection|\IXP\Models\PatchPanelPortFile[] $patchPanelPortFiles
 * @property-read int|null $patch_panel_port_files_count
 * @property-read Collection|\IXP\Models\PatchPanelPortFile[] $patchPanelPortFilesPublic
 * @property-read int|null $patch_panel_port_files_public_count
 * @property-read Collection|\IXP\Models\PatchPanelPortHistory[] $patchPanelPortHistories
 * @property-read int|null $patch_panel_port_histories_count
 * @property-read \IXP\Models\SwitchPort $switchPort
 * @method static Builder|PatchPanelPort masterPort()
 * @method static Builder|PatchPanelPortAggregator newModelQuery()
 * @method static Builder|PatchPanelPortAggregator newQuery()
 * @method static Builder|PatchPanelPortAggregator query()
 * @mixin \Eloquent
 */
class PatchPanelPortAggregator extends PatchPanelPort
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patch_panel_port AS ppp';

    /**
     * Return the list of patch panel ports
     *
     * @param int|null  $ppid
     * @param bool      $advanced
     * @param int|null  $location
     * @param int|null  $cabinet
     * @param int|null  $cabletype
     * @param bool      $availableForUse
     *
     * @return Collection
     */
    public static function list( int $ppid = null, bool $advanced = false, int $location = null, int $cabinet = null, int $cabletype = null, bool $availableForUse = false ): Collection
    {
        return self::selectRaw( '
            ppp.*,
            count( pppf.id ) AS files, count( ppph.id ) AS histories,
            pp.name as ppname, pp.port_prefix AS prefix,
            sp.name AS spname,
            s.name AS sname, c.abbreviatedName AS cname,
            count( ppps.id ) AS nbslave, 
            max( ppps.number ) AS slavenumber' ) // only one slave port!
            ->from( 'patch_panel_port AS ppp' )
            ->join( 'patch_panel AS pp', 'pp.id', 'ppp.patch_panel_id')
            ->leftJoin( 'switchport AS sp', 'sp.id', 'ppp.switch_port_id')
            ->leftJoin( 'switch AS s', 's.id', 'sp.switchid')
            ->leftJoin( 'cust AS c', 'c.id', 'ppp.customer_id')
            ->leftJoin( 'patch_panel_port_file AS pppf', 'pppf.patch_panel_port_id', 'ppp.id')
            ->leftJoin( 'patch_panel_port_history AS ppph', 'ppph.patch_panel_port_id', 'ppp.id')
            ->leftJoin( 'patch_panel_port AS ppps', 'ppps.duplex_master_id', 'ppp.id')
            ->when( !$advanced , function( Builder $q ) {
                return $q->whereNull( 'ppp.duplex_master_id' );
            } )
            ->when( $ppid , function( Builder $q, $ppid ) {
                return $q->where('ppp.patch_panel_id', $ppid );
            } )
            ->when( $cabinet || $location , function( Builder $q ) use( $location, $cabinet ) {
                return $q->leftJoin( 'cabinet as cab', 'cab.id', 'pp.cabinet_id' )
                    ->when( $cabinet , function( Builder $q, $cabinet ) {
                        return $q->where( 'pp.cabinet_id', $cabinet );
                    })->when( $location , function( Builder $q, $location ) {
                        return $q->where('cab.locationid', $location );
                    } );
            } )
            ->when( $cabletype , function( Builder $q, $cabletype ) {
                return $q->where('pp.cable_type', $cabletype );
            } )
            ->when( $availableForUse , function( Builder $q ) {
                return $q->whereIn('ppp.state', PatchPanelPort::$AVAILABLE_STATES );
            } )
            ->groupByRaw( 'ppp.id, ppp.number' )
            ->orderBy( 'ppp.number' )->get()->keyBy( 'id' );
    }

    /**
     * Get all the patch panel ports available for a patch panel ID
     *
     * Possibility to exclude some ppp id from the list
     *
     * port available => PatchPanelPort::$AVAILABLE_FOR_ALLOCATION_STATES
     *
     * @param   int         $ppid           ID of the patch panel
     * @param   array       $excludeIds     Patch Panel Port ID that we want to exclude from the list
     * @param   int|null    $includeSlave   Patch Panel Port ID of the slave to include
     * @param   bool        $excludeDuplex  Should we exclude the duplex port ?
     *
     * @return  array   list of patch panel form key => pppId , value => ppp name
     */
    public static function getAvailablePorts( int $ppid, $excludeIds = [], int $includeSlave = null, bool $excludeDuplex = true )
    {
        $ppps = PatchPanelPort::selectRaw(
            'ppp.id, ppp.number, 
            ppp.patch_panel_id, ppp.duplex_master_id,
            GROUP_CONCAT( pp.port_prefix, ppp.number ) AS name' )
            ->from( 'patch_panel_port AS ppp' )
            ->join( 'patch_panel AS pp', 'pp.id', 'ppp.patch_panel_id' )
            ->leftJoin( 'patch_panel_port AS ppps', 'ppps.duplex_master_id', 'ppp.id' )
            ->where( 'pp.id', $ppid )
            ->when( $excludeDuplex , function( Builder $q ) {
                return $q->whereNull( 'ppps.duplex_master_id' )
                    ->whereNull( 'ppp.duplex_master_id' );
            }, function( $q ) {
                return $q->whereNull( 'ppp.duplex_master_id' );
            } )
            ->whereIn( 'ppp.state', PatchPanelPort::$AVAILABLE_FOR_ALLOCATION_STATES )
            ->when( $includeSlave , function( Builder $q, $includeSlave ) {
                return $q->orWhere('ppp.id', $includeSlave );
            } )
            ->when( count( $excludeIds ) > 0 , function( Builder $q ) use( $excludeIds ) {
                return $q->whereNotIn('ppp.id', $excludeIds );
            } )
            ->groupBy( 'ppp.id' )->orderBy( 'ppp.number' )
            ->get();

        if( $excludeDuplex ){
            return $ppps->toArray();
        }

        $result = [];
        foreach ( $ppps as $ppp ){
            /** @var $ppp PatchPanelPort */
            $result[ $ppp->id ] = [
                'id'        => $ppp->id,
                'name'      => $ppp->name(),
                'isDuplex'  => $ppp->duplexSlavePorts()->exists()
            ];
        }
        return $result;
    }
}