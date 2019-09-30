<?php

namespace IXP\Http\Controllers\Customer;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use App, Auth, Cache, Countries, D2EM, DateTime, Former, Mail, Redirect;

use IXP\Events\Customer\BillingDetailsChanged as CustomerBillingDetailsChangedEvent;

use IXP\Http\Controllers\Controller;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use Entities\{
    BgpSession              as BgpSessionEntity,
    CompanyBillingDetail    as CompanyBillingDetailEntity,
    CompanyRegisteredDetail as CompanyRegisteredDetailEntity,
    Customer                as CustomerEntity,
    CustomerNote            as CustomerNoteEntity,
    CustomerTag             as CustomerTagEntity,
    IRRDBConfig             as IRRDBConfigEntity,
    IXP                     as IXPEntity,
    NetworkInfo             as NetworkInfoEntity,
    RSPrefix                as RSPrefixEntity,
    User                    as UserEntity,
    Vlan                    as VlanEntity
};


use IXP\Mail\Customer\WelcomeEmail;

use IXP\Http\Requests\Customer\{
    Store                   as CustomerRequest,
    BillingInformation      as BillingInformationRequest,
    WelcomeEmail            as WelcomeEmailRequest
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Customer Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Customers
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerController extends Controller
{

    /**
     * Display all the Customers as a list
     *
     * @param Request $r
     * @return  View
     */
    public function list( Request $r ): View
    {

        if( ( $state = $r->input( 'state' ) ) !== null ) {
            if( isset( CustomerEntity::$CUST_STATUS_TEXT[ $state ] ) ) {
                $r->session()->put( "cust-list-state", $state );
            } else {
                $r->session()->remove( "cust-list-state" );
            }
        } else if( $r->session()->exists( "cust-list-state" ) ) {
            $state = $r->session()->get( "cust-list-state" );
        }

        if( ( $type = $r->input( 'type' ) ) !== null ) {
            if( isset( CustomerEntity::$CUST_TYPES_TEXT[ $type ] ) ) {
                $r->session()->put( "cust-list-type", $type );
            } else {
                $r->session()->remove( "cust-list-type" );
            }
        } else if( $r->session()->exists( "cust-list-type" ) ) {
            $type = $r->session()->get( "cust-list-type" );
        }

        if( ( $showCurrentOnly = $r->input( 'current-only' ) ) !== null ) {
            $r->session()->put( "cust-list-current-only", $showCurrentOnly );
        } else if( $r->session()->exists( "cust-list-current-only" ) ) {
            $showCurrentOnly = $r->session()->get( "cust-list-current-only" );
        } else {
            $showCurrentOnly = false;
        }


        if( $r->input( 'tag' )  !== null ) {
            /** @var CustomerTagEntity $s */
            if(  $tag = D2EM::getRepository( CustomerTagEntity::class )->find( $r->input( 'tag' ) ) ) {
                $tid = $tag->getId();
                $r->session()->put( "cust-list-tag", $tid );
            } else {
                $r->session()->remove( "cust-list-tag" );
                $tid = false;
            }
        } else if( $r->session()->exists( "cust-list-tag" ) ) {
            $tid = $r->session()->get( "cust-list-tag" );
        } else {
            $tid = false;
        }



        if( $state || $type || $showCurrentOnly || $tid ){
            $summary = $showCurrentOnly ? ( ":: Current " . ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ) : ( ":: All " . ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ) ;

            if( $state ){
                $summary .= " - State: " . CustomerEntity::$CUST_STATUS_TEXT[ $state ];
            }

            if( $type ){
                $summary .= " - Type: " . CustomerEntity::$CUST_TYPES_TEXT[ $type ];
            }

            if( $tid ){
                $summary .= " - Tag: " . D2EM::getRepository( CustomerTagEntity::class )->getAsArray()[ $tid ];
            }

        } else{
            $summary = false;
        }



        return view( 'customer/list' )->with([
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAllForFeList( $showCurrentOnly, $state, $type, $tid ),
            'state'                 => $state           ?? false,
            'type'                  => $type            ?? false,
            'showCurrentOnly'       => $showCurrentOnly ?? false,
            'tag'                   => $tid ?? false,
            'tags'                  => D2EM::getRepository( CustomerTagEntity::class )->getAsArray(),
            'summary'               => $summary
        ]);
    }

    /**
     * Display the form to add/edit a customer
     *
     * @param Request $request
     * @param int $id The Customer ID
     *
     * @return View
     */
    public function edit( Request $request, int $id = null ): View
    {

        if( $id ) {
            /** @var CustomerEntity $cust */
            if( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            // populate the form with data
            Former::populate([
                'name'                  => $request->old( 'name',                $cust->getName() ),
                'type'                  => $request->old( 'type',                $cust->getType() ),
                'shortname'             => $request->old( 'shortname',           $cust->getShortname() ),
                'corpwww'               => $request->old( 'corpwww',             $cust->getCorpwww() ),
                'datejoin'              => $request->old( 'datejoin',            ( $cust->getDatejoin()    ? $cust->getDatejoin()->format( "Y-m-d" )   : null ) ) ,
                'dateleft'              => $request->old( 'dateleft',            ( $cust->getDateleave()   ? $cust->getDateleave()->format( "Y-m-d" )  : null ) ) ,
                'status'                => $request->old( 'status',              $cust->getStatus() ),
                'md5support'            => $request->old( 'md5support',          $cust->getMD5Support() ),
                'abbreviatedName'       => $request->old( 'abbreviatedName',     $cust->getAbbreviatedName() ),
                'autsys'                => $request->old( 'autsys',              $cust->getAutsys() ),
                'maxprefixes'           => $request->old( 'maxprefixes',         $cust->getMaxprefixes() ),
                'peeringpolicy'         => $request->old( 'peeringpolicy',       $cust->getPeeringpolicy() ),
                'peeringemail'          => $request->old( 'peeringemail',        $cust->getPeeringemail() ),
                'peeringmacro'          => $request->old( 'peeringmacro',        $cust->getPeeringmacro() ),
                'peeringmacrov6'        => $request->old( 'peeringmacrov6',      $cust->getPeeringmacrov6() ),
                'irrdb'                 => $request->old( 'irrdb',               ( $cust->getIRRDB() ? $cust->getIRRDB()->getId() : null ) ) ,
                'activepeeringmatrix'   => $request->old( 'activepeeringmatrix', ( $cust->getActivepeeringmatrix() ? 1 : 0 ) ) ,
                'nocphone'              => $request->old( 'nocphone',            $cust->getNocphone() ),
                'noc24hphone'           => $request->old( 'noc24hphone',         $cust->getNoc24hphone() ),
                'nocemail'              => $request->old( 'nocemail',            $cust->getNocemail() ),
                'nochours'              => $request->old( 'nochours',            $cust->getNochours() ),
                'nocwww'                => $request->old( 'nocwww',              $cust->getNocwww() ),
                'isReseller'            => $request->old( 'isReseller',          ( $cust->getIsReseller() ? 1 : 0 ) ),
                'isResold'              => $request->old( 'isResold',            ( $this->resellerMode() && $cust->getReseller() ? 1 : 0 ) ),
                'reseller'              => $request->old( 'reseller',            ( $this->resellerMode() && $cust->getReseller() ? $cust->getReseller()->getId() : false ) ),
                'peeringdb_oauth'       => $request->old( 'peeringdb_oauth',     $cust->getPeeringdbOAuth() ),
            ]);

        } else {
            // populate the form with default data
            Former::populate([
                'activepeeringmatrix'  => 1,
                'peeringdb_oauth'      => 1,
            ]);

        }


        return view( 'customer/edit' )->with([
            'cust'                          => $cust ?? false,
            'irrdbs'                        => D2EM::getRepository( IRRDBConfigEntity::class )->getAsArray(),
            'resellers'                     => D2EM::getRepository( CustomerEntity::class )->getResellerNames(),
        ]);
    }

    /**
     * Add or edit a customer (set all the data needed)
     *
     * @param   CustomerRequest $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function store( CustomerRequest $r ): RedirectResponse
    {
        $isEdit = $r->input( 'id' ) ? true : false;

        /** @var CustomerEntity $c */
        if( $isEdit && $c = D2EM::getRepository( CustomerEntity::class )->find( $r->input( 'id' ) ) ) {
            if( !$c ) {
                abort(404, ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' not found' );
            }
        } else {
            $c = new CustomerEntity;
            D2EM::persist( $c );
        }


        $c->setName(                 $r->input( 'name'                 ) );
        $c->setType(                 $r->input( 'type'                 ) );
        $c->setShortname(            $r->input( 'shortname'            ) );
        $c->setCorpwww(              $r->input( 'corpwww'              ) );
        $c->setStatus(               $r->input( 'status'               ) );
        $c->setMD5Support(           $r->input( 'md5support'           ) );
        $c->setAbbreviatedName(      $r->input( 'abbreviatedName'      ) );
        $c->setDatejoin(  $r->input( 'datejoin'                  )  ? new \DateTime( $r->input( 'datejoin'    ) ) : null );
        $c->setDateleave($r->input( 'dateleft'                 )  ? new \DateTime( $r->input( 'dateleft'   ) ) : null );

        $c->setAutsys(               $r->input( 'autsys'               ) );
        $c->setMaxprefixes(          $r->input( 'maxprefixes'          ) );
        $c->setPeeringemail(         $r->input( 'peeringemail'         ) );
        $c->setPeeringmacro(         $r->input( 'peeringmacro'         ) );
        $c->setPeeringmacrov6(       $r->input( 'peeringmacrov6'       ) );
        $c->setPeeringpolicy(        $r->input( 'peeringpolicy'        ) );
        $c->setActivepeeringmatrix(  $r->input( 'activepeeringmatrix'  ) ? 1 : 0 );


        $c->setNocphone(             $r->input( 'nocphone'             ) );
        $c->setNoc24hphone(          $r->input( 'noc24hphone'          ) );
        $c->setNocemail(             $r->input( 'nocemail'             ) );
        $c->setNochours(             $r->input( 'nochours'             ) );
        $c->setNocwww(               $r->input( 'nocwww'               ) );

        $c->setPeeringdbOAuth($r->input( 'peeringdb_oauth' ) ? 1 : 0 );

        $c->setIsReseller( $r->input( 'isReseller'           ) ? 1 : 0 );

        if( $r->input( 'isResold' ) ) {
            $c->setReseller( D2EM::getRepository( CustomerEntity::class )->find( $r->input( "reseller" ) ) );
        } else {
            $c->setReseller( null );
        }

        if( $isEdit ) {

            $c->setLastupdated( new DateTime() );
            $c->setLastupdatedby( Auth::getUser()->getId() );

        } else {

            $c->setCreated( new DateTime() );
            $c->setCreator( Auth::getUser()->getId() );

            $bdetail = new CompanyBillingDetailEntity;
            D2EM::persist( $bdetail );
            $bdetail->setPurchaseOrderRequired( 0 );

            $rdetail = new CompanyRegisteredDetailEntity;
            D2EM::persist( $rdetail );

            $c->setBillingDetails( $bdetail );
            $c->setRegistrationDetails( $rdetail );

            $c->addIXP( D2EM::getRepository( IXPEntity::class )->getDefault() );
        }

        if( $r->input( 'irrdb' ) ) {
            $c->setIRRDB( D2EM::getRepository( IRRDBConfigEntity::class)->find( $r->input( 'irrdb' ) ) ) ;
        } else {
            $c->setIRRDB( null );
        }

        D2EM::flush();
        Cache::forget( 'admin_home_customers' );

        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' successfully ' . ( $isEdit ? ' edited.' : ' added.' ), Alert::SUCCESS );

        if( $isEdit ){
            return Redirect::to( route( "customer@overview" , [ "id" => $c->getId() ] ) );
        } else {
            return Redirect::to( route( "customer@billing-registration" , [ "id" => $c->getId() ] ) );
        }

    }


    /**
     * Display the billing registration form a customer
     *
     * @param Request $request
     * @param int $id The Customer ID
     *
     * @return View
     */
    public function editBillingAndRegDetails( Request $request, int $id = null ): View
    {

        $c = false; /** @var CustomerEntity $c */
        if( !$id || !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
            abort( 404, ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' not found' );
        }

        $cbd = $c->getBillingDetails();
        $crd = $c->getRegistrationDetails();

        $dataBillingDetail = [];
        if( !( $this->resellerMode() && $c->isResoldCustomer() ) ){
            $dataBillingDetail = [
                'billingContactName'        => $request->old( 'billingContactName',     $cbd->getBillingContactName() ),
                'billingFrequency'          => $request->old( 'billingFrequency',       $cbd->getBillingFrequency() ),
                'billingAddress1'           => $request->old( 'billingAddress1',        $cbd->getBillingAddress1() ),
                'billingAddress2'           => $request->old( 'billingAddress2',        $cbd->getBillingAddress2() ),
                'billingAddress3'           => $request->old( 'billingAddress3',        $cbd->getBillingAddress3() ),
                'billingTownCity'           => $request->old( 'billingTownCity',        $cbd->getBillingTownCity() ),
                'billingPostcode'           => $request->old( 'billingPostcode',        $cbd->getBillingPostcode() ),
                'billingCountry'            => $request->old( 'billingCountry', in_array( $cbd->getBillingCountry(),  array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ) ? $cbd->getBillingCountry() : null ),
                'billingEmail'              => $request->old( 'billingEmail',           $cbd->getBillingEmail() ),
                'billingTelephone'          => $request->old( 'billingTelephone',       $cbd->getBillingTelephone() ),
                'purchaseOrderRequired'     => $request->old( 'purchaseOrderRequired',  ( $cbd->getPurchaseOrderRequired() ? 1 : 0 ) ),
                'invoiceMethod'             => $request->old( 'invoiceMethod',          $cbd->getInvoiceMethod() ),
                'invoiceEmail'              => $request->old( 'invoiceEmail',           $cbd->getInvoiceEmail() ),
                'vatRate'                   => $request->old( 'vatRate',                $cbd->getVatRate() ),
                'vatNumber'                 => $request->old( 'vatNumber',              $cbd->getVatNumber() ),
            ];
        }

        $dataRegistrationDetail = [
            'registeredName'            => $request->old( 'registeredName',             $crd->getRegisteredName() ),
            'companyNumber'             => $request->old( 'companyNumber',              $crd->getCompanyNumber() ),
            'jurisdiction'              => $request->old( 'jurisdiction',               $crd->getJurisdiction() ),
            'address1'                  => $request->old( 'address1',                   $crd->getAddress1() ),
            'address2'                  => $request->old( 'address2',                   $crd->getAddress2() ),
            'address3'                  => $request->old( 'address3',                   $crd->getAddress3() ) ,
            'townCity'                  => $request->old( 'townCity',                   $crd->getTownCity() ),
            'postcode'                  => $request->old( 'postcode',                   $crd->getPostcode() ),
            'country'                   => $request->old( 'country',            in_array( $crd->getCountry(),  array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ) ? $crd->getCountry() : null ),
        ];


        Former::populate( array_merge( $dataRegistrationDetail, $dataBillingDetail ) );

        return view( 'customer/billing-registration' )->with([
            'c'                             => $c,
            'juridictions'                  => D2EM::getRepository( CompanyRegisteredDetailEntity::class )->getJuridictionsAsArray(),
            'countries'                     => Countries::getList('name' )
        ]);
    }


    /**
     * Add or edit a customer's registration / billing information
     *
     * Also sends an email notification if so configured.
     *
     * @param   BillingInformationRequest $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function storeBillingAndRegDetails( BillingInformationRequest $request ): RedirectResponse 
    {
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404, 'Unknown customer');
        }

        $ocbd = clone $c->getBillingDetails();
        $cbd  = $c->getBillingDetails();
        $crd  = $c->getRegistrationDetails();

        $crd->setRegisteredName(        $request->input( 'registeredName'       ) );
        $crd->setCompanyNumber(         $request->input( 'companyNumber'        ) );
        $crd->setJurisdiction(          $request->input( 'jurisdiction'         ) );
        $crd->setAddress1(              $request->input( 'address1'             ) );
        $crd->setAddress2(              $request->input( 'address2'             ) );
        $crd->setAddress3(              $request->input( 'address3'             ) );
        $crd->setTownCity(              $request->input( 'townCity'             ) );
        $crd->setPostcode(              $request->input( 'postcode'             ) );
        $crd->setCountry(               $request->input( 'country'              ) );

        $cbd->setBillingContactName(     $request->input( 'billingContactName'   ) );
        $cbd->setBillingFrequency(       $request->input( 'billingFrequency'     ) );
        $cbd->setBillingAddress1(        $request->input( 'billingAddress1'      ) );
        $cbd->setBillingAddress2(        $request->input( 'billingAddress2'      ) );
        $cbd->setBillingAddress3(        $request->input( 'billingAddress3'      ) );
        $cbd->setBillingTownCity(        $request->input( 'billingTownCity'      ) );
        $cbd->setBillingPostcode(        $request->input( 'billingPostcode'      ) );
        $cbd->setBillingCountry(         $request->input( 'billingCountry'       ) );
        $cbd->setBillingEmail(           $request->input( 'billingEmail'         ) );
        $cbd->setBillingTelephone(       $request->input( 'billingTelephone'     ) );
        $cbd->setPurchaseOrderRequired(  $request->input( 'purchaseOrderRequired') ? 1 : 0 );
        $cbd->setInvoiceMethod(          $request->input( 'invoiceMethod'        ) );
        $cbd->setInvoiceEmail(           $request->input( 'invoiceEmail'         ) );
        $cbd->setVatRate(                $request->input( 'vatRate'              ) );
        $cbd->setVatNumber(              $request->input( 'vatNumber'            ) );

        D2EM::flush();

        event( new CustomerBillingDetailsChangedEvent( $ocbd, $cbd ) );

        return Redirect::to( route( "customer@overview" , [ "id" => $c->getId() , "tab" => "details" ]  ) );
    }

    /**
     * Display the list of all the Customers
     *
     * @return  View
     */
    public function details(): View
    {
        return view( 'customer/details' )->with([
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getCurrentActive( false, true, false ),
            'associates'            => false,
        ]);
    }

    /**
     * Display the list of all asscociate customers
     *
     * @return  View
     */
    public function associates(): View
    {
        return view( 'customer/details' )->with([
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getCurrentAssociate( false ),
            'associates'            => true,
        ]);
    }

    /**
     * Display all the information for a customer
     *
     * @param int $id ID of the customer
     *
     * @return  View
     */
    public function detail( int $id ): View
    {
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ){
            abort( 404);
        }

        return view( 'customer/detail' )->with([
            'c'       => $c,
            'netinfo' => D2EM::getRepository( NetworkInfoEntity::class )->asVlanProtoArray()
        ]);
    }


    /**
     * Display the customer overview
     *
     * @param   int $id Id of the customer
     * @param   string $tab Tab from the overview selected
     * @return  View
     * @throws
     */
    public function overview( $id = null, $tab = null ) : View
    {

        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
            abort(404, 'Unknown customer');
        }

        $grapher = App::make('IXP\Services\Grapher' );

        // get customer's notes
        $cns = D2EM::getRepository( CustomerNoteEntity::class )->fetchForCustomer( $c );

        $peersStatus = [];

        foreach( $c->getVirtualInterfaces() as $vi ) {
            foreach( $vi->getVlanInterfaces() as $vli ) {
                if( $vli->getVlan()->getPrivate() ) {
                    continue;
                }
                
                $peersStatus[ $vli->getId() ][] = D2EM::getRepository( BgpSessionEntity::class )->getPeersStatus( $vli );
            }
        }

        if( !$tab ){
            $tab = request()->input( "tab", false );
        }

        return view( 'customer/overview' )->with([
            'c'                         => $c,
            'customers'                 => D2EM::getRepository( CustomerEntity::class )->getNames( true ),
            'netInfo'                   => D2EM::getRepository( NetworkInfoEntity::class )->asVlanProtoArray(),
            'isSuperUser'               => Auth::getUser()->isSuperUser(),

            // is this user watching all notes for this customer?
            'coNotifyAll'               => Auth::getUser()->getPreference( "customer-notes.{$c->getId()}.notify" ) ? true : false,

            // what specific notes is this user watching?
            'coNotify'                  => Auth::getUser()->getAssocPreference( "customer-notes.watching" ) ? Auth::getUser()->getAssocPreference( "customer-notes.watching" )[0] : [],

            'rsRoutes'                  => ( config( 'ixp_fe.frontend.disabled.rs-prefixes', false ) && $c->isRouteServerClient() )
                                                ? D2EM::getRepository( RSPrefixEntity::class )->aggregateRouteSummariesForCustomer( $c->getId() ) : false,

            'crossConnects'             => D2EM::getRepository( CustomerEntity::class )->getCrossConnects( $c->getId() ),
            'aggregateGraph'            => $c->isGraphable() ? $grapher->customer( $c ) : false,
            'grapher'                   => $grapher,
            'rsclient'                  => $c->isRouteServerClient(),
            'as112client'               => $c->isAS112Client(),
            'as112UiActive'             => $this->as112UiActive(),
            'countries'                 => Countries::getList('name' ),
            'tab'                       => strtolower( $tab ),
            'notes'                     => $cns,
            'notesInfo'                 => D2EM::getRepository( CustomerNoteEntity::class )->analyseForUser( $cns, $c, Auth::user() ),
            'peers'                     => D2EM::getRepository( CustomerEntity::class )->getPeeringManagerArrayByType( $c , D2EM::getRepository( VlanEntity::class )->getPeeringManagerVLANs(), [ 4, 6 ] ) ?? false,
        ]);
    }

    /**
     * Display the Welcome Email form
     *
     * @param   int $id Id of the customer
     * @return  View
     * @throws \Throwable
     */
    public function welcomeEmail( int $id ) : View{
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ){
            abort( 404);
        }

        $emails = array();
        foreach( $c->getUsers() as $user ){
            /** @var UserEntity $user */
            if( $email = filter_var( $user->getEmail(), FILTER_VALIDATE_EMAIL ) ) {
                $emails[] = $email;
            }
        }

        Former::populate( [
            'to'                        => $c->getNocemail(),
            'cc'                        => implode( ',', $emails ),
            'bcc'                       => config('identity.email'),
            'subject'                   => config('identity.name'). ' :: Welcome Mail',
        ] );

        return view( 'customer/welcome-email' )->with([
            'c'         => $c,
            'body'      => view( "customer/emails/welcome-email" )->with([
                'c'                     => $c,
                'admins'                => $c->getAdminUsers(),
                'netinfo'               => D2EM::getRepository( NetworkInfoEntity::class )->asVlanProtoArray(),
                'identityEmail'         => config('identity.email'),
                'identityOrgname'       => config('identity.orgname'),
            ])->render()
        ]);

    }

    /**
     * Send the welcome email to a customer
     *
     * @param WelcomeEmailRequest $r
     * @return RedirectResponse|View
     */
    public function sendWelcomeEmail( WelcomeEmailRequest $r )
    {
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $r->input( 'id' ) ) ) ){
            abort( 404);
        }

        $mailable = new WelcomeEmail( $c, $r );

        try {
            $mailable->checkIfSendable();
        } catch( \Exception $e ) {
            AlertContainer::push( $e->getMessage(), Alert::DANGER );
            return back()->withInput();
        }

        Mail::send( $mailable );

        AlertContainer::push( "Welcome email sent.", Alert::SUCCESS );

        return Redirect::to( route( "customer@overview", [ "id" => $c->getId() ] ) );

    }

    /**
     * Recap the information that will be deleted with the customer
     *
     * @param   int   $id Id of the customer
     * @return  View|RedirectResponse
     */
    public function deleteRecap( int $id )
    {
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ){
            abort( 404);
        }

        // cannot delete a customer with active cross connects:
        if( count( $c->getPatchPanelPorts() ) ) {
            AlertContainer::push( "This customer has active patch panel ports. Please cease "
                . "these (or set them to awaiting cease and unset the customer link in the patch panel "
                . "port) to proceed with deleting this customer.", Alert::DANGER
            );
            return Redirect::to( route( "customer@overview", $c->getId() ) );
        }

        // cannot delete a customer with fan out ports:
        if( $c->isResoldCustomer() ) {
            foreach( $c->getVirtualInterfaces() as $vi ) {
                foreach( $vi->getPhysicalInterfaces() as $pi ) {
                    if( $pi->getFanoutPhysicalInterface() ) {
                        AlertContainer::push( "This customer has is a resold customer with fan out physical "
                            . "interfaces. Please delete these manually before proceeding with deleting the customer.",
                            Alert::DANGER
                        );
                        return Redirect::to( route( "customer@overview", $c->getId() ) );
                    }
                }
            }
        }


        return view( 'customer/delete' )->with([
            'c'         => $c,
        ]);
    }

    /**
     * Delete a customer and everything related !!
     *
     * @param   Request $request Instance of HTTP request
     * @return  RedirectResponse
     * @throws \Exception
     */
    public function delete( Request $request) : RedirectResponse
    {
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $request->input( "id" ) ) ) ) {
            abort( 404);
        }

        // Keep the name before delete
        $oldname = $c->getFormattedName();

        if( D2EM::getRepository( CustomerEntity::class )->delete( $c ) ) {
            AlertContainer::push( "Customer <em>{$oldname}</em> deleted.", Alert::SUCCESS );
        } else {
            AlertContainer::push( "Error: customer could not be deleted. Please open a GitHub bug report.", Alert::DANGER );
        }

        Cache::forget( 'admin_home_customers' );
        return Redirect::to( route( "customer@list" ) );
    }


    /**
     * Display the form to add/remove customer tags
     *
     * @param int $id    The Customer ID
     *
     * @return View
     */
    public function tags( int $id = null ): View
    {
        /** @var CustomerEntity $cust */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
            abort(404, "Unknown Customer");
        }

        return view( 'customer/tags' )->with([
            'c'             => $c,
            'tags'          => D2EM::getRepository( CustomerTagEntity::class )->findAll( [], [ "display_as" => "ASC"] ),
            'selectedTags'  => D2EM::getRepository( CustomerTagEntity::class )->getAllForCustomer( $c->getId() ),
        ]);
    }

    /**
     * Add or edit a customer (set all the data needed)
     *
     * @param   Request $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function storeTags( Request $r ): RedirectResponse
    {

        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $r->input( "id" ) ) ) ) {
            abort(404, "Unknown Customer");
        }

        foreach( D2EM::getRepository( CustomerTagEntity::class )->findAll() as $customerTag ){
            /** @var CustomerTagEntity $customerTag */
            if( $r->input( "tag-" . $customerTag->getId() ) ){
                // check if the link between the customer and the tag already exist
                if( !array_key_exists( $customerTag->getId(), D2EM::getRepository( CustomerTagEntity::class)->getAllForCustomer( $c->getId() ) ) ){
                    $customerTag->addCustomer( $c );
                }
            } else{
                $customerTag->removeCustomer( $c );
            }
        }

        D2EM::flush();

        AlertContainer::push( "The Tags have been updated.", Alert::SUCCESS );

        return Redirect::to( route( "customer@overview" , [ "id" => $c->getId() ] ) );
    }

}

