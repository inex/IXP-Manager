<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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

/**
 *
 * Auto-generated Doctrine ORM File
 *
 * @category ORM
 * @package IXP_ORM_Models
 * @copyright Copyright 2008 - 2010 Internet Neutral Exchange Association Limited <info (at) inex.ie>
 * @author Barry O'Donovan <barryo (at) inex.ie>
 */
class CustTable extends Doctrine_Table
{

    /**
     * Return an array of all customer names where the array key is the customer id.
     *
     * @return array An array of all customer names with the customer id as the key.
     */
    public static function getAllNames()
    {
        $names = Doctrine_Query::create()
            ->select( 'id, name' )
            ->from( 'Cust c' )
            ->orderBy( 'name ASC' )
            ->fetchArray();

        $a = array();
        foreach( $names as $n )
            $a[$n['id']] = $n['name'];

        return $a;
    }
    
    
    /**
     * Utility function to load all customers of a given type
     * 
     * @param array|int $type The customer type (see Cust::TYPE_*)
     * @param int $hydration The Doctrine hydration method to use
     * @return The resultset in the requested hydration
     */
    public static function getByType( $type, $hydration = Doctrine_Core::HYDRATE_RECORD )
    {
        if( !is_array( $type ) )
            $type = array( $type );
            
        return Doctrine_Query::create()
            ->from( 'Cust c' )
            ->whereIn( 'c.type', $type )
            ->execute( null, $hydration );
    }

}