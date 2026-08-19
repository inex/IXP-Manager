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

namespace Tests\Http\Controllers;

use Tests\TestCase;

/**
 *
 */
class SettingsControllerTest extends TestCase
{
    public function testConfiguration(): void
    {
        foreach (config('ixp_fe_settings.panels') as $panel) {
            foreach ($panel['fields'] as $field) {
                $this->assertTrue(config()->has($field['config_key']), "settings config_key " . $field['config_key'] . " is not defined in config()");
            }
        }
    }

}