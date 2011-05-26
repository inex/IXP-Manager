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


/*
 *
 *
 * http://www.inex.ie/
 * (c) Internet Neutral Exchange Association Ltd
 */

/**
 * The CLI controller.
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
        Doctrine_Query::create()
            ->delete( 'TrafficDaily' )
            ->where( 'day = ?', $day )
            ->execute();

        $custs = Doctrine_Query::create()
            ->select( 'c.shortname' )
            ->addSelect( 'c.name' )
            ->from( 'Cust c' )
            ->whereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->andWhere( 'c.status = ?', array( Cust::STATUS_NORMAL ) )
            ->andWhere( 'c.dateleave = 0 or c.dateleave IS NULL' )
            ->andWhereIn( 'c.shortname', array( 'inex', 'routeservers' ), true )
            ->fetchArray();

        foreach( $custs as $cust )
        {
            $stats = array();

            foreach( INEX_Mrtg::$CATEGORIES as $category )
            {
	            $mrtg = new INEX_Mrtg(
	                            INEX_Mrtg::getMrtgFilePath( $this->config['mrtg']['path'],
	                                'LOG', 'aggregate', $category,
	                                $cust['shortname']
	                            )
	            );

                $td = new TrafficDaily();
                $td['day']      = $day;
                $td['category'] = $category;
                $td['cust_id']  = $cust['id'];

                foreach( INEX_Mrtg::$PERIODS as $period )
                {
                    $stats = $mrtg->getValues( $period, $category, false );

                    $td["{$period}_avg_in"]  = $stats['averagein'];
                    $td["{$period}_avg_out"] = $stats['averageout'];
                    $td["{$period}_max_in"]  = $stats['maxin'];
                    $td["{$period}_max_out"] = $stats['maxout'];
                    $td["{$period}_tot_in"]  = $stats['totalin'];
                    $td["{$period}_tot_out"] = $stats['totalout'];

                }

                $td->save();
            }
        }
    }

    public function updateTraffic95thPercentileDbAction()
    {
        $tstart = mktime(  0,  0,  0, date('m'), date('j')-1, date('Y') );
        $tend   = mktime( 23, 59, 59, date('m'), date('j')-1, date('Y') );

        if( $this->_verbose )
        {
            echo "\n\nupdateTraffic95thPercentileDbAction()\n\n";
            echo "Updating traffic statistics and 95th percentiles...\n\n";
            echo "START: $tstart " . date( 'Y-m-d H:i:s', $tstart ) . "\n";
            echo "END:   $tend "   . date( 'Y-m-d H:i:s', $tend   ) . "\n";
            echo "Deleting pre-existing records for the period of they exist...\n\n";
        }

        // This should only be done once a day and if values already exist for 'today',
        // just delete them.
        Doctrine_Query::create()
            ->delete( 'Traffic95th' )
            ->where( 'datetime >= ?', date( 'Y-m-d H:i:s', $tstart ) )
            ->andWhere( 'datetime <= ?', date( 'Y-m-d H:i:s', $tend ) )
            ->execute();

        $custs = Doctrine_Query::create()
            ->select( 'c.shortname' )
            ->addSelect( 'c.name' )
            ->from( 'Cust c' )
            ->whereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->andWhere( 'c.status = ?', array( Cust::STATUS_NORMAL ) )
            ->andWhere( 'c.dateleave = 0 or c.dateleave IS NULL' )
            ->andWhereIn( 'c.shortname', array( 'inex', 'routeservers' ), true )
            ->fetchArray();


        $percentiles = array();
        foreach( $custs as $cust )
        {
            if( $this->_verbose )
            {
                echo "\n${cust['name']} (${cust['shortname']} - #${cust['id']})\n";
                echo "\tGathering statistics from MRTG files...\n";
            }

            $stats = array();

            $mrtg = new INEX_Mrtg(
                            INEX_Mrtg::getMrtgFilePath( $this->config['mrtg']['path'],
                                'LOG', 'aggregate', 'bits',
                                $cust['shortname']
                            )
            );

            $dataPoints = $mrtg->getArray();

            unset( $mrtg );

            $qualifyingDataPointCount = 0;

            foreach( $dataPoints as $dp )
            {
                if( $dp[0] >= $tstart && $dp[0] <= $tend )
                {
                    $t95th = new Traffic95th();
                    $t95th['cust_id']  = $cust['id'];
                    $t95th['datetime'] = date( 'Y-m-d H:i:s', $dp[0] );

                    list( $intTime, $avgratein, $avgrateout, $peakratein, $peakrateout ) = $dp;

                    if( $avgratein > $avgrateout )
                        $t95th['average'] = $avgratein;
                    else
                        $t95th['average'] = $avgrateout;

                    if( $peakratein > $peakrateout )
                        $t95th['max'] = $peakratein;
                    else
                        $t95th['max'] = $peakrateout;

                    $t95th->save();
                    $t95th->free( true ); // memory management

                    ++$qualifyingDataPointCount;
                }
            }

            unset( $dataPoints );

            if( $this->_verbose )
                echo "\tInserted " . $qualifyingDataPointCount . " qualifying data points into database...\n";


            // calculate the 95th for the current month or the overall for the last month
            if( date( 'j') == '1' )
            {
	            if( $this->_verbose )
	                echo "\tCalculating 95th percentile for last month...\n";

                $tend2   = date( 'Y-m-d H:i:s', mktime( 23, 59, 59, date( 'm' ), date('j')-1, date( 'Y' ) ) );
                $tstart2 = mktime( 0,  0,  0, date( 'm', $tend2 ), 1, date( 'Y', $tend2 ) );
                $month2  = date( 'Y-m-d', $tstart2 );
                $tstart2 = date( 'Y-m-d H:i:s', $tstart2 );
            }
            else
            {
                if( $this->_verbose )
                    echo "\tCalculating 95th percentile for this month to date...\n";

                $tend2   = date( 'Y-m-d H:i:s', mktime( 23, 59, 59, date( 'm' ), date('j')-1, date( 'Y' ) ) );
                $tstart2 = mktime( 0,  0,  0,  date( 'm' ), 1, date( 'Y' ) );
                $month2  = date( 'Y-m-d', $tstart2 );
                $tstart2 = date( 'Y-m-d H:i:s', $tstart2 );
            }

            if( $percentiles[$cust['shortname']] = Traffic95thTable::get95thPercentile( $cust['id'], $tstart2, $tend2 ) )
            {
	            if( $this->_verbose )
	                echo "\tFound 95th percentile: ${percentiles[$cust['shortname']]}\n";

                if( count( $entry = Doctrine_Core::getTable( 'Traffic95thMonthly' )
                        ->findByDql( 'cust_id = ? AND month = ?', array( $cust['id'], $month2 ) ) ) )
                {
                    if( $this->_verbose )
                        echo "\tUpdating value for this month to 95th traffic table...\n";

                    // there should only be one of these (unique index on cust_id and month)!
                    $entry[0]['max_95th'] = $percentiles[$cust['shortname']] * 8;
                    $entry[0]->save();
                    $entry->free();
                }
                else
                {
                    if( $this->_verbose )
	                echo "\tAdding first value for this month to 95th traffic table...\n";

                    $entry = new Traffic95thMonthly();
                    $entry['cust_id']  = $cust['id'];
                    $entry['month']    = $month2;
                    $entry['max_95th'] = $percentiles[$cust['shortname']] * 8;
                    $entry->save();
                    $entry->free();
                }

            }

        }

    }




    /**
     * This function looks for members who have changed their traffic patterns significantly
     * when comparing 'yesterday' to the last month.
     */
    public function examineTrafficDeltasAction()
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
        $mail->setFrom( $this->config['cli']['traffic_differentials']['from_email'], $this->config['cli']['traffic_differentials']['from_name'] )
             ->setSubject( $this->config['cli']['traffic_differentials']['subject'] )
             ->setType( Zend_Mime::MULTIPART_RELATED );

        foreach( $this->config['cli']['traffic_differentials']['recipients'] as $r )
            $mail->addTo( $r );

        $mailHtml = $this->view->render( 'customer/mail/diff-header.tpl' );

        $numWithExceededThreshold = 0;

        foreach( $custs as $c )
        {
            $tds = Doctrine_Query::create()
	               ->from( 'TrafficDaily td' )
	               ->where( 'td.category = ?', 'bits' )
	               ->andWhere( 'td.cust_id = ?', $c['id'] )
	               ->orderBy( 'td.day DESC' )
	               ->limit( $this->config['cli']['traffic_differentials']['stddev_calc_length'] + 1 )
	               ->fetchArray();

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
	            echo $c['name'] . "\n";
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
                $this->view->days          = $this->config['cli']['traffic_differentials']['stddev_calc_length'];

                $mrtg = $mail->createAttachment(
                    @file_get_contents(
                        INEX_Mrtg::getMrtgFilePath(
                            $this->config['mrtg']['path'],
                            'PNG',
                            'aggregate',
                            'bits',
                            $c['shortname'],
                            'month'
                        )
                    ),
                    "image/png",
                    Zend_Mime::DISPOSITION_INLINE,
                    Zend_Mime::ENCODING_BASE64,
                    $c['shortname'] . ".png"
                );
                $mrtg->id = $c['shortname'];

                $mailHtml .= $this->view->render( 'customer/mail/diff-member.tpl' );

                $numWithExceededThreshold++;
            }

        }

        $this->view->numWithExceededThreshold = $numWithExceededThreshold;

        $mailHtml .= $this->view->render( 'customer/mail/diff-footer.tpl' );

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

}


