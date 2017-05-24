<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MemberExportController extends Controller {



    /**
     * API call to generate DNS ARPA records in a given format
     *
     * @param int $vlanid Database id of a vlan to generate the ARPA entries for (vlan.id)
     * @param int $protocol Protocol to generate the ARPA entries for
     * @return Response
     */
    public function ixf( Request $request, string $version = '0.6' ) {

        if( !Auth::check() && !config( 'ixp_api.json_export_schema.public', false ) ) {
            abort(401, 'Public access not permitted' );
        }

        $exporter = new \IXP\Utils\Export\JsonSchema;
        return response()->json( $exporter->get( $version, true, Auth::check() ) );
    }

}
