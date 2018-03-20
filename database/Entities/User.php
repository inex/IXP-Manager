<?php

namespace Entities;

use Entities\{
    ApiKey              as ApiKeyEntity,
    Contact             as ContactEntity,
    Customer            as CustomerEntity,
    User                as UserEntity,
    UserLoginHistory    as UserLoginHistoryEntity,
    UserPreference      as UserPreferenceEntity
};

use Illuminate\Contracts\Auth\Authenticatable;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entities\User
 */
class User implements Authenticatable
{
    use \OSS_Doctrine2_WithPreferences;

    const AUTH_PUBLIC    = 0;
    const AUTH_CUSTUSER  = 1;
    const AUTH_CUSTADMIN = 2;
    const AUTH_SUPERUSER = 3;

    public static $PRIVILEGES = array(
        User::AUTH_CUSTUSER  => 'CUSTUSER',
        User::AUTH_CUSTADMIN => 'CUSTADMIN',
        User::AUTH_SUPERUSER => 'SUPERUSER'
    );

    public static $PRIVILEGES_ALL = array(
        User::AUTH_PUBLIC    => 'PUBLIC',
        User::AUTH_CUSTUSER  => 'CUSTUSER',
        User::AUTH_CUSTADMIN => 'CUSTADMIN',
        User::AUTH_SUPERUSER => 'SUPERUSER'
    );

    public static $PRIVILEGES_TEXT = array(
        User::AUTH_CUSTUSER  => 'Customer User',
        User::AUTH_CUSTADMIN => 'Customer Administrator',
        User::AUTH_SUPERUSER => 'Superuser'
    );


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
    protected $email;

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
     * @var \DateTime $lastupdated
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
     * @var \DateTime $created
     */
    protected $created;

    /**
     * @var integer $id
     */
    protected $id;


    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $LastLogins;

    /**
     * @var \Doctrine\Common\Collections\Collection
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
     * @var UserEntity
     */
    protected $Children;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Preferences = new ArrayCollection();
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
    public function setPrivs($privs)
    {
        $this->privs = $privs;

        return $this;
    }

    /**
     * Get privs
     *
     * @return integer
     */
    public function getPrivs()
    {
        return $this->privs;
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
     * @param \DateTime $lastupdated
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
     * @return \DateTime
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
     * Get Customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
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
     * @return \Entities\User
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

    protected $Contact;


    /**
     * Set Contact
     *
     * @param ContactEntity $contact
     * @return User
     */
    public function setContact(ContactEntity $contact = null)
    {
        $this->Contact = $contact;

        return $this;
    }

    /**
     * Get Contact
     *
     * @return \Entities\Contact
     */
    public function getContact()
    {
        return $this->Contact;
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
     * @return \Doctrine\Common\Collections\Collection
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
     * @return \Doctrine\Common\Collections\Collection
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
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /***************************************************************************
     | END LARAVEL 5 USER PROVIDER INTERFACE METHODS
     ***************************************************************************/

}
