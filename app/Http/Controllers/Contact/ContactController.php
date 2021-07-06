<?php

namespace IXP\Http\Controllers\Contact;

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

use Auth, Former, Redirect, stdClass;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\{
    Aggregators\ContactGroupAggregator,
    Contact,
    ContactGroup,
    Customer,
    User
};

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Contact Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Contact
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var Contact
     */
    protected $object = null;

    /**
     * @inheritdoc
     */
    protected static $route_prefix = 'contact';

    /**
     * The minimum privileges required to access this controller.
     *
     * If you set this to less than the superuser, you need to manage privileges and access
     * within your own implementation yourself.
     *
     * @var int
     */
    public static $minimum_privilege = User::AUTH_CUSTADMIN;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = ( object )[
            'model'             => Contact::class,
            'pagetitle'         => 'Contacts',
            'titleSingular'     => 'Contact',
            'nameSingular'      => 'contact',
            'documentation'     => 'https://docs.ixpmanager.org/usage/contacts/',
            'defaultAction'     => 'list',
            'defaultController' => 'ContactController',
            'listOrderBy'       => 'name',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'contact',
        ];

        switch( $privs = Auth::getUser()->privs() ) {
            case User::AUTH_SUPERUSER:
                $this->feParams->listColumns = [
                    'customer'  => [
                        'title'      => 'Customer',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'custid'
                    ],
                    'name'      => 'Name',
                    'position'  => 'Position',
                    'email'     => 'Email',
                    'created_at'       => [
                        'title'     => 'Created',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ],
                    'updated_at'       => [
                        'title'     => 'Updated',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ]
                ];

                if( config('contact_group.types.ROLE') ) {
                    $this->feParams->listColumns = array_merge(
                        $this->feParams->listColumns,
                        [
                            'contact_roles'     => [
                                'title'         => 'Role',
                                'type'          => self::$FE_COL_TYPES[ 'LABEL' ],
                                'array'   => [
                                    'delimiter'         => ',',
                                    'replace'           => '',
                                    'index'             => 'name',
                                ]
                            ]
                        ]
                    );
                }
                break;

            case User::AUTH_CUSTADMIN || User::AUTH_CUSTUSER:
                $this->feParams->pagetitle = 'Your Contacts';

                $this->feParams->listColumns = [
                    'name'      => 'Name',
                    'position'  => 'Position',
                    'email'     => 'Email',
                    'phone'     => 'Phone',
                    'created_at'       => [
                        'title'     => 'Created',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ],
                ];
                break;

            default:
                $this->unauthorized();
        }

        // display the same information in the view as the list
        if( $privs === User::AUTH_SUPERUSER ) {
            $this->feParams->viewColumns = array_merge(
                $this->feParams->listColumns,
                [
                    'phone'     => 'Phone',
                    'mobile'    => 'Mobile',
                    'notes'     => [
                        'title'         => 'Notes',
                        'type'          => self::$FE_COL_TYPES[ 'PARSDOWN' ]
                    ]
                ]
            );
        } else {
            $this->feParams->viewColumns = $this->feParams->listColumns;
        }
    }

    /**
     * Gets a listing of contacts or a single one if an ID is provided
     *
     * @param stdClass $feParams
     * @param int|null $id
     * @param int|null $role
     * @param int|null $cgid
     *
     * @return array
     */
    private function getFeList( stdClass $feParams, int $id = null, int $role = null, int $cgid = null ): array
    {
        $isSuperUser = Auth::getUser()->isSuperUser();
        $query = Contact::select( [ 'contact.*', 'cust.name AS customer', 'cust.id AS custid' ])
            ->leftJoin( 'cust', 'cust.id', 'contact.custid'  )
            ->when( $id , function ( Builder $query, $id ) {
                return $query->where('contact.id', $id );
            })
            ->when( !$isSuperUser, function ( Builder $query ) {
                return $query->where('cust.id', Auth::getUser()->custid );
            })
            ->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
                return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
            });

        if( config('contact_group.types.ROLE') ) {
            $groupid = $role ?: ($cgid ?: null);
            $query->when( $groupid , function ( Builder $query, $groupid ) {
                return $query->leftJoin( 'contact_to_group', function( $join ) {
                    $join->on( 'contact.id', 'contact_to_group.contact_id');
                })->where('contact_to_group.contact_group_id', $groupid );
            });

            if( $isSuperUser ) {
                $query->with( 'contactRoles', 'contactGroups' );
            }
        }
        return $query->get()->toArray();
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int|null $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     *
     * @throws
     */
    protected function listGetData( ?int $id = null ): array
    {
        $role = $cg = null;
        $cgs = [];

        if( config('contact_group.types.ROLE') ) {
            $activeGroups   = ContactGroupAggregator::getGroupNamesTypeArray( false, false , true);

            if( !in_array( $role = request()->role, array_column( $activeGroups[ "ROLE" ], 'id' ), false ) ) {
                $role = null;
            }

            if( $cg = request()->cgid ) {
                // flatten the multi dimensional array
                foreach( $activeGroups as $gname => $gvalue ) {
                    foreach( $gvalue as $index => $val ){
                        $cgs[ $index ] = $val[ 'name' ];
                    }
                }

                if( !array_key_exists( $cg, $cgs ) ) {
                    $cg = null;
                }
            }

            $this->data[ 'params' ] = [
                'role'              => $role,
                'roles'             => $activeGroups[ "ROLE" ],
                'cg'                => $cg,
                'contactGroups'     => $cgs,
            ];
        } else {
            $this->data[ 'params' ] = [
                'role'              => $role,
                'roles'             => [],
                'cg'                => $cg,
                'contactGroups'     => $cgs,
            ];
        }

        return $this->getFeList( $this->feParams, $id, $role, $cg );
    }

    /**
     * @inheritdoc
     */
    protected function preView(): void
    {
        if( Auth::getUser()->custid !== (int)$this->data[ 'item' ][ 'custid' ]  && !Auth::getUser()->isSuperUser() ) {
            $this->unauthorized();
        }

        if( isset( $this->data[ 'item' ][ 'contact_groups' ] ) && count( $this->data[ 'item' ][ 'contact_groups' ] ) ) {
            $this->feParams->viewColumns = array_merge(
                $this->feParams->viewColumns,
                [
                    'contact_groups'     => [
                        'title'         => 'Group',
                        'type'          => self::$FE_COL_TYPES[ 'LABEL' ],
                        'array'   => [
                            'delimiter'         => ',',
                            'replace'           => '<br/>',
                            'array_index'       => [
                                'type', 'name'
                            ]
                        ]
                    ]
                ]
            );
        }
    }

    /**
     * Display the form to add/edit an object
     *
     * @return array
     *
     * @throws
     */
    protected function createPrepareForm(): array
    {
        $this->setRedirectSession();
        $data = $this->getContactsData();

        if( $cust = Customer::find( request()->cust ) ) {
            Former::populate( [
                'custid'                    => $cust->id,
            ] );
        }

        return [
            'object'                => $this->object,
            'groupsForContact'      => [],
            'custs'                 => Customer::orderBy( 'name' )->get(),
            'roles'                 => $data[ 'roles' ],
            'allGroups'             => $data[ 'allGroups' ],
        ];
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $r
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $r ): bool|RedirectResponse
    {

        $this->checkForm( $r );
        $custid = Auth::getUser()->isSuperUser() ? $r->custid : Auth::getUser()->custid;

        $this->object = Contact::make(
            array_merge( $r->all(), [
                'creator'       => Auth::getUser()->username,
                'lastupdatedby' => Auth::id()
            ] )
        );
        $this->object->custid = $custid;
        $this->object->save();

        if( !$this->addGroupsToObject( $r->roles ?? [] ) ) {
            return redirect( route( self::route_prefix() . '@edit', [ 'id' => $this->object->id ] ) );
        }

        return true;
    }

    /**
     * Display the form to edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     *
     * @throws
     */
    protected function editPrepareForm( int $id ): array
    {
        $this->setRedirectSession();
        $this->object   = Contact::findOrFail( $id );
        $data           = $this->getContactsData();

        if( Auth::getUser()->custid !== $this->object->customer->id && !Auth::getUser()->isSuperUser() ){
            $this->unauthorized();
        }

        $contactDetail = [
            'name'                      => request()->old( 'name',      $this->object->name     ),
            'position'                  => request()->old( 'position',  $this->object->position ),
            'custid'                    => request()->old( 'custid',    $this->object->custid   ),
            'email'                     => request()->old( 'email',     $this->object->email    ),
            'phone'                     => request()->old( 'phone',     $this->object->phone    ),
            'mobile'                    => request()->old( 'mobile',    $this->object->mobile   ),
            'notes'                     => request()->old( 'notes',     $this->object->notes    ),
        ];

        $contactGroupDetail = [];
        $contactGroup       =  ContactGroupAggregator::getGroupNamesTypeArray( false, $this->object->id );

        foreach( $data[ 'allGroups' ] as $gname => $gvalue ) {
            foreach( $gvalue as $g ){
                $contactGroupDetail[ $gname . '_' . $g[ 'id' ] ] =  request()->old( $gname . '_' . $g[ 'id' ] , isset( $contactGroup[ $gname ][  $g[ 'id' ] ] ) ? 1 : 0 ) ;
            }
        }

        Former::populate( array_merge( $contactDetail, $contactGroupDetail ) );

        return [
            'object'                => $this->object,
            'groupsForContact'      => $this->object->contactGroupsAll()->get()->keyBy( 'id' )->toArray(),
            'custs'                 => Customer::orderBy( 'name' )->get(),
            'roles'                 => $data[ 'roles' ],
            'allGroups'             => $data[ 'allGroups' ],
        ];
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doUpdate( Request $r, int $id ): bool|RedirectResponse
    {
        $this->object = Contact::findOrFail( $id );
        $this->checkForm( $r );

        $custid = Auth::getUser()->custid;

        if( Auth::getUser()->isSuperUser() ) {
            $custid = $r->custid;
        }

        $this->object->fill(
            array_merge( $r->all(), [
                'creator'       => Auth::getUser()->username,
                'lastupdatedby' => Auth::id()
            ] )
        );

        $this->object->custid = $custid;
        $this->object->save();

        $objectGroups   = $this->object->contactGroupsAll()->get()->keyBy( 'id' )->toArray();
        $groupToAdd     = array_diff( $r->roles ?? [], array_keys( $objectGroups ) );
        $groupToDelete  = array_diff( array_keys( $objectGroups ), $r->roles ?? [] );

        if( !$this->addGroupsToObject( $groupToAdd ) ) {
            return redirect( route( self::route_prefix() . '@edit', [ 'id' => $this->object->id ] ) );
        }

        // save the object if addGroupsToObject was success
        foreach( $groupToDelete as $gToDelete ) {
            if( ( $cgroup = ContactGroup::find( $gToDelete ) ) && $cgroup->active ) {
                $this->object->contactGroupsAll()->detach( $cgroup->id );
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function postStoreRedirect(): ?string
    {
        if( !Auth::getUser()->isSuperUser() ) {
            return route( 'contact@list' );
        }

        $redirect = session()->get( "contact_post_store_redirect" );
        session()->remove( "contact_post_store_redirect" );

        // retrieve the customer ID
        if( $redirect === 'customer@overview' ) {
            return route( 'customer@overview' , [ 'cust' => $this->object->custid , 'tab' => 'contacts' ] );
        }

        return null;
    }

    /**
     * Function which can be over-ridden to perform any pre-deletion tasks
     *
     * You can stop the deletion by returning false but you should also add a
     * message to explain why (to the AlertContainer).
     *
     * The object to be deleted is available via `$this->>object`
     *
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete(): bool
    {
        session()->remove( 'ixp_contact_delete_custid' );
        if( Auth::getUser()->isSuperUser() ) {
            // keep the customer ID for redirection on success
            $this->request->session()->put( "ixp_contact_delete_custid", $this->object->customer->id );
        } elseif( $this->object->customer->id !== Auth::getUser()->custid ) {
            AlertContainer::push( 'You are not authorised to delete this contact.', Alert::DANGER );
            return false;
        }

        $this->object->contactGroupsAll()->detach();
        return true;
    }

    /**
     * Allow D2F implementations to override where the post-delete redirect goes.
     *
     * To implement this, have it return a valid route url (e.g. `return route( "route-name" );`
     *
     * @return null|string
     */
    protected function postDeleteRedirect(): ?string
    {
        // retrieve the customer ID
        if( strpos( request()->headers->get('referer', "" ), "customer/overview" ) ) {
            if( $custid = $this->request->session()->get( "ixp_contact_delete_custid" ) ) {
                $this->request->session()->remove( "ixp_contact_delete_custid" );
                return route( "customer@overview", [ "cust" => $custid, "tab" => "contacts" ] );
            }
        }
        return route( 'contact@list' );
    }

    /**
     * Check if the form is valid
     *
     * @param $r
     *
     * @return void
     */
    public function checkForm( Request $r ): void
    {
        $rules = [
            'name'                  => 'required|string|max:255',
            'position'              => 'nullable|string|max:255',
            'email'                 => 'nullable|email',
            'phone'                 => 'nullable|string|max:50',
            'mobile'                => 'nullable|string|max:50',
            'notes'                 => 'nullable|string|max:255',
        ];

        if( Auth::getUser()->isSuperUser() ){
            $rules = array_merge( $rules,
                [ 'custid' => 'required|integer|exists:cust,id' ]
            );
        }

        $r->validate( $rules );
    }

    /**
     * Set the session in order to redirect to the good location
     *
     * @return void
     */
    private function setRedirectSession(): void
    {
        session()->remove( "contact_post_store_redirect" );

        // check if we come from the customer overview or the contact list
        if( strpos( request()->headers->get('referer', '' ), "customer/overview" ) ) {
            session()->put( 'contact_post_store_redirect',     'customer@overview' );
            session()->put( 'contact_post_store_redirect_cid', request()->cust );
        } else {
            session()->put( 'contact_post_store_redirect', 'contact@list' );
            session()->put( 'contact_post_store_redirect_cid', null );
        }
    }

    /**
     * return an array of contacts data
     *
     * @return array
     */
    private function getContactsData(): array
    {
        if( config('contact_group.types.ROLE') ) {
            return [
                'roles'     => ContactGroupAggregator::getGroupNamesTypeArray( 'ROLE' )[ "ROLE" ],
                'allGroups' => ContactGroupAggregator::getGroupNamesTypeArray( false, false, true )
            ];
        }
        return [ 'roles' => null, 'allGroups' => [] ];
    }

    /**
     * Add contact group to the contact object
     *
     * @param array $groups
     *
     * @return bool
     */
    private function addGroupsToObject( array $groups ): bool
    {
        foreach( $groups as $index => $groupid ) {
            if( $cgroup = ContactGroup::find( $groupid ) ) {
                if( $cgroup->limited_to !== 0 ) {
                    $nbContactForCust = Contact::when( $groupid , function ( Builder $query, $groupid ) {
                        return $query->leftJoin( 'contact_to_group', function( $join ) {
                            $join->on( 'contact.id', 'contact_to_group.contact_id');
                        })->where('contact_to_group.contact_group_id', $groupid );
                    })
                        ->where( 'custid', $this->object->custid  )
                        ->get()->count();

                    if( $cgroup->limited_to <= $nbContactForCust && !$this->object->contactGroupsAll->contains( 'id', $groupid ) ) {
                        AlertContainer::push( "Contact group " . $cgroup->type . " : " . $cgroup->name . " has a limited membership and is full." , Alert::DANGER );
                        return false;
                    }
                }

                if( !$this->object->contactGroupsAll->contains( 'id', $groupid ) ) {
                    $this->object->contactRoles()->attach( $cgroup );
                }
            }
        }
        return true;
    }
}