<?php

namespace Entities;

/**
 * PatchPanelPort
 */
class PatchPanelPort
{
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
return $this->internal_use;
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
}
