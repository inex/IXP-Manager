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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Tests;

use Database\Seeders\Tests\CiTestDataSeeder;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\TestCase as BaseTestCase;

use Facebook\WebDriver\Chrome\ChromeOptions;

use Facebook\WebDriver\Remote\{
    DesiredCapabilities,
    RemoteWebDriver,
};
use PHPUnit\Framework\Attributes\BeforeClass;

/**
 * DuskTestCase
 *
 * These tests are isolated from eachother using the DatabaseTruncation trait which
 * truncates all tables after each test, and then re-seeding with the CiTestDataSeeder.
 *
 * DatabaseTruncation is used over RefreshDatabase as in these tests Laravel is run by
 * the artisan serve command and is not managed by the test suite directly. Hence we are
 * not in a position to wrap calls in a database transaction.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTruncation;

    protected $seed = true;
    protected $seeder = CiTestDataSeeder::class;

    /**
     * Prepare for Dusk test execution.
     *
     * @return void
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        static::startChromeDriver([
            '--port=9515'
        ]);
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return RemoteWebDriver
     */
    #[\Override]
    protected function driver(): RemoteWebDriver
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
}
