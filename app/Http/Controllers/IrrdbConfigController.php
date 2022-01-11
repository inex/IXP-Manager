<?php

namespace IXP\Http\Controllers;

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

use Former;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Models\IrrdbConfig;

use IXP\Rules\IdnValidate;

use IXP\Utils\Http\Controllers\Frontend\EloquentController;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Irrdb Config Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IrrdbConfigController extends EloquentController
{
    /**
     * The object being created / edited
     *
     * @var IrrdbConfig
     */
    protected $object = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(): void
    {
        $this->feParams         = (object)[
            'model'             => IrrdbConfig::class,
            'pagetitle'         => 'IRRDB Sources',
            'titleSingular'     => 'IRRDB Source',
            'nameSingular'      => 'an IRRDB Sources',
            'listOrderBy'       => 'host',
            'listOrderByDir'    => 'ASC',
            'viewFolderName'    => 'irrdb-config',
            'documentation'     => 'https://docs.ixpmanager.org/features/irrdb/',
            'listColumns'       => [
                'host'      => 'Host',
                'source'    => 'Source'
            ],
        ];

        // display the same information in the view as the list
        $this->feParams->viewColumns = array_merge(
            $this->feParams->listColumns,
            [
                'notes' => [
                    'title'         => 'Notes',
                    'type'          => self::$FE_COL_TYPES[ 'PARSDOWN' ]
                ],
                'created_at'       => [
                    'title'         => 'Created',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ],
                'updated_at'       => [
                    'title'         => 'Updated',
                    'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                ]
            ]
        );
    }

    /**
     * Provide array of rows for the list action and view action
     *
     * @param int|null $id The `id` of the row to load for `view` action`. `null` if `listAction`
     *
     * @return array
     */
    protected function listGetData( ?int $id = null ): array
    {
        $feParams = $this->feParams;
        return IrrdbConfig::when( $id , function( Builder $q, $id ) {
            return $q->where('id', $id );
        } )->when( $feParams->listOrderBy , function( Builder $q, $orderby ) use ( $feParams )  {
            return $q->orderBy( $orderby, $feParams->listOrderByDir ?? 'ASC');
        })->get()->toArray();
    }

    /**
     * Display the form to create an object
     *
     * @return array
     */
    protected function createPrepareForm(): array
    {
        return [
            'object'                => $this->object
        ];
    }

    /**
     * Display the form to update an object
     *
     * @param int|null $id
     *
     * @return array
     */
    protected function editPrepareForm( int $id = null ): array
    {
        $this->object = IrrdbConfig::findOrFail( $id );

        Former::populate([
            'host'              => request()->old( 'host',         $this->object->host      ),
            'source'            => request()->old( 'source',       $this->object->source    ),
            'notes'             => request()->old( 'notes',        $this->object->notes     ),
        ]);

        return [
            'object'                => $this->object
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
        $this->object = IrrdbConfig::create( $r->all() );
        return true;
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
        $this->object = IrrdbConfig::findOrFail( $id );
        $this->checkForm( $r );
        $this->object->update( $r->all() );
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function preDelete() : bool
    {
        $okay = true;
        if( ( $cnt = $this->object->customers()->count() ) ) {
            AlertContainer::push( "You cannot delete this IRRDB Source there are {$cnt} customer(s) associated with it. ", Alert::DANGER );
            $okay = false;
        }
        return $okay;
    }

    /**
     * Check if the form is valid
     *
     * @param Request $r
     */
    public function checkForm( Request $r ): void
    {
        $r->validate( [
            'host'                  => [ 'required', 'max:255', 'string', new IdnValidate() ],
            'source'                => 'required|string|max:255',
        ] );
    }
}