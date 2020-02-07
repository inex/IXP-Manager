<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\DocstoreLog
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\DocstoreLog query()
 * @mixin \Eloquent
 */
class DocstoreLog extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the file that owns this log.
     */
    public function file()
    {
        return $this->belongsTo('IXP\Models\DocstoreFile');
    }

}
