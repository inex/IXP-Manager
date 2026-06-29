<?php

namespace Tests;

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

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use IXP\Models\User;

/**
 * TestCase
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public const API_KEY_CUSTUSER  = 'ixpm_v7zO4TmaX3Ft_bo4NB55lMRFPSqoNDCv34xDkwg8d2WK01bMzif';
    public const API_KEY_CUSTADMIN = 'ixpm_GcMPPjWig7w2_G6yoQYvkQSLhfkOuhwEYoULcsM512Vjt2sQ532';
    public const API_KEY_SUPERUSER = 'ixpm_iqLw1OF50aPU_XX3U8cGvlRiaf7YLX8a41uSJVqBbRAAl0LfKVo';

    public function __construct( $name = null, array $data = [], $dataName = '' )
    {
        date_default_timezone_set('Europe/Dublin');

        if( !defined('LARAVEL_START') ) {
            define( 'LARAVEL_START', microtime(true ) );
        }

        parent::__construct( $name, $data, $dataName );
    }

    /**
     * Utility function to get a customer user
     *
     * @param string $username
     *
     * @return User
     */
    public function getCustUser( string $username = 'imcustuser' ): User
    {
        return User::whereUsername( $username )->get()->first();
    }

    /**
     * Utility function to get a customer admin user
     *
     * @param string $username
     *
     * @return User
     */
    public function getCustAdminUser( string $username = 'imcustadmin' ): User
    {
        return User::whereUsername( $username )->get()->first();
    }

    /**
     * Utility function to get a superuser
     *
     * @param string $username
     *
     * @return User
     */
    public function getSuperUser( string $username = 'travis' ): User
    {
        return User::whereUsername( $username )->get()->first();
    }
}
