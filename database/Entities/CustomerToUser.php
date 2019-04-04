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
        'created_by'  => 'array'
    ];

    /**
     * @var int
     */
    private $id;

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
     * @param Json $extra_attributes
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

}
