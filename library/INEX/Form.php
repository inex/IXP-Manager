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
 *  @package INEX_Form
 */
class INEX_Form extends Twitter_Form
{

    /**
     * A list of elements we should not update on an edit
     * if the submitted data is an empty string.
     * @var array
     */
    public $onEditSkipIfBlank = null;
    
    public $isEdit = false;

    public function __construct( $options = null, $isEdit = false )
    {
        parent::__construct( $options );

        $this->isEdit = $isEdit;
        
        $this->setAttrib( 'accept-charset', 'UTF-8' );
        $this->setMethod( 'post' );
        $this->setAttrib( "horizontal", true );
        
        $this->onEditSkipIfBlank = array();
    }

    public function assignFormToModel( $model, $controller, $isEdit )
    {

        // For logic to decide what non super users can edit:
        //         $auth = Zend_Auth::getInstance();
        //         if( $auth->hasIdentity() )
        //             $identity = $auth->getIdentity();
        //         else
        //             return false;

        //$identity['user']['privs']


        $columns = Doctrine::getTable( get_class( $model ) )->getFieldNames();

        foreach( $this->getElements() as $elementName => $elementConfig )
        {
            if( in_array( $elementName, $columns ) )
            {
                // don't remove certain elements on an edit
                if( $isEdit and in_array( $elementName, $this->onEditSkipIfBlank ) and $this->getValue( $elementName ) == '' )
                    continue;

                $model->$elementName = $this->getValue( $elementName );
            }
        }

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

    public function assignFromModel( $model )
    {
        $columns = $model->getTable()->getFieldNames();
    
        foreach( $this->getElements() as $elementName => $elementConfig )
            if( in_array( $elementName, $columns ) )
            $this->getElement( $elementName )->setValue( $model->$elementName );
    
        return $this;
    }
    
    public function assignToModel( $model, $isEdit = true )
    {
        $columns = $model->getTable()->getFieldNames();
    
        foreach( $this->getElements() as $elementName => $elementConfig )
        {
            if( in_array( $elementName, $columns ) )
            {
                // don't remove certain elements on an edit
                if( $isEdit and in_array( $elementName, $this->onEditSkipIfBlank ) and $this->getValue( $elementName ) == '' )
                    continue;
    
                $model->$elementName = $this->getValue( $elementName );
            }
        }
    
        return $model;
    }
    
    
    /**
     * Populate a Zend_Form SELECT element from a database table
     *
     *
     * @param Form_Element $element The form element to populate
     * @param string $model The model to select items from
     * @param string $indexElement The element with which to set the select value attributes with
     * @param string|array $displayElements If a string, then the element to show in the select, if an array, a list of elements concatenated with dashes
     * @param string $orderBy The element to order by
     * @param string $orderDir The order direction
     * @return int The maximum value of the $indexElement (asuming integer!)
     */
    public static function createSelectFromDatabaseTable( $element, $model, $indexElement, $displayElements, $orderBy = null, $orderDir = 'ASC' )
    {
        $query = Doctrine_Query::create()
            ->from( "$model m" );

        if( $orderBy !== null )
            $query->orderBy( "m.{$orderBy} {$orderDir}" );

        $collection = $query->execute();

        $options = array( '0' => '' );
        $maxId = 0;

        foreach( $collection as $c )
        {
            $value = '';

            if( is_array( $displayElements ) )
            {
                foreach( $displayElements as $e )
                $value .= "{$c[$e]} - ";

                $value = substr( $value, 0, strlen( $value ) - 2 );
            }
            else
                $value = $c[$displayElements];

            $options[ $c[$indexElement] ] = $value;

            if( $c[$indexElement] > $maxId ) $maxId = $c[$indexElement];
        }

        $element->setMultiOptions( $options );

        return( $maxId );
    }

}

?>