<?php

namespace Entities;

use Carbon\Carbon;
use Auth, D2EM;
use Doctrine\Common\Collections\ArrayCollection;

use IXP\Mail\PatchPanelPort\{
    Cease   as CeaseMail,
    Connect as ConnectMail,
    Info    as InfoMail,
    Loa     as LoaMail
};

/**
 * Entities\PatchPanelPort
 */

class PatchPanelPort
{

    /**
     * CONST STATES
     */
    const STATE_AVAILABLE              = 1;
    const STATE_AWAITING_XCONNECT      = 2;
    const STATE_CONNECTED              = 3;
    const STATE_AWAITING_CEASE         = 4;
    const STATE_CEASED                 = 5;
    const STATE_BROKEN                 = 6;
    const STATE_RESERVED               = 7;
    const STATE_PREWIRED               = 8;
    const STATE_OTHER                  = 999;


    /**
     * CONST CHARGEABLE
     */
    const CHARGEABLE_YES                = 1;
    const CHARGEABLE_NO                 = 2;
    const CHARGEABLE_HALF               = 3;
    const CHARGEABLE_OTHER              = 4;

    /**
     * CONST OWNED
     */
    const OWNED_CUST                    = 1;
    const OWNED_IXP                     = 2;
    const OWNED_SERV_PRO                = 3;
    const OWNED_DATA_CENTER             = 4;
    const OWNED_OTHER                   = 5;


    /**
     * CONST EMAIL
     */
    const EMAIL_CONNECT                 = 1;
    const EMAIL_CEASE                   = 2;
    const EMAIL_INFO                    = 3;
    const EMAIL_LOA                     = 4;

    /**
     * @var array Email ids to classes
     */
    public static $EMAIL_CLASSES = [
        self::EMAIL_CEASE       =>  CeaseMail::class,
        self::EMAIL_CONNECT     =>  ConnectMail::class,
        self::EMAIL_INFO        =>  InfoMail::class,
        self::EMAIL_LOA         =>  LoaMail::class,
    ];

    /**
     * Array STATES
     */
    public static $STATES = [
        self::STATE_AVAILABLE           => "Available",
        self::STATE_AWAITING_XCONNECT   => "Awaiting Xconnect",
        self::STATE_CONNECTED           => "Connected",
        self::STATE_AWAITING_CEASE      => "Awaiting Cease",
        self::STATE_CEASED              => "Ceased",
        self::STATE_BROKEN              => "Broken",
        self::STATE_RESERVED            => "Reserved",
        self::STATE_PREWIRED            => "Prewired",
        self::STATE_OTHER               => "Other"
    ];

    /**
     * Array STATES for allocated
     */
    public static $ALLOCATED_STATES = [
        self::STATE_AWAITING_XCONNECT,
        self::STATE_CONNECTED,
        self::STATE_AWAITING_CEASE,
    ];

    /**
     * Array STATES for available
     */
    public static $AVAILABLE_STATES = [
        self::STATE_AVAILABLE,
        self::STATE_PREWIRED,
        self::STATE_AWAITING_CEASE,
        self::STATE_CEASED,
    ];

    /**
     * Array STATES for available
     */
    public static $AVAILABLE_FOR_ALLOCATION_STATES = [
        self::STATE_AVAILABLE,
        self::STATE_PREWIRED,
    ];


    /**
     * Array $CHARGEABLES
     */
    public static $CHARGEABLES = [
        self::CHARGEABLE_YES            => "Yes",
        self::CHARGEABLE_NO             => "No",
        self::CHARGEABLE_HALF           => "Half",
        self::CHARGEABLE_OTHER          => "Other"
    ];

    /**
     * Array $CHARGEABLES
     */
    public static $OWNED_BY = [
        self::OWNED_CUST                => "Customer",
        self::OWNED_IXP                 => "IXP",
        self::OWNED_SERV_PRO            => "Service Provider",
        self::OWNED_DATA_CENTER         => "Data Center",
        self::OWNED_OTHER               => "Other",
    ];


    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var integer
     */
    private $number;

    /**
     * @var integer
     */
    private $state = self::STATE_AVAILABLE;

    /**
     * @var string
     */
    private $colo_circuit_ref = '';

    /**
     * @var string
     */
    private $colo_billing_ref = '';

    /**
     * @var string
     */
    private $ticket_ref = '';


    /**
     * @var string
     */
    private $notes = '';

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
    private $internal_use = false;

    /**
     * @var int
     */
    private $chargeable;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $duplexSlavePorts;

    /**
     * @var \Entities\PatchPanelPort
     */
    private $duplexMasterPort;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $patchPanelPortFiles;

    /**
     * @var string
     */
    private $private_notes = '';

    /**
     * @var integer
     */
    private $owned_by = self::OWNED_CUST;

    /**
     * @var string
     */
    private $loa_code = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->patchPanelPortHistory = new ArrayCollection();
    }


    /**
     * Set description
     *
     * @param string $description
     *
     * @return PatchPanelPort
     */
    public function setDescription(string $description): PatchPanelPort
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return PatchPanelPort
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get name
     *
     * @return integer
     */
    public function getName()
    {
        $name = $this->getPatchPanel()->getPortPrefix() . $this->getNumber();
        if( $this->hasSlavePort() ) {
            $name .= '/' . $this->getDuplexSlavePortName() . ' ';
            $name .= '(' . ( $this->getNumber() % 2 ? ( floor( $this->getNumber() / 2 ) ) + 1 : $this->getNumber() / 2 ) . ')';
        }
        return $name;
    }

    /**
     * Get name
     *
     * @return integer
     */
    public function getPrefix()
    {
        return $this->getPatchPanel()->getPortPrefix();
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
     * Get css class state
     *
     * @return integer
     */
    public function getStateCssClass()
    {
        if( Auth::getUser()->isSuperUser() ){
            if( $this->isAvailableForUse() or $this->isStatePrewired()):
                $class = 'success';
            elseif($this->isStateAwaitingXConnect()):
                $class = 'warning';
            elseif($this->isStateConnected()):
                $class = 'danger';
            else:
                $class = 'info';
            endif;
        } else {
            if( $this->isStateConnected() ):
                $class = 'success';
            elseif($this->isStateAwaitingCease()):
                $class = 'warning';
            elseif($this->isStateAwaitingXConnect()):
                $class = 'danger';
            else:
                $class = 'default';
            endif;
        }


        return $class;
    }

    /**
     * Set colo_circuit_ref
     *
     * @param string $colo_circuit_ref
     *
     * @return PatchPanelPort
     */
    public function setColoCircuitRef($colo_circuit_ref): PatchPanelPort
    {
        $this->colo_circuit_ref = $colo_circuit_ref;
        return $this;
    }

    /**
     * Get colo_circuit_ref
     *
     * @return string
     */
    public function getColoCircuitRef()
    {
        return $this->colo_circuit_ref ?? '';
    }

    /**
     * Set colo_billing_ref
     *
     * @param string $colo_billing_ref
     *
     * @return PatchPanelPort
     */
    public function setColoBillingRef( $colo_billing_ref ): PatchPanelPort
    {
        $this->colo_billing_ref = $colo_billing_ref ?? '';
        return $this;
    }

    /**
     * Get colo_billing_ref
     *
     * @return string
     */
    public function getColoBillingRef()
    {
        return $this->colo_billing_ref ?? '';
    }

    /**
     * Set ticket_ref
     *
     * @param string $ticket_ref
     *
     * @return PatchPanelPort
     */
    public function setTicketRef($ticket_ref): PatchPanelPort
    {
        $this->ticket_ref = $ticket_ref;
        return $this;
    }

    /**
     * Get ticket_ref
     *
     * @return string
     */
    public function getTicketRef()
    {
        return $this->ticket_ref ?? '';
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
        return $this->notes ?? '';
    }

    /**
     * Get notes using parse down
     *
     * @return string
     */
    public function getNotesParseDown()
    {
        return @parsedown( $this->notes );
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
     * Get assignedAt formated
     *
     * @return string
     */
    public function getAssignedAtFormated() {
        return $this->getAssignedAt() ? $this->getAssignedAt()->format('Y-m-d') : null;
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
     * Get connectedAt formated
     *
     * @return string
     */
    public function getConnectedAtFormated() {
        return $this->getConnectedAt() ? $this->getConnectedAt()->format('Y-m-d') : null;
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
     * Get ceaseRequestedAt formated
     *
     * @return string
     */
    public function getCeaseRequestedAtFormated() {
        return $this->getCeaseRequestedAt() ? $this->getCeaseRequestedAt()->format('Y-m-d') : null;
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
     * Get ceasedAt formated
     *
     * @return string
     */
    public function getCeasedAtFormated()
    {
        return $this->getCeasedAt() ? $this->getCeasedAt()->format('Y-m-d') : null;
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
     * Get lastStateChange
     *
     * @return string
     */
    public function getLastStateChangeFormated()
    {
        return $this->getLastStateChange() ? $this->getLastStateChange()->format('Y-m-d') : null;
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
        return  $this->internal_use;
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
        return isset( self::$CHARGEABLES[ $this->chargeable ] ) ? $this->chargeable : self::CHARGEABLE_NO;

    }

    /**
     * Set privateNotes
     *
     * @param string $privateNotes
     *
     * @return PatchPanelPort
     */
    public function setPrivateNotes($privateNotes)
    {
        $this->private_notes = $privateNotes;

        return $this;
    }

    /**
     * Get privateNotes
     *
     * @return string
     */
    public function getPrivateNotes()
    {
        return $this->private_notes ?? '';
    }

    /**
     * Get private notes using parseDown
     *
     * @return string
     */
    public function getPrivateNotesParseDown()
    {
        return @parsedown( $this->private_notes );
    }

    /**
     * Set ownedBy
     *
     * @param integer $ownedBy
     *
     * @return PatchPanelPort
     */
    public function setOwnedBy($ownedBy)
    {
        $this->owned_by = $ownedBy;

        return $this;
    }

    /**
     * Get ownedBy
     *
     * @return integer
     */
    public function getOwnedBy()
    {
        return $this->owned_by;
    }

    /**
     * Get ownedBy
     *
     * @return integer
     */
    public function getLoaCode()
    {
        return $this->loa_code;
    }

    /**
     * Set ownedBy
     *
     * @param string $loa_code
     * @return PatchPanelPort
     */
    public function setLoaCode(string $loa_code): PatchPanelPort
    {
        $this->loa_code = $loa_code;

        return $this;
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
    public function setSwitchPort(SwitchPort $switchPort = null)
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
     * Get switchPort ID
     *
     * @return int
     */
    public function getSwitchPortId() {
        return $this->getSwitchPort() ?  $this->getSwitchPort()->getId() : null;
    }

    /**
     * Get switchPort Name
     *
     * @return string
     */
    public function getSwitchPortName() {
        return $this->getSwitchPort() ?  $this->getSwitchPort()->getName() : null;
    }

    /**
     * Allow to know if a patch panel port has a switch port set
     *
     * @return bool
     */
    public function getHasSwitchPort(): bool {
        return $this->getSwitchPort() !== null;
    }

    /**
     * Allow to know if a patch panel port has a switch port set
     *
     * @return string
     */
    public function getPhysicalInterfaceState()
    {
        $switchPort = $this->getSwitchPort();
        if($switchPort != null){
            $physicalInterface = $switchPort->getPhysicalInterface();
            if($physicalInterface != null){
                return $physicalInterface->getStatus();
            }
        }

        return 0;
    }

    /**
     * Allow to know if a patch panel port has a switch port set
     *
     * @return string
     */
    public function getPhysicalInterfaceStateLabel()
    {
        $switchPort = $this->getSwitchPort();
        if($switchPort != null){
            $physicalInterface = $switchPort->getPhysicalInterface();
            if($physicalInterface != null){
                return $physicalInterface->resolveStatus();
            }
        }

        return 0;
    }



    /**
     * Add patchPanelPortHistory
     *
     * @param \Entities\PatchPanelPortHistory $patchPanelPortHistory
     *
     * @return PatchPanelPort
     */
    public function addPatchPanelPortHistory(PatchPanelPortHistory $patchPanelPortHistory)
    {
        $this->patchPanelPortHistory[] = $patchPanelPortHistory;
        return $this;
    }

    /**
     * Remove patchPanelPortHistory
     *
     * @param \Entities\PatchPanelPortHistory $patchPanelPortHistory
     */
    public function removePatchPanelPortHistory(PatchPanelPortHistory $patchPanelPortHistory)
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
     * Get patchPanelPortHistory
     *
     * @return array
     */
    public function getPatchPanelPortHistoryMaster()
    {
        $array = [];
        foreach( $this->patchPanelPortHistory as $history ){
            if( $history->getDuplexMasterPort() == null ) {
                $array[] = $history;
            }
        }
        return $array;
    }

    /**
     * Get number of patchPanelPortHistory which are not slave ports
     *
     * @return int
     */
    public function getMasterHistoryCount(): int {
        $cnt = 0;

        foreach( $this->getPatchPanelPortHistory() as $ppph ) {
            if( !$ppph->getDuplexMasterPort() ) {
                $cnt++;
            }
        }

        return $cnt;
    }

    /**
     * Add duplexSlavePort
     *
     * @param \Entities\PatchPanelPort $duplexSlavePort
     *
     * @return PatchPanelPort
     */
    public function addDuplexSlavePort(PatchPanelPort $duplexSlavePort)
    {
        $this->duplexSlavePorts[] = $duplexSlavePort;

        return $this;
    }

    /**
     * Remove duplexSlavePort
     *
     * @param \Entities\PatchPanelPort $duplexSlavePort
     */
    public function removeDuplexSlavePort(PatchPanelPort $duplexSlavePort)
    {
        $this->duplexSlavePorts->removeElement($duplexSlavePort);
    }

    /**
     * Get duplexSlavePorts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDuplexSlavePorts()
    {
        return $this->duplexSlavePorts;
    }

    /**
     * Is this port the master in a duplex port group?
     *
     * @return bool
     */
    public function hasSlavePort(): bool {
        return count( $this->getDuplexSlavePorts() ) > 0;
    }

    /**
     * Get duplexSlavePorts
     *
     * @return PatchPanelPort
     */
    public function getDuplexSlavePort()
    {
        if($this->hasSlavePort()){
            foreach($this->getDuplexSlavePorts() as $slave){
                return $slave;
            }
        }
        return null;
    }

    /**
     * Get duplexSlavePort name
     *
     * @return string
     */
    public function getDuplexSlavePortName()
    {
        if($this->getDuplexSlavePort() != null){
            return $this->getDuplexSlavePort()->getName();
        }
        else{
            return null;
        }
    }

    /**
     * Get duplexSlavePort id
     *
     * @return int
     */
    public function getDuplexSlavePortId()
    {
        if($this->getDuplexSlavePort() != null){
            return $this->getDuplexSlavePort()->getId();
        }
        else{
            return null;
        }
    }

    /**
     * Set duplexMasterPort
     *
     * @param \Entities\PatchPanelPort $duplexMasterPort
     *
     * @return PatchPanelPort
     */
    public function setDuplexMasterPort(PatchPanelPort $duplexMasterPort = null)
    {
        $this->duplexMasterPort = $duplexMasterPort;

        return $this;
    }

    /**
     * Get duplexMasterPort
     *
     * @return \Entities\PatchPanelPort
     */
    public function getDuplexMasterPort()
    {
        return $this->duplexMasterPort;
    }

    /**
     * Set patchPanel
     *
     * @param \Entities\PatchPanel $patchPanel
     *
     * @return PatchPanelPort
     */
    public function setPatchPanel(PatchPanel $patchPanel = null)
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
     * @param Customer $customer
     *
     * @return PatchPanelPort
     */
    public function setCustomer(Customer $customer = null)
    {
        $this->customer = $customer;

        if( $customer != null && !$this->getLoaCode() ) {
            $this->setLoaCode(str_random(25));
        } else if( $customer == null ) {
            $this->setLoaCode('');
        }

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
     * Get customer
     *
     * @return \Entities\Customer
     */
    public function getCabinet()
    {
        return $this->getPatchPanel()->getCabinet();
    }

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId()
    {
        return ($this->getCustomer() != null) ? $this->getCustomer()->getId() : null ;
    }

    /**
     * Get switcher ID
     *
     * @return int
     */
    public function getSwitchId()
    {
        return ($this->getSwitchPort() != null) ? $this->getSwitchPort()->getSwitcher()->getId() : null ;
    }

    /**
     * Get switcher Name
     *
     * @return string
     */
    public function getSwitchName()  {
        return( $this->getSwitchPort() != null ) ? $this->getSwitchPort()->getSwitcher()->getName() : null;
    }

    /**
     * Get customer Name
     *
     * @return string
     */
    public function getCustomerName()
    {
        return ($this->getCustomer() != null) ? $this->getCustomer()->getAbbreviatedName() : null ;
    }


    /**
     * Is this port available for use?
     *
     * It is if its state is one of: available, ceased, awaiting cease, prewired.
     *
     * @return bool
     */
    public function isAvailableForUse(): bool {
        return in_array( $this->getState(), self::$AVAILABLE_STATES );
    }

    /**
     * Is this port os allocated?
     *
     * It is if its state is one of: awaiting xconnect, connected, awaiting cease.
     *
     * @return bool
     */
    public function isAllocated(): bool {
        return in_array( $this->getState(), self::$ALLOCATED_STATES );
    }


    /**
     * Get appropriate states for allocating a port / determining if a port is allocated.
     *
     * Returns array of elements: state ID => state description
     *
     * @return array
     */
    public static function getAllocatedStatesWithDescription() {
        $states = [];
        foreach( self::$ALLOCATED_STATES as $i ) {
            $states[$i] = self::$STATES[$i];
        }
        return $states;
    }

    public function setDuplexPort( PatchPanelPort $duplexPort ){
        $duplexPort->setDuplexMasterPort($this);
        $this->addDuplexSlavePort( $duplexPort );
        D2EM::flush();

        return $duplexPort;
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$STATES array (or 'Unknown')
     * @return string
     */
    public function resolveStates(): string {
        return self::$STATES[ $this->getState() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$CHARGEABLES array (or 'Unknown')
     * @return string
     */
    public function resolveChargeable(): string {
        return self::$CHARGEABLES[ $this->getChargeable() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$STATES array (or 'Unknown')
     * @return string
     */
    public function resolveOwnedBy(): string {
        return self::$OWNED_BY[ $this->getOwnedBy() ] ?? 'Unknown';
    }


    public function getCustomerForASwitchPort(): Customer {
        if( $this->getSwitchPort() && ( $pi = $this->getSwitchPort()->getPhysicalInterface() ) ) {
            return $pi->getVirtualInterface()->getCustomer();
        }
        return null;
    }

    /**
     * Is this port part of a duplex port group?
     *
     * @return bool
     */
    public function isDuplexPort(): bool {
        return $this->getDuplexMasterPort() !== null || count( $this->getDuplexSlavePorts() ) > 0;
    }

    /**
     * Is this port has files
     *
     * @return bool
     */
    public function hasFiles(): bool {
        return count( $this->getPatchPanelPortFiles() ) > 0;
    }


    /**
     * Is this port has public files
     *
     * @return bool
     */
    public function hasPublicFiles(): bool {
        return count( $this->getPatchPanelPortPublicFiles() ) > 0;
    }

    /**
     * Reset the port to clear and available (including slave ports)
     *
     * @return PatchPanelPort
     */
    public function resetPatchPanelPort(): PatchPanelPort {
        foreach( $this->getDuplexSlavePorts() as $pppsp ) {
            $pppsp->resetPatchPanelPort();
            $this->removeDuplexSlavePort( $pppsp );
        }

        return $this->setState(PatchPanelPort::STATE_AVAILABLE)
            ->setDescription('')
            ->setLastStateChange(new \DateTime)
            ->setColoCircuitRef('')
            ->setTicketRef('')
            ->setNotes('')
            ->setPrivateNotes('')
            ->setAssignedAt(null)
            ->setConnectedAt(null)
            ->setCeaseRequestedAt(null)
            ->setCeasedAt(null)
            ->setInternalUse(false)
            ->setChargeable(false)
            ->setCustomer(null)
            ->setLoaCode('')
            ->setSwitchPort(null)
            ->setDuplexMasterPort(null);
    }

    /**
     * Add patchPanelPortFile
     *
     * @param \Entities\PatchPanelPortFile $patchPanelPortFile
     *
     * @return PatchPanelPort
     */
    public function addPatchPanelPortFile(PatchPanelPortFile $patchPanelPortFile)
    {
        $this->patchPanelPortFiles[] = $patchPanelPortFile;
        return $this;
    }

    /**
     * Remove patchPanelPortFile
     *
     * @param PatchPanelPortFile $patchPanelPortFile
     */
    public function removePatchPanelPortFile(PatchPanelPortFile $patchPanelPortFile)
    {
        $this->patchPanelPortFiles->removeElement($patchPanelPortFile);
    }

    /**
     * Get patchPanelPortFiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPatchPanelPortFiles()
    {
        return $this->patchPanelPortFiles;
    }

    /**
     * Get patchPanelPortFiles
     *
     * @return array
     */
    public function getPatchPanelPortPublicFiles()
    {
        $array = [];
        foreach($this->patchPanelPortFiles as $file){
            if(!$file->getIsPrivate()){
                $array[] = $file;
            }

        }

        return $array;
    }


    /**
     * Is the state STATE_AVAILABLE?
     *
     * @return bool
     */
    public function isStateAvailable(): bool {
        return $this->getState() === self::STATE_AVAILABLE;
    }

    /**
     * Is the state STATE_AWAITING_XCONNECT?
     *
     * @return bool
     */
    public function isStateAwaitingXConnect(): bool {
        return $this->getState() === self::STATE_AWAITING_XCONNECT;
    }

    /**
     * Is the state STATE_CONNECTED?
     *
     * @return bool
     */
    public function isStateConnected(): bool {
        return $this->getState() === self::STATE_CONNECTED;
    }

    /**
     * Is the state STATE_AWAITING_CEASE?
     *
     * @return bool
     */
    public function isStateAwaitingCease(): bool {
        return $this->getState() === self::STATE_AWAITING_CEASE;
    }

    /**
     * Is the state STATE_CEASED?
     *
     * @return bool
     */
    public function isStateCeased(): bool {
        return $this->getState() === self::STATE_CEASED;
    }

    /**
     * Is the state STATE_BROKEN?
     *
     * @return bool
     */
    public function isStateBroken(): bool {
        return $this->getState() === self::STATE_BROKEN;
    }

    /**
     * Is the state STATE_RESERVED?
     *
     * @return bool
     */
    public function isStateReserved(): bool {
        return $this->getState() === self::STATE_RESERVED;
    }

    /**
     * Is the state STATE_RESERVED?
     *
     * @return bool
     */
    public function isStatePrewired(): bool {
        return $this->getState() === self::STATE_PREWIRED;
    }

    /**
     * Is the state STATE_OTHER?
     *
     * @return bool
     */
    public function isStateOther(): bool {
        return $this->getState() === self::STATE_OTHER;
    }


    /**
     * Convert this object to an array
     *
     * @param bool $deep Include subobjects
     * @return array
     */
    public function toArray( bool $deep = false ): array {
        $a = [
            'id'               => $this->getId(),
            'patchPanelId'     => $this->getPatchPanel() ? $this->getPatchPanel()->getId() : null,
            'switchPortId'     => $this->getSwitchPort() ? $this->getSwitchPort()->getId() : null,
            'customerId'       => $this->getCustomer()   ? $this->getCustomer()->getId()   : null,
            'number'           => $this->getNumber(),
            'name'             => $this->getName(),
            'coloRef'          => $this->getColoCircuitRef(),
            'coloBillingRef'   => $this->getColoBillingRef(),
            'ticketRef'        => $this->getTicketRef(),
            'stateId'          => $this->getState(),
            'state'            => $this->resolveStates(),
            'notes'            => clean( $this->getNotes() ),
            'privateNotes'     => clean( $this->getPrivateNotes() ),
            'assignedAt'       => $this->getAssignedAt(),
            'connectedAt'      => $this->getConnectedAt(),
            'ceaseRequestedAt' => $this->getCeaseRequestedAt(),
            'ceasedAt'         => $this->getCeasedAt(),
            'internalUse'      => $this->getInternalUse(),
            'chargeableId'     => $this->getChargeable(),
            'chargeable'       => $this->resolveChargeable(),
            'isDuplex'         => $this->isDuplexPort(),
            'isDuplexMaster'   => $this->getDuplexMasterPort() ? false : true,
            'duplexMasterId'   => $this->getDuplexMasterPort() ? $this->getDuplexMasterPort()->getId() : null,
            'duplexSlaveId'    => $this->getDuplexSlavePort()  ? $this->getDuplexSlavePort()->getId()  : null,
            'loaCode'          => $this->getLoaCode(),
            'ownedById'        => $this->getOwnedBy(),
            'ownedBy'          => $this->resolveOwnedBy(),
            'isHistorical'     => false,
            'files'            => [],
        ];

        foreach( $this->getPatchPanelPortFiles() as $f ) {
            $f = [
                'id'         => $f->getId(),
                'name'       => $f->getName(),
                'type'       => $f->getType(),
                'uploadedAt' => $f->getUploadedAt(),
                'uploadedBy' => $f->getUploadedBy(),
                'size'       => $f->getSize(),
                'private'    => $f->getIsPrivate(),
            ];
            $a['files'][] = $f;
        }

        if( $deep ) {
            if( $a['patchPanelId'] ) {
                $a['patchPanel'] = $this->getPatchPanel()->toArray();
            }

            // we're not going to give all the objects - just what makes sense for utilities of this
            if( $a['switchPortId'] ) {
                $a['switchPort']['switchId'] = $this->getSwitchPort()->getSwitcher()->getId();
                $a['switchPort']['switch']   = $this->getSwitchPort()->getSwitcher()->getName();
                $a['switchPort']['name']     = $this->getSwitchPort()->getName();
                $a['switchPort']['ifName']   = $this->getSwitchPort()->getIfName();

                if( $this->getSwitchPort()->getPhysicalInterface() ) {
                    $a['switchPort']['physicalInterfaceId'] = $this->getSwitchPort()->getPhysicalInterface()->getId();
                    $a['switchPort']['physicalInterface']['statusId'] = $this->getSwitchPort()->getPhysicalInterface()->getStatus();
                    $a['switchPort']['physicalInterface']['status']   = $this->getSwitchPort()->getPhysicalInterface()->resolveStatus();

                } else {
                    $a['switchPort']['physicalInterfaceId'] = null;
                }
            }
        }

        return $a;
    }

    /**
     * Get patch panel details as JSON-compatibale array
     * @param bool $deep Include subobjects
     * @return array
     */
    public function jsonArray( bool $deep = false ): array {
        $a = $this->toArray($deep);

        $a['assignedAt']       = $a['assignedAt']       ? Carbon::instance( $a['assignedAt']       )->toIso8601String() : null;
        $a['connectedAt']      = $a['connectedAt']      ? Carbon::instance( $a['connectedAt']      )->toIso8601String() : null;
        $a['ceaseRequestedAt'] = $a['ceaseRequestedAt'] ? Carbon::instance( $a['ceaseRequestedAt'] )->toIso8601String() : null;
        $a['ceasedAt']         = $a['ceasedAt']         ? Carbon::instance( $a['ceasedAt']         )->toIso8601String() : null;

        foreach( $a['files'] as $i => $f ) {
            $a['files'][$i]['uploadedAt'] = Carbon::instance( $a['files'][$i]['uploadedAt'] )->toIso8601String();
        }

        return $a;
    }

    /**
     * Get patch panel details as JSON
     * @param bool $deep Include subobjects
     * @return string
     */
    public function json( bool $deep = false ): string {
        return json_encode( $this->jsonArray($deep), JSON_PRETTY_PRINT );
    }


    /**
     * A public facing reference for this. Essentially the ID.
     *
     * @return string
     */
    public function getCircuitReference(): string {
        return sprintf( "PPP-%05d", $this->getId() );
    }


}
