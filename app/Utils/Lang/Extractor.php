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

/**
 * Finds translatable strings in the source tree.
 *
 * IXP Manager keys translations on the English source string itself
 * (`__( 'Peering Manager' )`), so a "key" here is always the English text.
 *
 * Templates are Foil (`.foil.php`) or Blade (`.blade.php`) and both are just
 * PHP with inline HTML, so the primary scan is a real PHP tokenise rather than
 * a regex - that way `__( 'x' )` inside a comment or inside another string is
 * not picked up. Blade's `{{ }}` / `{!! !!}` / `@lang()` constructs are not PHP
 * to the tokeniser, so those are rewritten into PHP tags first.
 *
 * Only literal single- or double-quoted strings with no interpolation are
 * collected: a translation key has to be static or it cannot be extracted for
 * a translator.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Utils\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Extractor
{
    /**
     * Functions whose first argument is a translation key.
     *
     * @var string[]
     */
    public const array FUNCTIONS = [ '__', 'trans' ];

    /**
     * key => list of [ 'file' => ..., 'line' => ... ]
     *
     * @var array
     */
    private array $keys = [];

    /**
     * Call sites we found but could not extract because the first argument was
     * not a literal string (e.g. `__( $someVariable )`).
     *
     * @var array
     */
    private array $dynamic = [];

    /**
     * Scan the given files.
     *
     * @param  iterable  $files  absolute paths
     *
     * @return static
     */
    public function scan( iterable $files ): static
    {
        foreach( $files as $file ) {
            $this->scanFile( (string)$file );
        }

        ksort( $this->keys, SORT_NATURAL | SORT_FLAG_CASE );

        return $this;
    }

    /**
     * The extracted keys: key => list of [ 'file' => ..., 'line' => ... ]
     *
     * @return array
     */
    public function keys(): array
    {
        return $this->keys;
    }

    /**
     * Call sites with a non-literal first argument, as
     * [ 'file' => ..., 'line' => ..., 'function' => ... ]
     *
     * These cannot be translated as they stand and generally want rewriting to
     * use a placeholder, e.g. `__( 'Welcome :name', [ 'name' => $n ] )`.
     *
     * @return array
     */
    public function dynamic(): array
    {
        return $this->dynamic;
    }

    /**
     * Placeholders (`:name`, `:Name`, `:NAME`) used within a key.
     *
     * These must survive translation verbatim, so we surface them to the
     * translator and check them on import.
     *
     * @param   string  $key
     *
     * @return string[]
     */
    public static function placeholders( string $key ): array
    {
        preg_match_all( '/(?<!\w):([A-Za-z_][A-Za-z0-9_]*)/', $key, $m );

        return array_values( array_unique( $m[ 1 ] ?? [] ) );
    }

    /**
     * Scan a single file.
     *
     * @param   string  $file
     *
     * @return void
     */
    private function scanFile( string $file ): void
    {
        if( ( $src = @file_get_contents( $file ) ) === false ) {
            return;
        }

        if( str_ends_with( $file, '.blade.php' ) ) {
            $src = self::bladeToPhp( $src );
        }

        $this->scanSource( $src, $file );
    }

    /**
     * Rewrite the Blade constructs the PHP tokeniser cannot see into PHP tags.
     *
     * `{{ expr }}` and `{!! expr !!}` become `<?php expr; ?>`, and `@lang(...)`
     * becomes `<?php __(...); ?>`. Line count is preserved so reported line
     * numbers stay correct.
     *
     * @param   string  $src
     *
     * @return string
     */
    public static function bladeToPhp( string $src ): string
    {
        // {!! ... !!} first, so that its braces are not eaten by the {{ }} rule
        $src = preg_replace_callback( '/\{!!(.*?)!!\}/s', static function( array $m ): string {
            return '<?php ' . $m[ 1 ] . '; ?>';
        }, $src );

        $src = preg_replace_callback( '/\{\{(.*?)\}\}/s', static function( array $m ): string {
            return '<?php ' . $m[ 1 ] . '; ?>';
        }, $src );

        // @lang( ... ) - find the matching close paren rather than assuming
        // there are no nested parens or parens inside the string
        $out    = '';
        $offset = 0;

        while( ( $pos = strpos( $src, '@lang', $offset ) ) !== false ) {
            $open = $pos + 5;

            // skip whitespace between @lang and (
            while( $open < strlen( $src ) && ctype_space( $src[ $open ] ) ) {
                $open++;
            }

            if( ( $src[ $open ] ?? '' ) !== '(' ) {
                $out    .= substr( $src, $offset, $pos + 5 - $offset );
                $offset = $pos + 5;
                continue;
            }

            if( ( $close = self::matchingParen( $src, $open ) ) === null ) {
                $out    .= substr( $src, $offset, $pos + 5 - $offset );
                $offset = $pos + 5;
                continue;
            }

            $out    .= substr( $src, $offset, $pos - $offset );
            $out    .= '<?php __' . substr( $src, $open, $close - $open + 1 ) . '; ?>';
            $offset = $close + 1;
        }

        return $out . substr( $src, $offset );
    }

    /**
     * Index of the ')' matching the '(' at $open, or null.
     *
     * Quoted sections are skipped so that a paren inside a string does not
     * throw the count off.
     *
     * @param   string  $src
     * @param   int     $open
     *
     * @return int|null
     */
    private static function matchingParen( string $src, int $open ): ?int
    {
        $depth = 0;
        $len   = strlen( $src );

        for( $i = $open; $i < $len; $i++ ) {
            $c = $src[ $i ];

            if( $c === '"' || $c === "'" ) {
                $quote = $c;

                while( ++$i < $len ) {
                    if( $src[ $i ] === '\\' ) {
                        $i++;
                        continue;
                    }

                    if( $src[ $i ] === $quote ) {
                        break;
                    }
                }

                continue;
            }

            if( $c === '(' ) {
                $depth++;
            } elseif( $c === ')' && --$depth === 0 ) {
                return $i;
            }
        }

        return null;
    }

    /**
     * Tokenise and collect.
     *
     * @param   string  $src
     * @param   string  $file
     *
     * @return void
     */
    private function scanSource( string $src, string $file ): void
    {
        // a template that is not valid PHP on its own should not abort the run:
        try {
            $tokens = @token_get_all( $src );
        } catch( \Throwable ) {
            return;
        }

        $count = count( $tokens );

        for( $i = 0; $i < $count; $i++ ) {
            $token = $tokens[ $i ];

            if( !is_array( $token ) || $token[ 0 ] !== T_STRING
                    || !in_array( $token[ 1 ], self::FUNCTIONS, true ) ) {
                continue;
            }

            // Skip method and static calls ($obj->trans(...), Foo::trans(...))
            // and declarations (function trans(...)) - only the global helper
            // functions are translation calls.
            $prev = self::significantToken( $tokens, $i, -1 );

            if( is_array( $prev ) && in_array( $prev[ 0 ],
                    [ T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_NULLSAFE_OBJECT_OPERATOR ], true ) ) {
                continue;
            }

            if( self::significantToken( $tokens, $i, 1 ) !== '(' ) {
                continue;
            }

            $argIndex = self::significantIndex( $tokens, $i, 1 ) + 1;
            $arg      = self::significantToken( $tokens, $argIndex - 1, 1 );
            $line     = $token[ 2 ];

            if( is_array( $arg ) && $arg[ 0 ] === T_CONSTANT_ENCAPSED_STRING ) {
                // it is a literal, but make sure it is the whole argument and
                // not the start of a concatenation - "'a' . $b" cannot be a key
                $after = self::significantToken( $tokens, self::significantIndex( $tokens, $argIndex - 1, 1 ), 1 );

                if( $after === ',' || $after === ')' ) {
                    $this->keys[ self::unquote( $arg[ 1 ] ) ][] = [ 'file' => $file, 'line' => $line ];
                    continue;
                }
            }

            $this->dynamic[] = [ 'file' => $file, 'line' => $line, 'function' => $token[ 1 ] ];
        }
    }

    /**
     * The next/previous token, skipping whitespace and comments.
     *
     * @param   array   $tokens
     * @param   int     $from
     * @param   int     $dir    1 forwards, -1 backwards
     *
     * @return array|string|null
     */
    private static function significantToken( array $tokens, int $from, int $dir ): array|string|null
    {
        $i = self::significantIndex( $tokens, $from, $dir );

        return $i === null ? null : $tokens[ $i ];
    }

    /**
     * Index of the next/previous token, skipping whitespace and comments.
     *
     * @param   array   $tokens
     * @param   int     $from
     * @param   int     $dir
     *
     * @return int|null
     */
    private static function significantIndex( array $tokens, int $from, int $dir ): ?int
    {
        $skip = [ T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ];

        for( $i = $from + $dir; isset( $tokens[ $i ] ); $i += $dir ) {
            if( is_array( $tokens[ $i ] ) && in_array( $tokens[ $i ][ 0 ], $skip, true ) ) {
                continue;
            }

            return $i;
        }

        return null;
    }

    /**
     * Turn a PHP string literal into its value.
     *
     * @param   string  $literal
     *
     * @return string
     */
    private static function unquote( string $literal ): string
    {
        $quote = $literal[ 0 ];
        $body  = substr( $literal, 1, -1 );

        if( $quote === "'" ) {
            return str_replace( [ '\\\\', "\\'" ], [ '\\', "'" ], $body );
        }

        // double quoted: no interpolation is possible here because the
        // tokeniser only gives us T_CONSTANT_ENCAPSED_STRING for literals
        return stripcslashes( $body );
    }
}
