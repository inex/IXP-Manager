<?php

namespace IXP\Http\Controllers\Api\V4;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth;
use Illuminate\Http\{Request,Response};

use IXP\Utils\Export\JsonSchema as JsonSchemaExporter;

class MemberExportController extends Controller {



    /**
     * API call to generate DNS ARPA records in a given format
     *
     * @param Request $r
     * @param string $version Version fo schema to export
     * @return Response
     */
    public function ixf( Request $r, string $version = JsonSchemaExporter::EUROIX_JSON_LATEST ) {

        if( $r->access_key ) {
            if( $r->access_key !== config( 'ixp_api.json_export_schema.access_key' ) ) {
                abort( 401, 'Invalid access key' );
            }
        } elseif( !Auth::check() && !config( 'ixp_api.json_export_schema.public', false ) ) {
            abort(401, 'Public access not permitted' );
        }

        $withTags = $r->query('withtags', null) === "1";

        $exporter = new JsonSchemaExporter;
        return response()->json( $exporter->get( $version, true, Auth::check(), $withTags ), 200, [], JSON_PRETTY_PRINT )
            ->header( "Access-Control-Allow-Origin", "*" );
    }

}
