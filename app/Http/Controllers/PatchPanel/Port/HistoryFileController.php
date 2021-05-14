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
    JsonResponse
};

use IXP\Models\{
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
 * History File Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\PatchPanel\Port
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class HistoryFileController extends Controller
{
    /**
     * Make a patch panel port file private or public
     *
     * @param  PatchPanelPortHistoryFile $file patch panel port file ID
     *
     * @return  JsonResponse
     */
    public function togglePrivacy( PatchPanelPortHistoryFile $file ): JsonResponse
    {
        $file->update( [ 'is_private' => !$file->is_private ] );
        return response()->json( [ 'success' => true, 'isPrivate' => $file->is_private ] );
    }

    /**
     * Delete a patch panel port history file
     *
     * @param  PatchPanelPortHistoryFile  $file  patch panel port history file
     *
     * @return  RedirectResponse
     *
     * @throws Exception
     */
    public function delete( PatchPanelPortHistoryFile $file ): RedirectResponse
    {
        $path = 'files/' . $file->path();

        if( Storage::exists( $path ) && Storage::delete( $path ) ) {
            $file->delete();
            AlertContainer::push( 'Patch Panel Port File deleted.', Alert::SUCCESS );
        } else {
            AlertContainer::push( 'Patch Panel Port File could not be deleted.', Alert::DANGER );
        }

        return redirect( route( 'patch-panel-port@view', [ 'ppp' => $file->patchPanelPortHistory->patch_panel_port_id ] ) . '#ppp-' . $file->patch_panel_port_history_id );
    }

    /**
     * Download history files
     *
     * @param   PatchPanelPortHistoryFile $file the Patch panel port file
     *
     * @return  BinaryFileResponse
     */
    public function download( PatchPanelPortHistoryFile $file ): BinaryFileResponse
    {
        $u = User::find( Auth::id() );
        if( !$u->isSuperUser() ) {
            if( !$file->patchPanelPortHistory->cust_id
                || $file->patchPanelPortHistory->cust_id !== $u->custid
                || $file->is_private ) {
                Log::alert($u->username . ' tried to access a PPP history file with ID:' . $file->id . ' but does not have permission');
                abort(401);
            }
        }

        return response()->file( storage_path() . '/files/' . $file->path(), [ 'Content-Type' => $file->type ] );
    }
}