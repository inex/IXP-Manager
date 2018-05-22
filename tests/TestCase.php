<?php

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
