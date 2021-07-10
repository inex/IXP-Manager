<?php

namespace IXP\Models;

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

use Eloquent;

use Illuminate\Database\Eloquent\{
    Builder,
    Model
};

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\{
    Carbon,
    Collection
};

use Illuminate\Support\Facades\DB;

/**
 * IXP\Models\DocstoreLog
 *
 * @property int $id
 * @property int $docstore_file_id
 * @property int|null $downloaded_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \IXP\Models\DocstoreFile $file
 * @method static Builder|DocstoreLog newModelQuery()
 * @method static Builder|DocstoreLog newQuery()
 * @method static Builder|DocstoreLog query()
 * @method static Builder|DocstoreLog whereCreatedAt($value)
 * @method static Builder|DocstoreLog whereDocstoreFileId($value)
 * @method static Builder|DocstoreLog whereDownloadedBy($value)
 * @method static Builder|DocstoreLog whereId($value)
 * @method static Builder|DocstoreLog whereUpdatedAt($value)
 * @mixin Eloquent
 */
class DocstoreLog extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [ 'id' ];

    /**
     * Get the file that owns this log.
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(DocstoreFile::class , 'docstore_file_id' );
    }

    /**
     * Gets a listing of logs for the given file
     *
     * @param DocstoreFile  $file   Display logs from file
     *
     * @return Collection
     */
    public static function getListing( DocstoreFile $file ): Collection
    {
        return self::select([ 'docstore_logs.*', 'user.name AS name', 'user.username AS username' ])
            ->where('docstore_file_id', $file->id )
            ->leftJoin( 'user', 'user.id', '=', 'docstore_logs.downloaded_by' )
            ->orderBy('created_at', 'desc')->get();
    }

    /**
     * Gets a listing of logs for the given file
     *
     * @param DocstoreFile  $file   Display logs from file
     *
     * @return Collection
     */
    public static function getUniqueUserListing( DocstoreFile $file ): Collection
    {
        return self::select([ 'docstore_logs.id', 'docstore_logs.downloaded_by',
                DB::raw( 'COUNT(docstore_logs.downloaded_by) AS downloads' ),
                DB::raw( 'MAX(docstore_logs.created_at) AS last_downloaded' ),
                DB::raw( 'MIN(docstore_logs.created_at) AS first_downloaded' ),
                'user.name AS name', 'user.username AS username'
            ])
            ->where('docstore_file_id', $file->id )
            ->leftJoin( 'user', 'user.id', '=', 'docstore_logs.downloaded_by' )
            ->groupBy( 'downloaded_by' )
            ->orderBy('downloaded_by')->get();
    }
}