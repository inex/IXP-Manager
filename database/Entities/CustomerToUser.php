<?php

namespace Entities;

/**
 * CustomerToUser
 */
class CustomerToUser
{
    /**
     * @var int
     */
    private $privs;

    /**
     * @var \DateTime
     */
    private $created_at;


    /**
     * @var \Json
     */
    private $extra_attributes = [
        'created_by'  => []
    ];

    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $last_login_date;

    /**
     * @var string
     */
    private $last_login_from;

    /**
     * @var string
     */
    private $last_login_via;

    /**
     * @var \Entities\Customer
     */
    private $customer;

    /**
     * @var \Entities\User
     */
    private $user;

    /**
     * @var \Entities\UserLoginHistory
     */
    private $userLoginHistory;



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
     * Get User
     *
     * @return \Entities\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get User
     *
     * @return \Entities\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Get User history
     *
     * @return \Entities\UserLoginHistory
     */
    public function getUserLoginHistory()
    {
        return $this->userLoginHistory;
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
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Get last login date
     *
     * @return \DateTime
     */
    public function getLastLoginDate()
    {
        return $this->last_login_date;
    }

    /**
     * Get last login from
     *
     * @return string
     */
    public function getLastLoginFrom()
    {
        return $this->last_login_from;
    }

    /**
     * Get last login via
     *
     * @return string
     */
    public function getLastLoginVia()
    {
        return $this->last_login_via;
    }

    /**
     * Get Extra attributes
     *
     * @return \Json
     */
    public function getExtraAttributes()
    {
        return $this->extra_attributes;
    }

    /**
     * Set user
     *
     * @param \Entities\User
     * @return CustomerToUser
     */
    public function setUser( $user )
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set customer
     *
     * @param \Entities\Customer
     * @return CustomerToUser
     */
    public function setCustomer( $cust )
    {
        $this->customer = $cust;

        return $this;
    }

    /**
     * Set user login history
     *
     * @param \Entities\UserLoginHistory
     * @return CustomerToUser
     */
    public function setUserLoginHistory( $userLoginHistory )
    {
        $this->userLoginHistory = $userLoginHistory;

        return $this;
    }

    /**
     * Set privs
     *
     * @param integer $privs
     * @return CustomerToUser
     */
    public function setPrivs( $privs )
    {
        $this->privs = $privs;

        return $this;
    }

    /**
     * Set extra attributes
     *
     * @param array $extra_attributes
     * @return CustomerToUser
     */
    public function setExtraAttributes( $extra_attributes )
    {
        $this->extra_attributes = $extra_attributes;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $created_at
     * @return CustomerToUser
     */
    public function setCreatedAt( $created_at )
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Set last login at
     *
     * @param \DateTime $last_login_date
     * @return CustomerToUser
     */
    public function setLastLoginAt( $last_login_date )
    {
        $this->last_login_date = $last_login_date;

        return $this;
    }

    /**
     * Set last login at
     *
     * @param string $last_login_from
     * @return CustomerToUser
     */
    public function setLastLoginFrom( $last_login_from )
    {
        $this->last_login_from = $last_login_from;

        return $this;
    }

    /**
     * Set last login via
     *
     * @param string $last_login_via
     * @return CustomerToUser
     */
    public function setLastLoginVia( $last_login_via )
    {
        $this->last_login_via = $last_login_via;

        return $this;
    }


}
