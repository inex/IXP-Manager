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

    /**
     * @return void
     */
    #[\Override]
    /**
     * @return void
     */
    public function setup( array $args = [] )
    {
        $this->args = $args;
    }

    /**
     * @return array
     *
     * @psalm-return array<never, never>
     */
    #[\Override]
    /**
     * @return array
     *
     * @psalm-return array<never, never>
     */
    public function provideFilters()
    {
        return [];
    }

    /**
     * @return (static|string)[][]
     *
     * @psalm-return array{bird: list{static, 'getObject'}}
     */
    #[\Override]
    /**
     * @return (static|string)[][]
     *
     * @psalm-return array{bird: list{static, 'getObject'}}
     */
    public function provideFunctions()
    {
        return [
            'bird' => [$this, 'getObject']
        ];
    }


    public function getObject(): static
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

    # https://www.iana.org/assignments/bgp-well-known-communities/bgp-well-known-communities.xhtml
    public static $BGPCS = [
        '65535:0' => ['GRACEFUL_SHUTDOWN', 'warning'],
        '65535:1' => ['ACCEPT_OWN', 'info'],
        '65535:9' => ['STANDBY_PE', 'info'],
        '65535:666' => ['BLACKHOLE', 'warning'],
        '65535:65281' => ['NO_EXPORT', 'info'],
        '65535:65282' => ['NO_ADVERTISE', 'info'],
        '65535:65283' => ['NO_EXPORT_SUBCONFED', 'info'],
        '65535:65284' => ['NOPEER', 'info'],
    ];

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
     * Get information on a BGP well-known community
     */
    public function translateBgpCommunity( string $c ): ?array
    {
        return self::$BGPCS[$c] ?? null;
    }

    /**
     * Get information on a BGP large community used for filtering / info by IXP Manager
     */
    public function translateBgpFilteringLargeCommunity( string $lc ): ?array
    {
        if( isset( self::$BGPLCS[$lc] ) ) {
            return self::$BGPLCS[$lc];
        }

        foreach( self::$BGPLCS_REGEX as $re => $v ) {
            if( preg_match( '/^' . $re . '$/', $lc ) ) {
                return $v;
            }
        }

        return null;
    }
}