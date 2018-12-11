<?php

namespace Entities;

use Carbon\Carbon;
/**
 * Entities\PeeringManager
 */
class PeeringManager{

    /**
     * @var \DateTime $email_last_sent
     */
    protected $email_last_sent;

    /**
     * @var integer $emails_sent
     */
    protected $emails_sent;

    /**
     * @var boolean $peered
     */
    protected $peered;

    /**
     * @var boolean $rejected
     */
    protected $rejected;

    /**
     * @var string $notes
     */
    protected $notes;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\Customer
     */
    protected $Customer;

    /**
     * @var Entities\Customer
     */
    protected $Peer;


    /**
     * Set email_last_sent
     *
     * @param \DateTime $emailLastSent
     * @return PeeringManager
     */
    public function setEmailLastSent($emailLastSent)
    {
        $this->email_last_sent = $emailLastSent;
    
        return $this;
    }

    /**
     * Get email_last_sent
     *
     * @return \DateTime 
     */
    public function getEmailLastSent()
    {
        return $this->email_last_sent;
    }

    /**
     * Set emails_sent
     *
     * @param integer $emailsSent
     * @return PeeringManager
     */
    public function setEmailsSent($emailsSent)
    {
        $this->emails_sent = $emailsSent;
    
        return $this;
    }

    /**
     * Get emails_sent
     *
     * @return integer 
     */
    public function getEmailsSent()
    {
        return $this->emails_sent;
    }

    /**
     * Set peered
     *
     * @param boolean $peered
     * @return PeeringManager
     */
    public function setPeered($peered)
    {
        $this->peered = $peered;
    
        return $this;
    }

    /**
     * Get peered
     *
     * @return boolean 
     */
    public function getPeered()
    {
        return $this->peered;
    }

    /**
     * Set rejected
     *
     * @param boolean $rejected
     * @return PeeringManager
     */
    public function setRejected($rejected)
    {
        $this->rejected = $rejected;
    
        return $this;
    }

    /**
     * Get rejected
     *
     * @return boolean 
     */
    public function getRejected()
    {
        return $this->rejected;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return PeeringManager
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    
        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
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
     * @param Entities\Customer $customer
     * @return PeeringManager
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
     * Set Peer
     *
     * @param Entities\Customer $peer
     * @return PeeringManager
     */
    public function setPeer(\Entities\Customer $peer = null)
    {
        $this->Peer = $peer;
    
        return $this;
    }

    /**
     * Get Peer
     *
     * @return Entities\Customer 
     */
    public function getPeer()
    {
        return $this->Peer;
    }
    /**
     * @var \DateTime $created
     */
    protected $created;

    /**
     * @var \DateTime $updated
     */
    protected $updated;


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return PeeringManager
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return PeeringManager
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


    /**
     * Convert this object to an array
     *
     * @return array
     */
    public function toArray(): array {
        $a = [
            'id'                => $this->getId(),
            'email_last_sent'   => $this->getEmailLastSent()       ? Carbon::instance( $this->getEmailLastSent()       )->toIso8601String() : null,
            'emails_sent'       => $this->getEmailsSent() ? true : false,
            'peered'            => $this->getPeered() ? true : false,
            'rejected'          => $this->getRejected() ? true : false,
            'notes'             => $this->getNotes(),
            'created'           => $this->getCreated()       ? Carbon::instance( $this->getCreated()       )->toIso8601String() : null,
            'updated'           => $this->getUpdated()       ? Carbon::instance( $this->getUpdated()       )->toIso8601String() : null,
            'custid'            => $this->getCustomer()      ? $this->getCustomer()->getId() : null,
            'peer'              => $this->getPeer()          ? $this->getPeer()->getId() : null,

        ];


        return $a;
    }

}
