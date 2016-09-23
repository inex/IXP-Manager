<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * Controller: Actions for various database migrations
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DatabaseMigrationCliController extends IXP_Controller_CliAction
{

    /**
     * When upgrading to v3.4.0 - multi IXP functionality - this action adds all / any
     * existing IXPs to the default IXP.
     *
     *  Execute as: ./bin/ixptool.php -a database-migration-cli.v340-customers-to-ixps
     */
    public function v340CustomersToIxpsAction()
    {
        // ensure we are running at the correct version
        if( APPLICATION_VERSION != '3.4.0' )
            die( "ERROR: this migration is only available for version 3.4.0\n" );
        
        // and ensure we have not run it before
        $ixps = $this->getD2EM()->createQuery( 'SELECT i FROM \\Entities\\IXP i' )->getResult();
        
        if( count( $ixps ) != 1 )
            die( "ERROR: pre-migration, there should be one and only one IXP\n" );
        
        $ixp = $ixps[0];
        
        if( count( $ixp->getCustomers() ) )
            die( "ERROR: pre-migration, there should be no customers already assigned to the default IXP\n" );

        // all okay so far, assign all customers to the default IXP
        $customers = $this->getD2R( '\\Entities\\Customer' )->findAll();
        
        foreach( $customers as $c )
        {
            $ixp->addCustomer( $c );
            $c->addIXP( $ixp );
        }
        
        $this->getD2EM()->flush();
        
        echo "Migration completed successfully.\n";
    }
    
}

