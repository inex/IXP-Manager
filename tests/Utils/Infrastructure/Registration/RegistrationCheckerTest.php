<?php
/*
 * Copyright (C)lat 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace Tests\Utils\Infrastructure\Registration;

use IXP\Models\Infrastructure;
use IXP\Services\Infrastructure\Registration\RegistrationChecker;
use Tests\TestCase;

class RegistrationCheckerTest extends TestCase
{
    /**
     * If ixpmanager.org doesn't have an infrastructure with our ixf id or pdb id
     * then it should be reported in RegistrationCheckResult->toRegister
     */
    public function testLookUpUnregisteredInfra()
    {
        $userList = '
        {
            "ixp_list": []
        }';
        $this->assertTrue(Infrastructure::whereExcludeFromIxfExport(false)->exists());

        $testInfrastructure = new Infrastructure();
        $testInfrastructure->name = "MyTestInfrastructure";
        $testInfrastructure->ixf_ix_id = 123;
        $testInfrastructure->peeringdb_ix_id = 999;
        $testInfrastructure->save();

        \Http::fake(
            ['https://www.ixpmanager.org/js/ixp-manager-users.json' => $userList]
        );

        $checker = new RegistrationChecker();
        $result = $checker->check();

        $this->assertCount(
            1,
            array_filter($result->eligibleInfrastructures, fn($i) => $i->id === $testInfrastructure->id),
            "our test infra should be eligible"
        );

        $this->assertCount(
            1,
            array_filter($result->toRegister, fn($i) => $i->id === $testInfrastructure->id),
            "our test infra should be in toRegister"
        );


        $this->assertCount(
            0,
            array_filter($result->alreadyRegistered, fn($i) => $i->id === $testInfrastructure->id),
            "our test infra is not already registered"
        );

        $testInfrastructure->delete();
    }

    /**
     * If ixpmanager.org has a match using either our ixf id or pdb id then the infrastructure
     * goes into RegistrationCheckResult->alreadyRegistered (ixf id for this test)
     */
    public function testLookUpUnregisteredInfraIxf()
    {
        $userList = '
        {
            "ixp_list": [
                {
                    "name": "Test Exchange",
                    "shortname": "ABCIX",
                    "city": "XX",
                    "country": "YY",
                    "cc": "ZZ",
                    "gps": [
                        1.25,
                        2.25
                    ],
                    "url": "https://ixp.local",
                    "ixf_id": 123,
                    "peeringdb_id": 999,
                    "since": 2020
                }
            ]
        }';
        $this->assertTrue(Infrastructure::whereExcludeFromIxfExport(false)->exists());

        $testInfrastructure = new Infrastructure();
        $testInfrastructure->name = "MyTestInfrastructure";
        $testInfrastructure->ixf_ix_id = 123;
        $testInfrastructure->peeringdb_ix_id = null;
        $testInfrastructure->save();

        \Http::fake(
            ['https://www.ixpmanager.org/js/ixp-manager-users.json' => $userList]
        );

        $checker = new RegistrationChecker();
        $result = $checker->check();

        $this->assertCount(
            1,
            array_filter($result->eligibleInfrastructures, fn($i) => $i->id === $testInfrastructure->id),
            "our test infra should be eligible"
        );

        $this->assertCount(
            0,
            array_filter($result->toRegister, fn($i) => $i->id === $testInfrastructure->id),
            "our test infra does not need to be registered"
        );


        $this->assertCount(
            1,
            array_filter($result->alreadyRegistered, fn($i) => $i->id === $testInfrastructure->id),
            "our test infra should already be registered"
        );

        $testInfrastructure->delete();
    }

    /**
     * If ixpmanager.org has a match using either our ixf id or pdb id then the infrastructure
     * goes into RegistrationCheckResult->alreadyRegistered (pdb for this test)
     */
    public function testLookUpUnregisteredInfraPdb()
    {
        $userList = '
        {
            "ixp_list": [
                {
                    "name": "Test Exchange",
                    "shortname": "ABCIX",
                    "city": "XX",
                    "country": "YY",
                    "cc": "ZZ",
                    "gps": [
                        1.25,
                        2.25
                    ],
                    "url": "https://ixp.local",
                    "ixf_id": 123,
                    "peeringdb_id": 999,
                    "since": 2020
                }
            ]
        }';
        $this->assertTrue(Infrastructure::whereExcludeFromIxfExport(false)->exists());

        $testInfrastructure = new Infrastructure();
        $testInfrastructure->name = "MyTestInfrastructure";
        $testInfrastructure->ixf_ix_id = null;
        $testInfrastructure->peeringdb_ix_id = 999;
        $testInfrastructure->save();

        \Http::fake(
            ['https://www.ixpmanager.org/js/ixp-manager-users.json' => $userList]
        );

        $checker = new RegistrationChecker();
        $result = $checker->check();

        $this->assertCount(
            1,
            array_filter($result->eligibleInfrastructures, fn($i) => $i->id === $testInfrastructure->id),
            "our test infra should be eligible"
        );

        $this->assertCount(
            0,
            array_filter($result->toRegister, fn($i) => $i->id === $testInfrastructure->id),
            "our test infra does not need to be registered"
        );


        $this->assertCount(
            1,
            array_filter($result->alreadyRegistered, fn($i) => $i->id === $testInfrastructure->id),
            "our test infra should already be registered"
        );

        $testInfrastructure->delete();
    }

    /**
     * If our infrastructure is marked `exclude_from_ixf_export=true` then it's not eligible
     * and never considered for toRegister or alreadyRegistered
     */
    public function testLookUpExcludedInfra()
    {
        $userList = '
        {
            "ixp_list": [
                {
                    "name": "Test Exchange",
                    "shortname": "ABCIX",
                    "city": "XX",
                    "country": "YY",
                    "cc": "ZZ",
                    "gps": [
                        1.25,
                        2.25
                    ],
                    "url": "https://ixp.local",
                    "ixf_id": 123,
                    "peeringdb_id": 999,
                    "since": 2020
                }
            ]
        }';
        $this->assertTrue(Infrastructure::whereExcludeFromIxfExport(false)->exists());

        $testInfrastructure = new Infrastructure();
        $testInfrastructure->name = "MyTestInfrastructure";
        $testInfrastructure->ixf_ix_id = null;
        $testInfrastructure->peeringdb_ix_id = 999;
        $testInfrastructure->exclude_from_ixf_export = true;
        $testInfrastructure->save();

        \Http::fake(
            ['https://www.ixpmanager.org/js/ixp-manager-users.json' => $userList]
        );

        $checker = new RegistrationChecker();
        $result = $checker->check();

        $this->assertCount(
            0,
            array_filter($result->eligibleInfrastructures, fn($i) => $i->id === $testInfrastructure->id),
            "it's excluded from ixf export"
        );

        $this->assertCount(
            0,
            array_filter($result->toRegister, fn($i) => $i->id === $testInfrastructure->id),
            "should not be registered"
        );


        $this->assertCount(
            0,
            array_filter($result->alreadyRegistered, fn($i) => $i->id === $testInfrastructure->id),
            "should not be registered"
        );

        $testInfrastructure->delete();
    }

    /**
     *
     */
    public function testAllExcluded()
    {
        $eligibleIds = Infrastructure::whereExcludeFromIxfExport(false)->get()->map(fn($i) => $i->id)->toArray();
        Infrastructure::whereIn('id', $eligibleIds)->update(['exclude_from_ixf_export' => true]);

        $checker = new RegistrationChecker();
        $result = $checker->check();

        $this->assertCount(
            0,
            $result->eligibleInfrastructures,
            "it's excluded from ixf export"
        );

        $this->assertCount(
            0,
            $result->toRegister,
            "should not be registered"
        );


        $this->assertCount(
            0,
            $result->alreadyRegistered,
            "should not be registered"
        );

        Infrastructure::whereIn('id', $eligibleIds)->update(['exclude_from_ixf_export' => false]);
    }
}