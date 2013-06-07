
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

