<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IXP\Models\Logo
 *
 * @property int $id
 * @property int|null $customer_id
 * @property string $type
 * @property string $original_name
 * @property string $stored_name
 * @property string $uploaded_by
 * @property string $uploaded_at
 * @property int $width
 * @property int $height
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereStoredName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereUploadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereUploadedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereWidth($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Logo whereUpdatedAt($value)
 */
class Logo extends Model
{
    /**
     * Type for display on public website
     */
    public const TYPE_WWW80 = 'WWW80';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
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
    public function getShardedPath(): string
    {
        return $this->stored_name[ 0 ] . '/' . $this->stored_name[ 1 ] . '/' . $this->stored_name;
    }

    /**
     * Get the full path of the a logo
     *
     * @return string the/full/path/filename
     */
    public function getFullPath(): string
    {
        return public_path() . '/logos/' . $this->getShardedPath();
    }
}
