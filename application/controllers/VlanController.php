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
 * Controller: VLAN management
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
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
                'ixp'    => 'IXP',
                'infrastructure'    => 'Infrastructure',

                'private'        => [
                    'title'          => 'Private',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => \Entities\Vlan::$PRIVATE_YES_NO
                ],

                'rcvrfname' => 'VRF Name'
            ]
        ];

        if( !$this->multiIXP() )
            unset( $this->_feParams->listColumns['ixp'] );

        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'peering_matrix' => [
                    'title'          => 'Peering Matrix',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => \Entities\Vlan::$PRIVATE_YES_NO
                ],

                'peering_manager' => [
                    'title'          => 'Peering Manager',
                    'type'           => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'         => \Entities\Vlan::$PRIVATE_YES_NO
                ],

                'notes' => 'Notes' 
            ]
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
                    v.rcvrfname AS rcvrfname, v.notes AS notes,
                    v.private AS private, v.peering_matrix AS peering_matrix,
                    v.peering_manager AS peering_manager,
                    i.shortname AS infrastructure,
                    ix.shortname AS ixp'
            )
            ->from( '\\Entities\\Vlan', 'v' )
            ->join( 'v.Infrastructure', 'i' )
            ->join( 'i.IXP', 'ix' );

        if( $this->getParam( 'infra', false ) && $infra = $this->getD2R( '\\Entities\\Infrastructure' )->find( $this->getParam( 'infra' ) ) )
        {
            $qb->andWhere( 'i = :infra' )->setParameter( 'infra', $infra );
            $this->view->infra = $infra;
        }

        if( $this->getParam( 'publiconly', false ) )
        {
            $qb->andWhere( 'v.private = 0' );
            $this->view->publiconly = 1;
        }

        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 'v.id = ?1' )->setParameter( 1, $id );

        return $qb->getQuery()->getResult();
    }

    /**
     * Post process hook for add and edit actions.
     *
     * This is called immediately after the initstantiation of the form object and, if
     * editing, includes the Doctrine2 entity `$object`.
     *
     * If you need to have, for example, edit values set in the form, then use the
     * `addPrepare()` hook rather than this one.
     *
     * @see addPrepare()
     * @param OSS_Form $form The form object
     * @param \Entities\Vlan $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     */
     protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
     {
        if( $isEdit )
            $form->getElement( 'infrastructure' )->setValue( $object->getInfrastructure()->getId() );

        return true;
     }

    /**
     *
     * @param IXP_Form $form The form object
     * @param \Entities\Vlan $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {

        $object->setInfrastructure(
            $this->getD2EM()->getRepository( '\\Entities\\Infrastructure' )->find( $form->getElement( 'infrastructure' )->getValue() )
        );

        return true;
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


    /**
     * Show details of private VLANs
     */
    public function privateAction()
    {
        $infra = null;
        if( $this->getParam( 'infra', false ) && $infra = $this->getD2R( '\\Entities\\Infrastructure' )->find( $this->getParam( 'infra' ) ) )
            $this->view->infra = $infra;

    	$this->view->pvs = $this->getD2EM()->getRepository( '\\Entities\\Vlan' )->getPrivateVlanDetails( $infra );
    }

}
