<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Customer
 */
class Customer
{
    const TYPE_FULL       = 1;
    const TYPE_ASSOCIATE  = 2;
    const TYPE_INTERNAL   = 3;
    const TYPE_PROBONO    = 4;
    
    public static $CUST_TYPES_TEXT = array(
        self::TYPE_FULL      => 'Full',
        self::TYPE_ASSOCIATE => 'Associate',
        self::TYPE_INTERNAL  => 'Internal',
        self::TYPE_PROBONO   => 'Pro-bono'
    );
    
    
    const STATUS_NORMAL       = 1;
    const STATUS_NOTCONNECTED = 2;
    const STATUS_SUSPENDED    = 3;
    
    public static $CUST_STATUS_TEXT = array(
        self::STATUS_NORMAL           => 'Normal',
        self::STATUS_NOTCONNECTED     => 'Not Connected',
        self::STATUS_SUSPENDED        => 'Suspended',
    );
    
    const PEERING_POLICY_OPEN       = 'open';
    const PEERING_POLICY_SELECTIVE  = 'selective';
    const PEERING_POLICY_MANDATORY  = 'mandatory';
    const PEERING_POLICY_CLOSED     = 'closed';
    
    public static $PEERING_POLICIES = array(
        self::PEERING_POLICY_OPEN       => 'open',
        self::PEERING_POLICY_SELECTIVE  => 'selective',
        self::PEERING_POLICY_MANDATORY  => 'mandatory',
        self::PEERING_POLICY_CLOSED     => 'closed'
    );

    const NOC_HOURS_24x7 = '24x7';    
    const NOC_HOURS_8x5  = '8x5';    
    const NOC_HOURS_8x7  = '8x7';    
    const NOC_HOURS_12x5 = '12x5';    
    const NOC_HOURS_12x7 = '12x7';    
    
    public static $NOC_HOURS = array(
        self::NOC_HOURS_24x7 => '24x7',
        self::NOC_HOURS_8x5  => '8x5',
        self::NOC_HOURS_8x7  => '8x7',
        self::NOC_HOURS_12x5 => '12x5',
        self::NOC_HOURS_12x7 => '12x7'
    );
    
    
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var integer $type
     */
    private $type;

    /**
     * @var string $shortname
     */
    private $shortname;

    /**
     * @var integer $autsys
     */
    private $autsys;

    /**
     * @var integer $maxprefixes
     */
    private $maxprefixes;

    /**
     * @var string $peeringemail
     */
    private $peeringemail;

    /**
     * @var string $nocphone
     */
    private $nocphone;

    /**
     * @var string $noc24hrphone
     */
    private $noc24hrphone;

    /**
     * @var string $nocfax
     */
    private $nocfax;

    /**
     * @var string $nocemail
     */
    private $nocemail;

    /**
     * @var string $nochours
     */
    private $nochours;

    /**
     * @var string $nocwww
     */
    private $nocwww;

    /**
     * @var integer $irrdb
     */
    private $irrdb;

    /**
     * @var string $peeringmacro
     */
    private $peeringmacro;

    /**
     * @var string $peeringpolicy
     */
    private $peeringpolicy;

    /**
     * @var string $billingContact
     */
    private $billingContact;

    /**
     * @var string $billingAddress1
     */
    private $billingAddress1;

    /**
     * @var string $billingAddress2
     */
    private $billingAddress2;

    /**
     * @var string $billingCity
     */
    private $billingCity;

    /**
     * @var string $billingCountry
     */
    private $billingCountry;

    /**
     * @var string $corpwww
     */
    private $corpwww;

    /**
     * @var \DateTime $datejoin
     */
    private $datejoin;

    /**
     * @var \DateTime $dateleave
     */
    private $dateleave;

    /**
     * @var integer $status
     */
    private $status;

    /**
     * @var boolean $activepeeringmatrix
     */
    private $activepeeringmatrix;

    /**
     * @var string $notes
     */
    private $notes;

    /**
     * @var \DateTime $lastupdated
     */
    private $lastupdated;

    /**
     * @var integer $lastupdatedby
     */
    private $lastupdatedby;

    /**
     * @var string $creator
     */
    private $creator;

    /**
     * @var \DateTime $created
     */
    private $created;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $VirtualInterfaces;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $Contacts;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $ConsoleServerConnections;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $CustomerEquipment;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $Peers;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $PeersWith;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $XCusts;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $YCusts;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $RSDroppedPrefixes;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $Users;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $Traffic95ths;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $Traffic95thMonthlys;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $TrafficDailies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->VirtualInterfaces = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Contacts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ConsoleServerConnections = new \Doctrine\Common\Collections\ArrayCollection();
        $this->CustomerEquipment = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Peers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->PeersWith = new \Doctrine\Common\Collections\ArrayCollection();
        $this->XCusts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->YCusts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->RSDroppedPrefixes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Traffic95ths = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Traffic95thMonthlys = new \Doctrine\Common\Collections\ArrayCollection();
        $this->TrafficDailies = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Customer
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
     * @param integer $type
     * @return Customer
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set shortname
     *
     * @param string $shortname
     * @return Customer
     */
    public function setShortname($shortname)
    {
        $this->shortname = $shortname;
    
        return $this;
    }

    /**
     * Get shortname
     *
     * @return string
     */
    public function getShortname()
    {
        return $this->shortname;
    }

    /**
     * Set autsys
     *
     * @param integer $autsys
     * @return Customer
     */
    public function setAutsys($autsys)
    {
        $this->autsys = $autsys;
    
        return $this;
    }

    /**
     * Get autsys
     *
     * @return integer
     */
    public function getAutsys()
    {
        return $this->autsys;
    }

    /**
     * Set maxprefixes
     *
     * @param integer $maxprefixes
     * @return Customer
     */
    public function setMaxprefixes($maxprefixes)
    {
        $this->maxprefixes = $maxprefixes;
    
        return $this;
    }

    /**
     * Get maxprefixes
     *
     * @return integer
     */
    public function getMaxprefixes()
    {
        return $this->maxprefixes;
    }

    /**
     * Set peeringemail
     *
     * @param string $peeringemail
     * @return Customer
     */
    public function setPeeringemail($peeringemail)
    {
        $this->peeringemail = $peeringemail;
    
        return $this;
    }

    /**
     * Get peeringemail
     *
     * @return string
     */
    public function getPeeringemail()
    {
        return $this->peeringemail;
    }

    /**
     * Set nocphone
     *
     * @param string $nocphone
     * @return Customer
     */
    public function setNocphone($nocphone)
    {
        $this->nocphone = $nocphone;
    
        return $this;
    }

    /**
     * Get nocphone
     *
     * @return string
     */
    public function getNocphone()
    {
        return $this->nocphone;
    }

    /**
     * Set noc24hrphone
     *
     * @param string $noc24hrphone
     * @return Customer
     */
    public function setNoc24hrphone($noc24hrphone)
    {
        $this->noc24hrphone = $noc24hrphone;
    
        return $this;
    }

    /**
     * Get noc24hrphone
     *
     * @return string
     */
    public function getNoc24hrphone()
    {
        return $this->noc24hrphone;
    }

    /**
     * Set nocfax
     *
     * @param string $nocfax
     * @return Customer
     */
    public function setNocfax($nocfax)
    {
        $this->nocfax = $nocfax;
    
        return $this;
    }

    /**
     * Get nocfax
     *
     * @return string
     */
    public function getNocfax()
    {
        return $this->nocfax;
    }

    /**
     * Set nocemail
     *
     * @param string $nocemail
     * @return Customer
     */
    public function setNocemail($nocemail)
    {
        $this->nocemail = $nocemail;
    
        return $this;
    }

    /**
     * Get nocemail
     *
     * @return string
     */
    public function getNocemail()
    {
        return $this->nocemail;
    }

    /**
     * Set nochours
     *
     * @param string $nochours
     * @return Customer
     */
    public function setNochours($nochours)
    {
        $this->nochours = $nochours;
    
        return $this;
    }

    /**
     * Get nochours
     *
     * @return string
     */
    public function getNochours()
    {
        return $this->nochours;
    }

    /**
     * Set nocwww
     *
     * @param string $nocwww
     * @return Customer
     */
    public function setNocwww($nocwww)
    {
        $this->nocwww = $nocwww;
    
        return $this;
    }

    /**
     * Get nocwww
     *
     * @return string
     */
    public function getNocwww()
    {
        return $this->nocwww;
    }

    /**
     * Set irrdb
     *
     * @param integer $irrdb
     * @return Customer
     */
    public function setIrrdb($irrdb)
    {
        $this->irrdb = $irrdb;
    
        return $this;
    }

    /**
     * Get irrdb
     *
     * @return integer
     */
    public function getIrrdb()
    {
        return $this->irrdb;
    }

    /**
     * Set peeringmacro
     *
     * @param string $peeringmacro
     * @return Customer
     */
    public function setPeeringmacro($peeringmacro)
    {
        $this->peeringmacro = $peeringmacro;
    
        return $this;
    }

    /**
     * Get peeringmacro
     *
     * @return string
     */
    public function getPeeringmacro()
    {
        return $this->peeringmacro;
    }

    /**
     * Set peeringpolicy
     *
     * @param string $peeringpolicy
     * @return Customer
     */
    public function setPeeringpolicy($peeringpolicy)
    {
        $this->peeringpolicy = $peeringpolicy;
    
        return $this;
    }

    /**
     * Get peeringpolicy
     *
     * @return string
     */
    public function getPeeringpolicy()
    {
        return $this->peeringpolicy;
    }

    /**
     * Set billingContact
     *
     * @param string $billingContact
     * @return Customer
     */
    public function setBillingContact($billingContact)
    {
        $this->billingContact = $billingContact;
    
        return $this;
    }

    /**
     * Get billingContact
     *
     * @return string
     */
    public function getBillingContact()
    {
        return $this->billingContact;
    }

    /**
     * Set billingAddress1
     *
     * @param string $billingAddress1
     * @return Customer
     */
    public function setBillingAddress1($billingAddress1)
    {
        $this->billingAddress1 = $billingAddress1;
    
        return $this;
    }

    /**
     * Get billingAddress1
     *
     * @return string
     */
    public function getBillingAddress1()
    {
        return $this->billingAddress1;
    }

    /**
     * Set billingAddress2
     *
     * @param string $billingAddress2
     * @return Customer
     */
    public function setBillingAddress2($billingAddress2)
    {
        $this->billingAddress2 = $billingAddress2;
    
        return $this;
    }

    /**
     * Get billingAddress2
     *
     * @return string
     */
    public function getBillingAddress2()
    {
        return $this->billingAddress2;
    }

    /**
     * Set billingCity
     *
     * @param string $billingCity
     * @return Customer
     */
    public function setBillingCity($billingCity)
    {
        $this->billingCity = $billingCity;
    
        return $this;
    }

    /**
     * Get billingCity
     *
     * @return string
     */
    public function getBillingCity()
    {
        return $this->billingCity;
    }

    /**
     * Set billingCountry
     *
     * @param string $billingCountry
     * @return Customer
     */
    public function setBillingCountry($billingCountry)
    {
        $this->billingCountry = $billingCountry;
    
        return $this;
    }

    /**
     * Get billingCountry
     *
     * @return string
     */
    public function getBillingCountry()
    {
        return $this->billingCountry;
    }

    /**
     * Set corpwww
     *
     * @param string $corpwww
     * @return Customer
     */
    public function setCorpwww($corpwww)
    {
        $this->corpwww = $corpwww;
    
        return $this;
    }

    /**
     * Get corpwww
     *
     * @return string
     */
    public function getCorpwww()
    {
        return $this->corpwww;
    }

    /**
     * Set datejoin
     *
     * @param \DateTime $datejoin
     * @return Customer
     */
    public function setDatejoin($datejoin)
    {
        $this->datejoin = $datejoin;
    
        return $this;
    }

    /**
     * Get datejoin
     *
     * @return \DateTime
     */
    public function getDatejoin()
    {
        return $this->datejoin;
    }

    /**
     * Set dateleave
     *
     * @param \DateTime $dateleave
     * @return Customer
     */
    public function setDateleave($dateleave)
    {
        $this->dateleave = $dateleave;
    
        return $this;
    }

    /**
     * Get dateleave
     *
     * @return \DateTime
     */
    public function getDateleave()
    {
        // on 64bit system, MySQL's '0000-00-00' is in range and evaluates as a non-zero
        // date - see: https://bugs.php.net/bug.php?id=60257
        
        if( PHP_INT_SIZE == 4 )
            return $this->dateleave;
            
        if( $this->dateleave instanceof \DateTime && $this->dateleave->format( 'Y-m-d' ) == '-0001-11-30' )  // 0000-00-00 00:00:00 on 64bit systems
            return null;
                                            
        return $this->dateleave;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Customer
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set activepeeringmatrix
     *
     * @param boolean $activepeeringmatrix
     * @return Customer
     */
    public function setActivepeeringmatrix($activepeeringmatrix)
    {
        $this->activepeeringmatrix = $activepeeringmatrix;
    
        return $this;
    }

    /**
     * Get activepeeringmatrix
     *
     * @return boolean
     */
    public function getActivepeeringmatrix()
    {
        return $this->activepeeringmatrix;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Customer
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
     * Set lastupdated
     *
     * @param \DateTime $lastupdated
     * @return Customer
     */
    public function setLastupdated($lastupdated)
    {
        $this->lastupdated = $lastupdated;
    
        return $this;
    }

    /**
     * Get lastupdated
     *
     * @return \DateTime
     */
    public function getLastupdated()
    {
        return $this->lastupdated;
    }

    /**
     * Set lastupdatedby
     *
     * @param integer $lastupdatedby
     * @return Customer
     */
    public function setLastupdatedby($lastupdatedby)
    {
        $this->lastupdatedby = $lastupdatedby;
    
        return $this;
    }

    /**
     * Get lastupdatedby
     *
     * @return integer
     */
    public function getLastupdatedby()
    {
        return $this->lastupdatedby;
    }

    /**
     * Set creator
     *
     * @param string $creator
     * @return Customer
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    
        return $this;
    }

    /**
     * Get creator
     *
     * @return string
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Customer
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
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
     * Add VirtualInterfaces
     *
     * @param Entities\VirtualInterface $virtualInterfaces
     * @return Customer
     */
    public function addVirtualInterface(\Entities\VirtualInterface $virtualInterfaces)
    {
        $this->VirtualInterfaces[] = $virtualInterfaces;
    
        return $this;
    }

    /**
     * Remove VirtualInterfaces
     *
     * @param Entities\VirtualInterface $virtualInterfaces
     */
    public function removeVirtualInterface(\Entities\VirtualInterface $virtualInterfaces)
    {
        $this->VirtualInterfaces->removeElement($virtualInterfaces);
    }

    /**
     * Get VirtualInterfaces
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getVirtualInterfaces()
    {
        return $this->VirtualInterfaces;
    }

    /**
     * Add Contacts
     *
     * @param Entities\Contact $contacts
     * @return Customer
     */
    public function addContact(\Entities\Contact $contacts)
    {
        $this->Contacts[] = $contacts;
    
        return $this;
    }

    /**
     * Remove Contacts
     *
     * @param Entities\Contact $contacts
     */
    public function removeContact(\Entities\Contact $contacts)
    {
        $this->Contacts->removeElement($contacts);
    }

    /**
     * Get Contacts
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->Contacts;
    }

    /**
     * Add ConsoleServerConnections
     *
     * @param Entities\ConsoleServerConnection $consoleServerConnections
     * @return Customer
     */
    public function addConsoleServerConnection(\Entities\ConsoleServerConnection $consoleServerConnections)
    {
        $this->ConsoleServerConnections[] = $consoleServerConnections;
    
        return $this;
    }

    /**
     * Remove ConsoleServerConnections
     *
     * @param Entities\ConsoleServerConnection $consoleServerConnections
     */
    public function removeConsoleServerConnection(\Entities\ConsoleServerConnection $consoleServerConnections)
    {
        $this->ConsoleServerConnections->removeElement($consoleServerConnections);
    }

    /**
     * Get ConsoleServerConnections
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getConsoleServerConnections()
    {
        return $this->ConsoleServerConnections;
    }

    /**
     * Add CustomerEquipment
     *
     * @param Entities\CustomerEquipment $customerEquipment
     * @return Customer
     */
    public function addCustomerEquipment(\Entities\CustomerEquipment $customerEquipment)
    {
        $this->CustomerEquipment[] = $customerEquipment;
    
        return $this;
    }

    /**
     * Remove CustomerEquipment
     *
     * @param Entities\CustomerEquipment $customerEquipment
     */
    public function removeCustomerEquipment(\Entities\CustomerEquipment $customerEquipment)
    {
        $this->CustomerEquipment->removeElement($customerEquipment);
    }

    /**
     * Get CustomerEquipment
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCustomerEquipment()
    {
        return $this->CustomerEquipment;
    }

    /**
     * Add Peers
     *
     * @param Entities\PeeringManager $peers
     * @return Customer
     */
    public function addPeer(\Entities\PeeringManager $peers)
    {
        $this->Peers[] = $peers;
    
        return $this;
    }

    /**
     * Remove Peers
     *
     * @param Entities\PeeringManager $peers
     */
    public function removePeer(\Entities\PeeringManager $peers)
    {
        $this->Peers->removeElement($peers);
    }

    /**
     * Get Peers
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPeers()
    {
        return $this->Peers;
    }

    /**
     * Add PeersWith
     *
     * @param Entities\PeeringManager $peersWith
     * @return Customer
     */
    public function addPeersWith(\Entities\PeeringManager $peersWith)
    {
        $this->PeersWith[] = $peersWith;
    
        return $this;
    }

    /**
     * Remove PeersWith
     *
     * @param Entities\PeeringManager $peersWith
     */
    public function removePeersWith(\Entities\PeeringManager $peersWith)
    {
        $this->PeersWith->removeElement($peersWith);
    }

    /**
     * Get PeersWith
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPeersWith()
    {
        return $this->PeersWith;
    }

    /**
     * Add XCusts
     *
     * @param Entities\PeeringMatrix $xCusts
     * @return Customer
     */
    public function addXCust(\Entities\PeeringMatrix $xCusts)
    {
        $this->XCusts[] = $xCusts;
    
        return $this;
    }

    /**
     * Remove XCusts
     *
     * @param Entities\PeeringMatrix $xCusts
     */
    public function removeXCust(\Entities\PeeringMatrix $xCusts)
    {
        $this->XCusts->removeElement($xCusts);
    }

    /**
     * Get XCusts
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getXCusts()
    {
        return $this->XCusts;
    }

    /**
     * Add YCusts
     *
     * @param Entities\PeeringMatrix $yCusts
     * @return Customer
     */
    public function addYCust(\Entities\PeeringMatrix $yCusts)
    {
        $this->YCusts[] = $yCusts;
    
        return $this;
    }

    /**
     * Remove YCusts
     *
     * @param Entities\PeeringMatrix $yCusts
     */
    public function removeYCust(\Entities\PeeringMatrix $yCusts)
    {
        $this->YCusts->removeElement($yCusts);
    }

    /**
     * Get YCusts
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getYCusts()
    {
        return $this->YCusts;
    }

    /**
     * Add RSDroppedPrefixes
     *
     * @param Entities\RSDroppedPrefix $rSDroppedPrefixes
     * @return Customer
     */
    public function addRSDroppedPrefixe(\Entities\RSDroppedPrefix $rSDroppedPrefixes)
    {
        $this->RSDroppedPrefixes[] = $rSDroppedPrefixes;
    
        return $this;
    }

    /**
     * Remove RSDroppedPrefixes
     *
     * @param Entities\RSDroppedPrefix $rSDroppedPrefixes
     */
    public function removeRSDroppedPrefixe(\Entities\RSDroppedPrefix $rSDroppedPrefixes)
    {
        $this->RSDroppedPrefixes->removeElement($rSDroppedPrefixes);
    }

    /**
     * Get RSDroppedPrefixes
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRSDroppedPrefixes()
    {
        return $this->RSDroppedPrefixes;
    }

    /**
     * Add Users
     *
     * @param Entities\User $users
     * @return Customer
     */
    public function addUser(\Entities\User $users)
    {
        $this->Users[] = $users;
    
        return $this;
    }

    /**
     * Remove Users
     *
     * @param Entities\User $users
     */
    public function removeUser(\Entities\User $users)
    {
        $this->Users->removeElement($users);
    }

    /**
     * Get Users
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->Users;
    }

    /**
     * Add Traffic95ths
     *
     * @param Entities\Traffic95th $traffic95ths
     * @return Customer
     */
    public function addTraffic95th(\Entities\Traffic95th $traffic95ths)
    {
        $this->Traffic95ths[] = $traffic95ths;
    
        return $this;
    }

    /**
     * Remove Traffic95ths
     *
     * @param Entities\Traffic95th $traffic95ths
     */
    public function removeTraffic95th(\Entities\Traffic95th $traffic95ths)
    {
        $this->Traffic95ths->removeElement($traffic95ths);
    }

    /**
     * Get Traffic95ths
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTraffic95ths()
    {
        return $this->Traffic95ths;
    }

    /**
     * Add Traffic95thMonthlys
     *
     * @param Entities\Traffic95thMonthly $traffic95thMonthlys
     * @return Customer
     */
    public function addTraffic95thMonthly(\Entities\Traffic95thMonthly $traffic95thMonthlys)
    {
        $this->Traffic95thMonthlys[] = $traffic95thMonthlys;
    
        return $this;
    }

    /**
     * Remove Traffic95thMonthlys
     *
     * @param Entities\Traffic95thMonthly $traffic95thMonthlys
     */
    public function removeTraffic95thMonthly(\Entities\Traffic95thMonthly $traffic95thMonthlys)
    {
        $this->Traffic95thMonthlys->removeElement($traffic95thMonthlys);
    }

    /**
     * Get Traffic95thMonthlys
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTraffic95thMonthlys()
    {
        return $this->Traffic95thMonthlys;
    }

    /**
     * Add TrafficDailies
     *
     * @param Entities\TrafficDaily $trafficDailies
     * @return Customer
     */
    public function addTrafficDailie(\Entities\TrafficDaily $trafficDailies)
    {
        $this->TrafficDailies[] = $trafficDailies;
    
        return $this;
    }

    /**
     * Remove TrafficDailies
     *
     * @param Entities\TrafficDaily $trafficDailies
     */
    public function removeTrafficDailie(\Entities\TrafficDaily $trafficDailies)
    {
        $this->TrafficDailies->removeElement($trafficDailies);
    }

    /**
     * Get TrafficDailies
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTrafficDailies()
    {
        return $this->TrafficDailies;
    }
    /**
     * @var string $noc24hphone
     */
    private $noc24hphone;


    /**
     * Set noc24hphone
     *
     * @param string $noc24hphone
     * @return Customer
     */
    public function setNoc24hphone($noc24hphone)
    {
        $this->noc24hphone = $noc24hphone;
    
        return $this;
    }

    /**
     * Get noc24hphone
     *
     * @return string
     */
    public function getNoc24hphone()
    {
        return $this->noc24hphone;
    }
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $SecEvents;


    /**
     * Add SecEvents
     *
     * @param Entities\SecEvent $secEvents
     * @return Customer
     */
    public function addSecEvent(\Entities\SecEvent $secEvents)
    {
        $this->SecEvents[] = $secEvents;
    
        return $this;
    }

    /**
     * Remove SecEvents
     *
     * @param Entities\SecEvent $secEvents
     */
    public function removeSecEvent(\Entities\SecEvent $secEvents)
    {
        $this->SecEvents->removeElement($secEvents);
    }

    /**
     * Get SecEvents
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSecEvents()
    {
        return $this->SecEvents;
    }
    
    
    public function hasLeft()
    {
        // sigh. Using a date field to determine if an account is closed or not is a
        // very bad idea and should be changed => FIXME
        
        return $this->getDateleave() != null;
    }
    
    
    /**
     * Find all users of privilege CUSTADMIN for this customer
     *
     * @return \Entities\User[] Array of CUSTADMIN users
     */
    public function getAdminUsers()
    {
        $ausers = [];
        
        foreach( $this->getUsers() as $u )
            if( $u->getPrivs() == \Entities\User::AUTH_CUSTADMIN )
                $ausers[] = $u;
        
        return $ausers;
    }
    
    /**
     * Check if this customer is of the named type
     * @return boolean
     */
    public function isTypeFull()
    {
        return $this->getType() == self::TYPE_FULL;
    }

    /**
     * Check if this customer is of the named type
     * @return boolean
     */
    public function isTypeAssociate()
    {
        return $this->getType() == self::TYPE_ASSOCIATE;
    }

    /**
     * Check if this customer is of the named type
     * @return boolean
     */
    public function isTypeInternal()
    {
        return $this->getType() == self::TYPE_INTERNAL;
    }

    /**
     * Check if this customer is of the named type
     * @return boolean
     */
    public function isTypeProBono()
    {
        return $this->getType() == self::TYPE_PROBONO;
    }


}