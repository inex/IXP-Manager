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

use Eloquent;

use Illuminate\Database\Eloquent\{
    Builder,
    Model
};

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};

use IXP\Traits\Observable;

/**
 * IXP\Models\PatchPanel
 *
 * @property int $id
 * @property int|null $cabinet_id
 * @property string $name
 * @property string $colo_reference
 * @property int $cable_type
 * @property int $connector_type
 * @property string|null $installation_date
 * @property string $port_prefix
 * @property int $active
 * @property int $chargeable
 * @property string $location_notes
 * @property int|null $u_position
 * @property int|null $mounted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Cabinet|null $cabinet
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\PatchPanelPort[] $patchPanelPorts
 * @property-read int|null $patch_panel_ports_count
 * @method static Builder|PatchPanel newModelQuery()
 * @method static Builder|PatchPanel newQuery()
 * @method static Builder|PatchPanel query()
 * @method static Builder|PatchPanel whereActive($value)
 * @method static Builder|PatchPanel whereCabinetId($value)
 * @method static Builder|PatchPanel whereCableType($value)
 * @method static Builder|PatchPanel whereChargeable($value)
 * @method static Builder|PatchPanel whereColoReference($value)
 * @method static Builder|PatchPanel whereConnectorType($value)
 * @method static Builder|PatchPanel whereCreatedAt($value)
 * @method static Builder|PatchPanel whereId($value)
 * @method static Builder|PatchPanel whereInstallationDate($value)
 * @method static Builder|PatchPanel whereLocationNotes($value)
 * @method static Builder|PatchPanel whereMountedAt($value)
 * @method static Builder|PatchPanel whereName($value)
 * @method static Builder|PatchPanel wherePortPrefix($value)
 * @method static Builder|PatchPanel whereUPosition($value)
 * @method static Builder|PatchPanel whereUpdatedAt($value)
 * @mixin Eloquent
 */

class PatchPanel extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patch_panel';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cabinet_id',
        'name',
        'colo_reference',
        'cable_type',
        'connector_type',
        'installation_date',
        'port_prefix',
        'active',
        'chargeable',
        'location_notes',
        'u_position',
        'mounted_at',
    ];

    /**
     * CONST Cable types
     */
    public const CABLE_TYPE_UTP                = 1;
    public const CABLE_TYPE_SMF                = 2;
    public const CABLE_TYPE_MMF                = 3;
    public const CABLE_TYPE_OTHER              = 999;

    /**
     * Array Cable types
     */
    public static $CABLE_TYPES = [
        self::CABLE_TYPE_UTP            => 'UTP',
        self::CABLE_TYPE_SMF            => 'SMF',
        self::CABLE_TYPE_MMF            => 'MMF',
        self::CABLE_TYPE_OTHER          => 'Other',
    ];

    /**
     * Array 'Fibre' Cable types
     */
    public static $FIBRE_CABLE_TYPES = [
        self::CABLE_TYPE_SMF,
        self::CABLE_TYPE_MMF,
    ];


    /**
     * CONST Connector types
     */
    public const CONNECTOR_TYPE_RJ45           = 1;
    public const CONNECTOR_TYPE_SC             = 2;
    public const CONNECTOR_TYPE_LC             = 3;
    public const CONNECTOR_TYPE_MU             = 4;
    public const CONNECTOR_TYPE_OTHER          = 999;

    /**
     * Array Connector types
     */
    public static $CONNECTOR_TYPES = [
        self::CONNECTOR_TYPE_RJ45      => 'RJ45',
        self::CONNECTOR_TYPE_SC        => 'SC',
        self::CONNECTOR_TYPE_LC        => 'LC',
        self::CONNECTOR_TYPE_MU        => 'MU',
        self::CONNECTOR_TYPE_OTHER     => 'Other',
    ];

    /**
     * Counts from patch panel mount position
     */
    public const MOUNTED_AT_FRONT = 1;
    public const MOUNTED_AT_REAR  = 2;

    /**
     * Mounted at textual representations
     */
    public static $MOUNTED_AT = [
        self::MOUNTED_AT_FRONT => 'Front',
        self::MOUNTED_AT_REAR  => 'Rear',
    ];

    /**
     * Get the patch panel port files for this patch panel port
     */
    public function patchPanelPorts(): HasMany
    {
        return $this->hasMany(PatchPanelPort::class, 'patch_panel_id' );
    }

    /**
     * Get the cabinet that own the patch panel
     */
    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class, 'cabinet_id' );
    }

    /**
     * Turn the database integer representation of the cable type into text as
     * defined in the self::$CABLE_TYPES array (or 'Unknown')
     *
     * @return string
     */
    public function cableType(): string
    {
        return self::$CABLE_TYPES[ $this->cable_type ] ?? 'Unknown';
    }

    /**
     * Identify id this patch panel is a 'fibre' patch panel
     */
    public function isFibre(): bool
    {
        return in_array( $this->cable_type, self::$FIBRE_CABLE_TYPES );
    }

    /**
     * Turn the database integer representation of the connector type into text as
     * defined in the self::$CONNECTOR_TYPES array (or 'Unknown')
     *
     * @return string
     */
    public function connectorType(): string
    {
        return self::$CONNECTOR_TYPES[ $this->connector_type ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the PatchPanelPort::$CHARGEABLES array (or 'Unknown')
     *
     * @return string
     */
    public function chargeable(): string
    {
        return PatchPanelPort::$CHARGEABLES[ $this->chargeable ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the PatchPanelPort::$CHARGEABLES array (or 'Unknown')
     *
     * @return string
     */
    public function mountedAt(): string
    {
        return self::$MOUNTED_AT[ $this->mounted_at ] ?? 'Unknown';
    }

    /**
     * Does this patch panel have any duplex ports?
     *
     * @return bool
     */
    public function hasDuplexPort(): bool
    {
        $slave = self::select( [ 'ppps.id' ] )
            ->from( 'patch_panel AS pp' )
            ->leftJoin( 'patch_panel_port AS ppp', 'ppp.patch_panel_id', 'pp.id' )
            ->join( 'patch_panel_port AS ppps', 'ppps.id', 'ppp.duplex_master_id' )
            ->where( 'pp.id', $this->id )->get()->keyBy( 'id' )->count() > 0;

        $master = self::select( [ 'ppp.id' ] )
                ->from( 'patch_panel AS pp' )
                ->leftJoin( 'patch_panel_port AS ppp', 'ppp.patch_panel_id', 'pp.id' )
                ->whereNotNull( 'duplex_master_id' )
                ->where( 'pp.id', $this->id )->get()->keyBy( 'id' )->count() > 0;

        return $slave || $master;
    }

    /**
     * Get number of patch panel ports
     *
     * @return int
     */
    public function availableForUsePortCount(): int
    {
        $master = self::select( [ 'pppm.*' ] )
            ->from( 'patch_panel AS pp' )
            ->leftJoin( 'patch_panel_port AS ppp', 'ppp.patch_panel_id', 'pp.id' )
            ->leftJoin( 'patch_panel_port AS pppm', 'pppm.id', 'ppp.duplex_master_id' )
            ->whereIn( 'pppm.state', PatchPanelPort::$AVAILABLE_STATES )
            ->where( 'pp.id', $this->id )->get()->keyBy( 'id' )->count();

        $ppp = self::select( [ 'ppp.*' ] )
            ->from( 'patch_panel AS pp' )
            ->leftJoin( 'patch_panel_port AS ppp', 'ppp.patch_panel_id', 'pp.id' )
            ->whereIn( 'ppp.state', PatchPanelPort::$AVAILABLE_STATES )
            ->whereNull( 'ppp.duplex_master_id' )
            ->where( 'pp.id', $this->id )->get()->keyBy( 'id' )->count();

        return $master + $ppp;
    }

    /**
     * get the css class used to display the value => available ports / total ports
     *
     * @param  int  $total
     * @param  int  $available
     *
     * @return string
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     */
    public function cssClassPortCount( int $total, int $available): string
    {
        if($total !== 0):
            if( ($total - $available) / $total < 0.7 ):
                $class = "success";
            elseif( ($total - $available ) / $total < 0.85 ):
                $class = "warning";
            else:
                $class = "danger";
            endif;
        else:
            $class = "danger";
        endif;

        return $class;
    }

    /**
     * get the value available port / total port
     *
     *
     * @param  int      $availableForUsePortCount
     * @param  int      $portCount
     * @param  bool     $divide  if the value need to be divide by 2 (use when some patch panel ports have duplex port)
     *
     * @return string
     */
    public function availableOnTotalPort( int $availableForUsePortCount, int $portCount, bool $divide = false ): string
    {
        $available = ($divide)? floor( $availableForUsePortCount / 2 ) : $availableForUsePortCount;
        $total     = ($divide)? floor( $portCount / 2 ) : $portCount;

        return $available . ' / ' . $total;
    }

    /**
     * Create patch panel ports for a patch panel
     *
     * @param  int $n the number of port needed
     *
     * @return void
     */
    public function createPorts( int $n ): void
    {
        // what's the current maximum port number?
        // (we need this to add new ones to the end)
        $max = $this->patchPanelPorts()->max( 'number' );

        for( $i = 1; $i <= $n; $i++ ) {
            PatchPanelPort::create( [
                'number'            => ( $max + $i ),
                'state'             => PatchPanelPort::STATE_AVAILABLE,
                'patch_panel_id'    => $this->id,
                'chargeable'        => $this->chargeable,
                'last_state_change' => now(),
            ] );
        }
    }

    /**
     * A descriptive position of the patch panel in the rack
     *
     * @return string
     */
    public function locationDescription(): string
    {
        $loc = '';

        if( $this->u_position ) {
            $loc .= 'Located at U' . $this->u_position;

            if( $cf = $this->cabinet->u_counts_from ) {
                $loc .= ' (counting from the ' . strtolower( Cabinet::$U_COUNTS_FROM[ $cf ] ) . ')';
            }

            if( $ma = $this->mounted_at ) {
                $loc .= ' at the ' . strtolower( self::$MOUNTED_AT[ $ma ] ) . ' of the cabinet';
            }

            $loc .= '.';
        }

        return $loc;
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
            "Patch Panel [id:%d] '%s'",
            $model->id,
            $model->name
        );
    }
}
