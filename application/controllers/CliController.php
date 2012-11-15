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
 * Controller: CLI actions - needs review
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CliController extends INEX_Controller_Action
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



    public function uploadTrafficStatsToDbAction()
    {
        // This should only be done once a day and if values already exist for 'today',
        // just delete them.
        $day = date( 'Y-m-d' );
        $this->getD2EM()->getRepository( '\\Entities\\TrafficDaily' )->deleteForDay( $day );

        $custs = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( false, true, true );

        foreach( $custs as $cust )
        {
            $stats = array();

            foreach( INEX_Mrtg::$CATEGORIES as $category )
            {
	            $mrtg = new INEX_Mrtg(
                    INEX_Mrtg::getMrtgFilePath( $this->_options['mrtg']['path'] . '/members',
                        'LOG', 'aggregate', $category,
                        $cust->getShortname()
                    )
	            );

                $td = new \Entities\TrafficDaily();
                $td->setDay( new DateTime( $day ) );
                $td->setCategory( $category );
                $td->setCustomer( $cust );

                foreach( INEX_Mrtg::$PERIODS as $name => $period )
                {
                    $stats = $mrtg->getValues( $period, $category, false );

                    $fn = "set{$name}AvgIn";  $td->$fn( $stats['averagein']  );
                    $fn = "set{$name}AvgOut"; $td->$fn( $stats['averageout'] );
                    $fn = "set{$name}MaxIn";  $td->$fn( $stats['maxin']      );
                    $fn = "set{$name}MaxOut"; $td->$fn( $stats['maxout']     );
                    $fn = "set{$name}TotIn";  $td->$fn( $stats['totalin']    );
                    $fn = "set{$name}TotOut"; $td->$fn( $stats['totalout']   );
                }

                $this->getD2EM()->persist( $td );
            }
            $this->getD2EM()->flush();
        }
    }



    /**
     * This function looks for members who have changed their traffic patterns significantly
     * when comparing 'yesterday' to the last month.
     */
    public function examineTrafficDeltasAction()
    {
        $custs = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( false, true, true );
        
        $mail = $this->getMailer();
        $mail->setFrom( $this->_options['cli']['traffic_differentials']['from_email'], $this->_options['cli']['traffic_differentials']['from_name'] )
             ->setSubject( $this->_options['cli']['traffic_differentials']['subject'] )
             ->setType( Zend_Mime::MULTIPART_RELATED );

        foreach( $this->_options['cli']['traffic_differentials']['recipients'] as $r )
            $mail->addTo( $r );

        $mailHtml = $this->view->render( 'customer/email/diff-header.phtml' );

        $numWithExceededThreshold = 0;

        foreach( $custs as $c )
        {
            $tds = $this->getD2EM()->getRepository( '\\Entities\\TrafficDaily' )
                ->getAsArray( $c, $this->_options['cli']['traffic_differentials']['stddev_calc_length'] + 1, INEX_Mrtg::CATEGORY_BITS );

    	    $firstDone = false;
            $meanIn  = 0.0; $stddevIn  = 0.0;
            $meanOut = 0.0; $stddevOut = 0.0;
            $count = 0.0;

            foreach( $tds as $t )
            {
    	        if( !$firstDone )
	            {
	                $todayAvgIn  = $t['day_avg_in'];
	                $todayAvgOut = $t['day_avg_out'];
	                $firstDone = true;
	                continue;
	            }

    	        $count     += 1.0;
                $meanIn    += $t['day_avg_in'];
                $meanOut   += $t['day_avg_out'];
            }

            if( $count > 1 )
            {
                $meanIn  /= $count;
                $meanOut /= $count;

                foreach( $tds as $t )
                {
                    $stddevIn  += ( $t['day_avg_in']  - $meanIn  ) * ( $t['day_avg_in']  - $meanIn  );
                    $stddevOut += ( $t['day_avg_out'] - $meanOut ) * ( $t['day_avg_out'] - $meanOut );
                }

                $stddevIn  = sqrt( $stddevIn  / ( $count - 1 ) );
                $stddevOut = sqrt( $stddevOut / ( $count - 1 ) );
            }
            
            // so, is yesterday's traffic outside of the standard deviation? And is it an increase or decrease?
            $sIn  = ( $todayAvgIn  - $meanIn   ) > 0 ? 'increase' : 'decrease';
            $sOut = ( $todayAvgOut - $meanOut  ) > 0 ? 'increase' : 'decrease';
            $dIn  = abs( $todayAvgIn  - $meanIn  );
            $dOut = abs( $todayAvgOut - $meanOut );

            $thresholdIn  = 1.5*$stddevIn;
            $thresholdOut = 1.5*$stddevOut;

            if( $this->_verbose )
            {
	            echo $c->getName() . "\n";
	            printf( "\tIN  M: %d\tSD: %d\tDiff: %d\tT: %d\tR: %s\n",
	                intval( $meanIn ), intval( $stddevIn ), intval( $dIn ), $thresholdIn, ( $dIn > $thresholdIn ? 'OUT' : 'IN' )
	            );
	            printf( "\tOUT M: %d\tSD: %d\tDiff: %d\tT: %d\tR: %s\n\n",
	                intval( $meanOut ), intval( $stddevOut ), intval( $dOut ), $thresholdOut, ( $dOut > $thresholdOut ? 'OUT' : 'IN' )
	            );
            }

            if( $dIn > $thresholdIn || $dOut > $thresholdOut )
            {
                $this->view->cust          = $c;
                $this->view->in            = $todayAvgIn;
                $this->view->out           = $todayAvgOut;
                $this->view->stddevIn      = $stddevIn;
                $this->view->stddevOut     = $stddevOut;
                $this->view->meanIn        = $meanIn;
                $this->view->meanOut       = $meanOut;
                $this->view->dIn           = $dIn;
                $this->view->dOut          = $dOut;
                $this->view->sIn           = $sIn;
                $this->view->sOut          = $sOut;
                $this->view->threasholdIn  = $thresholdIn;
                $this->view->threasholdOut = $thresholdOut;
                $this->view->percentIn     = $meanIn  ? intval( ( $dIn  / $meanIn  ) * 100 ) : 'NONE';
                $this->view->percentOut    = $meanOut ? intval( ( $dOut / $meanOut ) * 100 ) : 'NONE';
                $this->view->days          = $this->_options['cli']['traffic_differentials']['stddev_calc_length'];

                $mrtg = $mail->createAttachment(
                    @file_get_contents(
                        INEX_Mrtg::getMrtgFilePath(
                            $this->_options['mrtg']['path'] . '/members',
                            'PNG',
                            'aggregate',
                            'bits',
                            $c->getShortname(),
                            'month'
                        )
                    ),
                    "image/png",
                    Zend_Mime::DISPOSITION_INLINE,
                    Zend_Mime::ENCODING_BASE64,
                    $c->getShortname() . ".png"
                );
                $mrtg->id = $c->getShortname();

                $mailHtml .= $this->view->render( 'customer/email/diff-member.phtml' );

                $numWithExceededThreshold++;
            }

        }

        $this->view->numWithExceededThreshold = $numWithExceededThreshold;

        $mailHtml .= $this->view->render( 'customer/email/diff-footer.phtml' );

        $mail->setBodyHtml( $mailHtml  );
        $mail->send();
    }

    /**
     * This function looks for members who are reaching or exceeding 80% port utilisation
     */
    public function examinePortUtilisationAction()
    {

        $custs = Doctrine_Query::create()
            ->select( 'c.shortname' )
            ->addSelect( 'c.name' )
            ->from( 'Cust c' )
            ->whereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->andWhere( 'c.status = ?', array( Cust::STATUS_NORMAL ) )
            ->andWhere( 'c.dateleave = 0 or c.dateleave IS NULL' )
            ->andWhereIn( 'c.shortname', array( 'inex', 'routeservers' ), true )
            ->fetchArray();

        $mail = new Zend_Mail();
        $mail->setFrom( $this->config['cli']['port_utilisation']['from_email'], $this->config['cli']['traffic_differentials']['from_name'] )
             ->setSubject( $this->config['cli']['port_utilisation']['subject'] )
             ->setType( Zend_Mime::MULTIPART_RELATED );

        foreach( $this->config['cli']['port_utilisation']['recipients'] as $r )
            $mail->addTo( $r );

        $this->view->threshold = $this->config['cli']['port_utilisation']['threshold'];
        $mailHtml = $this->view->render( 'customer/mail/util-header.tpl' );

        $numIntsWithExcessUtil = 0;

        foreach( $custs as $c )
        {
            $interfaces = Doctrine_Query::create()
                ->from( 'Virtualinterface vi' )
                ->leftJoin( 'vi.Physicalinterface pi' )
                ->leftJoin( 'pi.Switchport sp' )
                ->leftJoin( 'sp.SwitchTable s' )
                ->where( 'vi.custid = ?', $c['id'] )
                ->orderBy( 'pi.monitorindex' )
                ->fetchArray();


            foreach( $interfaces as $i )
            {
                foreach( $i['Physicalinterface'] as $pi )
                {
                    $speed = $pi['speed'] * 1024 * 1024;

                    $mrtg = new INEX_Mrtg(
                        INEX_Mrtg::getMrtgFilePath( $this->config['mrtg']['path'],
                            'LOG', $pi['monitorindex'], INEX_Mrtg::CATEGORY_BITS,
                            $c['shortname']
                        )
                    );

                    $stats = $mrtg->getValues( INEX_Mrtg::PERIOD_WEEK, INEX_Mrtg::CATEGORY_BITS, false );

                    $maxIn  = $stats['maxin'] * 8.0;
                    $maxOut = $stats['maxout'] * 8.0;

                    $switch_port = $pi['Switchport']['SwitchTable']['name'] . ' :: ' . $pi['Switchport']['name'];

                    $utilIn  = $maxIn  / $speed;
                    $utilOut = $maxOut / $speed;

	                if( $utilIn > $this->config['cli']['port_utilisation']['threshold'] || $utilOut > $this->config['cli']['port_utilisation']['threshold'] )
	                {
	                    $this->view->cust       = $c;
	                    $this->view->utilIn     = $utilIn;
	                    $this->view->utilOut    = $utilOut;
	                    $this->view->switchport = $switch_port;

		                $mrtg = $mail->createAttachment(
		                    file_get_contents(
		                       INEX_Mrtg::getMrtgFilePath(
		                            $this->config['mrtg']['path'],
		                            'PNG',
		                            $pi['monitorindex'],
		                            INEX_Mrtg::CATEGORY_BITS,
		                            $c['shortname'],
		                            INEX_Mrtg::PERIOD_WEEK
		                        )
		                    ),
		                    "image/png",
		                    Zend_Mime::DISPOSITION_INLINE,
		                    Zend_Mime::ENCODING_BASE64,
		                    $c['shortname'] . ".png"
		                );

	                    $mrtg->id = $c['shortname'];

	                    $mailHtml .= $this->view->render( 'customer/mail/util-member.tpl' );

	                    $numIntsWithExcessUtil++;
	                }
                }
            }
        }

        $this->view->numWithExcessUtil = $numIntsWithExcessUtil;

        $mailHtml .= $this->view->render( 'customer/mail/util-footer.tpl' );

        $mail->setBodyHtml( $mailHtml  );
        $mail->send();
    }


    /**
     * Generates a Nagios configuration for supported switches in the database
     */
    public function generateNagiosConfigAction()
    {

        $switches = Doctrine_Query::create()
            ->from( 'SwitchTable s' )
            ->leftJoin( 's.Vendor v' )
            ->leftJoin( 's.Cabinet c' )
            ->leftJoin( 'c.Location l' )
            ->where( 's.active = 1' )
            ->fetchArray();

        #print_r( $switches );
        #exit;

        echo $this->view->render( 'cli/nagios/switch-definitions.tpl' );

        $all     = array();
        $brocade = array();
        $cisco   = array();
        $mrv     = array();

        $locations = array();

        foreach( $switches as $s )
        {
            $this->view->sw = $s;
            echo $this->view->render( 'cli/nagios/switch-hosts.tpl' );

            switch( $s['Vendor']['name'] )
            {
                case 'Foundry Networks':
                    $brocade[] = $s['name'];
                    break;

                case 'Cisco Systems':
                    $cisco[] = $s['name'];
                    break;

                case 'MRV':
                    $mrv[] = $s['name'];
                    break;
            }

            $all[] = $s['name'];

            $locations[$s['Cabinet']['Location']['shortname']][] = $s['name'];
        }

        $this->view->all = $all;

        $this->view->locations = $locations;

        $this->view->vendor_brocade = $brocade;
        $this->view->vendor_cisco   = $cisco;
        $this->view->vendor_mrv     = $mrv;

        echo $this->view->render( 'cli/nagios/switch-templates.tpl' );
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
        $users = Doctrine_Query::create()->from( 'User u' )->execute( null, Doctrine::HYDRATE_RECORD );

        foreach( $users as $u )
        {
            if( $u->hasPreference( "mailinglist.{$list}.subscribed" ) )
                continue;

            if( in_array( $u['email'], $addresses ) )
                $u->setPreference( "mailinglist.{$list}.subscribed", 1 );
            else
                $u->setPreference( "mailinglist.{$list}.subscribed", 0 );
        }
    }

    /**
     * Mailing list subscribed action - list all addresses subscribed to the given list
     */
    public function mailingListSubscribedAction()
    {
        $list = $this->_getMailingList();
    
        $users = Doctrine_Query::create()
            ->select( 'u.email' )
            ->from( 'User u' )
            ->leftJoin( 'u.UserPref up' )
            ->where( 'up.attribute = ?', "mailinglist.{$list}.subscribed" )
            ->andWhere( 'up.value = 1')
            ->execute( null, Doctrine::HYDRATE_SINGLE_SCALAR );
        
        foreach( $users as $user )
            echo "$user\n";
    }
    
    /**
     * Mailing list unsubscribed action - list all addresses not subscribed to the given list
     */
    public function mailingListUnsubscribedAction()
    {
        $list = $this->_getMailingList();
    
        $users = Doctrine_Query::create()
        ->select( 'u.email' )
        ->from( 'User u' )
        ->leftJoin( 'u.UserPref up' )
        ->where( 'up.attribute = ?', "mailinglist.{$list}.subscribed" )
        ->andWhere( 'up.value = 0')
        ->execute( null, Doctrine::HYDRATE_SINGLE_SCALAR );
    
        foreach( $users as $user )
            echo "$user\n";
    }
    
    /**
     * Mailing list password sync - create and execute commands to set mailing list p/w of subscribers
     */
    public function mailingListPasswordSyncAction()
    {
        $list = $this->_getMailingList();

        // we'll sync by default so only if we're told not to will the following be true:
        if( isset( $this->config['mailinglists'][$list]['syncpws'] ) && !$this->config['mailinglists'][$list]['syncpws'] )
        {
            if( $this->_verbose ) echo "{$list}: Password sync for the given mailing list is disabled";
        }
        else
        {
            $users = Doctrine_Query::create()
                ->select( 'u.email, u.password' )
                ->from( 'User u' )
                ->leftJoin( 'u.UserPref up' )
                ->where( 'up.attribute = ?', "mailinglist.{$list}.subscribed" )
                ->andWhere( 'up.value = 1')
                ->execute( null, Doctrine::HYDRATE_ARRAY );
            
            foreach( $users as $user )
            {
                $cmd = sprintf( "{$this->config['mailinglist']['cmd']['changepw']} %s %s %s",
                        escapeshellarg( $list ), escapeshellarg( $user['email'] ), escapeshellarg( $user['password'] )
                );
                
                if( $this->_verbose ) echo "$cmd\n";
                exec( $cmd );
            }
        }
    }
    
    /**
     * Mailing list syncronisation - generates a shell script for all mailing lists
     */
    public function mailingListSyncScriptAction()
    {
        // do we have mailing lists defined?
        if( !isset( $this->config['mailinglists'] ) || !count( $this->config['mailinglists'] ) )
            die( "ERR: No valid mailing lists defined in your application.ini\n" );
        
        $apppath = APPLICATION_PATH;
        $date = date( 'Y-m-d H:i:s' );
        
        echo <<<END_BLOCK
#! /bin/sh

#
# Script for syncronising subscriptions between mailing lists and IXP Manager.
#
# Does not affect any subscriptions with email addresses that do not match a user
# in IXP Manager.
#
# Generated: {$date}
#


END_BLOCK;
        
        
        foreach( $this->config['mailinglists'] as $name => $ml )
        {
            echo <<<END_BLOCK
#######################################################################################################################################
##
## {$name} - {$ml['name']}
##

# Set default subsciption settings for any new IXP Manager users
{$this->config['mailinglist']['cmd']['list_members']} {$name} | {$apppath}/../bin/ixptool.php -a cli.mailing-list-init --p1={$name}

# Add new subscriptions to the list
{$apppath}/../bin/ixptool.php -a cli.mailing-list-subscribed --p1={$name} | {$this->config['mailinglist']['cmd']['add_members']} {$name} >/dev/null

# Remove subscriptions from the list
{$apppath}/../bin/ixptool.php -a cli.mailing-list-unsubscribed --p1={$name} | {$this->config['mailinglist']['cmd']['remove_members']} {$name} >/dev/null

# Sync passwords
{$apppath}/../bin/ixptool.php -a cli.mailing-list-password-sync --p1={$name} >/dev/null

END_BLOCK;
        }
    }
    
    
    private function _getMailingList()
    {
        // do we have mailing lists defined?
        if( !isset( $this->config['mailinglist']['enabled'] ) || !$this->config['mailinglist']['enabled'] )
            die( "ERR: Mailing lists disabled in configuration( use: mailinglist.enabled = 1 to enabled)\n" );
        
        if( !( $list = $this->getFrontController()->getParam( 'param1', false ) ) )
            die( "ERR: You must specify a list name (e.g. --p1 listname)\n" );
        
        // do we have mailing lists defined?
        if( !isset( $this->config['mailinglists'] ) || !count( $this->config['mailinglists'] ) )
            die( "ERR: No valid mailing lists defined in your application.ini\n" );
        
        // is it a valid list?
        if( !isset( $this->config['mailinglists'][$list] ) )
            die( "ERR: The specifed list ({$list}) is not defined in your application.ini\n" );
        
        return $list;
    }
}


