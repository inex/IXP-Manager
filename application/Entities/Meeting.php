<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Meeting
 */
class Meeting
{
    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $before_text
     */
    protected $before_text;

    /**
     * @var string $after_text
     */
    protected $after_text;

    /**
     * @var \DateTime $date
     */
    protected $date;

    /**
     * @var \DateTime $time
     */
    protected $time;

    /**
     * @var string $venue
     */
    protected $venue;

    /**
     * @var string $venue_url
     */
    protected $venue_url;

    /**
     * @var \DateTime $created_at
     */
    protected $created_at;

    /**
     * @var integer $updated_by
     */
    protected $updated_by;

    /**
     * @var \DateTime $updated_at
     */
    protected $updated_at;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $MeetingItems;

    /**
     * @var Entities\User
     */
    protected $CreatedBy;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->MeetingItems = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set title
     *
     * @param string $title
     * @return Meeting
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set before_text
     *
     * @param string $beforeText
     * @return Meeting
     */
    public function setBeforeText($beforeText)
    {
        $this->before_text = $beforeText;
    
        return $this;
    }

    /**
     * Get before_text
     *
     * @return string 
     */
    public function getBeforeText()
    {
        return $this->before_text;
    }

    /**
     * Set after_text
     *
     * @param string $afterText
     * @return Meeting
     */
    public function setAfterText($afterText)
    {
        $this->after_text = $afterText;
    
        return $this;
    }

    /**
     * Get after_text
     *
     * @return string 
     */
    public function getAfterText()
    {
        return $this->after_text;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Meeting
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Meeting
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set venue
     *
     * @param string $venue
     * @return Meeting
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;
    
        return $this;
    }

    /**
     * Get venue
     *
     * @return string 
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * Set venue_url
     *
     * @param string $venueUrl
     * @return Meeting
     */
    public function setVenueUrl($venueUrl)
    {
        $this->venue_url = $venueUrl;
    
        return $this;
    }

    /**
     * Get venue_url
     *
     * @return string 
     */
    public function getVenueUrl()
    {
        return $this->venue_url;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Meeting
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_by
     *
     * @param integer $updatedBy
     * @return Meeting
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updated_by = $updatedBy;
    
        return $this;
    }

    /**
     * Get updated_by
     *
     * @return integer 
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Meeting
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
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
     * Add MeetingItems
     *
     * @param Entities\MeetingItem $meetingItems
     * @return Meeting
     */
    public function addMeetingItem(\Entities\MeetingItem $meetingItems)
    {
        $this->MeetingItems[] = $meetingItems;
    
        return $this;
    }

    /**
     * Remove MeetingItems
     *
     * @param Entities\MeetingItem $meetingItems
     */
    public function removeMeetingItem(\Entities\MeetingItem $meetingItems)
    {
        $this->MeetingItems->removeElement($meetingItems);
    }

    /**
     * Get MeetingItems
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMeetingItems()
    {
        return $this->MeetingItems;
    }

    /**
     * Set CreatedBy
     *
     * @param Entities\User $createdBy
     * @return Meeting
     */
    public function setCreatedBy(\Entities\User $createdBy = null)
    {
        $this->CreatedBy = $createdBy;
    
        return $this;
    }

    /**
     * Get CreatedBy
     *
     * @return Entities\User 
     */
    public function getCreatedBy()
    {
        return $this->CreatedBy;
    }
}