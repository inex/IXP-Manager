<?php

namespace IXP\Services\Grapher\Graph;

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\{
    Customer,
    User,
};

use IXP\Services\Grapher;
use IXP\Services\Grapher\{Graph};

/**
 * Grapher -> P2P Graph covering /all/ of a customers connections
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MultiP2p extends Graph
{
    /**
     * Source customer to graph
     */
    private readonly Customer $srcCustomer;

    /**
     * Destination customer to graph
     */
    private readonly Customer $dstCustomer;

    /**
     * Constructor
     *
     * @param Grapher $grapher
     * @param Customer $srcCustomer
     * @param Customer $dstCustomer
     */
    public function __construct( Grapher $grapher, Customer $srcCustomer, Customer $dstCustomer )
    {
        parent::__construct( $grapher );
        $this->srcCustomer = $srcCustomer;
        $this->dstCustomer = $dstCustomer;
    }

    /**
     * Get the source vlan we're set to use
     */
    public function srcCustomer(): Customer
    {
        return $this->srcCustomer;
    }

    /**
     * Get the dest vlan we're set to use
     */
    public function dstCustomer(): Customer
    {
        return $this->dstCustomer;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     */
    #[\Override]
    public function name(): string
    {
        return sprintf( "Customer P2P :: %s - %s (all traffic)",
            $this->srcCustomer()->abbreviatedName ?? "Source Customer",
            $this->dstCustomer()->abbreviatedName ?? "Destination Customer"
        );
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     */
    #[\Override]
    public function identifier(): string
    {
        return sprintf( "multip2p-scid%05d-dcid%05d", $this->srcCustomer()->id, $this->dstCustomer()->id );
    }

    /**
     * Utility function to determine if the currently logged in user can access 'all customer's p2p' graphs
     */
    public static function authorisedForAllCustomers(): bool
    {
        /** @var User $us */
        $us = Auth::user();
        if( Auth::check() && $us->isSuperUser() ) {
            return true;
        }

        if( !Auth::check() && is_numeric( config( 'grapher.access.p2p' ) ) && config( 'grapher.access.p2p' ) === User::AUTH_PUBLIC ) {
            return true;
        }

        return Auth::check() && is_numeric( config( 'grapher.access.p2p' ) ) && $us->privs() >= config( 'grapher.access.p2p' );
    }

    /**
     * This function controls access to the graph.
     *
     * {@inheritDoc}
     *
     * For (public) vlan aggregate graphs we pretty much allow complete access.
     */
    #[\Override]
    public function authorise(): bool
    {
        // NB: see above authorisedForAllCustomers()
        if( is_numeric( config( 'grapher.access.p2p' ) ) && config( 'grapher.access.p2p' ) === User::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( !Auth::check() ) {
            $this->deny();
        }

        /** @var User $us */
        $us = Auth::getUser();
        if( $us->isSuperUser() ) {
            return $this->allow();
        }

        if( $us->custid === $this->srcCustomer()->id ) {
            return $this->allow();
        }

        if( config( 'grapher.access.p2p' ) !== 'own_graphs_only'
            && is_numeric( config( 'grapher.access.p2p' ) )
            && $us->privs() >= (int)config( 'grapher.access.p2p' )
        ) {
            return $this->allow();
        }

        Log::notice( sprintf( "[Grapher] [Customer]: user %d::%s tried to access a customer multip2p graph "
                . "{$this->srcCustomer()->abbreviatedName} / {$this->dstCustomer()->abbreviatedName} which is not theirs", $us->id, $us->username )
        );

        $this->deny();
    }

    /**
     * Generate a URL to get this graphs 'file' of a given type
     *
     * @param array $overrides Allow standard parameters to be overridden (e.g. category)
     */
    #[\Override]
    public function url( array $overrides = [] ): string
    {
        return parent::url( $overrides ) . sprintf("&scid=%d&dcid=%d",
                $overrides['scid']   ?? $this->srcCustomer()->id,
                $overrides['dcid']   ?? $this->dstCustomer()->id
            );
    }

    /**
     * Get parameters in bulk as an associative array
     *
     * Extends base function
     *
     * @return (\Carbon\Carbon|int|mixed|null)[]
     *
     * @psalm-return array{protocol: mixed, period: mixed, category: mixed, type: mixed, period_start?: \Carbon\Carbon|null, period_end?: \Carbon\Carbon|null, scid: int, dcid: int}
     */
    #[\Override]
    public function getParamsAsArray(): array
    {
        $p = parent::getParamsAsArray();
        $p['scid'] = $this->srcCustomer()->id;
        $p['dcid'] = $this->dstCustomer()->id;
        return $p;
    }

    /**
     * Process user input for the parameter: customer ID
     *
     * Raises ModelNotFoundException if not found, which is rendered as a 404.
     */
    public static function processParameterCustomer( int $cid ): Customer
    {
        return Customer::findOrFail( $cid );
    }

    /**
     * Process user input for the parameter: src customer ID
     *
     * Raises ModelNotFoundException if not found, which is rendered as a 404.
     */
    public static function processParameterSrcCustomer( int $scid ): Customer
    {
        return self::processParameterCustomer( $scid );
    }

    /**
     * Process user input for the parameter: dst customer ID
     *
     * Raises ModelNotFoundException if not found, which is rendered as a 404.
     */
    public static function processParameterDstCustomer( int $dcid ): Customer
    {
        return self::processParameterCustomer( $dcid );
    }
}