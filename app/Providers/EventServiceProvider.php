<?php

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

namespace IXP\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        'IXP\Listeners\Customer\Note\EmailOnChange',
    ];



    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'IXP\Events\Customer\BillingDetailsChanged' => [
            'IXP\Listeners\Customer\BillingDetailsChanged'
        ],

        'IXP\Events\Layer2Address\Added' => [
            'IXP\Listeners\Layer2Address\Changed',
        ],
        'IXP\Events\Layer2Address\Deleted' => [
            'IXP\Listeners\Layer2Address\Changed',
        ],

        'IXP\Events\User\Welcome' => [
            'IXP\Listeners\User\EmailWelcome'
        ],

        'IXP\Events\User\C2uWelcome' => [
            'IXP\Listeners\User\C2uEmailWelcome'
        ],

        'IXP\Events\Auth\ForgotUsername' => [
            'IXP\Listeners\Auth\ForgotUsername'
        ],

        'IXP\Events\Auth\ForgotPassword' => [
            'IXP\Listeners\Auth\ForgotPassword'
        ],

        'IXP\Events\Auth\PasswordReset' => [
            'IXP\Listeners\Auth\PasswordReset'
        ],


    ];




    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }

}
