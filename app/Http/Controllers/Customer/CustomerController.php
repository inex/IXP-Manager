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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use App, Auth, Cache, Countries, Former, Mail;

use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Log;
use IXP\Exceptions\GeneralException;
use IXP\Utils\CountryUtils;
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
        $clearFilters = $r->boolean( "clear-filters" );
        $filtersSubmitted = $r->boolean( "filters-submitted" );

        if( $clearFilters ) {
            $r->session()->forget( [
                "cust-list-state",
                "cust-list-type",
                "cust-list-peering-policy",
                "cust-list-route-server",
                "cust-list-joined-from",
                "cust-list-joined-to",
                "cust-list-left-from",
                "cust-list-left-to",
                "cust-list-reseller-type",
                "cust-list-show-resellers",
                "cust-list-show-resold",
                "cust-list-show-other",
                "cust-list-reseller",
                "cust-list-current-only",
                "cust-list-tag",
                "cust-list-filter-tags",
                "cust-list-include-tags",
                "cust-list-exclude-tags",
                "cust-list-include-tags-mode",
                "cust-list-exclude-tags-mode",
            ] );
        }

        $multiValueFilter = function( string $input, string $sessionKey, array $allowed, bool $integerValues = false ) use( $r, $filtersSubmitted ): array {
            $values = $filtersSubmitted || $r->has( $input )
                ? (array)$r->input( $input, [] )
                : (array)$r->session()->get( $sessionKey, [] );
            $values = $integerValues ? array_map( "intval", $values ) : array_map( "strval", $values );
            $values = array_values( array_unique( array_filter(
                $values,
                static fn( int|string $value ): bool => isset( $allowed[ $value ] )
            ) ) );

            if( count( $values ) ) {
                $r->session()->put( $sessionKey, $values );
            } else {
                $r->session()->remove( $sessionKey );
            }

            return $values;
        };

        $states = $multiValueFilter( "state", "cust-list-state", Customer::$CUST_STATUS_TEXT, true );
        $types = $multiValueFilter( "type", "cust-list-type", Customer::$CUST_TYPES_TEXT, true );
        $peeringPolicies = $multiValueFilter( "peering-policy", "cust-list-peering-policy", Customer::$PEERING_POLICIES );
        $routeServers = $multiValueFilter( "route-server", "cust-list-route-server", [ "yes" => true, "no" => true ] );

        $validDate = static function( mixed $value ): bool {
            if( !is_string( $value ) || !preg_match( "/^(\d{4})-(\d{2})-(\d{2})$/", $value, $matches ) ) {
                return false;
            }

            return checkdate( (int)$matches[ 2 ], (int)$matches[ 3 ], (int)$matches[ 1 ] );
        };

        $dateFilters = [];
        foreach( [ "joined-from", "joined-to", "left-from", "left-to" ] as $dateFilter ) {
            $sessionKey = "cust-list-" . $dateFilter;

            if( $filtersSubmitted ) {
                $value = trim( (string)$r->input( $dateFilter, "" ) );
                if( $validDate( $value ) ) {
                    $dateFilters[ $dateFilter ] = $value;
                    $r->session()->put( $sessionKey, $value );
                } else {
                    $dateFilters[ $dateFilter ] = false;
                    $r->session()->remove( $sessionKey );
                }
            } else {
                $value = $r->session()->get( $sessionKey, false );
                $dateFilters[ $dateFilter ] = $validDate( $value ) ? $value : false;
            }
        }

        $joinedFrom = $dateFilters[ "joined-from" ];
        $joinedTo = $dateFilters[ "joined-to" ];
        $leftFrom = $dateFilters[ "left-from" ];
        $leftTo = $dateFilters[ "left-to" ];

        $resellers = $this->resellerMode()
            ? Customer::select( [ "id", "name", "abbreviatedName" ] )->resellerOnly()->orderBy( "name" )->get()
            : collect();

        if( !$this->resellerMode() ) {
            $showResellers = true;
            $showResold = true;
            $showOther = true;
            $r->session()->remove( "cust-list-show-resellers" );
            $r->session()->remove( "cust-list-show-resold" );
            $r->session()->remove( "cust-list-show-other" );
            $r->session()->remove( "cust-list-reseller-type" );
        } elseif( $filtersSubmitted ) {
            $resellerTypes = array_values( array_unique( array_filter(
                array_map( "strval", (array)$r->input( "reseller-type", [] ) ),
                static fn( string $type ): bool => in_array( $type, [ "reseller", "resold", "other" ], true )
            ) ) );
            $showAllResellerTypes = count( $resellerTypes ) === 0;
            $showResellers = $showAllResellerTypes || in_array( "reseller", $resellerTypes, true );
            $showResold = $showAllResellerTypes || in_array( "resold", $resellerTypes, true );
            $showOther = $showAllResellerTypes || in_array( "other", $resellerTypes, true );
            $r->session()->put( "cust-list-show-resellers", $showResellers );
            $r->session()->put( "cust-list-show-resold", $showResold );
            $r->session()->put( "cust-list-show-other", $showOther );
        } else {
            $showResellers = (bool)$r->session()->get( "cust-list-show-resellers", true );
            $showResold = (bool)$r->session()->get( "cust-list-show-resold", true );
            $showOther = (bool)$r->session()->get( "cust-list-show-other", true );
        }

        $resellerTypes = array_keys( array_filter( [
            "reseller" => $showResellers,
            "resold"   => $showResold,
            "other"    => $showOther,
        ] ) );

        if( !$this->resellerMode() ) {
            $resellerId = false;
            $r->session()->remove( "cust-list-reseller" );
        } elseif( ( $resellerId = $r->input( "reseller" ) ) !== null ) {
            $resellerId = (int)$resellerId;
            if( $resellers->contains( "id", $resellerId ) ) {
                $r->session()->put( "cust-list-reseller", $resellerId );
            } else {
                $resellerId = false;
                $r->session()->remove( "cust-list-reseller" );
            }
        } elseif( $r->session()->exists( "cust-list-reseller" ) ) {
            $resellerId = (int)$r->session()->get( "cust-list-reseller" );
        } else {
            $resellerId = false;
        }

        if( $filtersSubmitted ) {
            $showCurrentOnly = $r->boolean( "current-only" );
            $r->session()->put( "cust-list-current-only", $showCurrentOnly );
        } else {
            $showCurrentOnly = (bool)$r->session()->get( "cust-list-current-only", true );
        }

        $tags = CustomerTag::orderBy( "display_as" )->get()->keyBy( "id" )->toArray();
        $includeTagsMode = $filtersSubmitted
            ? (string)$r->input( "include-tags-mode", "all" )
            : (string)$r->session()->get( "cust-list-include-tags-mode", "all" );

        if( !in_array( $includeTagsMode, [ "all", "any" ], true ) ) {
            $includeTagsMode = "all";
        }

        if( $filtersSubmitted ) {
            $r->session()->put( "cust-list-include-tags-mode", $includeTagsMode );
        }

        $excludeTagsMode = $filtersSubmitted
            ? (string)$r->input( "exclude-tags-mode", "any" )
            : (string)$r->session()->get( "cust-list-exclude-tags-mode", "any" );

        if( !in_array( $excludeTagsMode, [ "all", "any" ], true ) ) {
            $excludeTagsMode = "any";
        }

        if( $filtersSubmitted ) {
            $r->session()->put( "cust-list-exclude-tags-mode", $excludeTagsMode );
        }

        if( $filtersSubmitted ) {
            $includeTags = array_values( array_unique( array_filter(
                array_map( "intval", (array)$r->input( "include-tags", [] ) ),
                fn( int $tagId ): bool => isset( $tags[ $tagId ] )
            ) ) );
            $excludeTags = array_values( array_unique( array_filter(
                array_map( "intval", (array)$r->input( "exclude-tags", [] ) ),
                fn( int $tagId ): bool => isset( $tags[ $tagId ] )
            ) ) );

            $r->session()->put( "cust-list-include-tags", $includeTags );
            $r->session()->put( "cust-list-exclude-tags", $excludeTags );
            $r->session()->remove( "cust-list-filter-tags" );
            $r->session()->remove( "cust-list-tag" );
        } else {
            $includeTags = (array)$r->session()->get( "cust-list-include-tags", [] );
            $excludeTags = (array)$r->session()->get( "cust-list-exclude-tags", [] );
        }

        $includeTags = array_values( array_filter( $includeTags, fn( int $tagId ): bool => isset( $tags[ $tagId ] ) ) );
        $excludeTags = array_values( array_filter( $excludeTags, fn( int $tagId ): bool => isset( $tags[ $tagId ] ) ) );
        $filterTags = count( $includeTags ) > 0 || count( $excludeTags ) > 0;

        return view( 'customer/list' )->with([
            'states'                => $states,
            'clearFilters'          => $clearFilters,
            'types'                 => $types,
            'peeringPolicies'       => $peeringPolicies,
            'routeServers'          => $routeServers,
            'joinedFrom'            => $joinedFrom,
            'joinedTo'              => $joinedTo,
            'leftFrom'              => $leftFrom,
            'leftTo'                => $leftTo,
            'showResellers'         => $showResellers,
            'showResold'            => $showResold,
            "showOther"             => $showOther,
            "resellerTypes"          => $resellerTypes,
            'resellerId'            => $resellerId,
            'resellers'             => $resellers,
            'showCurrentOnly'       => $showCurrentOnly ?? false,
            'filterTags'            => $filterTags,
            'includeTags'           => $includeTags,
            'includeTagsMode'       => $includeTagsMode,
            'excludeTags'           => $excludeTags,
            'excludeTagsMode'       => $excludeTagsMode,
            'tags'                  => $tags,
            'custs'                 => Customer::with( [ 'resellerObject', 'tags', 'companyRegisteredDetail' ] )
                ->selectRaw( 'cust.*' )
                ->withExists( [ 'vlanInterfaces as route_server_client' => function( Builder $q ) {
                    $q->where( 'vlaninterface.rsclient', true )
                        ->where( function( Builder $protocolQuery ) {
                            $protocolQuery->where( 'vlaninterface.ipv4enabled', true )
                                ->orWhere( 'vlaninterface.ipv6enabled', true );
                        } );
                } ] )
                ->when( $filterTags, function( Builder $q ) use( $includeTags, $excludeTags, $includeTagsMode, $excludeTagsMode ) {
                    if( $includeTagsMode === "any" && count( $includeTags ) ) {
                        $q->whereHas( "tags", fn( Builder $tagQuery ) => $tagQuery->whereIn( "cust_tag.id", $includeTags ) );
                    } else {
                        foreach( $includeTags as $tagId ) {
                            $q->whereHas( "tags", fn( Builder $tagQuery ) => $tagQuery->where( "cust_tag.id", $tagId ) );
                        }
                    }

                    if( $excludeTagsMode === "all" && count( $excludeTags ) ) {
                        $q->where( function( Builder $excludeQuery ) use( $excludeTags ) {
                            foreach( $excludeTags as $tagId ) {
                                $excludeQuery->orWhereDoesntHave( "tags", fn( Builder $tagQuery ) => $tagQuery->where( "cust_tag.id", $tagId ) );
                            }
                        } );
                    } elseif( count( $excludeTags ) ) {
                        $q->whereDoesntHave( "tags", fn( Builder $tagQuery ) => $tagQuery->whereIn( "cust_tag.id", $excludeTags ) );
                    }

                    return $q;
                } )->when( count( $states ), function( Builder $q ) use( $states ) {
                    return $q->whereIn( 'cust.status', $states );
                } )->when( count( $types ), function( Builder $q ) use( $types ) {
                    return $q->whereIn( 'cust.type', $types );
                } )->when( count( $peeringPolicies ), function( Builder $q ) use( $peeringPolicies ) {
                    return $q->whereIn( 'cust.peeringpolicy', $peeringPolicies );
                } )->when( $joinedFrom, function( Builder $q ) use( $joinedFrom ) {
                    return $q->whereDate( "cust.datejoin", ">=", $joinedFrom );
                } )->when( $joinedTo, function( Builder $q ) use( $joinedTo ) {
                    return $q->whereDate( "cust.datejoin", "<=", $joinedTo );
                } )->when( $leftFrom, function( Builder $q ) use( $leftFrom ) {
                    return $q->whereDate( "cust.dateleave", ">=", $leftFrom );
                } )->when( $leftTo, function( Builder $q ) use( $leftTo ) {
                    return $q->whereDate( "cust.dateleave", "<=", $leftTo );
                } )->when( $routeServers === [ "yes" ], function( Builder $q ) {
                    return $q->whereRaw( Customer::SQL_CUST_CURRENT )
                        ->whereHas( 'vlanInterfaces', function( Builder $vlanInterfaceQuery ) {
                            $vlanInterfaceQuery->where( 'vlaninterface.rsclient', true )
                                ->where( function( Builder $protocolQuery ) {
                                    $protocolQuery->where( 'vlaninterface.ipv4enabled', true )
                                        ->orWhere( 'vlaninterface.ipv6enabled', true );
                                } );
                        } );
                } )->when( $routeServers === [ "no" ], function( Builder $q ) {
                    return $q->whereRaw( Customer::SQL_CUST_CURRENT )
                        ->whereDoesntHave( 'vlanInterfaces', function( Builder $vlanInterfaceQuery ) {
                            $vlanInterfaceQuery->where( 'vlaninterface.rsclient', true )
                                ->where( function( Builder $protocolQuery ) {
                                    $protocolQuery->where( 'vlaninterface.ipv4enabled', true )
                                        ->orWhere( 'vlaninterface.ipv6enabled', true );
                                } );
                        } );
                } )->when( $this->resellerMode() && !( $showResellers && $showResold && $showOther ), function( Builder $q ) use( $showResellers, $showResold, $showOther ) {
                    return $q->where( function( Builder $categoryQuery ) use( $showResellers, $showResold, $showOther ) {
                        if( !$showResellers && !$showResold && !$showOther ) {
                            return $categoryQuery->whereRaw( "0 = 1" );
                        }

                        if( $showResellers ) {
                            $categoryQuery->orWhere( "cust.isReseller", true );
                        }

                        if( $showResold ) {
                            $categoryQuery->orWhereNotNull( "cust.reseller" );
                        }

                        if( $showOther ) {
                            $categoryQuery->orWhere( function( Builder $otherQuery ) {
                                $otherQuery->where( "cust.isReseller", false )
                                    ->whereNull( "cust.reseller" );
                            } );
                        }

                        return $categoryQuery;
                    } );
                } )->when( $resellerId, function( Builder $q ) use( $resellerId ) {
                    return $q->where( 'cust.reseller', $resellerId );
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
            'datejoin'              => $r->old( 'datejoin',            !$cust->datejoin ?: Carbon::parse($cust->datejoin)->format( "Y-m-d" ) ) ,
            'dateleave'             => $r->old( 'dateleave',           !$cust->dateleave ?: Carbon::parse($cust->dateleave)->format( "Y-m-d" ) ),
            'status'                => $r->old( 'status',              $cust->status                ),
            'MD5Support'            => $r->old( 'MD5Support',          $cust->MD5Support            ),
            'abbreviatedName'       => $r->old( 'abbreviatedName',     $cust->abbreviatedName       ),
            'autsys'                => $r->old( 'autsys',              (string) $cust->autsys       ),
            'maxprefixes'           => $r->old( 'maxprefixes',         (string) $cust->maxprefixes   ),
            'maxprefixesv6'         => $r->old( 'maxprefixesv6',       (string) $cust->maxprefixesv6 ),
            'peeringpolicy'         => $r->old( 'peeringpolicy',       $cust->peeringpolicy         ),
            'peeringemail'          => $r->old( 'peeringemail',        $cust->peeringemail          ),
            'peeringmacro'          => $r->old( 'peeringmacro',        $cust->peeringmacro          ),
            'peeringmacrov6'        => $r->old( 'peeringmacrov6',      $cust->peeringmacrov6        ),
            'irrdb'                 => $r->old( 'irrdb',               (string) $cust->irrdb                 ),
            'activepeeringmatrix'   => $r->old( 'activepeeringmatrix', (string) $cust->activepeeringmatrix   ),
            'nocphone'              => $r->old( 'nocphone',            $cust->nocphone              ),
            'noc24hphone'           => $r->old( 'noc24hphone',         $cust->noc24hphone           ),
            'nocemail'              => $r->old( 'nocemail',            $cust->nocemail              ),
            'nochours'              => $r->old( 'nochours',            $cust->nochours              ),
            'nocwww'                => $r->old( 'nocwww',              $cust->nocwww                ),
            'isReseller'            => $r->old( 'isReseller',          (string) $cust->isReseller            ),
            'isResold'              => $r->old( 'isResold',            ( $this->resellerMode() && $cust->reseller ) ? '1' : '0' ),
            'reseller'              => $r->old( 'reseller',            ( $this->resellerMode() && $cust->reseller ) ? (string) $cust->reseller : null ),
            'peeringdb_oauth'       => $r->old( 'peeringdb_oauth',     (string) $cust->peeringdb_oauth       ),
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
                'purchaseOrderNumber'       => $r->old( 'purchaseOrderNumber',    $cbd->purchaseOrderNumber ),
                'invoiceMethod'             => $r->old( 'invoiceMethod',          $cbd->invoiceMethod ),
                'invoiceEmail'              => $r->old( 'invoiceEmail',           $cbd->invoiceEmail ),
                'vatRate'                   => $r->old( 'vatRate',                $cbd->vatRate ),
                'vatNumber'                 => $r->old( 'vatNumber',              $cbd->vatNumber ),
                'billingNotes'              => $r->old( 'billingNotes',           $cbd->notes ),
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
            'country'                   => $r->old( 'country',                    CountryUtils::isKnownBy($crd->country) ? $crd->country : null ),
            'notes'                     => $r->old( 'notes',                      $crd->notes ),
        ];

        Former::populate( array_merge( $dataRegistrationDetail, $dataBillingDetail ) );

        return view( 'customer/billing-registration' )->with([
            'c'                             => $cust,
            'jurisdictions'                 => CompanyRegisteredDetail::select( 'jurisdiction' )
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
            // resolve name clash before saving
            $cbd->update( array_merge( $r->all(), [ 'notes' => $r->billingNotes ] ) );
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
     * @param Customer $cust the customer
     * @param string|null $tab Tab from the overview selected
     *
     * @return  View
     * @throws GeneralException
     * @throws BindingResolutionException
     */
    public function overview( Customer $cust, ?string $tab = null ) : View
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
                'peers' => CustomerAggregator::getPeeringManagerArrayByType( $cust, Vlan::peeringManager()->orderBy( 'number' )->get()->toArray(), [ 4,6 ] ) ?: false
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
        Log::notice("Welcome email sent for {$cust->id}|{$cust->name}");
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
        if ($redirect = $this->redirectIfCustomerHasResellerCustomers($cust)) {
            return $redirect;
        } else if ($redirect = $this->redirectIfCustomerHasActiveCrossConnects($cust)) {
            return $redirect;
        } else if ($redirect = $this->redirectIfCustomerHasActiveFanoutPorts($cust)) {
            return $redirect;
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
        if ($redirect = $this->redirectIfCustomerHasResellerCustomers($cust)) {
            return $redirect;
        } else if ($redirect = $this->redirectIfCustomerHasActiveCrossConnects($cust)) {
            return $redirect;
        } else if ($redirect = $this->redirectIfCustomerHasActiveFanoutPorts($cust)) {
            return $redirect;
        }

        if( CustomerAggregator::deleteObject( $cust ) ) {
            AlertContainer::push( "Customer <em>{$cust->getFormattedName()}</em> deleted.", Alert::SUCCESS );
            Cache::forget( 'admin_home_customers' );
        } else {
            AlertContainer::push( "Customer could not be deleted. Please open a GitHub bug report.", Alert::DANGER );
        }
        return redirect( route( "customer@list" ) );
    }

    private function redirectIfCustomerHasResellerCustomers( Customer $cust ): ?RedirectResponse
    {
        if( $cust->isReseller && Customer::whereReseller( $cust->id )->notDeleted()->count() > 0 ) {
            AlertContainer::push( "This customer is a reseller still associated with active resold customers. Please ".
                " disassociate their customers before proceeding with deleting the reselling customer.", Alert::DANGER );
            return redirect( route( "customer@overview", [ 'cust' => $cust->id ] ) );
        }
        return null;
    }

    private function redirectIfCustomerHasActiveCrossConnects( Customer $cust ): ?RedirectResponse
    {
        if( $cust->patchPanelPorts->isNotEmpty() ) {
            AlertContainer::push( "This customer has active patch panel ports. Please cease "
                . "these (or set them to awaiting cease and unset the customer link in the patch panel "
                . "port) to proceed with deleting this customer.", Alert::DANGER
            );
            return redirect( route( "customer@overview", [ 'cust' => $cust->id ] ) );
        }
        return null;
    }

    private function redirectIfCustomerHasActiveFanoutPorts( Customer $cust ): ?RedirectResponse
    {
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
        return null;
    }
}