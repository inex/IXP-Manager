<?php

namespace IXP\Http\Controllers\Customer;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use App, Auth, Cache, Countries, Former, Mail;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\{
    JsonResponse,
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Events\Customer\BillingDetailsChanged as CustomerBillingDetailsChangedEvent;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    Aggregators\CustomerAggregator,
    CompanyBillingDetail,
    CompanyRegisteredDetail,
    Customer,
    CustomerNote,
    CustomerTag,
    IrrdbConfig,
    NetworkInfo,
    Router,
    Vlan
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

use IXP\Services\Grapher;

/**
 * Customer Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Customer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerController extends Controller
{
    /**
     * Display all the Customers as a list
     *
     * @param   Request $r
     *
     * @return  View
     */
    public function list( Request $r ): View
    {
        if( ( $state = $r->state ) !== null ) {
            if( isset( Customer::$CUST_STATUS_TEXT[ $state ] ) ) {
                $r->session()->put( "cust-list-state", $state );
            } else {
                $r->session()->remove( "cust-list-state" );
            }
        } else if( $r->session()->exists( "cust-list-state" ) ) {
            $state = $r->session()->get( "cust-list-state" );
        }

        if( ( $type = $r->type ) !== null ) {
            if( isset( Customer::$CUST_TYPES_TEXT[ $type ] ) ) {
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

        $tags = CustomerTag::all()->keyBy( 'id' )->toArray();

        if( $r->tag  !== null ) {
            if(  isset( $tags[ $r->tag ] ) ) {
                $tid = $tags[ $r->tag ][ 'id' ];
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

        return view( 'customer/list' )->with([
            'state'                 => $state           ?? false,
            'type'                  => $type            ?? false,
            'showCurrentOnly'       => $showCurrentOnly ?? false,
            'tag'                   => $tid ?? false,
            'tags'                  => $tags,
            'custs'                 => Customer::selectRaw( 'cust.*' )
                ->when( $tid, function( Builder $q, $tid ) {
                    return $q->leftJoin( 'cust_to_cust_tag AS t', 't.customer_id', 'cust.id' )
                        ->where( 't.customer_tag_id', $tid );
                } )->when( $state && isset( Customer::$CUST_STATUS_TEXT[ $state ] ), function( Builder $q ) use( $state ) {
                    return $q->where( 'cust.status', $state );
                } )->when( $type && isset( Customer::$CUST_TYPES_TEXT[ $type ] ), function( Builder $q ) use( $type ) {
                    return $q->where( 'cust.type', $type );
                } )->when( $showCurrentOnly, function( Builder $q ) {
                    return $q->whereRaw( Customer::SQL_CUST_CURRENT );
                } )->orderByRaw( 'cust.name' )->get(),
        ]);
    }

    /**
     * Display the form to create a customer
     *
     * @return View
     */
    public function create(): View
    {
        // populate the form with default data
        Former::populate([
            'activepeeringmatrix'  => 1,
            'peeringdb_oauth'      => 1,
        ]);

        return view( 'customer/edit' )->with([
            'cust'              => false,
            'irrdbs'            => IrrdbConfig::orderBy( 'source' )->get(),
            'resellers'         => Customer::select( [ 'id', 'name' ] )
                ->resellerOnly()->orderBy( 'name' )->get(),
        ]);
    }

    /**
     * Create a customer (set all the data needed)
     *
     * @param   CustomerRequest $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function store( CustomerRequest $r ): RedirectResponse
    {
        $bdetail = CompanyBillingDetail::create( [ 'purchaseOrderRequired' => 0 ] );
        $rdetail = CompanyRegisteredDetail::create( [ 'registeredName' => $r->name ] );

        $cust = Customer::create( array_merge( $r->all(),
            [
                'reseller'                      => $r->isResold ? $r->reseller : null,
                'company_registered_detail_id'  =>  $rdetail->id,
                'company_billing_details_id'    =>  $bdetail->id,
                'creator'                       =>  Auth::id()
            ]
        ) );

        Cache::forget( 'admin_home_customers' );
        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' created.', Alert::SUCCESS );
        return redirect( route( 'customer@billing-registration' , [ 'cust' => $cust->id ] ) );
    }

    /**
     * Display the form to edit a customer
     *
     * @param Request   $r
     * @param Customer  $cust The Customer
     *
     * @return View
     */
    public function edit( Request $r, Customer $cust ): View
    {
        Former::populate([
            'name'                  => $r->old( 'name',                $cust->name              ),
            'type'                  => $r->old( 'type',                $cust->type              ),
            'shortname'             => $r->old( 'shortname',           $cust->shortname         ),
            'corpwww'               => $r->old( 'corpwww',             $cust->corpwww           ),
            'datejoin'              => $r->old( 'datejoin',            !$cust->datejoin ?: $cust->datejoin->format( "Y-m-d" ) ) ,
            'dateleave'             => $r->old( 'dateleave',           !$cust->dateleave ?: $cust->dateleave->format( "Y-m-d" ) ),
            'status'                => $r->old( 'status',              $cust->status                ),
            'MD5Support'            => $r->old( 'MD5Support',          $cust->MD5Support            ),
            'abbreviatedName'       => $r->old( 'abbreviatedName',     $cust->abbreviatedName       ),
            'autsys'                => $r->old( 'autsys',              $cust->autsys                ),
            'maxprefixes'           => $r->old( 'maxprefixes',         $cust->maxprefixes           ),
            'peeringpolicy'         => $r->old( 'peeringpolicy',       $cust->peeringpolicy         ),
            'peeringemail'          => $r->old( 'peeringemail',        $cust->peeringemail          ),
            'peeringmacro'          => $r->old( 'peeringmacro',        $cust->peeringmacro          ),
            'peeringmacrov6'        => $r->old( 'peeringmacrov6',      $cust->peeringmacrov6        ),
            'irrdb'                 => $r->old( 'irrdb',               $cust->irrdb                 ),
            'activepeeringmatrix'   => $r->old( 'activepeeringmatrix', $cust->activepeeringmatrix   ),
            'nocphone'              => $r->old( 'nocphone',            $cust->nocphone              ),
            'noc24hphone'           => $r->old( 'noc24hphone',         $cust->noc24hphone           ),
            'nocemail'              => $r->old( 'nocemail',            $cust->nocemail              ),
            'nochours'              => $r->old( 'nochours',            $cust->nochours              ),
            'nocwww'                => $r->old( 'nocwww',              $cust->nocwww                ),
            'isReseller'            => $r->old( 'isReseller',          $cust->isReseller            ),
            'isResold'              => $r->old( 'isResold',            ( $this->resellerMode() && $cust->reseller ) ),
            'reseller'              => $r->old( 'reseller',            ( $this->resellerMode() && $cust->reseller ) ? $cust->reseller : false ),
            'peeringdb_oauth'       => $r->old( 'peeringdb_oauth',     $cust->peeringdb_oauth       ),
        ]);

        return view( 'customer/edit' )->with([
            'cust'                          => $cust,
            'irrdbs'                        => IrrdbConfig::orderBy( 'source' )->get(),
            'resellers'                     => Customer::select( [ 'id', 'name' ] )->resellerOnly()
                ->orderBy( 'name' )->get(),
        ]);
    }

    /**
     * Update a customer (set all the data needed)
     *
     * @param   CustomerRequest $r instance of the current HTTP request
     * @param   Customer        $cust
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function update( CustomerRequest $r, Customer $cust ): RedirectResponse
    {
        $cust->update( array_merge(
            $r->all(),
            [
                'lastupdatedby' => Auth::id(),
                'reseller'      => $r->isResold ? $r->reseller : null,
            ]
        ));

        Cache::forget( 'admin_home_customers' );
        AlertContainer::push( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' updated ', Alert::SUCCESS );
        return redirect( route( "customer@overview" , [ "cust" => $cust->id ] ) );
    }

    /**
     * Display the billing registration form a customer
     *
     * @param Request $r
     *
     * @param Customer $cust The Customer
     *
     * @return View
     */
    public function billingAndRegDetails( Request $r, Customer $cust ): View
    {
        $cbd = $cust->companyBillingDetail;
        $crd = $cust->companyRegisteredDetail;

        $dataBillingDetail = [];
        if( !( $cust->reseller && $this->resellerMode() ) ){
            $dataBillingDetail = [
                'billingContactName'        => $r->old( 'billingContactName',     $cbd->billingContactName ),
                'billingFrequency'          => $r->old( 'billingFrequency',       $cbd->billingFrequency ),
                'billingAddress1'           => $r->old( 'billingAddress1',        $cbd->billingAddress1 ),
                'billingAddress2'           => $r->old( 'billingAddress2',        $cbd->billingAddress2 ),
                'billingAddress3'           => $r->old( 'billingAddress3',        $cbd->billingAddress3 ),
                'billingTownCity'           => $r->old( 'billingTownCity',        $cbd->billingTownCity ),
                'billingPostcode'           => $r->old( 'billingPostcode',        $cbd->billingPostcode ),
                'billingCountry'            => $r->old( 'billingCountry', in_array( $cbd->billingCountry, array_values( Countries::getListForSelect( 'iso_3166_2' ) ), false ) ? $cbd->billingCountry : null ),
                'billingEmail'              => $r->old( 'billingEmail',           $cbd->billingEmail ),
                'billingTelephone'          => $r->old( 'billingTelephone',       $cbd->billingTelephone ),
                'purchaseOrderRequired'     => $r->old( 'purchaseOrderRequired',  $cbd->purchaseOrderRequired ),
                'invoiceMethod'             => $r->old( 'invoiceMethod',          $cbd->invoiceMethod ),
                'invoiceEmail'              => $r->old( 'invoiceEmail',           $cbd->invoiceEmail ),
                'vatRate'                   => $r->old( 'vatRate',                $cbd->vatRate ),
                'vatNumber'                 => $r->old( 'vatNumber',              $cbd->vatNumber ),
            ];
        }

        $dataRegistrationDetail = [
            'registeredName'            => $r->old( 'registeredName',             $crd->registeredName ),
            'companyNumber'             => $r->old( 'companyNumber',              $crd->companyNumber ),
            'jurisdiction'              => $r->old( 'jurisdiction',               $crd->jurisdiction ),
            'address1'                  => $r->old( 'address1',                   $crd->address1 ),
            'address2'                  => $r->old( 'address2',                   $crd->address2 ),
            'address3'                  => $r->old( 'address3',                   $crd->address3 ) ,
            'townCity'                  => $r->old( 'townCity',                   $crd->townCity ),
            'postcode'                  => $r->old( 'postcode',                   $crd->postcode ),
            'country'                   => $r->old( 'country',            in_array( $crd->country,  array_values( Countries::getListForSelect( 'iso_3166_2' ) ), false ) ? $crd->country : null ),
        ];

        Former::populate( array_merge( $dataRegistrationDetail, $dataBillingDetail ) );

        return view( 'customer/billing-registration' )->with([
            'c'                             => $cust,
            'juridictions'                  => CompanyRegisteredDetail::select( 'jurisdiction' )
                ->where( 'jurisdiction', '!=', '' )->distinct()->get()->toArray(),
            'countries'                     => Countries::getList('name' )
        ]);
    }

    /**
     * Create or edit a customer's registration / billing information
     *
     * Also sends an email notification if so configured.
     *
     * @param   BillingInformationRequest   $r      instance of the current HTTP request
     * @param   Customer                    $cust
     *
     * @return  RedirectResponse
     * @throws
     */
    public function storeBillingAndRegDetails( BillingInformationRequest $r, Customer $cust ): RedirectResponse
    {
        $ocbd = clone $cust->companyBillingDetail;
        $cbd  = $cust->companyBillingDetail;
        $crd  = $cust->companyRegisteredDetail;

        $crd->update( $r->all() );

        if( !( $cust->reseller && $this->resellerMode() ) ) {
            $cbd->update( $r->all() );
        }

        event( new CustomerBillingDetailsChangedEvent( $ocbd, $cbd ) );
        return redirect( route( "customer@overview" , [ 'cust' => $cust->id , 'tab' => 'details' ]  ) );
    }

    /**
     * Display the list of all the Customers
     *
     * @return RedirectResponse|View
     */
    public function details(): RedirectResponse|View
    {
        if( config( 'ixp_fe.customer.details_public') ) {
            return view( 'customer/details' )->with([
                'custs'                 => Customer::currentActive( true, false )->get(),
                'associates'            => false,
            ]);
        }
        return redirect()->back();
    }

    /**
     * Display the list of all associate customers
     *
     * @return RedirectResponse|View
     */
    public function associates(): RedirectResponse|View
    {
        if( config( 'ixp_fe.customer.details_public') ) {
            return view( 'customer/details' )->with([
                'custs'                 => Customer::current()->associate()
                    ->orderBy( 'name' )->get(),
                'associates'            => true,
            ]);
        }
        return redirect()->back();
    }

    /**
     * Display all the information for a customer
     *
     * @param Customer $cust
     *
     * @return RedirectResponse|View
     */
    public function detail( Customer $cust ): RedirectResponse|View
    {
        if( config( 'ixp_fe.customer.details_public') ) {
            return view( 'customer/detail' )->with([
                'c'       => $cust->load( [ 'logo',
                    'virtualInterfaces.physicalInterfaces',
                    'virtualInterfaces.vlanInterfaces.vlan',
                    'virtualInterfaces.vlanInterfaces.ipv4address',
                    'virtualInterfaces.vlanInterfaces.ipv6address',
                ] ),
                'netinfo' => NetworkInfo::vlanProtocol(),
                'rsasns'  => Router::routeServer()
                    ->groupBy( 'asn' )->get()->pluck( 'asn' )->toArray()
            ]);
        }
        return redirect()->back();
    }

    /**
     * Display the customer overview
     *
     * @param   Customer    $cust   the customer
     * @param   string|null $tab    Tab from the overview selected
     *
     * @return  View
     *
     * @throws
     */
    public function overview( Customer $cust, string $tab = null ) : View
    {
        $grapher = App::make( Grapher::class );

        // get customer's notes
        $notes = $cust->customerNotes()->orderByDesc( 'created_at' )->get();

        return view( 'customer/overview' )->with([
            'c'                         => $cust
                ->load( [ 'companyRegisteredDetail', 'companyBillingDetail',
                    'virtualInterfaces.physicalInterfaces.switchPort.switcher.infrastructureModel',
                    'virtualInterfaces.physicalInterfaces.switchPort.switcher.cabinet.location',
                    'virtualInterfaces.physicalInterfaces.switchPort.patchPanelPort.patchPanel',
                    'virtualInterfaces.vlanInterfaces.ipv6address',
                    'virtualInterfaces.vlanInterfaces.ipv4address',
                    'virtualInterfaces.vlanInterfaces.layer2Addresses',
                    'virtualInterfaces.vlanInterfaces.vlan.vlanInterfaces',
                    'customerToUser.user.user2FA',
                    'customerToUser.user.customers',
                    'customerToUser.userLoginHistories',
                    'contacts.contactRoles',
                    'consoleServerConnections.consoleServer.cabinet.location',
                    'resoldCustomers', 'irrdbConfig',
                ] ),
            'customers'                 => Customer::select([ 'id', 'name' ])->current()
                ->orderBy( 'name' )->get()->keyBy( 'id' )->toArray(),
            'netInfo'                   => NetworkInfo::vlanProtocol(),
            'crossConnects'             => $cust->patchPanelPorts()
                ->with( [ 'patchPanel.cabinet.location' ] )->masterPort()->get(),
            'crossConnectsHistory'      => $cust->patchPanelPortHistories()
                ->with( [ 'patchPanelPort.patchPanel.cabinet.location' ] )->masterPort()->get(),
            'aggregateGraph'            => $cust->isGraphable() ? $grapher->customer( $cust ) : false,
            'grapher'                   => $grapher,
            'rsclient'                  => $cust->routeServerClient(),
            'as112client'               => $cust->isAS112Client(),
            'as112UiActive'             => $this->as112UiActive(),
            'countries'                 => Countries::getList('name' ),
            'tab'                       => strtolower( $tab ) ?: false,
            'notes'                     => $notes,
            'notesInfo'                 => CustomerNote::analyseForUser( $notes, $cust, Auth::getUser() ),
        ]);
    }

    /**
     * Display the customer overview peers
     *
     * @param  Request  $r
     * @param  Customer  $cust
     *
     * @return JsonResponse
     */
    public function loadPeersFrag( Request $r, Customer $cust ): JsonResponse
    {
        return response()->json( [
            'success' => true,
            'htmlFrag' => view('customer/overview-tabs/peers')->with([
                'peers' => CustomerAggregator::getPeeringManagerArrayByType( $cust, Vlan::peeringManager()->orderBy( 'number' )->get(), [ 4,6 ] ) ?: false
            ])->render()
        ] );
    }

    /**
     * Display the Welcome Email form
     *
     * @param   Customer $cust
     *
     * @return  View
     *
     * @throws
     */
    public function welcomeEmail( Customer $cust ) : View
    {
        $emails = [];
        foreach( $cust->users as $user ){
            if( $email = filter_var( $user->email, FILTER_VALIDATE_EMAIL ) ) {
                $emails[] = $email;
            }
        }

        Former::populate( [
            'to'                        => $cust->nocemail,
            'cc'                        => implode( ',', $emails ),
            'bcc'                       => config('identity.email'),
            'subject'                   => config('identity.name'). ' :: Welcome Mail',
        ] );

        return view( 'customer/welcome-email' )->with([
            'c'         => $cust,
            'body'      => view( "customer/emails/welcome-email" )->with([
                'c'                     => $cust,
                'admins'                => $cust->customerToUser()->custAdmin()->get(),
                'netinfo'               => NetworkInfo::vlanProtocol(),
                'identityEmail'         => config('identity.email'),
                'identityOrgname'       => config('identity.orgname'),
            ])->render()
        ]);
    }

    /**
     * Send the welcome email to a customer
     *
     * @param WelcomeEmailRequest   $r
     * @param Customer              $cust
     *
     * @return RedirectResponse|View
     */
    public function sendWelcomeEmail( WelcomeEmailRequest $r, Customer $cust ): RedirectResponse|View
    {
        $mailable = new WelcomeEmail( $cust, $r );

        try {
            $mailable->checkIfSendable();
        } catch( \Exception $e ) {
            AlertContainer::push( $e->getMessage(), Alert::DANGER );
            return back()->withInput();
        }

        Mail::send( $mailable );
        AlertContainer::push( "Welcome email sent.", Alert::SUCCESS );
        return redirect( route( "customer@overview", [ "cust" => $cust->id ] ) );
    }

    /**
     * Recap the information that will be deleted with the customer
     *
     * @param   Customer   $cust customer
     *
     * @return  View|RedirectResponse
     */
    public function deleteRecap( Customer $cust ): RedirectResponse|View
    {
        // cannot delete a customer with active cross connects:
        if( $cust->patchPanelPorts->isNotEmpty() ) {
            AlertContainer::push( "This customer has active patch panel ports. Please cease "
                . "these (or set them to awaiting cease and unset the customer link in the patch panel "
                . "port) to proceed with deleting this customer.", Alert::DANGER
            );
            return redirect( route( "customer@overview", [ 'cust' => $cust->id ] ) );
        }

        // cannot delete a customer with fan out ports:
        if( Customer::leftJoin( 'virtualinterface AS vi', 'vi.custid', 'cust.id' )
            ->leftJoin( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->whereNotNull( 'pi.fanout_physical_interface_id' )
            ->whereNotNull( 'reseller' )->where( 'cust.id', $cust->id )->count() ){
                        AlertContainer::push( "This customer has is a resold customer with fan out physical "
                            . "interfaces. Please delete these manually before proceeding with deleting the customer.",
                            Alert::DANGER
                        );
                        return redirect( route( "customer@overview", [ 'cust' => $cust->id ] ) );
        }

        return view( 'customer/delete' )->with([
            'c'         => $cust,
        ]);
    }

    /**
     * Delete a customer and everything related !!
     *
     * @param   Customer    $cust
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function delete( Customer $cust ) : RedirectResponse
    {
        if( CustomerAggregator::deleteObject( $cust ) ) {
            AlertContainer::push( "Customer <em>{$cust->getFormattedName()}</em> deleted.", Alert::SUCCESS );
            Cache::forget( 'admin_home_customers' );
        } else {
            AlertContainer::push( "Customer could not be deleted. Please open a GitHub bug report.", Alert::DANGER );
        }
        return redirect( route( "customer@list" ) );
    }
}