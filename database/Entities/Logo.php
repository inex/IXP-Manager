<?php

namespace Entities;

/**
 * Logo
 */
class Logo
{
/**
 * @var string
 */
private $original_name;

/**
 * @var string
 */
private $stored_name;

/**
 * @var string
 */
private $uploaded_by;

/**
 * @var \DateTime
 */
private $uploaded_at;

/**
 * @var integer
 */
private $width;

/**
 * @var integer
 */
private $height;

/**
 * @var integer
 */
private $id;

/**
 * @var \Entities\Customer
 */
private $customer;


/**
 * Set originalName
 *
 * @param string $originalName
 *
 * @return Logo
 */
public function setOriginalName($originalName)
{
$this->original_name = $originalName;

return $this;
}

/**
 * Get originalName
 *
 * @return string
 */
public function getOriginalName()
{
return $this->original_name;
}

/**
 * Set storedName
 *
 * @param string $storedName
 *
 * @return Logo
 */
public function setStoredName($storedName)
{
$this->stored_name = $storedName;

return $this;
}

/**
 * Get storedName
 *
 * @return string
 */
public function getStoredName()
{
return $this->stored_name;
}

/**
 * Set uploadedBy
 *
 * @param string $uploadedBy
 *
 * @return Logo
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
 * Set uploadedAt
 *
 * @param \DateTime $uploadedAt
 *
 * @return Logo
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
 * Set width
 *
 * @param integer $width
 *
 * @return Logo
 */
public function setWidth($width)
{
$this->width = $width;

return $this;
}

/**
 * Get width
 *
 * @return integer
 */
public function getWidth()
{
return $this->width;
}

/**
 * Set height
 *
 * @param integer $height
 *
 * @return Logo
 */
public function setHeight($height)
{
$this->height = $height;

return $this;
}

/**
 * Get height
 *
 * @return integer
 */
public function getHeight()
{
return $this->height;
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
 * Set customer
 *
 * @param \Entities\Customer $customer
 *
 * @return Logo
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
     * Creates a hierarchy directory structure to shard image storage
     *
     * @return string the/sharded/path/filename
     */
    public function getShardedPath() {
        return substr($this->getStoredName(), 0, 1) . '/' . substr($this->getStoredName(), 1, 2) . '/' . $this->getStoredName();
    }


}
