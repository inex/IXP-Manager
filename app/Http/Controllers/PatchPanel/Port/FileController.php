<?php

namespace IXP\Http\Controllers\PatchPanel\Port;

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

use Auth, Log, Storage;

use Exception;
use Illuminate\Http\{
    RedirectResponse,
    Request,
    JsonResponse
};

use IXP\Models\{
    PatchPanelPort,
    PatchPanelPortFile,
    PatchPanelPortHistoryFile,
    User
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use IXP\Http\Controllers\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * File Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\PatchPanel\Port
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class FileController extends Controller
{
    /**
     * Make a patch panel port file private or public
     *
     * @param  PatchPanelPortFile $file patch panel port file ID
     *
     * @return  JsonResponse
     */
    public function togglePrivacy( PatchPanelPortFile $file ): JsonResponse
    {
        $file->update( [ 'is_private' => !$file->is_private ] );
        return response()->json( [ 'success' => true, 'isPrivate' => $file->is_private ] );
    }

    /**
     * Delete a patch panel port file
     *
     * @param  Request  $r
     * @param  PatchPanelPortFile  $file  patch panel port file
     *
     * @return  RedirectResponse|JsonResponse
     *
     * @throws Exception
     */
    public function delete( Request $r, PatchPanelPortFile $file ): RedirectResponse|JsonResponse
    {
        $path = 'files/' . $file->path();

        if( Storage::exists( $path ) && Storage::delete( $path ) ) {
            $file->delete();
            $message = 'Patch Panel Port File deleted.'; $success = true;
        } else {
            $message = 'Patch Panel Port File could not be deleted.'; $success = false;
        }

        if( (bool)$r->jsonResponse ) {
            return response()->json( ['success'     => true,    'message' => 'File deleted' ] );
        }

        AlertContainer::push( $message, $success ? Alert::SUCCESS : Alert::DANGER );
        return redirect()->to( route( 'patch-panel-port@view', [ 'ppp' => $file->patch_panel_port_id ] ) );
    }

    /**
     * Upload a file to a patch panel port
     *
     * @param  PatchPanelPort   $ppp    patch panel port
     * @param  Request          $r      instance of the current HTTP request
     *
     * @return  JsonResponse
     */
    public function upload( Request $r, PatchPanelPort $ppp ): JsonResponse
    {
        if( !( $file = $r->file( 'file' ) ) ) {
            return response()->json( [ 'success' => false, 'message' => 'You need to upload a file.' ] );
        }

        $hash = hash('sha256', $ppp->id . '-' . $file->getClientOriginalName() );
        $path = "files/" . PatchPanelPortFile::UPLOAD_PATH . '/' . $hash[ 0 ] . '/'
            . $hash[ 1 ] . '/' . $hash;

        if( Storage::exists( $path ) ) {
            return response()->json( [ 'success' => false, 'message' => 'This port already has a file with the same name.' ] );
        }

        if( Storage::put( $path, fopen( $file->getRealPath(), 'rb+' ) ) ) {
            $pppf = PatchPanelPortFile::create( [
                'patch_panel_port_id'   => $ppp->id,
                'name'                  => $file->getClientOriginalName(),
                'type'                  => Storage::mimeType( $path ),
                'uploaded_at'           => now(),
                'uploaded_by'           => Auth::getUser()->username,
                'size'                  => Storage::size( $path ),
                'storage_location'      => $hash
            ] );

            return response()->json( [ 'success' => true, 'message' => 'File uploaded.', 'id' => $pppf->id ] );
        }

        return response()->json( [ 'success' => false, 'message' => 'Could not save file to storage location' ] );
    }

    /**
     * Download files
     *
     * @param   PatchPanelPortFile $file the Patch panel port file
     *
     * @return  BinaryFileResponse
     */
    public function download( PatchPanelPortFile $file ): BinaryFileResponse
    {
        $u = User::find( Auth::id() );
        if( !$u->isSuperUser() ) {
            if( !$file->patchPanelPort->customer
                || $file->patchPanelPort->customer_id !== $u->custid
                || $file->is_private ) {
                Log::alert($u->username . ' tried to access a PPP file with ID:' . $file->id . ' but does not have permission');
                abort(401);
            }
        }

        return response()->file( storage_path() . '/files/' . $file->path(), [ 'Content-Type' => $file->type ] );
    }
}