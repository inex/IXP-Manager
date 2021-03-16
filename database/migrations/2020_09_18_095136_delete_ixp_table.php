<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteIxpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_to_ixp', function (Blueprint $table) {
            $table->drop();
        });

        $sm = Schema::getConnection()->getDoctrineSchemaManager();

        foreach( $sm->listTableForeignKeys('infrastructure') as $fk ) {
            if( $fk->getForeignTableName() === 'ixp' ) {
                Schema::table( 'infrastructure', function( Blueprint $table ) use ( $fk ) {
                    $table->dropForeign( $fk->getName() );
                    $table->dropColumn( 'ixp_id' );
                } );
            }
        }

        foreach( $sm->listTableForeignKeys('traffic_daily') as $fk ) {
            if( $fk->getForeignTableName() === 'ixp' ) {
                Schema::table( 'traffic_daily', function( Blueprint $table ) use ( $fk ) {
                    $table->dropForeign( $fk->getName() );
                    $table->dropColumn( 'ixp_id' );
                } );
            }
        }

        Schema::table('ixp', function (Blueprint $table) {
            $table->drop();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
