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

namespace IXP\Services\Validation\Validators;

use Illuminate\Http\Client\ConnectionException;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Services\Infrastructure\Registration\RegistrationChecker;
use IXP\Models\Infrastructure;


/**
 * This class asks people to register each infrastructure on ixpmanager.org
 *
 * The checks are only performed on infrastructures which are not excluded from ixf export.
 *
 * An infrastructure is deemed registered if it has a PeeringDB ID or an IX-F ID, and that id
 * (doesn't matter which, just one of them) appears in the ixpmanager.org users list.
 *
 * We report if an infrastructure has ID's but is not registered, and when they have
 * neither ID configured.
 *
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
final class IxpManagerIsRegistered implements Validator
{
    #[\Override]
    public function getName(): string
    {
        return "Check IXP's registered on IXP-Manager";
    }

    #[\Override]
    public function getDescription(): string
    {
        return "Checks that eligible infrastructures are recorded on ixpmanager.org";
    }

    #[\Override]
    public function getPriority(): int
    {
        return 30;
    }

    #[\Override]
    public function run( ValidationBackend $backend ): void
    {
        $checker = app(RegistrationChecker::class);
        try {
            $result = $checker->check();
        } catch ( ConnectionException $e) {
            $backend->warning("Could not fetch registered networks from ixpmanager.org: " . $e->getMessage());
            return;
        }

        // If we have infrastructures, but none are included in the ixf-export, just end now:
        if (Infrastructure::count() > 0 && count($result->eligibleInfrastructures) === 0) {
            // todo: unfair to make this a warning?
            $backend->info("All infrastructures are excluded from ixf-export")
                ->withDocsPath("features/ixf-export/");
            return;
        }

        foreach ($result->eligibleInfrastructures as $infra) {
            if ($infra->peeringdb_ix_id === null && $infra->ixf_ix_id === null) {
                $backend->info("Infrastructure " . $infra->name . " has no PeeringDB ID or IXF-ID configured")
                    ->withDocsPath("features/ixf-export/");
            }
        }

        if (count($result->toRegister) > 0) {
            $ids = array_map(fn($i) => $i->id, $result->toRegister);
            $backend->warning("Found infrastructures not registered on ixpmanager.org!")
                ->withCallToAction("Register", route('ixp-registration@register', ['infrastructure' => $ids]));
        }

        // todo: is this unnecessary noise?
        if (count($result->eligibleInfrastructures) > 0 && count($result->alreadyRegistered) === count($result->eligibleInfrastructures)) {
            $backend->info("All eligible infrastructures are registered on ixpmanager.org");
        }
    }
}