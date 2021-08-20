<?php

namespace IXP\Utils\Foil\Extensions;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Foil\Contracts\ExtensionInterface;

/**
 * Bird -> Renderer view extensions
 *
 * See: http://www.foilphp.it/docs/EXTENDING/CUSTOM-EXTENSIONS.html
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Bird implements ExtensionInterface
{

    /**
     * @var
     */
    private $args;

    public function setup( array $args = [] )
    {
        $this->args = $args;
    }

    public function provideFilters()
    {
        return [];
    }

    public function provideFunctions()
    {
        return [
            'bird' => [$this, 'getObject']
        ];
    }


    public function getObject(): Bird
    {
        return $this;
    }

    /**
     * Convert array of prefixes into less specifics
     * @param array $prefixes
     * @param int $proto
     * @param int $max
     *
     * @return array
     */
    public function prefixExactToLessSpecific( array $prefixes, int $proto = 6, int $max = 48 ): array
    {
        foreach( $prefixes as $i => $p ) {
            [ $net, $mask ] = explode( '/', $p );

            if( $mask > $max ) {
                // subnet is too small already so we do not allow it
                unset( $prefixes[$i] );
                continue;
            }

            if( $mask != $max ) {
                $prefixes[ $i ] = $net . '/' . $mask . '{' . $mask . ',' . $max . '}';
            }
        }

        return $prefixes;
    }


    // FIXME: need to find a place for this that allows end users to customise it
    public static $BGPLCS = [
        ':1101:1'  => [ 'PREFIX LENGTH TOO LONG', 'danger' ],
        ':1101:2'  => [ 'PREFIX LENGTH TOO SHORT', 'danger' ],
        ':1101:3'  => [ 'BOGON', 'danger' ],
        ':1101:4'  => [ 'BOGON ASN', 'danger' ],
        ':1101:5'  => [ 'AS PATH TOO LONG', 'danger' ],
        ':1101:6'  => [ 'AS PATH TOO SHORT', 'danger' ],
        ':1101:7'  => [ 'FIRST AS NOT PEER AS', 'danger' ],
        ':1101:8'  => [ 'NEXT HOP NOT PEER IP', 'danger' ],
        ':1101:9'  => [ 'IRRDB PREFIX FILTERED', 'danger' ],
        ':1101:10' => [ 'IRRDB ORIGIN AS FILTERED', 'danger' ],
        ':1101:11' => [ 'PREFIX NOT IN ORIGIN AS', 'danger' ],
        ':1101:12' => [ 'RPKI UNKNOWN', 'danger' ],
        ':1101:13' => [ 'RPKI INVALID', 'danger' ],
        ':1101:14' => [ 'TRANSIT FREE ASN', 'danger' ],
        ':1101:15' => [ 'TOO MANY COMMUNITIES', 'danger' ],

        ':1000:1'  => [ 'RPKI VALID', 'success' ],
        ':1000:2'  => [ 'RPKI UNKNOWN', 'info' ],
        ':1000:3'  => [ 'RPKI NOT CHECKED', 'warning' ],

        ':1001:0'  => [ 'IRRDB INVALID', 'info' ],
        ':1001:1'  => [ 'IRRDB VALID', 'success' ],
        ':1001:2'  => [ 'IRRDB NOT CHECKED', 'warning' ],
        ':1001:3'  => [ 'IRRDB MORE SPECIFIC', 'info' ],

        ':1001:1000'  => [ 'IRRDB FILTERED LOOSE', 'info' ],
        ':1001:1001'  => [ 'IRRDB FILTERED STRICT', 'info' ],
        ':1001:1002'  => [ 'IRRDB PREFIX EMPTY', 'warning' ],

        ':1001:1100'  => [ 'FROM IX ROUTESERVER', 'info' ],

        ':1001:1200'  => [ 'SAME AS NEXT HOP', 'info' ],
    ];

    // FIXME: need to find a place for this that allows end users to customise it
    public static $BGPLCS_REGEX = [
        ':101:\d+'  => [ 'PREPEND TO PEERAS - ONCE', 'info' ],
        ':102:\d+'  => [ 'PREPEND TO PEERAS - TWICE', 'info' ],
        ':103:\d+'  => [ 'PREPEND TO PEERAS - THREE TIMES', 'info' ],
    ];

    /**
     * Get information on a BGP large community used for filtering / info by IXP Manager
     */
    public function translateBgpFilteringLargeCommunity( string $lc ): ?array
    {
        foreach( self::$BGPLCS as $k => $v ) {
            if( $k === $lc ) {
                return $v;
            }
        }

        foreach( self::$BGPLCS_REGEX as $re => $v ) {
            if( preg_match( '/^' . $re . '$/', $lc ) ) {
                return $v;
            }
        }

        return null;
    }
}