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
class Traffic95thTable extends Doctrine_Table
{

    /**
     * Function to calculate the 95th percentile for a given data range.
     *
     * @param unknown_type $custid The ID of the customer to calculate for
     * @param unknown_type $start Start of period to calculcate for (Y-m-d H:i:s)
     * @param unknown_type $end End of period to calculcate for (Y-m-d H:i:s)
     * @return int The 95th percentile in bits.
     */
    public static function get95thPercentile( $custid, $start, $end )
    {
        // how many datapoints do we have?
        $count = Doctrine_Query::create()
            ->from( 'Traffic95th tm')
            ->select( 'COUNT( tm.datetime )' )
            ->where( "tm.datetime >= ?" )
            ->andWhere( "tm.datetime < ?" )
            ->andWhere( 'tm.cust_id = ?' )
            ->execute( array( $start, $end, $custid ), Doctrine_Core::HYDRATE_SINGLE_SCALAR );

        if( $count > 20 )
        {
            // we want the 95% percentile
            $index = (int)floor( $count * 0.05 );

            return Doctrine_Query::create()
                ->from( 'Traffic95TH tm' )
                ->select( 'tm.max' )
                ->where( "tm.datetime >= ?", $start )
                ->andWhere( "tm.datetime < ?", $end )
                ->andWhere( 'tm.cust_id = ?', $custid )
                ->limit( 1 )
                ->offset( $index )
                ->orderBy( 'tm.datetime DESC' )
                ->execute( null, Doctrine_Core::HYDRATE_SINGLE_SCALAR );

        }
        else
        {
            return 0;
        }
    }


}