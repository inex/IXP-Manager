<?php

use Entities\Switcher;
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
 * Controller: Manage switches (and other devices)
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchController extends IXP_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Switcher',
            'form'          => 'IXP_Form_Switch',
            'pagetitle'     => 'Switches',

            'titleSingular' => 'Switch',
            'nameSingular'  => 'a switch',

            'listOrderBy'    => 'name',
            'listOrderByDir' => 'ASC'
        ];

        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id'        => [ 'title' => 'UID', 'display' => false ],
                    'name'           => 'Name',

                    'cabinet'  => [
                        'title'      => 'Cabinet',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'cabinet',
                        'action'     => 'view',
                        'idField'    => 'cabinetid'
                    ],

                    'vendor'  => [
                        'title'      => 'Vendor',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'vendor',
                        'action'     => 'view',
                        'idField'    => 'vendorid'
                    ],

                    'model'          => 'Model',
                    'ipv4addr'       => 'IPv4 Address',
                    'infrastructure' => 'Infrastructure',
                    'active'       => [
                            'title'    => 'Active',
                            'type'     => self::$FE_COL_TYPES[ 'SCRIPT' ],
                            'script'   => 'frontend/list-column-active.phtml',
                            'colname'  => 'active'
                    ]
                ];

                // display the same information in the view as the list
                $this->_feParams->viewColumns = array_merge(
                    $this->_feParams->listColumns,
                    [
                        'ipv6addr'       => 'IPv6 Address',
                        'snmppasswd'     => 'SNMP Community',
                        'switchtype'     => 'Type',
                        'os'             => 'OS',
                        'osVersion'      => 'OS Version',
                        'serialNumber'   => 'Serial Number',
                        'osDate'         => [
                            'title'      => 'OS Date',
                            'type'       => self::$FE_COL_TYPES[ 'DATETIME' ]
                        ],
                        'lastPolled'         => [
                            'title'      => 'Last Polled',
                            'type'       => self::$FE_COL_TYPES[ 'DATETIME' ]
                        ],
                        'notes'          => 'Notes'
                    ]
                );

                $this->_feParams->defaultAction = 'list';
                break;

            case \Entities\User::AUTH_CUSTUSER:
                $this->_feParams->allowedActions = [ 'configuration' ];
                $this->_feParams->defaultAction = 'configuration';
                break;

            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }
    }

    /**
     * Provide array of users for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
            ->select( 's.id AS id, s.name AS name,
                s.ipv4addr AS ipv4addr, s.ipv6addr AS ipv6addr, s.snmppasswd AS snmppasswd,
                i.name AS infrastructure, s.switchtype AS switchtype, s.model AS model,
                s.active AS active, s.notes AS notes, s.lastPolled AS lastPolled,
                s.hostname AS hostname, s.os AS os, s.osDate AS osDate, s.osVersion AS osVersion,
                s.serialNumber AS serialNumber, s.mauSupported AS mauSupported,
                v.id AS vendorid, v.name AS vendor, c.id AS cabinetid, c.name AS cabinet'
            )
            ->from( '\\Entities\\Switcher', 's' )
            ->leftJoin( 's.Infrastructure', 'i' )
            ->leftJoin( 's.Cabinet', 'c' )
            ->leftJoin( 's.Vendor', 'v' );

        if( $this->getParam( 'infra', false ) && $infra = $this->getD2R( '\\Entities\\Infrastructure' )->find( $this->getParam( 'infra' ) ) )
        {
            $qb->andWhere( 'i = :infra' )->setParameter( 'infra', $infra );
            $this->view->infra = $infra;
        }

        $this->view->switchTypes = $switchTypes = \Entities\Switcher::$TYPES;
        $this->view->stype = $stype = $this->getSessionNamespace()->switch_list_stype
            = $this->getParam( 'stype', ( $this->getSessionNamespace()->switch_list_stype !== null
                ? $this->getSessionNamespace()->switch_list_stype : \Entities\Switcher::TYPE_SWITCH ) );
        if( $stype && isset( $switchTypes[$stype] ) )
            $qb->andWhere( 's.switchtype = :stype' )->setParameter( 'stype', $stype );

        $this->view->activeOnly = $activeOnly = $this->getSessionNamespace()->switch_list_active_only
            = $this->getParam( 'activeOnly', ( $this->getSessionNamespace()->switch_list_active_only !== null
                ? $this->getSessionNamespace()->switch_list_active_only : true ) );
        if( $activeOnly )
            $qb->andWhere( 's.active = :active' )->setParameter( 'active', true );

        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );

        if( $id !== null )
            $qb->andWhere( 's.id = ?1' )->setParameter( 1, $id );

        return $qb->getQuery()->getResult();
    }


    public function osViewAction()
    {
        $this->_feParams->listColumns = [
            'id'        => [ 'title' => 'UID', 'display' => false ],
            'name'           => 'Name',

            'vendor'  => [
                'title'      => 'Vendor',
                'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                'controller' => 'vendor',
                'action'     => 'view',
                'idField'    => 'vendorid'
            ],

            'model'          => 'Model',
            'os'             => 'OS',
            'osVersion'      => 'OS Version',
            'serialNumber'   => 'Serial Number',

            'osDate'         => [
                'title'      => 'OS Date',
                'type'       => self::$FE_COL_TYPES[ 'DATETIME' ]
            ],

            'lastPolled'         => [
                'title'      => 'Last Polled',
                'type'       => self::$FE_COL_TYPES[ 'DATETIME' ]
            ],

            'active'       => [
                    'title'    => 'Active',
                    'type'     => self::$FE_COL_TYPES[ 'SCRIPT' ],
                    'script'   => 'frontend/list-column-active.phtml',
                    'colname'  => 'active'
            ]
        ];

        return $this->listAction();
    }



    /**
     * Add new switch by polling it via SNMP
     */
    public function addBySnmpAction()
    {
        $this->view->sw = $sw = new \Entities\Switcher();
        $this->view->f  = $f  = new IXP_Form_Switch_AddBySNMP();

        if( $this->getRequest()->isPost() && $f->isValid( $_POST ) )
        {
            do
            {
                // ensure provided name and hostname are not in use
                if( $this->getD2R( '\\Entities\\Switcher' )->findOneBy( [ 'name' => $f->getValue( 'name' ) ] )
                        || $this->getD2R( '\\Entities\\Switcher' )->findOneBy( [ 'hostname' => $f->getValue( 'hostname' ) ] ) )
                {
                    $this->addMessage( 'A switch already exists with the given name / hostname', OSS_Message::ERROR );
                    break;
                }

                // can we talk to it by SNMP and discover some basic details?
                try
                {
                    $snmp = new \OSS_SNMP\SNMP( $f->getValue( 'hostname' ), $f->getValue( 'snmppasswd' ) );
                    $vendor = $snmp->getPlatform()->getVendor();
                }
                catch( \OSS_SNMP\Exception $e )
                {
                    $this->addMessage( "Could not query {$f->getValue( 'hostname' )} via SNMP.
                        Consider using the <a href=\"" . OSS_Utils::genUrl( 'switch', 'add' ) . "\">the manual add method</a>.",
                        OSS_Message::ERROR
                    );
                    break;
                }

                if( $vendor == 'Unknown' )
                {
                    $this->addMessage( "Could not interpret switch system description string - most likely
                            because no platform interpretor exists for it.<br/><br/>Please see
                            <a href=\"https://github.com/opensolutions/OSS_SNMP/wiki/Device-Discovery\">this OSS_SNMP page</a>
                            and consider adding one.<br /><br />
                            Otherwise use the <a href=\"" . OSS_Utils::genUrl( 'switch', 'add' ) . "\">the manual add method</a>.",
                        OSS_Message::ERROR
                    );
                    break;
                }


                if( !( $eVendor = $this->getD2R( '\\Entities\\Vendor' )->findOneBy( [ 'name' => $vendor ] ) ) )
                {
                    $this->addMessage( "No vendor defined for [{$vendor}]. Please
                        <a href=\"" . OSS_Utils::genUrl( 'vendor', 'add' ) . "\">add one first</a>.",
                        OSS_Message::ERROR
                    );
                    break;
                }


                // now we have a switch with all the necessary details, add it:
                $s = new Switcher();
                $s->setCabinet(
                    $this->getD2R( '\\Entities\\Cabinet' )->find( $f->getValue( 'cabinetid' ) )
                );
                $s->setVendor( $eVendor );
                $s->setName( $f->getValue( 'name' ) );
                $s->setHostname( $f->getValue( 'hostname' ) );
                $s->setIpv4addr( $this->_resolve( $s->getHostname(), DNS_A    ) );
                $s->setIpv6addr( $this->_resolve( $s->getHostname(), DNS_AAAA ) );
                $s->setSnmppasswd( $f->getValue( 'snmppasswd' ) );
                $s->setInfrastructure( $this->getD2R( '\\Entities\\Infrastructure' )->find( $f->getValue( 'infrastructure' ) ) );
                $s->setSwitchtype( $f->getValue( 'switchtype' ) );
                $s->setModel( $snmp->getPlatform()->getModel() );
                $s->setActive( true );
                $s->setOs( $snmp->getPlatform()->getOs() );
                $s->setOsDate( $snmp->getPlatform()->getOsDate() );
                $s->setOsVersion( $snmp->getPlatform()->getOsVersion() );
                $s->setLastPolled( new DateTime() );

                $this->getD2EM()->persist( $s );
                $this->getD2EM()->flush();

                // clear the cache
                $this->getD2R( '\\Entities\\Switcher' )->clearCache();

                $this->addMessage(
                    "Switch polled and added successfully! Please configure the ports found below.", OSS_Message::SUCCESS
                );

                $this->redirect( 'switch-port/snmp-poll/switch/' . $s->getId() );
            }while( false );
        }

        $this->_display( 'add-by-snmp.phtml' );
    }


    /**
     * Resolve a hostname into an IPv4/IPv6 address
     *
     * **NB:** Assumes only one IP address and as such only the first is returned
     *
     * @param string $hn The hostname to resolve
     * @param int $type The DNS query type - either DNS_A or DNS_AAAA
     * @throws Exception In the event that an unsupprted query type is requested
     * @return string|null The resolved IP address or null
     */
    private function _resolve( $hn, $type )
    {
        $a = dns_get_record( $hn, $type );

        if( empty( $a ) )
            return null;

        if( $type == DNS_A )
            return $a[0]['ip'];

        if( $type == DNS_AAAA )
            return $a[0]['ipv6'];

        throw new Exception( 'Unhandled DNS query type.' );
    }


    /**
     *
     * @param IXP_Form_Switch $form The form object
     * @param \Entities\Switcher $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @param array $options Options passed onto Zend_Form
     * @param string $cancelLocation Where to redirect to if 'Cancal' is clicked
     * @return void
     */
    protected function formPostProcess( $form, $object, $isEdit, $options = null, $cancelLocation = null )
    {
        if( $isEdit )
        {
            if( $object->getCabinet() )
                $form->getElement( 'cabinetid' )->setValue( $object->getCabinet()->getId() );
            if( $object->getVendor() )
                $form->getElement( 'vendorid'  )->setValue( $object->getVendor()->getId()  );

            if( $object->getInfrastructure() )
                $form->getElement( 'infrastructure' )->setValue( $object->getInfrastructure()->getId() );
            else
                $form->getElement( 'infrastructure' )->setValue( null );
        }
    }


    /**
     *
     * @param IXP_Form_Switch $form The form object
     * @param \Entities\Switcher $object The Doctrine2 entity (being edited or blank for add)
     * @param bool $isEdit True of we are editing an object, false otherwise
     * @return void
     */
    protected function addPostValidate( $form, $object, $isEdit )
    {
        $object->setCabinet(
            $this->getD2EM()->getRepository( '\\Entities\\Cabinet' )->find( $form->getElement( 'cabinetid' )->getValue() )
        );

        $object->setVendor(
            $this->getD2EM()->getRepository( '\\Entities\\Vendor' )->find( $form->getElement( 'vendorid' )->getValue() )
        );

        if( $form->getElement( 'infrastructure' )->getValue() )
        {
            $object->setInfrastructure(
                $this->getD2EM()->getRepository( '\\Entities\\Infrastructure' )->find( $form->getElement( 'infrastructure' )->getValue() )
            );
        }

        return true;
    }

    /**
     * Clear the cache after a change to a switch
     *
     * @param \Entities\Switcher $object
     * @return boolean
     */
    protected function postFlush( $object )
    {
        // this is created in Repositories\Switcher::getAndCache()
        $this->getD2R( '\\Entities\\Switcher' )->clearCache();
        return true;
    }


    function portReportAction()
    {
        $this->view->switches = $switches = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->getNames();
        $this->view->switch   = $switch   = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->find( $this->getParam( 'id', 0 ) );

        if( $switch === null )
        {
            $this->addMessage( 'Unknown switch.', OSS_Message::ERROR );
            return $this->redirect( 'switch/list' );
        }

        $allports = $this->getD2EM()->createQuery(
                'SELECT sp.id AS spid, sp.name AS portname, sp.type AS porttype
                FROM \\Entities\\SwitchPort sp
                WHERE sp.Switcher = ?1
                ORDER BY spid ASC'
            )
            ->setParameter( 1, $switch )
            ->getResult();

        $ports = $this->getD2EM()->createQuery(
                'SELECT sp.id AS spid, sp.name AS portname, sp.type AS porttype,
                        pi.speed AS speed, pi.duplex AS duplex, c.name AS custname

                FROM \\Entities\\SwitchPort sp
                    JOIN sp.PhysicalInterface pi
                    JOIN pi.VirtualInterface vi
                    JOIN vi.Customer c

                WHERE sp.Switcher = ?1

                ORDER BY spid ASC'
            )
            ->setParameter( 1, $switch )
            ->getArrayResult();

        foreach( $allports as $id => $port )
        {
            if( isset( $ports[0] ) && $ports[0][ 'portname' ] == $port[ 'portname' ] )
                $allports[ $port[ 'portname' ] ] = array_shift( $ports );
            else
                $allports[ $port[ 'portname' ] ] = $port;

            $allports[ $port[ 'portname' ] ]['porttype'] = \Entities\SwitchPort::$TYPES[ $allports[ $port[ 'portname' ] ]['porttype'] ];

            unset( $allports[ $id ] );
        }

        $this->view->ports = $allports;
    }

    public function configurationAction()
    {
        $superuser = $this->getUser()->getPrivs() == \Entities\User::AUTH_SUPERUSER;
        if( $this->getParam( 'ixp', false ) )
        {
            $this->view->ixp = $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $this->getParam( 'ixp' ) );
            if( !$superuser && !$this->getUser()->getCustomer()->getIXPs()->contains( $ixp ) )
                $this->redirectAndEnsureDie( '/erro/insufficient-permissions' );
        }
        else if( !$superuser )
            $this->view->ixp = $ixp = $this->getUser()->getCustomer()->getIXPs()[0];
        else
        {
            $ixp = $this->getD2R( "\\Entities\\IXP" )->findAll();
            if( $ixp )
                $this->view->ixp = $ixp = $ixp[0];
            else
                $ixp = false;
        }

        $this->view->registerClass( 'PHYSICALINTERFACE', '\\Entities\\PhysicalInterface' );
        $this->view->states   = \Entities\PhysicalInterface::$STATES;
        $this->view->ixps     = $ixps     = $this->getD2R( '\\Entities\\IXP'      )->getNames( $this->getUser() );
        $this->view->vlans    = $vlans    = $this->getD2R( '\\Entities\\Vlan'     )->getNames( 1, $ixp );
        $this->view->switches = $switches = $this->getD2R( '\\Entities\\Switcher' )->getNames( false, 0, $ixp );

        $this->view->switchid = $sid   = ( $this->getParam( 'sid', false ) && isset( $switches[ $this->getParam( 'sid' ) ] ) ) ? $this->getParam( 'sid' ) : null;
        $this->view->vlanid   = $vid   = ( $this->getParam( 'vid', false ) && isset( $vlans[    $this->getParam( 'vid' ) ] ) ) ? $this->getParam( 'vid' ) : null;
        $this->view->ixpid    = $ixpid = $ixp ? $ixp->getId() : null;

        $this->view->config = $this->getD2EM()->getRepository( '\\Entities\\Switcher' )->getConfiguration( $sid, $vid, $ixpid, $superuser );
    }


    /**
     * Function which can be over-ridden to perform any pre-deletion tasks
     *
     * You can stop the deletion by returning false but you should also add a
     * message to explain why.
     *
     * @param object $object The Doctrine2 entity to delete
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete( $object )
    {
        foreach( $object->getPorts() as $p )
        {
            if( $p->getPhysicalInterface() )
            {
                $this->addMessage(
                    "Could not delete the switch as at least one switch port is assigned to a physical interface for a customer",
                    OSS_Message::ERROR
                );
                return false;
            }
        }

        // if we got here, all switch ports are free
        foreach( $object->getPorts() as $p )
            $this->getD2EM()->remove( $p );

        foreach( $object->getSecEvents() as $se )
            $this->getD2EM()->remove( $se );

        return true;
    }


}
