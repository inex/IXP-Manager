<?php

namespace Repositories\Traits;

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

use Entities\{
    IPv4Address  as IPv4AddressEntity,
    IPv6Address  as IPv6AddressEntity,
    Vlan         as VlanEntity
};
use Exception;


/**
 * IPAddress Trait
 *
 */
trait IPAddress {

    private function getEntityNameFromCalledRepository() {
        if( substr( get_called_class(), -11, 4 ) == 'IPv6' ) {
            return IPv6AddressEntity::class;
        } else if( substr( get_called_class(), -11, 4 ) == 'IPv4' ) {
            return IPv4AddressEntity::class;
        }

        throw new Exception('Programming error - unknown repository using IP Address trait.' );
    }

    /**
     * Bulk add IP addresses from the given array.
     *
     * The array returned contains two futher arrays:
     *
     * * `preexisting` => addresses that already existed in the database.
     * * `new`         => addresses added (if `skip == true`) or addresses that would have been added.
     *
     * @param array $addresses
     * @param VlanEntity $vlan
     * @param bool $skip If the address already exists, then skip over it (default). Otherwise, do not add any addresses.
     * @return array
     * @throws Exception
     */
    public function bulkAdd( array $addresses, VlanEntity $vlan, bool $skip = true )
    {
        $entity = $this->getEntityNameFromCalledRepository();

        $results = [
            'preexisting'  => [],
            'new'          => []
        ];

        $this->getEntityManager()->getConnection()->beginTransaction();

        try {

            foreach( $addresses as $a ) {
                // does the address already exist?
                if( $ipentity = $this->getEntityManager()->getRepository( $entity )->findOneBy( [ 'address' => $a, 'Vlan' => $vlan ] ) ) {
                    $results[ 'preexisting' ][] = $a;
                } else {
                    $ipentity = new $entity();
                    $ipentity->setVlan( $vlan );
                    $ipentity->setAddress( $a );
                    $this->getEntityManager()->persist( $ipentity );
                    $results['new'][] = $a;
                }
            }

            if( !$skip && count( $results['preexisting'] ) ) {
                $this->getEntityManager()->getConnection()->rollBack();
            } else {
                $this->getEntityManager()->flush();
                $this->getEntityManager()->getConnection()->commit();
            }

        } catch (Exception $e) {
            $this->getEntityManager()->getConnection()->rollBack();
            throw $e;
        }

        return $results;
    }

    /**
     * Find any existing but unallocated IP addresses in the given VLAN from the list provided.
     *
     * Sample usage:
     *
     *     $ips = D2EM::getRepository( IPv4AddressEntity::class )->getFreeAddressesFromList( $v,
     *         D2EM::getRepository( IPv4AddressEntity::class )->generateSequentialAddresses( $network )
     *     );
     *
     * @param VlanEntity $vlan
     * @param array $list
     * @return array
     * @throws
     */
    public function getFreeAddressesFromList( VlanEntity $vlan, array $list ): array
    {
        $entity = $this->getEntityNameFromCalledRepository();
        $free = [];

        foreach( $list as $a ) {
            // does the address exist
            if( $ipentity = $this->getEntityManager()->getRepository( $entity )->findOneBy( [ 'address' => $a, 'Vlan' => $vlan ] ) ) {
                // and is it allocated?
                if( $ipentity->getVlanInterface() ) {
                    continue;
                }

                $free[] = $ipentity;
            }
        }

        return $free;
    }
}

