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
 * PatchPanelPortFile
 */
class PatchPanelPortFile
{
    CONST UPLOAD_PATH = 'ppp';

    private $created_at;
    private $updated_at;
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
     * @var boolean
     */
    private $is_private = '0';

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
     * Get type as an icon from awesome font
     *
     * @return string
     */
    public function getTypeAsIcon()
    {
        switch ($this->type) {
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



    /**
    * Set isPrivate
    *
    * @param boolean $isPrivate
    *
    * @return PatchPanelPortFile
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
     * get the patch for the panel port file
     *
     * @return string
     */
    public function getPath() {
        return self::UPLOAD_PATH . '/' . substr( $this->getStorageLocation(), 0, 1 ) . '/'
            . substr( $this->getStorageLocation(), 1, 1 ) . '/' . $this->getStorageLocation();
    }


}
