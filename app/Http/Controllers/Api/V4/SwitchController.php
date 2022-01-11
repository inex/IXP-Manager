<?php

namespace IXP\Http\Controllers\Api\V4;

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

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\{
    Request,
    JsonResponse
};

use IXP\Models\{
    Aggregators\SwitcherAggregator,
    Switcher,
    SwitchPort
};

/**
 * SwitcherController API Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchController extends Controller
{
    /**
     * Get the switch port for a Switch
     *
     * @param   Request     $r      instance of the current HTTP request
     * @param   Switcher    $s      switch
     *
     * @return  JsonResponse JSON array of listPort
     */
    public function ports( Request $r, Switcher $s ): JsonResponse
    {
        return response()->json( [
            'ports' => SwitcherAggregator::allPorts( $s->id , $r->types ?? [] , $r->spIdsExcluded ?? [], (bool)$r->notAssignToPI, (bool)$r->piNull )
        ] );
    }

    /**
     * Get the switch port for a Switch for patch panel port
     *
     * @param Request   $r
     * @param Switcher  $s
     *
     * @return  JsonResponse array of listPort
     */
    public function switchPortForPPP( Request $r, Switcher $s ): JsonResponse
    {
        return response()->json( [
            'listPorts' => SwitchPort::selectRaw( 'sp.name AS name, sp.type AS type, sp.id AS id' )
                ->from( 'switchport AS sp' )
                ->leftJoin( 'patch_panel_port AS ppp', 'ppp.switch_port_id', 'sp.id' )
                ->where( 'sp.switchid', $s->id )
                ->when( $r->custId , function( Builder $q ) use( $r ) {
                    return $q->leftJoin( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
                        ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'pi.virtualinterfaceid' )
                        ->where('vi.custid', $r->custId );
                } )
                ->when( $r->spId , function( Builder $q, $r ) {
                    return $q->where('sp.id', '!=', $r->spId );
                })
                ->whereNull( 'ppp.switch_port_id' )
                ->orderBy( 'sp.id' )->get()->toArray()
        ] );
    }

    /**
     * Get the Prewired switch port for a Switch
     *
     * @param Request   $r
     * @param Switcher  $s
     *
     * @return  JsonResponse array of listPort
     */
    public function switchPortPrewired( Request $r, Switcher $s ): JsonResponse
    {
        return response()->json( [
            'listPorts' => SwitchPort::selectRaw( 'sp.name AS name, sp.type AS type, sp.id AS id' )
                ->from( 'switchport AS sp' )
                ->leftJoin( 'patch_panel_port AS ppp', 'ppp.switch_port_id', 'sp.id' )
                ->whereRaw( 'sp.id NOT IN ( SELECT pi.switchportid
                                      FROM physicalinterface pi )' )
                ->when( $r->spId , function( Builder $q, $r ) {
                    return $q->where('sp.id', '!=', $r->spId );
                })
                ->where( 'sp.switchid', $s->id )
                ->whereNull( 'ppp.switch_port_id' )
                ->whereIn( 'sp.type', [ SwitchPort::TYPE_UNSET, SwitchPort::TYPE_PEERING ] )
                ->orderBy( 'sp.id' )->get()->toArray()
        ] );
    }

    /**
     * Get the switch status for monitoring purposes
     *
     * @param  Switcher  $s
     *
     * @return JsonResponse
     */
    public function status( Switcher $s ): JsonResponse
    {
        return response()->json( $s->status() );
    }

    /**
     * Get the switch status for monitoring purposes
     *
     * @param  Switcher  $s
     *
     * @return JsonResponse
     */
    public function coreBundlesStatus( Switcher $s ): JsonResponse
    {
        $okay = true;
        $msgs = [];

        foreach( $s->getCoreBundles() as $cb ) {
            if( $cb->enabled ) {
                $linksup      = count( $cb->coreLinksWithIfOperStateX() ); // with no args this defaults to X = oper state up for enabled links
                $linksenabled = count( $cb->corelinks()->active()->get()->toArray() );

                if( $linksup === $linksenabled ) {
                    $msgs[] = $cb->switchSideX( true )->name . ' - ' . $cb->switchSideX( false )->name . " OK - {$linksup}/${linksenabled} links up";
                } else {
                    $okay = false;
                    $msgs[] = 'ISSUE: ' . $cb->switchSideX( true )->name . ' - ' . $cb->switchSideX( false )->name . " has {$linksup}/${linksenabled} links up";
                }
            } else {
                $msgs[] = 'Ignoring ' . $cb->switchSideX( true )->name . ' - ' . $cb->switchSideX( false )->name . ' as core bundle disabled';
            }
        }

        if( $msgs === [] ) {
            $msgs[] = "No core bundles configured for this switch";
        }
        return response()->json( [ 'status' => $okay, 'switchname' => $s->name, 'msgs' => $msgs ] );
    }
}