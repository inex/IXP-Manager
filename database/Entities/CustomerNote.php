<?php

namespace Entities;

/**
 * CustomerNote
 */
class CustomerNote
{
    /**
     * @var boolean
     */
    protected $private;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $note;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $updated;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Entities\Customer
     */
    protected $Customer;


    /**
     * Set private
     *
     * @param boolean $private
     * @return CustomerNote
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    
        return $this;
    }

    /**
     * Get private
     *
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return CustomerNote
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return CustomerNote
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNoteParsedown()
    {
        return @parseDown( $this->note );
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return CustomerNote
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
     * Set Customer
     *
     * @param Customer $customer
     * @return CustomerNote
     */
    public function setCustomer( Customer $customer )
    {
        $this->Customer = $customer;
    
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
     * Return the main fields of the note as an array
     * @return array
     */
    public function toArray(): array
    {
        return [
            'created'           => $this->getCreated(),
            'updated'           => $this->getUpdated(),
            'id'                => $this->getId(),
            'note'              => $this->getNote(),
            'noteParsedown'     => @parsedown( $this->getNote() ),
            'private'           => $this->getPrivate(),
            'title'             => $this->getTitle()
        ];
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return CustomerNote
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
