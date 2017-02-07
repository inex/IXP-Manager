<?php

namespace Entities;

/**
 * PatchPanelPortHistory
 */
class PatchPanelPortHistory
{
/**
 * @var integer
 */
private $number;

/**
 * @var integer
 */
private $state;

    /**
     * @var string
     */
    private $colo_circuit_ref;

    /**
     * @var string
     */
    private $ticket_ref;

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
 * @var boolean
 */
private $internal_use = '0';

/**
 * @var boolean
 */
private $chargeable = '0';

/**
 * @var string
 */
private $customer;

/**
 * @var string
 */
private $switchport;

/**
 * @var integer
 */
private $id;

/**
 * @var \Entities\PatchPanelPort
 */
private $patchPanelPort;


/**
 * Set number
 *
 * @param integer $number
 *
 * @return PatchPanelPortHistory
 */
public function setNumber($number)
{
$this->number = $number;

return $this;
}

/**
 * Get number
 *
 * @return integer
 */
public function getNumber()
{
return $this->number;
}

/**
 * Set state
 *
 * @param integer $state
 *
 * @return PatchPanelPortHistory
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
     * Set colo_circuit_ref
     *
     * @param string $colo_circuit_ref
     *
     * @return PatchPanelPort
     */
    public function setColoCircuitRef(string $colo_circuit_ref): PatchPanelPort
    {
        $this->colo_circuit_ref = $colo_circuit_ref;
        return $this;
    }

    /**
     * Get colo_circuit_ref
     *
     * @return string
     */
    public function getColoCircuitRef(): string
    {
        return $this->colo_circuit_ref;
    }

    /**
     * Set ticket_ref
     *
     * @param string $ticket_ref
     *
     * @return PatchPanelPort
     */
    public function setTicketRef(string $ticket_ref): PatchPanelPort
    {
        $this->ticket_ref = $ticket_ref;
        return $this;
    }

    /**
     * Get ticket_ref
     *
     * @return string
     */
    public function getTicketRef(): string
    {
        return $this->ticket_ref;
    }


/**
 * Set notes
 *
 * @param string $notes
 *
 * @return PatchPanelPortHistory
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
 * @return PatchPanelPortHistory
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
 * Set connectedAt
 *
 * @param \DateTime $connectedAt
 *
 * @return PatchPanelPortHistory
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
 * Set ceaseRequestedAt
 *
 * @param \DateTime $ceaseRequestedAt
 *
 * @return PatchPanelPortHistory
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
 * Set ceasedAt
 *
 * @param \DateTime $ceasedAt
 *
 * @return PatchPanelPortHistory
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
 * Set internalUse
 *
 * @param boolean $internalUse
 *
 * @return PatchPanelPortHistory
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
return $this->internal_use;
}

/**
 * Set chargeable
 *
 * @param boolean $chargeable
 *
 * @return PatchPanelPortHistory
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
 * Set customer
 *
 * @param string $customer
 *
 * @return PatchPanelPortHistory
 */
public function setCustomer($customer)
{
$this->customer = $customer;

return $this;
}

/**
 * Get customer
 *
 * @return string
 */
public function getCustomer()
{
return $this->customer;
}

/**
 * Set switchport
 *
 * @param string $switchport
 *
 * @return PatchPanelPortHistory
 */
public function setSwitchport($switchport)
{
$this->switchport = $switchport;

return $this;
}

/**
 * Get switchport
 *
 * @return string
 */
public function getSwitchport()
{
return $this->switchport;
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
 * Set patchPanelPort
 *
 * @param \Entities\PatchPanelPort $patchPanelPort
 *
 * @return PatchPanelPortHistory
 */
public function setPatchPanelPort(\Entities\PatchPanelPort $patchPanelPort = null)
{
$this->patchPanelPort = $patchPanelPort;

return $this;
}

/**
 * Get patchPanelPort
 *
 * @return \Entities\PatchPanelPort
 */
public function getPatchPanelPort()
{
return $this->patchPanelPort;
}
/**
 * @var \Doctrine\Common\Collections\Collection
 */
private $duplexSlavePorts;

/**
 * @var \Entities\PatchPanelPortHistory
 */
private $duplexMasterPort;

/**
 * Constructor
 */
public function __construct()
{
$this->duplexSlavePorts = new \Doctrine\Common\Collections\ArrayCollection();
}

/**
 * Add duplexSlavePort
 *
 * @param \Entities\PatchPanelPortHistory $duplexSlavePort
 *
 * @return PatchPanelPortHistory
 */
public function addDuplexSlavePort(\Entities\PatchPanelPortHistory $duplexSlavePort)
{
$this->duplexSlavePorts[] = $duplexSlavePort;

return $this;
}

/**
 * Remove duplexSlavePort
 *
 * @param \Entities\PatchPanelPortHistory $duplexSlavePort
 */
public function removeDuplexSlavePort(\Entities\PatchPanelPortHistory $duplexSlavePort)
{
$this->duplexSlavePorts->removeElement($duplexSlavePort);
}

/**
 * Get duplexSlavePorts
 *
 * @return \Doctrine\Common\Collections\Collection
 */
public function getDuplexSlavePorts()
{
return $this->duplexSlavePorts;
}

/**
 * Set duplexMasterPort
 *
 * @param \Entities\PatchPanelPortHistory $duplexMasterPort
 *
 * @return PatchPanelPortHistory
 */
public function setDuplexMasterPort(\Entities\PatchPanelPortHistory $duplexMasterPort = null)
{
$this->duplexMasterPort = $duplexMasterPort;

return $this;
}

/**
 * Get duplexMasterPort
 *
 * @return \Entities\PatchPanelPortHistory
 */
public function getDuplexMasterPort()
{
return $this->duplexMasterPort;
}
}
