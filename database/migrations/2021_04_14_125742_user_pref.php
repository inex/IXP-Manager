<?php

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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use IXP\Models\User;

class UserPref extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->json('prefs' )->nullable()->after( 'extra_attributes' );
        });


        // Import the last read note data
        $notesRead = DB::table( 'user_pref' )
            ->select( [ 'user_id', 'attribute', 'value' ] )
            ->where( 'attribute', 'like', 'customer-notes.%.last_read' )
            ->get()->toArray();

        $userNotes = [];

        foreach( $notesRead as $read ){
            $cust_id = explode( '.', $read->attribute )[1];
            $userNotes[ $read->user_id ][ $cust_id ] = \Carbon\Carbon::parse( (int)$read->value )->format( 'Y-m-d H:i:s' );
        }

        foreach( $userNotes as $user_id => $note ){
           DB::table('user')
            ->where('id', $user_id )
            ->update( [ 'prefs' => [ 'notes' => [ 'last_read' => $note ] ] ] );
        }

        // Import the watching note data
        $notesWatching = DB::table( 'user_pref' )
            ->select( [ 'user_id', 'attribute', 'value' ] )
            ->where( 'attribute', 'like', 'customer-notes.watching.%' )
            ->get()->toArray();

        $userWatching = [];

        foreach( $notesWatching as $watching ){
            $note_id = explode( '.', $watching->attribute )[2];
            $userWatching[ $watching->user_id ][ $note_id ] = \Carbon\Carbon::parse( (int)$read->value )->format( 'Y-m-d H:i:s' );
        }

        foreach( $userWatching as $user_id => $watching ){
            $u = User::find( $user_id );
            $prefs = $u->prefs;
            $prefs[ 'notes' ][ 'note_watching' ] = $watching;
            $u->prefs = $prefs ;
            $u->save();
        }

        // Import the global notifications data
        $notifications = DB::table( 'user_pref' )
            ->select( [ 'user_id', 'attribute', 'value' ] )
            ->where( 'attribute', 'customer-notes.notify' )
            ->get()->toArray();

        $notifs = [];

        foreach( $notifications as $notif ){
            $notifs[ $notif->user_id ] = $notif->value;
        }

        foreach( $notifs as $user_id => $global_notifs ){
            $u = User::find( $user_id );
            $prefs = $u->prefs;
            $prefs[ 'notes' ][ 'global_notifs' ] = $global_notifs;
            $u->prefs = $prefs ;
            $u->save();
        }

        // Import the individual notes notifications data
        $notesNotify = DB::table( 'user_pref' )
            ->select( [ 'user_id', 'attribute', 'value' ] )
            ->where( 'attribute', 'like','customer-notes.%.notify' )
            ->get()->toArray();

        $notify = [];

        foreach( $notesNotify as $not ){
            $note_id = explode( '.', $not->attribute )[1];
            $notify[ $not->user_id ][ $note_id ] = $not->value;
        }

        foreach( $notify as $user_id => $individual_notifs ){
            $u = User::find( $user_id );
            $prefs = $u->prefs;
            $prefs[ 'notes' ][ 'customer_watching' ] = $individual_notifs;
            $u->prefs = $prefs ;
            $u->save();
        }

        // Import read_upto notes data
        $notesReadupto = DB::table( 'user_pref' )
            ->select( [ 'user_id', 'attribute', 'value' ] )
            ->where( 'attribute','customer-notes.read_upto' )
            ->get()->toArray();

        $readupto = [];

        foreach( $notesReadupto as $upto ){
            $readupto[ $upto->user_id ] = \Carbon\Carbon::parse( (int)$upto->value )->format( 'Y-m-d H:i:s' );;
        }

        foreach( $readupto as $user_id => $read ){
            $u = User::find( $user_id );
            $prefs = $u->prefs;
            $prefs[ 'notes' ][ 'read_upto' ] = $read;
            $u->prefs = $prefs ;
            $u->save();
        }

        // Import the mailing list data
        $mailingList = DB::table( 'user_pref' )
            ->select( [ 'user_id', 'attribute', 'value' ])
            ->where( 'attribute', 'like', 'mailinglist%' )
            ->get()->toArray();

        $userMailling = [];

        foreach( $mailingList as $mailling ){
            $list = explode( '.', $mailling->attribute )[1];
            $userMailling[ $mailling->user_id ][ $list ] = $mailling->value;
        }

        foreach( $userMailling as $user_id => $mail ){
            $u = User::find( $user_id );
            $prefs = $u->prefs;
            $prefs[ 'mailinglist' ] = $mail;
            $u->prefs = $prefs ;
            $u->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn( 'prefs' );
        });
    }
}
