<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFailedJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 2021-03-06 BOD: Migration no longer required as included in 2020_06_01_143931_database_schema_at_end_v5
        // Migration file kept for anyone upgrading so that their database table of migration states remains consistent.

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
