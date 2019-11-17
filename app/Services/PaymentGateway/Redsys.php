<?php

namespace Services\PaymentGateway;

class Redsys
{

    public CONST GATEWAY_NAME = 'Redsys';

    private $transaction_data;

    private $gateway;

    private $extra_params = ['paymentMethod', 'payment_intent'];

    /**
     * @var array with gateway config and test mode
     */
    private $options;

    public function __construct($gateway, $options = [])
    {
        $this->gateway = $gateway;
        $this->options = $options;
    }

    public function startTransaction($order_total, $order_email, $event, $ticket_order = null)
    {
        $this->createTransactionData($order_total, $order_email, $event, $ticket_order);
        return $this->gateway->purchase($this->transaction_data)->send();
    }

    private function createTransactionData($order_total, $order_email, $event, $ticket_order = null)
    {
        return $this->transaction_data = [
            // This is needed for remove decimal (last two digits are considered decimal)
            'brandName'          => !empty($this->options['merchantName']) ? $this->options['merchantName'] : $event->organiser->name,
            'description'        => __('PaymentGateway_Redsys.buy_item'),
            'titular'            => $this->getClientName($order_email, $ticket_order),
            'transactionId'      => $this->generateOrderID(),
            'amount'             => ($order_total * 100),
            'currency'           => $event->currency->code,
            'signatureMode'      => 'HMAC_SHA256_V1',
            'terminal'           => !empty($this->options['terminal']) ? $this->options['terminal'] : 1,
            'transactionType'    => !empty($this->options['transactionType']) ? $this->options['transactionType'] : 0,
            'productDescription' => 'ASD',
            'merchantURL'        => $this->url(
                route('showEventCheckoutPaymentReturn', [
                    'event_id'                => $event->id,
                    'is_payment_notification' => 1
                ])
            ),
            'cancelUrl'          => $this->url(
                route('showEventCheckoutPaymentReturn', [
                    'event_id'             => $event->id,
                    'is_payment_cancelled' => 1
                ])
            ),
            'returnUrl'          => $this->url(
                route('showEventCheckoutPaymentReturn', [
                    'event_id'              => $event->id,
                    'is_payment_successful' => 1
                ])
            ),
        ];
    }

    protected function getClientName($order_email, $ticket_order)
    {
        if (empty($ticket_order) || !isset($ticket_order['request_data']) || count($ticket_order['request_data']) < 1) {
            return $order_email;
        }

        return $ticket_order['request_data'][0]['order_first_name'].' '.$ticket_order['request_data'][0]['order_last_name'];
    }

    /**
     * Redsys needs an order number to process the payment, as Attendize generates it later we generate a random one.
     */
    protected function generateOrderID()
    {
        return crc32(time().'_'.abs(uniqid('', true)));
    }

    /**
     * If we cannot use HTTPs (e.g. with LetsEncrypt certificates) make the request via HTTP
     *
     * @param $url
     * @return mixed
     */
    protected function url($url)
    {
        !isset($this->options['https']) ?: str_replace('https://', 'http://', $url);
        return $url;
    }

    public function getTransactionData()
    {
        return $this->transaction_data;
    }

    public function extractRequestParameters($request)
    {
        foreach ($this->extra_params as $param) {
            if (!empty($request->get($param))) {
                $this->options[$param] = $request->get($param);
            }
        }
    }

    public function completeTransaction($transactionId = '')
    {

        $intentData = [
            'paymentIntentReference' => $this->options['payment_intent'],
        ];

        $paymentIntent = $this->gateway->fetchPaymentIntent($intentData);
        $response = $paymentIntent->send();
        if ($response->requiresConfirmation()) {
            $response = $this->gateway->confirm($intentData)->send();
        }

        return $response;
    }

    public function getAdditionalData($response)
    {
        return [];
    }

    public function storeAdditionalData()
    {
        return true;
    }

    public function refundTransaction($order, $refund_amount, $refund_application_fee)
    {

        $request = $this->gateway->cancel([
            'transactionReference'   => $order->transaction_id,
            'amount'                 => $refund_amount,
            'refundApplicationFee'   => $refund_application_fee,
            'paymentIntentReference' => $order->payment_intent
        ]);

        $response = $request->send();

        if ($response->isCancelled()) {
            $refundResponse['successful'] = true;
        } else {
            $refundResponse['successful'] = false;
            $refundResponse['error_message'] = $response->getMessage();
        }

        return $refundResponse;
    }

}