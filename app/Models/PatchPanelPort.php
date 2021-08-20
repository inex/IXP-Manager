<?php

namespace IXP\Models;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
*/

use Auth, DB, Eloquent, Storage;

use Illuminate\Database\Eloquent\{
    Builder,
    Model
};

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};

use IXP\Mail\PatchPanelPort\{
    Cease   as CeaseMail,
    Connect as ConnectMail,
    Info    as InfoMail,
    Loa     as LoaMail
};
use IXP\Exceptions\GeneralException;

use IXP\Traits\Observable;

/**
 * IXP\Models\PatchPanelPort
 *
 * @property int $id
 * @property int|null $switch_port_id
 * @property int|null $patch_panel_id
 * @property int|null $customer_id
 * @property int $state
 * @property string|null $notes
 * @property string|null $assigned_at
 * @property string|null $connected_at
 * @property string|null $cease_requested_at
 * @property string|null $ceased_at
 * @property string|null $last_state_change
 * @property int $internal_use
 * @property int $chargeable
 * @property int|null $duplex_master_id
 * @property int $number
 * @property string|null $colo_circuit_ref
 * @property string|null $ticket_ref
 * @property string|null $private_notes
 * @property int $owned_by
 * @property string|null $loa_code
 * @property string|null $description
 * @property string|null $colo_billing_ref
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer|null $customer
 * @property-read PatchPanelPort|null $duplexMasterPort
 * @property-read \Illuminate\Database\Eloquent\Collection|PatchPanelPort[] $duplexSlavePorts
 * @property-read int|null $duplex_slave_ports_count
 * @property-read \IXP\Models\PatchPanel|null $patchPanel
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPortFile[] $patchPanelPortFiles
 * @property-read int|null $patch_panel_port_files_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPortFile[] $patchPanelPortFilesPublic
 * @property-read int|null $patch_panel_port_files_public_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPortHistory[] $patchPanelPortHistories
 * @property-read int|null $patch_panel_port_histories_count
 * @property-read \IXP\Models\SwitchPort|null $switchPort
 * @method static Builder|PatchPanelPort masterPort()
 * @method static Builder|PatchPanelPort newModelQuery()
 * @method static Builder|PatchPanelPort newQuery()
 * @method static Builder|PatchPanelPort query()
 * @method static Builder|PatchPanelPort whereAssignedAt($value)
 * @method static Builder|PatchPanelPort whereCeaseRequestedAt($value)
 * @method static Builder|PatchPanelPort whereCeasedAt($value)
 * @method static Builder|PatchPanelPort whereChargeable($value)
 * @method static Builder|PatchPanelPort whereColoBillingRef($value)
 * @method static Builder|PatchPanelPort whereColoCircuitRef($value)
 * @method static Builder|PatchPanelPort whereConnectedAt($value)
 * @method static Builder|PatchPanelPort whereCreatedAt($value)
 * @method static Builder|PatchPanelPort whereCustomerId($value)
 * @method static Builder|PatchPanelPort whereDescription($value)
 * @method static Builder|PatchPanelPort whereDuplexMasterId($value)
 * @method static Builder|PatchPanelPort whereId($value)
 * @method static Builder|PatchPanelPort whereInternalUse($value)
 * @method static Builder|PatchPanelPort whereLastStateChange($value)
 * @method static Builder|PatchPanelPort whereLoaCode($value)
 * @method static Builder|PatchPanelPort whereNotes($value)
 * @method static Builder|PatchPanelPort whereNumber($value)
 * @method static Builder|PatchPanelPort whereOwnedBy($value)
 * @method static Builder|PatchPanelPort wherePatchPanelId($value)
 * @method static Builder|PatchPanelPort wherePrivateNotes($value)
 * @method static Builder|PatchPanelPort whereState($value)
 * @method static Builder|PatchPanelPort whereSwitchPortId($value)
 * @method static Builder|PatchPanelPort whereTicketRef($value)
 * @method static Builder|PatchPanelPort whereUpdatedAt($value)
 * @mixin Eloquent
 */

class PatchPanelPort extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patch_panel_port';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'state' => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'switch_port_id',
        'patch_panel_id',
        'customer_id',
        'state',
        'notes',
        'assigned_at',
        'connected_at',
        'cease_requested_at',
        'ceased_at',
        'last_state_change',
        'internal_use',
        'chargeable',
        'duplex_master_id',
        'number',
        'colo_circuit_ref',
        'ticket_ref',
        'private_notes',
        'owned_by',
        'loa_code',
        'description',
        'colo_billing_ref'
    ];

    /**
     * CONST STATES
     */
    public const STATE_AVAILABLE              = 1;
    public const STATE_AWAITING_XCONNECT      = 2;
    public const STATE_CONNECTED              = 3;
    public const STATE_AWAITING_CEASE         = 4;
    public const STATE_CEASED                 = 5;
    public const STATE_BROKEN                 = 6;
    public const STATE_RESERVED               = 7;
    public const STATE_PREWIRED               = 8;
    public const STATE_OTHER                  = 999;

    /**
     * CONST OWNED
     */
    public const OWNED_CUST                    = 1;
    public const OWNED_IXP                     = 2;
    public const OWNED_SERV_PRO                = 3;
    public const OWNED_DATA_CENTER             = 4;
    public const OWNED_OTHER                   = 5;

    /**
     * CONST CHARGEABLE
     */
    public const CHARGEABLE_YES                = 1;
    public const CHARGEABLE_NO                 = 2;
    public const CHARGEABLE_HALF               = 3;
    public const CHARGEABLE_OTHER              = 4;

    /**
     * CONST EMAIL
     */
    public const EMAIL_CONNECT                 = 1;
    public const EMAIL_CEASE                   = 2;
    public const EMAIL_INFO                    = 3;
    public const EMAIL_LOA                     = 4;

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
     * Array STATES for available
     */
    public static $AVAILABLE_FOR_ALLOCATION_STATES = [
        self::STATE_AVAILABLE,
        self::STATE_PREWIRED,
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
     * Array STATES for allocated
     */
    public static $ALLOCATED_STATES_TEXT = [
        self::STATE_AWAITING_XCONNECT   => "Awaiting Xconnect",
        self::STATE_CONNECTED           => "Connected",
        self::STATE_AWAITING_CEASE      => "Awaiting Cease",
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
     * Array $CHARGEABLES
     */
    public static $CHARGEABLES = [
        self::CHARGEABLE_YES            => "Yes",
        self::CHARGEABLE_NO             => "No",
        self::CHARGEABLE_HALF           => "Half",
        self::CHARGEABLE_OTHER          => "Other"
    ];

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
     * Get the Patch Panel that owns this patch panel port
     */
    public function patchPanel(): BelongsTo
    {
        return $this->belongsTo( PatchPanel::class , 'patch_panel_id' );
    }

    /**
     * Get the switch port that owns this patch panel port
     */
    public function switchPort(): BelongsTo
    {
        return $this->belongsTo( SwitchPort::class , 'switch_port_id' );
    }

    /**
     * Get the customer that owns this patch panel port
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo( Customer::class , 'customer_id' );
    }

    /**
     * Get the duplex master port that owns this patch panel port
     */
    public function duplexMasterPort(): BelongsTo
    {
        return $this->belongsTo( __CLASS__, 'duplex_master_id' );
    }

    /**
     * Get the patch panel port files for this patch panel port
     */
    public function patchPanelPortFiles(): HasMany
    {
        return $this->hasMany(PatchPanelPortFile::class, 'patch_panel_port_id' );
    }

    /**
     * Get the patch panel port histories for this patch panel port
     */
    public function patchPanelPortHistories(): HasMany
    {
        return $this->hasMany(PatchPanelPortHistory::class, 'patch_panel_port_id' );
    }

    /**
     * Get the public patch panel port files for this patch panel port
     */
    public function patchPanelPortFilesPublic(): HasMany
    {
        return $this->hasMany(PatchPanelPortFile::class, 'patch_panel_port_id' )
            ->where( 'is_private', 0 );
    }

    /**
     * Get the duplex slaves ports for this patch panel port
     */
    public function duplexSlavePorts(): HasMany
    {
        return $this->hasMany( __CLASS__ , 'duplex_master_id' );
    }

    /**
     * A public facing reference for this. Essentially the ID.
     *
     * @return string
     */
    public function circuitReference(): string
    {
        return sprintf( "PPP-%05d", $this->id );
    }

    /**
     * Is this port part of a duplex port group?
     *
     * @return bool
     */
    public function isDuplexPort(): bool
    {
        return $this->duplex_master_id !== null || $this->duplexSlavePorts()->count() > 0;
    }

    /**
     * Scope a query to match master ports only
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeMasterPort( Builder $query ): Builder
    {
        return $query->where('duplex_master_id', null );
    }


    /**
     * Get name
     *
     * @return string
     */
    public function name(): string
    {
        $name = $this->patchPanel->port_prefix . $this->number;

        if( $duplex = $this->duplexSlavePorts->first() ) {
            $name .= '/' . $duplex->name() . ' (' . ( $this->patchPanel->isFibre() ? 'Fibre, duplex port: ' : '' );
            $name .= ( $this->number % 2 ? ( floor( $this->number / 2 ) ) + 1 : $this->number / 2 ) . ')';
        }

        return $name;
    }


    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$STATES array (or 'Unknown')
     *
     * @return string
     */
    public function states(): string
    {
        return self::$STATES[ $this->state ] ?? 'Unknown';
    }

    /**
     * Get chargeable
     *
     * @return int
     */
    public function isChargeable(): int
    {
        return isset( self::$CHARGEABLES[ $this->chargeable ] ) ? $this->chargeable : self::CHARGEABLE_NO;

    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$CHARGEABLES array (or 'Unknown')
     *
     * @return string
     */
    public function chargeable(): string
    {
        return self::$CHARGEABLES[ $this->chargeable ] ?? 'Unknown';
    }

    /**
     * Is this port os allocated?
     *
     * It is if its state is one of: awaiting xconnect, connected, awaiting cease.
     *
     * @return bool
     */
    public function allocated(): bool
    {
        return in_array( $this->state, self::$ALLOCATED_STATES );
    }

    /**
     * Is this port available for use?
     *
     * It is if its state is one of: available, ceased, awaiting cease, prewired.
     *
     * @return bool
     */
    public function availableForUse(): bool
    {
        return in_array( $this->state, self::$AVAILABLE_STATES );
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$STATES array (or 'Unknown')
     * @return string
     */
    public function ownedBy(): string
    {
        return self::$OWNED_BY[ $this->owned_by ] ?? 'Unknown';
    }

    /**
     * Is the state STATE_AVAILABLE?
     *
     * @return bool
     */
    public function stateAvailable(): bool
    {
        return $this->state === self::STATE_AVAILABLE;
    }

    /**
     * Is the state STATE_AWAITING_XCONNECT?
     *
     * @return bool
     */
    public function stateAwaitingXConnect(): bool
    {
        return $this->state === self::STATE_AWAITING_XCONNECT;
    }

    /**
     * Is the state STATE_CONNECTED?
     *
     * @return bool
     */
    public function stateConnected(): bool
    {
        return $this->state === self::STATE_CONNECTED;
    }

    /**
     * Is the state STATE_AWAITING_CEASE?
     *
     * @return bool
     */
    public function stateAwaitingCease(): bool
    {
        return $this->state === self::STATE_AWAITING_CEASE;
    }

    /**
     * Is the state STATE_CEASED?
     *
     * @return bool
     */
    public function stateCeased(): bool
    {
        return $this->state === self::STATE_CEASED;
    }

    /**
     * Is the state STATE_RESERVED?
     *
     * @return bool
     */
    public function stateReserved(): bool
    {
        return $this->state === self::STATE_RESERVED;
    }

    /**
     * Is the state STATE_RESERVED?
     *
     * @return bool
     */
    public function statePrewired(): bool
    {
        return $this->state === self::STATE_PREWIRED;
    }

    /**
     * Get css class state
     *
     * @param int $state
     * @param bool $superUser
     *
     * @return string
     */
    public static function stateCssClass( int $state, bool $superUser = false ): string
    {
        if( $superUser ) {
            if( in_array( $state, self::$AVAILABLE_STATES ) || $state === self::STATE_PREWIRED ){
                $class = 'success';
            } elseif( $state === self::STATE_AWAITING_XCONNECT ){
                $class = 'warning';
            } elseif( $state === self::STATE_CONNECTED ){
                $class = 'danger';
            } else {
                $class = 'info';
            }

            return $class;
        }

        if( $state === self::STATE_CONNECTED ){
            $class = 'success';
        } elseif( $state === self::STATE_AWAITING_CEASE ) {
            $class = 'warning';
        } elseif( $state === self::STATE_AWAITING_XCONNECT ) {
            $class = 'danger';
        } else {
            $class = 'info';
        }

        return $class;
    }

    /**
     * Reset the port and set status to available (including slave ports)
     *
     * @return void
     */
    public function reset(): void
    {
        foreach( $this->duplexSlavePorts as $pppsp ) {
            $pppsp->reset();
            $pppsp->update( [ 'duplex_master_id', null ] );
        }

        // Attributes that are not reset
        $excludedValues = [ 'id', 'patch_panel_id', 'number', 'owned_by', 'colo_billing_ref', 'updated_at', 'created_at' ];

        // Get for each attributes their default values
        $attributesToReset = DB::query()->selectRaw( 'COLUMN_NAME, COLUMN_DEFAULT, DATA_TYPE' )
            ->from( 'INFORMATION_SCHEMA.COLUMNS' )
            ->where( 'TABLE_NAME', $this->table )
            ->where( 'TABLE_SCHEMA', config( 'database.connections.mysql.database' ) )
            ->whereNotIn( 'COLUMN_NAME', $excludedValues )->get();


        foreach( $attributesToReset as $attr ) {
            // if the types are varchar or longtext set '' instead of null
            $this->{$attr->COLUMN_NAME} = in_array( $attr->DATA_TYPE, [ 'varchar', 'longtext' ] ) ? '' : $attr->COLUMN_DEFAULT;
        }

        $this->state                = self::STATE_AVAILABLE;
        $this->last_state_change    = now();
        $this->save();
    }

    /**
     * Archive a patch panel port (and its slave ports)
     *
     * NB: does not reset the original port.
     *
     * @return PatchPanelPortHistory
     *
     * @throws
     */
    public function archive(): PatchPanelPortHistory
    {
        $historyPort = PatchPanelPortHistory::createFromPort( $this );

        foreach( $this->duplexSlavePorts as $slave ) {
            /** @var PatchPanelPort  $slave */
            PatchPanelPortHistory::create(
                array_merge(
                    $historyPort->replicate( [ 'id', 'duplex_master_id', 'number', 'patch_panel_port_id' ] )->toArray(),
                    [
                        'number'                => $slave->number,
                        'duplex_master_id'      => $historyPort->id,
                        'patch_panel_port_id'   => $slave->id,
                    ]
                )
            );
        }

        foreach( $this->patchPanelPortFilesPublic as $file ) {
            PatchPanelPortHistoryFile::createFromFile( $file, $historyPort );
            $file->delete();
        }

        return $historyPort;
    }

    /**
     * Move details / contents of a PPP to another PPP.
     *
     * Moves the information and files from a patch panel port to an other one
     * (and also move duplex slave if there is one). This function also:
     *
     * * Creates history of the old patch panel port
     * * Resets the old patch panel port (and duplex slave)
     *
     * @param PatchPanelPort        $dest          The destination port
     * @param PatchPanelPort|null   $slave         If the source port is a duplex port, we need a new slave also.
     *
     * @return boolean
     *
     * @throws
     */
    public function move( PatchPanelPort $dest, PatchPanelPort $slave = null ): bool
    {
        // preflight checks
        if( $this->duplexSlavePorts()->count() && ( $slave === null || !$slave->availableForUse() ) ) {
            throw new GeneralException( 'Source is duplex but no slave / free slave provided' );
        }

        if( !$dest->availableForUse() ) {
            throw new GeneralException( 'Destination port is not available for use' );
        }

        foreach( $this->patchPanelPortFiles as $file ){
            $file->update( [ 'patch_panel_port_id' => $dest->id ] );
        }

        if( !( $history = $this->archive() )  ) {
            return false;
        }

        // wipe source switch port as it is a unique constraint in the db
        $spid = $this->switch_port_id;
        $this->update( [ 'switch_port_id' => null ] );

        // Update the new port with the data of the old port
        $dest->update( $this->replicate(
                [
                    'id',
                    'switch_port_id',
                    'patch_panel_id',
                    'duplex_master_id',
                    'number',
                    'private_notes',
                    'colo_billing_ref'
                ]
            )->toArray()
        );

        $dest->update( [
            'switch_port_id' => $spid,
            'private_notes'  => "### " . now()->format('Y-m-d')." - IXP Manager\n\nMoved from "
                . $this->patchPanel->name . "/" . $this->name()
                . " by ". ( Auth::check() ? Auth::getUser()->username : "unknown/unauth" )
                . " on " . now()->format('Y-m-d') . ".\n\n"
                . $this->private_notes,
        ]);

        if( $slave ){
            $slave->update( [ 'duplex_master_id' => $dest->id ] );
        }

        // Reset the old port
        $this->reset();

        $history->update( [
            'private_notes' => "### " . now()->format('Y-m-d' ) . " - IXP Manager\n\nMoved to "
                . $dest->patchPanel->name . "/" . $dest->name()
                . " by ". ( Auth::check() ? Auth::getUser()->username : "unknown/unauth" )
                . " on " . now()->format( 'Y-m-d' ) . ".\n\n"
                . ( $dest->patchPanelPortFiles()->count() ? "See new port for files.\n\n" : '' )
                . $history->private_notes
        ] );

        return true;
    }

    /**
     * Remove a patch panel port and everything linked to it ( duplex port, files, histories, etc...)
     *
     * Also:
     *
     * * optionally deletes the linked slave port.
     * * deletes all the history.
     * * deletes in the database and on the disk all the files/filesHistory uploaded for this port.
     *
     * @throws
     */
    public function remove(): void
    {
        // Delete slave port first
        if( $this->duplexSlavePorts()->count() ){
            foreach( $this->duplexSlavePorts as $slave ){
                /** @var $slave PatchPanelPort */
                $slave->remove();
            }
        }

        // Delete port histories and files
        foreach( $this->patchPanelPortHistories as $history ) {
            /** @var $history PatchPanelPortHistory */

            $user = Auth::check() ? Auth::getUser()->username : 'unkown/unauth';
            foreach( PatchPanelPortHistory::whereDuplexMasterId( $history->id )->get() as $duplex ){
                $duplex->update([
                    'duplex_master_id'  => null,
                    'private_notes'     => "### " . now()->format('Y-m-d') . " - IXP Manager \n\nHad a master port that was deleted by {$user} on " . now()->format('Y-m-d') . "\n\n"
                        . $duplex->private_notes
                ]);
            }

            foreach( $history->patchPanelPortHistoryFiles as $historyFile ) {
                $path = 'files/' . $historyFile->path();

                $historyFile->update( [ 'patch_panel_port_history_id' => null ] );
                $historyFile->delete();

                if( Storage::exists( $path ) ){
                    Storage::delete( $path );
                }
            }
            $history->update( [ 'patch_panel_port_id' => null ] );
            $history->delete();
        }

        // Delete port files
        foreach( $this->patchPanelPortFiles as $file ){
            $path = 'files/' . $file->getPath();

            $file->update( [ 'patch_panel_port_id' => null ] );
            $file->delete();
            if( Storage::exists( $path ) ){
                Storage::delete( $path );
            }
        }

        $this->delete();
    }

    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Model $model
     *
     * @return string
     */
    public static function logSubject( Model $model ): string
    {
        return sprintf(
            "Patch Panel Port [id:%d] '%s' belonging to Patch Panel [id:%d] '%s'",
            $model->id,
            $model->name(),
            $model->patch_panel_id,
            $model->patchPanel->name,
        );
    }
}
