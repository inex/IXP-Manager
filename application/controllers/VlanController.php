<?php

/*
 * Copyright (C) 2009-2012 Internet Neutral Exchange Association Limited.
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
 * Controller: VLAN management
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Vlan',
            'form'          => 'IXP_Form_Vlan',
            'pagetitle'     => 'VLANs',
        
            'titleSingular' => 'VLAN',
            'nameSingular'  => 'a VLAN',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'number',
            'listOrderByDir' => 'ASC',
        
            'listColumns'    => [
            
                'id'        => [ 'title' => 'UID', 'display' => false ],
                'name'      => 'Name',
                'number'    => 'Tag',
                
                'private'        => [
                    'title'          => 'Private',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => \Entities\Vlan::$PRIVATE_YES_NO
                ],
                
                'rcvrfname' => 'VRF Name'
            ]
        ];
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [ 'notes' => 'Notes' ]
        );
    }
    
    /**
     * Provide array of VLANs for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'v.id AS id, v.name AS name, v.number AS number,
                    v.rcvrfname AS rcvrfname, v.notes AS notes, v.private AS private'
            )
            ->from( '\\Entities\\Vlan', 'v' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'v.id = ?1' )->setParameter( 1, $id );
    
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Clear the cache after a change to a VLAN
     *
     * @param \Entities\Vlan $object
     * @return boolean
     */
    protected function postFlush( $object )
    {
        // this is created in Repositories\Vlan::getNames()
        $this->getD2Cache()->delete( \Repositories\Vlan::ALL_CACHE_KEY );
        return true;
    }
    
}

