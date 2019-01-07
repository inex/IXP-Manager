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

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use Entities\User as UserEntity;

use D2EM;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    const API_KEY_CUSTUSER  = 'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC7';
    const API_KEY_CUSTADMIN = 'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC8';
    const API_KEY_SUPERUSER = 'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC9';


    /**
     * Utility function to get a customer user
     * @param string $username
     * @return UserEntity
     */
    public function getCustUser( string $username = 'imcustuser' ): UserEntity {
        /** @var UserEntity $u */
        $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => $username ] );
        return $u;
    }

    /**
     * Utility function to get a customer admin user
     * @param string $username
     * @return UserEntity
     */
    public function getCustAdminUser( string $username = 'imcustadmin' ): UserEntity {
        /** @var UserEntity $u */
        $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => $username ] );
        return $u;
    }

    /**
     * Utility function to get a superuser
     * @param string $username
     * @return UserEntity
     */
    public function getSuperUser( string $username = 'travis' ): UserEntity {
        /** @var UserEntity $u */
        $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => $username ] );
        return $u;
    }


}
