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

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    PatchPanelPort,
};

/**
 * Common Functions Patch panel port Controllers
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\PatchPanel\Port
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Common extends Controller
{
    /**
     * Generate the LoA PDF
     *
     * @param PatchPanelPort $ppp
     *
     * @return array To be unpacked with list( $pdf, $pdfname )
     */
    protected function createLoaPDF( PatchPanelPort $ppp ): array
    {
        $pdf = app('dompdf.wrapper');
        $pdf->loadView( 'patch-panel-port/loa', [ 'ppp' => $ppp ] );
        $pdfName = sprintf( "LoA-%s-%s.pdf", $ppp->circuitReference(), now()->format( 'Y-m-d' ) );
        return [ $pdf, $pdfName ];
    }
}