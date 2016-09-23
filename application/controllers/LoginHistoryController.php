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
 * Controller: Login history management
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @author     Nerijus Barauskas <nerijus@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LoginHistoryController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->assertPrivilege( \Entities\User::AUTH_SUPERUSER );
    
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\UserLoginHistory',
            'pagetitle'     => 'Login History',
        
            'titleSingular' => 'Login History',
            'nameSingular'  => 'a Login History',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'at',
            'listOrderByDir' => 'DESC',
            'readonly'       => 'true',
        
            'listColumns'    => [
            
                'id'        => [ 'title' => 'UID', 'display' => false ],
                'ip'    => 'IP',
                'at'       => [
                    'title'     => 'At',
                    'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        ];
        
        // display the same information in the view as the list
        $this->_feParams->viewColumns = $this->_feParams->listColumns;
    }
    
    /**
     * Provide array of VLANs for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 'lh.id AS id, lh.at AS at, lh.ip AS ip, u.id AS user_id' )
            ->from( '\\Entities\\UserLoginHistory', 'lh' )
            ->join( 'lh.User', 'u' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'lh.id = ?1' )->setParameter( 1, $id );

        if( $this->getParam( 'uid', false ) )
            $qb->andWhere( 'u.id = ?2' )->setParameter( 2, $this->getParam( 'uid' ) );

        if( $this->getParam( 'limit', false ) )
            $qb->setMaxResults( 1000 );
    
        return $qb->getQuery()->getResult();
    }
    
}

