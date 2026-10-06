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

namespace Tests\Utils;

use Illuminate\Foundation\Testing\TestCase;
use IXP\Exceptions\GeneralException;

class SettingsUrlHelperTest extends TestCase
{
    public function testSettingsUrlToPanel()
    {
        $url = settings_ui_url( "frontend_controllers", null);
        $this->assertEquals(url('/admin') . "/settings/frontend_controllers", $url);
    }

    public function testSettingsUrlToFieldOnPanel()
    {
        $this->assertEquals(
            url('/admin') . "/settings/frontend_controllers#as112",
            settings_ui_url( "frontend_controllers", "as112")
        );
    }

    public function testSettingsUnknownPanel()
    {
        $this->expectException(GeneralException::class);
        $this->expectExceptionMessage("Panel [unknown] not defined");
        settings_ui_url( "unknown", null);
    }

    public function testSettingsUnknownField()
    {
        $this->expectException(GeneralException::class);
        $this->expectExceptionMessage("Field [corn] not defined on panel [frontend_controllers]");
        settings_ui_url( "frontend_controllers", "corn");
    }
}