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
    Relations\BelongsTo};

use IXP\Traits\Observable;

/**
 * IXP\Models\CustomerNote
 *
 * @property int $id
 * @property int $customer_id
 * @property int $private
 * @property string $title
 * @property string $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \IXP\Models\Customer $customer
 * @method static Builder|CustomerNote newModelQuery()
 * @method static Builder|CustomerNote newQuery()
 * @method static Builder|CustomerNote privateOnly()
 * @method static Builder|CustomerNote publicOnly()
 * @method static Builder|CustomerNote query()
 * @method static Builder|CustomerNote whereCreatedAt($value)
 * @method static Builder|CustomerNote whereCustomerId($value)
 * @method static Builder|CustomerNote whereId($value)
 * @method static Builder|CustomerNote whereNote($value)
 * @method static Builder|CustomerNote wherePrivate($value)
 * @method static Builder|CustomerNote whereTitle($value)
 * @method static Builder|CustomerNote whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomerNote extends Model
{
    use Observable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust_notes';

    /**
     * Get the customer that own the customer note
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id' );
    }

    /**
     * Scope a query to only include private notes
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePrivateOnly( Builder $query ): Builder
    {
        return $query->where( 'private', 1 );
    }

    /**
     * Scope a query to only public notes
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublicOnly( Builder $query ): Builder
    {
        return $query->where( 'private', 0 );
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
            "%s Note [id:%d] belonging to %s [id:%d] '%s'",
            ucfirst( config( 'ixp_fe.lang.customer.one' ) ),
            $model->id,
            config( 'ixp_fe.lang.customer.one' ),
            $model->customer->id,
            $model->customer->name
        );
    }

    /**
     * Get note read statistics for a given set of notes and a user
     *
     * Returns an associate array with keys:
     *
     * * `notesReadUpto` - UNIX timestamp of when the user last read all notes / marked them as read
     * * `notesLastRead` - UNIX timestamp of when the user last read this customer's notes
     * * `unreadNotes`   - number of unread notes for this customer
     *
     * @param Collection    $notes
     * @param Customer      $c      The customer
     * @param User          $u       Optional user
     *
     * @return array
     */
    public static function analyseForUser( Collection $notes, Customer $c, User $u ): array
    {
        $unreadNotes    = 0;
        $rut            = $u->prefs[ 'notes' ][ 'read_upto' ] ?? null;
        $lastRead       = $u->prefs[ 'notes' ][ 'last_read' ][ $c->id ] ?? null;

        if( $lastRead || $rut ) {
            foreach( $notes as $note ) {/** @var CustomerNote  $note */
                if( ( !$rut || $rut < $note->updated_at ) && ( !$lastRead || $lastRead < $note->updated_at ) ) {
                    $unreadNotes++;
                }
            }
        } else {
            $unreadNotes = $notes->count();
        }

        return [ "notesReadUpto" => $rut , "notesLastRead" => $lastRead, "unreadNotes" => $unreadNotes ];
    }
}
