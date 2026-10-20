<?php
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
*/

declare(strict_types=1);

namespace Tests\Rules;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use IXP\Rules\Sha256MatchesFile;
use Tests\TestUtils\ValidationErrorLogger;

class Sha256MatchesFileTest extends TestCase
{
    public function testRule(): void
    {
        $uploadedFile = UploadedFile::fake()->create( 'test.txt' );
        $realSha256 = hash( 'sha256', $uploadedFile->getContent() );

        $rule = new Sha256MatchesFile( 'uploadedFile', 'file', 'sha256' );

        // DataAwareRule:
        $rule->setData( [ 'uploadedFile' => $uploadedFile ] );

        // New file uploaded, correct Sha256 provided - no errors
        $rule->validate( '', $realSha256, ( $fails = new ValidationErrorLogger() )( ... ) );
        $this->assertEquals( [], $fails->fails );

        // New file uploaded, no sha256 provided - no errors
        $rule->validate( '', null, ( $fails = new ValidationErrorLogger() )( ... ) );
        $this->assertEquals( [], $fails->fails );

        // New file uploaded, wrong sha256 provided - returns errors
        $rule->validate( '', '4242424242424242424242424242424242424242424242424242424242424242', ( $fails = new ValidationErrorLogger() )( ... ) );
        $this->assertEquals( [ 'The sha256 checksum calculated on the server does not match the one you provided.' ], $fails->fails );
    }
}