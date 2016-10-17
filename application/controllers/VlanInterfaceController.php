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
 * Controller: VLAN Interface controller
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanInterfaceController extends IXP_Controller_FrontEnd
{

    use IXP_Controller_Trait_Interfaces;

    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\VlanInterface',
            'form'          => 'IXP_Form_Interface_Vlan',
            'pagetitle'     => 'VLAN Interfaces',

            'titleSingular' => 'VLAN Interface',
            'nameSingular'  => 'a VLAN interface',

            'defaultAction' => 'list',

            'listOrderBy'    => 'customer',
            'listOrderByDir' => 'ASC',
        ];

        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id' => [ 'title' => 'UID', 'display' => false ],

                    'customer'  => [
                        'title'      => 'Customer',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'custid'
                    ],

                    'vlan'  => [
                        'title'      => 'VLAN Name',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'vlan',
                        'action'     => 'list',
                        'idField'    => 'vlanid'
                    ],
                    'rsclient'       => [
                            'title'    => 'Route Server',
                            'type'     => self::$FE_COL_TYPES[ 'SCRIPT' ],
                            'script'   => 'frontend/list-column-active.phtml',
                            'colname'  => 'rsclient'
                    ],
                    'ipv4'          => 'ipv4',
                    'ipv6'          => 'ipv6'
                ];

                $this->_feParams->viewColumns = array_merge(
                    $this->_feParams->listColumns,
                    [
                        'ipv4enabled'      => 'IPv4 Enabled',
                        'ipv4hostname'     => 'IPv4 Hostname',
                        'ipv6enabled'      => 'IPv6 Enabled',
                        'ipv6hostname'     => 'IPv6 Hostname',
                        'mcastenabled'     => 'Multicast Enabled',
                        'irrdbfilter'      => 'IRRDB Filter',
                        'bgpmd5secret'     => 'BGP MD5 Secret (deprecated)',
                        'ipv4bgpmd5secret' => 'IPv4 BGP MD5 Secret',
                        'ipv6bgpmd5secret' => 'IPv6 BGP MD5 Secret',
                        'maxbgpprefix'     => 'Max BGP Prefixes',
                        'rsclient'         => 'Route Server Client',
                        'ipv4canping'      => 'Monitoring Enabled via IPv4 ICMP',
                        'ipv6canping'      => 'Monitoring Enabled via IPv6 ICMP',
                        'ipv4monitorrcbgp' => 'Monitor Route Collector IPv4 BGP Session',
                        'ipv6monitorrcbgp' => 'Monitor Route Collector IPv6 BGP Session',
                        'busyhost'         => 'Busy Host?',
                        'notes'            => 'Notes'
                    ],
                    ( $this->as112UiActive() ? ['as112client' => 'AS112 Client'] : [] )
                );

                break;

            case \Entities\User::AUTH_CUSTADMIN:
            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }

    }



    /**
     * Provide array of virtual interfaces for the listAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select(
                'vli.id AS id, vli.mcastenabled AS mcastenabled,
                 vli.ipv4enabled AS ipv4enabled, vli.ipv4hostname AS ipv4hostname, vli.ipv4canping AS ipv4canping,
                     vli.ipv4monitorrcbgp AS ipv4monitorrcbgp, vli.ipv4bgpmd5secret AS ipv4bgpmd5secret,
                 vli.ipv6enabled AS ipv6enabled, vli.ipv6hostname AS ipv6hostname, vli.ipv6canping AS ipv6canping,
                     vli.ipv6monitorrcbgp AS ipv6monitorrcbgp, vli.ipv6bgpmd5secret AS ipv6bgpmd5secret,
                 vli.irrdbfilter AS irrdbfilter, vli.bgpmd5secret AS bgpmd5secret, vli.maxbgpprefix AS maxbgpprefix,
                 vli.as112client AS as112client, vli.busyhost AS busyhost, vli.notes AS notes,
                 vli.rsclient AS rsclient,
                 ip4.address AS ipv4, ip6.address AS ipv6,
                 v.id AS vlanid, v.name AS vlan,
                 vi.id AS vintid,
                 c.name AS customer, c.id AS custid'
            )
            ->from( '\\Entities\\VlanInterface', 'vli' )
            ->leftJoin( 'vli.VirtualInterface', 'vi' )
            ->leftJoin( 'vli.Vlan', 'v' )
            ->leftJoin( 'vli.IPv4Address', 'ip4' )
            ->leftJoin( 'vli.IPv6Address', 'ip6' )
            ->leftJoin( 'vi.Customer', 'c' );

        if( $id !== null )
            $qb->where( 'vli.id = ' . intval( $id ) );

        return $qb->getQuery()->getArrayResult();
    }


    /**
     * @param IXP_Form_Interface_Vlan $form The form object
     * @param \Entities\VlanInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
        {
            $form->getElement( 'virtualinterfaceid' )->setValue( $object->getVirtualInterface()->getId() );
            $form->getElement( 'preselectCustomer'  )->setValue( $object->getVirtualInterface()->getCustomer()->getId() );
            $form->getElement( 'vlanid'             )->setValue( $object->getVlan()->getId()             );

            $form->getElement( 'preselectIPv4Address'   )->setValue( $object->getIPv4Address() ? $object->getIPv4Address()->getAddress() : null );
            $form->getElement( 'preselectIPv6Address'   )->setValue( $object->getIPv6Address() ? $object->getIPv6Address()->getAddress() : null );
            $form->getElement( 'preselectVlanInterface' )->setValue( $object->getId()        );

            if( $this->getParam( 'rtn', false ) == 'vli' )
                $form->setAction( OSS_Utils::genUrl( 'vlan-interface', 'edit', false, [ 'id' => $object->getId(), 'rtn' => 'vli' ] ) );
            else
                $form->getElement( 'cancel' )->setAttrib( 'href', OSS_Utils::genUrl( 'virtual-interface', 'edit', false, [ 'id' => $object->getVirtualInterface()->getId() ] ) );
        }
        else // not editing
        {
            if( $this->getRequest()->isPost() && ( $vintid = ( isset( $_POST['virtualinterfaceid'] ) && $_POST['virtualinterfaceid'] ) ) )
                $vint = $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->find( $_POST['virtualinterfaceid'] );
            else if( ( $vintid = $this->getRequest()->getParam( 'vintid' ) ) !== null )
                $vint = $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->find( $vintid );

            if( !isset( $vint ) || !$vint )
            {
                $this->addMessage( 'You need a containing virtual interface before you add a VLAN interface', OSS_Message::ERROR );
                $this->redirect( 'virtual-interface/add' );
            }


            // make BGP MD5 easy
            $form->getElement( 'ipv4bgpmd5secret' )->setValue( OSS_String::random() );
            $form->getElement( 'ipv6bgpmd5secret' )->setValue(  $form->getElement( 'ipv4bgpmd5secret' )->getValue() );
            $form->getElement( 'maxbgpprefix' )->setValue( $vint->getCustomer()->getMaxprefixes() );

            $form->getElement( 'virtualinterfaceid' )->setValue( $vint->getId() );
            $form->getElement( 'preselectCustomer'  )->setValue( $vint->getCustomer()->getId() );

            $form->getElement( 'cancel' )->setAttrib( 'href', OSS_Utils::genUrl( 'virtual-interface', 'edit', false, [ 'id' => $vint->getId() ] ) );
        }
    }

    /**
     * @param IXP_Form_Interface_Vlan $form The form object
     * @param \Entities\VlanInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setVirtualInterface(
            $this->getD2EM()->getRepository( '\\Entities\\VirtualInterface' )->find( $form->getElement( 'virtualinterfaceid' )->getValue() )
        );

        $object->setVlan(
            $this->getD2EM()->getRepository( '\\Entities\\Vlan' )->find( $form->getElement( 'vlanid' )->getValue() )
        );


         if( !$this->setIp( $form, $object->getVirtualInterface(), $object, false ) || !$this->setIp( $form, $object->getVirtualInterface(), $object, true ) )
            return false;

        return true;
    }


    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful add / edit operation.
     *
     * @param IXP_Form_Interface_Vlan $form The form object
     * @param \Entities\VlanInterface $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function addDestinationOnSuccess( $form, $object, $isEdit  )
    {
        if( $this->getParam( 'rtn', false ) == 'vli' )
            return false;

        $this->addMessage(
            'VLAN interface successfuly ' . ( $isEdit ? 'edited.' : 'added.' ), OSS_Message::SUCCESS
        );

        $this->redirectAndEnsureDie( 'virtual-interface/edit/id/' . $object->getVirtualInterface()->getId() );
    }

    /**
     * You can add `OSS_Message`s here and redirect to a custom destination after a
     * successful deletion operation.
     *
     * @return bool `false` for standard message and redirection, otherwise redirect within this function
     */
    protected function deleteDestinationOnSuccess()
    {
        if( $this->getParam( 'rtn', false ) == 'vli' )
            return false;

        $this->addMessage(
            'VLAN interface deleted successfuly.', OSS_Message::SUCCESS
        );

        $this->redirectAndEnsureDie( 'virtual-interface/edit/id/' . $this->getParam( 'vintid' ) );
    }

}
