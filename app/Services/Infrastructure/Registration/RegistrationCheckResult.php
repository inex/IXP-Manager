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
 * InfrastructureRegistrationCheckResult
 * @see RegistrationChecker
 */
class RegistrationCheckResult
{
    /**
     * Any infrastructures which are not excluded from IX-F export are eligible for registration.
     * If an infrastructure doesn't have peeringdb_ix_id and ixf_ix_id it'll be here, but won't
     * be sorted into alreadyRegistered or toRegister.
     * @var Infrastructure[]
     */
    public array $eligibleInfrastructures = [];

    /**
     * These infrastructures were already registered.
     * @var Infrastructure[]
     */
    public array $alreadyRegistered = [];

    /**
     * These infrastructures should be registered (unregistered but have a peeringdb_ix_id or
     * ixf_ix_id)
     * @var Infrastructure[]
     */
    public array $toRegister = [];
}