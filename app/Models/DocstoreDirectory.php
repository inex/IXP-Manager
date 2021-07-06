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

use Eloquent, Storage;

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

use Illuminate\Support\Facades\{
    Cache,
    Log
};

/**
 * IXP\Models\DocstoreDirectory
 *
 * @property int $id
 * @property int|null $parent_dir_id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EloquentCollection|\IXP\Models\DocstoreFile[] $files
 * @property-read int|null $files_count
 * @property-read DocstoreDirectory|null $parentDirectory
 * @property-read EloquentCollection|DocstoreDirectory[] $subDirectories
 * @property-read int|null $sub_directories_count
 * @method static Builder|DocstoreDirectory newModelQuery()
 * @method static Builder|DocstoreDirectory newQuery()
 * @method static Builder|DocstoreDirectory query()
 * @method static Builder|DocstoreDirectory whereCreatedAt($value)
 * @method static Builder|DocstoreDirectory whereDescription($value)
 * @method static Builder|DocstoreDirectory whereId($value)
 * @method static Builder|DocstoreDirectory whereName($value)
 * @method static Builder|DocstoreDirectory whereParentDirId($value)
 * @method static Builder|DocstoreDirectory whereUpdatedAt($value)
 * @mixin Eloquent
 */

class DocstoreDirectory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'parent_dir_id'
    ];

    /**
     * Get the subdirectories for this directory
     */
    public function subDirectories(): HasMany
    {
        return $this->hasMany( __CLASS__, 'parent_dir_id', 'id' )->orderBy('name');
    }

    /**
     * Get the parent directory
     */
    public function parentDirectory(): BelongsTo
    {
        return $this->belongsTo( __CLASS__, 'parent_dir_id', 'id' );
    }

    /**
     * Get the files in this directory
     */
    public function files(): HasMany
    {
        return $this->hasMany(DocstoreFile::class );
    }

    /**
     * Gets a listing of directories for the given (or root) directory and as
     * appropriate for the user (or public access)
     *
     * @param DocstoreDirectory|null        $dir
     * @param User|null                     $user
     *
     * @return EloquentCollection
     */
    public static function getListing( ?DocstoreDirectory $dir, ?User $user ): EloquentCollection
    {
        return self::where('parent_dir_id', $dir->id ?? null )
            ->when( !$user || !$user->isSuperUser() , function( Builder $q ) use ( $user) {
                $q->whereHas( 'files', function( Builder $q ) use ( $user ) {
                    return $q->where( 'min_privs', '<=', $user ? $user->privs() : User::AUTH_PUBLIC );
                } );
            })->orderBy('name')->get();

    }

    /**
     * Create an array of directories keeping the hierarchy root/subfolder
     *
     *  [
     *      [ "id" => 1, "name"  => "Folder 1" ],
     *      [ "id" => 2, "name"  => " - Sub Folder 1" ],
     *      [ "id" => 3, "name"  => " - Sub Folder 2" ],
     *      [ "id" => 4, "name"  => "Folder 2" ],
     *  ]
     *
     * @param $dirs EloquentCollection
     * @param $depth int
     *
     * @return array
     */
    public static function getListingForDropdown( EloquentCollection $dirs, int $depth = 5 ): array
    {
        $data = [];
        $data[] = [ 'id' => '', 'name' => 'Root Directory' ];

        foreach( $dirs as $dir ) {
            $data[] = [ 'id' => $dir->id, 'name' => str_repeat( '&nbsp;', $depth ) . '-&nbsp;' . $dir->name ];

            foreach( self::getListingForDropdown( $dir->subDirectories, $depth + 5 ) as $sub ) {
                $data[] = $sub;
            }
        }
        return $data;
    }

    /**
     * Static property used by getHierarchyForUserClass() and recurseForHierarchyForUserClass()
     * to build the hierarchy.
     *
     * Yields an array of the following form (i.e. an array of subdirs in each dir):
     *
     * ```
     * [
     *     directory_id => [ [ 'id' => subdir_id, 'name' => subdir_name ], [ ... ], ... ],
     *     ...
     *  ]
     * ```
     *
     * @var array
     */
    public static $dirs = [];

    /**
     * The cache key used in getHierarchyForUserClass()
     * @var string
     */
    public const CACHE_KEY_FOR_USER_CLASS_HIERARCHY = 'docstore_directory_hierarchy_for_user_class_';

    /**
     * Build a hierarchy of all subdirs that this user class should be able to see.
     *
     * This also caches the results as it can be query intensive for large structures and,
     * for choosing to display the menu options, would be ran per page hit.
     *
     * @param int $priv
     *
     * @return mixed
     */
    public static function getHierarchyForUserClass( int $priv = User::AUTH_SUPERUSER )
    {
        return Cache::remember( self::CACHE_KEY_FOR_USER_CLASS_HIERARCHY . $priv, 86400, function() use ( $priv ) {
            self::where('parent_dir_id', null )->orderBy('name' )->get()->each( function( $sd ) use ( $priv ) {
                if( self::recurseForHierarchyForUserClass( $sd, $priv ) ) {
                    self::$dirs[ $sd->parent_dir_id ][] = [ 'id' => $sd->id, 'name' => $sd->name ];
                }
            });

            if( self::$dirs === [] ) {
                // nothing for this user class in subdirectories. Should they see the document store at all?
                // Yes, they should, if it itself contains files they should be able to see:
                $rootDirVisible = self::where('parent_dir_id', null )
                    ->whereHas( 'files', function( Builder $query ) use ( $priv ) {
                        $query->where( 'min_privs', '<=', $priv );
                    } )->get()->isNotEmpty();

                if( $rootDirVisible ) {
                    self::$dirs[null]  = [ 'id' => null, 'name' => 'Root Directory' ];
                }
            }
            return self::$dirs;
        } );
    }

    /**
     * Companion recursive function for getHierarchyForUserClass() to recurse to the leaf node
     * of a directory hierarchy and return uo through the parent nodes whether it should be
     * included or not.
     *
     * @param DocstoreDirectory $subdir
     * @param int               $priv User class to test for
     *
     * @return bool
     */
    private static function recurseForHierarchyForUserClass( DocstoreDirectory $subdir, int $priv ): bool
    {
        $includeSubdir = false;
        foreach( $subdir->subDirectories as $sd ) {
            if( $shouldInclude = self::recurseForHierarchyForUserClass( $sd, $priv ) ) {
                self::$dirs[ $sd->parent_dir_id ][] = [ 'id' => $sd->id, 'name' => $sd->name ];
                $includeSubdir = true;
            }
        }

        // we have recursed all the subdirectories above. Have we decided to include this one?
        if( $includeSubdir ) {
            return true;
        }

        if( $priv === User::AUTH_SUPERUSER ) {
            return true;
        }

        return self::where( 'id', $subdir->id )
            ->whereHas( 'files', function( Builder $query ) use ( $priv ) {
                $query->where( 'min_privs', '<=', $priv );
            } )
            ->get()->isNotEmpty();
    }

    /**
     * Delete all the files and subdirectories for a given folder
     *
     * @param DocstoreDirectory $dir
     *
     * @return void
     *
     * @throws
     */
    public static function recursiveDelete( DocstoreDirectory $dir ): void
    {
        $dir->subDirectories->each( function( DocstoreDirectory $subdir ) {
            self::recursiveDelete( $subdir );
        });

        $dir->files->each( function( DocstoreFile $file ) {
            $file->logs()->delete();
            Storage::disk( $file->disk )->delete( $file->path );
            $file->delete();
            Log::info( sprintf( "Docstore: file [%d|%s] deleted", $file->id, $file->name ) );
        });

        Log::info( sprintf( "Docstore: directory [%d|%s] deleted", $dir->id, $dir->name ) );
        $dir->delete();
    }
}