<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table( 'company_registration_detail', function( Blueprint $table ) {
            $table->longText( 'notes' )->after( 'country' )->nullable();
        } );

        Schema::table( 'company_billing_detail', function( Blueprint $table ) {
            $table->string( 'purchaseOrderNumber', 50 )->after( 'purchaseOrderRequired' )->nullable();
            $table->longText( 'notes' )->after( 'billingFrequency' )->nullable();
        } );
    }

    public function down(): void
    {
        Schema::table( 'company_registration_detail', function( Blueprint $table ) {
            $table->dropColumn('notes');
        } );

        Schema::table( 'company_billing_detail', function( Blueprint $table ) {
            $table->dropColumn( 'purchaseOrderNumber' );
            $table->dropColumn( 'notes' );
        } );
    }
};
