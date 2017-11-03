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
 * Controller: Statistics / graphs
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PublicStatisticsController extends IXP_Controller_Action
{
    use IXP_Controller_Trait_Statistics;

    public function init() {
        if( !config('ixp_fe.statistics.public', true ) ) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
    }


    public function publicAction()
    {
        // get the available graphs
        $ixps = $this->getD2R( '\\Entities\\IXP' )->findAll();
        $grapher = App::make('IXP\Services\Grapher');
        $category = $this->setCategory( 'category', true );

        $graphs = [];
        foreach( $ixps as $ixp )
        {
            $graphs[] = $grapher->ixp( $ixp )
                            ->setType(     Graph::TYPE_PNG )
                            ->setProtocol( Graph::PROTOCOL_ALL )
                            ->setCategory( $category );

            foreach( $ixp->getInfrastructures() as $inf )
            {
                $graphs[] = $grapher->infrastructure( $inf )
                                ->setType(     Graph::TYPE_PNG )
                                ->setProtocol( Graph::PROTOCOL_ALL )
                                ->setCategory( $category );
            }
        }

        if( !count( $graphs ) )
        {
            $this->addMessage(
                "Aggregate graphs have not been configured. Please see <a href=\"https://ixp-manager.readthedocs.org/en/latest/features/grapher.html\">this documentation</a> for instructions.",
                OSS_Message::ERROR
            );
            $this->redirect('');
        }

        $this->view->graphs     = $graphs;

        $graphid = $this->getParam( 'graph', 0 );
        if( !isset( $graphs[ $graphid ] ) )
            $graphid = 0;

        $this->view->graphid    = $graphid;
        $this->view->graph      = $graphs[$graphid];

        $this->setPeriod();
    }

    public function trunksAction()
    {
        if( !is_array( config('grapher.backends.mrtg.trunks') ) || !count( config('grapher.backends.mrtg.trunks') ) ) {
            $this->addMessage(
                "Trunk graphs have not been configured. Please see <a href=\"https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs\">this documentation</a> for instructions.",
                OSS_Message::ERROR
            );
            $this->redirect('');
        }

        // get the available graphs
        foreach( config('grapher.backends.mrtg.trunks') as $g ) {
            $ixpid              = $g['ixpid'];
            $images[]           = $g['name'];
            $graphs[$g['name']] = $g['title'];
        }
        $this->view->graphs  = $graphs;

        $this->setPeriod();

        $grapher = App::make('IXP\Services\Grapher');

        $namereq = $this->getParam( 'trunk', $images[0] );
        if( !in_array( $namereq, $images ) )
            $namereq = $images[0];
        $this->view->namereq   = $namereq;
        $this->view->graph     =  $grapher->trunk( $namereq )->setType( Graph::TYPE_PNG )
                        ->setProtocol( Graph::PROTOCOL_ALL )->setCategory( Graph::CATEGORY_BITS );
    }

    public function switchesAction()
    {
        $eSwitches = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->getAndCache( true, \Entities\Switcher::TYPE_SWITCH );
        $grapher = App::make('IXP\Services\Grapher');
        $category = $this->setCategory( 'category', true );

        $switches = [];
        foreach( $eSwitches as $s ) {
            $switches[ $s->getId() ] = $grapher->switch( $s )->setType( Graph::TYPE_PNG )->setProtocol( Graph::PROTOCOL_ALL )->setCategory( $category );
        }

        $this->view->switches = $switches;

        $switchid = $this->getParam( 'switch', array_keys( $switches )[0] );
        if( !in_array( $switchid, array_keys( $switches ) ) )
            $switchid = array_keys( $switches )[0];

        $this->view->switchid     = $switchid;
        $this->view->graph        = $switches[$switchid];

        $this->setPeriod();
    }

}
