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
 * Replaces an AS  Number with some JS magic to invoke a colorbox.
 *
 * @param string $value The AS number
 */
function smarty_modifier_asnumber( $value )
{
    return '<a href="#cb' . $value . '" '
        . 'onClick=\'$.colorbox({href:"https://apps.db.ripe.net/search/query.html?searchtext=as'
            . $value . '", iframe: true, width: "75%", height: "75%"});'
            . 'return false;\'>' . $value . '</a>';
}

