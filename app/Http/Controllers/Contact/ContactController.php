<?php

namespace IXP\Http\Controllers\Contact;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, Former, Redirect;

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\{
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
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactController extends EloquentController
{
    /**
     * The object being added / edited
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
            'entity'            => Contact::class,
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

        switch( Auth::getUser()->getPrivs() ) {
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
                    'created'       => [
                        'title'     => 'Created',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ],
                    'lastupdated'       => [
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
                    'id'        => [
                        'title' => 'UID',
                        'display' => false
                    ],
                    'name'      => 'Name',
                    'position'  => 'Position',
                    'email'     => 'Email',
                    'phone'     => 'Phone',
                    'created'       => [
                        'title'     => 'Created',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ],
                ];
                break;

            default:
                $this->unauthorized();
        }

        // display the same information in the view as the list
        if( !Auth::getUser()->isSuperUser() ) {
            $this->feParams->viewColumns = $this->feParams->listColumns;
        } else {
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
        }
    }

    /**
     * @inheritdoc
     */
    protected function preView(): void
    {
        if( !Auth::getUser()->isSuperUser() && Auth::getUser()->getCustomer()->getId() != $this->data[ 'item' ][ 'custid' ] ) {
            $this->unauthorized();
        }

        if( isset( $this->data[ "item" ][ "contact_groups" ] ) ) {
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
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     *
     * @throws
     */
    protected function listGetData( $id = null ): array
    {
        $role = $cg = null;
        $cgs = [];

        if( config('contact_group.types.ROLE') ) {
            $activeGroups   = ContactGroup::getGroupNamesTypeArray( false, false , true);
            $allGroups      = ContactGroup::getGroupNamesTypeArray();

            if( !in_array( $role = request()->role, array_column( $activeGroups[ "ROLE" ], 'id' ), false ) ) {
                $role = null;
            }

            if( $cg = request()->input( "cgid" ) ) {
                // flatten the multi dimensional array
                foreach( $activeGroups as $gname => $gvalue ) {
                    foreach( $gvalue as $index => $val ){
                        $cgs[$index] = $val[ 'name' ];
                    }
                }

                if( !array_key_exists( $cg, $allGroups['ROLE'] ) ) {
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

        return Contact::getFeList( $this->feParams, $id, $role, $cg );
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
        if( strpos( request()->headers->get('referer', "" ), "customer/overview" ) ) {
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
                'roles'    => ContactGroup::getGroupNamesTypeArray( 'ROLE' )[ "ROLE" ],
                'allGroups' => ContactGroup::getGroupNamesTypeArray( false, false, true)
            ];
        }

        return [ 'roles' => null, 'allGroups' => [] ];
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
            'custs'                 => Customer::getListAsArray(),
            'roles'                 => $data[ 'roles' ],
            'allGroups'             => $data[ 'allGroups' ],
        ];
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
    protected function editPrepareForm( $id = null ): array
    {
        $this->setRedirectSession();
        $data = $this->getContactsData();

        $this->object = Contact::findOrFail( $id );

        if( !Auth::getUser()->isSuperUser() && Auth::getUser()->getCustomer()->getId() !== $this->object->customer->id ){
            $this->unauthorized();
        }

        $contactDetail = [
            'name'                      => request()->old( 'name',      $this->object->name ),
            'position'                  => request()->old( 'position',  $this->object->position ),
            'custid'                    => request()->old( 'custid',    $this->object->custid ),
            'email'                     => request()->old( 'email',     $this->object->email ),
            'phone'                     => request()->old( 'phone',     $this->object->phone ),
            'mobile'                    => request()->old( 'mobile',    $this->object->mobile ),
            'notes'                     => request()->old( 'notes',     $this->object->notes ),
        ];

        $contactGroupDetail = [];

        $contactGroup =  ContactGroup::getGroupNamesTypeArray( false, $this->object->id );

        foreach( $data[ 'allGroups' ] as $gname => $gvalue ) {
            foreach( $gvalue as $g ){
                $contactGroupDetail[ $gname . '_' . $g[ 'id' ] ] =  request()->old( $gname . '_' . $g[ 'id' ] , isset( $contactGroup[ $gname ][  $g[ 'id' ] ] ) ? 1 : 0 ) ;
            }
        }

        Former::populate( array_merge( $contactDetail, $contactGroupDetail ) );

        return [
            'object'                => $this->object,
            'groupsForContact'      => $this->object->contactGroupsAll()->get()->keyBy( 'id' )->toArray(),
            'custs'                 => Customer::getListAsArray(),
            'roles'                 => $data[ 'roles' ],
            'allGroups'             => $data[ 'allGroups' ],
        ];
    }

    /**
     * Check if the form is valid
     *
     * @param $request
     *
     * @return void
     */
    public function checkForm( Request $request ): void
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
            $rules = array_merge( $rules, [ 'custid' => [
                'required', 'integer',
                function( $attribute, $value, $fail ) use ($request) {
                    if( !Customer::whereId( $value )->exists() ) {
                        return $fail( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' is invalid / does not exist.' );
                    }
                }
            ] ] );
        }

        $request->validate( $rules );
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
                if( $cgroup->limited_to != 0 ) {
                    $nbContactForCust = Contact::getForCustomer( $this->object->customer, $groupid )->count();

                    if( !$this->object->contactGroupsAll()->contains( 'id', $groupid ) && $cgroup->limited_to <= $nbContactForCust ) {
                        AlertContainer::push( "Contact group " . $cgroup->type . " : " . $cgroup->name . " has a limited membership and is full." , Alert::DANGER );
                        return false;
                    }
                }

                if( !$this->object->contactGroupsAll->contains( 'id', $groupid ) ) {
                    $this->object->contactRoles()->save( $cgroup );
                }
            }
        }
        return true;
    }
    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $request )
    {
        $this->checkForm( $request );
        $custid = Auth::getUser()->getCustomer()->getId();

        if( Auth::getUser()->isSuperUser() ) {
            $custid = $request->custid;
        }

        $this->object = Contact::create(
            array_merge( $request->all(), [
                'created'       => now(),
                'creator'       => Auth::getUser()->getUsername(),
                'custid'        => $custid,
                'lastupdated'   => now(),
                'lastupdatedby' => Auth::getUser()->getId()
            ] )
        );

        if( !$this->addGroupsToObject( $request->roles ?? [] ) ) {
            return Redirect::back()->withInput( $request->all() );
        }

        return true;
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     * @param int $id
     *
     * @return bool|RedirectResponse
     *
     */
    public function doUpdate( Request $request, int $id )
    {
        $this->object = Contact::findOrFail( $id );
        $this->checkForm( $request );

        $custid = Auth::getUser()->getCustomer()->getId();

        if( Auth::getUser()->isSuperUser() ) {
            $custid = $request->custid;
        }

        $this->object->update(
            array_merge( $request->all(), [
                'created'       => now(),
                'creator'       => Auth::getUser()->getUsername(),
                'custid'        => $custid,
                'lastupdated'   => now(),
                'lastupdatedby' => Auth::getUser()->getId()
            ] )
        );

        $objectGroups = $this->object->contactGroupsAll()->get()->keyBy( 'id' )->toArray();
        $groupToAdd     = array_diff( $request->roles ?? [], array_keys( $objectGroups ) );
        $groupToDelete  = array_diff( array_keys( $objectGroups ), $request->roles ?? [] );

        if( !$this->addGroupsToObject( $groupToAdd ) ) {
            return Redirect::back()->withInput( $request->all() );
        }

        foreach( $groupToDelete as $gToDelete ) {
            if( $cgroup = ContactGroup::find( $gToDelete ) ) {
                if( $cgroup->active ) {
                    $this->object->contactGroupsAll()->detach( $cgroup->id );
                }
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
            return route( 'customer@overview' , [ 'id' => $this->object->customer->id , 'tab' => 'contacts' ] );
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
        } else {
            if( $this->object->customer->id !== Auth::getUser()->getCustomer()->getId() ) {
                AlertContainer::push( 'You are not authorised to delete this contact.', Alert::DANGER );
                return false;
            }
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
                return route( "customer@overview", [ "id" => $custid, "tab" => "contacts" ] );
            }
        }

        return route( 'contact@list' );
    }
}