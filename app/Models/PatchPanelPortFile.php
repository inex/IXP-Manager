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

use Eloquent;

use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};

use Illuminate\Support\Collection;


/**
 * IXP\Models\PatchPanelPortFile
 *
 * @property int $id
 * @property int|null $patch_panel_port_id
 * @property string $name
 * @property string $type
 * @property string $uploaded_at
 * @property string $uploaded_by
 * @property int $size
 * @property int $is_private
 * @property string $storage_location
 * @property-read \IXP\Models\PatchPanelPort|null $patchPanelPort
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile wherePatchPanelPortId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereStorageLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereUploadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortFile whereUploadedBy($value)
 * @mixin Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\PatchPanelPortFile whereUpdatedAt($value)
 */

class PatchPanelPortFile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patch_panel_port_file';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patch_panel_port_id',
        'name',
        'type',
        'uploaded_at',
        'uploaded_by',
        'size',
        'is_private',
        'storage_location',
    ];

    PUBLIC CONST UPLOAD_PATH = 'ppp';

    /**
     * Get the Patch Panel Port that owns this patch panel port file
     */
    public function patchPanelPort(): BelongsTo
    {
        return $this->belongsTo( PatchPanelPort::class , 'patch_panel_port_id' );
    }

    /**
     * Get name
     *
     * @return string
     */
    public function nameTruncated(): string
    {
        return strlen( $this->name ) > 80 ? substr( $this->name,0,80 ) . '...' . explode('.', $this->name )[1] : $this->name;
    }

    /**
     * Return the formated size
     *
     * @return string
     */
    public function sizeFormated(): string
    {
        $bytes = $this->size;
        if( $bytes >= 1073741824 ) {
            $bytes = number_format($bytes / 1073741824, 2 ) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' kB';
        } elseif ($bytes > 1) {
            $bytes .= ' bytes';
        } elseif ($bytes === 1) {
            $bytes .= ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * Get type as an icon from awesome font
     *
     * @return string
     */
    public function typeAsIcon(): string
    {
        switch ( $this->type ) {
            case 'image/jpeg':
                $icon = 'fa-file-image-o';
                break;
            case 'image/png':
                $icon = 'fa-file-image-o';
                break;
            case 'image/bmp':
                $icon = 'fa-file-image-o';
                break;
            case 'application/pdf':
                $icon = 'fa-file-pdf-o';
                break;
            case 'application/zip':
                $icon = 'fa-file-archive-o';
                break;
            case 'text/plain':
                $icon = 'fa-file-text';
                break;
            default:
                $icon = 'fa-file';
                break;
        }
        return $icon;
    }

    /**
     * Get the path for the panel port file
     *
     * @return string
     */
    public function path(): string
    {
        return self::UPLOAD_PATH . '/' . $this->storage_location[ 0 ] . '/'
            . $this->storage_location[ 1 ] . '/' . $this->storage_location;
    }
}