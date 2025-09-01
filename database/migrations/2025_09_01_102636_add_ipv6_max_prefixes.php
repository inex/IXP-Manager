<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};

return new class extends Migration {
    public function up(): void
    {
        Schema::table( 'cust', function( Blueprint $table ) {
            $table->integer( 'maxprefixes' )->nullable()->unsigned()->default( null )->change();
            $table->integer( 'maxprefixesv6' )->nullable()->unsigned()->default( null )->after( 'maxprefixes' );
        } );

        Schema::table( 'vlaninterface', function( Blueprint $table ) {
            $table->integer( 'ipv6maxbgpprefix' )->nullable()->unsigned()->default( null )->after( 'maxbgpprefix' );
            $table->integer( 'maxbgpprefix' )->nullable()->unsigned()->default( null )->change();
            $table->renameColumn( 'maxbgpprefix', 'ipv4maxbgpprefix' );
        } );

        DB::statement( "UPDATE cust SET maxprefixesv6 = maxprefixes" );
        DB::statement( "UPDATE vlaninterface SET ipv6maxbgpprefix = ipv4maxbgpprefix" );
    }

    public function down(): void
    {
        Schema::table( 'cust', function( Blueprint $table ) {
            $table->dropColumn( 'maxprefixesv6' );
        } );

        Schema::table( 'vlaninterface', function( Blueprint $table ) {
            $table->dropColumn( 'ipv6maxbgpprefix' );
            $table->renameColumn( 'ipv4maxbgpprefix', 'maxbgpprefix' );
        } );
    }
};
