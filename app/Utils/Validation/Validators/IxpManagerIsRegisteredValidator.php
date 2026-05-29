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

namespace IXP\Utils\Validation\Validators;

use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Models\Infrastructure;


/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class IxpManagerIsRegisteredValidator implements Validator
{
    public function getName(): string
    {
        return "Check IXP's registered on IXP-Manager";
    }

    public function getDescription(): string
    {
        return "Checks that eligible infrastructures are recorded on ixpmanager.org";
    }

    public function getPriority(): int
    {
        return 100;
    }

    public function run( ValidationBackend $backend ): void
    {
        $infrastructures = Infrastructure::whereExcludeFromIxfExport(false)->get();
        if ($infrastructures->isEmpty()) {
            $backend->info("All infrastructures are excluded from ixf-export");
            return;
        }

        try {
            $ixpList = \Http::get('https://www.ixpmanager.org/js/ixp-manager-users.json')->json('ixp_list');
        } catch (\Exception $e) {
            $ixpList = null;
            $backend->error("IXP list could not be retrieved from ixpmanager.org: " . $e->getMessage());
        }

        if ($ixpList) {
            $pdbIds        = array_column($ixpList, 'peeringdb_id');
            $ixfIds        = array_column($ixpList, 'ixf_id');
            $registerCount = 0;

            foreach ($infrastructures as $infra) {
                // an infrastructure is registered if we have a peeringdb id or ixf id, and we can
                // locate either in the IXP list obtained from ixpmanager.org
                $registered =
                    ($infra->peeringdb_ix_id != null && in_array($infra->peeringdb_ix_id, $pdbIds)) ||
                    ($infra->ixf_ix_id != null && in_array($infra->ixf_ix_id, $ixfIds));

                if ($registered) {
                    $registerCount++;
                } else if ($infra->peeringdb_ix_id != null || $infra->ixf_ix_id != null) {
                    // if we are not registered, but we have a peeringdb or ixf_ix_id, request the user to register
                    $backend->warning("Infrastructure " . $infra->name . " is not registered on ixpmanager.org");
                } else {
                    $backend->info("Infrastructure " . $infra->name . " has no PeeringDB ID or IXF-ID configured");
                }
            }

            if ($registerCount === count($infrastructures)) {
                $backend->info("All infrastructures are registered on ixpmanager.org");
            }
        }
    }
}