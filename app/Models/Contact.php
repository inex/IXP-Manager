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
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany
};

use IXP\Traits\Observable;

/**
 * IXP\Models\Contact
 *
 * @property int $id
 * @property int|null $custid
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $mobile
 * @property bool $facilityaccess
 * @property bool $mayauthorize
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $lastupdatedby
 * @property string|null $creator
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $position
 * @property string|null $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ContactGroup[] $contactGroups
 * @property-read int|null $contact_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ContactGroup[] $contactGroupsAll
 * @property-read int|null $contact_groups_all_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\IXP\Models\ContactGroup[] $contactRoles
 * @property-read int|null $contact_roles_count
 * @property-read \IXP\Models\Customer|null $customer
 * @method static Builder|Contact newModelQuery()
 * @method static Builder|Contact newQuery()
 * @method static Builder|Contact query()
 * @method static Builder|Contact whereCreatedAt($value)
 * @method static Builder|Contact whereCreator($value)
 * @method static Builder|Contact whereCustid($value)
 * @method static Builder|Contact whereEmail($value)
 * @method static Builder|Contact whereFacilityaccess($value)
 * @method static Builder|Contact whereId($value)
 * @method static Builder|Contact whereLastupdatedby($value)
 * @method static Builder|Contact whereMayauthorize($value)
 * @method static Builder|Contact whereMobile($value)
 * @method static Builder|Contact whereName($value)
 * @method static Builder|Contact whereNotes($value)
 * @method static Builder|Contact wherePhone($value)
 * @method static Builder|Contact wherePosition($value)
 * @method static Builder|Contact whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contact extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'mobile',
        'facilityaccess',
        'mayauthorize',
        'lastupdatedby',
        'creator',
        'position',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'facilityaccess'            => 'boolean',
        'mayauthorize'              => 'boolean',
    ];

    /**
     * Get the contact groups that are type role for the contact
     */
    public function contactRoles(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_to_group', 'contact_id' )
            ->where( 'type', ContactGroup::TYPE_ROLE )
            ->orderBy( 'name' )->withTimestamps();
    }

    /**
     * Get the contact groups that are not type role for the contact
     */
    public function contactGroups(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_to_group', 'contact_id' )
            ->where( 'type', '!=', ContactGroup::TYPE_ROLE )
            ->orderBy( 'name' );
    }

    /**
     * Get all the contact groups for the contact
     */
    public function contactGroupsAll(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_to_group', 'contact_id' )
            ->orderBy( 'name' );
    }

    /**
     * Get the customer that own the contact
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'custid' );
    }

    /**
     * String to describe the model being updated / deleted / created
     *
     * @param Model $model
     *
     * @return string
     */
    public static function logSubject( Model $model ): string
    {
        return sprintf(
            "Contact [id:%d] belonging to %s [id:%d] '%s'",
            $model->id,
            config( 'ixp_fe.lang.customer.one' ),
            $model->customer->id,
            $model->customer->shortname
        );
    }
}