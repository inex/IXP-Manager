<?php

namespace Entities;

/**
 * PatchPanelPortFile
 */
class PatchPanelPortFile
{
    /**
     * @var integer
     */
    private $id;
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
     * @var \Entities\PatchPanelPort
     */
    private $patchPanelPort;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return PatchPanelPortFile
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
     * @return PatchPanelPortFile
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
     * @return PatchPanelPortFile
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
     * @return PatchPanelPortFile
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
     * @return PatchPanelPortFile
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
     * @return PatchPanelPortFile
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
     * Set patchPanelPort
     *
     * @param \Entities\PatchPanelPort $patchPanelPort
     *
     * @return PatchPanelPortFile
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
}
