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

/*
|--------------------------------------------------------------------------
| Web Routes using Eloquent2Frontend
|--------------------------------------------------------------------------
|
|
*/
IXP\Http\Controllers\ApiKeyController::routes();
IXP\Http\Controllers\CabinetController::routes();
IXP\Http\Controllers\ConsoleServer\ConsoleServerController::routes();
IXP\Http\Controllers\ConsoleServer\ConsoleServerConnectionController::routes();
IXP\Http\Controllers\Contact\ContactController::routes();
IXP\Http\Controllers\Contact\ContactGroupController::routes();
IXP\Http\Controllers\CustKitController::routes();
IXP\Http\Controllers\Customer\CustomerTagController::routes();
IXP\Http\Controllers\InfrastructureController::routes();
IXP\Http\Controllers\IrrdbConfigController::routes();
IXP\Http\Controllers\Layer2AddressController::routes();
IXP\Http\Controllers\LocationController::routes();
IXP\Http\Controllers\LoginHistoryController::routes();
IXP\Http\Controllers\MacAddressController::routes();
IXP\Http\Controllers\NetworkInfoController::routes();
//IXP\Http\Controllers\RipeAtlas\MeasurementController::routes();
//IXP\Http\Controllers\RipeAtlas\ResultController::routes();
//IXP\Http\Controllers\RipeAtlas\RunController::routes();
//IXP\Http\Controllers\RipeAtlas\ProbesController::routes();
IXP\Http\Controllers\Switches\SwitchController::routes();
IXP\Http\Controllers\Switches\SwitchPortController::routes();
IXP\Http\Controllers\User\UserRememberTokenController::routes();
IXP\Http\Controllers\VendorController::routes();
IXP\Http\Controllers\VlanController::routes();