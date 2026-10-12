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

use IXP\Contracts\Validation\ValidationRunner;
use IXP\Services\Validation\Backend;
use IXP\Services\Validation\Enums\Severity;
use IXP\Services\Validation\Validators\Grapher;
use Tests\TestCase;

class GrapherTest extends TestCase
{
    public function testBasicInfo()
    {
        $validator = new Grapher();
        $this->assertEquals("Grapher validator", $validator->getName());
        $this->assertEquals("Performs checks on grapher configuration.", $validator->getDescription());
        $this->assertEquals(44, $validator->getPriority());
    }

    public function testProviderUnknownClass()
    {
        $providers = config('grapher.providers');
        $providers['myprovider'] = '\IXP\UnknownNamespace\UnknownClass';
        config(['grapher.providers' => $providers]);

        $runner = new Backend(Grapher::class);
        $runner->run();
        $this->assertHasError($runner, "myprovider backend provider (\IXP\UnknownNamespace\UnknownClass) does not exist.");
    }

    public function testProviderWrongInterface()
    {
        $providers = config('grapher.providers');
        $providers['myprovider'] = 'Directory';
        config(['grapher.providers' => $providers]);

        $runner = new Backend(Grapher::class);
        $runner->run();
        $this->assertHasError($runner, "Grapher backend provider (Directory) does not implement interface IXP\Contracts\Grapher\Backend");
    }

    private function assertHasError( ValidationRunner $runner, $message)
    {
        $located = false;
        foreach ($runner->getResults() as $result) {
            if ($result->severity === Severity::Error && $result->message === $message) {
                $located = true;
            }
        }
        $this->assertTrue($located, "The provided error was not found in results");
    }
}