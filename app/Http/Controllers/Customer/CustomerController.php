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

use IXP\Events\Customer\BillingDetailsChanged as CustomerBillingDetailsChangedEvent;

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
    public function store( CustomerRequest $r ): RedirectResponse {

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
        $c->setDatejoin(             $r->input( 'datejoin'             )  ? new \DateTime( $r->input( 'datejoin'    ) ) : null );
        $c->setDateleave(            $r->input( 'dateleave'            )  ? new \DateTime( $r->input( 'dateleave'   ) ) : null );
        $c->setStatus(               $r->input( 'status'               ) );
        $c->setMD5Support(           $r->input( 'md5support'           ) );
        $c->setAbbreviatedName(      $r->input( 'abbreviatedName'      ) );


        $c->setAutsys(               $r->input( 'autsys'               ) );
        $c->setMaxprefixes(          $r->input( 'maxprefixes'          ) );
        $c->setPeeringemail(         $r->input( 'peeringemail'         ) );
        $c->setPeeringmacro(         $r->input( 'peeringmacro'         ) );
        $c->setPeeringmacrov6(       $r->input( 'peeringmacrov6'       ) );
        $c->setPeeringpolicy(        $r->input( 'peeringpolicy'        ) );
        $c->setActivepeeringmatrix(  $r->input( 'activepeeringmatrix'  ) );


        $c->setNocphone(             $r->input( 'nocphone'             ) );
        $c->setNoc24hphone(          $r->input( 'noc24hphone'          ) );
        $c->setNocemail(             $r->input( 'nocemail'             ) );
        $c->setNochours(             $r->input( 'nochours'             ) );
        $c->setNocwww(               $r->input( 'nocwww'               ) );

        $c->setIsReseller(           $r->input( 'isReseller'           ) ?? false  );

        if( $r->input( 'isResold' ) ) {
            $c->setReseller( D2EM::getRepository( CustomerEntity::class )->find( $this->input( "reseller" ) ) );
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
        }

        if( $r->input( 'irrdb' ) ) {
            $c->setIRRDB( D2EM::getRepository( IRRDBConfigEntity::class)->find( $r->input( 'irrdb' ) ) ) ;
        } else {
            $c->setIRRDB( null );
        }

        D2EM::flush();

        AlertContainer::push( 'Customer successfully ' . ( $isEdit ? ' edited.' : ' added.' ), Alert::SUCCESS );

        if( $isEdit ){
            return Redirect::to( route( "customer@overview" , [ "id" => $c->getId() ] ) );
        } else {
            return Redirect::to( route( "customer@billingRegistration" , [ "id" => $c->getId() ] ) );
        }

    }


    /**
     * Display the billing registration form a customer
     *
     * @param int $id    The Customer ID
     *
     * @return View
     */
    public function editBillingAndRegDetails( int $id = null ): View {

        $c = false; /** @var CustomerEntity $c */
        if( !$id || !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
            abort( 404, 'Customer not found' );
        }

        $cbd = $c->getBillingDetails();
        $crd = $c->getRegistrationDetails();


        $dataBillingDetail = [];
        if( !( $this->resellerMode() && $c->isResoldCustomer() ) ){
            $dataBillingDetail = [
                'billingContactName'        => $cbd->getBillingContactName(),
                'billingFrequency'          => $cbd->getBillingFrequency(),
                'billingAddress1'           => $cbd->getBillingAddress1(),
                'billingAddress2'           => $cbd->getBillingAddress2(),
                'billingAddress3'           => $cbd->getBillingAddress3(),
                'billingTownCity'           => $cbd->getBillingTownCity(),
                'billingPostcode'           => $cbd->getBillingPostcode(),
                'billingCountry'            => $cbd->getBillingCountry(),
                'billingEmail'              => $cbd->getBillingEmail(),
                'billingTelephone'          => $cbd->getBillingTelephone(),
                'purchaseOrderRequired'     => $cbd->getPurchaseOrderRequired() ? 1 : 0,
                'invoiceMethod'             => $cbd->getInvoiceMethod(),
                'invoiceEmail'              => $cbd->getInvoiceEmail(),
                'vatRate'                   => $cbd->getVatRate(),
                'vatNumber'                 => $cbd->getVatNumber(),
            ];
        }

        $dataRegistrationDetail = [
            'registeredName'            => $crd->getRegisteredName(),
            'companyNumber'             => $crd->getCompanyNumber(),
            'jurisdiction'              => $crd->getJurisdiction(),
            'address1'                  => $crd->getAddress1(),
            'address2'                  => $crd->getAddress2(),
            'address3'                  => $crd->getAddress3(),
            'townCity'                  => $crd->getTownCity(),
            'postcode'                  => $crd->getPostcode(),
            'country'                   => $crd->getCountry(),
        ];

        Former::populate( array_merge( $dataRegistrationDetail, $dataBillingDetail ) );

        return view( 'customer/billing-registration' )->with([
            'c'                             => $c,
            'juridictions'                  => D2EM::getRepository( CompanyRegisteredDetailEntity::class )->getJuridictionsAsArray(),
            'countries'                     => Countries::getList('name' )
        ]);
    }


    /**
     * Add or edit a customer's regitsration / billing information
     *
     * email notification
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

        $crd->setRegisteredName(    $request->input( 'registeredName'       ) );
        $crd->setCompanyNumber(     $request->input( 'companyNumber'        ) );
        $crd->setJurisdiction(      $request->input( 'jurisdiction'         ) );
        $crd->setAddress1(          $request->input( 'address1'             ) );
        $crd->setAddress2(          $request->input( 'address2'             ) );
        $crd->setAddress3(          $request->input( 'address3'             ) );
        $crd->setTownCity(          $request->input( 'townCity'             ) );
        $crd->setPostcode(          $request->input( 'postcode'             ) );
        $crd->setCountry(           $request->input( 'country'              ) );

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
        $cbd->setPurchaseOrderRequired(  $request->input( 'purchaseOrderRequired') ?? 0 );
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
        $custs          = D2EM::getRepository( CustomerEntity::class )->getCurrentActive( false, true, false );

        return view( 'customer/details' )->with([
            'custs'                 => $custs,
            'associates'            => false,
        ]);
    }

    /**
     * Display the list of all asscociate customers
     *
     * @return  View
     */
    public function associates(): View {
        $custs          = D2EM::getRepository( CustomerEntity::class )->getCurrentAssociate( false );

        return view( 'customer/details' )->with([
            'custs'                 => $custs,
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

