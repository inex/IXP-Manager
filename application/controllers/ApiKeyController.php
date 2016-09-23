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
 * Controller: Manage a user's API keys
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiKeyController extends IXP_Controller_FrontEnd
{
    
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\ApiKey',
            //'form'          => 'IXP_Form_ApiKey',
            'pagetitle'     => 'API Keys',
        
            'titleSingular' => 'API Key',
            'nameSingular'  => 'an API key',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'created',
            'listOrderByDir' => 'ASC',
        
            'listColumns'    => [
        
                'id'           => [ 'title' => 'UID', 'display' => false ],
                'apiKey'       => 'API Key',
                'created'      => [
                    'title'        => 'Created',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'expires'      => [
                    'title'        => 'Expires',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'lastseenAt'   => [
                    'title'        => 'Lastseen',
                    'type'         => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'lastseenFrom' => 'Lastseen From'
            ]
        ];
        
        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
            case \Entities\User::AUTH_CUSTUSER:
                break;
        
            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }
        
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = $this->_feParams->listColumns;
    }
    
    
    /**
     * Provide array of API keys for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select(
                'a.id AS id, a.apiKey as apiKey, a.created AS created, a.expires AS expires,
                 a.lastseenAt AS lastseenAt, a.lastseenFrom AS lastseenFrom'
            )
            ->from( '\\Entities\\ApiKey', 'a' )
            ->leftJoin( 'a.User', 'u' )
            ->where( 'u = :user' )
            ->setParameter( 'user', $this->getUser() );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'a.id = ?1' )->setParameter( 1, $id );
        
        return $qb->getQuery()->getResult();
    }
    
    
    public function editAction()
    {
        $this->addMessage( 'You cannot edit API keys (at this time).', OSS_Message::ERROR );
        $this->redirect( 'api-key/list' );
    }
    
    public function addAction()
    {
        if( count( $this->getUser()->getApiKeys() ) >= 10 )
        {
            $this->addMessage( 'We currently have a limit of 10 API keys per user. Please contact us if you require more.', OSS_Message::ERROR );
            $this->redirect( 'api-key/list' );
        }
        
        $key = new \Entities\ApiKey();
        $key->setUser( $this->getUser() );
        $key->setCreated( new DateTime() );
        $key->setApiKey( OSS_String::random( 48, true, true, true, '', '' ) );
        $key->setAllowedIPs( '' );
        $key->setExpires( null );
        $key->setLastseenFrom( '' );

        $this->getD2EM()->persist( $key );
        $this->getUser()->addApiKey( $key );
        $this->getD2EM()->flush();
        
        $this->addMessage( 'Your new API key has been created - <code>' . $key->getApiKey() . '</code>', OSS_Message::SUCCESS );
        $this->redirect( 'api-key/list' );
    }
}

