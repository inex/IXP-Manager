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
    public function list( Request $r ): View {

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
            $summary = $showCurrentOnly ? ":: Current Customers" : ":: All Customers" ;

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
     * @param int $id    The Customer ID
     *
     * @return View
     */
    public function edit( int $id = null ): View {

        if( $id ) {
            /** @var CustomerEntity $cust */
            if( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            $old = request()->old();

            // populate the form with data
            Former::populate([
                'name'                  => array_key_exists( 'name',                $old    ) ? $old['name']                    : $cust->getName(),
                'type'                  => array_key_exists( 'type',                $old    ) ? $old['type']                    : $cust->getType(),
                'shortname'             => array_key_exists( 'shortname',           $old    ) ? $old['shortname']               : $cust->getShortname(),
                'corpwww'               => array_key_exists( 'corpwww',             $old    ) ? $old['corpwww']                 : $cust->getCorpwww(),
                'datejoin'              => array_key_exists( 'datejoin',            $old    ) ? $old['datejoin']                : ( $cust->getDatejoin()    ? $cust->getDatejoin()->format( "Y-m-d" )   : null ) ,
                'dateleft'              => array_key_exists( 'dateleft',            $old    ) ? $old['dateleft']                : ( $cust->getDateleave()   ? $cust->getDateleave()->format( "Y-m-d" )  : null ) ,
                'status'                => array_key_exists( 'status',              $old    ) ? $old['status']                  : $cust->getStatus(),
                'md5support'            => array_key_exists( 'md5support',          $old    ) ? $old['md5support']              : $cust->getMD5Support(),
                'abbreviatedName'       => array_key_exists( 'abbreviatedName',     $old    ) ? $old['abbreviatedName']         : $cust->getAbbreviatedName(),
                'autsys'                => array_key_exists( 'autsys',              $old    ) ? $old['autsys']                  : $cust->getAutsys(),
                'maxprefixes'           => array_key_exists( 'maxprefixes',         $old    ) ? $old['maxprefixes']             : $cust->getMaxprefixes(),
                'peeringpolicy'         => array_key_exists( 'peeringpolicy',       $old    ) ? $old['peeringpolicy']           : $cust->getPeeringpolicy(),
                'peeringemail'          => array_key_exists( 'peeringemail',        $old    ) ? $old['peeringemail']            : $cust->getPeeringemail(),
                'peeringmacro'          => array_key_exists( 'peeringmacro',        $old    ) ? $old['peeringmacro']            : $cust->getPeeringmacro(),
                'peeringmacrov6'        => array_key_exists( 'peeringmacrov6',      $old    ) ? $old['peeringmacrov6']          : $cust->getPeeringmacrov6(),
                'irrdb'                 => array_key_exists( 'irrdb',               $old    ) ? $old['irrdb']                   : ( $cust->getIRRDB() ? $cust->getIRRDB()->getId() : null ) ,
                'activepeeringmatrix'   => array_key_exists( 'activepeeringmatrix', $old    ) ? $old['activepeeringmatrix']     : ( $cust->getActivepeeringmatrix() ? 1 : 0 ) ,
                'nocphone'              => array_key_exists( 'nocphone',            $old    ) ? $old['nocphone']                : $cust->getNocphone(),
                'noc24hphone'           => array_key_exists( 'noc24hphone',         $old    ) ? $old['noc24hphone']             : $cust->getNoc24hphone(),
                'nocemail'              => array_key_exists( 'nocemail',            $old    ) ? $old['nocemail']                : $cust->getNocemail(),
                'nochours'              => array_key_exists( 'nochours',            $old    ) ? $old['nochours']                : $cust->getNochours(),
                'nocwww'                => array_key_exists( 'nocwww',              $old    ) ? $old['nocwww']                  : $cust->getNocwww(),
                'isReseller'            => array_key_exists( 'isReseller',          $old    ) ? $old['isReseller']              : ( $cust->getIsReseller() ? 1 : 0 ),
                'isResold'              => array_key_exists( 'isResold',            $old    ) ? $old['isResold']                : ( $this->resellerMode() && $cust->getReseller() ? 1 : 0 ),
                'reseller'              => array_key_exists( 'reseller',            $old    ) ? $old['reseller']                : ( $this->resellerMode() && $cust->getReseller() ? $cust->getReseller()->getId() : false ),
                'peeringdb_oauth'       => array_key_exists( 'peeringdb_oauth',     $old    ) ? $old['peeringdb_oauth']         : $cust->getPeeringdbOAuth(),
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
                abort(404, 'Customer not found' );
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

        AlertContainer::push( 'Customer successfully ' . ( $isEdit ? ' edited.' : ' added.' ), Alert::SUCCESS );

        if( $isEdit ){
            return Redirect::to( route( "customer@overview" , [ "id" => $c->getId() ] ) );
        } else {
            return Redirect::to( route( "customer@billing-registration" , [ "id" => $c->getId() ] ) );
        }

    }


    /**
     * Display the billing registration form a customer
     *
     * @param int $id    The Customer ID
     * @return View
     */
    public function editBillingAndRegDetails( int $id = null ): View {

        $c = false; /** @var CustomerEntity $c */
        if( !$id || !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
            abort( 404, 'Customer not found' );
        }

        $cbd = $c->getBillingDetails();
        $crd = $c->getRegistrationDetails();


        $old = request()->old();

        $dataBillingDetail = [];
        if( !( $this->resellerMode() && $c->isResoldCustomer() ) ){
            $dataBillingDetail = [
                'billingContactName'        => array_key_exists( 'billingContactName',      $old    ) ? $old['billingContactName']      : $cbd->getBillingContactName(),
                'billingFrequency'          => array_key_exists( 'billingFrequency',        $old    ) ? $old['billingFrequency']        : $cbd->getBillingFrequency(),
                'billingAddress1'           => array_key_exists( 'billingAddress1',         $old    ) ? $old['billingAddress1']         : $cbd->getBillingAddress1(),
                'billingAddress2'           => array_key_exists( 'billingAddress2',         $old    ) ? $old['billingAddress2']         : $cbd->getBillingAddress2(),
                'billingAddress3'           => array_key_exists( 'billingAddress3',         $old    ) ? $old['billingAddress3']         : $cbd->getBillingAddress3(),
                'billingTownCity'           => array_key_exists( 'billingTownCity',         $old    ) ? $old['billingTownCity']         : $cbd->getBillingTownCity(),
                'billingPostcode'           => array_key_exists( 'billingPostcode',         $old    ) ? $old['billingPostcode']         : $cbd->getBillingPostcode(),
                'billingCountry'            => array_key_exists( 'billingCountry',          $old    ) ? $old['billingCountry']          : in_array( $cbd->getBillingCountry(),  array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ) ? $cbd->getBillingCountry() : null,
                'billingEmail'              => array_key_exists( 'billingEmail',            $old    ) ? $old['billingEmail']            : $cbd->getBillingEmail(),
                'billingTelephone'          => array_key_exists( 'billingTelephone',        $old    ) ? $old['billingTelephone']        : $cbd->getBillingTelephone(),
                'purchaseOrderRequired'     => array_key_exists( 'purchaseOrderRequired',   $old    ) ? $old['purchaseOrderRequired']   : ( $cbd->getPurchaseOrderRequired() ? 1 : 0 ),
                'invoiceMethod'             => array_key_exists( 'invoiceMethod',           $old    ) ? $old['invoiceMethod']           : $cbd->getInvoiceMethod(),
                'invoiceEmail'              => array_key_exists( 'invoiceEmail',            $old    ) ? $old['invoiceEmail']            : $cbd->getInvoiceEmail(),
                'vatRate'                   => array_key_exists( 'vatRate',                 $old    ) ? $old['vatRate']                 : $cbd->getVatRate(),
                'vatNumber'                 => array_key_exists( 'vatNumber',               $old    ) ? $old['vatNumber']               : $cbd->getVatNumber(),
            ];
        }

        $dataRegistrationDetail = [
            'registeredName'            => array_key_exists( 'registeredName',                  $old    ) ? $old['registeredName']  : $crd->getRegisteredName(),
            'companyNumber'             => array_key_exists( 'companyNumber',                   $old    ) ? $old['companyNumber']   : $crd->getCompanyNumber(),
            'jurisdiction'              => array_key_exists( 'jurisdiction',                    $old    ) ? $old['jurisdiction']    : $crd->getJurisdiction(),
            'address1'                  => array_key_exists( 'address1',                        $old    ) ? $old['address1']        : $crd->getAddress1(),
            'address2'                  => array_key_exists( 'address2',                        $old    ) ? $old['address2']        : $crd->getAddress2(),
            'address3'                  => array_key_exists( 'address3',                        $old    ) ? $old['address3']        : $crd->getAddress3(),
            'townCity'                  => array_key_exists( 'townCity',                        $old    ) ? $old['townCity']        : $crd->getTownCity(),
            'postcode'                  => array_key_exists( 'postcode',                        $old    ) ? $old['postcode']        : $crd->getPostcode(),
            'country'                   => array_key_exists( 'country',                         $old    ) ? $old['country']         : in_array( $crd->getCountry(),  array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ) ? $crd->getCountry() : null,
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
    public function details(): View {
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
    public function associates(): View {
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
    public function detail( int $id ): View {
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
     * @throws  \IXP_Exception
     */
    public function overview( $id = null, $tab = null ) : View {

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
    public function delete( Request $request) : RedirectResponse {
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $request->input( "id" ) ) ) ){
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
    public function tags( int $id = null ): View {

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
    public function storeTags( Request $r ): RedirectResponse {

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

