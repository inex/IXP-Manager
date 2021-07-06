<?php

namespace IXP\Services\Grapher;

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

use IXP\Services\Grapher;

use IXP\Contracts\Grapher\Backend as GrapherBackend;

use IXP\Exceptions\Services\Grapher\{
    ParameterException
};

use Illuminate\Auth\Access\AuthorizationException;

/**
 * Grapher -> Abstract Graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Graph
{
    /**
     * Category to use
     *
     * @var
     */
    private $category = self::CATEGORY_DEFAULT;

    /**
     * Protocol to use
     *
     * @var
     */
    private $protocol = self::PROTOCOL_DEFAULT;

    /**
     * Grapher Service
     *
     * @var Grapher
     */
    private $grapher;

    /**
     * Backend to use
     *
     * @var GrapherBackend
     */
    private $backend = null;

    /**
     * Data points (essentially a cache which is wiped as appropriate)
     *
     * @var array
     */
    private $data = null;

    /**
     * Statistics object (essentially a cache which is wiped as appropriate)
     * @var Statistics
     */
    private $statistics = null;

    /**
     * Renderer object (essentially a cache which is wiped as appropriate)
     *
     * @var Renderer
     */
    private $renderer = null;


    /**
     * Period of one day for graphs
     */
    public const PERIOD_DAY   = 'day';

    /**
     * Period of one week for graphs
     */
    public const PERIOD_WEEK  = 'week';

    /**
     * Period of one month for graphs
     */
    public const PERIOD_MONTH = 'month';

    /**
     * Period of one year for graphs
     */
    public const PERIOD_YEAR  = 'year';

    /**
     * Default period
     */
    public const PERIOD_DEFAULT  = self::PERIOD_DAY;

    /**
     * Period to use
     * @var
     */
    protected $period = self::PERIOD_DEFAULT;

    /**
     * Array of valid periods for drill down graphs
     */
    public const PERIODS = [
        self::PERIOD_DAY   => self::PERIOD_DAY,
        self::PERIOD_WEEK  => self::PERIOD_WEEK,
        self::PERIOD_MONTH => self::PERIOD_MONTH,
        self::PERIOD_YEAR  => self::PERIOD_YEAR
    ];

    /**
     * Array of valid periods for drill down graphs
     */
    public const PERIOD_DESCS = [
        self::PERIOD_DAY   => 'Day',
        self::PERIOD_WEEK  => 'Week',
        self::PERIOD_MONTH => 'Month',
        self::PERIOD_YEAR  => 'Year'
    ];

    /**
     * 'Bits' category for graphs
     */
    public const CATEGORY_BITS     = 'bits';

    /**
     * 'Packets' category for graphs
     */
    public const CATEGORY_PACKETS  = 'pkts';

    /**
     * 'Errors' category for graphs
     */
    public const CATEGORY_ERRORS   = 'errs';

    /**
     * 'Discards' category for graphs
     */
    public const CATEGORY_DISCARDS = 'discs';

    /**
     * 'Broadcasts' category for graphs
     */
    public const CATEGORY_BROADCASTS = 'bcasts';

    /**
     * Default category
     */
    public const CATEGORY_DEFAULT  = self::CATEGORY_BITS;

    /**
     * Array of valid categories for graphs
     */
    public const CATEGORIES = [
        self::CATEGORY_BITS       => self::CATEGORY_BITS,
        self::CATEGORY_PACKETS    => self::CATEGORY_PACKETS,
        self::CATEGORY_ERRORS     => self::CATEGORY_ERRORS,
        self::CATEGORY_DISCARDS   => self::CATEGORY_DISCARDS,
        self::CATEGORY_BROADCASTS => self::CATEGORY_BROADCASTS,
    ];

    /**
     * Useful array of just bits and packets categories for graphs
     */
    public const CATEGORIES_BITS_PKTS = [
        self::CATEGORY_BITS     => self::CATEGORY_BITS,
        self::CATEGORY_PACKETS  => self::CATEGORY_PACKETS,
    ];

    /**
     * Array of valid categories for graphs
     */
    public const CATEGORY_DESCS = [
        self::CATEGORY_BITS       => 'Bits',
        self::CATEGORY_PACKETS    => 'Packets',
        self::CATEGORY_ERRORS     => 'Errors',
        self::CATEGORY_DISCARDS   => 'Discards',
        self::CATEGORY_BROADCASTS => 'Broadcasts',
    ];

    /**
     * Useful array of just bits and packets categories for graphs
     */
    public const CATEGORIES_BITS_PKTS_DESCS = [
        self::CATEGORY_BITS     => 'Bits',
        self::CATEGORY_PACKETS  => 'Packets',
    ];

    /**
     * Protocols for graphs
     */
    public const PROTOCOL_IPV4 = 'ipv4';

    /**
     * Protocols for graphs
     */
    public const PROTOCOL_IPV6 = 'ipv6';

    /**
     * Protocols for graphs
     */
    public const PROTOCOL_ALL = 'all';

    /**
     * Default protocol for graphs
     */
    public const PROTOCOL_DEFAULT = self::PROTOCOL_ALL;

    /**
     * Array of valid protocols
     */
    public const PROTOCOLS = [
        self::PROTOCOL_ALL  => self::PROTOCOL_ALL,
        self::PROTOCOL_IPV4 => self::PROTOCOL_IPV4,
        self::PROTOCOL_IPV6 => self::PROTOCOL_IPV6
    ];

    /**
     * Array of valid real protocols
     */
    public const PROTOCOLS_REAL = [
        self::PROTOCOL_IPV4 => self::PROTOCOL_IPV4,
        self::PROTOCOL_IPV6 => self::PROTOCOL_IPV6
    ];

    /**
     * Array of valid protocols
     */
    public const PROTOCOL_DESCS = [
        self::PROTOCOL_ALL  => 'All',
        self::PROTOCOL_IPV4 => 'IPv4',
        self::PROTOCOL_IPV6 => 'IPv6'
    ];

    /**
     * Array of valid real protocols
     */
    public const PROTOCOL_REAL_DESCS = [
        self::PROTOCOL_IPV4 => 'IPv4',
        self::PROTOCOL_IPV6 => 'IPv6'
    ];

    /**
     * Grapher file format return type: png
     *
     * @var string
     */
    public const TYPE_PNG   = 'png';

    /**
     * Grapher file format return type: log
     *
     * @var string
     */
    public const TYPE_LOG   = 'log';

    /**
     * Grapher file format return type: rrd
     *
     * @var string
     */
    public const TYPE_RRD   = 'rrd';

    /**
     * Grapher file format return type: json
     *
     * @var string
     */
    public const TYPE_JSON  = 'json';

    /**
     * Default type
     *
     * @var string
     */
    public const TYPE_DEFAULT = self::TYPE_PNG;

    /**
     * Type to use
     *
     * @var string
     */
    private $type = self::TYPE_DEFAULT;

    /**
     * Possible types and descriptions
     *
     * @var array
     */
    public const TYPES = [
        self::TYPE_PNG  => self::TYPE_PNG,
        self::TYPE_LOG  => self::TYPE_LOG,
        self::TYPE_RRD  => self::TYPE_RRD,
        self::TYPE_JSON => self::TYPE_JSON,
    ];

    /**
     * Possible types and descriptions
     *
     * @var array
     */
    public const TYPE_DESCS = [
        self::TYPE_PNG  => 'PNG',
        self::TYPE_LOG  => 'LOG',
        self::TYPE_RRD  => 'RRD',
        self::TYPE_JSON => 'JSON',
    ];

    /**
     * Possible content types
     *
     * @var array
     */
    public const CONTENT_TYPES = [
        self::TYPE_PNG  => 'image/png',
        self::TYPE_LOG  => 'application/json',
        self::TYPE_RRD  => 'application/octet-stream',
        self::TYPE_JSON => 'application/json',
    ];

    /**
     * Constructor
     *
     * @param Grapher $grapher
     */
    public function __construct( Grapher $grapher )
    {
        $this->setGrapher( $grapher );
    }

    /**
     * Get the grapher service
     *
     * @return Grapher
     */
    protected function grapher(): Grapher
    {
        return $this->grapher;
    }

    /**
     * Set the grapher service
     *
     * @param Grapher $grapher
     *
     * @return Graph
     */
    protected function setGrapher( $grapher ): Graph
    {
        $this->grapher = $grapher;
        return $this;
    }

    /**
     * For a given graph object ($this), find a backend that can process it
     *
     * @return GrapherBackend
     *
     * @throws
     */
    public function backend(): GrapherBackend
    {
        if( $this->backend === null ) {
            $this->backend = $this->grapher()->backendForGraph( $this );
        }
        return $this->backend;
    }

    /**
     * For a given graph object ($this), find its data via the backend
     *
     * @return array
     */
    public function data(): array
    {
        if( $this->data === null ) {
            $this->data = $this->grapher()->remember( $this->cacheKey('data'), function() {
                return $this->backend()->data($this);
            });
        }
        return $this->data;
    }

    /**
     * For a given graph object ($this), find its data via the backend and
     * return as JSON
     *
     * @return string
     *
     * @throws
     */
    public function log(): string
    {
        return json_encode($this->data(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    /**
     * A veritable table of contents for API access to this graph
     *
     * @return array
     *
     * @throws
     */
    public function toc(): array
    {
        $arr        = [ 'class' => $this->lcClassType() ];
        $supports   = $this->backend()->supports();

        foreach( $supports[ $this->lcClassType() ][ 'types' ] as $t ) {
            $arr[ 'urls' ][ $t ] = $this->url( [ 'type' => $t ] );
        }

        $arr[ 'base_url' ]   = url('grapher/'.$this->lcClassType());
        $arr[ 'statistics' ] = $this->statistics()->all();
        $arr[ 'params' ]     = $this->getParamsAsArray();
        $arr[ 'supports' ]   = $supports[ $this->lcClassType() ];
        $arr[ 'backends' ]   = [];

        foreach( $this->grapher()->backendsForGraph($this) as $backend ) {
            $arr[ 'backends' ][ $backend->name() ]   = $backend->name();
        }

        $arr['backend']    = $this->backend()->name();

        return $arr;
    }

    /**
     * For a given graph object ($this), find its data via the backend and
     * return as JSON
     *
     * @return string
     *
     * @throws
     */
    public function json(): string
    {
        return json_encode( $this->toc(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT );
    }

    /**
     * Generate a URL to get this graphs 'file' of a given type
     *
     * **NB:** should be overridden in subclasses as appropriate!
     *
     * @param array $overrides Allow standard parameters to be overridden (e.g. category)
     *
     * @return string
     */
    public function url( array $overrides = [] ): string
    {
        return url('grapher/' . $this->lcClassType()) . sprintf( "?period=%s&type=%s&category=%s&protocol=%s" ,
            $overrides[ 'period' ]   ?? $this->period(),
            $overrides[ 'type' ]     ?? $this->type(),
            $overrides[ 'category' ] ?? $this->category(),
            $overrides[ 'protocol' ] ?? $this->protocol()
        );
    }

    /**
     * For a given graph object ($this), get it's png
     *
     * @return string
     */
    public function png(): string
    {
        return $this->grapher()->remember( $this->cacheKey('png' ), function() {
            return $this->backend()->png( $this );
        });
    }

    /**
     * Get the path to the graphing data file (e.g. path to log or rrd file).
     *
     * @return string
     */
    public function dataPath(): string
    {
        return $this->backend()->dataPath( $this );
    }

    /**
     * For a given graph object ($this), get it's rrd
     *
     * @return string
     */
    public function rrd(): string
    {
        return $this->grapher()->remember( $this->cacheKey('rrd'), function() {
            return $this->backend()->rrd($this);
        });
    }

    /**
     * For a given graph object ($this), calculate various statistics
     *
     * @return Statistics
     */
    public function statistics(): Statistics
    {
        if( $this->statistics === null ) {
            $this->statistics = new Statistics( $this );
        }
        return $this->statistics;
    }

    /**
     * For a given graph object ($this), render it
     *
     * @return Renderer
     */
    public function renderer(): Renderer
    {
        if( $this->renderer === null ) {
            $this->renderer = new Renderer( $this );
        }

        return $this->renderer;
    }

    /**
     * We cache certain data (e.g. backend, data, statistics). This needs to be wiped
     * if certain graph parameters are changed.
     */
    public function wipe(): void
    {
        $this->backend    = null;
        $this->data       = null;
        $this->statistics = null;
        $this->renderer   = null;
    }

    /**
     * Return the class name less the IXP\Grapher\Graph\ namespace
     *
     * @return string
     */
    public function classType(): string
    {
        $class  = explode( '\\', get_class( $this ) );
        return array_pop( $class );
    }

    /**
     * Return the class name less the IXP\Grapher\Graph\ namespace as lower case
     *
     * @return string
     */
    public function lcClassType(): string
    {
        return strtolower( $this->classType() );
    }

    /**
     * A function to generate a cache key for a given graph object
     *
     * @param string $type The 'type' to append to the key (e.g. png, log, rrd, etc.)
     *
     * @return string
     */
    public function cacheKey( string $type = '' ): string
    {
        return 'grapher::' . $this->identifier()
            . '-proto' . $this->protocol()
            . '-' . $this->category()
            . '-' . $this->period()
            . '-' . $this->type()
            . '.' . $type;
    }

    /**
     * The name of a graph (e.g. member name, IXP name, etc)
     *
     * @return string
     */
    abstract public function name(): string;

    /**
     * The title of a graph (e.g. member name, IXP name, etc)
     *
     * Example: ORGNAME :: Graph->name() :: PROTOCOL
     *
     * @return string
     */
    public function title(): string
    {
        return config( 'identity.orgname') . " :: " . $this->name()
            . ( $this->protocol() === self::PROTOCOL_ALL ? '' : ' :: ' . self::PROTOCOL_DESCS[ $this->protocol() ] );
    }

    /**
     * Watermark for graphs (e.g. member name, IXP name, etc)
     *
     * Example: ORGNAME :: Graph->name() :: PROTOCOL
     *
     * @return string
     */
    public function watermark(): string
    {
        return (string)config( 'identity.watermark' );
    }

    /**
     * A unique identifier for this 'graph type'
     *
     * E.g. for an IXP, it might be ixpxxx where xxx is the database id
     *
     * @return string
     */
    abstract public function identifier(): string;

    /**
     * A simple key for this graph
     *
     * e.g. grapher-ixp002-ipv4-bits-day-rrd
     *
     * @return string
     */
    public function key(): string
    {
        return 'grapher-' . $this->identifier()
            . '-' . $this->protocol()
            . '-' . $this->category()
            . '-' . $this->period()
            . '-' . $this->type();
    }

    /**
     * This function controls access to the graph.
     *
     * You can check user privileges / membership / etc. and then call allow()
     * to permit access or deny() to deny it. These methods can also be
     * overridden.
     *
     * In it's default incarnation, this will **always** fail. You need to explicitly
     * allow graph access based on your own requirements.
     *
     * @return bool
     *
     * @throws
     */
    public function authorise(): bool
    {
        $this->deny();
        return false;
    }

    /**
     * Action to take when authorisation fails.
     *
     * @throws AuthorizationException
     *
     * @param string $message Deny message
     */
    protected function deny( $message = 'This action is unauthorized.' ): void
    {
        throw new AuthorizationException($message);
    }

    /**
    * Action to take when authorisation succeeds.
     *
    * @return bool
     */
    protected function allow(): bool
    {
        return true;
    }

    /**
     * Get the period we're set to use
     * @return string
     */
    public function period(): string
    {
        return $this->period;
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
        if( !isset( $this::PERIODS[ $v ] ) ) {
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
        return self::PERIOD_DESCS[ $period ] ?? 'Unknown';
    }

    /**
     * Get the protocol we're set to use
     *
     * @return string
     */
    public function protocol(): string
    {
        return $this->protocol;
    }

    /**
     * Set the protocol we should use
     *
     * @param string $v
     *
     * @return Graph Fluid interface
     *
     * @throws ParameterException
     */
    public function setProtocol( string $v ): Graph
    {
        if( !isset( $this::PROTOCOLS[ $v ] ) ) {
            throw new ParameterException('Invalid protocol ' . $v );
        }

        if( $this->protocol() !== $v ) {
            $this->wipe();
        }

        $this->protocol = $v;
        return $this;
    }

    /**
     * Get the protocol description for a given protocol identifier
     *
     * @param string $protocol
     *
     * @return string
     */
    public static function resolveProtocol( string $protocol ): string
    {
        return self::PROTOCOL_DESCS[ $protocol ] ?? 'Unknown';
    }

    /**
     * Get the category we're set to use
     *
     * @return string
     */
    public function category(): string
    {
        return $this->category;
    }

    /**
     * Get the category description
     *
     * @return string
     */
    public function resolveMyCategory(): string
    {
        return self::CATEGORY_DESCS[ $this->category() ] ?? 'Unknown';
    }

    /**
     * Get the category description for a given category identifier
     * @param string $category
     *
     * @return string
     */
    public static function resolveCategory( string $category ): string
    {
        return self::CATEGORY_DESCS[ $category ] ?? 'Unknown';
    }

    /**
     * Set the category we should use
     *
     * @param string $v
     *
     * @return Graph Fluid interface
     *
     * @throws ParameterException
     */
    public function setCategory( string $v ): Graph
    {
        if( !isset( $this::CATEGORIES[ $v ] ) ) {
            throw new ParameterException('Invalid category ' . $v );
        }

        if( $this->category() !== $v ) {
            $this->wipe();
        }

        $this->category = $v;
        return $this;
    }

    /**
     * Get the type we're set to use
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * Set the type we should use
     *
     * @param string $v
     *
     * @return Graph Fluid interface
     *
     * @throws ParameterException
     */
    public function setType( string $v ): Graph
    {
        if( !isset( $this::TYPES[ $v ] ) ) {
            throw new ParameterException('Invalid type ' . $v );
        }

        if( $this->type() !== $v ) {
            $this->wipe();
        }

        $this->type = $v;
        return $this;
    }

    /**
     * Set parameters in bulk from associative array
     *
     * Base function supports keys: type, protocol, category, period
     *
     * Will pass through thrown exceptions from setXXX() functions
     *
     * @param array $params
     *
     * @return Graph Fluid interface
     */
    public function setParamsFromArray( array $params ): Graph
    {
        foreach( [ 'type', 'category', 'period', 'protocol'] as $param ){
            if( isset( $params[$param] ) ) {
                $fn = 'set' . ucfirst( $param );
                $this->$fn( $params[$param] );
            }
        }
        return $this;
    }

    /**
     * Get parameters in bulk as associative array
     *
     * Base function supports keys: type, protocol, category, period
     *
     * @return array $params
     */
    public function getParamsAsArray(): array
    {
        $p = [];
        foreach( [ 'type', 'category', 'period', 'protocol'] as $param ){
            $p[ $param ] = $this->$param();
        }
        return $p;
    }

    /**
     * Process user input for the parameter: period
     *
     * Note that this function just sets the default if the input is invalid.
     * If you want to force an exception in such cases, use setPeriod()
     *
     * @param string|null  $v The user input value
     * @param string|null  $d The preferred default value
     *
     * @return string The verified / sanitised / default value
     */
    public static function processParameterPeriod( $v = null, $d = null ): string
    {
        if( !isset( self::PERIODS[ $v ] ) ) {
            $v = $d ?? self::PERIOD_DEFAULT;
        }
        return $v;
    }

    /**
     * Process user input for the parameter: protocol
     *
     * Note that this function just sets the default if the input is invalid.
     * If you want to force an exception in such cases, use setProtocol()
     *
     * @param string|null $v The user input value
     *
     * @return string The verified / sanitised / default value
     */
    public static function processParameterProtocol( $v = null ): string
    {
        if( !isset( self::PROTOCOLS[ $v ] ) ) {
            $v = self::PROTOCOL_DEFAULT;
        }
        return $v;
    }

    /**
     * Process user input for the parameter: protocol (real only, not both)
     *
     * Note that this function just sets the default (ipv4) if the input is invalid.
     * If you want to force an exception in such cases, use setProtocol()
     *
     * @param string|null $v The user input value
     * @return string The verified / sanitised / default value
     */
    public static function processParameterRealProtocol( $v = null ): string
    {
        if( !isset( self::PROTOCOLS_REAL[ $v ] ) ) {
            $v = self::PROTOCOL_IPV4;
        }
        return $v;
    }

    /**
     * Process user input for the parameter: category
     *
     * Note that this function just sets the default if the input is invalid.
     * If you want to force an exception in such cases, use setCategory()
     *
     * @param string|null $v The user input value
     * @param bool $bits_pkts_only
     *
     * @return string The verified / sanitised / default value
     */
    public static function processParameterCategory( $v = null, $bits_pkts_only = false ): string
    {
        if( ( $bits_pkts_only && !isset( self::CATEGORIES_BITS_PKTS[$v] ) ) || ( !$bits_pkts_only && !isset( self::CATEGORIES[ $v ] ) ) ) {
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
     * @param string|null $v The user input value
     *
     * @return string The verified / sanitised / default value
     */
    public static function processParameterType( $v = null ): string
    {
        if( !isset( self::TYPES[ $v ] ) ) {
            $v = self::TYPE_DEFAULT;
        }
        return $v;
    }
}