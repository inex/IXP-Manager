<?php

declare(strict_types=1);
namespace IXP\Tasks\Irrdb;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM;
use Entities\Customer;
use Exception;
use IXP\Exceptions\ConfigurationException;
use IXP\Exceptions\GeneralException;
use IXP\Utils\Bgpq4;
use Log;

/**
 * UpdateDb
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Irrdb
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class UpdateDb
{
    /**
     * BGPQ4 Utility Interface details object.
     *
     * @var Bgpq4
     */
    private $bgpq4 = null;

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
    private $protocols = [4,6];

    /**
     * Variable for timing
     */
    private $time     = 0.0;


    /**
     * Stardard result array
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
     * @param Customer $c
     * @throws ConfigurationException
     */
    public function __construct( Customer $c, ?array $protocols = null ) {
        $this->setCustomer( $c );

        if( $protocols !== null ) {
            $this->protocols = $protocols;
        }

        $this->setBgpq4( new Bgpq4( config( 'ixp.irrdb.bgpq4.path' ) ) );
    }

    /**
     * Set the customer member
     *
     * @param Customer $customer
     * @return UpdateDb
     */
    public function setCustomer( Customer $customer ): UpdateDb {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Get the customer
     *
     * @return Customer
     */
    public function customer(): Customer {
        return $this->customer;
    }


    /**
     * Get the protocols to update
     *
     * @return array
     */
    public function protocols(): array {
        return $this->protocols;
    }


    /**
     * Set the Bgpq4 utility
     *
     * @param Bgpq4 $bgpq4
     * @return UpdateDb
     * @throws ConfigurationException
     */
    public function setBgpq4( Bgpq4 $bgpq4 ): UpdateDb {
        $this->bgpq4 = $bgpq4;
        return $this;
    }

    /**
     * Get the Bgpq4 utility
     *
     * @return Bgpq4
     */
    public function bgpq4(): Bgpq4 {
        return $this->bgpq4;
    }

    /**
     * Start a timer
     * @return $this
     */
    protected function startTimer() {
        $this->time = microtime(true);
        return $this;
    }

    /**
     * Return time since timer started
     * @return float
     */
    protected function timeElapsed() {
        return microtime(true) - $this->time;
    }

    /**
     * Update the database IrrdbAsn table with the member's ASNs for a given protocol.
     *
     * This is transaction safe and works as follows ensuring the member's ASNs are available
     * to any script requiring them at any time.
     *
     * @param array $fromIrrdb
     * @param int $protocol The protocol to use (4 or 6)
     * @param string $type
     * @return bool
     * @throws Exception
     * @throws GeneralException
     */
    protected function updateDb( array $fromIrrdb, int $protocol, $type = 'prefix' ): bool {

        switch( $type ) {
            case 'asn':
                $dbTable = 'irrdb_asn';
                $entity  = 'Entities\IrrdbAsn';
                break;

            case 'prefix':
                $dbTable = 'irrdb_prefix';
                $entity  = 'Entities\IrrdbPrefix';
                break;

            default:
                throw new GeneralException( 'Unknown type for updating: ' . $type );
        }

        $conn = D2EM::getConnection();
        $this->startTimer();
        $fromDb = D2EM::getRepository( $entity )->getForCustomerAndProtocol( $this->customer(), $protocol );
        $this->result['dbTime'] += $this->timeElapsed();

        // The calling function and the Bgpq4 class does a lot of validation and error
        // checking. But the last thing we need to do is start filtering all prefixes/ASNs if
        // something falls through to here. So, as a basic check, make sure we do not accept
        // an empty array of prefixes/ASNs for a customer that has a lot.

        if( count( $fromIrrdb ) == 0 ) {
            // make sure the customer doesn't have a non-empty prefix/ASN set that we're about to delete
            if( count( $fromDb ) != 0 ) {
                $msg = "IRRDB {$type}: {$this->customer()->getName()} has a non-zero {$type} count for IPv{$protocol} in the database but "
                    . "BGPQ4 returned none. Please examine manually. No databases changes made for this customer.";
                Log::alert( $msg );
                $result['msg'] = $msg;
            }

            // in either case, we have nothing to do with an empty ASN list:
            return false;
        }

        $this->startTimer();

        $fromIrrdbSet = new \Ds\Set($fromIrrdb);

        foreach( $fromDb as $i => $p ) {
            if( $fromIrrdbSet->contains( $p[$type] ) ) {
                // ASN/prefix exists in both db and IRRDB - no action required
                unset($fromDb[$i]);
                $fromIrrdbSet->remove($p[$type]);
            }
        }

        $fromIrrdb = $fromIrrdbSet->toArray();

        // at this stage, the arrays are now:
        // $fromDb      => asns/prefixes in the database that need to be deleted
        // $fromIrrdb   => new asns/prefixes that need to be added

        $this->result['v'.$protocol]['stale'] = $fromDb;
        $this->result['v'.$protocol]['new']   = $fromIrrdb;

        // validate any remaining IRRDB prefixes/ASNs before we put them near the database
        $fromIrrdb = $this->validate( $fromIrrdb, $protocol );
        $this->result['procTime'] += $this->timeElapsed();

        $this->startTimer();
        $conn->beginTransaction();

        try {
            $now = date( 'Y-m-d H:i:s' );

            foreach( $fromIrrdb as $p ) {
                Log::debug( "INSERT [{$type}]: {$this->customer()->getShortname()} IPv{$protocol} {$p}" );
                $conn->executeUpdate(
                    "INSERT INTO `{$dbTable}` ( customer_id, {$type}, protocol, last_seen, first_seen ) VALUES ( ?, ?, ?, ?, ? )",
                    [ $this->customer()->getId(), $p, $protocol, $now, $now ]
                );
            }

            foreach( $fromDb as $i => $p ) {
                Log::debug( "DELETE [{$type}]: {$this->customer()->getShortname()} IPv{$protocol} ID:{$p['id']} {$p[$type]}" );
                $conn->executeUpdate(
                    "DELETE FROM `{$dbTable}` WHERE id = ?",
                        [ $p['id'] ]
                );
            }

            $conn->executeUpdate(
                "UPDATE `{$dbTable}` SET last_seen = ? WHERE customer_id = ? AND protocol = ?",
                [ $now, $this->customer()->getId(), $protocol ]
            );

            $conn->commit();

            $this->result['dbTime'] += $this->timeElapsed();
        } catch( Exception $e ) {
            $conn->rollback();
            $this->result['dbTime'] += $this->timeElapsed();
            throw $e;
        }

        return true;
    }

    /**
     * Validate ASNs/prefixes. Implement in subclasses.
     * @param array $prefixes
     * @param int $protocol
     * @return array
     */
    abstract protected function validate( array $prefixes, int $protocol ): array;
}
