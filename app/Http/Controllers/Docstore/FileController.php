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
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use \League\Flysystem\Exception as FlySystemException;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    DocstoreFile,
    DocstoreLog,
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Storage;

class FileController extends Controller
{
    /**
     * View a docstore file apply to allowed mimetype ( DocstoreFile::$
     *
     * @param Request $request
     * @param DocstoreFile $file
     *
     * @return mixed
     *
     * @throws
     */
    public function view( Request $request, DocstoreFile $file )
    {
        $this->authorize( 'view', $file );

        if( $request->user() ) {
            $file->logs()->save( new DocstoreLog( [ 'downloaded_by' => $request->user()->getId() ] ) );
        }

        return view( 'docstore/file/view', [
            'file'      => $file,
            'content'   => Storage::disk( $file->disk )->get( $file->path )
        ] );
    }

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
        $this->authorize( 'download', $file );

        if( $request->user() ) {
            $file->logs()->save( new DocstoreLog( [ 'downloaded_by' => $request->user()->getId() ] ) );
        }

        try {
            return Storage::disk( $file->disk )->download( $file->path, $file->name );
        } catch( FlySystemException $e ) {
            AlertContainer::push( "This file could not be found / downloaded. Please report this error to the support team.", Alert::DANGER );
            return redirect( route( 'docstore-dir@list', [ 'dir' => $file->directory->id ] ) );
        }
    }

    /**
     * Delete a file
     *
     * @param Request $request
     *
     * @param DocstoreFile $file
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete( Request $request , DocstoreFile $file ): RedirectResponse
    {
        $this->authorize( 'delete', $file );

        $dir = $file->directory;

        Storage::disk( $file->disk )->delete( $file->path );
        $file->logs()->delete();
        $file->delete();

        AlertContainer::push( "File <em>{$request->name}</em> deleted.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir ] ) );
    }
}
