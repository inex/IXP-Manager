<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table( 'infrastructure', function( Blueprint $table ) {
            $table->boolean( 'exclude_from_ixf_export' )->default( false )->after( 'ixf_ix_id' );
        } );
    }
    
    public function down(): void
    {
        Schema::table( 'infrastructure', function( Blueprint $table ) {
            $table->dropColumn( 'exclude_from_ixf_export' );
        } );
    }
};
