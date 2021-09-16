<?php

namespace IXP\Utils\Foil\Extensions;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Utils\View\Alert\Container as AlertContainer;

use Foil\Contracts\ExtensionInterface;

use Illuminate\Support\Facades\Auth;

use PragmaRX\Google2FALaravel\Support\Authenticator as GoogleAuthenticator;
use function count;

/**
 * Grapher -> Renderer view extensions
 *
 * See: http://www.foilphp.it/docs/EXTENDING/CUSTOM-EXTENSIONS.html
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IXP implements ExtensionInterface
{

    /**
     * @var
     */
    private $args;

    public function setup( array $args = [] )
    {
        $this->args = $args;
    }

    public function provideFilters(): array
    {
       return [];
    }

    public function provideFunctions(): array
    {
        return [
            'alerts'                 => [ AlertContainer::class, 'html' ],
            'as112UiActive'          => [ $this, 'as112UiActive' ],
            'asNumber'               => [ $this, 'asNumber' ],
            'google2faAuthenticator' => [ $this, 'google2faAuthenticator' ],
            'logoManagementEnabled'  => [ $this, 'logoManagementEnabled' ],
            'maxFileUploadSize'      => [ $this, 'maxFileUploadSize' ],
            'nagiosHostname'         => [ $this, 'nagiosHostname' ],
            'nakedUrl'               => [ $this, 'nakedUrl' ],
            'resellerMode'           => [ $this, 'resellerMode' ],
            'scaleBits'              => [ $this, 'scaleBits' ],
            'scaleBytes'             => [ $this, 'scaleBytes' ],
            'scaleSpeed'             => [ $this, 'scaleSpeed' ],
            'scaleFilesize'          => [ $this, 'scaleFilesize' ],
            'softwrap'               => [ $this, 'softwrap' ],
            'whoisPrefix'            => [ $this, 'whoisPrefix' ],
        ];
    }

    /**
     * Max file upload size
     *
     * Inspired by: http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
     *
     * @return string
     */
    public function maxFileUploadSize(): string
    {
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
     *
     * @return string            Scaled / formatted number / type.
     */
    private function scale( float $v, string $format, int $decs = 3, int $returnType = 0 ): string
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
     *
     * @return string
     */
    public function scaleBits( float $v, int $decs = 3 ): string
    {
        return $this->scale( $v, 'bits', $decs );
    }

    /**
     * See scale above
     * @param float $v
     * @param int $decs
     *
     * @return string
     */
    public function scaleSpeed( float $v, int $decs = 0 ): string
    {
        return $this->scale( $v, 'speed', $decs );
    }


    /**
     * See scale above
     * @param float $v
     * @param int $decs
     *
     * @return string
     */
    public function scaleBytes( float $v, int $decs = 3 ): string
    {
        return $this->scale( $v, 'bytes', $decs );
    }

    /**
     * Scale a size in bytes in human style filesize
     *
     * @param int  $bytes          The value to scale
     * @return string            Scaled / formatted number / type.
     */
    public function scaleFilesize( int $bytes ): string
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
     *
    * @return string            Scaled / formatted number / type.
    */
    public function softwrap( array $data, int $perline, string $elementSeparator, string $lineEnding, int $indent = 0, int $pad = 0 ): string
    {
        if( !( $cnt = count( $data ) ) ) {
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
     *
     * @return string
     */
    public function nagiosHostname( string $abbreviatedName, int $asn, int $protocol, int $vlanid, int $vliid ): string
    {
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
     *
     * @return string
     */
    public function asNumber( $asn, $addAs = true ): string
    {
        if( Auth::check() && $asn ) {
            return '<a href="#ixpm-asnumber-' . $asn . '" onClick="ixpAsnumber( ' . $asn . ' ); return false;">' . ( $addAs ? 'AS' : '' ) . $asn . '</a>';
        }

        return ( $addAs ? 'AS' : '' ) . $asn;
    }

    /**
     * Replaces an IP prefix with some JS magic to invoke a bootbox.
     *
     * @param $prefix
     * @param bool $subnet
     *
     * @return string
     */
    public function whoisPrefix( $prefix, $subnet = true ): string
    {
        if( Auth::check() && $prefix ) {
            return '<a href="#ixpm-prefix-whois-' . md5($prefix) . '" onClick="ixpWhoisPrefix( \'' . $prefix . '\' , \'' . $subnet . '\'); return false;">' . $prefix . '</a>';
        }

        return $prefix;
    }

    /**
     * Takes a URL with https://xxx/ and returns xxx
     *
     * @param  string $url      The URL
     *
     * @return string
     */
    public function nakedUrl( string $url ): string
    {
        $url = preg_replace( '/^http[s]?:\/\//', '', $url );
        return preg_replace( '/\/$/', '', $url );
    }

    /**
     * Get Google Authenticator
     *
     * @return GoogleAuthenticator
     */
    public function google2faAuthenticator(): GoogleAuthenticator
    {
        return new GoogleAuthenticator( request() );
    }
}
