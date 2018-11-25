<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Entities\{
    Customer                    as CustomerEntity,
    CustomerNote                as CustomerNoteEntity,
    NetworkInfo                 as NetworkInfoEntity,
    RSPrefix                    as RSPrefixEntity,
    User                        as UserEntity
};

use Illuminate\Http\{
    RedirectResponse
};

use IXP\Events\Customer\BillingDetailsChanged as CustomerBillingDetailsChangedEvent;

use Illuminate\View\View;

use IXP\Http\Requests\Dashboard\{
    NocDetailsRequest,
    BillingDetailsRequest
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use App, Auth, Countries, D2EM, DateTime, Redirect;

/**
 * DashboardController Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DashboardController extends Controller
{

    /**
     * Display dashboard
     *
     * @param   string $tab Tab from the overview selected
     *
     * @return  View|RedirectResponse
     *
     * @throws
     */
    public function index( string $tab = null ) {

        if( Auth::getUser()->getPrivs() != UserEntity::AUTH_CUSTUSER ){
            return Redirect::to( '' );
        }

        $c = Auth::getUser()->getCustomer();
        $grapher = null;

        if( !$c->isTypeAssociate() ) {

            $resoldCustomer     = $c->isResoldCustomer();
            $netinfo            = D2EM::getRepository( NetworkInfoEntity::class )->asVlanProtoArray();
            $grapher            = $grapher = App::make('IXP\Services\Grapher');


            if( $c->isRouteServerClient() ) {
                $rsRoutes = D2EM::getRepository( RSPrefixEntity::class )->aggregateRouteSummariesForCustomer( $c->getId() );
            }
        }

        $cns = D2EM::getRepository( CustomerNoteEntity::class )->fetchForCustomer( $c, true );
        $cbd = $c->getBillingDetails();

        $old = request()->old();

        // array used to populate the details forms
        // former doesn't allow us to populate a form the classic way when there is >1 forms on the same view.
        $dataNocDetail = [
            'nocphone'                  => array_key_exists( 'nocphone',                $old    ) ? $old['nocphone']                : $c->getNocphone(),
            'noc24hphone'               => array_key_exists( 'noc24hphone',             $old    ) ? $old['noc24hphone']             : $c->getNoc24hphone(),
            'nocemail'                  => array_key_exists( 'nocemail',                $old    ) ? $old['nocemail']                : $c->getNocemail(),
            'nochours'                  => array_key_exists( 'nochours',                $old    ) ? $old['nochours']                : $c->getNoc24hphone(),
            'nocwww'                    => array_key_exists( 'nocwww',                  $old    ) ? $old['nocwww']                  : $c->getNocwww(),
        ];

        $dataBillingDetail = [
            'billingContactName'        => array_key_exists( 'billingContactName',      $old    ) ? $old['billingContactName']      : $cbd->getBillingContactName(),
            'billingAddress1'           => array_key_exists( 'billingAddress1',         $old    ) ? $old['billingAddress1']         : $cbd->getBillingAddress1(),
            'billingAddress2'           => array_key_exists( 'billingAddress2',         $old    ) ? $old['billingAddress2']         : $cbd->getBillingAddress2(),
            'billingAddress3'           => array_key_exists( 'billingAddress3',         $old    ) ? $old['billingAddress3']         : $cbd->getBillingAddress3(),
            'billingTownCity'           => array_key_exists( 'billingTownCity',         $old    ) ? $old['billingTownCity']         : $cbd->getBillingTownCity(),
            'billingPostcode'           => array_key_exists( 'billingPostcode',         $old    ) ? $old['billingPostcode']         : $cbd->getBillingPostcode(),
            'billingCountry'            => array_key_exists( 'billingCountry',          $old    ) ? $old['billingCountry']          : in_array( $cbd->getBillingCountry(),  array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ) ? $cbd->getBillingCountry() : null,
            'billingEmail'              => array_key_exists( 'billingEmail',            $old    ) ? $old['billingEmail']            : $cbd->getBillingEmail(),
            'billingTelephone'          => array_key_exists( 'billingTelephone',        $old    ) ? $old['billingTelephone']        : $cbd->getBillingTelephone(),
            'invoiceEmail'              => array_key_exists( 'invoiceEmail',            $old    ) ? $old['invoiceEmail']            : $cbd->getInvoiceEmail(),
        ];

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'dashboard/index' )->with([
            'recentMembers'                 => array_slice( D2EM::getRepository( CustomerEntity::class )->getRecent(), 0 , 5 ),
            'crossConnects'                 => D2EM::getRepository( CustomerEntity::class       )->getCrossConnects(    $c->getId()             ),
            'notesInfo'                     => D2EM::getRepository( CustomerNoteEntity::class   )->analyseForUser(      $cns, $c, Auth::user()  ),
            'rsRoutes'                      => $rsRoutes        ?? null,
            'resoldCustomer'                => $resoldCustomer  ?? null,
            'netinfo'                       => $netinfo         ?? null,
            'c'                             => $c,
            'notes'                         => $cns,
            'grapher'                       => $grapher,
            'dataBillingDetail'             => $dataBillingDetail,
            'dataNocDetail'                 => $dataNocDetail,
            'countries'                     => Countries::getList('name' ),
            'tab'                           => strtolower( $tab )
        ]);
    }


    /**
     * Edit NOC details of a customer via the dashboard
     *
     * @param   NocDetailsRequest $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function storeNocDetails( NocDetailsRequest $r ){

        /** @var CustomerEntity $c */
        $c = Auth::getUser()->getCustomer();

        $c->setNocphone(        $r->input( 'nocphone'       ) );
        $c->setNoc24hphone(     $r->input( 'noc24hphone'    ) );
        $c->setNocemail(        $r->input( 'nocemail'       ) );
        $c->setNochours(        $r->input( 'nochours'       ) );
        $c->setNocwww(          $r->input( 'nocwww'         ) );
        $c->setLastupdated(     new DateTime() );
        $c->setLastupdatedby(   Auth::getUser()->getId() );

        D2EM::flush();

        AlertContainer::push( 'Your NOC details have been updated', Alert::SUCCESS );

        return Redirect::to( route( "dashboard@index", [ "tab" => "details" ] ) );
    }

    /**
     * Edit Billing details of a customer via the dashboard
     *
     *
     * @param   BillingDetailsRequest $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function storeBillingDetails( BillingDetailsRequest $r ){

        /** @var CustomerEntity $c */
        $c = Auth::getUser()->getCustomer();

        $cbd  = $c->getBillingDetails();
        $ocbd = clone $c->getBillingDetails();

        $cbd->setBillingContactName(     $r->input( 'billingContactName'   ) );
        $cbd->setBillingAddress1(        $r->input( 'billingAddress1'      ) );
        $cbd->setBillingAddress2(        $r->input( 'billingAddress2'      ) );
        $cbd->setBillingAddress3(        $r->input( 'billingAddress3'      ) );
        $cbd->setBillingTownCity(        $r->input( 'billingTownCity'      ) );
        $cbd->setBillingPostcode(        $r->input( 'billingPostcode'      ) );
        $cbd->setBillingCountry(         $r->input( 'billingCountry'       ) );
        $cbd->setBillingEmail(           $r->input( 'billingEmail'         ) );
        $cbd->setBillingTelephone(       $r->input( 'billingTelephone'     ) );
        $cbd->setInvoiceEmail(           $r->input( 'invoiceEmail'         ) );

        D2EM::flush();

        event( new CustomerBillingDetailsChangedEvent( $ocbd, $cbd ) );

        AlertContainer::push( 'Your billing details have been updated', Alert::SUCCESS );

        return Redirect::to( route( "dashboard@index", [ "tab" => "details" ] ) );

    }
}
