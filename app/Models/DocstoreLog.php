<?php

namespace IXP\Models;

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

use Eloquent;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model
};

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IXP\Models\DocstoreLog
 *
 * @method static Builder|DocstoreLog newModelQuery()
 * @method static Builder|DocstoreLog newQuery()
 * @method static Builder|DocstoreLog query()
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
        return $this->belongsTo('IXP\Models\DocstoreFile' , 'docstore_file_id' );
    }

    /**
     * Gets a listing of logs for the given file
     *
     * @param DocstoreFile  $file   Display logs from file
     * @param bool          $unique Display unique result
     *
     * @return Collection
     */
    public static function getListing( DocstoreFile $file, bool $unique = false )
    {
        $list = self::where('docstore_file_id', $file->id );

        if( $unique ) {
            $list->groupBy( 'downloaded_by' );
        }

        return $list->orderBy('downloaded_by')->get();
    }

}