<?php

namespace IXP\Http\Controllers\Docstore;

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

use Illuminate\Http\Request;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    DocstoreFile, DocstoreLog
};

use Storage;

class FileController extends Controller
{
    /**
     * Download a docstore file
     *
     * @param Request $request
     * @param DocstoreFile $file
     *
     * @return mixed
     *
     * @throws
     */
    public function download( Request $request, DocstoreFile $file )
    {
        $this->authorize( 'view', $file );

        if( $request->user() ) {
            $file->logs()->save( new DocstoreLog( [ 'downloaded_by' => $request->user()->getId() ] ) );
        }

        return Storage::disk( $file->disk )->download( $file->path, $file->name );
    }
}
