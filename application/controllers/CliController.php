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
 * Controller: CLI actions - needs review
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CliController extends IXP_Controller_Action
{

    /**
     * Verbose flag
     */
    private $_verbose = false;


    public function preDispatch()
    {
        //Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        if ( php_sapi_name() != 'cli' ) die( 'Unauthorised access to action!' );

        $this->_verbose = $this->getFrontController()->getParam( 'verbose' );
    }


    public function postDispatch()
    {
        print "\n";
    }


    /**
     * Demo action to demonstrate CLI action
     *
     */
    public function testAction()
    {
        print "This is a demo action.\n";
    }



    /**
     * Generate a JSON or CSV list of all contacts by a given group
     *
     * E.g.:
     *
     *     ./ixptool.php -a cli.cli-export-group -p type=ROLE,format=csv,cid=1
     *
     * Possible command line parameters are:
     *
     * * **type:** Contact group type (e.g. `ROLE`
     * * **name:** Contact group name
     * * **format:** Output format - one of `json` (default) or `csv`
     * * **sn:** Customer shortname to limit results to
     * * **cid:** Customer id to limit results to
     *
     */

    public function cliExportGroupAction()
    {
        $type   = $this->getParam( 'type',   false );
        $name   = $this->getParam( 'name',   false );
        $format = $this->getParam( 'format', false );
        $sn     = $this->getParam( 'sn',     false );
        $cid    = $this->getParam( 'cid',    false );

        if( ( !$type && !$name ) || ( $type && $name ) )
        {
            echo "ERR: Group name or type must be set (and not both).\n";
            return;
        }

        if( !$format )
            $format = 'json';

        $dql =  "SELECT c.name AS name, c.position as position, c.email AS email, c.phone AS phone, c.mobile AS mobile,
                    c.facilityaccess AS facilityacces, c.mayauthorize AS mayauthorize, c.notes as notes

             FROM \\Entities\\Contact c
                LEFT JOIN c.Groups cg
                LEFT JOIN c.Customer cu\n";

        if( $type )
            $dql .= " WHERE cg.type = :type";
        else
            $dql .= " WHERE cg.name = :name";

        if( $cid )
            $dql .= " AND cu.id = :cid";
        else if( $sn )
            $dql .= " AND cu.shortname = :sn";

        $dql .= " GROUP BY c.id";

        $q = $this->getEntityManager()->createQuery( $dql );

        if( $type )
            $q->setParameter( 'type', $type );
        else
            $q->setParameter( 'name', $name );

        if( $cid )
            $q->setParameter( 'cid', $cid );
        else if( $sn )
            $q->setParameter( 'sn', $sn );

        $contacts = $q->getArrayResult();

        if( !$contacts )
            return;

        if( $format == "csv" )
        {
            $names= [];
            foreach( $contacts[0] as $name => $data )
                $names[]= $name;

            array_unshift( $contacts, $names );
            $csv = new OSS_Csv( $contacts );
            echo $csv->getContents( $csv );
        }
        else
            echo json_encode( $contacts );
    }



}
