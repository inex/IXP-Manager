<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use App, Auth, Countries, D2EM, DateTime, Redirect;

use Entities\{
    Customer                    as CustomerEntity,
    CustomerNote                as CustomerNoteEntity,
    NetworkInfo                 as NetworkInfoEntity,
    PatchPanelPort              as PatchPanelPortEntity,
    RSPrefix                    as RSPrefixEntity
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Events\Customer\BillingDetailsChanged as CustomerBillingDetailsChangedEvent;

use IXP\Http\Requests\Dashboard\{
    NocDetailsRequest,
    BillingDetailsRequest
};

use IXP\Models\{
    Customer,
    DocstoreCustomerFile
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * DashboardController Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DashboardController extends Controller
{

    /**
     * Display dashboard
     *
     * @param Request $request
     * @param string $tab Tab from the overview selected
     *
     * @return  View|RedirectResponse
     *
     */
    public function index( Request $request, string $tab = null )
    {

        // Redirect Super user
        if( Auth::getUser()->isSuperUser() ){
            return Redirect::to( '/');
        }

        $c = Auth::getUser()->customer;
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

        // array used to populate the details forms
        // former doesn't allow us to populate a form the classic way when there is >1 forms on the same view.
        $dataNocDetail = [
            'nocphone'                  => $request->old( 'nocphone',                $c->getNocphone() ),
            'noc24hphone'               => $request->old( 'noc24hphone',             $c->getNoc24hphone() ),
            'nocemail'                  => $request->old( 'nocemail',                $c->getNocemail() ),
            'nochours'                  => $request->old( 'nochours',                $c->getNochours() ),
            'nocwww'                    => $request->old( 'nocwww',                  $c->getNocwww() ),
        ];

        $dataBillingDetail = [
            'billingContactName'        => $request->old( 'billingContactName',      $cbd->getBillingContactName() ),
            'billingAddress1'           => $request->old( 'billingAddress1',         $cbd->getBillingAddress1() ),
            'billingAddress2'           => $request->old( 'billingAddress2',         $cbd->getBillingAddress2() ),
            'billingAddress3'           => $request->old( 'billingAddress3',         $cbd->getBillingAddress3() ),
            'billingTownCity'           => $request->old( 'billingTownCity',         $cbd->getBillingTownCity() ),
            'billingPostcode'           => $request->old( 'billingPostcode',         $cbd->getBillingPostcode() ),
            'billingCountry'            => $request->old( 'billingCountry',          in_array( $cbd->getBillingCountry(),  array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ) ? $cbd->getBillingCountry() : null ),
            'billingEmail'              => $request->old( 'billingEmail',            $cbd->getBillingEmail() ),
            'billingTelephone'          => $request->old( 'billingTelephone',        $cbd->getBillingTelephone() ),
            'invoiceEmail'              => $request->old( 'invoiceEmail',            $cbd->getInvoiceEmail() ),
        ];

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'dashboard/index' )->with([
            'recentMembers'                 => array_slice( D2EM::getRepository( CustomerEntity::class )->getRecent(), 0 , 5 ),
            'crossConnects'                 => D2EM::getRepository( PatchPanelPortEntity::class       )->getForCustomer(    $c->getId()             ),
            'notesInfo'                     => D2EM::getRepository( CustomerNoteEntity::class   )->analyseForUser(      $cns, $c, Auth::getUser()  ),
            'rsRoutes'                      => $rsRoutes        ?? null,
            'resoldCustomer'                => $resoldCustomer  ?? null,
            'netInfo'                       => $netinfo         ?? null,
            'c'                             => $c,
            'notes'                         => $cns,
            'grapher'                       => $grapher,
            'dataBillingDetail'             => $dataBillingDetail,
            'dataNocDetail'                 => $dataNocDetail,
            'countries'                     => Countries::getList('name' ),
            'tab'                           => strtolower( $tab ),
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
    public function storeNocDetails( NocDetailsRequest $r )
    {
        if( Auth::getUser()->isCustUser() ){
            abort( 403, 'Insufficient Permissions.' );
        }

        $c = Auth::getUser()->customer;

        $c->setNocphone(        $r->input( 'nocphone'       ) );
        $c->setNoc24hphone(     $r->input( 'noc24hphone'    ) );
        $c->setNocemail(        $r->input( 'nocemail'       ) );
        $c->setNochours(        $r->input( 'nochours'       ) );
        $c->setNocwww(          $r->input( 'nocwww'         ) );
        $c->setLastupdated(     new DateTime() );
        $c->setLastupdatedby(   Auth::id() );

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

        if( Auth::getUser()->isCustUser() ){
            abort( 403, 'Insufficient Permissions.' );
        }

        /** @var CustomerEntity $c */
        $c = Auth::getUser()->customer;

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
