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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

var oss_cookie_options = {
    'expires': 90,
    'path': "/"
};
        
var oss_prefs = {
    'iLength'         : 10,
    'cdrs_period'     : 'd',
    'cdrs_settings'   : 'hidden',
    'cdrs_dataTables' : true
};
                        
var cprefs = $.jsonCookie( 'oss_prefs' );
                        
if( cprefs != null )
    oss_prefs = cprefs;
                            
                            
/**
 * This is default function and it's called than page is loaded.
 */

$( 'document' ).ready( function(){

    // Activate the Bootstrap menubar
	$('.dropdown-toggle').dropdown();
});

