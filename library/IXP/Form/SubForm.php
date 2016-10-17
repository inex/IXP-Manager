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
 *
 * @package IXP_Form
 * @subpackage SubForm
 */
class IXP_Form_SubForm extends Zend_Form_SubForm
{

    public function assignFormToModel( $model, $controller, $isEdit )
    {

        // For logic to decide what non super users can edit:
        //         $auth = Zend_Auth::getInstance();
        //         if( $auth->hasIdentity() )
        //             $identity = $auth->getIdentity();
        //         else
        //             return false;

        //$identity['user']['privs']


        $columns = Doctrine::getTable( $controller->getModelName() )->getFieldNames();

        foreach( $this->getElements() as $elementName => $elementConfig )
            if( in_array( $elementName, $columns ) )
                $model->$elementName = $this->getValue( $elementName );

        return $model;
    }

    public function assignModelToForm( $model, $controller )
    {
        $columns = Doctrine::getTable( $controller->getModelName() )->getFieldNames();

        foreach( $this->getElements() as $elementName => $elementConfig )
            if( in_array( $elementName, $columns ) )
                $this->getElement( $elementName )->setValue( $model->$elementName );

        return $this;
    }

}

?>