<?php namespace IXP\Utils\Foil\Extensions;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Utils\View\Alert\Container as AlertContainer;

use Illuminate\Support\Facades\Auth;

/**
 * Grapher -> Renderer view extensions
 *
 * See: http://www.foilphp.it/docs/EXTENDING/CUSTOM-EXTENSIONS.html
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP implements ExtensionInterface {

    private $args;

    public function setup(array $args = []) {
        $this->args = $args;
    }

    public function provideFilters(): array {
       return [];
    }

    public function provideFunctions(): array {
        return [
            'alerts'                => [ AlertContainer::class, 'html' ],
            'as112UiActive'         => [ $this, 'as112UiActive' ],
            'asNumber'              => [ $this, 'asNumber' ],
            'logoManagementEnabled' => [ $this, 'logoManagementEnabled' ],
            'maxFileUploadSize'     => [ $this, 'maxFileUploadSize' ],
            'multiIXP'              => [ $this, 'multiIXP' ],
            'nagiosHostname'        => [ $this, 'nagiosHostname' ],
            'resellerMode'          => [ $this, 'resellerMode' ],
            'scaleBits'             => [ $this, 'scaleBits' ],
            'scaleBytes'            => [ $this, 'scaleBytes' ],
            'softwrap'              => [ $this, 'softwrap' ],
        ];
    }





    /**
     * Max file upload size
     *
     * Inspired by: http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
     */
    public function maxFileUploadSize(): string {
        static $max_size = null;

        $parseSize = function( $size ) {
            $unit = preg_replace( '/[^bkmgtpezy]/i', '', $size ); // Remove the non-unit characters from the size.
            $size = preg_replace( '/[^0-9\.]/',      '', $size ); // Remove the non-numeric characters from the size.

            if( $unit ) {
                // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
                return round( $size * ( 1024 ** stripos( 'bkmgtpezy', $unit[0] ) ) );
            }

            return round( $size );
        };

        if( $max_size === null ) {
            $max_size = $parseSize( ini_get('post_max_size') );

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = $parseSize( ini_get('upload_max_filesize') );
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }

        return $this->scale( $max_size, 'bytes' );
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
     * @return string            Scaled / formatted number / type.
     */
    private function scale( float $v, string $format, int $decs = 3, int $returnType = 0 ): string {
        if( $format === 'bytes' ) {
            $formats = [
                'Bytes', 'KBytes', 'MBytes', 'GBytes', 'TBytes'
            ];
        } else if( \in_array( $format, [ 'pkts', 'errs', 'discs', 'bcasts' ] ) ) {
            $formats = [
                'pps', 'Kpps', 'Mpps', 'Gpps', 'Tpps'
            ];
        } else {
            $formats = [
                'bits', 'Kbits', 'Mbits', 'Gbits', 'Tbits'
            ];
        }

        $num_formats = \count( $formats );
        for( $i = 0; $i < $num_formats; $i++ ) {
            if( ( $v / 1000.0 < 1.0 ) || ( $num_formats === $i + 1 ) ) {
                if( $returnType === 0 ) {
                    return number_format( $v, $decs ) . ' ' . $formats[ $i ];
                }

                if( $returnType === 1 ) {
                    return number_format( $v, $decs );
                }

                return $formats[ $i ];
            }

            $v /= 1000.0;
        }

        return (string)$v;
    }

    /**
     * See scale above
     * @param float $v
     * @param int $decs
     * @return string
     */
    public function scaleBits( float $v, int $decs = 3 ): string {
        return $this->scale( $v, 'bits', $decs );
    }

    /**
     * See scale above
     * @param float $v
     * @param int $decs
     * @return string
     */
    public function scaleBytes( float $v, int $decs = 3 ): string {
        return $this->scale( $v, 'bytes', $decs );
    }

    /**
    * Soft wrap
    *
    * Print an array of data separated by $elementSeparator within the same line and only
    * print $perline elements per line terminated each line with $lineEnding (and an implicit \n).
    *
    * Set $indent to indent //subsequent// lines (i.e. not the first)
    *
    * @param array  $data
    * @param int    $perline
    * @param string $elementSeparator
    * @param string $lineEnding
    * @param int    $indent
    * @param int    $pad
    * @return string            Scaled / formatted number / type.
    */
    public function softwrap( array $data, int $perline, string $elementSeparator, string $lineEnding, int $indent = 0, int $pad = 0 ): string {
        if( !( $cnt = \count( $data ) ) ) {
            return '';
        }

        $itrn = 0;
        $str  = '';

        foreach( $data as $d ) {
            if( $itrn === $cnt ) {
                break;
            }

            if( $itrn === 0 && $cnt > 1 && $perline === 1 ) {
                $str .= $d . $lineEnding . "\n" . str_repeat(' ', $indent);
            } else if( ($itrn+1) !== $cnt && ($itrn+1) % $perline !== 0 ) {
                $str .= str_pad( $d . $elementSeparator, $pad );
            } else if( $itrn > 0 && ($itrn+1) !== $cnt && ($itrn+1) % $perline == 0 ) {
                $str .= $d . $lineEnding . "\n" . str_repeat( ' ', $indent );
            } else {
                $str .= $d;
            }

            $itrn++;
        }

        return $str;
    }


    /**
     * Get a consistent hostname for a given member VLAN interface
     *
     * @param string $abbreviatedName Customer's abbreviated name
     * @param int    $asn             Customer's ASN
     * @param int    $protocol        Protocol
     * @param int    $vlanid          VLAN ID
     * @param int    $vliid           VLAN Interface ID
     * @return string
     */
    public function nagiosHostname( string $abbreviatedName, int $asn, int $protocol, int $vlanid, int $vliid ): string {
        return preg_replace( '/[^a-zA-Z0-9]/', '-', strtolower( $abbreviatedName ) ) . '-as' . $asn . '-ipv' . $protocol . '-vlanid' . $vlanid . '-vliid' . $vliid;
    }



    /**
     * Checks if reseller mode is enabled.
     *
     * To enable reseller mode set the env variable IXP_RESELLER_ENABLED
     *
     * @see http://docs.ixpmanager.org/features/reseller/
     *
     * @return bool
     */
    public function resellerMode(): bool
    {
        return (bool)config( 'ixp.reseller.enabled', false );
    }

    /**
     * Checks if logo management is enabled
     *
     * To enable logos in the UI set IXP_FE_FRONTEND_DISABLED_LOGO=false in .env
     *
     * @return bool
     */
    public function logoManagementEnabled(): bool
    {
        return !(bool)config( 'ixp_fe.frontend.disabled.logo' );
    }

    /**
     * Checks if multi IXP mode is enabled.
     *
     * To enable multi IXP mode set the env variable IXP_MULTIIXP_ENABLED
     *
     * NB: this functionality is deprecated in IXP Manager v4.0 and will be
     * removed piecemeal.
     *
     * @see https://github.com/inex/IXP-Manager/wiki/Multi-IXP-Functionality
     *
     * @return bool
     */
    public function multiIXP(): bool
    {
        return (bool)config( 'ixp.multiixp.enabled', false );
    }

    /**
     * Checks if as112 is activated in the UI.
     *
     * To disable as112 in the UI set the env variable IXP_AS112_UI_ACTIVE
     *
     * @see http://docs.ixpmanager.org/features/as112/
     *
     * @return bool
     */
    public function as112UiActive(): bool
    {
        return (bool)config( 'ixp.as112.ui_active', false );
    }

    /**
     * Replaces an AS  Number with some JS magic to invoke a bootbox.
     *
     * @param  int    $asn      The AS number
     * @param  bool   $addAs    Do we need to add AS?
     * @return string
     */
    public function asNumber( $asn, $addAs = true )
    {
        if( Auth::check() && $asn ) {
            return '<a href="#ixpm-asnumber-' . $asn . '" onClick="ixpAsnumber( ' . $asn . ' ); return false;">' . ( $addAs ? 'AS' : '' ) . $asn . '</a>';
        }

        return ( $addAs ? 'AS' : '' ) . $asn;
    }

}
