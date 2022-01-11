<?php

declare(strict_types=1);
namespace IXP\Tasks\Irrdb;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use DB, Exception, Log;

use IXP\Exceptions\{
    ConfigurationException,
    GeneralException
};

use IXP\Models\{
    Aggregators\IrrdbAggregator,
    Customer,
    IrrdbAsn,
    IrrdbPrefix
};

use Illuminate\Support\Facades\Cache;
use IXP\Utils\Bgpq3;

/**
 * UpdateDb
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin      <yann@islandbridgenetworks.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Irrdb
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class UpdateDb
{
    /**
     * BGPQ3 Utility Interface details object.
     *
     * @var Bgpq3
     */
    private $bgpq3 = null;

    /**
     * Customer to update prefixes of
     *
     * @var Customer
     */
    private $customer = null;

    /**
     * Protocols to update
     *
     * @var array
     */
    private $protocols = [ 4,6 ];

    /**
     * Variable for timing
     */
    private $time     = 0.0;

    /**
     * Standard result array
     *
     * @var array
     */
    protected $result = [
        'wiped'     => false,
        'v4'        => [
            'count'     => 0,
            'stale'     => [],
            'new'       => [],
            'dbUpdated' => false,
        ],
        'v6'        => [
            'count'     => 0,
            'stale'     => [],
            'new'       => [],
            'dbUpdated' => false,
        ],
        'netTime'   => 0.0,
        'dbTime'    => 0.0,
        'procTime'  => 0.0,
        'msg'       => null,
    ];

    /**
     * UpdatePrefixDb constructor.
     *
     * @param Customer      $c
     * @param array|null    $protocols
     *
     * @throws ConfigurationException
     */
    public function __construct( Customer $c, ?array $protocols = null ) {
        $this->setCustomer( $c );

        if( $protocols !== null ) {
            $this->protocols = $protocols;
        }

        $this->setBgpq3( new Bgpq3( config( 'ixp.irrdb.bgpq3.path' ) ) );
    }

    /**
     * Set the customer member
     *
     * @param Customer $customer
     *
     * @return UpdateDb
     */
    public function setCustomer( Customer $customer ): UpdateDb
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Get the customer
     *
     * @return Customer
     */
    public function customer(): Customer
    {
        return $this->customer;
    }

    /**
     * Get the protocols to update
     *
     * @return array
     */
    public function protocols(): array
    {
        return $this->protocols;
    }

    /**
     * Set the Bgpq3 utility
     *
     * @param Bgpq3 $bgpq3
     *
     * @return UpdateDb
     *
     * @throws
     */
    public function setBgpq3( Bgpq3 $bgpq3 ): UpdateDb
    {
        $this->bgpq3 = $bgpq3;
        return $this;
    }

    /**
     * Get the Bgpq3 utility
     *
     * @return Bgpq3
     */
    public function bgpq3(): Bgpq3
    {
        return $this->bgpq3;
    }

    /**
     * Start a timer
     * @return $this
     */
    protected function startTimer(): self
    {
        $this->time = microtime(true);
        return $this;
    }

    /**
     * Return time since timer started
     * @return float
     */
    protected function timeElapsed(): float
    {
        return microtime(true) - $this->time;
    }

    /**
     * Update the database IrrdbAsn table with the member's ASNs for a given protocol.
     *
     * This is transaction safe and works as follows ensuring the member's ASNs are available
     * to any script requiring them at any time.
     *
     * @param array     $fromIrrdb
     * @param int       $protocol   The protocol to use (4 or 6)
     * @param string    $type
     *
     * @return bool
     *
     * @throws
     */
    protected function updateDb( array $fromIrrdb, int $protocol, $type = 'prefix' ): bool
    {
        switch( $type ) {
            case 'asn':
                $model      = IrrdbAsn::class; /** @var IrrdbAsn $model  */
                break;
            case 'prefix':
                $model      = IrrdbPrefix::class; /** @var IrrdbPrefix $model  */
                break;
            default:
                throw new GeneralException( 'Unknown type for updating: ' . $type );
        }

        $this->startTimer();
        $fromDb = IrrdbAggregator::forCustomerAndProtocol( $this->customer()->id, $protocol, $type );
        $this->result['dbTime'] += $this->timeElapsed();

        // The calling function and the Bgpq3 class does a lot of validation and error
        // checking. But the last thing we need to do is start filtering all prefixes/ASNs if
        // something falls through to here. So, as a basic check, make sure we do not accept
        // an empty array of prefixes/ASNs for a customer that has a lot.

        if( count( $fromIrrdb ) === 0 ) {
            // make sure the customer doesn't have a non-empty prefix/ASN set that we're about to delete
            if( count( $fromDb ) !== 0 ) {
                $msg = "IRRDB {$type}: {$this->customer()->name} has a non-zero {$type} count for IPv{$protocol} in the database but "
                    . "BGPQ3 returned none. Please examine manually. No databases changes made for this customer.";
                Log::alert( $msg );
                $result['msg'] = $msg;
            }

            // in either case, we have nothing to do with an empty ASN list:
            return false;
        }

        $this->startTimer();

        $fromIrrdbSet = new \Ds\Set( $fromIrrdb );

        foreach( $fromDb as $i => $p ) {
            if( $fromIrrdbSet->contains( $p[ $type ] ) ) {
                // ASN/prefix exists in both db and IRRDB - no action required
                unset( $fromDb[ $i ] );
                $fromIrrdbSet->remove( $p[$type] );
            }
        }

        $fromIrrdb = $fromIrrdbSet->toArray();

        // at this stage, the arrays are now:
        // $fromDb      => asns/prefixes in the database that need to be deleted
        // $fromIrrdb   => new asns/prefixes that need to be added

        $this->result[ 'v'.$protocol ][ 'stale' ] = $fromDb;
        $this->result[ 'v'.$protocol ][ 'new' ]   = $fromIrrdb;

        // validate any remaining IRRDB prefixes/ASNs before we put them near the database
        $fromIrrdb = $this->validate( $fromIrrdb, $protocol );

        $this->result['procTime'] += $this->timeElapsed();

        $this->startTimer();

        DB::beginTransaction();

        try {
            $now = now()->format( 'Y-m-d H:i:s' );

            foreach( $fromIrrdb as $p ) {
                Log::debug( "INSERT [{$type}]: {$this->customer()->shortname} IPv{$protocol} {$p}" );
                $model::create(
                    [
                        'customer_id'   => $this->customer()->id,
                        $type           => $p,
                        'protocol'      => $protocol,
                        'last_seen'     => $now,
                        'first_seen'    => $now,
                    ]
                );
            }

            foreach( $fromDb as $i => $p ) {
                Log::debug( "DELETE [{$type}]: {$this->customer()->shortname} IPv{$protocol} ID:{$p['id']} {$p[$type]}" );
                $model::where( 'id', $p['id'] )->delete();
            }

            $model::where( 'customer_id', $this->customer()->id )
                ->where( 'protocol', $protocol )
                ->update( [ 'last_seen' => $now ] );

            DB::commit();
            $this->result['dbTime'] += $this->timeElapsed();

            // Store the prefixes to cache to speed up route server configuration generation.
            if( $type === 'asn' ) {
                IrrdbAggregator::asnsForRouterConfiguration( $this->customer(), $protocol, true );
            } else {
                IrrdbAggregator::prefixesForRouterConfiguration( $this->customer(), $protocol, true );
            }

        } catch( Exception $e ) {
            DB::rollBack();
            $this->result['dbTime'] += $this->timeElapsed();
            throw $e;
        }

        return true;
    }

    /**
     * Validate ASNs/prefixes. Implement in subclasses.
     *
     * @param array $prefixes
     * @param int $protocol
     *
     * @return array
     */
    abstract protected function validate( array $prefixes, int $protocol ): array;
}