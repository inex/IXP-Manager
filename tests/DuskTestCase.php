<?php

namespace Tests;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Laravel\Dusk\TestCase as BaseTestCase;

use Facebook\WebDriver\Chrome\ChromeOptions;

use Facebook\WebDriver\Remote\{
    DesiredCapabilities,
    RemoteWebDriver,

};

/**
 * DuskTestCase
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--lang=en-GB',
            '--window-size=1600,1200',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Overrides any .env files for dusk tests
     *
     * @param array $variables
     */
    protected function overrideEnv($variables = [])
    {
        $path = '.env';

        if (file_exists($path)) {

            // The environment variables to prepend
            $prepend = '';

            // Convert all new parameters to expected format
            foreach ($variables as $key => $value) {
                $prepend .= PHP_EOL . $key . '="' . $value . '"' ;
            }

            // Grab original .env file contents
            $original = file_get_contents($path);

            // Write all to .env file for dusk test
            file_put_contents($path, $original . $prepend);
        }
    }

    /**
     * Delete a value in .env files for dusk tests
     *
     * @param array $variables
     */
    protected function deleteEnvValue($variables = [])
    {
        $path = '.env';

        if ( file_exists( $path ) ) {

            // Grab original .env file contents
            $original = explode("\n", file_get_contents( $path ) );
            $output = '';
            // Convert all new parameters to expected format
            foreach ( $original as $value ) {
                if ( $value != $variables ) {
                    $output .= $value . PHP_EOL;
                }
            }

            // Write all to .env file for dusk test
            file_put_contents($path, $output );
        }
    }
}
