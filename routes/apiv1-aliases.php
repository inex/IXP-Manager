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
Route::get('apiv1/member-list/list/version/{version}', 'IXP\Http\Controllers\Api\V4\MemberExportController@ixf' );


