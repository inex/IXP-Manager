<?php

namespace Entities;

/**
 * PatchPanelPortHistoryFile
 */
class PatchPanelPortHistoryFile
{
/**
 * @var string
 */
private $name;

/**
 * @var string
 */
private $type;

/**
 * @var \DateTime
 */
private $uploaded_at;

/**
 * @var string
 */
private $uploaded_by;

/**
 * @var integer
 */
private $size;

/**
 * @var string
 */
private $storage_location;

/**
 * @var integer
 */
private $id;

/**
 * @var \Doctrine\Common\Collections\Collection
 */
private $patchPanelPortHistory;

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
 * @return PatchPanelPortHistoryFile
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
 * Set type
 *
 * @param string $type
 *
 * @return PatchPanelPortHistoryFile
 */
public function setType($type)
{
$this->type = $type;

return $this;
}

/**
 * Get type
 *
 * @return string
 */
public function getType()
{
return $this->type;
}

/**
 * Set uploadedAt
 *
 * @param \DateTime $uploadedAt
 *
 * @return PatchPanelPortHistoryFile
 */
public function setUploadedAt($uploadedAt)
{
$this->uploaded_at = $uploadedAt;

return $this;
}

/**
 * Get uploadedAt
 *
 * @return \DateTime
 */
public function getUploadedAt()
{
return $this->uploaded_at;
}

/**
 * Set uploadedBy
 *
 * @param string $uploadedBy
 *
 * @return PatchPanelPortHistoryFile
 */
public function setUploadedBy($uploadedBy)
{
$this->uploaded_by = $uploadedBy;

return $this;
}

/**
 * Get uploadedBy
 *
 * @return string
 */
public function getUploadedBy()
{
return $this->uploaded_by;
}

/**
 * Set size
 *
 * @param integer $size
 *
 * @return PatchPanelPortHistoryFile
 */
public function setSize($size)
{
$this->size = $size;

return $this;
}

/**
 * Get size
 *
 * @return integer
 */
public function getSize()
{
return $this->size;
}

/**
 * Set storageLocation
 *
 * @param string $storageLocation
 *
 * @return PatchPanelPortHistoryFile
 */
public function setStorageLocation($storageLocation)
{
$this->storage_location = $storageLocation;

return $this;
}

/**
 * Get storageLocation
 *
 * @return string
 */
public function getStorageLocation()
{
return $this->storage_location;
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
 * Add patchPanelPortHistory
 *
 * @param \Entities\PatchPanelPortHistory $patchPanelPortHistory
 *
 * @return PatchPanelPortHistoryFile
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
}
