<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\User
 */
class User
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
    
    public static $PRIVILEGES_TEXT = array(
        User::AUTH_CUSTUSER  => 'Customer User',
        User::AUTH_CUSTADMIN => 'Customer Superuser',
        User::AUTH_SUPERUSER => 'Superuser'
    );
    
    
    /**
     * @var string $username
     */
    protected $username;

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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $Preferences;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $ChangeLogs;

    /**
     * @var Entities\Customer
     */
    protected $Customer;

    /**
     * @var Entities\User
     */
    protected $Children;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Preferences = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ChangeLogs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param Entities\UserPreference $preferences
     * @return User
     */
    public function addPreference(\Entities\UserPreference $preferences)
    {
        $this->Preferences[] = $preferences;
    
        return $this;
    }

    /**
     * Remove Preferences
     *
     * @param Entities\UserPreference $preferences
     */
    public function removePreference(\Entities\UserPreference $preferences)
    {
        $this->Preferences->removeElement($preferences);
    }

    /**
     * Get Preferences
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPreferences()
    {
        return $this->Preferences;
    }

    /**
     * Add ChangeLogs
     *
     * @param Entities\ChangeLog $changeLogs
     * @return User
     */
    public function addChangeLog(\Entities\ChangeLog $changeLogs)
    {
        $this->ChangeLogs[] = $changeLogs;
    
        return $this;
    }

    /**
     * Remove ChangeLogs
     *
     * @param Entities\ChangeLog $changeLogs
     */
    public function removeChangeLog(\Entities\ChangeLog $changeLogs)
    {
        $this->ChangeLogs->removeElement($changeLogs);
    }

    /**
     * Get ChangeLogs
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getChangeLogs()
    {
        return $this->ChangeLogs;
    }

    /**
     * Set Customer
     *
     * @param Entities\Customer $customer
     * @return User
     */
    public function setCustomer(\Entities\Customer $customer = null)
    {
        $this->Customer = $customer;
    
        return $this;
    }

    /**
     * Get Customer
     *
     * @return Entities\Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * Set Children
     *
     * @param Entities\User $children
     * @return User
     */
    public function setChildren(\Entities\User $children = null)
    {
        $this->Children = $children;
    
        return $this;
    }

    /**
     * Get Children
     *
     * @return Entities\User
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
     * @param Entities\User $children
     * @return User
     */
    public function addChildren(\Entities\User $children)
    {
        $this->Children[] = $children;
    
        return $this;
    }

    /**
     * Remove Children
     *
     * @param Entities\User $children
     */
    public function removeChildren(\Entities\User $children)
    {
        $this->Children->removeElement($children);
    }

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $Meetings;


    /**
     * Add Meetings
     *
     * @param Entities\Meeting $meetings
     * @return User
     */
    public function addMeeting(\Entities\Meeting $meetings)
    {
        $this->Meetings[] = $meetings;
    
        return $this;
    }

    /**
     * Remove Meetings
     *
     * @param Entities\Meeting $meetings
     */
    public function removeMeeting(\Entities\Meeting $meetings)
    {
        $this->Meetings->removeElement($meetings);
    }

    /**
     * Get Meetings
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMeetings()
    {
        return $this->Meetings;
    }
    /**
     * @var \Entities\Contact
     */
    protected $Contact;


    /**
     * Set Contact
     *
     * @param \Entities\Contact $contact
     * @return User
     */
    public function setContact(\Entities\Contact $contact = null)
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
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $LastLogins;


    /**
     * Add LastLogins
     *
     * @param \Entities\UserLoginHistory $lastLogins
     * @return User
     */
    public function addLastLogin(\Entities\UserLoginHistory $lastLogins)
    {
        $this->LastLogins[] = $lastLogins;
    
        return $this;
    }

    /**
     * Remove LastLogins
     *
     * @param \Entities\UserLoginHistory $lastLogins
     */
    public function removeLastLogin(\Entities\UserLoginHistory $lastLogins)
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ApiKeys;


    /**
     * Add ApiKeys
     *
     * @param \Entities\ApiKey $apiKeys
     * @return User
     */
    public function addApiKey(\Entities\ApiKey $apiKeys)
    {
        $this->ApiKeys[] = $apiKeys;
    
        return $this;
    }

    /**
     * Remove ApiKeys
     *
     * @param \Entities\ApiKey $apiKeys
     */
    public function removeApiKey(\Entities\ApiKey $apiKeys)
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
}