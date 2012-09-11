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
 * CustAdminController
 *
 * @author
 * @version
 */

class CustAdminController extends INEX_Controller_Action
{
    /**
     * Allow a CUSTADMIN to manage their users
     */
    public function usersAction()
    {
        if( isset( $this->session->custadminInstructions ) )
            $this->view->skipInstructions = true;
        else
        {
            $this->session->custadminInstructions = true;
            $this->view->skipInstructions = false;
        }
    }


    public function toggleEnabledAction()
    {
        // load the user and see if it exists
        if( !( $u = Doctrine::getTable( 'User' )->find( $this->getRequest()->getParam( 'id' ) ) ) )
        {
            $this->view->message = new INEX_Message( 'There is no such user in our database', INEX_Message::MESSAGE_TYPE_ERROR );
            return( $this->_forward( 'users' ) );
        }

        // now is the current CUSTADMIN user entitled to edit the specified user?
        if( $u->custid != $this->user->custid )
        {
            $this->getLogger()->alert( "In cust-admin/toggle-enabled, user ($this->user->username} tried to illegally edit {$u->username}!" );
            $this->view->message = new INEX_Message( 'You have tried to edit a user that is not yours. Our administrators have been alerted and will act accordingly.', INEX_Message::MESSAGE_TYPE_ALERT );
            return( $this->_forward( 'users' ) );
        }

        $u->disabled = ( $u->disabled + 1 ) % 2;
        $u->save();

        if( $u->disabled )
            $this->session->message = new INEX_Message( "You have disabled user {$u->username}.", INEX_Message::MESSAGE_TYPE_SUCCESS );
        else
            $this->session->message = new INEX_Message( "You have enabled user {$u->username}.", INEX_Message::MESSAGE_TYPE_SUCCESS );

        $this->getLogger()->info( "cust-admin/toggle-enabled: {$this->user->username} set disbaled flag of {$u->username} to {$u->disabled}" );

        $this->_redirect( 'cust-admin/users' );
    }

}

