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

namespace Tests\Trait;

use Tests\DuskTestCase;

/**
 * ModifiesEnv is a trait used to help with test classes, mostly dusk test cases
 * where the .env file is the only way to influence the applications configuration
 *
 * Its aim is to make it easy to make a change to the env file, and to automatically
 * restore it's contents after the test completes.
 *
 * The one place where there is a risk we'd miss a change is if a test overwrites
 * the env file in a setUp method_before_ calling parent::setUp
 */
trait ModifiesEnv
{
    /**
     * Store the original env file so it can be restored later
     */
    protected ?string $originalEnvBeforeModification = null;

    /**
     * This method will copy the .env file before a test method is run
     */
    public function setUpModifiesEnv(): void
    {
        $file = $this->ensureFileExists( $this->getEnvFileLocation() );
        $this->ensureEnvFileCopiedBeforeModification( $file );
    }

    /**
     * This copies the .env file if we have not already done so. This code can't
     * live in setUpModifiesEnv as in one test case, we modify the .env before we
     * call any setUp methods! So, we should aim to be robust against this, and
     * make sure our env modification methods also call this too
     */
    protected function ensureEnvFileCopiedBeforeModification( string $file ): void
    {
        if (null === $this->originalEnvBeforeModification) {
            $this->originalEnvBeforeModification = file_get_contents( $file );
        }
    }

    /**
     * This method will check if the env file has different contents to when we made
     * a copy, and if so, reverts it back to the copy we made.
     * If the class using this trait inherits from DuskTestCase we'll call
     * awaitArtisanEnvReload.
     */
    public function tearDownModifiesEnv(): void
    {
        $file = $this->ensureFileExists( $this->getEnvFileLocation() );
        if( file_get_contents( $file ) !== $this->originalEnvBeforeModification ) {
            file_put_contents( $file, $this->originalEnvBeforeModification );
            if( array_key_exists( DuskTestCase::class, class_parents( static::class ) ) ) {
                $this->awaitArtisanEnvReload();
            }
        }
    }

    /**
     * sleep for some time, allowing ./artisan serve to catch up with changes to the .env file
     * (this was 1s in SettingsControllerTest, 2s in User2FAControllerTest, be optimistic and
     * take 1s for now)
     */
    protected function awaitArtisanEnvReload(): void
    {
        usleep( 1_000_000 );
    }

    /**
     * Return the location for the .env file
     */
    protected function getEnvFileLocation(): string
    {
        return __DIR__ . "/../../.env";
    }

    /**
     * Checks if the file exists, and if so returns the path. Otherwise raises an exception.
     */
    protected function ensureFileExists( string $file ): string
    {
        if ( !file_exists( $file ) ) {
            throw new \RuntimeException( "Path to env file does not exist: " . $file );
        }
        return $file;
    }

    /**
     * Takes provided $envFileContents and completely overwrites the .env file
     */
    protected function overwriteEnvFile( string $envFileContents ): void
    {
        $path = $this->ensureFileExists( $this->getEnvFileLocation() );
        $this->ensureEnvFileCopiedBeforeModification( $path );
        file_put_contents( $path, $envFileContents );
    }

    /**
     * Overrides any .env files for dusk tests.
     * Variables consists of key => value pairs.
     *
     * @param array $variables
     */
    protected function overrideEnv( array $variables = [] ): void
    {
        $path = $this->ensureFileExists( $this->getEnvFileLocation() );
        $this->ensureEnvFileCopiedBeforeModification( $path );

        // The environment variables to prepend
        $prepend = '';

        // Convert all new parameters to expected format
        foreach ($variables as $key => $value) {
            $prepend .= PHP_EOL . $key . '="' . $value . '"' ;
        }

        // Grab original .env file contents
        $original = file_get_contents( $path );

        // Write all to .env file for dusk test
        file_put_contents( $path, $original . $prepend );
    }

    /**
     * Replace any .env file attribute for dusk tests
     * If there are no attribute find, add it at the end of file
     *
     * @param string $fromAttribute
     * @param string $toAttribute
     */
    protected function replaceEnvAttr( string $fromAttribute, string $toAttribute ): void
    {
        $path = $this->ensureFileExists( $this->getEnvFileLocation() );
        $this->ensureEnvFileCopiedBeforeModification( $path );

        // Grab original .env file contents
        $original = explode( "\n", file_get_contents( $path ) );
        $output = '';
        $replaced = false;
        $exist = false;
        // Iterate through the attributes
        foreach ( $original as $value ) {
            if ( $value == $toAttribute ) {
                $exist = true;
            }
            if ( $value != $fromAttribute ) {
                // write in the rest of the values
                $output .= $value . PHP_EOL;
            } else {
                // Replace the Attribute
                $output .= $toAttribute . PHP_EOL;
                $replaced = true;
            }
        }

        //if not replaced, and doesn't exist the attribute, add it
        if ( !$replaced && !$exist ) {
            $output .= $toAttribute . PHP_EOL;
        }

        // Write all to .env file for dusk test
        file_put_contents( $path, $output );
    }

    /**
     * Delete a value in .env files for dusk tests
     *
     * @param string $attribute
     */
    protected function deleteEnvValue( string $attribute ): void
    {
        $path = $this->ensureFileExists( $this->getEnvFileLocation() );
        $this->ensureEnvFileCopiedBeforeModification( $path );

        // Grab original .env file contents
        $original = explode("\n", file_get_contents( $path ) );
        $output = '';
        // Iterate through the attributes
        foreach ( $original as $value ) {
            if ( $value != $attribute ) {
                $output .= $value . PHP_EOL;
            }
        }

        // Write all to .env file for dusk test
        file_put_contents( $path, $output );
    }
}