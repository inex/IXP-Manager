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
use Illuminate\Support\Facades\Log;
use Storage;

/**
 * IXP\Models\DocstoreDirectory
 *
 * @property int $id
 * @property int|null $parent_dir_id
 * @property string $name
 * @property string $description
 * @property int $min_privs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\DocstoreFile[] $files
 * @property-read int|null $files_count
 * @property-read \IXP\Models\DocstoreDirectory|null $parentDirectory
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\DocstoreDirectory[] $subDirectories
 * @property-read int|null $sub_directories_count
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereMinPrivs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereParentDirId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereUpdatedAt($value)
 * @mixin \Eloquent
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
    public static function getListing( ?DocstoreDirectory $dir, ?UserEntity $user ): EloquentCollection
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




    public static function getListing2( int $privs = User::AUTH_SUPERUSER, $dir )
    {
        $list = self::where('parent_dir_id',$dir ?? null );


        dd( self::getListingFor( $list->orderBy('name')->get(), $privs ) );

        return $list->orderBy('name')->get();
    }

    public static function getListingFor( $dirs, $privs )
    {

        $data = [];
        foreach( $dirs as $dir ) {

            $files = self::where('id', $dir->id )
                ->whereHas( 'files', function( Builder $query ) use ( $privs ) {
                    $query->where( 'min_privs', '<=', $privs );
                } )
                ->get();


            if( $files->isEmpty() && $dir->subDirectories->isEmpty() ) {
                continue;
            }

            $data[ $dir->id ] = [
                'id'        => $dir->id,
                'name'      => $dir->name,
                'file'      => $files->isNotEmpty(),
            ];

            foreach( self::getListingFor( $dir->subDirectories, $privs ) as $sub ) {
                $data[ $dir->id ][ 'sub' ] = $sub;
            }




            /*if( $files->isNotEmpty() || $dir->subDirectories ){
                $data[ $dir->id ] = [
                    'id'        => $dir->id,
                    'name'      => $dir->name,
                    'file'      => $files->isNotEmpty()

                ];
                //$data[ $dir->id ][ 'sub' ] = self::getListingFor( $dir->subDirectories, $privs );
            }

            if( $dir->subDirectories ){
                self::getListingFor( $dir->subDirectories, $privs );
            }*/



            /*


            $data[ $dir->id ] = [
                    'id'        => $dir->id,
                    'name'      => $dir->name,
                    'hasFile'    => $files->isNotEmpty() ? true : false
             ];


            foreach( self::getListingFor( $dir->subDirectories, $privs ) as $sub ) {
                if( $sub[ 'hasFile' ] ) {
                    $data[ $sub ][ 'sub' ] = $sub;
                }

            }*/



        }

        return $data;
    }

    /**
     * Delete all the files and subdirectories for a given folder
     *
     * @param DocstoreDirectory $dir
     *
     * @throws
     */
    public static function recursiveDelete( DocstoreDirectory $dir )
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
