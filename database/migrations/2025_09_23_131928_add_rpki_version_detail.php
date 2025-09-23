<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table( 'routers', function( Blueprint $table ) {
            $table->smallInteger('rpki_min_version' )->nullable()->default( null )->unsigned()->after('rpki');
            $table->smallInteger('rpki_max_version' )->nullable()->default( null )->unsigned()->after('rpki_min_version');
        } );
    }

    public function down(): void
    {
        Schema::table( 'routers', function( Blueprint $table ) {
            $table->dropColumn( 'rpki_min_version' );
            $table->dropColumn( 'rpki_max_version' );
        } );
    }
};
