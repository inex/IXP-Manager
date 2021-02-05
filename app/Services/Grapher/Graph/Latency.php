<?php

namespace IXP\Services\Grapher\Graph;

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

use Auth, Log;

use Illuminate\Auth\Access\AuthorizationException;

use IXP\Exceptions\Services\Grapher\{
    ParameterException
};

use IXP\Models\{
    VlanInterface as VlanInterfaceModel,
    User
};

use IXP\Services\Grapher;
use IXP\Services\Grapher\Graph;

/**
 * Grapher -> Latency Graphs
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Latency extends Graph
{
    /**
     * VLAN interface to graph
     *
     * @var VlanInterfaceModel
     */
    private $vli = null;

    /**
     * Period of three hours for graphs
     */
    public const PERIOD_3HOURS   = '3hours';

    /**
     * Period of thirty hours for graphs
     */
    public const PERIOD_30HOURS  = '30hours';

    /**
     * Period of ten days for graphs
     */
    public const PERIOD_10DAYS = '10days';

    /**
     * Period of one year for graphs
     */
    public const PERIOD_1YEAR  = '1year';

    /**
     * Default period
     */
    public const PERIOD_DEFAULT  = self::PERIOD_3HOURS;

    /**
     * Array of valid periods for drill down graphs
     */
    public const PERIODS = [
        self::PERIOD_3HOURS     => "3hours",
        self::PERIOD_30HOURS    => "30hours",
        self::PERIOD_10DAYS     => "10days",
        self::PERIOD_1YEAR      => "1year"
    ];

    /**
     * Array of valid periods for drill down graphs - descriptions
     */
    public const PERIODS_DESC = [
        self::PERIOD_3HOURS     => "3 hours",
        self::PERIOD_30HOURS    => "30 hours",
        self::PERIOD_10DAYS     => "10 days",
        self::PERIOD_1YEAR      => "year"
    ];

    /**
     * Default protocol for graphs
     */
    public const PROTOCOL_DEFAULT = self::PROTOCOL_IPV4;

    /**
     * Constructor
     *
     * @param   Grapher             $grapher
     * @param   VlanInterfaceModel  $vli
     *
     * @throws ParameterException
     */
    public function __construct( Grapher $grapher, VlanInterfaceModel $vli )
    {
        parent::__construct( $grapher );
        $this->vli = $vli;
        $this->setPeriod( self::PERIOD_3HOURS );
    }

    /**
     * Set the period we should use
     *
     * @param string $v
     *
     * @return Graph Fluid interface
     *
     * @throws ParameterException
     */
    public function setPeriod( string $v ): Graph
    {
        if( !isset( self::PERIODS[ $v ] ) ) {
            throw new ParameterException('Invalid period ' . $v );
        }

        if( $this->period() !== $v ) {
            $this->wipe();
        }

        $this->period = $v;
        return $this;
    }

    /**
     * Get the period description for a given period identifier
     *
     * @param string|null $period
     *
     * @return string
     */
    public static function resolvePeriod( $period = null ): string
    {
        return self::PERIODS[ $period ] ?? 'Unknown';
    }

    /**
     * Get the vlan interface we're meant to graph for latency
     *
     * @return VlanInterfaceModel
     */
    public function vli(): VlanInterfaceModel
    {
        return $this->vli;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc
     *
     * @return string
     */
    public function name(): string
    {
        return sprintf( "Latency Graph :: %s :: %s :: %s",
            $this->vli()->vlan->name,
            $this->vli()->virtualInterface->customer->abbreviatedName,
            $this->protocol() === self::PROTOCOL_IPV4 ? $this->vli()->ipv4address->address : $this->vli()->ipv6address->address
        );
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     *
     * @return string
     */
    public function identifier(): string
    {
        return sprintf( "latency-vli%d-%s", $this->vli()->id, $this->protocol() );
    }

    /**
     * Utility function to determine if the currently logged in user can access 'all customer's latency' graphs
     *
     * @return bool
     */
    public static function authorisedForAllCustomers(): bool
    {
        if( Auth::check() && Auth::getUser()->isSuperUser() ) {
            return true;
        }

        if( !Auth::check() && is_numeric( config( 'grapher.access.latency' ) ) && config( 'grapher.access.latency' ) === User::AUTH_PUBLIC ) {
            return true;
        }

        return Auth::check() && is_numeric( config( 'grapher.access.latency' ) ) && Auth::getUser()->privs() >= config( 'grapher.access.latency' );
    }

    /**
     * This function controls access to the graph.
     *
     * {@inheritDoc}
     *
     * For (public) vlan aggregate graphs we pretty much allow complete access.
     *
     * @throws AuthorizationException
     */
    public function authorise(): bool
    {
        // NB: see above authorisedForAllCustomers()
        if( is_numeric( config( 'grapher.access.latency' ) ) && config( 'grapher.access.latency' ) === User::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( !Auth::check() ) {
            $this->deny();
            return false;
        }

        if( Auth::getUser()->isSuperUser() ) {
            return $this->allow();
        }

        if( Auth::getUser()->custid === $this->vli()->virtualInterface->customer->id ) {
            return $this->allow();
        }

        if( config( 'grapher.access.latency' ) !== 'own_graphs_only'
            && is_numeric( config( 'grapher.access.latency' ) )
            && Auth::getUser()->privs >= config( 'grapher.access.latency' )
        ) {
            return $this->allow();
        }

        Log::notice( sprintf( "[Grapher] [Latency]: user %d::%s tried to access a latency graph for vli "
                . "{$this->vli()->id} which is not theirs", Auth::id(), Auth::getUser()->username )
        );

        $this->deny();
        return false;
    }

    /**
     * Generate a URL to get this graphs 'file' of a given type
     *
     * @param array $overrides Allow standard parameters to be overridden (e.g. category)
     *
     * @return string
     */
    public function url( array $overrides = [] ): string
    {
        return parent::url( $overrides ) . sprintf("&id=%d",
                $overrides[ 'id' ] ?? $this->vli()->id
            );
    }

    /**
     * Get parameters in bulk as associative array
     *
     * Extends base function
     *
     * @return array $params
     */
    public function getParamsAsArray(): array
    {
        $p          = parent::getParamsAsArray();
        $p['id']    = $this->vli()->id;
        return $p;
    }

    /**
     * Process user input for the parameter: vlanint
     *
     * Does a abort(404) if invalid
     *
     * @param   int     $vliid  The user input value
     *
     * @return  VlanInterfaceModel
     */
    public static function processParameterVlanInterface( int $vliid ): VlanInterfaceModel
    {
        return VlanInterfaceModel::findOrFail( $vliid );
    }
}