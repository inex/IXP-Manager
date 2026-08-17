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

namespace IXP\Console\QaCommands;


/**
 * QA command to check that the settings UI configuration uses the correct config keys, etc
 */
class CheckSettingsUiConfig extends \Illuminate\Console\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qa:check-settings-ui-config {--ignore-config-key=*}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = "Checks that configuration options used in the Settings UI all exist";

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws \Throwable
     *
     * @psalm-return 0|1
     */
    public function handle(): int
    {
        $ignoredConfigKeys = $this->option('ignore-config-key');

        $incorrectConfigKey = false;

        foreach (config('ixp_fe_settings.panels') as $panelKey => $panel) {
            foreach ($panel['fields'] as $fieldKey => $field) {
                if (!in_array($field['config_key'], $ignoredConfigKeys) && !config()->has($field['config_key'])) {
                    $incorrectConfigKey = true;
                    $this->warn($panelKey . ' / ' . $fieldKey . ' config key ' . $field['config_key'] . ' is not defined in config()');
                }
            }
        }

        if ($incorrectConfigKey) {
            return 1;
        }

        return 0;
    }
}