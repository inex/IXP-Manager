<?php
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes - Legacy Aliases
|--------------------------------------------------------------------------
|
| Aliases to legacy routes from IXP Manager <v4
|
*/


// IXF Member List Export
Route::get('apiv1/member-list/list',                   'IXP\Http\Controllers\Api\V4\MemberExportController@ixf' );
Route::get('apiv1/member-list/list/version/{version}', 'IXP\Http\Controllers\Api\V4\MemberExportController@ixf' );


Route::get( 'static/support', function() {
   return redirect( 'public-content/support' );
});

// Mainly used by www.inex.ie - remove pretty quickly:
Route::get( 'public/ajax-overall-stats-by-month', 'IXP\Http\Controllers\Api\V4\StatisticsController@overallByMonth' );

