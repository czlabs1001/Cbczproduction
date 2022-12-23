<?php

namespace BeycanPress\SolPay\WooCommerce;

class Loader extends PluginHero\Plugin
{
    public function __construct($pluginFile)
    {
        parent::__construct([
            'pluginFile' => $pluginFile,
            'textDomain' => 'solpay',
            'pluginKey' => 'solpay_woocommerce',
            'settingKey' => 'solpay_woocommerce_settings',
            'pluginVersion' => '1.0.3'
        ]);

        // if ($this->setting('license')) {
        //     add_action('plugins_loaded', function() {
        //         new Payment\Register();
        //     });
        // } else {
        //     $this->adminNotice(esc_html__('In order to use the "SolPay WooCommerce" Plugin, please enter your license (purchase) code in the license field in the settings section.', 'solpay'), 'error');
        // }
        
        add_action('plugins_loaded', function() {
            new Payment\Register();
        });
    }

    public function adminProcess() : void
    {
        new Pages\TransactionList();
        
        add_action('init', function(){
            new Settings;
        }, 9);
    }

    public static function activation() : void
    {
        (new Models\Transaction())->createTable();
    }

    public static function uninstall() : void
    {
        $settings = get_option(self::$instance->settingKey);
        if (isset($settings['dds']) && $settings['dds']) {
            delete_option(self::$instance->settingKey);
            delete_option('woocommerce_'.Payment\Gateway::$gateway.'_settings');
            (new Models\Transaction())->drop();
        }
    }
}
