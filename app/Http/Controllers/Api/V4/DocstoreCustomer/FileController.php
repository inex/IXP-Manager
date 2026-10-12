<?php

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Http\Controllers\Api\V4\DocstoreCustomer;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use IXP\Models\DocstoreCustomerFile;
use IXP\Models\User;

class FileController
{
    /**
     * Get information on a docstore customer file
     *
     * @param  DocstoreCustomerFile  $file
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function info( DocstoreCustomerFile $file ): JsonResponse
    {
        Gate::authorize( 'info', $file );

        $createdByUser = User::find( $file->created_by );
        $createdBy = $createdByUser ? ($createdByUser->username . " (" . $createdByUser->name . ")") : null;

        return response()->json( [
            'file_name'     => $file->name,
            'created_by'    => $createdBy,
            'customer'      => $file->customer->name,
            'dspath'        => config( 'filesystems.disks.' . $file->disk . '.root', '*** UNKNOWN LOCATION ***' ) . '/' . $file->path,
            'created_at'    => $file->created_at,
            'last_modified' => \Carbon\Carbon::parse( Storage::disk( $file->disk )->lastModified( $file->path ) )->format( 'Y-m-d H:i:s',  ),
            'size'          => Storage::disk( $file->disk )->size( $file->path ),
        ] );
    }
}