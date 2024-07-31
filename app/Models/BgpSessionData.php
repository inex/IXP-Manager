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

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * IXP\Models\BGPSessionData
 *
 * @property int $id
 * @property int|null $srcipaddressid
 * @property int|null $dstipaddressid
 * @property int|null $protocol
 * @property int|null $vlan
 * @property int|null $packetcount
 * @property string|null $timestamp
 * @property string|null $source
 * @method static Builder|BgpSessionData newModelQuery()
 * @method static Builder|BgpSessionData newQuery()
 * @method static Builder|BgpSessionData query()
 * @method static Builder|BgpSessionData whereDstipaddressid( $value )
 * @method static Builder|BgpSessionData whereId( $value )
 * @method static Builder|BgpSessionData wherePacketcount( $value )
 * @method static Builder|BgpSessionData whereProtocol( $value )
 * @method static Builder|BgpSessionData whereSource( $value )
 * @method static Builder|BgpSessionData whereSrcipaddressid( $value )
 * @method static Builder|BgpSessionData whereTimestamp( $value )
 * @method static Builder|BgpSessionData whereVlan( $value )
 * @mixin Eloquent
 */
class BgpSessionData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bgpsessiondata';

}
