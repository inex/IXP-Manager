<?php

namespace IXP\Models;

use Entities\User as UserEntity;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;



/**
 * IXP\Models\DocstoreDirectory
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $min_privs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereMinPrivs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreDirectory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DocstoreDirectory extends Model
{

    /**
     * Get the subdirectories for this directory
     */
    public function subDirectories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DocstoreDirectory::class, 'parent_dir_id', 'id' );
    }

    /**
     * Get the parent directory
     */
    public function parentDirectory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DocstoreDirectory::class, 'parent_dir_id', 'id' );
    }

    /**
     * Get the files in this directory
     */
    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DocstoreFile::class);
    }


    /**
     * Gets a listing of directories for the given (or root) directory and as
     * appropriate for the user (or public access)
     *
     * @param DocstoreDirectory|null $dir
     * @param UserEntity|null $user
     * @return EloquentCollection
     */
    public static function getListing( ?DocstoreDirectory $dir, ?UserEntity $user ): EloquentCollection
    {
        return self::where( 'min_privs', '<=', $user ? $user->getPrivs() : UserEntity::AUTH_PUBLIC )
            ->where('parent_dir_id', $dir ? $dir->id : null )
            ->orderBy('name')->get();
    }

}
