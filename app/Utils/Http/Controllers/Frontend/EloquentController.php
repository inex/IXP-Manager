<?php

namespace IXP\Utils\Http\Controllers\Frontend;

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
use Auth, Log, Route, Str;

use Illuminate\Database\Eloquent\Model;
use IXP\Models\User;

use IXP\Http\Controllers\Controller;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Exceptions\GeneralException;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Frontend Functions
 *
 * Based on Barry's original code from:
 *     https://github.com/opensolutions/OSS-Framework/blob/master/src/OSS/Controller/Action/Trait/Doctrine2Frontend.php
 *
 *
 * @see        http://docs.ixpmanager.org/dev/frontend-crud/
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Utils\Http\Controllers\Frontend
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class EloquentController extends Controller
{
    /**
     * Parameters used by the frontend controller
     *
     * @var object Parameters used by the frontend controller
     */
    protected $feParams = null;

    /**
     * Array for data that is passed to the templates
     *
     * @var array
     */
    protected $data = [];

    /**
     * The object being created / edited / viewed
     */
    protected $object = null;

    /**
     * The http request
     */
    protected $request = null;

    /**
     * Allow controllers to override the default successful store message
     *
     * @var string
     */
    protected $store_alert_success_message = null;

    /**
     * The URL prefix to use.
     *
     * Automatically determined based on the controller name if not set.
     *
     * @var string|null
     */
    protected static $route_prefix = null;

    /**
     * The minimum privileges required to access this controller.
     *
     * If you set this to less than the superuser, you need to manage privileges and access
     * within your own implementation yourself.
     *
     * @var int
     */
    public static $minimum_privilege = User::AUTH_SUPERUSER;

    /**
     * Is this a read only controller?
     *
     * @var boolean
     */
    public static $read_only = false;

    /**
     * Should we allow a read only controller to delete
     *
     * @var boolean
     */
    public static $allow_delete_for_read_only = false;

    /**
     * Do we disable to edit?
     *
     * @var boolean
     */
    public static $disable_edit = false;

    /**
     * Where are the base views (list/edit/view).
     *
     * @var string
     */
    protected static $baseViews = 'frontend';

    /**
     * Sometimes we need to pass a custom request object for validation / authorisation.
     *
     * Set the name of the function here and the route for store will be pointed to it instead of doStore()
     *
     * @var string
     */
    protected static $storeFn = 'store';

    /**
     * Column / table data types when displaying data.
     * @var array
     */
    static public $FE_COL_TYPES = [
        'HAS_ONE'           => 'hasOne',
        'CUSTOM_HAS_ONE'    => 'customHasOne',
        'DATETIME'          => 'datetime',
        'DATE'              => 'date',
        'TIME'              => 'time',
        'UNIX_TIMESTAMP'    => 'unix_timestamp',
        'SCRIPT'            => 'script',
        'SPRINTF'           => 'sprintf',
        'REPLACE'           => 'replace',
        'XLATE'             => 'xlate',
        'YES_NO'            => 'yes_no',
        'INVERSE_YES_NO'    => 'yes_no_inverse',
        'YES_NO_NULL'       => 'yes_no_null',
        'PARSDOWN'          => 'parsdown',
        'CONST'             => 'const',
        'LABEL'             => 'label',
        'ARRAY'             => 'array',
        'INTEGER'           => 'integer',
        'LIMIT'             => 'limit',
        'TEXT'              => 'text',
        'COUNTRY'           => 'country',
        'WHO_IS_PREFIX'     => 'who_is_prefix',
        'JSON'              => 'json',
    ];

    /**
     * The class's initialisation method.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->feInit();
            $this->data[ 'col_types' ] = self::$FE_COL_TYPES;

            return $next($request);
        });
    }

    /**
     * This must be overridden.
     */
    abstract protected function feInit();

    /**
     * The default routes for a Doctrine2Frontend class
     *
     * @return void
     */
    public static function routes(): void
    {
        // add leading slash to class name for absolute resolution:
        $class = '\\' . static::class;
        $route_prefix = self::route_prefix();

        Route::group( [ 'prefix' => $route_prefix ], static function() use ( $class, $route_prefix ) {
            Route::get( 'list',      $class . '@list' )->name( $route_prefix . '@list' );
            Route::get( 'view/{id}', $class . '@view' )->name( $route_prefix . '@view' );

            if( !static::$read_only ) {
                Route::get(  'create',          $class . '@create'  )->name( $route_prefix . '@create'  );
                Route::delete( 'delete/{id}',   $class . '@delete'  )->name( $route_prefix . '@delete'  );
                if( !static::$disable_edit ) {
                    Route::get( 'edit/{id}',    $class . '@edit'    )->name( $route_prefix . '@edit'    );
                    Route::put( 'update/{id}',  $class . '@update'  )->name( $route_prefix . '@update'  );
                }
                Route::post( 'store', $class . '@' . static::$storeFn )->name( $route_prefix . '@store' );
            }
        });

        $class::additionalRoutes( $route_prefix );
    }

    /**
     * Work out the route prefix
     *
     * @return null|string
     */
    public static function route_prefix(): ?string
    {
        $class = static::class;

        if( $class::$route_prefix ) {
            return $class::$route_prefix;
        }
        return Str::kebab( substr( class_basename( $class ), 0, -10 ) );
    }

    /**
     * Function which can be over-ridden to add additional routes
     *
     * If you don't want to use the defaults as well as some additional, override
     * `routes()` instead.
     *
     * @param string $route_prefix
     *
     * @return void
     */
    protected static function additionalRoutes( string $route_prefix ): void {}

    /**
     * Provide array of table rows for the list action (and view action)
     *
     * @param int|null $id The `id` of the row to load for `view` action. `null` if `list` action.
     *
     * @return array
     */
    abstract protected function listGetData( ?int $id = null ): array;

    /**
     * Function which can be over-ridden to perform any pre-list tasks
     *
     * E.g. adding elements to $this->data for the pre/post-amble templates.
     *
     * @return void
     */
    protected function preList():void {}

    /**
     * Function which can be over-ridden to perform a "can list" authorisation step.
     *
     * Must return null (for okay) or a RedirectResponse (`redirect()`).
     *
     * @return RedirectResponse|null ?RedirectResponse
     */
    protected function canList(): ?RedirectResponse
    {
        return null;
    }

    /**
     *  Include the necessary templates to the list function
     *
     * @return void
     */
    protected function listIncludeTemplates(): void
    {
        $this->data[ 'view' ][ 'listEmptyMessage']      = $this->resolveTemplate( 'list-empty-message',     false );
        $this->data[ 'view' ][ 'listScriptExtra']       = $this->resolveTemplate( 'js/list-extra',          false );
        $this->data[ 'view' ][ 'listHeadOverride']      = $this->resolveTemplate( 'list-head-override',     false );
        $this->data[ 'view' ][ 'listRowOverride']       = $this->resolveTemplate( 'list-row-override',      false );
        $this->data[ 'view' ][ 'listPreamble']          = $this->resolveTemplate( 'list-preamble',          false );
        $this->data[ 'view' ][ 'listPostamble']         = $this->resolveTemplate( 'list-postamble',         false );
        $this->data[ 'view' ][ 'listRowMenu']           = $this->resolveTemplate( 'list-row-menu',          false );
        $this->data[ 'view' ][ 'pageHeaderPreamble']    = $this->resolveTemplate( 'page-header-preamble',   false );
        $this->data[ 'view' ][ 'listScript' ]           = $this->resolveTemplate( 'js/list' );
        $this->data[ 'view' ][ 'common' ]               = $this->resolveTemplate( 'js/common',              false );
    }

    /**
     * List the contents of a database table.
     *
     * @param Request $param
     *
     * @return View|RedirectResponse
     */
    public function list( Request $param )
    {
        if( ( $r = $this->canList() ) !== null ) {
            return $r;
        }

        $this->data[ 'rows' ] = $this->listGetData();
        $this->listIncludeTemplates();
        $this->preList();

        return $this->display( 'list' );
    }

    /**
     * Provide single object for view.
     *
     * @param int $id The `id` of the row to load for `view` action.
     *
     * @return array
     */
    protected function viewGetData( int $id ): array
    {
        $data = $this->listGetData( $id );

        if( is_array( $data ) && reset( $data ) ) {
            // get first value of the array
            return $data[0];
        }

        abort( 404, "No Data" );
    }

    /**
     * Function which can be over-ridden to perform any pre-view tasks
     *
     * E.g. adding elements to $this->view for the pre/post-amble templates.
     *
     * @return void
     */
    protected function preView(): void {}

    /**
     * View an object
     *
     * @param Request   $r      The HTTP Request object
     * @param int       $id     The `id` of the row to load for `view` action.
     *
     * @return View
     */
    public function view( Request $r, int $id ): View
    {
        $this->data[ 'item' ]                         = $this->viewGetData( $id ) ;
        $this->data[ 'view' ][ 'viewPreamble']        = $this->resolveTemplate( 'view-preamble',      false );
        $this->data[ 'view' ][ 'viewPostamble']       = $this->resolveTemplate( 'view-postamble',     false );
        $this->data[ 'view' ][ 'viewRowOverride']     = $this->resolveTemplate( 'view-row-override',  false );
        $this->data[ 'view' ][ 'viewScript' ]         = $this->resolveTemplate( 'js/view',            false );
        $this->data[ 'view' ][ 'common' ]             = $this->resolveTemplate( 'js/common',         false );

        $this->preView();

        return $this->display( 'view' );
    }

    /**
     * Prepares data for the create form
     *
     * @return array
     *
     * @throws GeneralException
     */
    protected function createPrepareForm(): array
    {
        throw new GeneralException( 'For non-read-only Eloquent2Frontend controllers, you must override this method.' );
    }

    /**
     * Prepares data for the edit form
     *
     * @param int $id
     *
     * @return array
     *
     * @throws GeneralException
     */
    protected function editPrepareForm( int $id ): array
    {
        throw new GeneralException( 'For non-read-only Eloquent2Frontend controllers, you must override this method.' );
    }

    /**
     * Common set up tasks for add and edit actions.
     *
     * @return void
     */
    protected function addEditSetup() : void
    {
        $this->data[ 'view' ][ 'editForm']               = $this->resolveTemplate( 'edit-form' );
        $this->data[ 'view' ][ 'editPreamble']           = $this->resolveTemplate( 'edit-preamble',         false );
        $this->data[ 'view' ][ 'editPostamble']          = $this->resolveTemplate( 'edit-postamble',        false );
        $this->data[ 'view' ][ 'editHeaderPreamble']     = $this->resolveTemplate( 'edit-header-preamble',  false );
        $this->data[ 'view' ][ 'editScript' ]            = $this->resolveTemplate( 'js/edit',               false );
    }

    /**
     * Create an object
     *
     * @return View
     *
     * @throws GeneralException
     */
    public function create(): View
    {
        $this->data[ 'params' ] = $this->createPrepareForm();
        $this->data[ 'params' ]['isAdd'] = true;
        $this->addEditSetup();
        return $this->display( 'edit' );
    }

    /**
     * Edit an object
     *
     * @param int $id ID of the object to edit
     *
     * @return view
     *
     * @throws
     */
    public function edit( int $id ): View
    {
        $this->data[ 'params' ] = $this->editPrepareForm( $id );
        $this->data[ 'params' ]['isAdd'] = false;
        $this->addEditSetup();
        return $this->display('edit' );
    }

    /**
     * Function to do the actual validation of the create/edit form..
     *
     * @param Request $r
     *
     * @throws GeneralException
     */
    public function checkForm( Request $r ): void
    {
        throw new GeneralException( 'For non-read-only Eloquent2Frontend controllers, you must override this method.' );
    }

    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $r
     *
     * @return RedirectResponse|bool
     *
     * @throws GeneralException
     */
    public function doStore( Request $r )
    {
        throw new GeneralException( 'For non-read-only Eloquent2Frontend controllers, you must override this method.' );
    }

    /**
     * Function to do the actual update of the submitted object.
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return RedirectResponse|bool
     *
     * @throws GeneralException
     */
    public function doUpdate( Request $r, int $id )
    {
        throw new GeneralException( 'For non-read-only Doctrine2Frontend controllers, you must override this method.' );
    }

    /**
     * Action for storing a new object
     *
     * @param Request $r
     *
     * @return RedirectResponse|bool
     *
     * @throws
     */
    public function store( Request $r )
    {
        $storeResult = $this->doStore( $r );

        if( $storeResult !== true ) {
            return $storeResult;
        }

        $this->postFlush( 'create' );

        Log::notice( ( Auth::check() ? Auth::getUser()->username : 'A public user' ) . ' created'
            . ' ' . $this->feParams->nameSingular . ' with ID ' . $this->object->id );

        AlertContainer::push( $this->store_alert_success_message ?? $this->feParams->titleSingular . " created.", Alert::SUCCESS );

        return redirect()->to( $this->postStoreRedirect() ?? route( self::route_prefix() . '@' . 'list' ) );
    }

    /**
     * Action for updating an object
     *
     * @param Request   $r
     * @param int       $id
     *
     * @return RedirectResponse|bool
     *
     * @throws GeneralException
     */
    public function update( Request $r, int $id )
    {
        $updateResult = $this->doUpdate( $r, $id );

        if( $updateResult !== true ) {
            return $updateResult;
        }

        $this->postFlush( 'update' );

        Log::notice( ( Auth::check() ? Auth::getUser()->username : 'A public user' ) . ' updated'
            . ' ' . $this->feParams->nameSingular . ' with ID ' . $this->object->id );

        AlertContainer::push( $this->update_alert_success_message ?? $this->feParams->titleSingular . " updated.", Alert::SUCCESS );

        return redirect()->to( $this->postStoreRedirect() ?? route( self::route_prefix() . '@' . 'list' ) );
    }

    /**
     * Allow D2F implementations to override where the post-store redirect goes.
     *
     * To implement this, have it return a valid route name
     *
     * @return string|null
     */
    protected function postStoreRedirect(): ?string
    {
        return null;
    }

    /**
     * Optional method to be overridden if a D2F controllers needs to perform post-database flush actions
     *
     * @param string $action Either 'add', 'edit', 'delete'
     *
     * @return bool
     */
    protected function postFlush( string $action ): bool
    {
        return true;
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
        return true;
    }

    /**
     * Delete an object
     *
     * @param Request $r
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( Request $r ): RedirectResponse
    {
        $this->object = $this->feParams->model::findOrFail( $r->id );

        $this->request = $r;

        if( $this->preDelete() ) {
            $this->object->delete();
            $this->postFlush( 'delete' );
            AlertContainer::push( $this->feParams->titleSingular . " deleted.", Alert::SUCCESS );
        }
        if( $url = $this->postDeleteRedirect() ) {
            return redirect()->to( $url );
        }

        return redirect()->route( self::route_prefix() . '@' . 'list' );
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
        return null;
    }

    /**
     * Displays the standard Frontend template or the controllers overridden version.
     *
     * @see _resolveTemplate()
     *
     * @param string $tpl The template to display
     *
     * @return View
     */
    protected function display( string $tpl ): View
    {
        $this->feParams->route_prefix = self::route_prefix();

        return view( $this->resolveTemplate( $tpl ) )->with( [
            'data'          => $this->data,
            'feParams'      => $this->feParams
        ]);
    }

    /**
     * Resolves the standard Frontend template or the controllers overridden version.
     *
     * All frontend actions have their own template: `frontend/{$action}.foil.php` which is
     * displayed by default. You can however override these by creating a template named:
     * `{$controller}/{$action}.foil.php`. This function looks for an overriding template
     * and displays that if it exists, otherwise it displays the default.
     *
     * This will also work for subdirectories: e.g. `$tpl = forms/add.html` is also valid.
     *
     * @param string    $tpl            The template to display
     * @param bool      $quitOnMissing  If a template is not found, this normally throws a 404. If this is set to false, the function returns false instead.
     *
     * @return bool|string The template to use of false if none found
     */
    protected function resolveTemplate( string $tpl, bool $quitOnMissing = true )
    {
        if( view()->exists( $this->feParams->viewFolderName . "/{$tpl}" ) ) {
            return $this->feParams->viewFolderName . "/{$tpl}";
        }

        if( view()->exists( static::$baseViews . "/{$tpl}"  ) ) {
            return static::$baseViews . "/{$tpl}";
        }

        if( view()->exists( $tpl  ) ) {
            return $tpl;
        }

        if( $quitOnMissing ) {
            abort( 404, "No template exists in frontend or controller's view directory for " . $tpl );
        }

        return false;
    }

    /**
     * A helper function which can be called when the needs to be logged in
     * or have a greater privilege.
     *
     * @param string $url  URL to redirect to (default: the default route)
     * @param int    $code Redirection code (default: 302)
     *
     * @return void
     */
    protected function unauthorized( string $url = '', $code = 302 ): void
    {
        abort( 302, '', [ 'Location' => url($url) ] );
        // belt and braces:
        die( "File: " . __FILE__ . "\nLine: " . __LINE__ . "\nBug: you should not see this..." );
    }
}