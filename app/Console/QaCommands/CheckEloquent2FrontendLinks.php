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

use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use IXP\Console\Commands\Command;
use IXP\Models\User;
use IXP\Utils\Http\Controllers\Frontend\EloquentController;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

/**
 * Artisan command to check configured links within EloquentController classes
 */
class CheckEloquent2FrontendLinks extends  Command
{
    protected $signature = 'qa:check-e2f-links';

    protected $description = "This QA command inspects EloquentControllers feParams, looking for listColumns or viewColumns column definitions with invalid routes to the respective models (just hasOne so far). As controllers may vary the columns based on user privileges, we test each controller at each privilege level.";

    /**
     * controllers to skip during analysis
     */
    protected array $ignoredControllers = [
        "\IXP\Http\Controllers\RipeAtlas\MeasurementController",
    ];

    public function handle(): int
    {
        $pathRoot = app_path("/Http/Controllers/");

        $routes = Route::getRoutes();

        $errors = [];
        // Recursive search for .php files in Controllers directory
        foreach ($this->recursiveSearch($pathRoot, ".php") as $file) {
            // Make FQDN for each found class
            $class = "\\IXP\\Http\\Controllers\\" . basename(str_replace("/", "\\", substr($file, strlen($pathRoot) )), ".php");

            if (in_array($class, $this->ignoredControllers)) {
                if ($this->isVerbosityVerbose()) {
                    $this->warn("Ignoring class $class");
                }
                continue;
            }

            $reflectionClass = new ReflectionClass($class);
            if (!$reflectionClass->isSubclassOf(EloquentController::class)) {
                continue;
            }

            // Only interested in eloquent controllers, they have the frontend stuff. Load feParams.
            foreach (array_keys(User::$PRIVILEGES) as $privs) {
                if ($this->isVerbosityVerbose()) {
                    $this->info("Checking controller $class with privs $privs");
                }
                // Some controllers, eg ContactController, modify their columns based on the current users permissions.
                Auth::login(User::byPrivs($privs)->firstOrFail());

                $feParams = $this->getFeParamsForController($class);

                if (is_array($feParams->listColumns)) {
                    foreach ($feParams->listColumns as $modelKey => $cdef) {
                        if (!is_array($cdef)) {
                            continue;
                        }
                        if ($columnErrors = $this->analyseColumn("listColumns", $class, $privs, $modelKey, $routes, $cdef)) {
                            $errors = array_merge($errors, $columnErrors);
                        }
                    }
                }

                if (is_array($feParams->viewColumns)) {
                    foreach ($feParams->viewColumns as $modelKey => $cdef) {
                        if (!is_array($cdef)) {
                            continue;
                        }
                        if ($columnErrors = $this->analyseColumn("viewColumns", $class, $privs, $modelKey, $routes, $cdef)) {
                            $errors = array_merge($errors, $columnErrors);
                        }
                    }
                }
            }
        }

        foreach ($errors as $error) {
            $this->warn($error);
        }

        return count($errors) > 0 ? 1 : 0;
    }

    private function getFeParamsForController(string $class): \stdClass
    {
        $controller = new $class();
        $controller->feInit();
        $reflection = new \ReflectionProperty($class, 'feParams');

        /** @var \stdClass */
        return $reflection->getValue($controller);
    }

    /**
     * Given a cdef (column definition), inspect how it links to the model.
     * If the routes are not registered, or any other warning condition arises, a log
     * will be included in the returned array of errors
     */
    private function analyseColumn( string $feParamsList, string $classPath, int $privs, string $modelKey, RouteCollectionInterface $routes, array $cdef ): array
    {
        $errors = [];
        if (array_key_exists('type', $cdef) && $cdef['type'] === "hasOne") {
            if (array_key_exists('route', $cdef)) {
                // cDef is using route to refer to the model, does the route exist?
                $route = $routes->getByName($cdef['route']);
                if (null === $route) {
                    $errors[] = "$classPath (privilege level $privs): $feParamsList $modelKey  - route {$cdef['route']} doesn't exist";
                }
            } else if (array_key_exists('controller', $cdef) && array_key_exists('action', $cdef)) {
                // cDef is using controller/action to refer to the model, does the uri exist?
                $found = [];
                foreach ($routes as $route) {
                    if (str_contains($route->uri(), $cdef['controller'] . "/" . $cdef['action'])) {
                        $found[] = $route;
                    }
                }
                if (empty($found)) {
                    $errors[] = "$classPath (privilege level $privs): $feParamsList $modelKey no route(s) found for " . $cdef['controller'] . "/" . $cdef['action'];
                }
            } else {
                // cDef isn't using either, apparently? hasOne cdef's are supposed to refer to something.
                if (!array_key_exists('controller', $cdef)) {
                    $errors[] = "$classPath (privilege level $privs): $feParamsList $modelKey is missing controller";
                }
                if (!array_key_exists('action', $cdef)) {
                    $errors[] = "$classPath (privilege level $privs): $feParamsList $modelKey is missing action";
                }
            }
        }

        return $errors;
    }

    /**
     * Performs a recursive search for files nested within $rootDirectory which contain $pattern.
     *
     * @return string[]
     */
    private function recursiveSearch( string $rootDirectory, string $pattern): array
    {
        $matches = [];
        foreach( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $rootDirectory ) ) as $file) {
            /** @var \SplFileInfo $file */
            if( str_contains( $file->getFilename(), $pattern ) ) {
                $matches[] = $file->getRealPath();
            }
        }
        return $matches;
    }
}
