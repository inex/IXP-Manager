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
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
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

            foreach( IXP_Mrtg::$CATEGORIES as $category )
            {
	            $mrtg = new IXP_Mrtg(
                    IXP_Mrtg::getMrtgFilePath( $this->_options['mrtg']['path'] . '/members',
                        'LOG', 'aggregate', $category,
                        $cust->getShortname()
                    )
	            );

                $td = new \Entities\TrafficDaily();
                $td->setDay( new DateTime( $day ) );
                $td->setCategory( $category );
                $td->setCustomer( $cust );

                foreach( IXP_Mrtg::$PERIODS as $name => $period )
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
                ->getAsArray( $c, $this->_options['cli']['traffic_differentials']['stddev_calc_length'] + 1, IXP_Mrtg::CATEGORY_BITS );

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
                        IXP_Mrtg::getMrtgFilePath(
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
        $custs = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( false, true, false );
        
        $mail = $this->getMailer();
        $mail->setFrom( $this->_options['cli']['port_utilisation']['from_email'], $this->_options['cli']['traffic_differentials']['from_name'] )
             ->setSubject( $this->_options['cli']['port_utilisation']['subject'] )
             ->setType( Zend_Mime::MULTIPART_RELATED );

        foreach( $this->_options['cli']['port_utilisation']['recipients'] as $r )
            $mail->addTo( $r );

        $this->view->threshold = $this->_options['cli']['port_utilisation']['threshold'];
        $mailHtml = $this->view->render( 'customer/email/util-header.phtml' );

        $numIntsWithExcessUtil = 0;

        foreach( $custs as $c )
        {
            foreach( $c->getVirtualInterfaces() as $vi )
            {
                foreach( $vi->getPhysicalInterfaces() as $pi )
                {
                    $speed = $pi->getSpeed() * 1024 * 1024;

                    $mrtg = new IXP_Mrtg(
                        IXP_Mrtg::getMrtgFilePath( $this->_options['mrtg']['path'] . '/members',
                            'LOG', $pi->getMonitorindex(), IXP_Mrtg::CATEGORY_BITS,
                            $c->getShortname()
                        )
                    );

                    $stats = $mrtg->getValues( IXP_Mrtg::PERIOD_WEEK, IXP_Mrtg::CATEGORY_BITS, false );

                    $maxIn  = $stats['maxin'] * 8.0;
                    $maxOut = $stats['maxout'] * 8.0;

                    $switch_port = $pi->getSwitchport()->getSwitcher()->getName() . ' :: ' . $pi->getSwitchport()->getName();

                    $utilIn  = $maxIn  / $speed;
                    $utilOut = $maxOut / $speed;

                    if( $this->_verbose )
                    {
                        echo $c->getName() . "\n";
                        printf( "\tIN %0.2f%%\tOUT: %0.2f%%\n", $utilIn * 100.0, $utilOut * 100.0 );
                    }
                    
	                if( $utilIn > $this->_options['cli']['port_utilisation']['threshold'] || $utilOut > $this->_options['cli']['port_utilisation']['threshold'] )
	                {
	                    $this->view->cust       = $c;
	                    $this->view->utilIn     = $utilIn;
	                    $this->view->utilOut    = $utilOut;
	                    $this->view->switchport = $switch_port;

		                $mrtg = $mail->createAttachment(
		                    file_get_contents(
		                       IXP_Mrtg::getMrtgFilePath(
		                            $this->_options['mrtg']['path'] . '/members',
		                            'PNG',
		                            $pi->getMonitorindex(),
		                            IXP_Mrtg::CATEGORY_BITS,
		                            $c->getShortname(),
		                            IXP_Mrtg::PERIOD_WEEK
		                        )
		                    ),
		                    "image/png",
		                    Zend_Mime::DISPOSITION_INLINE,
		                    Zend_Mime::ENCODING_BASE64,
		                    $c->getShortname() . ".png"
		                );

	                    $mrtg->id = $c->getShortname();

	                    $mailHtml .= $this->view->render( 'customer/email/util-member.phtml' );

	                    $numIntsWithExcessUtil++;
	                }
                }
            }
        }

        $this->view->numWithExcessUtil = $numIntsWithExcessUtil;

        $mailHtml .= $this->view->render( 'customer/email/util-footer.phtml' );

        $mail->setBodyHtml( $mailHtml  );
        $mail->send();
    }


    /**
     * Generates a Nagios configuration for supported switches in the database
     */
    public function generateNagiosConfigAction()
    {
        $switches = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->getAndCache( true );
        
        echo $this->view->render( 'cli/nagios/switch-definitions.phtml' );

        $brocade = array();
        $cisco   = array();
        $mrv     = array();

        $all     = [];

        foreach( $switches as $s )
        {
            $this->view->sw = $s;
            echo $this->view->render( 'cli/nagios/switch-hosts.phtml' );

            switch( $s->getVendor()->getName() )
            {
                case 'Foundry Networks':
                    $brocade[] = $s->getName();
                    break;

                case 'Cisco Systems':
                    $cisco[] = $s->getName();
                    break;

                case 'MRV':
                    $mrv[] = $s->getName();
                    break;
            }

            $all[] = $s->getName();

            if( isset( $locations[ $s->getCabinet()->getLocation()->getShortname() ] ) )
                $locations[ $s->getCabinet()->getLocation()->getShortname() ] .= ", " . $s->getName();
            else
                $locations[ $s->getCabinet()->getLocation()->getShortname() ] = $s->getName();
        }

        $this->view->all = implode( ', ', $all );

        $this->view->locations = $locations;
        
        $this->view->vendor_brocade = implode( ', ', $brocade );
        $this->view->vendor_cisco   = implode( ', ', $cisco   );
        $this->view->vendor_mrv     = implode( ', ', $mrv     );

        echo $this->view->render( 'cli/nagios/switch-templates.phtml' );
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
            die( "ERR: Mailing lists disabled in configuration( use: mailinglist.enabled = 1 to enabled)\n" );
        
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
}


