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

use DB, Eloquent;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model
};

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};

use Illuminate\Support\Carbon;

use IXP\Traits\Observable;

/**
 * IXP\Models\DocstoreFile
 *
 * @property int $id
 * @property int|null $docstore_directory_id
 * @property string $name
 * @property string $disk
 * @property string $path
 * @property string|null $sha256
 * @property string|null $description
 * @property int $min_privs
 * @property Carbon $file_last_updated
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \IXP\Models\DocstoreDirectory|null $directory
 * @property-read Collection|\IXP\Models\DocstoreLog[] $logs
 * @property-read int|null $logs_count
 * @method static Builder|DocstoreFile newModelQuery()
 * @method static Builder|DocstoreFile newQuery()
 * @method static Builder|DocstoreFile query()
 * @method static Builder|DocstoreFile whereCreatedAt($value)
 * @method static Builder|DocstoreFile whereCreatedBy($value)
 * @method static Builder|DocstoreFile whereDescription($value)
 * @method static Builder|DocstoreFile whereDisk($value)
 * @method static Builder|DocstoreFile whereDocstoreDirectoryId($value)
 * @method static Builder|DocstoreFile whereFileLastUpdated($value)
 * @method static Builder|DocstoreFile whereId($value)
 * @method static Builder|DocstoreFile whereMinPrivs($value)
 * @method static Builder|DocstoreFile whereName($value)
 * @method static Builder|DocstoreFile wherePath($value)
 * @method static Builder|DocstoreFile whereSha256($value)
 * @method static Builder|DocstoreFile whereUpdatedAt($value)
 * @mixin Eloquent
 */

class DocstoreFile extends Model
{
    use Observable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'docstore_directory_id',
        'path',
        'sha256',
        'min_privs',
        'file_last_updated',
        'created_by',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'file_last_updated',
    ];


    /**
     * File extension allowed to be viewed
     *
     * @var array
     */
    public static $extensionViewable = [ '.txt', '.md' ];

    /**
     * File extension allowed to be edited
     *
     * @var array
     */
    public static $extensionEditable = [ '.txt', '.md' ];

    /**
     * Get the directory that owns the file.
     */
    public function directory(): BelongsTo
    {
        return $this->belongsTo(DocstoreDirectory::class, 'docstore_directory_id' );
    }

    /**
     * Get the access logs for this file
     */
    public function logs(): HasMany
    {
        return $this->hasMany(DocstoreLog::class );
    }

    /**
     * Can we view that file?
     *
     * @return bool
     */
    public function isViewable(): bool
    {
        return in_array( '.' . pathinfo( $this->name, PATHINFO_EXTENSION ), self::$extensionViewable, true );
    }

    /**
     * Can we edit that file?
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return in_array( '.' . pathinfo( $this->name, PATHINFO_EXTENSION ), self::$extensionEditable, true );
    }

    /**
     * Get the extension of the file
     *
     * @return string
     */
    public function extension(): string
    {
        return pathinfo( $this->name, PATHINFO_EXTENSION );
    }

    /**
     * Gets a directory listing of files for the given (or root) directory and as
     * appropriate for the user (or public access)
     *
     * @param DocstoreDirectory|null    $dir
     * @param int                       $privs
     *
     * @return Collection
     */
    public static function getListing( ?DocstoreDirectory $dir = null, int $privs = User::AUTH_PUBLIC ): Collection
    {
        return self::where('min_privs', '<=', $privs )
            ->where('docstore_directory_id', $dir->id ?? null )
            ->withCount([ 'logs as downloads_count', 'logs as unique_downloads_count' => function( Builder $query ) {
                $query->select( DB::raw('COUNT( DISTINCT downloaded_by )' ) );
            }])
            ->orderBy('name')->get();
    }

    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Model $model
     *
     * @return string
     */
    public static function logSubject( Model $model ): string
    {
        return sprintf(
            "Docstore File [id:%d] '%s'",
            $model->id,
            $model->name,
        );
    }
}