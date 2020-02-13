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

use Entities\User as UserEntity;

use Illuminate\Database\Eloquent\{
    Builder,
    Collection as EloquentCollection,
    Model
};

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};

use Illuminate\Support\Carbon;

/**
 * IXP\Models\DocstoreDirectory
 *
 * @property int $id
 * @property int $parent_dir_id
 * @property string $name
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|DocstoreDirectory newModelQuery()
 * @method static Builder|DocstoreDirectory newQuery()
 * @method static Builder|DocstoreDirectory query()
 * @method static Builder|DocstoreDirectory whereCreatedAt( $value )
 * @method static Builder|DocstoreDirectory whereDescription( $value )
 * @method static Builder|DocstoreDirectory whereId( $value )
 * @method static Builder|DocstoreDirectory whereName( $value )
 * @method static Builder|DocstoreDirectory whereUpdatedAt( $value )
 * @mixin Eloquent
 */

class DocstoreDirectory extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name','description', 'parent_dir_id' ];

    /**
     * Get the subdirectories for this directory
     */
    public function subDirectories(): HasMany
    {
        return $this->hasMany(DocstoreDirectory::class, 'parent_dir_id', 'id' );
    }

    /**
     * Get the parent directory
     */
    public function parentDirectory(): BelongsTo
    {
        return $this->belongsTo(DocstoreDirectory::class, 'parent_dir_id', 'id' );
    }

    /**
     * Get the files in this directory
     */
    public function files(): HasMany
    {
        return $this->hasMany(DocstoreFile::class);
    }

    /**
     * Gets a listing of directories for the given (or root) directory and as
     * appropriate for the user (or public access)
     *
     * @param DocstoreDirectory|null    $dir
     * @param UserEntity|null           $user
     *
     * @return EloquentCollection
     */
    public static function getListing( ?DocstoreDirectory $dir, ?UserEntity $user )
    {
        $list = self::where('parent_dir_id', $dir ? $dir->id : null );

        if( !$user || !$user->isSuperUser() ) {
            $list->whereHas( 'files', function( Builder $query ) use ( $user ) {
                $query->where( 'min_privs', '<=', $user ? $user->getPrivs() : UserEntity::AUTH_PUBLIC );
            } );
        }

        return $list->orderBy('name')->get();
    }


    /**
     * Create an array of directories keeping the hierarchy root/subfolder
     *
     *  [
     *      [
     *          "id" => 1
     *          "name"  => Folder 1
     *      ]
     *
     *      [
     *          "id" => 2
     *          "name"  => -&nbsp;Sub Folder 1
     *      ]
     *
     *      [
     *          "id" => 3
     *          "name"  => -&nbsp;Sub Folder 2
     *      ]
     *
     *      [
     *          "id" => 4
     *          "name"  => Folder 2
     *      ]
     *
     *
     *  ]
     *
     * @param $dirs
     * @param $depth
     *
     * @return array
     */
    public static function getListingForDropdown( $dirs, $depth = 5 )
    {
        $data[] = [
            'id'        => '',
            'name'      => 'Root Directory'
        ];

        foreach( $dirs as $dir ) {

            $data[] = [
                'id'        => $dir->id,
                'name'      => str_repeat( '&nbsp;', $depth ) . '-&nbsp;' . $dir->name
            ];

            foreach( self::getListingForDropdown( $dir->subDirectories, $depth + 5 ) as $sub ) {
                $data[] = $sub;
            }
        }

        return $data;

    }

}
