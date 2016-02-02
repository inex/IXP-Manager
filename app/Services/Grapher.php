<?php

namespace IXP\Services;

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Limited.
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

use IXP\Exceptions\Services\Grapher\{BadBackendException,CannotHandleRequestException,ConfigurationException,ParameterException};

use Config;
use D2EM;

use Entities\{IXP};

/**
 * Grapher Backend -> Mrtg
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Grapher {

    /**
     * Period of one day for graphs
     */
    const PERIOD_DAY   = 'day';

    /**
     * Period of one week for graphs
     */
    const PERIOD_WEEK  = 'week';

    /**
     * Period of one month for graphs
     */
    const PERIOD_MONTH = 'month';

    /**
     * Period of one year for graphs
     */
    const PERIOD_YEAR  = 'year';

    /**
     * Default period
     */
    const PERIOD_DEFAULT  = self::PERIOD_DAY;

    /**
     * Period to use
     * @var
     */
    private $period = self::PERIOD_DEFAULT;


    /**
     * Array of valid periods for drill down graphs
     */
    const PERIOD_DESCS = [
        self::PERIOD_DAY   => 'Day',
        self::PERIOD_WEEK  => 'Week',
        self::PERIOD_MONTH => 'Month',
        self::PERIOD_YEAR  => 'Year'
    ];

    /**
     * 'Bits' category for graphs
     */
    const CATEGORY_BITS     = 'bits';

    /**
     * 'Packets' category for graphs
     */
    const CATEGORY_PACKETS  = 'pkts';

    /**
     * 'Errors' category for graphs
     */
    const CATEGORY_ERRORS   = 'errs';

    /**
     * 'Discards' category for graphs
     */
    const CATEGORY_DISCARDS = 'discs';

    /**
     * Default category
     */
    const CATEGORY_DEFAULT  = self::CATEGORY_BITS;

    /**
     * Category to use
     * @var
     */
    private $category = self::CATEGORY_DEFAULT;


    /**
     * Array of valid categories for graphs
     */
    const CATEGORY_DESC = [
        self::CATEGORY_BITS     => 'Bits',
        self::CATEGORY_PACKETS  => 'Packets',
        self::CATEGORY_ERRORS   => 'Errors',
        self::CATEGORY_DISCARDS => 'Discards',
    ];

    /**
     * Protocols for graphs
     */
    const PROTOCOL_IPV4 = 4;

    /**
     * Protocols for graphs
     */
    const PROTOCOL_IPV6 = 6;

    /**
     * Protocols for graphs
     */
    const PROTOCOL_ALL = 0;

    /**
     * Default protocol for graphs
     */
    const PROTOCOL_DEFAULT = self::PROTOCOL_ALL;

    /**
     * Protocol to use
     * @var
     */
    private $protocol = self::PROTOCOL_DEFAULT;


    /**
     * Array of valid protocols
     */
    const PROTOCOLS = array(
        self::PROTOCOL_ALL  => 'All',
        self::PROTOCOL_IPV4 => 'IPv4',
        self::PROTOCOL_IPV6 => 'IPv6'
    );


    /**
     * Grapher file format return type: png
     * @var string
     */
    const TYPE_PNG   = 'png';

    /**
     * Grapher file format return type: log
     * @var string
     */
    const TYPE_LOG   = 'log';

    /**
     * Grapher file format return type: rrd
     * @var string
     */
    const TYPE_RRD   = 'rrd';

    /**
     * Grapher file format return type: json
     * @var string
     */
    const TYPE_JSON  = 'json';

    /**
     * Default type
     * @var string
     */
    const TYPE_DEFAULT = self::TYPE_PNG;

    /**
     * Type to use
     * @var string
     */
    private $type = self::TYPE_DEFAULT;

    /**
     * Possible types and descriptions
     * @var array
     */
    const TYPES = [
        self::TYPE_PNG  => 'PNG',
        self::TYPE_LOG  => 'LOG',
        self::TYPE_RRD  => 'RRD',
        self::TYPE_JSON => 'JSON',
    ];

    /**
     * IXP to graph
     * @var \Entities\IXP
     */
    private $ixp = null;


    /**
     * Initialised grapher backends
     * @var array
     */
    public $backends = [];



    /**
     * Constructor
     */
    public function __construct() {
        // set a default IXP
        $this->ixp = d2r( 'IXP' )->getDefault();
    }

    /**
     * As we allow multiple graphing backends, we need to resolve
     * which one we're meant to use here.
     *
     * The order of resolution is:
     *
     * 1. As specified in the `$backend` parameter if not null
     * 2. First backend in `configs/grapher.php` `backend` element.
     *
     * @param string $backend|null
     * @return string
     */
    public function resolveBackend( string $backend = null ): string {
        if( $backend === null ) {
            if( count( config('grapher.backend') ) ) {
                $backend = config('grapher.backend')[0];
            } else {
                throw new ConfigurationException( 'No graphing backend supplied or configured (see configs/grapher.php)' );
            }
        }

        if( !in_array($backend,config('grapher.backend') ) ) {
            throw new BadBackendException( 'No graphing provider enabled (see configs/grapher.php) for ' . $backend );
        }

        return $backend;
    }

    /**
     * Return the required grapher for the specified backend
     *
     * If the backend is not specified, it is resolved via `resolveBackend()`.
     * @see IXP\Console\Commands\Grapher\GrapherCommand::resolveBackend()
     *
     * @param string|null $backend A specific backend to return. If not specified, we use command line arguments
     * @return \IXP\Contracts\Grapher
     */
    public function getBackend( $backend = null ) {
        $backend = $this->resolveBackend( $backend );

        if( !isset( $this->backends[$backend] ) ) {
            $backendClass = Config::get( "grapher.providers.{$backend}" );
            $this->backends[ $backend ] = new $backendClass( $app['config']['grapher']['backends'][ $backend ] );
        }
        return $this->backends[$backend];
    }


    /**
     * Process user input for the parameter: period
     *
     * Note that this function just sets the default if the input is invalid.
     * If you want to force an exception in such cases, use setPeriod()
     *
     * @param string $v The user input value
     * @return string The verified / sanitised / default value
     */
    public function processParameterPeriod( string $v ): string {
        if( !isset( self::PERIOD_DESCS[ $v ] ) ) {
            $v = self::PERIOD_DEFAULT;
        }
        return $this->setPeriod($v)->period();
    }

    /**
     * Get the period we're set to use
     * @return string
     */
    public function period(): string {
        return $this->period;
    }

    /**
     * Set the period we should use
     * @param int $v
     * @return \IXP\Services\Grapher Fluid interface
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     */
    public function setPeriod( $v ): Grapher {
        if( !isset( self::PERIOD_DESCS[ $v ] ) ) {
            throw new ParameterException('Invalid period ' . $v );
        }

        $this->period = $v;
        return $this;
    }

    /**
     * Process user input for the parameter: protocol
     *
     * Note that this function just sets the default if the input is invalid.
     * If you want to force an exception in such cases, use setProtocol()
     *
     * @param string $v The user input value
     * @return string The verified / sanitised / default value
     */
    public function processParameterProtocol( int $v ): int {
        if( !isset( self::PROTOCOLS[ $v ] ) ) {
            $v = self::PROTOCOL_DEFAULT;
        }
        return $this->setProtocol($v)->protocol();
    }

    /**
     * Get the protocol we're set to use
     * @return int
     */
    public function protocol(): int {
        return $this->protocol;
    }

    /**
     * Set the protocol we should use
     * @param int $v
     * @return \IXP\Services\Grapher Fluid interface
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     */
    public function setProtocol( $v ): Grapher {
        if( !isset( self::PROTOCOLS[ $v ] ) ) {
            throw new ParameterException('Invalid protocol ' . $v );
        }

        $this->protocol = $v;
        return $this;
    }

    /**
     * Process user input for the parameter: category
     *
     * Note that this function just sets the default if the input is invalid.
     * If you want to force an exception in such cases, use setCategory()
     *
     * @param string $v The user input value
     * @return string The verified / sanitised / default value
     */
    public function processParameterCategory( string $v ): string {
        if( !isset( self::CATEGORY_DESC[ $v ] ) ) {
            $v = self::CATEGORY_DEFAULT;
        }
        return $this->setCategory($v)->category();
    }

    /**
     * Get the category we're set to use
     * @return string
     */
    public function category(): string {
        return $this->category;
    }

    /**
     * Set the category we should use
     * @param int $v
     * @return \IXP\Services\Grapher Fluid interface
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     */
    public function setCategory( $v ): Grapher {
        if( !isset( self::CATEGORY_DESC[ $v ] ) ) {
            throw new ParameterException('Invalid category ' . $v );
        }

        $this->category = $v;
        return $this;
    }

    /**
     * Process user input for the parameter: type
     *
     * Note that this function just sets the default if the input is invalid.
     * If you want to force an exception in such cases, use setType()
     *
     * @param string $v The user input value
     * @return string The verified / sanitised / default value
     */
    public function processParameterType( string $v ): string {
        if( !isset( self::TYPES[ $v ] ) ) {
            $v = self::TYPE_DEFAULT;
        }
        return $this->setType($v)->type();
    }

    /**
     * Get the type we're set to use
     * @return string
     */
    public function type(): string {
        return $this->type;
    }

    /**
     * Set the type we should use
     * @param int $v
     * @return \IXP\Services\Grapher Fluid interface
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     */
    public function setType( $v ): Grapher {
        if( !isset( self::TYPES[ $v ] ) ) {
            throw new ParameterException('Invalid type ' . $v );
        }

        $this->type = $v;
        return $this;
    }

    /**
     * Process user input for the parameter: ixp
     *
     * Note that this function just sets the default if the input is invalid.
     * If you want to force an exception in such cases, use setIXP()
     *
     * @param int $v The user input value
     * @return int The verified / sanitised / default value
     */
    public function processParameterIXP( int $v ): int {
        if( $v == 0 || !d2r( 'IXP' )->find( $v ) ) {
            $v = d2r( 'IXP' )->getDefault()->getId();
        }
        return $this->setIXP($v)->ixp()->getId();
    }

    /**
     * Get the IXP we're set to use
     * @return \Entities\IXP
     */
    public function ixp(): IXP {
        return $this->ixp;
    }

    /**
     * Set the IXP we should use
     * @param int $v
     * @return \IXP\Services\Grapher Fluid interface
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     */
    public function setIXP( $v ): Grapher {
        if( $v == 0 || !d2r( 'IXP' )->find( $v ) ) {
            throw new ParameterException('Invalid IXP id ' . $v );
        }

        $this->ixp = d2r( 'IXP' )->find( $v );
        return $this;
    }
}
