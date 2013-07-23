<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\MeetingItem
 */
class MeetingItem
{
    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $role
     */
    protected $role;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @var string $company
     */
    protected $company;

    /**
     * @var string $company_url
     */
    protected $company_url;

    /**
     * @var string $summary
     */
    protected $summary;

    /**
     * @var string $presentation
     */
    protected $presentation;

    /**
     * @var string $filename
     */
    protected $filename;

    /**
     * @var string $video_url
     */
    protected $video_url;

    /**
     * @var boolean $other_content
     */
    protected $other_content;

    /**
     * @var integer $created_by
     */
    protected $created_by;

    /**
     * @var \DateTime $created_at
     */
    protected $created_at;

    /**
     * @var integer $updated_by
     */
    protected $updated_by;

    /**
     * @var \DateTime $updated_ar
     */
    protected $updated_ar;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Entities\Meeting
     */
    protected $Meeting;


    /**
     * Set title
     *
     * @param string $title
     * @return MeetingItem
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
     * Set name
     *
     * @param string $name
     * @return MeetingItem
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
     * Set role
     *
     * @param string $role
     * @return MeetingItem
     */
    public function setRole($role)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return MeetingItem
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set company
     *
     * @param string $company
     * @return MeetingItem
     */
    public function setCompany($company)
    {
        $this->company = $company;
    
        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set company_url
     *
     * @param string $companyUrl
     * @return MeetingItem
     */
    public function setCompanyUrl($companyUrl)
    {
        $this->company_url = $companyUrl;
    
        return $this;
    }

    /**
     * Get company_url
     *
     * @return string 
     */
    public function getCompanyUrl()
    {
        return $this->company_url;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return MeetingItem
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    
        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set presentation
     *
     * @param string $presentation
     * @return MeetingItem
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;
    
        return $this;
    }

    /**
     * Get presentation
     *
     * @return string 
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return MeetingItem
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    
        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set video_url
     *
     * @param string $videoUrl
     * @return MeetingItem
     */
    public function setVideoUrl($videoUrl)
    {
        $this->video_url = $videoUrl;
    
        return $this;
    }

    /**
     * Get video_url
     *
     * @return string 
     */
    public function getVideoUrl()
    {
        return $this->video_url;
    }

    /**
     * Set other_content
     *
     * @param boolean $otherContent
     * @return MeetingItem
     */
    public function setOtherContent($otherContent)
    {
        $this->other_content = $otherContent;
    
        return $this;
    }

    /**
     * Get other_content
     *
     * @return boolean 
     */
    public function getOtherContent()
    {
        return $this->other_content;
    }

    /**
     * Set created_by
     *
     * @param integer $createdBy
     * @return MeetingItem
     */
    public function setCreatedBy($createdBy)
    {
        $this->created_by = $createdBy;
    
        return $this;
    }

    /**
     * Get created_by
     *
     * @return integer 
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return MeetingItem
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
     * @return MeetingItem
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Meeting
     *
     * @param Entities\Meeting $meeting
     * @return MeetingItem
     */
    public function setMeeting(\Entities\Meeting $meeting = null)
    {
        $this->Meeting = $meeting;
    
        return $this;
    }

    /**
     * Get Meeting
     *
     * @return Entities\Meeting 
     */
    public function getMeeting()
    {
        return $this->Meeting;
    }
    /**
     * @var \DateTime $updated_at
     */
    protected $updated_at;


    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return MeetingItem
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
}