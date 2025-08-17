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

use Auth;

use IXP\Services\Grapher;
use IXP\Services\Grapher\{Graph};

use IXP\Models\{
    CoreBundle as CoreBundleModel,
    User
};

/**
 * Grapher -> Core Bundle Graph
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yann Robin       <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CoreBundle extends Graph
{
    /**
     * CoreBundle to graph
     *
     * @var CoreBundleModel
     */
    private $cb = null;

    /**
     * Which side of the core bundle to show?
     *
     * @var string
     */
    private $side = 'a';

    /**
     * Constructor
     *
     * @param Grapher           $grapher
     * @param CoreBundleModel   $cb
     * @param string            $side
     *
     */
    public function __construct( Grapher $grapher, CoreBundleModel $cb, string $side = 'a' )
    {
        parent::__construct( $grapher );
        $this->cb = $cb;
        $this->side = strtolower( $side );
    }

    /**
     * Get the CoreBundle we're set to use
     *
     * @return CoreBundleModel
     */
    public function coreBundle(): CoreBundleModel
    {
        return $this->cb;
    }

    /**
     * Set the CoreBundleEntity we should use
     *
     * @param CoreBundleModel $cb
     *
     * @return static Fluid interface
     */
    public function setCoreBundle( CoreBundleModel $cb ): static
    {
        if( $this->coreBundle() && $this->coreBundle()->id !== $cb->id ) {
            $this->wipe();
        }

        $this->cb = $cb;
        return $this;
    }

    /**
     * Get the side to show
     *
     * @return string
     */
    public function side(): string
    {
        if( in_array( $this->side, [ 'a', 'b' ] ) ) {
            return $this->side;
        }

        return 'a';
    }

    /**
     * Get the side to show
     *
     * @param string $side
     */
    public function setSide( string $side ): static
    {
        $this->side = self::processParameterSide( $side );
        return $this;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     *
     * @return string
     */
    #[\Override]
    public function name(): string
    {
        return $this->coreBundle()->graph_title;
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     *
     * @return string
     */
    #[\Override]
    public function identifier(): string
    {
        return sprintf( "cb-aggregate-%05d-side%s", $this->coreBundle()->id, $this->side() );
    }

    /**
     * This function controls access to the graph.
     *
     * {@inheritDoc}
     *
     * For (public) vlan aggregate graphs we pretty much allow complete access.
     *
     * @return bool
     */
    #[\Override]
    public function authorise(): bool
    {
        /** @var User $us */
        $us = Auth::getUser();

        if( Auth::check() && $us->isSuperUser() ) {
            return $this->allow();
        }

        if( !in_array( $this->category(), Graph::CATEGORIES_BITS_PKTS ) ) {
            $this->deny();
            return false;
        }

        if( !Auth::check() && is_numeric( config( 'grapher.access.trunk' ) ) && config( 'grapher.access.trunk' ) === User::AUTH_PUBLIC ) {
            return $this->allow();
        }

        if( Auth::check() && is_numeric( config( 'grapher.access.trunk' ) ) && $us->privs() >= config( 'grapher.access.trunk' ) ) {
            return $this->allow();
        }

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
    #[\Override]
    public function url( array $overrides = [] ): string
    {
        return parent::url( $overrides ) . sprintf("&id=%d",
                $overrides[ 'id' ] ?? $this->coreBundle()->id
        );
    }

    /**
     * Get parameters in bulk as associative array
     *
     * Extends base function
     *
     * @return (\Carbon\Carbon|int|mixed|null|string)[]
     *
     * @psalm-return array{protocol: mixed, period: mixed, category: mixed, type: mixed, period_start?: \Carbon\Carbon|null, period_end?: \Carbon\Carbon|null, id: int, side: string}
     */
    #[\Override]
    public function getParamsAsArray(): array
    {
        $p          = parent::getParamsAsArray();
        $p['id']    = $this->coreBundle()->id;
        $p['side']  = $this->side();
        return $p;
    }

    /**
     * Process user input for the parameter: cb
     *
     * Does a abort(404) if invalid
     *
     * @param int $cb The user input value
     *
     * @return CoreBundleModel The verified / sanitised / default value
     */
    public static function processParameterCoreBundle( int $cb ): CoreBundleModel
    {
        return CoreBundleModel::findOrFail( $cb );
    }

    /**
     * Process user input for the parameter: side
     *
     * Does a abort(404) if invalid
     *
     * @param string $s The user input value
     *
     * @return string The verified / sanitised / default value
     */
    public static function processParameterSide( string $s ): string
    {
        $s = strtolower( $s );
        if( !in_array( $s, [ 'a', 'b'] ) ) {
            abort(404);
        }
        return $s;
    }
}