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

namespace Tests\Services\Validation\Validators;

use IXP\Contracts\Validation\Validator;
use IXP\Services\Validation\Validators\As112;
use IXP\Services\Validation\Validators\Basic;
use IXP\Services\Validation\Validators\Config;
use IXP\Services\Validation\Validators\Grapher;
use IXP\Services\Validation\Validators\IxpManagerIsRegistered;
use IXP\Services\Validation\Validators\IxpManagerRunningLatestVersion;
use IXP\Services\Validation\Validators\NagiosMonitoring;
use IXP\Services\Validation\Validators\PatchPanel;
use IXP\Services\Validation\Validators\PeeringDb;
use IXP\Services\Validation\Validators\Router;
use IXP\Services\Validation\Validators\ScheduledTasks;
use IXP\Services\Validation\Validators\Security;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MetadataTest extends TestCase
{
    public static function metadataProvider(): array
    {
        return [
            [As112::class, "AS112 validator", "Check AS112 feature settings", 50],
            [Basic::class, "Basic validations", "Perform some basic system checks", 0],
            [Config::class, "Configuration validation", "Perform checks of the IXP Manager configuration", 20],
            [Grapher::class, "Grapher validator", "Performs checks on grapher configuration.", 44],
            [IxpManagerIsRegistered::class, "Check IXP's registered on IXP-Manager", "Checks that eligible infrastructures are recorded on ixpmanager.org", 30],
            [IxpManagerRunningLatestVersion::class, "IXP Manager version check", "Records which version of IXP Manager is installed, and notifies if an update is available.", 5],
            [NagiosMonitoring::class, "Nagios monitoring validator", "Checks that nagios configurations have been refreshed recently.", 14],
            [PatchPanel::class, "Patch Panel validator", "Checks patch panel configuration", 70],
            [PeeringDb::class, "Peering DB Setup Validator", "Checks PeeringDB integration", 48],
            [Router::class, "RPKI for Router Configuration", "Check router configuration", 40],
            [ScheduledTasks::class, "Scheduler Task validator", "Checks the IXP Manager task scheduler is running.", 13],
            [Security::class, "Security settings", "Check security settings are properly configured.", 10],
        ];
    }

    #[DataProvider('metadataProvider')]
    public function testMetadata(string $class, string $expectedName, string $expectedDescription, int $expectedPriority)
    {
        /** @var Validator $object */
        $object = new $class();
        $this->assertNotEmpty($object->getName(), $class . " validator name should not be empty");
        $this->assertNotEmpty($object->getDescription(), $class . " validator description should not be empty");

        $this->assertEquals($expectedName, $object->getName(), $class . " validator name should match expected value");
        $this->assertEquals($expectedDescription, $object->getDescription(), $class . " validator description should match expected value");
        $this->assertEquals($expectedPriority, $object->getPriority(), $class . " validator priority should match expected value");
    }
}