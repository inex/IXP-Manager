<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Entities;


/**
 * PatchPanelPortHistoryFile
 */
class PatchPanelPortHistoryFile
{
    private $created_at;
    private $updated_at;
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
     * Get name
     *
     * @return string
     */
    public function getNameTruncate()
    {
        return strlen($this->name) > 80 ? substr($this->name,0,80)."...".explode('.',$this->name)[1] : $this->name;
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
     * @return string
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
     * @param PatchPanelPortHistory $patchPanelPortHistory
     *
     * @return PatchPanelPortHistoryFile
     */
    public function addPatchPanelPortHistory(PatchPanelPortHistory $patchPanelPortHistory)
    {
        $this->patchPanelPortHistory = $patchPanelPortHistory;

        return $this;
    }

    /**
     * Remove patchPanelPortHistory
     *
     * @param PatchPanelPortHistory $patchPanelPortHistory
     */
    public function removePatchPanelPortHistory(PatchPanelPortHistory $patchPanelPortHistory)
    {
        $this->patchPanelPortHistory->removeElement($patchPanelPortHistory);
    }

    /**
     * Get patchPanelPortHistory
     *
     * @return PatchPanelPortHistory
     */
    public function getPatchPanelPortHistory()
    {
        return $this->patchPanelPortHistory;
    }


    /**
     * Populate this file history entity with details from a patch panel port file.
     *
     * @param PatchPanelPortFile $pppf
     * @return PatchPanelPortHistoryFile
     */
    public function setFromPatchPanelPortFile( PatchPanelPortFile $pppf ): PatchPanelPortHistoryFile {

        return $this->setName( $pppf->getName() )
            ->setSize( $pppf->getSize() )
            ->setType( $pppf->getType() )
            ->setStorageLocation( $pppf->getStorageLocation() )
            ->setUploadedBy( $pppf->getUploadedBy() )
            ->setUploadedAt( $pppf->getUploadedAt() )
            ->setIsPrivate( $pppf->getIsPrivate() );
    }

    /**
     * get the patch for the panel port history file
     *
     * @return string
     */
    public function getPath() {
        return PatchPanelPortFile::UPLOAD_PATH . '/' . substr( $this->getStorageLocation(), 0, 1 ) . '/'
            . substr( $this->getStorageLocation(), 1, 1 ) . '/' . $this->getStorageLocation();
    }

}

