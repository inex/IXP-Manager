<?php


/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * Repeats a character / string for the size of a given string.
 *
 * This function is useful for generating 'underlines' of dashes
 * for example.
 *
 * Example usage:
 *     {$string|underline}
 *
 * @param string $value The string that will be underlined
 * @param string $char The underlining character (defaults to '-' )
 */
function smarty_modifier_underline( $value, $char = '-' )
{
    $u = '';

    for( $i = 0; $i < strlen( $value ); $i++ )
        $u .= $char;

    return "$value\n$u";
}

