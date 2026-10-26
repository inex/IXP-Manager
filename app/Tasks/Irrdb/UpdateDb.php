<?php

declare(strict_types=1);
namespace IXP\Tasks\Irrdb;

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\{Aggregators\IrrdbAggregator, Customer, IrrdbAsn, IrrdbPrefix, IrrdbUpdateLog};

use IXP\Contracts\IrrQuerier;

/**
 * UpdateDb
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin      <yann@islandbridgenetworks.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Irrdb
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class UpdateDb
{
    /**
     * IrrQuerier utility
     *
     * @var IrrQuerier
     */
    private IrrQuerier $irrdb;

    /**
     * Customer to update prefixes of
     *
     * @var Customer
     */
    private Customer $customer;

    /**
     * Protocols to update
     *
     * @var array
     */
    private array $protocols = [ 4,6 ];

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
     * @param IrrQuerier    $irrQuerier
     * @param Customer      $c
     * @param array|null    $protocols
     *
     * @throws ConfigurationException
     */
    public function __construct( IrrQuerier $irrQuerier, Customer $c, ?array $protocols = null ) {
        $this->setCustomer( $c );

        if( $protocols !== null ) {
            $this->protocols = $protocols;
        }

        $this->setIrrQuerier( $irrQuerier );
    }

    /**
     * Set the customer member
     *
     * @param Customer $customer
     */
    public function setCustomer( Customer $customer ): static
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
     * Set the IrrQuerier utility
     *
     * @param IrrQuerier $irrdb
     *
     */
    public function setIrrQuerier( IrrQuerier $irrdb ): static
    {
        $this->irrdb = $irrdb;
        return $this;
    }

    /**
     * Get the IrrQuerier utility
     *
     * @return IrrQuerier
     */
    public function irrdb(): IrrQuerier
    {
        return $this->irrdb;
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
     * @throws GeneralException|\Throwable
     *
     * @psalm-param 'asn'|'prefix' $type
     */
    protected function updateDb( array $fromIrrdb, int $protocol, string $type = 'prefix' ): bool
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

        $table  = ( new $model )->getTable();
        $field  = $type;                         // 'prefix' | 'asn' — column name equals $type
        $custId = $this->customer()->id;

        $this->startTimer();
        $existingCount = DB::table( $table )->where( 'customer_id', $custId )
            ->where( 'protocol', $protocol )->count();
        $this->result['dbTime'] += $this->timeElapsed();

        // The calling function and the Bgpq3 class does a lot of validation and error
        // checking. But the last thing we need to do is start filtering all prefixes/ASNs if
        // something falls through to here. So, as a basic check, make sure we do not accept
        // an empty array of prefixes/ASNs for a customer that has a lot.
        if( count( $fromIrrdb ) === 0 ) {
            // make sure the customer doesn't have a non-empty prefix/ASN set that we're about to delete
            if( $existingCount !== 0 ) {
                $msg = "IRRDB {$type}: {$this->customer()->name} has a non-zero {$type} count for IPv{$protocol} in the database but "
                    . "BGPQ3 returned none. Please examine manually. No databases changes made for this customer.";
                Log::alert( $msg );
                $this->result['msg'] = $msg;
            }

            // in either case, we have nothing to do with an empty ASN list:
            return false;
        }

        $this->startTimer();

        // Diff without materialising the existing set: start from the IRRDB set, then
        // stream the rows already in the database, removing common members and collecting
        // the stale rows. Runs and closes before beginTransaction() so no result set is open.
        $toInsert = new \Ds\Set( $fromIrrdb );
        $stale    = [];

        foreach( DB::table( $table )->where( 'customer_id', $custId )->where( 'protocol', $protocol )
                     ->select( 'id', $field )->cursor() as $row ) {
            if( $toInsert->contains( $row->{$field} ) ) {
                // prefix/ASN exists in both db and IRRDB - no action required
                $toInsert->remove( $row->{$field} );
            } else {
                $stale[] = [ 'id' => $row->id, $field => $row->{$field} ];
            }
        }

        $new = $toInsert->toArray();
        unset( $toInsert );

        // at this stage:
        // $stale => prefixes/ASNs in the database that need to be deleted
        // $new   => new prefixes/ASNs that need to be added
        $this->result[ 'v'.$protocol ][ 'stale' ] = $stale;
        $this->result[ 'v'.$protocol ][ 'new' ]   = $new;

        // validate any remaining IRRDB prefixes/ASNs before we put them near the database
        $new = $this->validate( $new, $protocol );

        $this->result['procTime'] += $this->timeElapsed();

        $this->startTimer();

        DB::beginTransaction();

        try {
            $now = now()->format( 'Y-m-d H:i:s' );

            foreach( array_chunk( $new, 1000 ) as $chunk ) {
                $insrows = [];
                foreach( $chunk as $v ) {
                    $insrows[] = [
                        'customer_id' => $custId,
                        $field        => $v,
                        'protocol'    => $protocol,
                        'first_seen'  => $now,
                        'last_seen'   => $now,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];
                }
                DB::table( $table )->insert( $insrows );
            }

            foreach( array_chunk( array_column( $stale, 'id' ), 1000 ) as $ids ) {
                DB::table( $table )->whereIn( 'id', $ids )->delete();
            }

            DB::table( $table )->where( 'customer_id', $custId )
                ->where( 'protocol', $protocol )
                ->update( [ 'last_seen' => $now, 'updated_at' => $now ] );

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

        $this->logUpdate( $protocol, $type );

        return true;
    }

    /**
     * Update the database to record that a IRRDB update completed successfully.
     *
     * @param int       $protocol   The protocol to use (4 or 6)
     * @param string    $type
     */
    protected function logUpdate( int $protocol, string $type ): void
    {
        IrrdbUpdateLog::updateOrCreate(
            [ 'cust_id' => $this->customer()->id ],
            [ "{$type}_v{$protocol}" => now() ]
        );
    }

    /**
     * Validate ASNs/prefixes. Implement in subclasses.
     *
     * @param array $entries
     * @param int $protocol
     *
     * @return array
     */
    abstract protected function validate( array $entries, int $protocol ): array;
}
