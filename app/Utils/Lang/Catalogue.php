<?php

namespace IXP\Utils\Lang;

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

use JsonException;
use RuntimeException;

/**
 * Read and write a JSON translation catalogue - lang/<locale>.json
 *
 * Laravel's "JSON translation" files are a flat map of English source string
 * to translation. Keys are written sorted so that a diff of the file shows
 * only what actually changed, and unicode is left unescaped so a translator
 * (or a reviewer) can read the file.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Utils\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Catalogue
{
    /**
     * @param   string  $locale
     * @param   array   $lines   source string => translation
     */
    public function __construct(
        private readonly string $locale,
        private array $lines = [],
    ) {
    }

    /**
     * Load the catalogue for a locale. A missing file is an empty catalogue.
     *
     * @param   string  $locale
     *
     * @return static
     *
     * @throws RuntimeException  if the file exists but is not valid JSON
     */
    public static function load( string $locale ): static
    {
        $path = self::path( $locale );

        if( !file_exists( $path ) ) {
            return new static( $locale );
        }

        try {
            $lines = json_decode( (string)file_get_contents( $path ), true, 512, JSON_THROW_ON_ERROR );
        } catch( JsonException $e ) {
            throw new RuntimeException( "{$path} is not valid JSON: {$e->getMessage()}", previous: $e );
        }

        if( !is_array( $lines ) ) {
            throw new RuntimeException( "{$path} does not contain a JSON object" );
        }

        return new static( $locale, $lines );
    }

    /**
     * Path of the catalogue file for a locale.
     *
     * @param   string  $locale
     *
     * @return string
     */
    public static function path( string $locale ): string
    {
        return base_path( 'lang/' . $locale . '.json' );
    }

    /**
     * @return string
     */
    public function locale(): string
    {
        return $this->locale;
    }

    /**
     * source string => translation
     *
     * @return array
     */
    public function all(): array
    {
        return $this->lines;
    }

    /**
     * @param   string  $key
     *
     * @return bool
     */
    public function has( string $key ): bool
    {
        return isset( $this->lines[ $key ] ) && trim( (string)$this->lines[ $key ] ) !== '';
    }

    /**
     * @param   string  $key
     *
     * @return string|null
     */
    public function get( string $key ): ?string
    {
        return $this->has( $key ) ? (string)$this->lines[ $key ] : null;
    }

    /**
     * Set (or, with an empty value, remove) a translation.
     *
     * @param   string  $key
     * @param   string|null  $translation
     *
     * @return void
     */
    public function set( string $key, ?string $translation ): void
    {
        if( $translation === null || trim( $translation ) === '' ) {
            unset( $this->lines[ $key ] );
            return;
        }

        $this->lines[ $key ] = $translation;
    }

    /**
     * Drop every translation whose key is not in $keys.
     *
     * @param   string[]  $keys
     *
     * @return string[]  the keys that were removed
     */
    public function prune( array $keys ): array
    {
        $keep    = array_flip( $keys );
        $removed = [];

        foreach( array_keys( $this->lines ) as $key ) {
            if( !isset( $keep[ $key ] ) ) {
                $removed[] = $key;
                unset( $this->lines[ $key ] );
            }
        }

        return $removed;
    }

    /**
     * Write the catalogue back to disk, sorted.
     *
     * @return string  the path written
     */
    public function save(): string
    {
        ksort( $this->lines, SORT_NATURAL | SORT_FLAG_CASE );

        $path = self::path( $this->locale );

        if( !is_dir( $dir = dirname( $path ) ) ) {
            mkdir( $dir, 0755, true );
        }

        file_put_contents( $path, json_encode(
            $this->lines,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        ) . "\n" );

        return $path;
    }
}
