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

use Illuminate\Database\Eloquent\{
    Model,
    Relations\BelongsTo
};

/**
 * IXP\Models\PatchPanelPortHistoryFile
 *
 * @property int $id
 * @property int|null $patch_panel_port_history_id
 * @property string $name
 * @property string $type
 * @property string $uploaded_at
 * @property string $uploaded_by
 * @property int $size
 * @property int $is_private
 * @property string $storage_location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\PatchPanelPortHistory|null $patchPanelPortHistory
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile wherePatchPanelPortHistoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereStorageLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereUploadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatchPanelPortHistoryFile whereUploadedBy($value)
 * @mixin Eloquent
 */

class PatchPanelPortHistoryFile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patch_panel_port_history_file';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patch_panel_port_history_id',
        'name',
        'type',
        'uploaded_at',
        'uploaded_by',
        'size',
        'is_private',
        'storage_location',
    ];

    /**
     * Get the Patch Panel Port history that owns this patch panel port history file
     */
    public function patchPanelPortHistory(): BelongsTo
    {
        return $this->belongsTo( PatchPanelPortHistory::class , 'patch_panel_port_history_id' );
    }

    /**
     * Populate this file history model with details from a patch panel port file.
     *
     * @param PatchPanelPortFile    $file
     * @param PatchPanelPortHistory $portHistory
     *
     * @return PatchPanelPortHistoryFile
     */
    public static function createFromFile( PatchPanelPortFile $file, PatchPanelPortHistory $portHistory): PatchPanelPortHistoryFile
    {
        return self::create([
            'patch_panel_port_history_id'   => $portHistory->id,
            'name'                          => $file->name,
            'type'                          => $file->type,
            'uploaded_at'                   => $file->uploaded_at,
            'uploaded_by'                   => $file->uploaded_by,
            'size'                          => $file->size,
            'is_private'                    => $file->is_private,
            'storage_location'              => $file->storage_location,
        ]);
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
     * Return the formatted size
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
     * get the patch for the panel port history file
     *
     * @return string
     */
    public function path(): string
    {
        return PatchPanelPortFile::UPLOAD_PATH . '/' . $this->storage_location[ 0 ] . '/'
            . $this->storage_location[ 1 ] . '/' . $this->storage_location;
    }
}
