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

use Illuminate\Database\Eloquent\{
    Builder,
    Model
};

/**
 * IXP\Models\CustomerToUser
 *
 * @property int $id
 * @property int $customer_id
 * @property int $user_id
 * @property int $privs
 * @property string|null $last_login_date
 * @property string|null $last_login_from
 * @property string $created_at
 * @property mixed|null $extra_attributes
 * @property string|null $last_login_via
 * @method static Builder|CustomerToUser newModelQuery()
 * @method static Builder|CustomerToUser newQuery()
 * @method static Builder|CustomerToUser query()
 * @method static Builder|CustomerToUser whereCreatedAt($value)
 * @method static Builder|CustomerToUser whereCustomerId($value)
 * @method static Builder|CustomerToUser whereExtraAttributes($value)
 * @method static Builder|CustomerToUser whereId($value)
 * @method static Builder|CustomerToUser whereLastLoginDate($value)
 * @method static Builder|CustomerToUser whereLastLoginFrom($value)
 * @method static Builder|CustomerToUser whereLastLoginVia($value)
 * @method static Builder|CustomerToUser wherePrivs($value)
 * @method static Builder|CustomerToUser whereUserId($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\CustomerToUser whereUpdatedAt($value)
 */
class CustomerToUser extends Model
{

}