<?php

namespace IXP\Http\Controllers\Docstore;

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

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    DocstoreFile,
    DocstoreLog
};

/**
 * LogController Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Docstore
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LogController extends Controller
{
    /**
     * Display the list of unique logs for a file
     *
     * @param  DocstoreFile  $file
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function uniqueList( DocstoreFile $file ) : View
    {
        $this->authorize( 'viewAny', DocstoreLog::class );

        return view( 'docstore/log/list', [
            'file'          => $file,
            'unique'        => true,
            'logs'          => DocstoreLog::getUniqueUserListing( $file )
        ] );
    }

    /**
     * Display the list of all logs for a file
     *
     * @param  DocstoreFile  $file
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function list( DocstoreFile $file ) : View
    {
        $this->authorize( 'viewAny', DocstoreLog::class );

        return view( 'docstore/log/list', [
            'file'          => $file,
            'unique'        => false,
            'logs'          => DocstoreLog::getListing( $file )
        ] );
    }
}