<?php

use Illuminate\Database\Migrations\Migration;

class AddRedsysGateway extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('payment_gateways')->insert(
            [
                'provider_name'           => 'Redsys',
                'provider_url'            => 'http://www.redsys.es/',
                'is_on_site'              => 0,
                'can_refund'              => 0,
                'name'                    => 'Redsys',
                'default'                 => 0,
                'admin_blade_template'    => 'ManageAccount.Partials.Redsys',
                'checkout_blade_template' => 'Public.ViewEvent.Partials.PaymentRedsys'
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('payment_gateways')->where('name', '=', 'Redsys')->delete();
    }
}
