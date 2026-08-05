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

namespace Tests\Utils\Validation\Dto;

use IXP\Utils\Validation\Dto\Result;
use IXP\Utils\Validation\Enums\ResultType;
use Tests\TestCase;

class ResultFeatureTest extends TestCase
{
    public function testDto()
    {
        $result = new Result("Reporting back...", ResultType::Info);
        $this->assertEquals("Reporting back...", $result->message);
        $this->assertEquals(ResultType::Info, $result->type);

        // Test withDocsPath - involves helper method and config
        $this->assertNull($result->docsUrl);

        $result->withDocsPath("features/as112/");
        $this->assertEquals(config( 'ixp_fe.documentation.base_url' ) . "/features/as112/", $result->docsUrl);


        // Test withSettingsLink - involves helper method and config
        $this->assertNull($result->settingsUrl);

        $result->withSettingsLink("frontend_controllers");
        $this->assertEquals(route("settings@index", ['tab' => "frontend_controllers"]), $result->settingsUrl);

        $result->withSettingsLink("frontend_controllers", "as112");
        $this->assertEquals(route("settings@index", ['tab' => "frontend_controllers"]) . "#as112", $result->settingsUrl);

    }
}