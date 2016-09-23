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
 * Controller: Manage IRRDB sources
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IrrdbConfigController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\IRRDBConfig',
            'form'          => 'IXP_Form_IrrdbConfig',
            'pagetitle'     => 'IRRDB Sources',
        
            'titleSingular' => 'IRRDB Source',
            'nameSingular'  => 'an IRRDB source',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'host',
            'listOrderByDir' => 'ASC',
        
            'listColumns'    => [
                'id'        => [ 'title' => 'UID', 'display' => false ],
                'host'      => 'Host',
                'protocol'  => 'Protocol',
                'source'    => 'Source'
            ]
        ];
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [ 'notes' => 'Notes' ]
        );
    }
    
    /**
     * Provide array of objects for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
        ->select( 'o.id AS id, o.host AS host, o.protocol AS protocol,
                    o.source AS source, o.notes AS notes'
        )
        ->from( '\\Entities\\IRRDBConfig', 'o' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'o.id = ?1' )->setParameter( 1, $id );
    
        return $qb->getQuery()->getResult();
    }
    
 }

