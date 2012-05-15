<?php

/*
 * Copyright (C) 2012 Internet Neutral Exchange Association Limited.
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

class INEX_String
{

    /**
     * Generates a random string.
     *
     * @param int     $length     The length of the random string we want to generate. Default: 16
     * @param boolean $lowerCase  If true then lowercase characters will be used. Default: true
     * @param boolean $upperCase  If true then uppercase characters will be used. Default: true
     * @param boolean $numbers    If true then numbers will be used. Default: true
     * @param string  $additional These characters also will be used. Default: ''
     * @param string  $exclude    These characters will be excluded. Default: '1iIl0O'
     * @return string The random string.
     */
    public static function random( $length=16, $lowerCase = true, $upperCase = true, $numbers = true, $additional = '', $exclude = '1iIl0O' )
    {
        if( $length == 0 )
            return '';
    
        $str = '';
        
        if( $lowerCase )
            $str .= 'abcdefghijklmnopqrstuvwxyz';
    
        if( $upperCase )
            $str .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
        if( $numbers )
            $str .= '0123456789';
    
        $str .= $additional;
    
        if( $exclude != '' )
        {
            foreach( str_split( $exclude ) as $excludeChar )
            {
                $str = INEX_String::mb_str_replace( $excludeChar, '', $str );
            }
        }
    
        $repeat = ceil( ( 1 + ( $length / mb_strlen( $str ) ) ) );

        return substr( str_shuffle( str_repeat( $str, $repeat ) ), 1, $length );
    }
    
    
    /**
     * The Unicode version of str_replace().
     *
     * @param string $needle      The string portion to replace in the haystack
     * @param string $replacement The replacement for the string portion
     * @param string $haystack    The haystack
     * @return string
     */
    public static function mb_str_replace( $needle, $replacement, $haystack )
    {
        $needle_len      = mb_strlen( $needle );
        $replacement_len = mb_strlen( $replacement );
        $pos             = mb_strpos( $haystack, $needle );
    
        while( $pos !== false )
        {
            $haystack = mb_substr( $haystack, 0, $pos ) . $replacement . mb_substr( $haystack, $pos + $needle_len );
            $pos = mb_strpos( $haystack, $needle, $pos + $replacement_len );
        }
    
        return $haystack;
    }
    
    
}
