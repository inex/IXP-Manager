<?php

namespace IXP\Providers;

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

use Illuminate\Auth\Events\{
    Failed,
    Login
};

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use IXP\Listeners\Auth\{
    Google2FALoginSucceeded,
    LoginFailed,
    LoginSuccessful
};

use IXP\Listeners\Customer\Note\EmailOnChange;

use PragmaRX\Google2FALaravel\Events\LoginSucceeded;

use SocialiteProviders\Manager\SocialiteWasCalled;

use IXP\Events\Customer\BillingDetailsChanged;

use IXP\Listeners\Layer2Address\Changed;

use IXP\Events\Layer2Address\{
    Added,
    Deleted
};

use IXP\Events\User\{
    UserCreated,
    UserAddedToCustomer
};

use IXP\Listeners\User\{
    SendNewUserWelcomeEmail,
    SendUserAddedToCustomerWelcomeEmail
};

use IXP\Events\Auth\{
    ForgotUsername,
    ForgotPassword,
    PasswordReset
};
use IXP\Events\RipeAtlas\MeasurementComplete;

/**
 * Event Service Provider
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        EmailOnChange::class,
    ];

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        Login::class => [
            LoginSuccessful::class
        ],

        Failed::class => [
            LoginFailed::class
        ],

        BillingDetailsChanged::class => [
            \IXP\Listeners\Customer\BillingDetailsChanged::class
        ],

        Added::class => [
            Changed::class,
        ],
        Deleted::class => [
            Changed::class,
        ],

        UserCreated::class => [
            SendNewUserWelcomeEmail::class
        ],

        UserAddedToCustomer::class => [
            SendUserAddedToCustomerWelcomeEmail::class
        ],

        ForgotUsername::class => [
            \IXP\Listeners\Auth\ForgotUsername::class
        ],

        ForgotPassword::class => [
            \IXP\Listeners\Auth\ForgotPassword::class
        ],

        PasswordReset::class => [
            \IXP\Listeners\Auth\PasswordReset::class
        ],

        LoginSucceeded::class => [
            Google2FALoginSucceeded::class
        ],

        SocialiteWasCalled::class => [
            'SocialiteProviders\\PeeringDB\\PeeringDBExtendSocialite@handle',
        ],

        MeasurementComplete::class => [
            \IXP\Listeners\RipeAtlas\MeasurementComplete::class
        ],

    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}