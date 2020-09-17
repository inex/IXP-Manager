<?php

namespace IXP\Models;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, stdClass;

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany
};


/**
 * IXP\Models\Contact
 *
 * @property int $id
 * @property int|null $custid
 * @property string $name
 * @property string|null $position
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $mobile
 * @property int $facilityaccess
 * @property int $mayauthorize
 * @property string|null $notes
 * @property string|null $lastupdated
 * @property int|null $lastupdatedby
 * @property string|null $creator
 * @property string|null $created
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
 * @method static Builder|Contact whereCreated($value)
 * @method static Builder|Contact whereCreator($value)
 * @method static Builder|Contact whereCustid($value)
 * @method static Builder|Contact whereEmail($value)
 * @method static Builder|Contact whereFacilityaccess($value)
 * @method static Builder|Contact whereId($value)
 * @method static Builder|Contact whereLastupdated($value)
 * @method static Builder|Contact whereLastupdatedby($value)
 * @method static Builder|Contact whereMayauthorize($value)
 * @method static Builder|Contact whereMobile($value)
 * @method static Builder|Contact whereName($value)
 * @method static Builder|Contact whereNotes($value)
 * @method static Builder|Contact wherePhone($value)
 * @method static Builder|Contact wherePosition($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\Contact whereUpdatedAt($value)
 */
class Contact extends Model
{
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
     * Get the contact groups that are type role for the contact
     */
    public function contactRoles(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_to_group', 'contact_id' )
            ->where( 'type', ContactGroup::TYPE_ROLE )
            ->orderBy( 'name', 'asc' );
    }

    /**
     * Get the contact groups that are not type role for the contact
     */
    public function contactGroups(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_to_group', 'contact_id' )
            ->where( 'type', '!=', ContactGroup::TYPE_ROLE )
            ->orderBy( 'name', 'asc' );
    }

    /**
     * Get all the contact groups for the contact
     */
    public function contactGroupsAll(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_to_group', 'contact_id' )
            ->orderBy( 'name', 'asc' );
    }

    /**
     * Get the customer that own the contact
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'custid' );
    }
}