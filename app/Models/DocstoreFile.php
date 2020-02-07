<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\DocstoreFile
 *
 * @property int $id
 * @property int $docstore_directory_id
 * @property string $name
 * @property string $disk
 * @property string $path
 * @property string $description
 * @property int $min_privs
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile whereDocstoreDirectoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile whereMinPrivs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DocstoreFile extends Model
{

    /**
     * Get the directory that owns the file.
     */
    public function directory()
    {
        return $this->belongsTo('IXP\Models\DocstoreDirectory');
    }

    /**
     * Get the access logs for this file
     */
    public function logs()
    {
        return $this->hasMany('IXP\Models\DocstoreLog');
    }
}
