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

namespace Entities;

use D2EM, Datetime;

use Entities\{
    ApiKey              as ApiKeyEntity,
    Contact             as ContactEntity,
    Customer            as CustomerEntity,
    CustomerToUser      as CustomerToUserEntity,
    User2FA             as User2FAEntity,
    UserRememberToken  as UserRememberTokenEntity,
    User                as UserEntity,
    UserLoginHistory    as UserLoginHistoryEntity,
    UserPreference      as UserPreferenceEntity
};

use Doctrine\Common\Collections\Collection;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Authenticatable;

use Doctrine\Common\Collections\ArrayCollection;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

use IXP\Events\Auth\ForgotPassword as ForgotPasswordEvent;

use IXP\Utils\Doctrine2\WithPreferences as Doctrine2_WithPreferences;

use PragmaRX\Google2FALaravel\Support\Authenticator as GoogleAuthenticator;

use Psy\Util\Json;


/**
 * Entities\User
 */
class User implements Authenticatable, CanResetPasswordContract
{
    use Doctrine2_WithPreferences;
    use CanResetPassword;

    const AUTH_PUBLIC    = 0;
    const AUTH_CUSTUSER  = 1;
    const AUTH_CUSTADMIN = 2;
    const AUTH_SUPERUSER = 3;

    public static $PRIVILEGES = array(
        User::AUTH_CUSTUSER  => 'CUSTUSER',
        User::AUTH_CUSTADMIN => 'CUSTADMIN',
        User::AUTH_SUPERUSER => 'SUPERUSER',
    );

    public static $PRIVILEGES_ALL = array(
        User::AUTH_PUBLIC    => 'PUBLIC',
        User::AUTH_CUSTUSER  => 'CUSTUSER',
        User::AUTH_CUSTADMIN => 'CUSTADMIN',
        User::AUTH_SUPERUSER => 'SUPERUSER',
    );

    public static $PRIVILEGES_TEXT = array(
        User::AUTH_CUSTUSER  => 'Customer User',
        User::AUTH_CUSTADMIN => 'Customer Administrator',
        User::AUTH_SUPERUSER => 'Superuser',
    );

    public static $PRIVILEGES_TEXT_ALL = array(
        User::AUTH_PUBLIC    => 'Public / Non-User',
        User::AUTH_CUSTUSER  => 'Customer User',
        User::AUTH_CUSTADMIN => 'Customer Administrator',
        User::AUTH_SUPERUSER => 'Superuser',
    );


    public static $PRIVILEGES_TEXT_SHORT = array(
        User::AUTH_CUSTUSER  => 'Cust User',
        User::AUTH_CUSTADMIN => 'Cust Admin',
        User::AUTH_SUPERUSER => 'Superuser',
    );

    public static $PRIVILEGES_TEXT_VSHORT = array(
        User::AUTH_CUSTUSER  => 'CU',
        User::AUTH_CUSTADMIN => 'CA',
        User::AUTH_SUPERUSER => 'SU',
    );

    public static $PRIVILEGES_TEXT_NONSUPERUSER = array(
        User::AUTH_CUSTUSER  => 'Customer User',
        User::AUTH_CUSTADMIN => 'Customer Administrator',
    );

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $username
     */
    public $username;

    /**
     * @var string $password
     */
    protected $password;

    /**
     * @var string $email
     */
    public $email;

    /**
     * @var string $authorisedMobile
     */
    protected $authorisedMobile;

    /**
     * @var integer $uid
     */
    protected $uid;

    /**
     * @var integer $privs
     */
    protected $privs;

    /**
     * @var boolean $disabled
     */
    protected $disabled;

    /**
     * @var DateTime $lastupdated
     */
    protected $lastupdated;

    /**
     * @var integer $lastupdatedby
     */
    protected $lastupdatedby;

    /**
     * @var string $creator
     */
    protected $creator;

    /**
     * @var DateTime $created
     */
    protected $created;

    /**
     * @var integer $id
     */
    protected $id;


    /**
     * @var integer $peeringdb_id
     */
    private $peeringdb_id;


    /**
     * @var Json
     */
    private $extra_attributes = [];


    /**
     * @var Collection
     */
    protected $LastLogins;

    /**
     * @var Collection
     */
    private $ApiKeys;

    /**
     * @var ArrayCollection
     */
    protected $Preferences;

    /**
     * @var CustomerEntity
     */
    protected $Customer;

    /**
     * @var CustomerEntity
     */
    protected $Customers;

    /**
     * @var ContactEntity
     */
    protected $Contact;


    /**
     * @var UserEntity
     */
    protected $Children;

    /**
     * @var User2FAEntity
     */
    protected $User2FA;

    /**
     * @var UserRememberTokenEntity
     */
    protected $UserRememberToken;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Preferences          = new ArrayCollection();
        $this->Customers            = new ArrayCollection();
        $this->ApiKeys              = new ArrayCollection();
        $this->UserRememberToken   = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set authorisedMobile
     *
     * @param string $authorisedMobile
     * @return User
     */
    public function setAuthorisedMobile($authorisedMobile)
    {
        $this->authorisedMobile = $authorisedMobile;

        return $this;
    }

    /**
     * Get authorisedMobile
     *
     * @return string
     */
    public function getAuthorisedMobile()
    {
        return $this->authorisedMobile;
    }

    /**
     * Set uid
     *
     * @param integer $uid
     * @return User
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set privs
     *
     * @param integer $privs
     * @return User
     */
    public function setPrivs( $privs )
    {
        $this->privs = $privs;

        return $this;
    }

    /**
     * Get User privilege from the User table
     *
     * @return integer
     */
    public function getUserPrivs()
    {
        return $this->privs;
    }

    /**
     * Get privilege from the table CustomerToUser
     *
     * @return integer
     */
    public function getPrivs()
    {
        $listC2u = D2EM::getRepository( CustomerToUserEntity::class )->findBy( [ 'customer' => $this->getCustomer(), 'user' => $this->getId() ] );

        return isset( $listC2u[0] ) ? $listC2u[0]->getPrivs() : null;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return User
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set lastupdated
     *
     * @param DateTime $lastupdated
     * @return User
     */
    public function setLastupdated($lastupdated)
    {
        $this->lastupdated = $lastupdated;

        return $this;
    }

    /**
     * Get lastupdated
     *
     * @return \DateTime
     */
    public function getLastupdated()
    {
        return $this->lastupdated;
    }

    /**
     * Set lastupdatedby
     *
     * @param integer $lastupdatedby
     * @return User
     */
    public function setLastupdatedby($lastupdatedby)
    {
        $this->lastupdatedby = $lastupdatedby;

        return $this;
    }

    /**
     * Get lastupdatedby
     *
     * @return integer
     */
    public function getLastupdatedby()
    {
        return $this->lastupdatedby;
    }




    /**
     * Get PeeringDB ID
     *
     * @return int
     */
    public function getPeeringDbId(): ?int
    {
        return $this->peeringdb_id;
    }


    /**
     * Set PeeringDB ID
     *
     * @param int $id
     * @return User
     */
    public function setPeeringDbId( ?int $id )
    {
        $this->peeringdb_id = $id;

        return $this;
    }



    /**
     * Get Extra attributes
     *
     * @return Json
     */
    public function getExtraAttributes()
    {
        return $this->extra_attributes;
    }


    /**
     * Set extra attributes
     *
     * @param Json $extra_attributes
     * @return User
     */
    public function setExtraAttributes( $extra_attributes )
    {
        $this->extra_attributes = $extra_attributes;

        return $this;
    }



    /**
     * Get Customer
     *
     * @return \Entities\Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * Set creator
     *
     * @param string $creator
     * @return User
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return string
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the current customer to user entity - if one exists.
     *
     * @return CustomerToUserEntity|null
     */
    public function getCurrentCustomerToUser(): ?CustomerToUserEntity
    {
        if( !$this->getCustomer() ) {
            return null;
        }

        $c2u = D2EM::getRepository( CustomerToUserEntity::class )->findBy( [ 'customer' => $this->getCustomer(), 'user' => $this->getId() ] );
        return isset( $c2u[0] ) ? $c2u[0] : null;
    }




    /**
     * Add Preferences
     *
     * @param UserPreferenceEntity $preferences
     * @return User
     */
    public function addPreference(UserPreferenceEntity $preferences)
    {
        $this->Preferences[] = $preferences;

        return $this;
    }

    /**
     * Remove Preferences
     *
     * @param UserPreferenceEntity $preferences
     */
    public function removePreference(UserPreferenceEntity $preferences)
    {
        $this->Preferences->removeElement($preferences);
    }

    /**
     * Get Preferences
     *
     * @return ArrayCollection
     */
    public function getPreferences()
    {
        return $this->Preferences;
    }

    /**
     * Set Customer
     *
     * @param CustomerEntity $customer
     * @return User
     */
    public function setCustomer(CustomerEntity $customer = null)
    {
        $this->Customer = $customer;

        return $this;
    }



    /**
     * Add Customer
     *
     * @param CustomerEntity $customer
     * @return User
     */
    public function addCustomer(CustomerEntity $customer)
    {
        $this->Customers[] = $customer;

        return $this;
    }

    /**
     * Remove Customer
     *
     * @param CustomerToUserEntity $customer
     */
    public function removeCustomer(CustomerToUserEntity $customer)
    {
        $this->Customers->removeElement($customer);
    }

    /**
     * Get Customers
     *
     * @return Collection|Customer[]
     */
    public function getCustomers(){
        $custs = [];
        foreach( $this->Customers as $c2u ){
            $custs[] = $c2u->getCustomer();
        }

        return $custs;
    }

    /**
     * Get Customers
     *
     * @return Collection|Customer[]
     */
    public function getActiveCustomers()
    {
        $custs = [];
        foreach( $this->Customers as $c2u ){
            $c = $c2u->getCustomer();/** @var $c CustomerEntity */
            if( $c->isActive() ){
                $custs[] = $c2u->getCustomer();
            }
        }

        return $custs;
    }


    /**
     * @return Collection|CustomerToUser[]
     */
    public function getCustomers2User() {
        return $this->Customers;
    }


    /**
     * Set Children
     *
     * @param UserEntity $children
     * @return User
     */
    public function setChildren(UserEntity $children = null)
    {
        $this->Children = $children;

        return $this;
    }

    /**
     * Get Children
     *
     * @return UserEntity
     */
    public function getChildren()
    {
        return $this->Children;
    }


    /**
     * Get Formatted Name - utility function required by OSS library
     *
     * @return string
     */
    public function getFormattedName()
    {
        return $this->getUsername();
    }


    /**
     * Add Children
     *
     * @param UserEntity $children
     * @return User
     */
    public function addChildren(UserEntity $children)
    {
        $this->Children[] = $children;

        return $this;
    }

    /**
     * Remove Children
     *
     * @param UserEntity $children
     */
    public function removeChildren(UserEntity $children)
    {
        $this->Children->removeElement($children);
    }

    /**
     * Add LastLogins
     *
     * @param UserLoginHistoryEntity $lastLogins
     * @return User
     */
    public function addLastLogin(UserLoginHistoryEntity $lastLogins)
    {
        $this->LastLogins[] = $lastLogins;

        return $this;
    }

    /**
     * Remove LastLogins
     *
     * @param UserLoginHistoryEntity $lastLogins
     */
    public function removeLastLogin(UserLoginHistoryEntity $lastLogins)
    {
        $this->LastLogins->removeElement($lastLogins);
    }

    /**
     * Get LastLogins
     *
     * @return Collection
     */
    public function getLastLogins()
    {
        return $this->LastLogins;
    }


    /**
     * Add ApiKeys
     *
     * @param ApiKeyEntity $apiKeys
     * @return User
     */
    public function addApiKey(ApiKeyEntity $apiKeys)
    {
        $this->ApiKeys[] = $apiKeys;

        return $this;
    }

    /**
     * Remove ApiKeys
     *
     * @param ApiKeyEntity $apiKeys
     */
    public function removeApiKey(ApiKeyEntity $apiKeys)
    {
        $this->ApiKeys->removeElement($apiKeys);
    }

    /**
     * Get ApiKeys
     *
     * @return Collection
     */
    public function getApiKeys()
    {
        return $this->ApiKeys;
    }


    /**
     * Is the user of the named type?
     * @return bool
     */
    public function isCustUser(): bool {
        return $this->getPrivs() == self::AUTH_CUSTUSER;
    }

    /**
     * Is the user of the named type?
     * @return bool
     */
    public function isCustAdmin(): bool {
        return $this->getPrivs() == self::AUTH_CUSTADMIN;
    }

    /**
     * Is the user of the named type?
     * @return bool
     */
    public function isSuperUser(): bool {
        return $this->getPrivs() == self::AUTH_SUPERUSER;
    }


    /**
     * Set Contact
     *
     * @param ContactEntity $contact
     * @return User
     */
    public function setContact( ContactEntity $contact)
    {
        $this->Contact = $contact;

        return $this;
    }

    /**
     * Get Contact
     *
     * @return ContactEntity
     */
    public function getContact()
    {
        return $this->Contact;
    }

    /**
     * Set Password Security
     *
     * @param User2FAEntity $user2fa
     * @return User
     */
    public function setUser2FA( ?User2FAEntity $user2fa )
    {
        $this->User2FA = $user2fa;

        return $this;
    }

    /**
     * Get Password Security
     *
     * @return User2FAEntity
     */
    public function getUser2FA()
    {
        return $this->User2FA;
    }


    /**
     * Does 2fa need to be enforced for this user?
     *
     * @return bool
     */
    public function is2faEnforced()
    {
        if( !config('google2fa.enabled') ) {
            return false;
        }

        return $this->getPrivs() >= config( "google2fa.ixpm_2fa_enforce_for_users" )
            && ( !$this->getUser2FA() || !$this->getUser2FA()->enabled() );
    }

    /**
     * Is 2FA enabled for this user
     *
     * @return bool
     */
    public function is2faEnabled()
    {
        if( !config('google2fa.enabled') ) {
            return false;
        }

        return $this->getUser2FA() && $this->getUser2FA()->enabled();
    }

    /**
     * Check if the user is required to authenticate with 2FA for the current session
     *
     * @return bool
     */
    public function is2faAuthRequiredForSession()
    {
        if( !config('google2fa.enabled') ) {
            return false;
        }

        if( !$this->getUser2FA() || !$this->getUser2FA()->enabled() ) {

            // If the user does not have 2fa configured or enabled but it is required, then return true:
            if( $this->is2faEnforced() ) {
                return true;
            }

            return false;
        }

        $authenticator = new GoogleAuthenticator( request() );

        if( $authenticator->isAuthenticated() ) {
            return false;
        }

        return true;
    }

    /**
     * Add Remember token
     *
     * @param UserRememberTokenEntity $UserRememberToken
     * @return User
     */
    public function addUserRememberToken( UserRememberTokenEntity $UserRememberToken )
    {
        $this->UserRememberToken[] = $UserRememberToken;

        return $this;
    }

    /**
     * Remove Remember token
     *
     * @param UserRememberTokenEntity $UserRememberToken
     */
    public function removeRememberTokens( UserRememberTokenEntity $UserRememberToken )
    {
        $this->UserRememberToken->removeElement( $UserRememberToken );
    }

    /**
     * Get Remember tokens
     *
     * @return Collection
     */
    public function getUserRememberTokens()
    {
        return $this->UserRememberToken;
    }

    /**
     * Is this a user of an associate member?
     *
     * @return bool
     */
    public function isAssociate(): bool
    {
        return $this->getCustomer()->isTypeAssociate();
    }



    /***************************************************************************
     | LARAVEL 5 USER PROVIDER INTERFACE METHODS
     ***************************************************************************/

    /**
     * Get the unique identifier for the user.
     *
     * Required as we implement `\Illuminate\Auth\UserInterface`
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    /**
     * Get the unique identifier name for the user.
     *
     * Required as we implement `\Illuminate\Auth\UserInterface`
     *
     * @return mixed
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }


    /**
     * Get the password for the user.
     *
     * Required as we implement `\Illuminate\Auth\UserInterface`
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->getPassword();
    }

    /**
     * Get the "remember me" token value.
     *
     * @return string
     */
    public function getRememberToken()
    {
        // We have overridden Laravel's remember token fucntionality and do not rely on this.
        // However, some Laravel functionality if triggered on this returning a non-false value
        // to execute certain functionality. As such, we'll just return something random:
        return Str::random(60);
    }

    /**
     * Set the "remember me" token value.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // We have overridden Laravel's remember token fucntionality and do not rely on this.
        return;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'token';
    }


    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        event( new ForgotPasswordEvent( $token, $this ) );
    }

    public function getEmailForPasswordReset(){
        return $this->getUsername();
    }

    /***************************************************************************
     | END LARAVEL 5 USER PROVIDER INTERFACE METHODS
     ***************************************************************************/


    /**
     * Allow direct access to some properties.
     *
     * Because we use Laravel Doctrine, some Laravel packages will fail as they expect to
     * be able to access object properties in the same mannor as Eloquent.
     *
     * We use this to work around those issues.
     *
     * @param string $name
     * @return mixed
     */
    public function __get( string $name )
    {
        switch( $name ) {

            // google2fa Laravel bridge looking for 2fa secret
            case 'secret':
                return $this->getUser2FA() ? $this->getUser2FA()->getSecret() : null;
                break;
        }

        return null;
    }
}
