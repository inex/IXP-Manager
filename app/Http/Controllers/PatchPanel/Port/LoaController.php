<?php

namespace IXP\Http\Controllers\PatchPanel\Port;

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

use Auth, Log;

use Illuminate\Http\{
    Response,
};

use Illuminate\View\View;

use IXP\Models\{
    PatchPanelPort,
    User
};

/**
 * PatchPanelPort Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\PatchPanel\Port
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LoaController extends Common
{
    /**
     * Download a Letter of Authority file - LoA
     *
     * @param   PatchPanelPort $ppp the patch panel port
     *
     * @return  Response
     */
    public function download( PatchPanelPort $ppp ): Response
    {
        $this->setupLoA( $ppp );
        [ $pdf, $pdfname ] = $this->createLoaPDF( $ppp );
        return $pdf->download( $pdfname );
    }

    /**
     * View a Letter of Authority file - LoA
     *
     * @param   PatchPanelPort $ppp the patch panel port
     *
     * @return  Response
     */
    public function view( PatchPanelPort $ppp ): Response
    {
        $this->setupLoA( $ppp );
        [ $pdf, $pdfname ] = $this->createLoaPDF( $ppp );
        return $pdf->stream( $pdfname );
    }

    /**
     * Bootstrap LoA request
     *
     * @param PatchPanelPort $ppp
     *
     * @return void
     */
    private function setupLoA( PatchPanelPort $ppp ): void
    {
        $u = User::find( Auth::id() );
        if( $ppp->customer_id !== $u->custid  && !$u->isSuperUser() ) {
            Log::alert($u->username . ' tried to create a PPP LoA for PPP:' . $ppp->id . ' but does not have permission');
            abort(401);
        }
    }

    /**
     * Allow to access to the Loa with the patch panel port ID and the LoA code
     *
     * @param  PatchPanelPort   $ppp        The patch panel port
     * @param  string           $loaCode    LoA Code
     *
     * @return  View
     */
    public function verify( PatchPanelPort $ppp, string $loaCode ): View
    {
        if( $ppp->loa_code !== $loaCode ) {
            Log::alert( "Failed PPP LoA verification for port {$ppp->id} from {$_SERVER['REMOTE_ADDR']} - invalid LoA code presented" );
        } else if( !$ppp->stateAwaitingXConnect() ) {
            Log::alert( "PPP LoA verification denied for port {$ppp->id} from {$_SERVER['REMOTE_ADDR']} - port status is not AwaitingXConnect" );
        }
        return view( 'patch-panel-port/verify-loa' )->with([
            'ppp'           => $ppp,
            'loaCode'       => $loaCode
        ]);
    }
}