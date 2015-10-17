<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Controller: Statistics CLI Actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StatisticsCliController extends IXP_Controller_CliAction
{

    /**
     * This function looks for members who are reaching or exceeding 80% port utilisation
     */
    public function emailPortUtilisationAction()
    {
        $custs = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( false, true, false );

        $mail = $this->getMailer();
        $mail->setFrom( $this->_options['cli']['port_utilisation']['from_email'], $this->_options['cli']['traffic_differentials']['from_name'] )
            ->setSubject( $this->_options['cli']['port_utilisation']['subject'] )
            ->setType( Zend_Mime::MULTIPART_RELATED );

        foreach( $this->_options['cli']['port_utilisation']['recipients'] as $r )
            $mail->addTo( $r );

        $this->view->threshold = $this->_options['cli']['port_utilisation']['threshold'];
        $mailHtml = $this->view->render( 'statistics-cli/email/util-header.phtml' );

        $numIntsWithExcessUtil = 0;

        foreach( $custs as $c )
        {
            foreach( $c->getVirtualInterfaces() as $vi )
            {
                foreach( $vi->getPhysicalInterfaces() as $pi )
                {
                    if( $pi->getStatus() != \Entities\PhysicalInterface::STATUS_CONNECTED )
                        continue;

                    $speed = $pi->getSpeed() * 1024 * 1024;

                    $mrtg = new IXP_Mrtg(
                            IXP_Mrtg::getMrtgFilePath(
                                    $pi->getSwitchport()->getSwitcher()->getInfrastructure()->getIXP()->getMrtgPath() . '/members',
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

                    if( $this->isVerbose() || $this->isDebug() )
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
                                    $pi->getSwitchport()->getSwitcher()->getInfrastructure()->getIXP()->getMrtgPath() . '/members',
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
                            "{$c->getShortname()}-{$pi->getMonitorindex()}.png"
                        );

                        $this->view->mrtg_id = $mrtg->id = "{$c->getShortname()}-{$pi->getMonitorindex()}";

                        $mailHtml .= $this->view->render( 'statistics-cli/email/util-member.phtml' );

                        $numIntsWithExcessUtil++;
                    }
                }
            }
        }

        $this->view->numWithExcessUtil = $numIntsWithExcessUtil;

        $mailHtml .= $this->view->render( 'statistics-cli/email/util-footer.phtml' );

        $mail->setBodyHtml( $mailHtml  );
        $mail->send();
    }


    /**
     * This function looks for members who have changed their traffic patterns significantly
     * when comparing 'yesterday' to the last month.
     */
    public function emailTrafficDeltasAction()
    {
        $custs = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( false, true, true );

        $mail = $this->getMailer();
        $mail->setFrom( $this->_options['cli']['traffic_differentials']['from_email'], $this->_options['cli']['traffic_differentials']['from_name'] )
            ->setSubject( $this->_options['cli']['traffic_differentials']['subject'] )
            ->setType( Zend_Mime::MULTIPART_RELATED );

        foreach( $this->_options['cli']['traffic_differentials']['recipients'] as $r )
            $mail->addTo( $r );

        $mailHtml = $this->view->render( 'statistics-cli/email/diff-header.phtml' );

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

            if( $this->isVerbose() || $this->isDebug() )
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
                            // FIXME plastering over multiIXP here for now
                            $this->getD2R( '\\Entities\\IXP' )->getDefault()->getMrtgPath() . '/members',
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

                $mailHtml .= $this->view->render( 'statistics-cli/email/diff-member.phtml' );

                $numWithExceededThreshold++;
            }

        }

        $this->view->numWithExceededThreshold = $numWithExceededThreshold;

        $mailHtml .= $this->view->render( 'statistics-cli/email/diff-footer.phtml' );

        $mail->setBodyHtml( $mailHtml  );
        $mail->send();
    }


    public function uploadTrafficStatsToDbAction()
    {
        // do this for all IXPs
        $ixps = $this->getD2R( '\\Entities\\IXP' )->findAll();

        foreach( $ixps as $ixp )
        {
            $this->verbose( "\nProcessing IXP " . $ixp->getName(), false );

            // This should only be done once a day and if values already exist for 'today',
            // just delete them.
            $day = date( 'Y-m-d' );
            $this->getD2EM()->getRepository( '\\Entities\\TrafficDaily' )->deleteForDay( $day, $ixp );

            $custs = $this->getD2R( '\\Entities\\Customer')->getConnected( false, true, $ixp );

            foreach( $custs as $cust )
            {
                $this->verbose( "\n\t- processing customer " . $cust->getName() . "\t ", false );
                $stats = array();

                foreach( IXP_Mrtg::$CATEGORIES as $category )
                {
                    $this->verbose( "({$category}) ", false );

                    $mrtg = new IXP_Mrtg(
                        IXP_Mrtg::getMrtgFilePath(
                            $ixp->getMrtgPath() . '/members',
                            'LOG', 'aggregate', $category,
                            $cust->getShortname()
                        )
                    );

                    $td = new \Entities\TrafficDaily();
                    $td->setDay( new DateTime( $day ) );
                    $td->setCategory( $category );
                    $td->setCustomer( $cust );
                    $td->setIXP( $ixp );

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

        if( isset( $this->_options['cli']['traffic_daily']['delete_old'] ) && $this->_options['cli']['traffic_daily']['delete_old'] )
        {
            if( isset( $this->_options['cli']['traffic_differentials']['stddev_calc_length'] ) && $this->_options['cli']['traffic_differentials']['stddev_calc_length'] )
            {
                $this->verbose( "\nDeleting old daily traffic records that are no longer required" );

                $this->getD2EM()->getRepository( '\\Entities\\TrafficDaily' )->deleteBefore(
                    new DateTime( "-{$this->_options['cli']['traffic_differentials']['stddev_calc_length']} days" ), $ixp
                );
            }
        }

        $this->verbose("");
    }


    public function emailPortsWithErrorsAction()
    {
        $this->emailPortsWithCounts( 'Errors', IXP_Mrtg::CATEGORY_ERRORS, 'day_tot_in', 'day_tot_out' );
    }

    public function emailPortsWithDiscardsAction()
    {
        $this->emailPortsWithCounts( 'Discards', IXP_Mrtg::CATEGORY_DISCARDS, 'day_tot_in', 'day_tot_out' );
    }

    private function emailPortsWithCounts( $type, $category, $inField, $outField )
    {
        $this->view->day = $day = date( 'Y-m-d', strtotime( '-1 days' ) );
        $data = $this->getD2R( '\\Entities\\TrafficDaily' )->load( $day, $category );

        $mail = $this->getMailer();
        $mail->setFrom( $this->_options['cli']['ports_with_counts']['from_email'], $this->_options['cli']['ports_with_counts']['from_name'] )
            ->setSubject( sprintf( $this->_options['cli']['ports_with_counts']['subject'], $type ) )
            ->setType( Zend_Mime::MULTIPART_RELATED );

        foreach( $this->_options['cli']['ports_with_counts']['recipients'] as $r )
            $mail->addTo( $r );

        $this->view->type = $type;
        $mailHtml = $this->view->render( 'statistics-cli/email/counts-header.phtml' );

        $numWithCounts = 0;

        foreach( $data as $d )
        {
            if( $d[ $inField ] == 0 && $d[ $outField ] == 0 )
                continue;

            $numWithCounts++;

            if( $this->isVerbose() || $this->isDebug() )
                echo "{$d['Customer']['name']}\t\tIN / OUT: {$d[ $inField ]} / {$d[ $outField ]}\n";

            $mrtg = $mail->createAttachment(
                file_get_contents(
                    IXP_Mrtg::getMrtgFilePath(
                        // FIXME plastering over multiIXP here for now
                        $this->getD2R( '\\Entities\\IXP' )->getDefault()->getMrtgPath() . '/members',
                        'PNG',
                        'aggregate',
                        $category,
                        $d['Customer']['shortname'],
                        IXP_Mrtg::PERIOD_DAY
                    )
                ),
                "image/png",
                Zend_Mime::DISPOSITION_INLINE,
                Zend_Mime::ENCODING_BASE64,
                "{$d['Customer']['shortname']}-aggregate.png"
            );

            $this->view->mrtg_id = $mrtg->id = "{$d['Customer']['shortname']}-aggregate";
            $this->view->ecust = $d['Customer'];
            $this->view->in  = $d[ $inField  ];
            $this->view->out = $d[ $outField ];
            $mailHtml .= $this->view->render( 'statistics-cli/email/counts-member.phtml' );
        }

        if( $numWithCounts )
        {
            $this->view->numWithCounts = $numWithCounts;
            $mailHtml .= $this->view->render( 'statistics-cli/email/counts-footer.phtml' );
            $mail->setBodyHtml( $mailHtml  );
            $mail->send();
        }
    }

    public function genMrtgConfAction()
    {
        // what IXP are we running on here?
        if( $this->multiIXP() )
        {
            $ixpid = $this->getParam( 'ixp', false );

            if( !$ixpid || !( $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $ixpid ) ) )
                die( "ERROR: Invalid or no IXP specified.\n" );
        }
        else
            $ixp = $this->getD2R( '\\Entities\\IXP' )->getDefault();

        $this->view->ixp                   = $ixp;
        $this->view->TRAFFIC_TYPES         = IXP_Mrtg::$TRAFFIC_TYPES;
        $this->view->portsByInfrastructure = $this->genMrtgConf_getPeeringPortsByInfrastructure( $ixp );

        // get all active trafficing customers
        $this->view->custs = $this->getD2R( '\\Entities\\Customer' )->getCurrentActive( false, true, false, $ixp );

        // Smarty has variable scope which OSS' skinning does not yet support so we need to use the native {include}
        // As such, we need to resolve here for skinning for these templates:
        $this->view->tmplMemberPort          = $this->view->resolveTemplate( 'statistics-cli/mrtg/member-port.cfg' );
        $this->view->tmplMemberAggregatePort = $this->view->resolveTemplate( 'statistics-cli/mrtg/member-aggregate-port.cfg' );
        $this->view->tmplMemberLagPort       = $this->view->resolveTemplate( 'statistics-cli/mrtg/member-lag-port.cfg' );

        if( isset( $this->_options['mrtg']['conf']['dstfile'] ) )
        {
            if( !$this->writeConfig( $this->_options['mrtg']['conf']['dstfile'], $this->view->render( 'statistics-cli/mrtg/index.cfg' ) ) )
                fwrite( STDERR, "Error: could not save configuration data\n" );
        }
        else
            echo $this->view->render( 'statistics-cli/mrtg/index.cfg' );
    }

    /**
     * Utility function to slurp all peering ports from the database and arrange them in
     * arrays by infrastructure and switch.
     *
     * @param \Entities\IXP $ixp
     */
    private function genMrtgConf_getPeeringPortsByInfrastructure( $ixp )
    {
        $data = [];

        foreach( $ixp->getInfrastructures() as $infra )
        {
            if( !$infra->getAggregateGraphName() )
                continue;

            $data[ $infra->getId() ]['mrtgIds']              = [];
            $data[ $infra->getId() ]['name']                 = $infra->getName();
            $data[ $infra->getId() ]['aggregate_graph_name'] = $infra->getAggregateGraphName();
            $data[ $infra->getId() ]['maxbytes']             = 0;
            $data[ $infra->getId() ]['switches']             = '';

            foreach( $infra->getSwitchers() as $switch )
            {
                if( $switch->getSwitchtype() != \Entities\Switcher::TYPE_SWITCH || !$switch->getActive() )
                    continue;

                $data[ $infra->getId() ]['switches'][ $switch->getId() ]             = [];
                $data[ $infra->getId() ]['switches'][ $switch->getId() ]['name']     = $switch->getName();
                $data[ $infra->getId() ]['switches'][ $switch->getId() ]['maxbytes'] = 0;
                $data[ $infra->getId() ]['switches'][ $switch->getId() ]['mrtgIds']  = [];

                foreach( $switch->getPorts() as $port )
                {
                    if( $port->getIfName() )
                    {
                        $snmpId = $port->ifnameToSNMPIdentifier();
                        $data[ $infra->getId() ]['maxbytes'] += $port->getIfHighSpeed() * 1000000 / 8; // Mbps * bps / to bytes
                        $data[ $infra->getId() ]['switches'][ $switch->getId() ]['maxbytes'] += $port->getIfHighSpeed() * 1000000 / 8;

                        foreach( IXP_Mrtg::$TRAFFIC_TYPES as $type => $vars )
                        {
                            $id = "{$vars['in']}#{$snmpId}&{$vars['out']}#{$snmpId}:{$switch->getSnmppasswd()}@{$switch->getHostname()}:::::2";

                            if( $port->getType() == \Entities\SwitchPort::TYPE_PEERING )
                                $data[ $infra->getId() ]['mrtgIds'][$type][] = $id;

                            $data[ $infra->getId() ]['switches'][ $switch->getId() ]['mrtgIds'][$type][] = $id;
                        }
                    }
                }
            }
        }

        return $data;
    }

}
