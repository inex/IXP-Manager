<?php

namespace Entities;


Use D2EM;
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
     * @var boolean
     */
    private $is_private = '0';

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $patchPanelPortHistory;


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
     * Get type as an icon from awesome font
     *
     * @return string
     */
    public function getTypeAsIcon()
    {
        switch ($this->getType()) {
            case 'image/jpeg':
                $icon = 'fa-file-image-o';
                break;
            case 'image/png':
                $icon = 'fa-file-image-o';
                break;
            case 'image/bmp':
                $icon = 'fa-file-image-o';
                break;
            case 'application/pdf':
                $icon = 'fa-file-pdf-o';
                break;
            case 'application/zip':
                $icon = 'fa-file-archive-o';
                break;
            case 'text/plain':
                $icon = 'fa-file-text';
                break;
            default:
                $icon = 'fa-file';
                break;
        }
        return $icon;
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
     * Get uploadedAt
     *
     * @return \DateTime
     */
    public function getUploadedAtFormated()
    {
        return ($this->getUploadedAt() == null) ? $this->getUploadedAt() : $this->getUploadedAt()->format('Y-m-d');
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

    function getSizeFormated()
    {
        $bytes = $this->getSize();
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' kB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
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
     * Set isPrivate
     *
     * @param boolean $isPrivate
     *
     * @return PatchPanelPortHistoryFile
     */
    public function setIsPrivate($isPrivate)
    {
        $this->is_private = $isPrivate;

        return $this;
    }

    /**
     * Get isPrivate
     *
     * @return boolean
     */
    public function getIsPrivate()
    {
        return $this->is_private;
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
        $this->patchPanelPortHistory = $patchPanelPortHistory;

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
     * Create a patch panel port file history
     * Duplicate all the datas of the current patch panel port file in the history table
     * And delete the patch panel port file record at the end
     *
     * @param \Entities\PatchPanelPort $patchPanelPort
     * @param \Entities\PatchPanelPortHistory $PPPHistory patch panel port history object
     * @author     Yann Robin <yann@islandbridgenetworks.ie>
     * @return string
     */
    public static function createHistory($patchPanelPort,$PPPHistory){

        foreach ($patchPanelPort->getPatchPanelPortFiles() as $file){

            $pppHistoryFile = new PatchPanelPortHistoryFile();

            $pppHistoryFile->setName($file->getName());
            $pppHistoryFile->setSize($file->getSize());
            $pppHistoryFile->setType($file->getType());
            $pppHistoryFile->setStorageLocation($file->getStorageLocation());
            $pppHistoryFile->setUploadedBy($file->getUploadedBy());
            $pppHistoryFile->setUploadedAt($file->getUploadedAt());
            $pppHistoryFile->setIsPrivate($file->getIsPrivate());
            $pppHistoryFile->addPatchPanelPortHistory($PPPHistory);

            D2EM::persist($pppHistoryFile);
            D2EM::remove($file);
        }

        D2EM::flush();
    }
}
