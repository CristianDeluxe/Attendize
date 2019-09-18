<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEventsTableSetNullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('location_address_line_1', 355)->nullable()->change();
            $table->string('location_address_line_2', 355)->nullable()->change();
            $table->string('location_state', 355)->nullable()->change();
            $table->string('location_post_code', 355)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('location_address_line_1', 355)->nullable(false)->change();
            $table->string('location_address_line_2', 355)->nullable(false)->change();
            $table->string('location_state', 355)->nullable(false)->change();
            $table->string('location_post_code', 355)->nullable(false)->change();
        });
    }
}
