<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\Oui
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Oui newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Oui newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Oui query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $oui
 * @property string $organisation
 * @method static \Illuminate\Database\Eloquent\Builder|Oui whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oui whereOrganisation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oui whereOui($value)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Oui whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oui whereUpdatedAt($value)
 */
class Oui extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oui';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'oui',
        'organisation',
    ];
}
