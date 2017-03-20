<?php

namespace Entities;

Use Parsedown;
Use D2EM;
/**
 * PatchPanelPortHistory
 */
class PatchPanelPortHistory
{
    /**
     * @var integer
     */
    private $id;
    /**
     * @var integer
     */
    private $number;

    /**
     * @var integer
     */
    private $state;

    /**
     * @var string
     */
    private $colo_circuit_ref;

    /**
     * @var string
     */
    private $ticket_ref;

    /**
     * @var string
     */
    private $notes;

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
     * @var boolean
     */
    private $internal_use = '0';

    /**
     * @var boolean
     */
    private $chargeable = '0';
    /**
     * @var string
     */
    private $private_notes;

    /**
     * @var integer
     */
    private $owned_by = '0';

    /**
     * @var string
     */
    private $customer;

    /**
     * @var string
     */
    private $switchport;

    /**
     * @var \Entities\PatchPanelPort
     */
    private $patchPanelPort;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $duplexSlavePorts;

    /**
     * @var \Entities\PatchPanelPortHistory
     */
    private $duplexMasterPort;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $patchPanelPortHistoryFiles;

    /**
     * Constructor
     */
    public function __construct() {
        $this->duplexSlavePorts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return PatchPanelPortHistory
     */
    public function setNumber( $number ) {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * Get name
     *
     * @return integer
     */
    public function getName() {
        $name = $this->getNumber();
        if( $this->hasSlavePort() ) {
            $name .= '/' . $this->getDuplexSlavePort()->getNumber();
        }
        return $name;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return PatchPanelPortHistory
     */
    public function setState( $state ) {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Set colo_circuit_ref
     *
     * @param string $colo_circuit_ref
     *
     * @return PatchPanelPort
     */
    public function setColoCircuitRef( string $colo_circuit_ref ) {
        $this->colo_circuit_ref = $colo_circuit_ref;
        return $this;
    }

    /**
     * Get colo_circuit_ref
     *
     * @return string
     */
    public function getColoCircuitRef(): string {
        return $this->colo_circuit_ref;
    }

    /**
     * Set ticket_ref
     *
     * @param string $ticket_ref
     *
     * @return PatchPanelPort
     */
    public function setTicketRef( string $ticket_ref ) {
        $this->ticket_ref = $ticket_ref;
        return $this;
    }

    /**
     * Get ticket_ref
     *
     * @return string
     */
    public function getTicketRef(): string {
        return $this->ticket_ref;
    }


    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return PatchPanelPortHistory
     */
    public function setNotes( $notes ) {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes() {
        return $this->notes;
    }

    /**
     * Get notes using parse down
     *
     * @return string
     */
    public function getNotesParseDown() {
        $parseDown = new Parsedown;
        return $parseDown->text( $this->notes );
    }

    /**
     * Set assignedAt
     *
     * @param \DateTime $assignedAt
     *
     * @return PatchPanelPortHistory
     */
    public function setAssignedAt( $assignedAt ) {
        $this->assigned_at = $assignedAt;

        return $this;
    }

    /**
     * Get assignedAt
     *
     * @return \DateTime
     */
    public function getAssignedAt() {
        return $this->assigned_at;
    }

    /**
     * Get assignedAt formated
     *
     * @return \DateTime
     */
    public function getAssignedAtFormated() {
        return ( $this->getAssignedAt() == null ) ? $this->getAssignedAt() : $this->getAssignedAt()->format( 'Y-m-d' );
    }

    /**
     * Set connectedAt
     *
     * @param \DateTime $connectedAt
     *
     * @return PatchPanelPortHistory
     */
    public function setConnectedAt( $connectedAt ) {
        $this->connected_at = $connectedAt;

        return $this;
    }

    /**
     * Get connectedAt
     *
     * @return \DateTime
     */
    public function getConnectedAt() {
        return $this->connected_at;
    }

    /**
     * Get connectedAt formated
     *
     * @return \DateTime
     */
    public function getConnectedAtFormated() {
        return ( $this->getConnectedAt() == null ) ? $this->getConnectedAt() : $this->getConnectedAt()->format( 'Y-m-d' );
    }

    /**
     * Set ceaseRequestedAt
     *
     * @param \DateTime $ceaseRequestedAt
     *
     * @return PatchPanelPortHistory
     */
    public function setCeaseRequestedAt( $ceaseRequestedAt ) {
        $this->cease_requested_at = $ceaseRequestedAt;

        return $this;
    }

    /**
     * Get ceaseRequestedAt
     *
     * @return \DateTime
     */
    public function getCeaseRequestedAt() {
        return $this->cease_requested_at;
    }

    /**
     * Get ceaseRequestedAt formated
     *
     * @return \DateTime
     */
    public function getCeaseRequestedAtFormated() {
        return ( $this->getCeaseRequestedAt() == null ) ? $this->getCeaseRequestedAt() : $this->getCeaseRequestedAt()->format( 'Y-m-d' );
    }

    /**
     * Set ceasedAt
     *
     * @param \DateTime $ceasedAt
     *
     * @return PatchPanelPortHistory
     */
    public function setCeasedAt( $ceasedAt ) {
        $this->ceased_at = $ceasedAt;

        return $this;
    }

    /**
     * Get ceasedAt
     *
     * @return \DateTime
     */
    public function getCeasedAt() {
        return $this->ceased_at;
    }

    /**
     * Get ceasedAt formated
     *
     * @return \DateTime
     */
    public function getCeasedAtFormated() {
        return ( $this->getCeasedAt() == null ) ? $this->getCeasedAt() : $this->getCeasedAt()->format( 'Y-m-d' );
    }

    /**
     * Set internalUse
     *
     * @param boolean $internalUse
     *
     * @return PatchPanelPortHistory
     */
    public function setInternalUse( $internalUse ) {
        $this->internal_use = $internalUse;

        return $this;
    }

    /**
     * Get internalUse
     *
     * @return boolean
     */
    public function getInternalUse() {
        return $this->internal_use;
    }

    /**
     * Get internalUse
     *
     * @return boolean
     */
    public function getInternalUseText() {
        return $this->getInternalUse() ? 'Yes' : 'No';
    }

    /**
     * Set chargeable
     *
     * @param boolean $chargeable
     *
     * @return PatchPanelPortHistory
     */
    public function setChargeable( $chargeable ) {
        $this->chargeable = $chargeable;

        return $this;
    }

    /**
     * Get chargeable
     *
     * @return boolean
     */
    public function getChargeable() {
        return $this->chargeable;
    }

    /**
     * Set privateNotes
     *
     * @param string $privateNotes
     *
     * @return PatchPanelPortHistory
     */
    public function setPrivateNotes( $privateNotes ) {
        $this->private_notes = $privateNotes;

        return $this;
    }

    /**
     * Get privateNotes
     *
     * @return string
     */
    public function getPrivateNotes() {
        return $this->private_notes;
    }

    /**
     * Get private notes using parseDown
     *
     * @return string
     */
    public function getPrivateNotesParseDown() {
        $parseDown = new Parsedown;
        return $parseDown->text( $this->private_notes );
    }

    /**
     * Set ownedBy
     *
     * @param integer $ownedBy
     *
     * @return PatchPanelPortHistory
     */
    public function setOwnedBy( $ownedBy ) {
        $this->owned_by = $ownedBy;

        return $this;
    }

    /**
     * Get ownedBy
     *
     * @return integer
     */
    public function getOwnedBy() {
        return $this->owned_by;
    }

    /**
     * Set customer
     *
     * @param string $customer
     *
     * @return PatchPanelPortHistory
     */
    public function setCustomer( $customer ) {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return string
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * Get customer
     *
     * @return string
     */
    public function getCustomerName() {
        return $this->customer;
    }

    /**
     * Get customer
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customer;
    }

    /**
     * Set switchport
     *
     * @param string $switchport
     *
     * @return PatchPanelPortHistory
     */
    public function setSwitchport( $switchport ) {
        $this->switchport = $switchport;

        return $this;
    }

    /**
     * Get switchport
     *
     * @return string
     */
    public function getSwitchport() {
        return $this->switchport;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set patchPanelPort
     *
     * @param \Entities\PatchPanelPort $patchPanelPort
     *
     * @return PatchPanelPortHistory
     */
    public function setPatchPanelPort( \Entities\PatchPanelPort $patchPanelPort = null ) {
        $this->patchPanelPort = $patchPanelPort;

        return $this;
    }

    /**
     * Get patchPanelPort
     *
     * @return \Entities\PatchPanelPort
     */
    public function getPatchPanelPort() {
        return $this->patchPanelPort;
    }

    /**
     * Get patchPanelPort
     *
     * @return \Entities\PatchPanelPort
     */
    public function getPatchPanel() {
        return $this->patchPanelPort;
    }

    /**
     * Get patchPanelPort
     *
     * @return \Entities\PatchPanelPort
     */
    public function getPatchPanel()
    {
        return $this->patchPanelPort;
    }
    /**
     * Add duplexSlavePort
     *
     * @param \Entities\PatchPanelPortHistory $duplexSlavePort
     *
     * @return PatchPanelPortHistory
     */
    public function addDuplexSlavePort( \Entities\PatchPanelPortHistory $duplexSlavePort ) {
        $this->duplexSlavePorts[] = $duplexSlavePort;

        return $this;
    }

    /**
     * Remove duplexSlavePort
     *
     * @param \Entities\PatchPanelPortHistory $duplexSlavePort
     */
    public function removeDuplexSlavePort( \Entities\PatchPanelPortHistory $duplexSlavePort ) {
        $this->duplexSlavePorts->removeElement( $duplexSlavePort );
    }

    /**
     * Get duplexSlavePorts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDuplexSlavePorts() {
        return $this->duplexSlavePorts;
    }

    /**
     * Set duplexMasterPort
     *
     * @param \Entities\PatchPanelPortHistory $duplexMasterPort
     *
     * @return PatchPanelPortHistory
     */
    public function setDuplexMasterPort( \Entities\PatchPanelPortHistory $duplexMasterPort = null ) {
        $this->duplexMasterPort = $duplexMasterPort;

        return $this;
    }

    /**
     * Get duplexMasterPort
     *
     * @return \Entities\PatchPanelPortHistory
     */
    public function getDuplexMasterPort() {
        return $this->duplexMasterPort;
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDuplexSlavePort() {
        if( $this->hasSlavePort() ) {
            foreach( $this->getDuplexSlavePorts() as $slave ) {
                return $slave;
            }
        } else {
            return null;
        }
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$CHARGEABLES array (or 'Unknown')
     * @return string
     */
    public function resolveChargeable(): string {
        return PatchPanelPort::$CHARGEABLES[ $this->getChargeable() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$STATES array (or 'Unknown')
     * @return string
     */
    public function resolveOwnedBy(): string {
        return PatchPanelPort::$OWNED_BY[ $this->getOwnedBy() ] ?? 'Unknown';
    }

    /**
     * Add patchPanelPortHistoryFile
     *
     * @param \Entities\PatchPanelPortHistoryFile $patchPanelPortHistoryFile
     *
     * @return PatchPanelPort
     */
    public function addPatchPanelPortHistoryFile( \Entities\PatchPanelPortHistoryFile $patchPanelPortHistoryFile ) {
        $this->patchPanelPortHistoryFiles[] = $patchPanelPortHistoryFile;
        return $this;
    }

    /**
     * Remove patchPanelPortHistoryFile
     *
     * @param \Entities\PatchPanelPortHistoryFile $patchPanelPortHistoryFile
     */
    public function removePatchPanelPortHistoryFile( \Entities\PatchPanelPortHistoryFile $patchPanelPortHistoryFile ) {
        $this->patchPanelPortHistoryFiles->removeElement( $patchPanelPortHistoryFile );
    }


    /**
     * Get patchPanelPortHistoryFiles
     *
     * @return \Entities\PatchPanelPortHistoryFile
     */
    public function getPatchPanelPortHistoryFile() {
        return $this->patchPanelPortHistoryFiles;
    }


    /**
     * Create a patch panel port history
     * Duplicate all the datas of the current patch panel port in the history table
     *
     * @author     Yann Robin <yann@islandbridgenetworks.ie>
     * @return \Entities\PatchPanelPortHistory
     */
<<<<<<< HEAD
    public static function createHistory($patchPanelPort){
        $pppHistory = new PatchPanelPortHistory();

        $pppHistory->setPatchPanelPort($patchPanelPort);
        $pppHistory->setNumber($patchPanelPort->getNumber());
        $pppHistory->setState($patchPanelPort->getState());
        $pppHistory->setColoCircuitRef($patchPanelPort->getColoCircuitRef());
        $pppHistory->setTicketRef($patchPanelPort->getTicketRef());
        $pppHistory->setNotes($patchPanelPort->getNotes());
        $pppHistory->setPrivateNotes($patchPanelPort->getPrivateNotes());
        $pppHistory->setAssignedAt($patchPanelPort->getAssignedAt());
        $pppHistory->setConnectedAt($patchPanelPort->getConnectedAt());
        $pppHistory->setCeaseRequestedAt($patchPanelPort->getCeaseRequestedAt());
        $pppHistory->setCeasedAt($patchPanelPort->getCeasedAt());
        $pppHistory->setInternalUse($patchPanelPort->getInternalUse());
        $pppHistory->setChargeable($patchPanelPort->getChargeable());
        $pppHistory->setOwnedBy($patchPanelPort->getOwnedBy());
        $pppHistory->setCustomer($patchPanelPort->getCustomerName());
        $pppHistory->setSwitchport($patchPanelPort->getSwitchName().' / '.$patchPanelPort->getSwitchPortName());

        $patchPanelPort->addPatchPanelPortHistory($pppHistory);

        D2EM::persist($pppHistory);
        D2EM::flush();

        if($patchPanelPort->hasSlavePort()){
            $slavePortHistory = clone $pppHistory;
            $slavePortHistory->setDuplexMasterPort($pppHistory);
            D2EM::persist($slavePortHistory);
        }


        if($patchPanelPort->hasSlavePort()){
=======
    public static function createHistory( $patchPanelPort ) {
        $pppHistory = new PatchPanelPortHistory();

        $pppHistory->setPatchPanelPort( $patchPanelPort );
        $pppHistory->setNumber( $patchPanelPort->getNumber() );
        $pppHistory->setState( $patchPanelPort->getState() );
        $pppHistory->setColoCircuitRef( $patchPanelPort->getColoCircuitRef() );
        $pppHistory->setTicketRef( $patchPanelPort->getTicketRef() );
        $pppHistory->setNotes( $patchPanelPort->getNotes() );
        $pppHistory->setPrivateNotes( $patchPanelPort->getPrivateNotes() );
        $pppHistory->setAssignedAt( $patchPanelPort->getAssignedAt() );
        $pppHistory->setConnectedAt( $patchPanelPort->getConnectedAt() );
        $pppHistory->setCeaseRequestedAt( $patchPanelPort->getCeaseRequestedAt() );
        $pppHistory->setCeasedAt( $patchPanelPort->getCeasedAt() );
        $pppHistory->setInternalUse( $patchPanelPort->getInternalUse() );
        $pppHistory->setChargeable( $patchPanelPort->getChargeable() );
        $pppHistory->setOwnedBy( $patchPanelPort->getOwnedBy() );
        $pppHistory->setCustomer( $patchPanelPort->getCustomerName() );
        $pppHistory->setSwitchport( $patchPanelPort->getSwitchName() . ' / ' . $patchPanelPort->getSwitchPortName() );

        $patchPanelPort->addPatchPanelPortHistory( $pppHistory );

        D2EM::persist( $pppHistory );
        D2EM::flush();

        if( $patchPanelPort->hasSlavePort() ) {
            $slavePortHistory = clone $pppHistory;
            $slavePortHistory->setNumber( $patchPanelPort->getDuplexSlavePort()->getNumber() );
            $slavePortHistory->setDuplexMasterPort( $pppHistory );
            D2EM::persist( $slavePortHistory );
        }


        if( $patchPanelPort->hasSlavePort() ) {
>>>>>>> a87e821b2d0df0344874f1c71b12c4cad915a0b0
            $slavePort = $patchPanelPort->getDuplexSlavePort();
            $slavePort->resetPatchPanelPort();
        }
        $patchPanelPort->resetPatchPanelPort();

        D2EM::flush();

        return $pppHistory;
    }

}
