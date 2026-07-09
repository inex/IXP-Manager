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

namespace IXP\Utils\Validation\Validators;

use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Contracts\Grapher\Backend as GrapherBackendContract;
use IXP\Models\User;
use IXP\Models\Vlan;

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class GrapherValidator implements Validator
{
    public function getName(): string
    {
        return "Grapher validator";
    }

    public function getDescription(): string
    {
        return "Performs checks on grapher configuration.";
    }

    public function getPriority(): int
    {
        return 10;
    }

    public function run( ValidationBackend $backend ): void
    {
        $this->checkProviders($backend);
        $this->checkEnabledBackends($backend);
        $this->checkAccessPermissions($backend);
        $this->checkCacheSettings($backend);
        $this->checkBackendDummy($backend);
        $this->checkBackendMrtg($backend);
        $this->checkBackendSflow($backend);
        $this->checkBackendSmokeping($backend);
    }

    private function checkProviders(ValidationBackend $backend): void
    {
        // Each provider should refer to a PHP class implementing IXP\Contracts\Grapher\Backend.
        foreach (config('grapher.providers') as $grapherBackend => $backendClass) {
            if (!class_exists($backendClass)) {
                $backend->error("$grapherBackend backend provider ($backendClass) does not exist.");
            } else {
                $backendReflectionClass = new \ReflectionClass($backendClass);
                if (!$backendReflectionClass->implementsInterface(GrapherBackendContract::class)) {
                    $backend->error("Grapher backend provider ($backendClass) does not implement interface " . GrapherBackendContract::class);
                }
            }
        }
    }

    private function checkEnabledBackends( ValidationBackend $backend): void
    {
        // Warn if there are no active backends, or only dummy is configured. Otherwise print active backends & providers.
        // Relevant setting: GRAPHER_BACKENDS
        if (count(config('grapher.backend')) === 0) {
            $backend->warning("No backends configured");
        } else if (count(config('grapher.backend')) === 1 && config('grapher.backend')[0] === "dummy") {
            $backend->warning("Only the dummy backend is active.");
        } else {
            $backend->info("Backends enabled: " . implode(",", config('grapher.backend')));
            $backend->info("Providers defined: " . implode(",", array_keys(config('grapher.providers'))));
        }

        // Any backend in use should have a corresponding provider. Providers are checked separately in checkProviders.
        foreach (config('grapher.backend') as $grapherBackend) {
            if (!config()->has('grapher.providers.' . $grapherBackend)) {
                $backend->warning("Backend " . $grapherBackend . " missing from grapher providers configuration.");
            }
        }
    }

    /**
     * Check settings related to grapher cache use
     */
    private function checkCacheSettings( ValidationBackend $backend): void
    {
        // Log whether cache in use
        if (config('grapher.cache.enabled')) {
            $backend->info("Grapher cache is enabled. Cache store is " . config('grapher.cache.store'));
        } else {
            $backend->info("Grapher cache is not enabled. Cache store is " . config('grapher.cache.store'));
        }

        // Relevant config: GRAPHER_CACHE_STORE
        if (!in_array(config('grapher.cache.store'), array_keys(config('cache.stores')))) {
            $backend->error("Setting for GRAPHER_CACHE_STORE setting is not defined in cache configuration (config/cache.php)");
        }
    }

    private function checkAccessPermissions(ValidationBackend $backend): void
    {
        $infraGraphs = ['ixp', 'infrastructure', 'location', 'switch',  'vlan', 'trunk', 'core-bundle'];
        $infraAllowedOptions = array_keys(User::$PRIVILEGES_ALL);

        $memberGraphs = ['customer', 'p2p', 'latency'];
        $memberAllowedOptions = array_merge(array_keys(User::$PRIVILEGES_ALL), ['own_graphs_only']);

        foreach ($infraGraphs as $graphType) {
            if (!in_array(config('grapher.access.' . $graphType), $infraAllowedOptions)) {
                $backend->error("Invalid access level for $graphType graph " . config('grapher.access.' . $graphType));
            }
        }

        $publicMemberGraphs = [];
        foreach ($memberGraphs as $graphType) {
            if (!in_array(config('grapher.access.' . $graphType), $memberAllowedOptions)) {
                $backend->error("Invalid access level for $graphType graph " . config('grapher.access.' . $graphType));
            } else if (config('grapher.access.' . $graphType) === User::AUTH_PUBLIC) {
                $publicMemberGraphs[] = $graphType;
            }
        }

        if (count($publicMemberGraphs) > 0) {
            $backend->warning("Member " . implode(", ", $publicMemberGraphs) . " graphs configured with public access - is this intentional?");
        }
    }

    private function checkBackendDummy( ValidationBackend $backend): void
    {
        if (!file_exists(config('grapher.backends.dummy.logdir'))) {
            $backend->warning("dummy backend logdir does not exist.");
        } else if (!is_dir(config('grapher.backends.dummy.logdir'))) {
            $backend->error("dummy backend logdir must point to a directory.");
        } else if (!is_readable(config('grapher.backends.dummy.logdir'))) {
            $backend->error("No permissions to read dummy backends logdir.");
        }
    }

    private function checkBackendMrtg( ValidationBackend $backend): void
    {
        if (config('grapher.backends.mrtg.dbtype') != 'log' && config('grapher.backends.mrtg.dbtype') != 'rrd') {
            $backend->error("MRTG dbtype is not 'log' or 'rrd'.");
        }

        if (str_starts_with(config('grapher.backends.mrtg.logdir'), 'http://') || str_starts_with(config('grapher.backends.mrtg.logdir'), 'https://')) {
            $backend->info("MRTG files accessed via HTTP");
        } else if (str_starts_with(config('grapher.backends.mrtg.logdir'), 'ftp://') || str_starts_with(config('grapher.backends.mrtg.logdir'), 'https://')) {
            $backend->info("MRTG files accessed via FTP");
        } else {
            // Assumed local file
            if (!file_exists(config('grapher.backends.mrtg.logdir'))) {
                $backend->error("MRTG logdir does not exist.");
            } else if (!is_dir(config('grapher.backends.mrtg.logdir'))) {
                $backend->error("MRTG logdir is not set to a directory.");
            } else if (!is_readable(config('grapher.backends.mrtg.logdir'))) {
                $backend->error("No permissions to read mrtg logdir.");
            }
        }

        if (file_exists(config_path() . '/grapher_trunks.php')) {
            $backend->info("Found grapher_trunks.php configuration file");
        }

        if (is_array(config('grapher.backends.mrtg.trunks'))) {
            $numTrunks = count(config('grapher.backends.mrtg.trunks'));
            $backend->info($numTrunks . " trunks defined in configuration");
        } else if (!is_null(config('grapher.backends.mrtg.trunks'))) {
            $backend->error("MRTG trunks appears misconfigured. grapher_trunks.php file may contain invalid structure.");
        }
    }

    private function checkBackendSflow( ValidationBackend $backend): void
    {
        if (in_array('sflow', config('grapher.backend')) && ! config('grapher.backends.sflow.enabled')) {
            $backend->warning("sflow backend active but not enabled in frontend");
        }

        if (str_starts_with(config('grapher.backends.sflow.root'), 'http://') || str_starts_with(config('grapher.backends.sflow.root'), 'https://')) {
            $backend->info("Sflow files accessed via HTTP");
        } else if (str_starts_with(config('grapher.backends.sflow.root'), 'ftp://') || str_starts_with(config('grapher.backends.sflow.root'), 'https://')) {
            $backend->info("Sflow files accessed via FTP");
        } else {
            // Assumed local file
            if (!file_exists(config('grapher.backends.sflow.root'))) {
                $backend->error( "Sflow root does not exist." );
            } else if (!is_dir(config('grapher.backends.sflow.root'))) {
                $backend->error( "Sflow root is not set to a directory." );
            } else if (!is_readable(config('grapher.backends.sflow.root'))) {
                $backend->error( "No read permissions for sflow root." );
            }
        }
    }

    private function checkBackendSmokeping( ValidationBackend $backend): void
    {
        if (in_array('smokeping', config('grapher.backend')) && ! config('grapher.backends.smokeping.enabled')) {
            $backend->warning("smokeping backend active but not enabled in frontend");
        }

        // todo: check vlans in this file exist
        if (file_exists(config_path('grapher_smokeping_overrides.php'))) {
            $backend->info("Found grapher_smokeping_overrides.php configuration file");
        }
        if (config()->has('grapher.backends.smokeping.overrides.per_vlan_urls')) {
            $numOverrides = count(config('grapher.backends.smokeping.overrides.per_vlan_urls'));
            $backend->info($numOverrides . " per vlan overrides defined");
            foreach (config('grapher.backends.smokeping.overrides.per_vlan_urls') as $vlan => $url) {
                if (null === Vlan::whereNumber($vlan)->first()) {
                    $backend->warning("Vlan " . $vlan . " used in overrides does not exist. Please check grapher_smokeping_overrides.php file.");
                }
            }
        }
    }
}