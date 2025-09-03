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
 *
 * Based on code from https://github.com/MirazMac/DotEnvWriter with MIT license.
 *
 * A PHP library to write values to .env files.
 *
 */


use InvalidArgumentException;
use IXP\Exceptions\Utils\DotEnvParserException;
use const LOCK_EX;

/**
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package IXP\Utils\DotEnv
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DotEnvParser
{
    /**
     * Parsed settings
     *
     * @var        array
     */
    protected array $settings = [];

    /**
     * Constructs a new instance.
     *
     * @param string|null $content The dotEnv content
     */
    public function __construct( protected ?string $content = null ) {}

    public function content(): ?string
    {
        return $this->content;
    }

    public function setContent( ?string $content ): static
    {
        $this->content = $content;
        return $this;
    }

    public function settings(): array {
        return $this->settings;
    }

    /**
     * Parses the environment file line by line and store the variables
     * @throws DotEnvParserException
     */
    public function parse(): static
    {
        // reset parser if already used
        if( $this->settings !== [] ) {
            $this->settings = [];
        }

        $lines = preg_split( '/\r\n|\r|\n/', $this->content() );

        // remove trailing blank lines
        while( end( $lines ) === "" ) {
            array_pop( $lines );
        }

        // remove leading blank lines
        while( $lines[0] === "" ) {
            array_shift( $lines );
        }


        foreach( $lines as $line ) {

            $line = trim($line);

            if( mb_strlen( $line ) && mb_strpos( $line, '#' ) !== 0 ) {

                if( preg_match( '/^(\s*=)|([\w_]+\s+=)|([\w_]+=\s+[\w_"\']+).*/', $line ) ) {
                    throw new DotEnvParserException( "Cannot parse .env line: " . $line );

                } else if( preg_match( '/^[\w_]+=["\']?.*\${[\w_]+}.*[#]?.*$/', $line ) ) {
                    throw new DotEnvParserException( "Cannot parse .env line as nested variables are not supported: " . $line );

                } else if( mb_strlen( $line ) && mb_strpos( $line, '#' ) !== 0 && mb_strpos( $line, '=' ) > 1 ) {

                    // all any of the following:

                    // KEY=
                    // KEY=VALUE
                    // KEY=VALUE # COMMENT
                    // KEY=VALUE ### COMMENT
                    // KEY= # COMMENT

                    // equals can appear in strings after the initial one
                    $key          = mb_substr( $line, 0, mb_strpos( $line, '=' ) );
                    $valueElement = mb_substr( $line, mb_strpos( $line, '=' ) + 1 );

                    if( $this->isValidName( $key ) === false ) {
                        throw new DotEnvParserException( "Invalid key name: " . $key );
                    }

                    // is there a comment at the end of the line?
                    $values = explode( '#', $valueElement );

                    $value = $this->parseValue( array_shift( $values ) );

                    $comment = '';
                    if( count( $values ) === 0 ) {
                        $comment = null;
                    } else if( count( $values ) === 1 ) {
                        $comment = trim( $values[ 0 ] );
                    } else {
                        // multiple hashes in the comment element
                        while( ( $a = array_shift( $values ) ) !== null ) {
                            if( $a === '' ) {
                                $comment .= '#';
                            } else {
                                $comment .= $a;
                            }
                        }
                        $comment = trim( $comment );
                    }

                    $this->settings[] = [
                        "key"     => trim( $key ),
                        "value"   => $value,
                        "comment" => $comment,
                    ];
                } else {
                    throw new DotEnvParserException( "Cannot parse .env line: " . $line );
                }

            } else if( mb_strpos( $line, '#' ) === 0 ) {

                if( mb_strlen( $line ) === 1 ) {
                    // blank comment line
                    $this->settings[] = [
                        "key"     => null,
                        "value"   => null,
                        "comment" => "",
                    ];
                } else {
                    $this->settings[] = [
                        "key"     => null,
                        "value"   => null,
                        "comment" => trim( mb_substr( $line, 1 ) ),
                    ];
                }
            } else if( mb_strlen( $line ) === 0 ) {
                // blank line
                $this->settings[] = [
                    "key"     => null,
                    "value"   => null,
                    "comment" => null,
                ];
            } else {
                throw new DotEnvParserException( "Cannot parse .env line: " . $line );
            }
        }

        // check for duplicate keys
        $keys = [];
        foreach( $this->settings as $setting ) {
            if( $setting['key'] !== null && isset( $keys[ $setting['key'] ] ) ) {
                throw new DotEnvParserException( "Cannot parse .env - at least two variables have the same name: " . $setting['key'] );
            }
            $keys[ $setting['key'] ] = true;
        }

        return $this;
    }

    /**
     * Strips quotes from the values when reading
     *
     * @param string $value The value
     * @return     string
     */
    public static function stripQuotes( string $value ): string
    {
        // only if we have a single word
        if( preg_match( '/^\s*[\w\/\.\\_\-,:=\+]+\s*$/', $value ) ) {
            return preg_replace('/^([\'"])(.*?)\1$/u', '$2', $value) ?? '';
        }

        return $value;
    }

    /**
     * Formats the value for human friendly output
     *
     * @param string $value The value
     * @return     bool|int|string
     */
    public static function parseValue( string $value ): bool|int|string
    {
        $value = self::stripQuotes( $value );

        if( in_array( strtolower( $value ), [ 'true', 'on', 'yes' ] ) ) {
            return true;
        }

        if( in_array( strtolower( $value ), [ 'false', 'off', 'no' ] ) ) {
            return false;
        }

        if( preg_match( '/^\-?[0-9]+$/', $value ) ) {
            return (int)$value;
        }

        // if it's a single word, or empty, trim and return
        if( preg_match( '/^\s*[\w\/\.\\_\-,:=\+]+\s*$/', $value ) || $value === '' ) {
            return trim( $value );
        }

        // otherwise return the value, quoted to maintain whitespace
        $value = preg_replace('/^([\'"])(.*?)\1$/u', '$2', trim( $value ) ) ?? '';
        return '"' . $value . '"';
    }

    /**
     * Determines whether the specified key is valid name for .env files.
     *
     * @param string $key The key
     *
     * @return     bool
     */
    protected function isValidName( string $key ): bool
    {
        return (bool)preg_match( '/^[\w]+$/', $key );
    }


}