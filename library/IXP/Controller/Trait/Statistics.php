<?php

/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * A trait of common statistics functions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller_Trait
 * @copyright  Copyright (c) 2009 - 2013, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
trait IXP_Controller_Trait_Statistics
{
    /**
     * The selected / default IXP. Available in the view as `$ixp`. Set in `setIXP()`.
     * @var \Entities\IXP The IXP / default IXP
     */
    private $ixp = null;

    /**
     * All available IXPs. Available in the view as `$ixps`. Set in `setIXP()`.
     * @var \Entities\IXP[] All available IXPs
     */
    private $ixps = null;


    /**
     * Set the IXP based on submitted parameters
     *
     * @param \Entities\Customer $cust Limit list of IXPs to this customer and ensure this customer is part of the selected IXP
     */
    protected function setIXP( $cust = false )
    {
        if( $this->multiIXP() )
        {
            // need to handle this two ways - one for customers and one for admins
            if( $cust )
            {
                $this->ixps = $this->view->ixps = $this->getD2R( "\\Entities\\IXP" )->getForCustomer( $cust );

                if( $this->getParam( 'ixp', false ) )
                    $this->view->ixp = $this->ixp = $this->loadIxpById( $this->getParam( 'ixp' ) );
                else
                    $this->view->ixp = $this->ixp = $cust->getIXPs()[0];

                $valid = false;
                foreach( $cust->getIXPs() as $i )
                {
                    if( $this->ixp->getId() == $i->getId() )
                    {
                        $valid = true;
                        break;
                    }
                }

                if( !$valid )
                {
                    $this->getLogger()->alert( "{$this->getUser()->getUsername()} tried to access an invalid IXP" );
                    $this->addMessage( "Invalid IXP for you :(", OSS_Message::ERROR );
                    $this->redirectAndEnsureDie('');
                }
            }
            else
            {
                $this->ixps = $this->view->ixps = $this->getD2R( "\\Entities\\IXP" )->findAll();

                if( $this->getParam( 'ixp', false ) )
                    $this->view->ixp = $this->ixp = $this->loadIxpById( $this->getParam( 'ixp' ) );
                else if( $this->ixps )
                    $this->view->ixp = $this->ixp = $this->ixps[0];
                else
                    throw new IXP_Exception( "No IXPs defined!" );
            }
        }
        else
        {
            // in non-multiIXP environments, there is only one IXP and it has ID 1
            $this->ixp = $this->view->ixp = $this->loadIxpById( 1 );
        }
    }


    /**
     * The selected infrastructure (or `aggregate`). Set in `setInfrastructure()` and available in the view as `$infra`.
     * @var \Entities\Infrastructure The selected infrastructure (or `aggregate`).
     */
    private $infra = null;

    /**
     * The selected infrastructure ID or `aggregate`. Set in `setInfrastructure()` and available in the view as `$infraid`.
     *
     * Useful as the infrastructure is either an object or 'aggregate' - using this variable allows direct use without
     * bounding if's.
     *
     * @var string|int The selected infrastructure ID or `aggregate`.
     */
    private $infraid = null;


    /**
     * Set the IXP based on submitted parameters
     */
    protected function setInfrastructure( $defaultToAggregate = true )
    {
        $this->view->infra = $this->view->infraid = $this->infra = $this->infraid
            = $this->getParam( 'infra', ( $defaultToAggregate ? 'aggregate' : false ) );

        if( $this->infra != "aggregate" )
        {
            foreach( $this->ixp->getInfrastructures() as $inf )
            {
                if( $inf->getId() == $this->infra )
                {
                    $this->view->infra   = $this->infra   = $inf;
                    $this->view->infraid = $this->infraid = $inf->getId();
                    break;
                }
            }

            if( !( $this->infra instanceof \Entities\Infrastructure ) )
            {
                $this->view->infra   = $this->infra   = $this->ixp->getInfrastructures()[0];
                $this->view->infraid = $this->infraid = $this->infra->getId();
            }
        }
    }


    /**
     * The selected VLAN. Set in `setVLAN()` and available in the view as `$vlan`.
     * @var \Entities\VLAN The selected VLAN.
     */
    private $vlan = null;


    /**
     * Set the VLAN based on submitted parameters
     */
    protected function setVLAN()
    {
        foreach( $this->infra->getVlans() as $v )
        {
            if( !$this->vlan && !$v->getPrivate() )
            $this->view->vlan = $this->vlan = $v;

            if( $v->getId() == $this->getParam( 'vlan', false ) )
            {
                $this->view->vlan = $this->vlan = $v;
                break;
            }
        }
    }


    /**
     * Utility function to extract, validate (and default if necessary) a
     * protocol from request parameters.
     *
     * Sets the view variables `$proto` to the chosen / defaulted protocol
     * and `$protocols` to all available protocols.
     *
     * @param string $pname The name of the parameter to extract the protocol from
     * @return string The chosen / defaulted protocol
     */
    protected function setProtocol( $pname = 'proto' )
    {
        $proto = $this->getParam( $pname, 4 );
        if( !in_array( $proto, IXP_Mrtg::$PROTOCOLS ) )
            $proto = IXP_Mrtg::PROTOCOL_IPV4;

        $this->view->proto     = $proto;
        $this->view->protocols = IXP_Mrtg::$PROTOCOLS;

        return $proto;
    }


    /**
     * Utility function to extract, validate (and default if necessary) a
     * category from request parameters.
     *
     * Sets the view variables `$category` to the chosen / defaulted category
     * and `$categories` to all available categories.
     *
     * @param string $pname The name of the parameter to extract the category from
     * @param bool $aggregate Use aggregate categories only (i.e. bits, pkts, no errs, no discs)
     * @return string The chosen / defaulted category
     */
    protected function setCategory( $pname = 'category', $aggregate = false )
    {
        $category = $this->getParam( $pname, IXP_Mrtg::$CATEGORIES['Bits'] );
        if( !in_array( $category, $aggregate ? IXP_Mrtg::$CATEGORIES_AGGREGATE : IXP_Mrtg::$CATEGORIES ) )
            $category = IXP_Mrtg::$CATEGORIES['Bits'];
        $this->view->category   = $category;
        $this->view->categories = $aggregate ? IXP_Mrtg::$CATEGORIES_AGGREGATE : IXP_Mrtg::$CATEGORIES;
        return $category;
    }

    /**
     * Utility function to extract, validate (and default if necessary) a
     * period from request parameters.
     *
     * Sets the view variables `$period` to the chosen / defaulted category
     * and `$periods` to all available periods.
     *
     * @param string $pname The name of the parameter to extract the period from
     * @return string The chosen / defaulted period
     */
    protected function setPeriod( $pname = 'period' )
    {
        $period = $this->getParam( $pname, IXP_Mrtg::$PERIODS['Day'] );
        if( !in_array( $period, IXP_Mrtg::$PERIODS ) )
            $period = IXP_Mrtg::$PERIODS['Day'];
        $this->view->period     = $period;
        $this->view->periods    = IXP_Mrtg::$PERIODS;
        return $period;
    }

}
