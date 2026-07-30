<?php

namespace Tests\Utils;

use IXP\Exceptions\GeneralException;
use Tests\TestCase;

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