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

class PeeringMatrixController extends INEX_Controller_Action
{

    public function indexAction()
    {
        $lan = $this->_request->getParam( 'lan', 0 );

        if( !isset( $this->config['peering_matrix']['public'][$lan] ) )
        {
            $this->session->message = new INEX_Message(
                "Invalid peering matrix requested",
                INEX_Message::MESSAGE_TYPE_ERROR
            );
            return( $this->_redirect( 'dashboard' ) );
        }
        
        $peering_states = Doctrine_Query::create()
            ->select( 'pm.x_as, pm.y_as, pm.peering_status' )
            ->addSelect( 'xc.name, xc.id, xc.peeringmacro, xc.peeringpolicy' )
            ->addSelect( 'yc.name, yc.id, yc.peeringmacro, yc.peeringpolicy' )
            ->from( 'PeeringMatrix pm' )
            ->leftJoin( 'pm.X_Cust xc' )
            ->leftJoin( 'pm.Y_Cust yc' )
            ->where( 'pm.vlan = ?', $this->config['peering_matrix']['public'][$lan]['number'] )
            ->orderBy( 'pm.x_as ASC, pm.y_as ASC' )
            ->fetchArray();
        
        // try and arrange the array as n x n keyed by x's as number
        $matrix = array();
        $potential = 0; 
        $active    = 0; 
        foreach( $peering_states as $pm )
        {
            $matrix[$pm['x_as']][] = $pm;
            if( $pm['peering_status'] == 'YES' )
                $active++;
            $potential++;
        }
        
        $this->view->potential = $potential;
        $this->view->active    = $active;
        $this->view->lan    = $lan;
        $this->view->matrix = $matrix;
        $this->view->display( 'peering-matrix/index.tpl' );
    }
                 
}


