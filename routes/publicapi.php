<?php

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| This routes file is for publicly accessible APIs that should not create
| cookies.
|
|
| NB: FIXME: Temporary API interface introduced to solve a specific issue in 2021-01.
| Not guaranteed to be consistent / permanent.
|
|
|
*/

Route::any( 'ping', 'PublicController@ping' )->name('api-v4:ping');
Route::any( 'test', 'PublicController@test' )->name('api-v4:test');


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Statistics
//

// get overall stats by month as a JSON response
Route::get( 'statistics/overall-by-month', 'StatisticsController@overallByMonth' );

Route::get( 'content/members/0/list.json', '\IXP\Http\Controllers\ContentController@simpleMembers' )->name( 'content/members' );
