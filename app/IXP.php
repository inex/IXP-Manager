<?php

namespace IXP;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * IXP - top level class for constants, etc.
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class IXP
{
    public const int IPv4 = 4;
    public const int IPv6 = 6;

    public static array $PROTOCOLS = [
        self::IPv4 => 'IPv4',
        self::IPv6 => 'IPv6',
    ];

    public static function protocol( int $p ): string {
        return self::$PROTOCOLS[$p] ?? '';
    }



    /**
     * Scale function
     *
     * This function will scale a number to (for example for traffic
     * measured in bits/second) to Kbps, Mbps, Gbps or Tbps; or data.
     * measured in bytes to KB, MB, GB or TB.
     *
     * Valid string formats ($strFormats) and what they return are:
     *    bytes               => Bytes, KBytes, MBytes, GBytes, TBytes
     *    pkts / errs / discs => pps, Kpps, Mpps, Gpps, Tpps
     *    bits / *            => bits, Kbits, Mbits, Gbits, Tbits
     *
     * Valid return types ($format) are:
     *    0 => fully formatted and scaled value. E.g.  12,354.235 Tbits
     *    1 => scaled value without string. E.g. 12,354.235
     *    2 => just the string. E.g. Tbits
     *
     * @param float  $v          The value to scale
     * @param string $format     The format to sue (as above: bytes / pkts / errs / etc )
     * @param int    $decs       Number of decimals after the decimal point. Defaults to 3.
     * @param int    $returnType Type of string to return. Valid values are listed above. Defaults to 0.
     *
     * @return string            Scaled / formatted number / type.
     */
    public static function scale( float $v, string $format, int $decs = 3, int $returnType = 0 ): string
    {
        if( $format === 'bytes' ) {
            $formats = [
                'Bytes', 'KBytes', 'MBytes', 'GBytes', 'TBytes'
            ];
        } else if( in_array( $format, [ 'pkts', 'errs', 'discs', 'bcasts' ] ) ) {
            $formats = [
                'pps', 'Kpps', 'Mpps', 'Gpps', 'Tpps'
            ];
        } else if( $format === 'speed' ) {
            $formats = [
                'Mbps', 'Gbps', 'Tbps'
            ];
        } else {
            $formats = [
                'bits', 'Kbits', 'Mbits', 'Gbits', 'Tbits'
            ];
        }

        $num_formats = count( $formats );
        for( $i = 0; $i < $num_formats; $i++ ) {
            $format = $i > 4 ? $formats[ 4 ] : $formats[ $i ];
            if( ( $v / 1000.0 < 1.0 ) || ( $num_formats === $i + 1 ) ) {
                if( $returnType === 0 ) {
                    return number_format( $v, $decs ) . ' ' . $format;
                }

                if( $returnType === 1 ) {
                    return number_format( $v, $decs );
                }

                return $format;
            }

            $v /= 1000.0;
        }

        return (string)$v;
    }

    /**
     * See scale above
     * @param float $v
     * @param int $decs
     *
     * @return string
     */
    public static function scaleBits( float $v, int $decs = 3 ): string
    {
        return self::scale( $v, 'bits', $decs );
    }

    /**
     * See scale above
     * @param float $v
     * @param int $decs
     *
     * @return string
     */
    public static function scaleSpeed( float $v, int $decs = 0 ): string
    {
        return self::scale( $v, 'speed', $decs );
    }


    /**
     * See scale above
     * @param float $v
     * @param int $decs
     *
     * @return string
     */
    public static function scaleBytes( float $v, int $decs = 3 ): string
    {
        return self::scale( $v, 'bytes', $decs );
    }

    /**
     * Scale a size in bytes in human style filesize
     *
     * @param int  $bytes          The value to scale
     *
     * @return string Scaled / formatted number / type.
     */
    public static function scaleFilesize( int $bytes ): string
    {
        if( $bytes >= 1073741824 ) {
            return number_format( $bytes / 1073741824, 2 ) . ' GB';
        }

        if( $bytes >= 1048576 ) {
            return number_format( $bytes / 1048576, 2 ) . ' MB';
        }

        if( $bytes >= 1024 ) {
            return number_format( $bytes / 1024, 2 ) . ' KB';
        }

        return $bytes . ' bytes';
    }


}