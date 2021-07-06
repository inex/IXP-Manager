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

use Auth, Eloquent, Storage;

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

use Illuminate\Support\Facades\{
    Cache,
    Log
};

/**
 * IXP\Models\DocstoreCustomerDirectory
 *
 * @property int $id
 * @property int $cust_id
 * @property int|null $parent_dir_id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \IXP\Models\Customer $customer
 * @property-read Collection|\IXP\Models\DocstoreCustomerFile[] $files
 * @property-read int|null $files_count
 * @property-read DocstoreCustomerDirectory|null $parentDirectory
 * @property-read Collection|DocstoreCustomerDirectory[] $subDirectories
 * @property-read int|null $sub_directories_count
 * @method static Builder|DocstoreCustomerDirectory newModelQuery()
 * @method static Builder|DocstoreCustomerDirectory newQuery()
 * @method static Builder|DocstoreCustomerDirectory query()
 * @method static Builder|DocstoreCustomerDirectory whereCreatedAt($value)
 * @method static Builder|DocstoreCustomerDirectory whereCustId($value)
 * @method static Builder|DocstoreCustomerDirectory whereDescription($value)
 * @method static Builder|DocstoreCustomerDirectory whereId($value)
 * @method static Builder|DocstoreCustomerDirectory whereName($value)
 * @method static Builder|DocstoreCustomerDirectory whereParentDirId($value)
 * @method static Builder|DocstoreCustomerDirectory whereUpdatedAt($value)
 * @mixin Eloquent
 */

class DocstoreCustomerDirectory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'parent_dir_id',
        'cust_id'
    ];

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
    public const CACHE_KEY_FOR_CUSTOMER_USER_CLASS_HIERARCHY = 'docstore_customer_directory_hierarchy_for_user_class_';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('privs', function ( Builder $builder ) {
            if( !Auth::check() ) {
                // if public user make sure that no records is returned
                $builder->where('id', null );
            } elseif( !Auth::getUser()->isSuperUser() ) {
                // if not super user make sure only records from the same customer are returned
                $builder->where('cust_id', Auth::getUser()->custid );
            }
        });
    }

    /**
     * Get the customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'id' );
    }

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
        return $this->hasMany(DocstoreCustomerFile::class);
    }

    /**
     * Gets a listing of directories for the given (or root) directory and as
     * appropriate for the user (or public access)
     *
     * @param Customer                  $cust
     * @param DocstoreDirectory|null    $dir
     * @param User|null                 $user
     *
     * @return Collection
     */
    public static function getListing( Customer $cust, User $user, ?DocstoreDirectory $dir = null ): Collection
    {
        return self::where( 'cust_id', $cust->id )->where('parent_dir_id', $dir->id ?? null)
            ->when( !$user->isSuperUser() , function( Builder $q ) use ( $user ) {
                $q->whereHas( 'files', function( Builder $q ) use ( $user ) {
                    return $q->where( 'min_privs', '<=', $user->privs() );
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
     * @param $dirs     Collection
     * @param $depth    int
     *
     * @return array
     */
    public static function getListingForDropdown( Collection $dirs, int $depth = 5 ): array
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
     * Build a hierarchy of all subdirs that this user class should be able to see.
     *
     * This also caches the results as it can be query intensive for large structures and,
     * for choosing to display the menu options, would be ran per page hit.
     *
     * @param Customer  $cust
     * @param int       $priv
     * @param bool      $showRoot Should we show the Root directory ('Root Directory')
     *
     * @return mixed
     */
    public static function getHierarchyForCustomerAndUserClass( Customer $cust, int $priv = User::AUTH_SUPERUSER, bool $showRoot = true )
    {
        return Cache::remember( self::CACHE_KEY_FOR_CUSTOMER_USER_CLASS_HIERARCHY . $cust->id . '_' . $priv, 86400, function() use ( $cust, $priv, $showRoot ) {
            self::where( 'cust_id', $cust->id )->whereNull('parent_dir_id' )
                ->orderBy('name')->get()->each( function( $sd ) use ( $priv ) {
                if( self::recurseForHierarchyForCustomerAndUserClass( $sd, $priv ) ) {
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

                if( $rootDirVisible && $showRoot ) {
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
     * @param DocstoreCustomerDirectory $subdir
     * @param int                       $priv User class to test for
     *
     * @return bool
     */
    private static function recurseForHierarchyForCustomerAndUserClass( DocstoreCustomerDirectory $subdir, int $priv ): bool
    {
        $includeSubdir = false;
        foreach( $subdir->subDirectories as $sd ) {
            if( $shouldInclude = self::recurseForHierarchyForCustomerAndUserClass( $sd, $priv ) ) {
                self::$dirs[$sd->parent_dir_id][] = [ 'id' => $sd->id, 'name' => $sd->name ];
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
     * Delete all the files and subdirectories for a given customer folder
     *
     * @param DocstoreCustomerDirectory $dir
     *
     * @throws
     */
    public static function recursiveDelete( DocstoreCustomerDirectory $dir ): void
    {
        $dir->subDirectories->each( function( DocstoreCustomerDirectory $subdir ) {
            self::recursiveDelete( $subdir );
        });

        $dir->files->each( function( DocstoreCustomerFile $file ) use ( $dir ) {
            Storage::disk( $file->disk )->delete( $file->path );
            $file->delete();
            Log::info( sprintf( "Docstore: file [%d|%s] for the customer [%d|%s] deleted", $file->id, $file->name, $dir->customer->id, $dir->customer->name ) );
        });

        Log::info( sprintf( "Docstore: directory [%d|%s] for the customer [%d|%s] deleted", $dir->id, $dir->name, $dir->customer->id, $dir->customer->name ) );
        $dir->delete();
    }

    /**
     * Delete all the files and directories for a given customer
     *
     * @param Customer $cust
     *
     * @throws
     */
    public static function deleteAllForCustomer( Customer $cust ): void
    {
        // Getting all the root directories for the customer
        $rootDirs = self::where( 'cust_id', $cust->id )->where( 'parent_dir_id', null )->get();

        $rootDirs->each( function( DocstoreCustomerDirectory $dir ) {
            self::recursiveDelete( $dir );
        });

        // Do we have files at the root that was not belonging to a directory ?
        $cust->docstoreCustomerFiles()->each( function( DocstoreCustomerFile $file ) use( $cust ) {
            Storage::disk( $file->disk )->delete( $file->path );
            $file->delete();
            Log::info( sprintf( "Docstore: file [%d|%s] for the customer [%d|%s] deleted", $file->id, $file->name, $cust->id, $cust->name ) );
        });
    }
}