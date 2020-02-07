<?php

namespace IXP\Models;

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
     * Get the files in this directory
     */
    public function files()
    {
        return $this->hasMany('IXP\Models\DocstoreFile');
    }

}
