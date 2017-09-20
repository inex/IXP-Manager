<?php

namespace IXP\Http\Controllers;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


use Illuminate\Support\Facades\View as ViewFacade;

/**
 * Doctrine2Frontend Functions
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Doctrine2Frontend extends Controller {

    /**
     * Parameters used by the frontend controller
     * @var array Parameters used by the frontend controller
     */
    protected $feParams = null;

    protected $data     = null;

    protected $view     = null;


    /**
     * The trait's initialisation method.
     *
     * This function is called from the Action's contructor and it passes those
     * same variables used for construction to the traits' init methods.
     *
     * @param object $request See Parent class constructor
     * @param object $response See Parent class constructor
     * @param object $invokeArgs See Parent class constructor
     */
    public function __construct( ){
        $this->feInit();
        $this->data[ 'col_types' ] = self::$FE_COL_TYPES;
    }

    static public $FE_COL_TYPES = [
        'HAS_ONE'  => 'hasOne',
        'DATETIME' => 'datetime',
        'DATE'     => 'date',
        'TIME'     => 'time',
        'SCRIPT'   => 'script',
        'SPRINTF'  => 'sprintf',
        'REPLACE'  => 'replace',
        'XLATE'    => 'xlate',
        'YES_NO'   => 'yes_no'
    ];


    /**
     * This is meant to be overridden.
     *
     * @throws OSS_Exception
     */
    protected function feInit(){
        //throw new OSS_Exception( 'FrontEnd controllers require an feInit() function' );
    }


    /**
     * List the contents of a database table.
     */
    public function listAction(){
        $this->data[ 'data' ]        = $this->listGetData() ;
        $this->view[ 'listScript' ]     = $this->resolveTemplate( 'js/list' );

        return $this->display( 'list' );
    }

    /**
     * Add (or edit) an object
     */
    public function addAction()
    {
        return $this->display( 'edit' );
    }

    /**
     * Add (or edit) an object
     */
    public function viewAction()
    {

    }

    /**
     * Add (or edit) an object
     */
    public function editAction()
    {

    }

    /**
     * Add (or edit) an object
     */
    public function deleteAction()
    {

    }

    /**
     * Displays the standard Frontend template or the controllers overridden version.
     *
     * @see _resolveTemplate()
     * @param string $tpl The template to display
     * @return void
     */
    protected function display( $tpl ){
        return view( $this->resolveTemplate( $tpl, true ) )->with( [ 'data' => $this->data , 'view' => $this->view ]);
    }

    /**
     * Resolves the standard Frontend template or the controllers overridden version.
     *
     * All frontend actions have their own template: `frontend/{$action}.foil.php` which is
     * displayed by default. You can however override these by creating a template named:
     * `{$controller}/{$action}.foil.php`. This function looks for an overriding template
     * and displays that if it exists, otherwise it displays the default.
     *
     * This will also work for subdirectories: e.g. `$tpl = forms/add.phtml` is also valid.
     *
     * @param string $tpl The template to display
     * @param bool $throw If true, throws an exception is no template is found
     * @return string|bool The template to use of false if none found
     * @throws OSS_Exception
     */
    protected function resolveTemplate( $tpl, $throw = false ){
        if( ViewFacade::exists ( $this->feParams->viewFolderName . "/{$tpl}" ) ) {
            return $this->feParams->viewFolderName . "/{$tpl}";
        } else if( ViewFacade::exists( "frontend/{$tpl}"  ) ) {
            return "frontend/{$tpl}";
        }

        abort(404, "No template exists in frontend or controller's view directory for ".$tpl);

        return false;
    }

}