<?php

namespace Services\PaymentGateway;

use Omnipay\Omnipay;

/**
 * The intention of this factory is to create a service that is a wrapper around the relative Omnipay implementation
 * Each Gateway is a facade around the Omnipay implementation. Each Class can then handle the nuances. Additionally
 * having a factory should make it easier to implement any Omnipay Gateway
 *
 * Class GatewayFactory
 * @package App\Services\PaymentGateway
 */
class Factory
{

    /**
     * @param $name
     * @param $paymentGatewayConfig
     * @return Dummy|Redsys|Stripe|StripeSCA
     * @throws \Exception
     */
    public static function create($name, $paymentGatewayConfig)
    {

        switch ($name) {

            case Dummy::GATEWAY_NAME :
            {

                $gateway = Omnipay::create($name);
                $gateway->initialize();

                return new Dummy($gateway, $paymentGatewayConfig);
            }

            case Redsys::GATEWAY_NAME :
            {
                // The current library uses the old name (Sermepa)
                $gateway = Omnipay::create('Sermepa');
                $gateway->initialize($paymentGatewayConfig);

                return new Redsys($gateway, $paymentGatewayConfig);
            }

            case Stripe::GATEWAY_NAME :
            {

                $gateway = Omnipay::create($name);
                $gateway->initialize($paymentGatewayConfig);

                return new Stripe($gateway, $paymentGatewayConfig);
            }

            case StripeSCA::GATEWAY_NAME :
            {

                $gateway = Omnipay::create($name);
                $gateway->initialize($paymentGatewayConfig);

                return new StripeSCA($gateway, $paymentGatewayConfig);

            }

            default :
            {
                throw New \Exception('Invalid gateway specified');
            }
        }
    }

    /**
     * Generate a configuration array for a gateway
     *
     * @param  array  $database_config  Array with the configuration saved in the database
     *
     * @return array
     */
    public static function config($database_config)
    {
        // Override Attendize Env Test Mode with Web Config
        $database_config['testMode'] = isset($database_config['test']) ? true : config('attendize.enable_test_payments');

        return $database_config;
    }
}