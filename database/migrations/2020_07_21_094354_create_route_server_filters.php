<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteServerFilters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_server_filters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer( 'customer_id' )->nullable();
            $table->integer( 'peer_id' )->nullable();
            $table->integer( 'vlan_id' )->nullable();
            $table->string( 'received_prefix',43 )->nullable( true );
            $table->string( 'advertised_prefix',43 )->nullable( true );
            $table->smallInteger('protocol' )->nullable( true );
            $table->string( 'action_advertise',255 )->nullable( true );
            $table->string( 'action_receive',255 )->nullable( true );
            $table->boolean( 'enabled' )->default( true );
            $table->integer('order_by' )->nullable( false );
            $table->string( 'live',255 )->nullable( false );

            $table->foreign('customer_id' )->references('id' )->on('cust' );
            $table->foreign('peer_id' )->references('id' )->on('cust' );
            $table->foreign('vlan_id' )->references('id' )->on('vlan' );

            $table->unique( [ 'customer_id', 'order_by' ] );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_server_filters');
    }
}