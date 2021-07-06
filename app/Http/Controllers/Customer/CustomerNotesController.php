<?php

namespace IXP\Http\Controllers\Customer;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;

use IXP\Http\Controllers\Controller;

use IXP\Models\Customer;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\Http\{
    RedirectResponse
};

/**
 * Customer Notes Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerNotesController extends Controller
{
    /**
     * @return RedirectResponse
     * @throws
     */
    public function readAll() : RedirectResponse
    {
        $prefs = Auth::getUser()->prefs;
        // Delete all last_read notes prefs
        if( isset( $prefs[ 'notes' ][ 'last_read' ] ) ) {
            unset( $prefs[ 'notes' ][ 'last_read' ] );
        }

        // Set read_upto at now()
        $prefs[ 'notes' ][ 'read_upto' ] = now()->format( 'Y-m-d H:i:s' );

        Auth::getUser()->prefs = $prefs;
        Auth::getUser()->save();

        AlertContainer::push( 'All notes have been mark as read.', Alert::SUCCESS );

        return redirect( route( "customerNotes@unreadNotes" ) );
    }

    /**
     * Get the list of unread not for the current user
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function unreadNotes()
    {
        $lastRead       = Auth::getUser()->prefs[ 'notes' ][ 'last_read' ] ?? [];
        $readUpto       = Auth::getUser()->prefs[ 'notes' ][ 'read_upto' ] ?? null;
        $latestNotes    = [];

        $custs = Customer::selectRaw(
            'cust.id AS cid, cust.name AS cname, cust.shortname AS cshortname, MAX( cn.updated_at) as latest'
        )->join( 'cust_notes AS cn', 'cn.customer_id', 'cust.id' )
        ->groupByRaw( 'cid, cname, cshortname' )
        ->orderByDesc( 'latest' )->distinct()->get()->toArray();

        foreach( $custs as $c ) {
            if( ( !$readUpto || $readUpto < $c['latest'] )
                && ( !isset( $lastRead[ $c['cid'] ] ) || $lastRead[ $c[ 'cid' ] ] < $c['latest'] ) ) {
                $latestNotes[] = $c;
            }
        }

        return view( 'customer/unread-notes' )->with([
            'notes' => $latestNotes,
        ]);
    }
}

