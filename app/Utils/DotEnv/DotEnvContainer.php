<?php

declare(strict_types=1);

namespace IXP\Utils\DotEnv;

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
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
 *
 */

use IXP\Exceptions\Utils\DotEnvInvalidSettingException;


/**
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package IXP\Utils\DotEnv
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DotEnvContainer
{
    /**
     * Constructs a new instance.
     *
     * @param array $settings
     */
    public function __construct( protected array $settings ) { }

    public function settings(): ?array
    {
        return $this->settings;
    }

    public function setSettings( ?array $settings ): static
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed (null if not set)
     */
    public function getValue( string $key ): mixed
    {
        foreach( $this->settings as $v ) {
            if( $v[ 'key' ] === $key ) {
                return $v[ 'value' ];
            }
        }

        return null;
    }
    
    /**
     * @param string $key
     * @return ?string
     */
    public function getComment( string $key ): ?string
    {
        foreach( $this->settings as $v ) {
            if( $v[ 'key' ] === $key ) {
                return $v[ 'comment' ];
            }
        }
        
        return null;
    }
    
    public function isset( string $key ): bool
    {
        return array_any( $this->settings, fn( $v ) => $v[ 'key' ] === $key );
    }

    public function indexOf( string $key ): ?int
    {
        return array_find_key( $this->settings, fn( $v ) => $v[ 'key' ] === $key );
    }

    public function unset( string $key ): array
    {
        foreach( $this->settings as $k => $v ) {
            if( $v[ 'key' ] === $key ) {
                return array_splice( $this->settings, $k, 1 );
            }
        }

        return [];
    }

    /**
     * For just a comment line, leave key and value null
     * For a blank line, leave them all as null
     *
     * @param string|null $key
     * @param string|null $value
     * @param string|null $comment
     * @return static
     * @throws DotEnvInvalidSettingException
     */
    public function set( ?string $key = null, ?string $value = null, ?string $comment = null ): static
    {
        // test
        if( $key === null && $value !== null ) {
            throw new DotEnvInvalidSettingException( 'Cannot set a value without a key' );
        }

        if( $key !== null && !preg_match( '/^[\w_]+$/', $key ) ) {
            throw new DotEnvInvalidSettingException( 'Invalid key exception: ' . $key );
        }

        // does the key already exist?
        if( $key !== null && $this->isset( $key ) ) {
            throw new DotEnvInvalidSettingException( 'Duplicate key exception: ' . $key );
        }

        $this->settings[] = [
            'key'     => $key,
            'value'   => $value === null ? null : DotEnvParser::parseValue( $value ),
            'comment' => $comment,
        ];

        return $this;
    }

    /**
     * For just a comment line, leave key and value null
     * For a blank line, leave them all as null
     *
     * @param string $key
     * @param ?string|null $value
     * @param ?string|null $comment
     * @return static
     * @throws DotEnvInvalidSettingException
     */
    public function replace( string $key, ?string $value = null, ?string $comment = null ): static
    {
        // does the key already exist?
        if( ( $idx = $this->indexOf( $key ) ) === null ) {
            throw new DotEnvInvalidSettingException( 'Cannot replace a key that does not exist: ' . $key );
        }

        $this->settings[ $idx ] = [
            'key'     => $key,
            'value'   => DotEnvParser::parseValue( $value ),
            'comment' => $comment,
        ];

        return $this;
    }
    
    
    /**
     * Update just the value of an existing key
     *
     * @param string $key
     * @param ?string|null $value
     * @return static
     * @throws DotEnvInvalidSettingException
     */
    public function updateValue( string $key, ?string $value = null ): static
    {
        // does the key already exist?
        if( ( $idx = $this->indexOf( $key ) ) === null ) {
            throw new DotEnvInvalidSettingException( 'Cannot update a key that does not exist: ' . $key );
        }
        
        $this->settings[ $idx ][ 'value' ] = DotEnvParser::parseValue( $value );
        
        return $this;
    }
    
}