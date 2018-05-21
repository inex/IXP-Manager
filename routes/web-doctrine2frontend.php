<?php

/*
|--------------------------------------------------------------------------
| Web Routes using Doctrine2Frontend
|--------------------------------------------------------------------------
|
|
*/

IXP\Http\Controllers\ApiKeyController::routes();
IXP\Http\Controllers\CabinetController::routes();
IXP\Http\Controllers\ConsoleServer\ConsoleServerController::routes();
IXP\Http\Controllers\ConsoleServer\ConsoleServerConnectionController::routes();
IXP\Http\Controllers\ContactsController::routes();
IXP\Http\Controllers\CustKitController::routes();
IXP\Http\Controllers\InfrastructureController::routes();
IXP\Http\Controllers\IrrdbConfigController::routes();
IXP\Http\Controllers\IxpController::routes();
IXP\Http\Controllers\Layer2AddressController::routes();
IXP\Http\Controllers\LocationController::routes();
IXP\Http\Controllers\LoginHistoryController::routes();
IXP\Http\Controllers\MacAddressController::routes();
IXP\Http\Controllers\Switches\SwitchController::routes();
IXP\Http\Controllers\Switches\SwitchPortsController::routes();
IXP\Http\Controllers\VendorController::routes();
IXP\Http\Controllers\VlanController::routes();

// tmp until ZF is consigned to history
Route::get( 'rack/view/id/{id}',   'CabinetController@view' );
Route::get( 'vendor/view/id/{id}', 'VendorController@view'  );

