<?php /** @noinspection ALL */

namespace IXP\Http\Controllers\Contact;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth, D2EM, Former, Redirect, Validator;

use Entities\{
    Contact             as ContactEntity,
    ContactGroup        as ContactGroupEntity,
    Customer            as CustomerEntity,
    User                as UserEntity
};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use IXP\Http\Controllers\Doctrine2Frontend;
use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};
use Log;


/**
 * Contact Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactController extends Doctrine2Frontend
{
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
    public static $minimum_privilege = UserEntity::AUTH_CUSTADMIN;

    /**
     * This function sets up the frontend controller
     */
    public function feInit()
    {

        $this->feParams         = ( object )[

            'entity'            => ContactEntity::class,
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
            case UserEntity::AUTH_SUPERUSER:

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
                            'role'     => [
                                'title'         => 'Role',
                                'type'          => self::$FE_COL_TYPES[ 'LABEL' ],
                                'array'   => [
                                    'delimiter'         => ',',
                                    'replace'           => ''
                                ]
                            ]
                        ]
                    );
                }

                break;

            case UserEntity::AUTH_CUSTADMIN || UserEntity::AUTH_CUSTUSER:


                $this->feParams->pagetitle = 'Your Contacts';

                $this->feParams->listColumns = [
                    'id'        => [ 'title' => 'UID', 'display' => false ],
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
    protected function preView()
    {
        if( !Auth::getUser()->isSuperUser() && Auth::getUser()->getCustomer()->getId() != $this->data[ 'item' ][ 'custid' ] ) {
            $this->unauthorized();
        }

        if( isset( $this->data[ "item" ][ "group" ] ) ) {
            $this->feParams->viewColumns = array_merge(
                $this->feParams->listColumns,
                [
                    'group'     => [
                        'title'         => 'Group',
                        'type'          => self::$FE_COL_TYPES[ 'LABEL' ],
                        'explode'   => [
                            'delimiter'         => ',',
                            'replace'           => '<br/>'
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
     * @return array
     *
     * @throws
     */
    protected function listGetData( $id = null )
    {

        $role = $cg = null;
        $cgs = [];

        if( config('contact_group.types.ROLE') ) {
            $groups = D2EM::getRepository( ContactGroupEntity::class )->getGroupNamesTypeArray( false, false , true);
            $allGroups = D2EM::getRepository( ContactGroupEntity::class )->getGroupNamesTypeArray();

            if( !in_array( $role = request()->input( "role" ) , array_column( $groups[ "ROLE" ], 'id')  ) ){
                $role = null;
            }

            if( $cg = request()->input( "cgid" ) ) {

                foreach( $groups as $gname => $gvalue ){
                    foreach( $gvalue as $index => $val ){
                        $cgs[$index] = $val[ 'name' ];
                    }
                }

                if( !array_key_exists( $cg, $cgs ) ) {
                    $cg = null;
                }
            }

            $this->data[ 'params' ] = [
                'role'              => $role,
                'roles'             => $groups[ "ROLE" ],
                'cg'                => $cg,
                'contactGroups'     => $cgs,
                'AllContactGroups'  => $allGroups[ "ROLE" ]
            ];

        } else {

            $this->data[ 'params' ] = [
                'role'              => $role,
                'roles'             => [],
                'cg'                => $cg,
                'contactGroups'     => $cgs,
                'AllContactGroups'  => [],
            ];
        }

        return D2EM::getRepository( ContactEntity::class )->getAllForFeList( $this->feParams, $id, $role, $cg );
    }



    /**
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     *
     * @throws
     */
    protected function addEditPrepareForm( $id = null ): array
    {
        $old = request()->old();
        session()->remove( "contact_post_store_redirect" );

        // check if we come from the customer overview or the contact list
        if( strpos( request()->headers->get('referer', "" ), "customer/overview" ) ) {
            session()->put( 'contact_post_store_redirect',     'customer@overview' );
            session()->put( 'contact_post_store_redirect_cid', request()->input('cust', null ) );
        } else {
            session()->put( 'contact_post_store_redirect', 'contact@list' );
            session()->put( 'contact_post_store_redirect_cid', null );
        }

        if( config('contact_group.types.ROLE') ) {
            $roles      = D2EM::getRepository( ContactGroupEntity::class )->getGroupNamesTypeArray( 'ROLE' )[ "ROLE" ];
            $allGroups  = D2EM::getRepository( ContactGroupEntity::class )->getGroupNamesTypeArray( false, false, true);
        } else {
            $roles     = null;
            $allGroups = [];
        }


        if( $id !== null ) {
            if( !( $this->object = D2EM::getRepository( ContactEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            if( !Auth::getUser()->isSuperUser() && Auth::getUser()->getCustomer()->getId() !== $this->object->getCustomer()->getId() ){
                $this->unauthorized();
            }

            $contactDetail = [
                'name'                      => request()->old( 'name',      $this->object->getName() ),
                'position'                  => request()->old( 'position',  $this->object->getPosition() ),
                'custid'                    => request()->old( 'custid',    $this->object->getCustomer()->getId() ),
                'email'                     => request()->old( 'email',     $this->object->getEmail() ),
                'phone'                     => request()->old( 'phone',     $this->object->getPhone() ),
                'mobile'                    => request()->old( 'mobile',    $this->object->getMobile() ),
                'notes'                     => request()->old( 'notes',     $this->object->getNotes() ),
            ];

            $contactGroupDetail = [];

            $contactGroup =  D2EM::getRepository( ContactGroupEntity::class )->getGroupNamesTypeArray( false, $this->object->getId() );

            foreach( $allGroups as $gname => $gvalue ) {
                foreach( $gvalue as $g ){
                    $contactGroupDetail[ $gname . '_' . $g[ 'id' ] ] =  request()->old( $gname . '_' . $g[ 'id' ] , isset( $contactGroup[ $gname ][  $g[ 'id' ] ] ) ? 1 : 0 ) ;
                }
            }

            Former::populate( array_merge( $contactDetail, $contactGroupDetail ) );

        } else {
            if( request()->input( "cust" ) && ( $cust = D2EM::getRepository( CustomerEntity::class )->find( request()->input( "cust" ) ) ) ){
                Former::populate( [
                    'custid'                    => $cust->getId(),
                ] );
            }
        }

        return [
            'object'                => $this->object,
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
            'roles'                 => $roles,
            'allGroups'             => $allGroups,
        ];
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
        $rules = [
            'name'                  => 'required|string|max:255',
            'position'              => 'nullable|string|max:255',
            'email'                 => 'nullable|email',
            'phone'                 => 'nullable|string|max:50',
            'mobile'                => 'nullable|string|max:50',
            'notes'                 => 'nullable|string|max:255',
        ];

        if( Auth::getUser()->isSuperUser() ){
            $rules = array_merge( $rules, [ 'custid' => 'required|integer|exists:Entities\Customer,id' ] );
        }

        $validator = Validator::make( $request->all(), $rules );


        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( ContactEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new ContactEntity;
            D2EM::persist( $this->object );

            $this->object->setCreated(  new \DateTime  );
            $this->object->setCreator(  Auth::getUser()->getUsername() );
        }

        if( Auth::getUser()->isSuperUser() ) {
            $this->object->setCustomer( D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid' ) ) );
        } else {
            $this->object->setCustomer( Auth::getUser()->getCustomer() );
        }

        $this->object->setName(              $request->input( 'name'            ) );
        $this->object->setPosition(          $request->input( 'position'        ) );
        $this->object->setEmail(             $request->input( 'email'           ) );
        $this->object->setPhone(             $request->input( 'phone'           ) );
        $this->object->setMobile(            $request->input( 'mobile'          ) );
        $this->object->setNotes(             $request->input( 'notes'           ) );

        $this->object->setLastupdated(      new \DateTime  );
        $this->object->setLastupdatedby(    Auth::getUser()->getId() );

        $allGroups = config('contact_group.types.ROLE') ? D2EM::getRepository( ContactGroupEntity::class )->getGroupNamesTypeArray( ) : [];


        $groups = [];

        foreach( $allGroups as $gname => $gvalue ) {
            foreach( $gvalue as $role ){

                if( $request->input( $gname . '_' . $role[ "id" ] ) ) {

                    /** @var ContactGroupEntity $group */
                    if( $group = D2EM::getRepository( ContactGroupEntity::class )->find( $role[ "id" ] ) ) {

                        if( $group->getLimitedTo() != 0 ) {
                            $contactsWithGroupForCustomer = D2EM::getRepository( ContactGroupEntity::class )->countForCustomer( $this->object->getCustomer(), $role[ "id" ] );

                            if( !$this->object->getGroups()->contains( $group ) && $group->getLimitedTo() <= $contactsWithGroupForCustomer ) {
                                AlertContainer::push( "Contact group " . $gname . " : " . $role[ "name" ] . " has a limited membership and is full." , Alert::DANGER );
                                return Redirect::back()->withInput( $request->all() );
                            }
                        }

                        if( !$this->object->getGroups()->contains( $group ) ) {
                            $this->object->addGroup( $group );
                            $group->addContact( $this->object );
                        }

                        $groups[] = $group;
                    }

                }
            }
        }


        foreach( $this->object->getGroups() as $key => $group ) {
            if( $group->getActive() ){
                if( !in_array( $group, $groups ) ) {
                    $this->object->getGroups()->remove( $key );
                }
            }

        }

        D2EM::flush();

        return true;
    }


    /**
     * @inheritdoc
     */
    protected function postStoreRedirect()
    {
        if( !Auth::getUser()->isSuperUser() ) {
            return route( 'contact@list' );
        } else {

            $redirect = session()->get( "contact_post_store_redirect" );
            session()->remove( "contact_post_store_redirect" );

            // retrieve the customer ID
            if( $redirect === 'customer@overview' ) {
                return route( 'customer@overview' , [ 'id' => $this->object->getCustomer()->getId() , 'tab' => 'contacts' ] );
            }

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
            $this->request->session()->put( "ixp_contact_delete_custid", $this->object->getCustomer()->getId() );
        } else {
            if( $this->object->getCustomer()->getId() != Auth::getUser()->getCustomer()->getId() ) {
                AlertContainer::push( 'You are not authorised to delete this contact.', Alert::DANGER );
                return false;
            }
        }

        return true;
    }


    /**
     * Allow D2F implementations to override where the post-delete redirect goes.
     *
     * To implement this, have it return a valid route url (e.g. `return route( "route-name" );`
     *
     * @return null|string
     */
    protected function postDeleteRedirect()
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
