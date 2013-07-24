<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\UserPreference
 */
class UserPreference
{
    /**
     * @var string $attribute
     */
    protected $attribute;

    /**
     * @var string $op
     */
    protected $op;

    /**
     * @var string $value
     */
    protected $value;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\User
     */
    protected $User;


    /**
     * Set attribute
     *
     * @param string $attribute
     * @return UserPreference
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    
        return $this;
    }

    /**
     * Get attribute
     *
     * @return string 
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set op
     *
     * @param string $op
     * @return UserPreference
     */
    public function setOp($op)
    {
        $this->op = $op;
    
        return $this;
    }

    /**
     * Get op
     *
     * @return string 
     */
    public function getOp()
    {
        return $this->op;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return UserPreference
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
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
     * Set User
     *
     * @param Entities\User $user
     * @return UserPreference
     */
    public function setUser(\Entities\User $user = null)
    {
        $this->User = $user;
    
        return $this;
    }

    /**
     * Get User
     *
     * @return Entities\User 
     */
    public function getUser()
    {
        return $this->User;
    }
    /**
     * @var integer $ix
     */
    protected $ix;


    /**
     * Set ix
     *
     * @param integer $ix
     * @return UserPreference
     */
    public function setIx($ix)
    {
        $this->ix = $ix;
    
        return $this;
    }

    /**
     * Get ix
     *
     * @return integer 
     */
    public function getIx()
    {
        return $this->ix;
    }
    /**
     * @var integer $expire
     */
    protected $expire;


    /**
     * Set expire
     *
     * @param integer $expire
     * @return UserPreference
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
    
        return $this;
    }

    /**
     * Get expire
     *
     * @return integer 
     */
    public function getExpire()
    {
        return $this->expire;
    }
}