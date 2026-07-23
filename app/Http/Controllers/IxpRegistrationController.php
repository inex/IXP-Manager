<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Http\Controllers;

use Former\Facades\Former;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use IXP\Http\Controllers\Utils\InfrastructureRegistrationChecker;
use IXP\Http\Requests\RegisterInfrastructureRequest;
use IXP\Models\Infrastructure;
use Countries;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container;

class IxpRegistrationController
{
    public function register( Request $request, InfrastructureRegistrationChecker $checker): View|RedirectResponse
    {
        if ($infrastructureList = $request->infrastructure) {
            $query = Infrastructure::whereExcludeFromIxfExport(0);
            if (is_array($infrastructureList)) {
                // Were we given a list of infrastructures?
                $query->whereIn('id', $infrastructureList);
            } else {
                // or just one single ID? (not used by the validator, but support it anyway)
                $query->whereId($infrastructureList);
            }
            $infrastructures = $query->get()->all();

            if (count($infrastructures) === 0) {
                Container::push("Empty list of infrastructures. (perhaps they are excluded from IXF export?)", Alert::INFO);
                return redirect()->to(route("admin@dashboard"));
            }
        } else {
            // If we weren't given a list of infrastructures, check our DB for unregistered infrastructures now
            try {
                $result = $checker->check();
            } catch ( ConnectionException $e ) {
                Container::push("Could not connect to IXP Manager website to check for registration", Alert::WARNING);
                return redirect()->to(route("admin@dashboard"));
            }

            if (count($result->toRegister) === 0) {
                Container::push("No infrastructures need to be registered", Alert::SUCCESS);
                return redirect()->to(route("admin@dashboard"));
            }

            $infrastructures = $result->toRegister;
        }

        Former::populate([
            'website'             => request()->old( 'website', config('identity.corporate_url') ),
            'ixpmurl'             => request()->old( 'ixpmurl', config('identity.url') ),
        ]);

        return view('ixp-registration.register', [
            'infrastructures'    => $infrastructures,
            'countries'         => Countries::getList('name' ),
        ]);
    }

    public function registerSubmit( RegisterInfrastructureRequest $request): RedirectResponse
    {
        $payload = [
            'website'            => $request->website,
            'ixpmurl'            => $request->ixpmurl,
            'since'              => $request->since,
            'submitted_by_name'  => $request->submitted_by_name,
            'submitted_by_email' => $request->submitted_by_email,
            'submitted_by_ml'    => $request->submitted_by_ml,
            'infrastructure'    => [],
        ];

        foreach ($request->infrastructure as $infrastructure) {
            if ($infrastructure['register'] === '1') {
                $payload['infrastructure'][] = [
                    'fullname'    => $infrastructure['fullname'],
                    'shortname'   => $infrastructure['shortname'],
                    'city'        => $infrastructure['city'],
                    'country'     => $infrastructure['country'],
                    'peeringdbid' => $infrastructure['peeringdbid'],
                    'ixfid'       => $infrastructure['ixfid'],
                    'gpsx'        => $infrastructure['gpsx'],
                    'gpsy'        => $infrastructure['gpsy'],
                ];
            }
        }

        // Did not provide any infrastructures with 'register' clicked?
        if (count($payload['infrastructure']) === 0) {
            Container::push("You must register at least one infrastructure", Alert::INFO);
            return redirect()->back()->withInput();
        }

        try {
            $response = \Http::withHeaders(['Accept' => 'application/json'])
                ->post(config('ixp_api.ixp-manager-dotorg.base_url') . "/api/community/submit-ixp", $payload);

            if ($response->successful()) {
                Container::push("Registration received!", Alert::SUCCESS);
                return redirect()->to(route('admin@dashboard'));
            }

            if ($response->clientError()) {
                \Log::warning("Client error while registering IXP manager.", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                Container::push("An HTTP error " . $response->status() . " occurred while registering your infrastructures. Application logs may have more information.", Alert::WARNING);
            } else {
                Container::push("A server HTTP error " . $response->status() . " occurred while processing your request.", Alert::WARNING);
            }
        } catch (ConnectionException $e) {
            Container::push("Failed to connect to " . config('ixp_api.ixp-manager-dotorg.base_url') . " - please try again later", Alert::WARNING);
        } catch (\Throwable $e) {
            \Log::error( "Unexpected error during IXP registration", [ 'error' => $e->getMessage() ] );
            Container::push( "An unexpected error occurred. Please try again.", Alert::WARNING );
        }

        return redirect()->back()->withInput();
    }
}