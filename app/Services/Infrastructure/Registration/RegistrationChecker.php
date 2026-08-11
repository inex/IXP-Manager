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

namespace IXP\Services\Infrastructure\Registration;

use IXP\Models\Infrastructure;

/**
 * InfrastructureRegistrationChecker
 *
 * This class checks the local database for infrastructures eligible for registration on ixpmanager.org.
 * Eligible simply means, not excluded from ixf export.
 *
 * If there are eligible infrastructures, we sort them depending on whether they are already registered
 * or to-be registered, and return this in a results class.
 *
 * note: this _could_ be improved, by tracking which fields are registered (pdb/ixf id).. and if we added
 * a new field which isn't registered, we _could_ report that back as to be registered.. but not super important now.
 */
class RegistrationChecker
{
    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function check(): RegistrationCheckResult
    {
        \Log::info("Checking infrastructures for registration on ixpmanager.org");
        $result = new RegistrationCheckResult();

        $result->eligibleInfrastructures = Infrastructure::whereExcludeFromIxfExport(0)->get()->all();
        if (count($result->eligibleInfrastructures) === 0) {
            // no eligible infrastructures, move on
            return $result;
        }

        \Log::info("Fetching registered IX information from ixpmanager.org");

        $ixpList    = \Http::get(ixp_manager_website_url('/js/ixp-manager-users.json'))->json('ixp_list');
        $pdbIds     = array_column($ixpList, 'peeringdb_id');
        $ixfIds     = array_column($ixpList, 'ixf_id');

        foreach ($result->eligibleInfrastructures as $infra) {
            // an infrastructure is registered if we have a peeringdb id or ixf id, and we can
            // locate either in the IXP list obtained from ixpmanager.org
            $registered =
                ($infra->peeringdb_ix_id != null && in_array($infra->peeringdb_ix_id, $pdbIds)) ||
                ($infra->ixf_ix_id != null && in_array($infra->ixf_ix_id, $ixfIds));

            if ($registered) {
                $result->alreadyRegistered[] = $infra;
            } else if ($infra->peeringdb_ix_id != null || $infra->ixf_ix_id != null) {
                // if we are not registered, but we have a peeringdb or ixf_ix_id, request the user to register
                $result->toRegister[] = $infra;
            }
        }

        return $result;
    }
}