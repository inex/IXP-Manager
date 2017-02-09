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

 use IXP\Services\Grapher\Graph;

/**
 * Controller: Statistics CLI Actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StatisticsCliController extends IXP_Controller_CliAction
{
    /**
     * This function looks for members who have changed their traffic patterns significantly
     * when comparing 'yesterday' to the last month.
     */
    public function emailTrafficDeltasAction()
    {
        $custs = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->getCurrentActive( false, true, true );
        $this->view->grapher = $grapher = App::make('IXP\Services\Grapher');

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
                ->getAsArray( $c, $this->_options['cli']['traffic_differentials']['stddev_calc_length'] + 1, Graph::CATEGORY_BITS );

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

                $graph = $grapher->customer( $c )->setCategory( Graph::CATEGORY_BITS )->setPeriod( Graph::PERIOD_MONTH );

                $mrtg = $mail->createAttachment(
                    $graph->png(),
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
        $this->view->grapher = $grapher = App::make('IXP\Services\Grapher');

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

                foreach( Graph::CATEGORIES as $category )
                {
                    $this->verbose( "({$category}) ", false );

                    $graph = $grapher->customer( $cust )->setCategory( $category );

                    $td = new \Entities\TrafficDaily();
                    $td->setDay( new DateTime( $day ) );
                    $td->setCategory( $category );
                    $td->setCustomer( $cust );
                    $td->setIXP( $ixp );

                    foreach( Graph::PERIOD_DESCS as $period => $name )
                    {
                        $stats = $graph->setPeriod($period)->statistics();

                        $fn = "set{$name}AvgIn";  $td->$fn( $stats->averageIn()  );
                        $fn = "set{$name}AvgOut"; $td->$fn( $stats->averageOut() );
                        $fn = "set{$name}MaxIn";  $td->$fn( $stats->maxIn()      );
                        $fn = "set{$name}MaxOut"; $td->$fn( $stats->maxOut()     );
                        $fn = "set{$name}TotIn";  $td->$fn( $stats->totalIn()    );
                        $fn = "set{$name}TotOut"; $td->$fn( $stats->totalOut()   );
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


}
