<?php

namespace IXP\Http\Controllers\Customer;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, D2EM , DateTime, Mail, Redirect, Former;

use Illuminate\View\View;
use IXP\Http\Controllers\Controller;
use Illuminate\Http\{
    RedirectResponse, Request
};

use Illuminate\Support\Facades\Session;

use Entities\{
    Customer                as CustomerEntity,
    IXP                     as IXPEntity,
    NetworkInfo             as NetworkInfoEntity,
    IRRDBConfig             as IRRDBConfigEntity,
    CompanyBillingDetail    as CompanyBillingDetailEntity,
    CompanyRegisteredDetail as CompanyRegisteredDetailEntity,
    User                    as UserEntity
};

use IXP\Mail\Customer\Email as EmailCustomer;


use IXP\Http\Requests\{
    StoreCustomer,
    StoreCustomerBillingInformation
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Customer Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerController extends Controller
{

    /**
     * Display all the Customers that are current in terms of `datejoin` and `dateleave` as a list
     *
     * @param bool  $currentCust    Display customers that are current in terms of `datejoin` and `dateleave`
     *
     * @return  View
     */
    public function listByCurrentCust( $currentCust = null ): View {
        if( $currentCust && !in_array( $currentCust,  [ 0, 1 ] )){
            abort( 404);
        }

        return $this->list( $currentCust , null , null );
    }

    /**
     * Display all the Customers as a list
     *
     * @param int   $status         Display customer by specific status
     *
     * @return  View
     */
    public function listByStatus( $status = null ): View {
        if( $status && !array_key_exists( $status, CustomerEntity::$CUST_STATUS_TEXT)){
            abort( 404);
        }

        return $this->list( null, $status, null );
    }

    /**
     * Display all the Customers as a list
     *
     * @param int   $type           Display customer by specific types
     *
     * @return  View
     */
    public function listByType( $type = null ): View {
        if( $type && !array_key_exists( $type, CustomerEntity::$CUST_TYPES_TEXT)){
            abort( 404);
        }
        return $this->list( null,null, $type );
    }

    /**
     * Display all the Customers as a list
     *
     * @param bool  $currentCust    Display customers that are current in terms of `datejoin` and `dateleave`
     * @param int   $status         Display customer by specific status
     * @param int   $type           Display customer by specific types
     *
     * @return  View
     */
    public function list( $currentCust = null, $status = null, $type = null ): View {

        if( $status !== null ){
            Session::put( "cust-list-status", $status );
        } else {
            if ( Session::exists( "cust-list-status" ) ) {
                $status = Session::get( "cust-list-status" );
            }
        }

        if( $type !== null ){
            Session::put( "cust-list-type", $type );
        } else {
            if ( Session::exists( "cust-list-type" ) ) {
                $type = Session::get( "cust-list-type" );
            }
        }

        if( $currentCust !== null ){
            Session::put( "cust-list-current", $currentCust );
        } else {
            if ( Session::exists( "cust-list-current" ) ) {
                $currentCust = Session::get( "cust-list-current" );
            }
        }

        return view( 'customer/list' )->with([
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAllForFeList( $currentCust, $status, $type ),
            'resellerMode'          => $this->resellerMode(),
            'status'                => $status          ?? false,
            'type'                  => $type            ?? false,
            'currentCust'           => $currentCust     ?? false,
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
        if( $id and !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        if( $cust ) {
            // populate the form with VLAN interface data
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
                'status'                => $cust->getStatus(),
                'irrdb'                 => $cust->getIRRDB()->getId(),
                'activepeeringmatrix'   => $cust->getActivepeeringmatrix() ? 1 : 0,
                'nocphone'              => $cust->getNocphone(),
                'noc24hphone'           => $cust->getNoc24hphone(),
                'nocfax'                => $cust->getNocfax(),
                'nocemail'              => $cust->getNocemail(),
                'nochours'              => $cust->getNoc24hphone(),
                'nocwww'                => $cust->getNocwww(),
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
            'ixp'                           => D2EM::getRepository( IXPEntity::class )->find( 1 ),
        ]);
    }

    /**
     * Add or edit a customer (set all the data needed)
     *
     * @param   StoreCustomer $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function store( StoreCustomer $request ): RedirectResponse {
        $isEdit = $request->input( 'id' ) ? true : false;
        /** @var CustomerEntity $cust */
        if( $isEdit && $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'id' ) ) ) {
            if( !$cust ) {
                abort(404, 'Unknown customer');
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
        $cust->setNocfax(               $request->input( 'nocfax'               ) );
        $cust->setNocemail(             $request->input( 'nocemail'             ) );
        $cust->setNochours(             $request->input( 'nochours'             ) );
        $cust->setNocwww(               $request->input( 'nocwww'               ) );

        $cust->setIsReseller(           $request->input( 'isReseller'           ) ?? false  );

        if( !$this->setReseller( $request, $cust ) ) {
            return Redirect::back( )->withErrors();
        }


        if( $isEdit ) {
            $cust->setLastupdated( new DateTime() );
            $cust->setLastupdatedby( Auth::getUser()->getId() );

            $bdetail = $cust->getBillingDetails();
            $rdetail = $cust->getRegistrationDetails();
        } else {
            $cust->setCreated( new DateTime() );
            $cust->setCreator( Auth::getUser()->getId() );

            $bdetail = new CompanyBillingDetailEntity;
            D2EM::persist( $bdetail );
            $bdetail->setPurchaseOrderRequired( 0 );

            $cust->setBillingDetails( $bdetail );

            $rdetail = new CompanyRegisteredDetailEntity;
            D2EM::persist( $rdetail );

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
            return Redirect::to( 'customer/overview/id/' . $cust->getId() );
        } else {
            return Redirect::to( 'customer/billing-registration/' . $cust->getId() );
        }

    }

    /**
     * Sets reseller to customer from form
     *
     * @param StoreCustomer     $request    instance of the current HTTP request
     * @param CustomerEntity    $cust
     *
     * @return bool If false, the form is not processed
     */
    private function setReseller( $request, $cust ){
        $isOK = true;

        if( !$this->resellerMode() )
            $isOK = true;

        if( $request->input( 'isResold' ) ) {
            if( !( $reseller = D2EM::getRepository( CustomerEntity::class )->find( $request->input( "reseller" ) ) ) ) {
                AlertContainer::push( 'Please select a reseller', Alert::DANGER );
                $isOK = false;
            }

            if( $cust->getReseller() && $cust->getReseller()->getId() != $request->input( 'reseller' ) ) {
                foreach( $cust->getVirtualInterfaces() as $viInt ) {
                    foreach( $viInt->getPhysicalInterfaces() as $pi ) {
                        if( $pi->getFanoutPhysicalInterface() && $pi->getFanoutPhysicalInterface()->getVirtualInterface()->getCustomer()->getId() == $cust->getReseller()->getId() ) {
                            AlertContainer::push( 'You can not change the reseller because there are still fanout ports from the current reseller linked to this customer\'s physical interfaces. You need to reassign these first.', Alert::DANGER );
                            $isOK = false;
                        }
                    }
                }
            }

            $cust->setReseller( $reseller );
        }
        else if( $cust->getReseller() ) {
            foreach( $cust->getVirtualInterfaces() as $vi ) {
                foreach( $vi->getPhysicalInterfaces() as $pi ) {
                    if( $pi->getFanoutPhysicalInterface() && $pi->getFanoutPhysicalInterface()->getVirtualInterface()->getCustomer()->getId() == $cust->getReseller()->getId() ) {
                        AlertContainer::push( 'You can not change this resold customer state because there are still physical interface(s) of this customer linked to fanout ports or the current reseller. You need to reassign these first', Alert::DANGER );
                        $isOK = false;
                    }
                }
            }
            $cust->setReseller( null );
        }

        if( !$request->input( 'isReseller' ) && $cust->getIsReseller() && count( $cust->getResoldCustomers() ) ) {
            AlertContainer::push( 'You can not change the reseller state because this customer still has resold customers. You need to reassign these first.', Alert::DANGER );
            $isOK = false;
        }

        return $isOK;
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
        ]);
    }


    /**
     * Add or edit a customer billing information
     *
     * email notification
     *
     * @param   StoreCustomerBillingInformation $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     * @throws
     */
    public function storeBillingInformation( StoreCustomerBillingInformation $request ): RedirectResponse {
        $isEdit = $request->input( 'id' ) ? true : false;
        /** @var CustomerEntity $cust */
        if( $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'id' ) ) ) {
            if( !$cust ) {
                abort(404, 'Unknown customer');
            }
        }

        $oldBillingDetail = $billingDetails         = $cust->getBillingDetails();
        $registrationDetails                        = $cust->getRegistrationDetails();

        $registrationDetails->setRegisteredName(    $request->input( 'registeredName'       ) );
        $registrationDetails->setCompanyNumber(     $request->input( 'companyNumber'        ) );
        $registrationDetails->setJurisdiction(      $request->input( 'jurisdiction'         ) );
        $registrationDetails->setAddress1(          $request->input( 'address1'             ) );
        $registrationDetails->setAddress2(          $request->input( 'address2'             ) );
        $registrationDetails->setAddress3(          $request->input( 'address3'             ) );
        $registrationDetails->setTownCity(          $request->input( 'townCity'             ) );
        $registrationDetails->setPostcode(          $request->input( 'postcode'             ) );
        $registrationDetails->setCountry(           $request->input( 'nacountryme'          ) );

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
            $mailable = new EmailCustomer( $cust );
            try {
                $mailable->subject( config('identity.sitename') . " - ('Billing Details Change Notification')" );
                $mailable->from( config('identity.email'), config('identity.name') );
                $mailable->to( config('ixp_tools.billing_updates_notify'), config('identity.sitename') . ' - Accounts' );
                $mailable->view( "customer/emails/billing-details" )->with( ['cust' => $cust, 'oldDetails' => $oldBillingDetail] );
                Mail::send( $mailable );

                if( Auth::getUser()->getPrivs() == UserEntity::AUTH_SUPERUSER ) {
                    AlertContainer::push( "Notification of updated billing details has been sent to " . config('ixp_tools.billing_updates_notify'), Alert::SUCCESS );
                }
            } catch( Exception $e ) {

                AlertContainer::push( "Could not sent notification of updated billing details to " . config('ixp_tools.billing_updates_notify')
                    . ". Check your email settings.", Alert::DANGER );
            }

        }

        return Redirect::to( 'customer/overview/id/' . $cust->getId() . '/tab/billing' );


    }

    public function details( ): View {
        $ixp            = D2EM::getRepository( IXPEntity::class )->getDefault();
        $custs          = D2EM::getRepository( CustomerEntity::class )->getCurrentActive( false, false, false, false);

        return view( 'customer/details' )->with([
            'ixp'                   => $ixp,
            'custs'                 => $custs,
        ]);
    }

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
}