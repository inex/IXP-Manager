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
     * Mailing list initialisation script
     *
     * First sets a user preference for ALL users *WITHOUT* a mailing list sub for this list to unsub'd.
     *
     * Then takes a list of *existing* mailing list addresses from stdin and:
     *   - is a user does not exist with same email, skips
     *   - if a user does exist with same email, sets his mailing list preference
     *
     * NB: This function is NON-DESTRUCTIVE. It will *NOT* affect any users with *EXISTING* settings
     * but set those without a setting to on / off as appropriate.
     *
     */
    public function mailingListInitAction()
    {
        $list = $this->_getMailingList();

        $stdin = fopen( "php://stdin","r" );
        $addresses = array();

        while( $address = strtolower( trim( fgets( $stdin ) ) ) )
            $addresses[] = $address;

        fclose( $stdin );

        if( $this->_verbose ) echo "Setting mailing list subscription for all users without a subscription setting...\n";
        $users = $this->getD2EM()->getRepository( '\\Entities\\User' )->findAll();

        foreach( $users as $u )
        {
            if( $u->hasPreference( "mailinglist.{$list}.subscribed" ) )
                continue;

            if( in_array( $u->getEmail(), $addresses ) )
                $u->setPreference( "mailinglist.{$list}.subscribed", 1 );
            else
                $u->setPreference( "mailinglist.{$list}.subscribed", 0 );
        }

        $this->getD2EM()->flush();
    }

    /**
     * Mailing list subscribed action - list all addresses subscribed to the given list
     */
    public function mailingListSubscribedAction()
    {
        $list = $this->_getMailingList();

        $users = $this->getD2EM()->getRepository( '\\Entities\\User' )->getMailingListSubscribers( $list, 1 );

        foreach( $users as $user )
            echo "{$user['email']}\n";
    }

    /**
     * Mailing list unsubscribed action - list all addresses not subscribed to the given list
     */
    public function mailingListUnsubscribedAction()
    {
        $list = $this->_getMailingList();

        $users = $this->getD2EM()->getRepository( '\\Entities\\User' )->getMailingListSubscribers( $list, 0 );

        foreach( $users as $user )
            echo "{$user['email']}\n";
    }

    /**
     * Mailing list password sync - create and execute commands to set mailing list p/w of subscribers
     */
    public function mailingListPasswordSyncAction()
    {
        $list = $this->_getMailingList();

        // we'll sync by default so only if we're told not to will the following be true:
        if( isset( $this->_options['mailinglists'][$list]['syncpws'] ) && !$this->_options['mailinglists'][$list]['syncpws'] )
        {
            if( $this->_verbose )
                die( "{$list}: Password sync for the given mailing list is disabled" );
            die();
        }

        $users = $this->getD2EM()->getRepository( '\\Entities\\User' )->getMailingListSubscribers( $list, 1 );

        foreach( $users as $user )
        {
            $cmd = sprintf( "{$this->_options['mailinglist']['cmd']['changepw']} %s %s %s",
                escapeshellarg( $list ), escapeshellarg( $user['email'] ), escapeshellarg( $user['password'] )
            );

            if( $this->_verbose ) echo "$cmd\n";

            if( !$this->getParam( 'noexec', false ) )
                exec( $cmd );
        }
    }

    /**
     * Mailing list syncronisation - generates a shell script for all mailing lists
     */
    public function mailingListSyncScriptAction()
    {
        // do we have mailing lists defined?
        if( !isset( $this->_options['mailinglists'] ) || !count( $this->_options['mailinglists'] ) )
            die( "ERR: No valid mailing lists defined in your application.ini\n" );

        $this->view->apppath = APPLICATION_PATH;
        $this->view->date = date( 'Y-m-d H:i:s' );

        echo $this->view->render( 'cli/mailing-list-sync-script.sh' );
    }


    private function _getMailingList()
    {
        // do we have mailing lists defined?
        if( !isset( $this->_options['mailinglist']['enabled'] ) || !$this->_options['mailinglist']['enabled'] )
            die( "ERR: Mailing lists disabled in configuration( use: mailinglist.enabled = 1 to enable)\n" );

        if( !( $list = $this->getFrontController()->getParam( 'param1', false ) ) )
            die( "ERR: You must specify a list name (e.g. --p1 listname)\n" );

        // do we have mailing lists defined?
        if( !isset( $this->_options['mailinglists'] ) || !count( $this->_options['mailinglists'] ) )
            die( "ERR: No valid mailing lists defined in your application.ini\n" );

        // is it a valid list?
        if( !isset( $this->_options['mailinglists'][$list] ) )
            die( "ERR: The specifed list ({$list}) is not defined in your application.ini\n" );

        return $list;
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



    /**
     * With the introduction of LAG graphs in 3.6.14, we wanted to merge past
     * traffic data form individual ports into the new lag files
     *
     * This CLI action just lists the files to merge to and from.
     *
     * Use a merger such as: http://bangbangsoundslikemachinery.blogspot.ie/2012/02/mrtg-log-aggregator.html
     * And set $MERGER and $MRTGPATH accordingly before running the resultant commands.
     */
    public function cliLagHistoryToFromAction()
    {
        // get all active trafficing customers
        $custs = $this->getD2R( '\\Entities\\Customer' )->getCurrentActive( false, true, false, $this->getD2R( '\\Entities\\IXP' )->getDefault() );

        foreach( $custs as $c )
        {
            foreach( $c->getVirtualInterfaces() as $vi )
            {
                if( count( $vi->getPhysicalInterfaces() ) <= 1 )
                    continue;

                foreach( IXP_Mrtg::$CATEGORIES as $category )
                {

                    echo '$MERGER';
                    foreach( $vi->getPhysicalInterfaces() as $pi )
                    {
                        echo ' $MRTGPATH/' . IXP_Mrtg::getMrtgFilePath( 'members', 'LOG', $pi->getMonitorIndex(), $category, $c->getShortname() );
                    }

                    echo ' >';
                    echo ' $MRTGPATH/' . IXP_Mrtg::getMrtgFilePath( 'members', 'LOG', 'lag-viid-' . $vi->getId(), $category, $c->getShortname() );
                    echo "\n";
                }
            }
        }
    }
}
