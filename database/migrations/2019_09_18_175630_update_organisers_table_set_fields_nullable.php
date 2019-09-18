<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrganisersTableSetFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organisers', function (Blueprint $table) {
            $table->text('about')->nullable()->change();
            $table->string('tax_id')->nullable()->change();
            $table->string('tax_name')->nullable()->change();
            $table->string('tax_value')->nullable()->change();
            $table->string('facebook')->nullable()->change();
            $table->string('twitter')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisers', function (Blueprint $table) {
            $table->text('about')->nullable(false)->change();
            $table->string('tax_id')->nullable(false)->change();
            $table->string('tax_name')->nullable(false)->change();
            $table->string('tax_value')->nullable(false)->change();
            $table->string('facebook')->nullable(false)->change();
            $table->string('twitter')->nullable(false)->change();
        });
    }
}
