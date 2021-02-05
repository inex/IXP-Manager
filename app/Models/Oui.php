<?php

namespace IXP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\Oui
 *
 * @property int $id
 * @property string $oui
 * @property string $organisation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Oui newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Oui newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Oui query()
 * @method static \Illuminate\Database\Eloquent\Builder|Oui whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oui whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oui whereOrganisation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oui whereOui($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Oui whereUpdatedAt($value)
 * @mixin \Eloquent
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
