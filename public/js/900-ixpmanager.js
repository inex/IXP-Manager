
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



/*****************************************************************************/
// Preferences via cookies
/*****************************************************************************/
/*
var tt_cookie_expiry_days = 90;
var tt_prefs = {
	'my_show_notes'       : false,
	'my_show_descs'       : false,
	'my_show_daily_total' : false,
	'my_show_weekly_total': false,
	'chart_settings'      : false
};

cprefs = $.jsonCookie( 'tt_prefs' );
if( cprefs != null )
	tt_prefs = cprefs;

*/