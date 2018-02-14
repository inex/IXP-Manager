<?php

namespace IXP\Http\Controllers\Customer;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use App, Auth, Countries, D2EM, DateTime, Exception, Former, Mail, Redirect;

use IXP\Http\Controllers\Controller;

use Illuminate\Http\{
    RedirectResponse,
    JsonResponse,
    Request
};

use Illuminate\View\View;


use Entities\{
    CompanyBillingDetail    as CompanyBillingDetailEntity,
    CompanyRegisteredDetail as CompanyRegisteredDetailEntity,
    Customer                as CustomerEntity,
    CustomerNote            as CustomerNoteEntity,
    IRRDBConfig             as IRRDBConfigEntity,
    IXP                     as IXPEntity,
    NetworkInfo             as NetworkInfoEntity,
    PhysicalInterface       as PhysicalInterfaceEntity,
    RSPrefix                as RSPrefixEntity,
    User                    as UserEntity
};

use IXP\Mail\Customer\Email as EmailCustomer;

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
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
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

        return view( 'customer/list' )->with([
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAllForFeList( $showCurrentOnly, $state, $type ),
            'state'                 => $state           ?? false,
            'type'                  => $type            ?? false,
            'showCurrentOnly'       => $showCurrentOnly ?? false,
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

        $cust = false; /** @var CustomerEntity $cust */
        if( $id && !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        if( $cust ) {
            // populate the form with data
            Former::populate([
                'name'                  => $cust->getName(),
                'type'                  => $cust->getType(),
                'shortname'             => $cust->getShortname(),
                'corpwww'               => $cust->getCorpwww(),
                'datejoin'              => $cust->getDatejoin() ? $cust->getDatejoin()->format( "Y-m-d" ) : null,
                'dateleft'              => $cust->getDateleave() ? $cust->getDateleave()->format( "Y-m-d" ) : null,
                'status'                => $cust->getStatus(),
                'md5support'            => $cust->getMD5Support(),
                'abbreviatedName'       => $cust->getAbbreviatedName(),
                'autsys'                => $cust->getAutsys(),
                'maxprefixes'           => $cust->getMaxprefixes(),
                'peeringpolicy'         => $cust->getPeeringpolicy(),
                'peeringemail'          => $cust->getPeeringemail(),
                'peeringmacro'          => $cust->getPeeringmacro(),
                'peeringmacrov6'        => $cust->getPeeringmacrov6(),
                'irrdb'                 => $cust->getIRRDB()->getId(),
                'activepeeringmatrix'   => $cust->getActivepeeringmatrix() ? 1 : 0,
                'nocphone'              => $cust->getNocphone(),
                'noc24hphone'           => $cust->getNoc24hphone(),
                'nocemail'              => $cust->getNocemail(),
                'nochours'              => $cust->getNoc24hphone(),
                'nocwww'                => $cust->getNocwww(),
                'isReseller'            => $cust->getIsReseller() ? 1 : 0,
                'isResold'              => $this->resellerMode() && $cust->getReseller() ? 1 : 0,
                'reseller'              => $this->resellerMode() && $cust->getReseller() ? $cust->getReseller()->getId() : false,
            ]);
        }

        return view( 'customer/edit' )->with([
            'cust'                          => $cust,
            'irrdbs'                        => D2EM::getRepository( IRRDBConfigEntity::class )->getAsArray(),
            'resellerMode'                  => $this->resellerMode(),
            'resellers'                     => D2EM::getRepository( CustomerEntity::class )->getResellerNames(),
        ]);
    }

    /**
     * Add or edit a customer (set all the data needed)
     *
     * @param   CustomerRequest $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function store( CustomerRequest $request ): RedirectResponse {

        dd( $request );
        $isEdit = $request->input( 'id' ) ? true : false;

        /** @var CustomerEntity $cust */
        if( $isEdit && $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'id' ) ) ) {
            if( !$cust ) {
                abort(404, 'Customer not found' );
            }
        } else {
            $cust = new CustomerEntity;
            D2EM::persist( $cust );
        }


        $cust->setName(                 $request->input( 'name'                 ) );
        $cust->setType(                 $request->input( 'type'                 ) );
        $cust->setShortname(            $request->input( 'shortname'            ) );
        $cust->setCorpwww(              $request->input( 'corpwww'              ) );
        $cust->setDatejoin(             $request->input( 'datejoin'             )  ? new \DateTime( $request->input( 'datejoin'    ) ) : null );
        $cust->setDateleave(            $request->input( 'dateleave'            )  ? new \DateTime( $request->input( 'dateleave'   ) ) : null );
        $cust->setStatus(               $request->input( 'status'               ) );
        $cust->setMD5Support(           $request->input( 'md5support'           ) );
        $cust->setAbbreviatedName(      $request->input( 'abbreviatedName'      ) );


        $cust->setAutsys(               $request->input( 'autsys'               ) );
        $cust->setMaxprefixes(          $request->input( 'maxprefixes'          ) );
        $cust->setPeeringemail(         $request->input( 'peeringemail'         ) );
        $cust->setPeeringmacro(         $request->input( 'peeringmacro'         ) );
        $cust->setPeeringmacrov6(       $request->input( 'peeringmacrov6'       ) );
        $cust->setPeeringpolicy(        $request->input( 'peeringpolicy'        ) );
        $cust->setActivepeeringmatrix(  $request->input( 'activepeeringmatrix'  ) );


        $cust->setNocphone(             $request->input( 'nocphone'             ) );
        $cust->setNoc24hphone(          $request->input( 'noc24hphone'          ) );
        $cust->setNocemail(             $request->input( 'nocemail'             ) );
        $cust->setNochours(             $request->input( 'nochours'             ) );
        $cust->setNocwww(               $request->input( 'nocwww'               ) );

        $cust->setIsReseller(           $request->input( 'isReseller'           ) ?? false  );

        if( $this->setReseller( $request, $cust ) ) {
            return Redirect::back()->withErrors();
        }


        if( $isEdit ) {
            $cust->setLastupdated( new DateTime() );
            $cust->setLastupdatedby( Auth::getUser()->getId() );
        } else {
            $cust->setCreated( new DateTime() );
            $cust->setCreator( Auth::getUser()->getId() );

            $bdetail = new CompanyBillingDetailEntity;
            D2EM::persist( $bdetail );
            $bdetail->setPurchaseOrderRequired( 0 );

            $rdetail = new CompanyRegisteredDetailEntity;
            D2EM::persist( $rdetail );

            $cust->setBillingDetails( $bdetail );
            $cust->setRegistrationDetails( $rdetail );
            $cust->setIsReseller( 0 );
        }

        if( $request->input( 'irrdb' ) ) {
            $cust->setIRRDB( D2EM::getRepository( IRRDBConfigEntity::class)->find( $request->input( 'irrdb' ) ) ) ;
        } else {
            $cust->setIRRDB( null );
        }

        if( !$isEdit ) {
            $cust->addIXP( D2EM::getRepository( IXPEntity::class )->find( $request->input( 'ixp' ) ) );
        }

        D2EM::flush();

        AlertContainer::push( 'Customer successfully ' . ( $isEdit ? ' edited.' : ' added.' ), Alert::SUCCESS );

        if( $isEdit ){
            return Redirect::to( route( "customer@overview" , [ "id" => $cust->getId() ] ) );
        } else {
            return Redirect::to( route( "customer@billingRegistration" , [ "id" => $cust->getId() ] ) );
        }

    }


    /**
     * Display the billing registration form a customer
     *
     * @param int $id    The Customer ID
     *
     * @return View
     */
    public function billingRegistration( int $id = null ): View {

        $cust = false; /** @var CustomerEntity $cust */
        if( $id and !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        $billingDetails         = $cust->getBillingDetails();
        $registrationDetails    = $cust->getRegistrationDetails();
        $billingNotify = config( 'ixp_tools.billing_updates_notify' );

        if( $cust ) {
            $dataBillingDetail = [];

            if( ( !isset( $billingNotify ) || !$billingNotify  ) || !$this->resellerMode() || !$cust->isResoldCustomer() ){
                $dataBillingDetail = [
                    'billingContactName'        => $billingDetails->getBillingContactName(),
                    'billingFrequency'          => $billingDetails->getBillingFrequency(),
                    'billingAddress1'           => $billingDetails->getBillingAddress1(),
                    'billingAddress2'           => $billingDetails->getBillingAddress2(),
                    'billingAddress3'           => $billingDetails->getBillingAddress3(),
                    'billingTownCity'           => $billingDetails->getBillingTownCity(),
                    'billingPostcode'           => $billingDetails->getBillingPostcode(),
                    'billingCountry'            => $billingDetails->getBillingCountry(),
                    'billingEmail'              => $billingDetails->getBillingEmail(),
                    'billingTelephone'          => $billingDetails->getBillingTelephone(),
                    'purchaseOrderRequired'     => $billingDetails->getPurchaseOrderRequired() ? 1 : 0,
                    'invoiceMethod'             => $billingDetails->getInvoiceMethod(),
                    'invoiceEmail'              => $billingDetails->getInvoiceEmail(),
                    'vatRate'                   => $billingDetails->getVatRate(),
                    'vatNumber'                 => $billingDetails->getVatNumber(),
                ];
            }

            $dataRegistrationDetail = [
                'registeredName'            => $registrationDetails->getRegisteredName(),
                'companyNumber'             => $registrationDetails->getCompanyNumber(),
                'jurisdiction'              => $registrationDetails->getJurisdiction(),
                'address1'                  => $registrationDetails->getAddress1(),
                'address2'                  => $registrationDetails->getAddress2(),
                'address3'                  => $registrationDetails->getAddress3(),
                'townCity'                  => $registrationDetails->getTownCity(),
                'postcode'                  => $registrationDetails->getPostcode(),
                'country'                   => $registrationDetails->getCountry(),
            ];

            Former::populate( array_merge( $dataRegistrationDetail, $dataBillingDetail ) );
        }

        return view( 'customer/billing-registration' )->with([
            'cust'                          => $cust,
            'juridictions'                  => D2EM::getRepository( CompanyRegisteredDetailEntity::class )->getJuridictionsAsArray(),
            'billingNotify'                 => $billingNotify,
            'resellerMode'                  => $this->resellerMode(),
            'countries'                     => Countries::getList('name' )
        ]);
    }


    /**
     * Add or edit a customer billing information
     *
     * email notification
     *
     * @param   BillingInformationRequest $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function storeBillingInformation( BillingInformationRequest $request ): RedirectResponse {
        /** @var CustomerEntity $cust */
        if( $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'id' ) ) ) {
            if( !$cust ) {
                abort(404, 'Unknown customer');
            }
        }

        $oldBillingDetail                           = clone $cust->getBillingDetails();
        $billingDetails                             = $cust->getBillingDetails();
        $registrationDetails                        = $cust->getRegistrationDetails();

        $registrationDetails->setRegisteredName(    $request->input( 'registeredName'       ) );
        $registrationDetails->setCompanyNumber(     $request->input( 'companyNumber'        ) );
        $registrationDetails->setJurisdiction(      $request->input( 'jurisdiction'         ) );
        $registrationDetails->setAddress1(          $request->input( 'address1'             ) );
        $registrationDetails->setAddress2(          $request->input( 'address2'             ) );
        $registrationDetails->setAddress3(          $request->input( 'address3'             ) );
        $registrationDetails->setTownCity(          $request->input( 'townCity'             ) );
        $registrationDetails->setPostcode(          $request->input( 'postcode'             ) );
        $registrationDetails->setCountry(           $request->input( 'country'          ) );

        $billingDetails->setBillingContactName(     $request->input( 'billingContactName'   ) );
        $billingDetails->setBillingFrequency(       $request->input( 'billingFrequency'     ) );
        $billingDetails->setBillingAddress1(        $request->input( 'billingAddress1'      ) );
        $billingDetails->setBillingAddress2(        $request->input( 'billingAddress2'      ) );
        $billingDetails->setBillingAddress3(        $request->input( 'billingAddress3'      ) );
        $billingDetails->setBillingTownCity(        $request->input( 'billingTownCity'      ) );
        $billingDetails->setBillingPostcode(        $request->input( 'billingPostcode'      ) );
        $billingDetails->setBillingCountry(         $request->input( 'billingCountry'       ) );
        $billingDetails->setBillingEmail(           $request->input( 'billingEmail'         ) );
        $billingDetails->setBillingTelephone(       $request->input( 'billingTelephone'     ) );
        $billingDetails->setPurchaseOrderRequired(  $request->input( 'purchaseOrderRequired') ?? 0 );
        $billingDetails->setInvoiceMethod(          $request->input( 'invoiceMethod'        ) );
        $billingDetails->setInvoiceEmail(           $request->input( 'invoiceEmail'         ) );
        $billingDetails->setVatRate(                $request->input( 'vatRate'              ) );
        $billingDetails->setVatNumber(              $request->input( 'vatNumber'            ) );

        D2EM::flush( $billingDetails );
        D2EM::flush( $registrationDetails );

        if( config( 'ixp_tools.billing_updates_notify', false ) && !$cust->getReseller() ) {
            // send notification email
            $mailable = new EmailCustomer( $cust );
            try {
                $mailable->subject( config('identity.sitename') . " - ('Billing Details Change Notification')" );
                $mailable->from( config('identity.email'), config('identity.name') );
                $mailable->to( config('ixp_tools.billing_updates_notify'), config('identity.sitename') . ' - Accounts' );
                $mailable->view( "customer/emails/billing-details" )->with( ['billingDetail' => $billingDetails, 'oldDetails' => $oldBillingDetail] );
                Mail::send( $mailable );

                if( Auth::getUser()->getPrivs() == UserEntity::AUTH_SUPERUSER ) {
                    AlertContainer::push( "Notification of updated billing details has been sent to " . config('ixp_tools.billing_updates_notify'), Alert::SUCCESS );
                }
            } catch( Exception $e ) {
                AlertContainer::push( "Could not sent notification of updated billing details to " . config('ixp_tools.billing_updates_notify')
                    . ". Check your email settings.", Alert::DANGER );
            }

        }

        return Redirect::to( route( "customer@overview" , [ "id" => $cust->getId() , "tab" => "details" ]  ) );

    }

    /**
     * Display the list of all the Customers
     *
     * @return  View
     * @throws
     */
    public function details( ): View {
        $ixp            = D2EM::getRepository( IXPEntity::class )->getDefault();
        $custs          = D2EM::getRepository( CustomerEntity::class )->getCurrentActive( false, false, false, false);

        return view( 'customer/details' )->with([
            'ixp'                   => $ixp,
            'custs'                 => $custs,
        ]);
    }

    /**
     * Display all the informations for a customer
     *
     * @param int $id ID of the customer
     *
     * @return  View
     */
    public function detail( int $id = null ): View {
        if( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ){
            abort( 404);
        }

        return view( 'customer/detail' )->with([
            'as112UiActive'         => $this->as112UiActive(),
            'cust'                  => $cust,
            'netInfo'               => D2EM::getRepository( NetworkInfoEntity::class )->asVlanProtoArray()
        ]);
    }

    /**
     * Load a customer from the database with the given ID (or ID in request)
     *
     * @param int $id The customer ID

     * @return \Entities\Customer The customer object
     */
    protected function loadCustomer( int $id = null ){
        if( Auth::getUser()->isSuperUser() ) {
            if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ){
                abort( 404);
            }
        } else {
            $c = Auth::getUser()->getCustomer();
        }

        return $c;
    }

    public function unreadNotes(){
        $lastReads = Auth::getUser()->getAssocPreference( 'customer-notes' )[0];

        $latestNotes = [];

        foreach( D2EM::getRepository( CustomerNoteEntity::class )->getLatestUpdate() as $ln ) {

            if( ( !isset( $lastReads['read_upto'] ) || $lastReads['read_upto'] < strtotime( $ln['latest']  ) )
                && ( !isset( $lastReads[ $ln['cid'] ] ) || $lastReads[ $ln['cid'] ]['last_read'] < strtotime( $ln['latest'] ) ) )
                $latestNotes[] = $ln;

        }

        return view( 'customer/unread-notes' )->with([
            'notes'                     => $latestNotes,
            'c'                         => Auth::getUser()->getCustomer()
        ]);
    }

    /**
     * Display the customer overview
     *
     * @param   int         $id         Id of the customer
     * @param   string      $tab        Tab from the overview selected
     *
     * @return  View
     * @throws
     */
    public function overview(  $id = null, string $tab = null ) : View {
        $netinfo            = D2EM::getRepository( NetworkInfoEntity::class )->asVlanProtoArray();
        $c                  = $this->loadCustomer( $id );
        $isSuperUser        = Auth::getUser()->isSuperUser();

        // is this user watching all notes for this customer?
        $coNotifyAll = Auth::getUser()->getPreference( "customer-notes.{$c->getId()}.notify" ) ? true : false;

        // what specific notes is this cusomer watching?
        $coNotify = Auth::getUser()->getAssocPreference( "customer-notes.watching" ) ? Auth::getUser()->getAssocPreference( "customer-notes.watching" )[0] : [];


        // load customer notes and the amount of unread notes for this user and customer
        // ASK FO THAT $this->_fetchCustomerNotes( $cust->getId() );

        $rsRoutes = $c->isRouteServerClient() ? D2EM::getRepository( RSPrefixEntity::class )->aggregateRouteSummariesForCustomer( $c->getId() ) : false;

        $crossConnects = D2EM::getRepository( CustomerEntity::class )->getCrossConnects( $c->getId() );

        // does the customer have any graphs?
        $hasAggregateGraph = false;
        $aggregateGraph = false;
        $grapher = App::make('IXP\Services\Grapher' );
        if( $c->getType() != CustomerEntity::TYPE_ASSOCIATE && !$c->hasLeft() ) {
            foreach( $c->getVirtualInterfaces() as $vi ) {
                foreach( $vi->getPhysicalInterfaces() as $pi ) {
                    if( $pi->getStatus() == PhysicalInterfaceEntity::STATUS_CONNECTED ) {
                        $hasAggregateGraph = true;
                        $aggregateGraph = $grapher->customer( $c );
                        break;
                    }
                }
            }
        }

        //is customer RS or AS112 client
        $rsclient = false;
        $as112client   = false;
        foreach( $c->getVirtualInterfaces() as $vi ) {
            foreach( $vi->getVlanInterfaces() as $vli ) {
                if( $vli->getRsclient() ){
                    $rsclient = true;
                }

                if( $vli->getAs112client() ){
                    $as112client = true;
                }
            }
        }

        $arrayNotes = $this->fetchCustomerNotes( $c->getId() );

        return view( 'customer/overview' )->with([
            'c'                         => $c,
            'customers'                 => D2EM::getRepository( CustomerEntity::class )->getNames( true ),
            'netInfo'                   => $netinfo,
            'isSuperUser'               => $isSuperUser,
            'coNotifyAll'               => $coNotifyAll,
            'coNotify'                  => $coNotify,
            'rsRoutes'                  => $rsRoutes,
            'crossConnects'             => $crossConnects,
            'hasAggregateGraph'         => $hasAggregateGraph,
            'aggregateGraph'            => $aggregateGraph,
            'grapher'                   => $grapher,
            'rsclient'                  => $rsclient,
            'as112client'               => $as112client,
            'logoManagementDisabled'    => $this->logoManagementDisabled(),
            'resellerMode'              => $this->resellerMode(),
            'as112UiActive'             => $this->as112UiActive(),
            'resellerResoldBilling'     => config('ixp.reseller.reseller'),
            'countries'                 => Countries::getList('name' ),
            'tab'                       => strtolower( $tab ),
            'notesInfo'                 => $arrayNotes
        ]);
    }

    /**
     * Load a customer's notes and calculate the amount of unread / updated notes
     * for the logged in user and the given customer
     *
     * Used by:
     * @see CustomerController
     * @see DashboardController
     *
     * @param int       $custid
     * @param boolean   $publicOnly
     *
     * @return array
     */
    protected function fetchCustomerNotes( $custid, $publicOnly = false ){
        $custNotes      = D2EM::getRepository( CustomerNoteEntity::class )->ordered( $custid, $publicOnly );
        $unreadNotes    = 0;
        $rut            = Auth::getUser()->getPreference( "customer-notes.read_upto" );
        $lastRead       = Auth::getUser()->getPreference( "customer-notes.{$custid}.last_read" );

        if( $lastRead || $rut ) {
            foreach( $custNotes as $cn ) {
                /** @var CustomerNoteEntity $cn */
                $time = $cn->getUpdated()->format( "U" );
                if( ( !$rut || $rut < $time ) && ( !$lastRead || $lastRead < $time ) ){
                    $unreadNotes++;
                }
            }
        } else {
            $unreadNotes = count( $custNotes );
        }

        return [ "custNotes" => $custNotes, "notesReadUpto" => $rut , "notesLastRead" => $lastRead, "unreadNotes" => $unreadNotes];

    }

    /**
     * Display the Welcome Email form
     *
     * @param   int      $id         Id of the customer
     *
     * @return  View
     * @throws
     */
    public function welcomeEmail( int $id = null ) : View{
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
            'body'      => view( "customer/emails/welcome-email" )->with( [
                    'c'                     => $c,
                    'admins'                => $c->getAdminUsers() ,
                    'netinfo'               => D2EM::getRepository( NetworkInfoEntity::class )->asVlanProtoArray(),
                    'identityEmail'         => config('identity.email'),
                    'identityOrgname'       => config('identity.orgname'),
                ] )->render()
        ]);

    }

    /**
     * Send the welcome email to a customer
     *
     * @param WelcomeEmailRequest $request
     *
     * @return RedirectResponse|View
     *
     * @throws
     */
    public function sendWelcomeEmail( WelcomeEmailRequest $request){
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'id' ) ) ) ){
            abort( 404);
        }

        $mailable = new EmailCustomer( $c );
        $mailable->prepareFromRequest( $request );

        $mailable->prepareBody( $request );

        try {
            $mailable->checkIfSendable();
        } catch( MailableException $e ) {
            AlertContainer::push( $e->getMessage(), Alert::DANGER );

            return view( 'customer/welcome-email' )->with([
                'c'         => $c,
                'body'      => view( "customer/emails/welcome-email" )->with( [
                    'c'                     => $c,
                    'admins'                => $c->getAdminUsers() ,
                    'netinfo'               => D2EM::getRepository( NetworkInfoEntity::class )->asVlanProtoArray(),
                    'identityEmail'         => config('identity.email'),
                    'identityOrgname'       => config('identity.orgname'),
                ] )->render()
            ]);
        }

        Mail::send( $mailable );

        AlertContainer::push( "Welcome email sent.", Alert::SUCCESS );

        return Redirect::to( route( "customer@overview", [ "id" => $c->getId() ] ) );

    }

    /**
     * Display Recap of the the information that will be deleted with the customer
     *
     * @param   int      $id         Id of the customer
     *
     * @return  View
     * @throws
     */
    public function deleteRecap( int $id = null ) : View{
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ){
            abort( 404);
        }

        return view( 'customer/delete' )->with([
            'c'         => $c,
        ]);

    }

    /**
     * Delete a customer and everything related !!
     *
     * @param   Request      $request         Instance of HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function delete( Request $request) : RedirectResponse{
        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $request->input( "id" ) ) ) ){
            abort( 404);
        }

        if( D2EM::getRepository( CustomerEntity::class )->delete( $c ) ) {
            AlertContainer::push( "Customer deleted with success.", Alert::SUCCESS );
        } else {
            AlertContainer::push( "Error", Alert::DANGER );
        }

        return Redirect::to( route( "customer@list" ) );

    }

}

