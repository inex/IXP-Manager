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

use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\BGPSession
 *
 * @property int $id
 * @property int $srcipaddressid
 * @property int $protocol
 * @property int $dstipaddressid
 * @property int $packetcount
 * @property string $last_seen
 * @property string|null $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereDstipaddressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession wherePacketcount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereSrcipaddressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BgpSession whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BgpSession extends Model{}
