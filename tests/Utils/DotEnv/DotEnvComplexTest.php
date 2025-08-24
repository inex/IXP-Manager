<?php
declare(strict_types=1);

namespace Tests\Utils\DotEnv;

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Utils\DotEnv\DotEnvContainer;
use IXP\Utils\DotEnv\DotEnvWriter;
use PHPUnit\Framework\TestCase;

use IXP\Exceptions\Utils\DotEnvParserException;
use IXP\Utils\DotEnv\DotEnvParser;

/**
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package IXP\Utils\DotEnv
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
final class DotEnvComplexTest extends TestCase
{
    private const string ENV_FILE = __DIR__ . '/../../../data/ci/known-good/ci-dotenv-complex.txt';
    
    /**
     * @throws DotEnvParserException
     */
    public function testContentAndSetContent(): void
    {
        $this->assertFileExists( self::ENV_FILE );
        $this->assertFileIsReadable( self::ENV_FILE );
        
        $container = new DotEnvContainer( new DotEnvParser( file_get_contents( self::ENV_FILE ) )->parse()->settings() );
        
        $this->assertEquals( file_get_contents( self::ENV_FILE ), new DotEnvWriter( $container->settings() )->generateContent() );
    }


}
