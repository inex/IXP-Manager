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

use Auth, Cache;

use Illuminate\Http\Request;

use Illuminate\View\View;

use IXP\Jobs\FetchFilteredPrefixesForCustomer;

use IXP\Models\Customer;

/**
 * Filtered Prefixes Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class FilteredPrefixesController extends Controller
{
    /**
     * Get the list
     *
     * @param Request   $r
     * @param Customer  $cust
     *
     * @return View
     *
     * @throws
     */
    public function list( Request $r, Customer $cust ): View
    {
        $this->authorize('view', $cust);

        // are we busting the cache?
        if( $r->reset_cache === "1" && Auth::getUser()->isSuperUser() ) {
            Cache::forget('filtered-prefixes-' . $cust->id );
        }

        // get the prefixes
        $filteredPrefixes = Cache::get( 'filtered-prefixes-' . $cust->id, false );

        if( $filteredPrefixes === false ) {
            // no cached result so schedule a job to gather them:
            FetchFilteredPrefixesForCustomer::dispatchAfterResponse( $cust );

            // if we are using the sync queue runner, it will have completed
            $filteredPrefixes = Cache::get( 'filtered-prefixes-' . $cust->id, false );
        }

        return view( 'filtered-prefixes.view' )->with([
            'cust'              => $cust,
            'filteredPrefixes'  => $filteredPrefixes,
        ]);
    }
}