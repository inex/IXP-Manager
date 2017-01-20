<?php

namespace Entities;

/**
 * PatchPanelPort
 */
class PatchPanelPort
{

    /**
     * CONST STATES
     */
    const STATE_AVAILABLE              = 1;
    const STATE_AWAITING_XCONNECT      = 2;
    const STATE_CONNECTED              = 3;
    const STATE_AWAITING_CEASE         = 4;
    const STATE_CEASED                 = 5;
    const STATE_BROKEN                 = 6;
    const STATE_OTHER                  = 999;


    /**
     * Array STATES
     */
    public static $STATES = [
        self::STATE_AVAILABLE         => "Available",
        self::STATE_AWAITING_XCONNECT => "Awaiting Xconnect",
        self::STATE_CONNECTED         => "Connected",
        self::STATE_AWAITING_CEASE    => "Awaiting cease",
        self::STATE_CEASED            => "Ceased",
        self::STATE_BROKEN            => "Broken",
        self::STATE_OTHER             => "Other"
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $state;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var \DateTime
     */
    private $assigned_at;

    /**
     * @var \DateTime
     */
    private $connected_at;

    /**
     * @var \DateTime
     */
    private $cease_requested_at;

    /**
     * @var \DateTime
     */
    private $ceased_at;

    /**
     * @var \DateTime
     */
    private $last_state_change;

    /**
     * @var boolean
     */
    private $internal_use = '0';

    /**
     * @var boolean
     */
    private $chargeable = '0';

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Entities\SwitchPort
     */
    private $switchPort;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $patchPanelPortHistory;

    /**
     * @var \Entities\PatchPanel
     */
    private $patchPanel;

    /**
     * @var \Entities\Customer
     */
    private $customer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->patchPanelPortHistory = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PatchPanelPort
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return PatchPanelPort
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return PatchPanelPort
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
     * Set assignedAt
     *
     * @param \DateTime $assignedAt
     *
     * @return PatchPanelPort
     */
    public function setAssignedAt($assignedAt)
    {
        $this->assigned_at = $assignedAt;
        return $this;
    }

    /**
     * Get assignedAt
     *
     * @return \DateTime
     */
    public function getAssignedAt()
    {
        return $this->assigned_at;
    }

    /**
     * Get assignedAt formated
     *
     * @return \DateTime
     */
    public function getAssignedAtFormated()
    {
        return ($this->getAssignedAt() == null) ? $this->getAssignedAt() : $this->getAssignedAt()->format('Y-m-d');
    }

    /**
     * Set connectedAt
     *
     * @param \DateTime $connectedAt
     *
     * @return PatchPanelPort
     */
    public function setConnectedAt($connectedAt)
    {
        $this->connected_at = $connectedAt;
        return $this;
    }

    /**
     * Get connectedAt
     *
     * @return \DateTime
     */
    public function getConnectedAt()
    {
        return $this->connected_at;
    }

    /**
     * Get connectedAt formated
     *
     * @return \DateTime
     */
    public function getConnectedAtFormated()
    {
        return ($this->getConnectedAt() == null) ? $this->getConnectedAt() : $this->getConnectedAt()->format('Y-m-d');
    }

    /**
     * Set ceaseRequestedAt
     *
     * @param \DateTime $ceaseRequestedAt
     *
     * @return PatchPanelPort
     */
    public function setCeaseRequestedAt($ceaseRequestedAt)
    {
        $this->cease_requested_at = $ceaseRequestedAt;
        return $this;
    }

    /**
     * Get ceaseRequestedAt
     *
     * @return \DateTime
     */
    public function getCeaseRequestedAt()
    {
        return $this->cease_requested_at;
    }

    /**
     * Get ceaseRequestedAt formated
     *
     * @return \DateTime
     */
    public function getCeaseRequestedAtFormated()
    {
        return ($this->getCeaseRequestedAt() == null) ? $this->getCeaseRequestedAt() : $this->getCeaseRequestedAt()->format('Y-m-d');
    }

    /**
     * Set ceasedAt
     *
     * @param \DateTime $ceasedAt
     *
     * @return PatchPanelPort
     */
    public function setCeasedAt($ceasedAt)
    {
        $this->ceased_at = $ceasedAt;
        return $this;
    }

    /**
     * Get ceasedAt
     *
     * @return \DateTime
     */
    public function getCeasedAt()
    {
        return $this->ceased_at;
    }

    /**
     * Get ceasedAt formated
     *
     * @return \DateTime
     */
    public function getCeasedAtFormated()
    {
        return ($this->getCeasedAt() == null) ? $this->getCeasedAt() : $this->getCeasedAt()->format('Y-m-d');
    }

    /**
     * Set lastStateChange
     *
     * @param \DateTime $lastStateChange
     *
     * @return PatchPanelPort
     */
    public function setLastStateChange($lastStateChange)
    {
        $this->last_state_change = $lastStateChange;
        return $this;
    }

    /**
     * Get lastStateChange
     *
     * @return \DateTime
     */
    public function getLastStateChange()
    {
        return $this->last_state_change;
    }

    /**
     * Get lastStateChange
     *
     * @return \DateTime
     */
    public function getLastStateChangeFormated()
    {
        return ($this->getLastStateChange() == null) ? $this->getLastStateChange() : $this->getLastStateChange()->format('Y-m-d');
    }

    /**
     * Set internalUse
     *
     * @param boolean $internalUse
     *
     * @return PatchPanelPort
     */
    public function setInternalUse($internalUse)
    {
        $this->internal_use = $internalUse;
        return $this;
    }

    /**
     * Get internalUse
     *
     * @return boolean
     */
    public function getInternalUse()
    {
        return  $this->internal_use;
    }

    /**
     * Get internalUse
     *
     * @return boolean
     */
    public function getInternalUseInt()
    {
        return $this->getInternalUse() ?  1 :  0;
    }

    /**
     * Get internalUse
     *
     * @return boolean
     */
    public function getInternalUseText()
    {
        return $this->getInternalUse() ?  'Yes' :  'No';
    }

    /**
     * Set chargeable
     *
     * @param boolean $chargeable
     *
     * @return PatchPanelPort
     */
    public function setChargeable($chargeable)
    {
        $this->chargeable = $chargeable;
        return $this;
    }

    /**
     * Get chargeable
     *
     * @return boolean
     */
    public function getChargeable()
    {
        return $this->chargeable;

    }

    /**
     * Get chargeable
     *
     * @return boolean
     */
    public function getChargeableInt()
    {
        return $this->getChargeable() ? 1 : 0;
    }

    /**
     * Get chargeable
     *
     * @return boolean
     */
    public function getChargeableText()
    {
        return $this->getChargeable() ? 'Yes' : 'No';
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
     * Set switchPort
     *
     * @param \Entities\SwitchPort $switchPort
     *
     * @return PatchPanelPort
     */
    public function setSwitchPort(\Entities\SwitchPort $switchPort = null)
    {
        $this->switchPort = $switchPort;
        return $this;
    }

    /**
     * Get switchPort
     *
     * @return \Entities\SwitchPort
     */
    public function getSwitchPort()
    {
        return $this->switchPort;
    }

    /**
     * Get switchPort ID
     *
     * @return \Entities\SwitchPort
     */
    public function getSwitchPortId()
    {
        return ($this->switchPort != null) ?  $this->getSwitchPort()->getId() : null;
    }

    /**
     * Get switchPort Name
     *
     * @return \Entities\SwitchPort
     */
    public function getSwitchPortName()
    {
        return ($this->switchPort != null) ?  $this->getSwitchPort()->getName() : null;
    }

    /**
     * Add patchPanelPortHistory
     *
     * @param \Entities\PatchPanelPortHistory $patchPanelPortHistory
     *
     * @return PatchPanelPort
     */
    public function addPatchPanelPortHistory(\Entities\PatchPanelPortHistory $patchPanelPortHistory)
    {
        $this->patchPanelPortHistory[] = $patchPanelPortHistory;
        return $this;
    }

    /**
     * Remove patchPanelPortHistory
     *
     * @param \Entities\PatchPanelPortHistory $patchPanelPortHistory
     */
    public function removePatchPanelPortHistory(\Entities\PatchPanelPortHistory $patchPanelPortHistory)
    {
        $this->patchPanelPortHistory->removeElement($patchPanelPortHistory);
    }

    /**
     * Get patchPanelPortHistory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPatchPanelPortHistory()
    {
        return $this->patchPanelPortHistory;
    }

    /**
     * Set patchPanel
     *
     * @param \Entities\PatchPanel $patchPanel
     *
     * @return PatchPanelPort
     */
    public function setPatchPanel(\Entities\PatchPanel $patchPanel = null)
    {
        $this->patchPanel = $patchPanel;
        return $this;
    }

    /**
     * Get patchPanel
     *
     * @return \Entities\PatchPanel
     */
    public function getPatchPanel()
    {
        return $this->patchPanel;
    }

    /**
     * Set customer
     *
     * @param \Entities\Customer $customer
     *
     * @return PatchPanelPort
     */
    public function setCustomer(\Entities\Customer $customer = null)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Get customer
     *
     * @return \Entities\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Get customer ID
     *
     * @return \Entities\Customer
     */
    public function getCustomerId()
    {
        return ($this->getCustomer() != null) ? $this->getCustomer()->getId() : null ;
    }

    /**
     * Get customer ID
     *
     * @return \Entities\Customer
     */
    public function getSwitchId()
    {
        return ($this->getSwitchPort() != null) ? $this->getSwitchPort()->getSwitcher()->getId() : null ;
    }

    /**
     * Get customer Name
     *
     * @return \Entities\Customer
     */
    public function getCustomerName()
    {
        return ($this->getCustomer() != null) ? $this->getCustomer()->getName() : null ;
    }
}
