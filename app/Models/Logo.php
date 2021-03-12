<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IXP\Models\Logo
 *
 * @property int $id
 * @property int|null $customer_id
 * @property string $original_name
 * @property string $stored_name
 * @property string $uploaded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property int $width
 * @property int $height
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|Logo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Logo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Logo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereStoredName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereUploadedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Logo whereWidth($value)
 * @mixin \Eloquent
 */
class Logo extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'original_name',
        'stored_name',
        'uploaded_by',
        'width',
        'height',
    ];

    /**
     * Get the customer that own the logo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Creates a hierarchy directory structure to shard image storage
     *
     * @return string the/sharded/path/filename
     */
    public function shardedPath(): string
    {
        return $this->stored_name[ 0 ] . '/' . $this->stored_name[ 1 ] . '/' . $this->stored_name;
    }

    /**
     * Get the full path of the a logo
     *
     * @return string the/full/path/filename
     */
    public function fullPath(): string
    {
        return public_path() . '/logos/' . $this->shardedPath();
    }
}
