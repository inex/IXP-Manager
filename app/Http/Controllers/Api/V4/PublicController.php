<?php

declare(strict_types=1);
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

use Auth, D2EM;

use Illuminate\Http\{
    JsonResponse,
    Response
};

use Entities\Infrastructure;

/**
 * PublicController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PublicController extends Controller {

    /**
     * Simple test route for API authentication
     *
     * Documented at: http://docs.ixpmanager.org/features/api/
     *
     * @return Response
     */
    public function test(): Response {
        return response()->make( "API Test Function!\n\nAuthenticated: "
                . ( Auth::check() ? 'Yes, as: ' . Auth::user()->getUsername() : 'No' ) . "\n\n", 200 )
            ->header( 'Content-Type', 'text/plain; charset=utf-8' );
    }

    /**
     * Simple ping route for basic public information.
     *
     * @return JsonResponse
     */
    public function ping(): JsonResponse {

        $infras = [];

        /** @var Infrastructure $i */
        foreach( D2EM::getRepository( Infrastructure::class )->findAll() as $i ) {
            $infra = [];
            $infra['name'] = $i->getName();
            $infra['shortname'] = $i->getShortname();
            $infra['ixf_ix_id'] = $i->getIxfIxId();
            $infra['peeringdb_ix_id'] = $i->getPeeringdbIxId();

            $infras[] = $infra;
        }


        return response()->json([
            'software' => "IXP Manager",
            'version'  => APPLICATION_VERSION,
            'verdate'  => APPLICATION_VERDATE,
            'url'      => url(''),
            'ixf-export' => config( 'ixp_api.json_export_schema.public' ),
            'infrastructures' => $infras,
            'identity' => [
                'sitename'  => config( 'identity.sitename' ),
                'legalname' => config( 'identity.legalname' ),
                'orgname'   => config( 'identity.orgname' ),
                'corp_url'  => config( 'identity.corporate_url' ),
                'city'      => config( 'identity.location.city' ),
                'country'   => config( 'identity.location.country' ),
            ],
        ], 200, [], JSON_PRETTY_PRINT );
    }




}
