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

use Doctrine\Common\Collections\Collection;
use Entities\{
    PatchPanelPort              as PatchPanelPortEntities,
    PatchPanelPortHistory       as PatchPanelPortHistoryEntity,
    PatchPanelPortHistoryFile   as PatchPanelPortHistoryFileEntity
};

use Doctrine\Common\Collections\ArrayCollection;

use DateTime, Parsedown;

/**
 * PatchPanelPortHistory
 */
class PatchPanelPortHistory
{
    private $created_at;
    private $updated_at;
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
    private $state;

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
    private $ticket_ref;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var DateTime
     */
    private $assigned_at;

    /**
     * @var DateTime
     */
    private $connected_at;

    /**
     * @var DateTime
     */
    private $cease_requested_at;

    /**
     * @var DateTime
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
     * @var integer
     */
    private $cust_id;

    /**
     * @var string
     */
    private $customer;

    /**
     * @var string
     */
    private $switchport;

    /**
     * @var PatchPanelPortEntities
     */
    private $patchPanelPort;

    /**
     * @var Collection
     */
    private $duplexSlavePorts;

    /**
     * @var PatchPanelPortHistoryEntity
     */
    private $duplexMasterPort;

    /**
     * @var Collection
     */
    private $patchPanelPortHistoryFiles;

    /**
     * Constructor
     */
    public function __construct() {
        $this->duplexSlavePorts = new ArrayCollection();
    }


    /**
     * Set description
     *
     * @param string $description
     *
     * @return PatchPanelPortHistory
     */
    public function setDescription(string $description): PatchPanelPortHistory
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
     * Get the patch panel
     *
     * @return PatchPanel
     */
    public function getPatchPanel(): PatchPanel
    {
        return $this->getPatchPanelPort()->getPatchPanel();
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
     * @return string
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
     * @return PatchPanelPortHistory
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
     * Set colo_billing_ref
     *
     * @param string $colo_billing_ref
     *
     * @return PatchPanelPortHistory
     */
    public function setColoBillingRef( string $colo_billing_ref ): PatchPanelPortHistory
    {
        $this->colo_billing_ref = $colo_billing_ref;
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
     * @return PatchPanelPortHistory
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
     * @param DateTime $assignedAt
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
     * @return DateTime
     */
    public function getAssignedAt() {
        return $this->assigned_at;
    }

    /**
     * Get assignedAt formated
     *
     * @return string
     */
    public function getAssignedAtFormated() {
        return ( $this->getAssignedAt() == null ) ? $this->getAssignedAt() : $this->getAssignedAt()->format( 'Y-m-d' );
    }

    /**
     * Set connectedAt
     *
     * @param DateTime $connectedAt
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
     * @return DateTime
     */
    public function getConnectedAt() {
        return $this->connected_at;
    }

    /**
     * Get connectedAt formated
     *
     * @return string
     */
    public function getConnectedAtFormated() {
        return ( $this->getConnectedAt() == null ) ? $this->getConnectedAt() : $this->getConnectedAt()->format( 'Y-m-d' );
    }

    /**
     * Set ceaseRequestedAt
     *
     * @param DateTime $ceaseRequestedAt
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
     * @return DateTime
     */
    public function getCeaseRequestedAt() {
        return $this->cease_requested_at;
    }

    /**
     * Get ceaseRequestedAt formated
     *
     * @return string
     */
    public function getCeaseRequestedAtFormated() {
        return ( $this->getCeaseRequestedAt() == null ) ? $this->getCeaseRequestedAt() : $this->getCeaseRequestedAt()->format( 'Y-m-d' );
    }

    /**
     * Set ceasedAt
     *
     * @param DateTime $ceasedAt
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
     * @return DateTime
     */
    public function getCeasedAt() {
        return $this->ceased_at;
    }

    /**
     * Get ceasedAt formated
     *
     * @return string
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
     * @return int|null
     */
    public function getCustId(): ?int
    {
        return $this->cust_id;
    }

    /**
     * @param int|null $cust_id
     *
     * @return PatchPanelPortHistory
     */
    public function setCustId( ?int $cust_id )
    {
        $this->cust_id = $cust_id;

        return $this;
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
     * @param PatchPanelPortEntities $patchPanelPort
     *
     * @return PatchPanelPortHistory
     */
    public function setPatchPanelPort( PatchPanelPortEntities $patchPanelPort = null ) {
        $this->patchPanelPort = $patchPanelPort;

        return $this;
    }

    /**
     * Get patchPanelPort
     *
     * @return PatchPanelPortEntities
     */
    public function getPatchPanelPort() {
        return $this->patchPanelPort;
    }
    
    /**
     * Add duplexSlavePort
     *
     * @param PatchPanelPortHistoryEntity $duplexSlavePort
     *
     * @return PatchPanelPortHistory
     */
    public function addDuplexSlavePort( PatchPanelPortHistoryEntity $duplexSlavePort ) {
        $this->duplexSlavePorts[] = $duplexSlavePort;

        return $this;
    }

    /**
     * Remove duplexSlavePort
     *
     * @param PatchPanelPortHistoryEntity $duplexSlavePort
     */
    public function removeDuplexSlavePort( PatchPanelPortHistoryEntity $duplexSlavePort ) {
        $this->duplexSlavePorts->removeElement( $duplexSlavePort );
    }

    /**
     * Get duplexSlavePorts
     *
     * @return Collection
     */
    public function getDuplexSlavePorts() {
        return $this->duplexSlavePorts;
    }

    /**
     * Set duplexMasterPort
     *
     * @param PatchPanelPortHistoryEntity $duplexMasterPort
     *
     * @return PatchPanelPortHistory
     */
    public function setDuplexMasterPort( PatchPanelPortHistoryEntity $duplexMasterPort = null ) {
        $this->duplexMasterPort = $duplexMasterPort;

        return $this;
    }

    /**
     * Get duplexMasterPort
     *
     * @return PatchPanelPortHistoryEntity
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
     * Get duplexSlavePort
     *
     * NOTE: This all assumes single duplex slave port
     *
     * @return PatchPanelPortHistory
     */
    public function getDuplexSlavePort() {
        if( $this->hasSlavePort() ) {
            foreach( $this->getDuplexSlavePorts() as $slave ) {
                return $slave;
            }
        }

        return null;
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
     * @param PatchPanelPortHistoryFileEntity $patchPanelPortHistoryFile
     *
     * @return PatchPanelPortHistory
     */
    public function addPatchPanelPortHistoryFile( PatchPanelPortHistoryFileEntity $patchPanelPortHistoryFile ) {
        $this->patchPanelPortHistoryFiles[] = $patchPanelPortHistoryFile;
        return $this;
    }

    /**
     * Remove patchPanelPortHistoryFile
     *
     * @param PatchPanelPortHistoryFileEntity $patchPanelPortHistoryFile
     */
    public function removePatchPanelPortHistoryFile( PatchPanelPortHistoryFileEntity $patchPanelPortHistoryFile ) {
        $this->patchPanelPortHistoryFiles->removeElement( $patchPanelPortHistoryFile );
    }


    /**
     * Get patchPanelPortHistoryFiles
     *
     * @return Collection
     */
    public function getPatchPanelPortHistoryFile() {
        return $this->patchPanelPortHistoryFiles;
    }


    /**
     * Populate this history entity with details from a patch panel port.
     *
     * @param PatchPanelPort $ppp
     *
     * @return PatchPanelPortHistory
     *
     * @throws
     */
    public function setFromPatchPanelPort( PatchPanelPort $ppp ): PatchPanelPortHistory {

        return $this->setPatchPanelPort( $ppp )
                    ->setDescription( $ppp->getDescription() )
                    ->setNumber( $ppp->getNumber() )
                    ->setState( $ppp->getState() )
                    ->setColoCircuitRef( $ppp->getColoCircuitRef() )
                    ->setColoBillingRef( $ppp->getColoBillingRef() )
                    ->setTicketRef( $ppp->getTicketRef() )
                    ->setNotes( $ppp->getNotes() )
                    ->setPrivateNotes( $ppp->getPrivateNotes() )
                    ->setAssignedAt( $ppp->getAssignedAt() )
                    ->setConnectedAt( $ppp->getConnectedAt() )
                    ->setCeaseRequestedAt( $ppp->getCeaseRequestedAt() )
                    ->setCeasedAt( $ppp->getCeasedAt() ? $ppp->getCeasedAt() : new DateTime )
                    ->setInternalUse( $ppp->getInternalUse() )
                    ->setChargeable( $ppp->getChargeable() )
                    ->setOwnedBy( $ppp->getOwnedBy() )
                    ->setCustId( $ppp->getCustomer() ? $ppp->getCustomer()->getId() : null )
                    ->setCustomer( $ppp->getCustomer() ? $ppp->getCustomer()->getName() : '' )
                    ->setSwitchport( $ppp->getSwitchPort() ? $ppp->getSwitchPort()->getSwitcher()->getName() . '::' . $ppp->getSwitchPort()->getName() : '' );
    }

}
