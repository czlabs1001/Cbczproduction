<?php

namespace BeycanPress\SolPay\WooCommerce\Payment;

use \BeycanPress\SolPay\WooCommerce\Lang;
use \BeycanPress\SolPay\WooCommerce\Settings;
use \BeycanPress\SolPay\WooCommerce\PluginHero\Helpers;
use \BeycanPress\SolPay\WooCommerce\Models\Transaction;

class Checkout
{
    use Helpers;
    
    /**
     * @var object
     */
    private $order;

    /**
     * @var object
     */
    private $api;

    /**
     * @return void
     */
    public function __construct()
    { 
        $this->api = new CheckoutApi();
        add_action('woocommerce_receipt_' . Gateway::$gateway, array($this, 'init'), 1);
    }

    /**
     * Ödeme bölümünü yükler
     * @param int $orderId
     * @return void
     */
    public function init($orderId) : void
    {   
        $this->order = wc_get_order($orderId);

        if ($this->order->get_status() != 'pending') {
            echo esc_html__('This order is not waiting for payment.', 'solpay');
        } else {

            $transaction = (new Transaction)->findOneBy([
                'orderId' => $orderId
            ], ['id', 'DESC']);

            if (!is_null($transaction) && $transaction->status == 'pending') {
                wp_redirect($this->order->get_checkout_order_received_url()); exit;
            }

            if ($this->setting('onlyLoggedInUser') && !is_user_logged_in()) {
                echo esc_html__('Please login to make a payment!', 'solpay');
            } else {
                $this->loadScripts();
                $this->viewEcho('payment/checkout');
            }
        }
    }

    /**
     * javascript ve css dosyalarını dahil eder
     * @return void
     */
    public function loadScripts() : void
    { 
        $this->addScript('js/solana-web3/solana-web3.js');
        $this->addScript('solpay/js/chunk-vendors.js');
        $this->addScript('solpay/js/app.js');
        $this->addStyle('solpay/css/app.css');
        $key = $this->addScript('js/main.js');
        wp_localize_script($key, 'SolPayWooCommerce', $this->jsData());
    }
    
    /**
     * Javascript'e gönderilecek dinamik veriyi hazırlar
     * @return array
     */
    private function jsData() : array
    {
        $converter = apply_filters(
            "SolPay/WooCommerce/Converter", 
            $this->setting('converter')
        );

        return [
            'order'=> [
                'id' => (int) $this->order->get_id(),
                'price' => (float) $this->order->get_total(),
                'currency' => strtoupper($this->order->get_currency())
            ],
            'mode' => 'payment',
            'lang' => Lang::get(),
            'currencies' => Settings::getCurrencies(),
            'wallets' => Settings::getWallets(),
            'imagesUrl' => $this->pluginUrl . 'assets/images/',
            'tokenDiscounts' => Settings::getTokenDiscounts(),
            'customTokens' => Settings::getCustomTokens(),
            'cluster' => $this->setting('cluster'),
            'apiUrl' => $this->api->getUrl(),
            'converter' => $converter,
        ];
    }
}