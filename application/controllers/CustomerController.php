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
 * Controller: Customers
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   INEX
 * @package    INEX_Controller
 * @copyright  Copyright (c) 2009 - 2012, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerController extends INEX_Controller_FrontEnd
{
    /**
     * This function sets up the frontend controller
     */
    protected function _feInit()
    {
        $this->view->registerClass( 'CUSTOMER', '\\Entities\\Customer' );
        
        $this->view->feParams = $this->_feParams = (object)[
            'entity'        => '\\Entities\\Customer',
            'form'          => 'INEX_Form_Customer',
            'pagetitle'     => 'Customers',
        
            'titleSingular' => 'Customer',
            'nameSingular'  => 'a customer',
        
            'defaultAction' => 'list',                    // OPTIONAL; defaults to 'list'
        
            'listOrderBy'    => 'c.name',
            'listOrderByDir' => 'ASC',
        ];
    
        switch( $this->getUser()->getPrivs() )
        {
            case \Entities\User::AUTH_SUPERUSER:
                $this->_feParams->listColumns = [
                    'id' => [ 'title' => 'UID', 'display' => false ],
        
                    'name'        => [
                        'title'      => 'Name',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'id'
                    ],
        
                    'autsys'      => 'AS',
                    
                    'shortname'   => [
                        'title'      => 'Shortname',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'id'
                    ],
                    
                    'peeringemail'   => 'Peering Email',
                    'noc24hphone'    => 'NOC 24h Phone',
                    
                    'datejoin'       => [
                        'title'     => 'Joined',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ]
                ];
                break;
    
            case \Entities\User::AUTH_CUSTADMIN:
            default:
                $this->redirectAndEnsureDie( 'error/insufficient-permissions' );
        }
    
        // display the same information in the view as the list
        $this->_feParams->viewColumns = array_merge(
            $this->_feParams->listColumns,
            [
                'type'            => [
                    'title'         => 'Type',
                    'type'          => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'        => \Entities\Customer::$CUST_TYPES_TEXT
                ],
                'maxprefixes'     => 'Max Prefixes',
                'nocphone'        => 'NOC Phone',
                'nocfax'          => 'NOC Fax',
                'nochours'        => 'NOC Hours',
                'nocemail'        => 'NOC Email',
                'nocwww'          => 'NOC WWW',
                'irrdb'           => 'IRRDB',
                'status'          => [
                    'title'         => 'Status',
                    'type'          => self::$FE_COL_TYPES[ 'XLATE' ],
                    'xlator'        => \Entities\Customer::$CUST_STATUS_TEXT
                ],
                'activepeeringmatrix' => 'Active Peering Matrix',
                'peeringmacro'    => 'Peering Macro',
                'peeringpolicy'   => 'Peering Policy',
                'billingContact'  => 'Billing Contact',
                'billingAddress1' => 'Billing Address1',
                'billingAddress2' => 'Billing Address2',
                'billingCity'     => 'Billing City',
                'billingCountry'  => 'Billing Country',
                'corpwww'         => 'Corporate WWW',
                'dateleave'       => [
                        'title'     => 'Left',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'notes'           => 'Notes',
                'lastupdated'     => 'Last Updated',
                'lastupdatedby'   => 'Last Updated By',
                'creator'         => 'Created By',
                'created'         => 'Created'
            ]
        );
    }
    
    
    
    
    /**
     * Provide array of customers for the listAction and viewAction
     *
     * @param int $id The `id` of the row to load for `viewAction`. `null` if `listAction`
     */
    protected function listGetData( $id = null )
    {
        $qb = $this->getD2EM()->createQueryBuilder()
                ->select( 'c.id AS id, c.name AS name, c.shortname AS shortname, c.type AS type,
                            c.autsys AS autsys, c.maxprefixes AS maxprefixes, c.peeringemail AS peeringemail,
                            c.nocphone AS nocphone, c.noc24hphone AS noc24hphone, c.nocfax AS nocfax,
                            c.nochours AS nochours, c.nocemail AS nocemail, c.nocwww AS nocwww,
                            c.irrdb AS irrdb, c.status AS status, c.activepeeringmatrix AS activepeeringmatrix,
                            c.peeringmacro AS peeringmacro, c.peeringpolicy AS peeringpolicy,
                            c.billingContact AS billingContact, c.billingAddress1 AS billingAddress1,
                            c.billingAddress2 AS billingAddress2, c.billingCity AS billingCity, c.billingCountry AS billingCountry,
                            c.corpwww AS corpwww, c.datejoin AS datejoin, c.dateleave AS dateleave,
                            c.notes AS notes, c.lastupdated AS lastupdated, c.lastupdatedby AS lastupdatedby,
                            c.creator AS creator, c.created AS created'
                        )
                ->from( '\\Entities\\Customer', 'c' );
    
        if( isset( $this->_feParams->listOrderBy ) )
            $qb->orderBy( $this->_feParams->listOrderBy, isset( $this->_feParams->listOrderByDir ) ? $this->_feParams->listOrderByDir : 'ASC' );
    
        if( $id !== null )
            $qb->andWhere( 'c.id = ?3' )->setParameter( 3, $id );
    
        return $qb->getQuery()->getResult();
    }
    
    
    public function viewAction()
    {
        $this->forward( 'overview' );
    }

    
    /**
     * The Customer Overview
     */
    public function overviewAction()
    {
        // Is the customer ID valid?
        if( !$this->getRequest()->getParam( 'id', false ) )
            return $this->forward( 'list' );
        
        $this->view->cust = $cust = $this->getD2EM()->getRepository( '\\Entities\\Customer' )->find( $this->getRequest()->getParam( 'id' ) );

        if( !$cust )
        {
            $this->addMessage( 'Invalid customer ID', OSS_Message::ERROR );
            return( $this->forward( 'list' ) );
        }
    }
    
    
    /**
     * Additional checks when a new object is being added.
     */
    protected function formValidateForAdd( $form )
    {

        if( Doctrine::getTable( $this->getModelName() )->findOneByShortname( $form->getValue( 'shortname' ) ) )
        {
            $form->getElement( 'shortname' )->addError( 'This short name is not available' );
            return false;
        }
    }



    /**
     * A generic action to list the elements of a database (as represented
     * by a Doctrine model) via Smarty templates.
     */
    public function getDataAction()
    {
        $dataQuery = Doctrine_Query::create()
        ->from( $this->getModelName() . ' x' )
        ->orderBy( 'x.shortname ASC' );

        if( $this->getRequest()->getParam( 'member' ) !== NULL )
        $dataQuery->andWhere( 'x.name LIKE ?', $this->getRequest()->getParam( 'member' ) . '%' );

        if( $this->getRequest()->getParam( 'shortname' ) !== NULL )
        $dataQuery->andWhere( 'x.shortname LIKE ?', $this->getRequest()->getParam( 'shortname' ) . '%' );


        $rows = $dataQuery->execute();

        $count = 0;
        $data = '';
        foreach( $rows as $row )
        {
            if( $count > 0 )
            $data .= ',';

            $count++;

            $data .= <<<END_JSON
    {
        "member":"{$row['name']}",
        "id":"{$row['id']}",
        "autsys":"{$row['autsys']}",
        "shortname":"{$row['shortname']}",
        "peeringemail":"{$row['peeringemail']}",
        "nocphone":"{$row['nocphone']}"
    }
END_JSON;

        }

        $data = <<<END_JSON
{"ResultSet":{
    "totalResultsAvailable":{$count},
    "totalResultsReturned":{$count},
    "firstResultPosition":0,
    "Result":[{$data}]}}
END_JSON;

        echo $data;

    }




    /**
     * Send the member an operations welcome mail
     *
     */
    public function sendWelcomeEmailAction()
    {
        // Is the customer ID valid?
        if( $this->getRequest()->getParam( 'id' ) !== NULL && is_numeric( $this->getRequest()->getParam( 'id' ) ) )
        {
            if( !( $customer = Doctrine::getTable( 'Cust' )->find( $this->getRequest()->getParam( 'id' ) ) ) )
            {
                $this->view->message = new INEX_Message( 'Invalid Member ID', "error" );
                return( $this->_forward( 'list' ) );
            }

            $this->view->customer = $customer;
        }
        else
        {
            $this->view->message = new INEX_Message( 'Invalid Member ID', "error" );
            return( $this->_forward( 'list' ) );
        }
        
        $this->view->custadmin = $customer->getCustAdminUser();

        $cancelLocation = 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . '://'
            . $_SERVER['SERVER_NAME'] . Zend_Controller_Front::getInstance()->getBaseUrl()
            . '/' . $this->getRequest()->getParam( 'controller' ) . '/list';

        $form = new INEX_Form_Customer_SendWelcomeEmail( null, false, $cancelLocation );

        $form->getElement( 'to' )->setValue( $customer['nocemail'] );

        $userEmails = array();
        foreach( $customer['User'] as $user )
        {
            if( Zend_Validate::is( $user['email'], 'EmailAddress' ) )
                $userEmails[] = $user['email'];
        }

        $form->getElement( 'cc' )->setValue( implode( ',', $userEmails ) );

        $form->getElement( 'bcc' )->setValue( $this->config['identity']['email'] );
        $form->getElement( 'subject' )->setValue( $this->config['identity']['name'] . ' :: Welcome Mail' );

        // Let's get the information we need for the welcome mail from the database.

        $this->view->networkInfo = Networkinfo::toStructuredArray();

        $this->view->connections = Doctrine_Query::create()
	        ->from( 'Virtualinterface vi' )
	        ->leftJoin( 'vi.Cust c' )
	        ->leftJoin( 'vi.Physicalinterface pi' )
	        ->leftJoin( 'vi.Vlaninterface vli' )
	        ->leftJoin( 'vli.Ipv4address v4' )
	        ->leftJoin( 'vli.Ipv6address v6' )
	        ->leftJoin( 'vli.Vlan v' )
	        ->leftJoin( 'pi.Switchport sp' )
	        ->leftJoin( 'sp.SwitchTable s' )
	        ->leftJoin( 's.Cabinet cb' )
	        ->leftJoin( 'cb.Location l' )
	        ->where( 'c.id = ?', $customer['id'] )
	        ->orderBy( 'pi.monitorindex' )
	        ->execute()
	        ->toArray( true );

	    $this->getLogger()->debug( print_r( $this->view->connections, true ) );

        $form->getElement( 'message' )->setValue( $this->view->render( 'customer' . DIRECTORY_SEPARATOR . 'welcomeEmail.tpl' ) );


        // Process a submitted form if it passes initial validation
        if( $this->inexGetPost( 'commit' ) !== null && $form->isValid( $_POST ) )
        {
            $validForm = true;
            // Validate all e-mail addresses
            foreach( array( 'to', 'cc', 'bcc' ) as $recipient )
            {
                if( $form->getValue( $recipient ) != '' )
                {
	                foreach( explode( ',', $form->getElement( $recipient )->getValue() ) as $email )
	                {
	                    if( !Zend_Validate::is( $email, 'EmailAddress' ) )
	                    {
	                        $form->getElement( $recipient )->addError( 'Invalid e-mail address: ' . $email );
	                        $validForm = false;
	                    }
	                }
                }
            }

            if( $validForm )
            {
                $mail = new Zend_Mail();
                $mail->setBodyText( $form->getValue( 'message' ) );
                $mail->setFrom( $this->config['identity']['email'], $this->config['identity']['name'] );
                $mail->setSubject( $form->getValue( 'subject' ) );

                foreach( array( 'To', 'Cc', 'Bcc' ) as $recipient )
                    if( $form->getValue( strtolower( $recipient ) ) != '' )
                        foreach( explode( ',', $form->getElement( strtolower( $recipient ) )->getValue() ) as $email )
	                        if( Zend_Validate::is( $email, 'EmailAddress' ) )
	                        {
	                            $fn = "add$recipient";
                                $mail->$fn( $email );
	                        }

                if( $mail->send() )
                {
                    $this->getLogger()->info( "Welcome email sent for {$customer['name']}" );
                    $this->view->message = new INEX_Message( "Welcome email successfully sent to {$customer['name']}", "success" );
                    $this->_forward( 'dashboard' );
                    return true;
                }
                else
                {
                    $this->getLogger()->err( "Could not sent welcome email for {$customer['name']}: " . print_r( $mail, true ) );
                    $this->view->message = new INEX_Message( "Welcome email could not be sent to {$customer['name']}. Please see logs for more verbose output.", "error" );
                }

            }
        }

        $this->view->form   = $form->render( $this->view );

        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'sendWelcomeEmail.tpl' );
    }


    public function leagueTableAction()
    {
        $metrics = array(
            'Total'   => 'data',
            'Max'     => 'max',
            'Average' => 'average'
        );

        $metric = $this->_request->getParam( 'metric', $metrics['Total'] );
        if( !in_array( $metric, $metrics ) )
            $metric = $metrics['Total'];

        $day = $this->_request->getParam( 'day', date( 'Y-m-d' ) );
        if( !Zend_Date::isDate( $day, 'Y-m-d' ) )
            $day = date( 'Y-m-d' );

        $category = $this->_request->getParam( 'category', INEX_Mrtg::$CATEGORIES['Bits'] );

        if( !in_array( $category, INEX_Mrtg::$CATEGORIES ) )
            $category = INEX_Mrtg::$CATEGORIES['Bits'];

        // load values from the database
        $this->view->trafficDaily = Doctrine_Query::create()
            ->from( 'TrafficDaily td' )
            ->where( 'day = ?', $day )
            ->andWhere( 'category = ?', $category )
            ->execute();

        $this->view->day        = $day;
        $this->view->category   = $category;
        $this->view->categories = INEX_Mrtg::$CATEGORIES;
        $this->view->metric     = $metric;
        $this->view->metrics    = $metrics;
        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'leagueTable.tpl' );
    }

    public function ninetyFifthAction()
    {
        $month = $this->_request->getParam( 'month', date( 'Y-m-01' ) );

        $cost = $this->_request->getParam( 'cost', "20.00" );
        if( !is_numeric( $cost ) )
            $cost = "20.00";
        $this->view->cost = $cost;

        $months = array();
        for( $year = 2010; $year <= date( 'Y' ); $year++ )
            for( $mth = ( $year == 2010 ? 4 : 1 ); $mth <= ( $year == 2010 ? date('n') : 12 ); $mth++ )
            {
                $ts = mktime( 0, 0, 0, $mth, 1, $year );
                $months[date( 'M Y', $ts )] = date( 'Y-m-01', $ts );
            }

        $this->view->months = $months;

        if( in_array( $month, array_values( $months ) ) )
            $this->view->month = $month;
        else
            $this->view->month = date( 'Y-m-01' );

        // load values from the database
        $traffic95thMonthly = Doctrine_Query::create()
            ->from( 'Traffic95thMonthly tf' )
            ->leftJoin( 'tf.Cust c' )
            ->where( 'month = ?', $month )
            ->execute()
            ->toArray();

        foreach( $traffic95thMonthly as $index => $row )
            $traffic95thMonthly[$index]['cost'] = sprintf( "%0.2f", $row['max_95th'] / 1024 / 1024 * $cost );

        $this->view->traffic95thMonthly = $traffic95thMonthly;

        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'ninety-fifth.tpl' );
    }

    public function statisticsOverviewAction()
    {
        $category = $this->_request->getParam( 'category', INEX_Mrtg::$CATEGORIES['Bits'] );

        if( !in_array( $category, INEX_Mrtg::$CATEGORIES ) )
            $category = INEX_Mrtg::$CATEGORIES['Bits'];

        $period = $this->_request->getParam( 'period', INEX_Mrtg::$PERIODS['Day'] );

        if( !in_array( $period, INEX_Mrtg::$PERIODS ) )
            $period = INEX_Mrtg::$PERIODS['Day'];

        $this->view->custs = Doctrine_Query::create()
            ->select( 'c.shortname' )
            ->addSelect( 'c.name' )
            ->from( 'Cust c' )
            ->whereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->andWhere( 'c.status = ?', array( Cust::STATUS_NORMAL ) )
            ->andWhere( 'c.dateleave = 0 or c.dateleave IS NULL' )
            ->andWhereIn( 'c.shortname', array( 'inex', 'routeservers' ), true )
            ->orderBy( 'c.name' )
            ->fetchArray();

        $this->view->category   = $category;
        $this->view->categories = INEX_Mrtg::$CATEGORIES;
        $this->view->period     = $period;
        $this->view->periods    = INEX_Mrtg::$PERIODS;
        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'statistics-overview.tpl' );
    }

    public function statisticsByLanAction()
    {
        $category = $this->_request->getParam( 'category', INEX_Mrtg::$CATEGORIES['Bits'] );

        if( !in_array( $category, INEX_Mrtg::$CATEGORIES ) )
            $category = INEX_Mrtg::$CATEGORIES['Bits'];

        $period = $this->_request->getParam( 'period', INEX_Mrtg::$PERIODS['Day'] );

        if( !in_array( $period, INEX_Mrtg::$PERIODS ) )
            $period = INEX_Mrtg::$PERIODS['Day'];

        $lanTag = $this->_request->getParam( 'lan', 10 );

        $lan = Doctrine_Core::getTable( 'Vlan' )->findOneByNumber( $lanTag );

        if( !$lan )
            $lan = Doctrine_Core::getTable( 'Vlan' )->findOneByNumber( 10 );

        $this->view->lan = $lan;

        $this->view->ints = Doctrine_Query::create()
            ->from( 'Vlaninterface vl' )
            ->leftJoin( 'vl.Virtualinterface vi' )
            ->leftJoin( 'vi.Cust c' )
            ->leftJoin( 'vi.Physicalinterface pi' )
            ->leftJoin( 'pi.Switchport sp' )
            ->leftJoin( 'sp.SwitchTable s' )
            ->whereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->andWhere( 'c.status = ?', array( Cust::STATUS_NORMAL ) )
            ->andWhere( 'c.dateleave = 0 or c.dateleave IS NULL' )
            ->andWhere( 'vl.vlanid = ?', $lan['id'] )
            ->andWhereIn( 'c.shortname', array( 'inex', 'routeservers' ), true )
            ->orderBy( 'c.name' )
            ->fetchArray();

        $this->view->category   = $category;
        $this->view->categories = INEX_Mrtg::$CATEGORIES;
        $this->view->period     = $period;
        $this->view->periods    = INEX_Mrtg::$PERIODS;
        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'statistics-by-lan.tpl' );
    }

    public function statisticsListAction()
    {
        $this->view->custs = Doctrine_Query::create()
            ->select( 'c.shortname' )
            ->addSelect( 'c.name' )
            ->from( 'Cust c' )
            ->whereIn( 'c.type', array( Cust::TYPE_FULL, Cust::TYPE_INTERNAL, Cust::TYPE_PROBONO ) )
            ->andWhere( 'c.status = ?', array( Cust::STATUS_NORMAL ) )
            ->andWhere( 'c.dateleave = 0 or c.dateleave IS NULL' )
            ->andWhereIn( 'c.shortname', array( 'inex', 'routeservers' ), true )
            ->orderBy( 'c.name' )
            ->fetchArray();

        $this->view->display( 'customer' . DIRECTORY_SEPARATOR . 'statistics-list.tpl' );
    }


    /**
     * Set a custom return from an add / edit
     */
    public function _addEditSetReturnOnSuccess( $form, $object )
    {
        return 'customer/dashboard/id/' . $object['id'];
    }

}

