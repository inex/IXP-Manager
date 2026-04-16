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

namespace Tests\Utils\View\Alert;

use IXP\Utils\View\Alert\Alert;

class AlertTest extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * Class defaults to Alert::info when not provided
     * @return void
     */
    public function testDtoDefaultClass()
    {
        $message = "Your IXP was created successfully!";
        $alert = new Alert($message);

        $this->assertEquals($message, $alert->message());
        $this->assertEquals(Alert::INFO, $alert->class());
    }

    /**
     * Class can be set to any of the Alert::CLASSES
     * @return void
     */
    public function testDtoWithClass()
    {
        $message = "Your IXP was created successfully!";

        foreach (Alert::CLASSES as $class) {
            $alert = new Alert( $message, $class );

            $this->assertEquals( $message, $alert->message() );
            $this->assertEquals( $class, $alert->class() );
        }
    }

    /**
     * Unknown class will be substituted with Alert::INFO
     * @return void
     */
    public function testDtoWithUnknownClass()
    {
        $message = "Your IXP was created successfully!";
        $class = "unknown";

        $alert = new Alert( $message, $class );
        $this->assertEquals( $message, $alert->message() );
        $this->assertEquals( Alert::INFO, $alert->class() );
    }
}