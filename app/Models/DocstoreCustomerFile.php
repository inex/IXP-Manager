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

use DB, Eloquent;

use Entities\User as UserEntity;

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
use Storage;


/**
 * IXP\Models\DocstoreCustomerFile
 *
 * @property int $id
 * @property int|null $cust_id
 * @property int|null $docstore_customer_directory_id
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
 * @property-read DocstoreCustomerDirectory|null $directory
 * @method static Builder|DocstoreCustomerFile newModelQuery()
 * @method static Builder|DocstoreCustomerFile newQuery()
 * @method static Builder|DocstoreCustomerFile query()
 * @method static Builder|DocstoreCustomerFile whereCreatedAt($value)
 * @method static Builder|DocstoreCustomerFile whereCreatedBy($value)
 * @method static Builder|DocstoreCustomerFile whereDescription($value)
 * @method static Builder|DocstoreCustomerFile whereDisk($value)
 * @method static Builder|DocstoreCustomerFile whereCustId($value)
 * @method static Builder|DocstoreCustomerFile whereDocstoreCustomerDirectoryId($value)
 * @method static Builder|DocstoreCustomerFile whereFileLastUpdated($value)
 * @method static Builder|DocstoreCustomerFile whereId($value)
 * @method static Builder|DocstoreCustomerFile whereMinPrivs($value)
 * @method static Builder|DocstoreCustomerFile whereName($value)
 * @method static Builder|DocstoreCustomerFile wherePath($value)
 * @method static Builder|DocstoreCustomerFile whereSha256($value)
 * @method static Builder|DocstoreCustomerFile whereUpdatedAt($value)
 * @mixin Eloquent
 */

class DocstoreCustomerFile extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name',
        'description',
        'cust_id',
        'docstore_customer_directory_id',
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
        return $this->belongsTo(DocstoreCustomerDirectory::class, 'docstore_customer_directory_id' );
    }

    /**
     * Get the customer that owns the file
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id' );
    }

    /**
     * Can we view that file?
     *
     * @return bool
     */
    public function isViewable(): bool
    {
        return in_array( '.' . pathinfo( Storage::disk( $this->disk )->url( $this->path ), PATHINFO_EXTENSION ), self::$extensionViewable );
    }

    /**
     * Can we edit that file?
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return in_array( '.' . pathinfo( Storage::disk( $this->disk )->url( $this->path ), PATHINFO_EXTENSION ), self::$extensionEditable );
    }

    /**
     * Get the extension of the file
     *
     * @return string
     */
    public function extension(): string
    {
        return pathinfo( Storage::disk( $this->disk )->url( $this->path ), PATHINFO_EXTENSION );
    }

    /**
     * Gets a directory listing of files for the given Customer and directory and as
     * appropriate for the user
     *
     * @param Customer                          $cust
     * @param DocstoreCustomerDirectory|null    $dir
     * @param UserEntity|null                   $user
     *
     * @return Collection
     */
    public static function getListing( Customer $cust, ?DocstoreCustomerDirectory $dir = null, ?UserEntity $user = null )
    {
        return self::where('min_privs', '<=', $user ? $user->getPrivs() : UserEntity::AUTH_PUBLIC )
            ->where('cust_id', $cust->id )
            ->where('docstore_customer_directory_id', $dir ? $dir->id : null )
            ->orderBy('name')->get();
    }
}
