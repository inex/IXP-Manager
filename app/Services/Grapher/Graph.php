<?php namespace IXP\Services\Grapher;

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

use IXP\Services\Grapher;
use IXP\Contracts\Grapher\Backend as GrapherBackend;

/**
 * Grapher -> Abstract Graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (c) 2009 - 2016, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Graph {

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
     * Grapher Service
     * @var IXP\Services\Grapher
     */
    private $grapher;

    /**
     * Backend to use
     * @var IXP\Contracts\Grapher\Backend
     */
    private $backend = null;

    /**
     * Data points (essentially a cache which is wiped as appropriate)
     * @var array
     */
    private $data = null;

    /**
     * Statistics object (essentially a cache which is wiped as appropriate)
     * @var IXP\Services\Grapher\Statistics
     */
    private $statistics = null;

    /**
     * Renderer object (essentially a cache which is wiped as appropriate)
     * @var IXP\Services\Grapher\Renderer
     */
    private $renderer = null;


    /**
     * Constructor
     */
    public function __construct( Grapher $grapher ) {
        $this->setGrapher( $grapher );
    }

    /**
     * Get the grapher service
     * @return IXP\Services\Grapher
     */
    protected function grapher(): Grapher {
        return $this->grapher;
    }

    /**
     * Set the grapher service
     * @param IXP\Services\Grapher $grapher
     * @return IXP\Services\Grapher\Graph
     */
    protected function setGrapher( $grapher ): Graph {
        $this->grapher = $grapher;
        return $this;
    }


    /**
     * For a given graph object ($this), find a backend that can process it
     *
     * @return IXP\Contracts\Grapher\Backend
     */
    public function backend(): GrapherBackend {
        if( $this->backend === null ) {
            $this->backend = $this->grapher()->backendForGraph( $this );
        }
        return $this->backend;
    }

    /**
     * For a given graph object ($this), find its data via the backend
     *
     * @return IXP\Contracts\Grapher\Backend
     */
    public function data(): array {
        if( $this->data === null ) {
            $this->data = $this->backend()->data($this);
        }
        return $this->data;
    }

    /**
     * For a given graph object ($this), calculate various statistics
     *
     * @return IXP\Contracts\Grapher\Statistics
     */
    public function statistics(): Statistics {
        if( $this->statistics === null ) {
            $this->statistics = new Statistics( $this );
        }
        return $this->statistics;
    }

    /**
     * For a given graph object ($this), render it
     *
     * @return IXP\Contracts\Grapher\Renderer
     */
    public function renderer(): Renderer {
        if( $this->renderer === null ) {
            $this->renderer = new Renderer( $this );
        }
        return $this->renderer;
    }


    /**
     * We cache certain data (e.g. backend, data, statistics). This needs to be wiped
     * if certain graph parameters are changed.
     */
    public function wipe() {
        $this->backend    = null;
        $this->data       = null;
        $this->statistics = null;
        $this->renderer   = null;
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
    public function setPeriod( $v ): Graph {
        if( !isset( self::PERIOD_DESCS[ $v ] ) ) {
            throw new ParameterException('Invalid period ' . $v );
        }

        if( $this->period() != $v ) {
            $this->wipe();
        }

        $this->period = $v;
        return $this;
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
    public function setProtocol( $v ): Graph {
        if( !isset( self::PROTOCOLS[ $v ] ) ) {
            throw new ParameterException('Invalid protocol ' . $v );
        }

        if( $this->protocol() != $v ) {
            $this->wipe();
        }

        $this->protocol = $v;
        return $this;
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
    public function setCategory( $v ): Graph {
        if( !isset( self::CATEGORY_DESC[ $v ] ) ) {
            throw new ParameterException('Invalid category ' . $v );
        }

        if( $this->category() != $v ) {
            $this->wipe();
        }

        $this->category = $v;
        return $this;
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
    public function setType( $v ): Graph {
        if( !isset( self::TYPES[ $v ] ) ) {
            throw new ParameterException('Invalid type ' . $v );
        }

        if( $this->type() != $v ) {
            $this->wipe();
        }

        $this->type = $v;
        return $this;
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
    public static function processParameterPeriod( string $v ): string {
        if( !isset( self::PERIOD_DESCS[ $v ] ) ) {
            $v = self::PERIOD_DEFAULT;
        }
        return $v;
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
    public static function processParameterProtocol( int $v ): int {
        if( !isset( self::PROTOCOLS[ $v ] ) ) {
            $v = self::PROTOCOL_DEFAULT;
        }
        return $v;
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
    public static function processParameterCategory( string $v ): string {
        if( !isset( self::CATEGORY_DESC[ $v ] ) ) {
            $v = self::CATEGORY_DEFAULT;
        }
        return $v;
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
    public static function processParameterType( string $v ): string {
        if( !isset( self::TYPES[ $v ] ) ) {
            $v = self::TYPE_DEFAULT;
        }
        return $v;
    }
}
