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

use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
    Model,
    Relations\BelongsToMany
};

/**
 * IXP\Models\ContactGroup
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property int $active
 * @property int $limited_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|\IXP\Models\Contact[] $contacts
 * @property-read int|null $contacts_count
 * @method static Builder|ContactGroup newModelQuery()
 * @method static Builder|ContactGroup newQuery()
 * @method static Builder|ContactGroup query()
 * @method static Builder|ContactGroup whereActive($value)
 * @method static Builder|ContactGroup whereCreatedAt($value)
 * @method static Builder|ContactGroup whereDescription($value)
 * @method static Builder|ContactGroup whereId($value)
 * @method static Builder|ContactGroup whereLimitedTo($value)
 * @method static Builder|ContactGroup whereName($value)
 * @method static Builder|ContactGroup whereType($value)
 * @method static Builder|ContactGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContactGroup extends Model
{
    public const TYPE_ROLE = 'ROLE';

    public static $TYPES = [
        self::TYPE_ROLE => 'Role'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_group';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'active',
        'limited_to'
    ];

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class )->withPivot( 'contact_to_group', 'contact_group_id' );
    }
}