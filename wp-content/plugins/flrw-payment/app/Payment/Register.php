<?php

namespace BeycanPress\SolPay\WooCommerce\Payment;

use \BeycanPress\SolPay\WooCommerce\Settings;
use \BeycanPress\SolPay\WooCommerce\PluginHero\Helpers;

class Register
{
    use Helpers;
    
    public function __construct()
    {   
        if (function_exists('WC')) {
            if ($this->setting('walletAddress') == '') {
                $this->adminNotice(esc_html__('If you did not specify a wallet address in the SolPay WooCommerce settings, the plugin will not work. Please specify a wallet address first.', 'solpay'), 'error');
                
            } else {
                if ($this->setting('converter') == 'coinmarketcap' && $this->setting('coinMarketCapApiKey') == '') {
                    $this->adminNotice(esc_html__('You selected the Coin Market Cap API as the Currency converter API, but you did not enter an API key! That\'s why the plugin has been deactivated!', 'solpay'), 'error');
                } else {
                    // Register gateways
                    add_filter('woocommerce_payment_gateways', function($gateways) {
                        $gateways[] = Gateway::class;
                        return $gateways;
                    });
                    
                    if (!is_admin()) {
                        new Details();
                        new Checkout();
                    }
                }
            }
        } else {
            $this->adminNotice(esc_html__('The SolPay WooCommerce” plugin cannot run without WooCommerce active. Please install and activate WooCommerce plugin.', 'solpay'), 'error');
        }
    }
}