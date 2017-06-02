<?php
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

namespace IXP\Http\Controllers;

use D2EM, Former, Input, Redirect;

use Entities\{
    CoreBundle  as CoreBundleEntity,
    Switcher    as SwitcherEntity
};

use Illuminate\Http\{
    JsonResponse,
    RedirectResponse,
    Request
};

use Illuminate\Support\Facades\View as FacadeView;
use Illuminate\View\View;

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

/**
 * Router Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CoreBundleController extends Controller
{
    /**
     * Display all the core bundles
     *
     * @return  View
     */
    public function list( int $id = null ): View {
        return view( 'core-bundle/index' )->with([
            'listCb'       => D2EM::getRepository( CoreBundleEntity::class )->findAll( )
        ]);
    }

    /**
     * Display the form to edit a core bundle
     *
     * @param  int    $id        core bundle that need to be edited
     * @return View
     */
    public function editWizard( int $id = null ): View {
        /** @var CoreBundleEntity $cb */
        if( $id ) {
            if( !( $cb = D2EM::getRepository( CoreBundleEntity::class )->find( $id ) ) ) {
                abort( 404 );
            }
        } else {
            $cb = new CoreBundleEntity;
        }

        // fill the form with router data
        Former::populate([
            'description'                => $cb->getDescription(),
        ]);

        Former::open()->rules([
            'description'                => 'required|string|max:255',
            'graph_name'                 => 'required|string|max:255',

        ]);

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'core-bundle/edit-wizard' )->with([
            'cb'                    => $cb,
            'types'                 => CoreBundleEntity::$TYPES,
        ]);
    }

    public function addCoreLinkFrag(Request $request ):JsonResponse {
        $nb = $request->input("nbCoreLink") + 1;

        $returnHTML = view('core-bundle/core-link-frag')->with([
            'switches'                      => D2EM::getRepository( SwitcherEntity::class )->getNames(),
            'nbLink'                        => $nb,
            'enabled'                       => $request->input("enabled") ? true : false,
        ])->render();

        return response()->json( ['success' => true, 'htmlFrag' => $returnHTML, 'nbCoreLinks' => $nb ] );

    }


}
