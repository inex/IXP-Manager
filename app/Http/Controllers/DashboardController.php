<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use App, Auth, Countries, Redirect;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use IXP\Events\Customer\BillingDetailsChanged as CustomerBillingDetailsChangedEvent;

use IXP\Http\Requests\Dashboard\{
    NocDetailsRequest,
    BillingDetailsRequest
};

use IXP\Models\{
    Aggregators\RsPrefixAggregator,
    Customer,
    CustomerNote,
    NetworkInfo
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};
use IXP\Services\Grapher;

/**
 * DashboardController Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DashboardController extends Controller
{
    /**
     * Display dashboard
     *
     * @param Request     $r
     * @param string|null $tab Tab from the overview selected
     *
     * @return  RedirectResponse|View
     *
     * @throws
     */
    public function index( Request $r, string $tab = null ): RedirectResponse|View
    {
        // Redirect Super user
        if( Auth::getUser()->isSuperUser() ) {
            return redirect( '/');
        }

        $c          = Auth::getUser()->customer;
        $grapher    = null;
        $cns        = $c->customerNotes()->publicOnly()->get();
        $cbd        = $c->companyBillingDetail;

        if( !$c->typeAssociate() ) {
            $resoldCustomer     = $c->reseller;
            $netinfo            = NetworkInfo::vlanProtocol();
            $grapher            = App::make( Grapher::class );
        }

        // array used to populate the details forms
        // former doesn't allow us to populate a form the classic way when there is >1 forms on the same view.
        $dataNocDetail = [
            'nocphone'                  => $r->old( 'nocphone',                $c->nocphone         ),
            'noc24hphone'               => $r->old( 'noc24hphone',             $c->noc24hphone      ),
            'nocemail'                  => $r->old( 'nocemail',                $c->nocemail         ),
            'nochours'                  => $r->old( 'nochours',                $c->nochours         ),
            'nocwww'                    => $r->old( 'nocwww',                  $c->nocwww           ),
        ];

        $dataBillingDetail = [
            'billingContactName'        => $r->old( 'billingContactName',      $cbd->billingContactName ),
            'billingAddress1'           => $r->old( 'billingAddress1',         $cbd->billingAddress1    ),
            'billingAddress2'           => $r->old( 'billingAddress2',         $cbd->billingAddress2    ),
            'billingAddress3'           => $r->old( 'billingAddress3',         $cbd->billingAddress3    ),
            'billingTownCity'           => $r->old( 'billingTownCity',         $cbd->billingTownCity    ),
            'billingPostcode'           => $r->old( 'billingPostcode',         $cbd->billingPostcode    ),
            'billingCountry'            => $r->old( 'billingCountry',          in_array( $cbd->billingCountry,  array_values( Countries::getListForSelect( 'iso_3166_2' ) ), false ) ? $cbd->billingCountry : null ),
            'billingEmail'              => $r->old( 'billingEmail',            $cbd->billingEmail       ),
            'billingTelephone'          => $r->old( 'billingTelephone',        $cbd->billingTelephone   ),
            'invoiceEmail'              => $r->old( 'invoiceEmail',            $cbd->invoiceEmail       ),
        ];

        return view( 'dashboard/index' )->with([
            'recentMembers'                 => Customer::getConnected( true, true, 'datejoin', 'desc' )->take( 5 ),
            'crossConnects'                 => $c->patchPanelPorts()->masterPort()->get(),
            'notesInfo'                     => CustomerNote::analyseForUser( $cns, $c, Auth::getUser() ),
            'rsRoutes'                      => $rsRoutes        ?? null,
            'resoldCustomer'                => $resoldCustomer  ?? null,
            'netInfo'                       => $netinfo         ?? null,
            'c'                             => $c->load( [
                'logo', 'virtualInterfaces.vlanInterfaces.vlan', 'resoldCustomers',
                'logo', 'virtualInterfaces.vlanInterfaces.layer2Addresses',
                'logo', 'virtualInterfaces.vlanInterfaces.ipv6address',
                'logo', 'virtualInterfaces.vlanInterfaces.ipv4address',
                'virtualInterfaces.physicalInterfaces.switchPort.switcher.infrastructureModel',
                'virtualInterfaces.physicalInterfaces.switchPort.switcher.cabinet.location',
                'virtualInterfaces.physicalInterfaces.switchPort.patchPanelPort.patchPanel',
            ] ),
            'notes'                         => $cns,
            'grapher'                       => $grapher,
            'dataBillingDetail'             => $dataBillingDetail,
            'dataNocDetail'                 => $dataNocDetail,
            'countries'                     => Countries::getList('name' ),
            'tab'                           => strtolower( $tab ) ?: false,
        ]);
    }

    /**
     * Edit NOC details of a customer via the dashboard
     *
     * @param   NocDetailsRequest $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function storeNocDetails( NocDetailsRequest $r ): RedirectResponse
    {
        $c = Auth::getUser()->customer;
        $c->nocphone        =   $r->nocphone;
        $c->noc24hphone     =   $r->noc24hphone;
        $c->nocemail        =   $r->nocemail;
        $c->nochours        =   $r->nochours;
        $c->nocwww          =   $r->nocwww;
        $c->lastupdatedby   =   Auth::id();
        $c->save();

        AlertContainer::push( 'NOC details updated', Alert::SUCCESS );
        return redirect( route( "dashboard@index", [ "tab" => "details" ] ) );
    }

    /**
     * Edit Billing details of a customer via the dashboard
     *
     *
     * @param   BillingDetailsRequest $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function storeBillingDetails( BillingDetailsRequest $r ): RedirectResponse
    {
        $c      = Auth::getUser()->customer;
        $cbd    = $c->companyBillingDetail;
        $ocbd   = clone $c->companyBillingDetail;

        $cbd->billingContactName    =   $r->billingContactName;
        $cbd->billingAddress1       =   $r->billingAddress1;
        $cbd->billingAddress2       =   $r->billingAddress2;
        $cbd->billingAddress3       =   $r->billingAddress3;
        $cbd->billingTownCity       =   $r->billingTownCity;
        $cbd->billingPostcode       =   $r->billingPostcode;
        $cbd->billingCountry        =   $r->billingCountry;
        $cbd->billingEmail          =   $r->billingEmail;
        $cbd->billingTelephone      =   $r->billingTelephone;
        $cbd->invoiceEmail          =   $r->invoiceEmail;
        $cbd->save();

        event( new CustomerBillingDetailsChangedEvent( $ocbd, $cbd ) );

        AlertContainer::push( 'Billing details updated.', Alert::SUCCESS );
        return Redirect::to( route( "dashboard@index", [ "tab" => "details" ] ) );
    }
}