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
 * Generate a HTML img tag for an Mrtg image.
 * 
 */
function smarty_function_genMrtgImgUrlTag( $params, &$smarty )
{
    if( isset( $params['varname'] ) )
        $varname = $params['varname'];
    else
        $varname = 'mrtgStats';
                        
    if( isset( $params['shortname'] ) )
        $url .= "/shortname/{$params['shortname']}";

    if( isset( $params['period'] ) )
        $url .= "/period/{$params['period']}";
    else
        $url .= "/period/day";

    if( isset( $params['category'] ) )
        $url .= "/category/{$params['category']}";
    else
        $url .= "/category/bits";

    if( isset( $params['monitorindex'] ) )
        $url .= "/monitorindex/{$params['monitorindex']}";
    else
        $url .= "/monitorindex/aggregate";

    return '<img width="500" height="135" border="0" src="' . $url . '" />';
}

?>