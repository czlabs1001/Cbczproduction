<?php

namespace BeycanPress\SolPay\WooCommerce;

use \CSF;
use \Beycan\LicenseVerifier;
use \BeycanPress\SolPay\WooCommerce\PluginHero\Plugin;
use \BeycanPress\SolPay\WooCommerce\PluginHero\Setting;

class Settings extends Setting
{
    use PluginHero\Helpers;
    
    /**
     * @var array
     */
    public static $currencies = [];

    /**
     * @var array
     */
    public static $customTokens = [];

    /**
     * @var array
     */
    public static $tokenDiscounts = [];

    /**
     * @var array
     */
    public static $wallets = [];

    public function __construct()
    {
        $prefix = $this->settingKey;
        $parent = $this->pages->TransactionList->slug;

        add_action("csf_{$prefix}_save_after", function($data, $opt) {
            if (isset($opt->errors['license'])) self::deleteLicense();
        }, 10, 2);

        parent::__construct($prefix, esc_html__('Settings', 'solpay'), $parent);

        self::createSection(array(

            'id'     => 'generalOptions', 
            'title'  => esc_html__('General options', 'solpay'),
            'icon'   => 'fa fa-cog',
            'fields' => array(
                array(
                    'id'      => 'dds',
                    'title'   => esc_html__('Data deletion status', 'solpay'),
                    'type'    => 'switcher',
                    'default' => false,
                    'help'    => esc_html__('This setting is passive come by default. You enable this setting. All data created by the plug-in will be deleted while removing the plug-in.', 'solpay')
                ),
                array(
                    'id'      => 'walletAddress',
                    'title'   => esc_html__('Wallet address', 'solpay'),
                    'type'    => 'text',
                    'help'    => esc_html__('The account address to which the payments will be transferred. Solana wallet address.', 'solpay'),
                    'sanitize' => function($val) {
						return sanitize_text_field($val);
					},
                    'validate' => function($val) {
                        $val = sanitize_text_field($val);
                        if (empty($val)) {
                            return esc_html__('Wallet address cannot be empty.', 'solpay');
                        } elseif (strlen($val) < 44 || strlen($val) > 44) {
                            return esc_html__('Wallet address must consist of 44 characters.', 'solpay');
                        }
                    }
                ),
                array(
                    'id'      => 'paymentCompleteOrderStatus',
                    'title'   => esc_html__('Payment complete order status', 'solpay'),
                    'type'    => 'select',
                    'help'    => esc_html__('The status to apply for order after payment is complete.', 'solpay'),
                    'options' => [
                        'wc-completed' => esc_html__('Completed', 'solpay'),
                        'wc-processing' => esc_html__('Processing', 'solpay')
                    ],
                    'default' => 'wc-completed',
                ),
                array(
                    'id'      => 'onlyLoggedInUser',
                    'title'   => esc_html__('Only logged in users can pay', 'solpay'),
                    'type'    => 'switcher',
                    'help'    => esc_html__('Even if a user enters the SolPay payment page, if they are not logged in, SolPay will not work at all.', 'solpay'),
                    'default' => false,
                ),
            )
        ));

        self::createSection(array(
            'id'     => 'walletsMenu', 
            'title'  => esc_html__('Accepted wallets', 'solpay'),
            'icon'   => 'fa fa-wallet',
            'fields' => array(
                array(
                    'id'     => 'wallets',
                    'type'   => 'fieldset',
                    'title'  => esc_html__('Wallets', 'solpay'),
                    'help'   => esc_html__('Specify the wallets you want to accept payments from.', 'solpay'),
                    'fields' => array(
                        array(
                            'id'      => 'Phantom',
                            'title'   => esc_html('Phantom'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id'      => 'Slope',
                            'title'   => esc_html('Slope'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id'      => 'Solflare',
                            'title'   => esc_html('Solflare'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id'      => 'Sollet',
                            'title'   => esc_html('Sollet'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id'      => 'Sollet-Extension',
                            'title'   => esc_html('Sollet (Extension)'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                    ),
                    'validate' => function($val) {
                        foreach ($val as $value) {
                            if ($value) {
                                break;
                            } else {
                                return esc_html__('You must activate at least one wallet!', 'solpay');
                            }
                        }
                    }
                ),
            ) 
        ));

        self::createSection(array(
            'id'     => 'solanaOptions', 
            'title'  => esc_html__('Solana options', 'solpay'),
            'icon'   => 'fa fa-link',
            'fields' => array(
                array(
                    'id' => 'cluster',
                    'type'  => 'select',
                    'title' => esc_html__('Cluster', 'solpay'),
                    'options' => [
                        'mainnet-beta' => 'Mainnet',
                        'testnet' => 'Testnet',
                        'devnet' => 'Devnet'
                    ],
                    'default' => 'mainnet-beta',
                    'desc'    => esc_html__('You can select other networks for testing. But for real operations "Mainnet" must be selected!', 'solpay'),
                ),
                array(
                    'id'      => 'nativeCurrency',
                    'title'   => esc_html__('Get paid with Solana (SOL) Active/Passive', 'solpay'),
                    'type'    => 'switcher',
                    'help'    => esc_html__('Get paid in native currency? - Solana (SOL)', 'solpay'),
                    'default' => true,
                ),
                array(
                    'id'      => 'currencies',
                    'title'   => esc_html__('Currencies', 'solpay'),
                    'type'    => 'group',
                    'help'    => esc_html__('Enter the currencies in the solana network you accept to receive payments.', 'solpay'),
                    'button_title' => esc_html__('Add new', 'solpay'),
                    'desc'    => esc_html__('To test your tokens on the testnet, you can enter the information in the currencies field and select the testnet Cluster.', 'solpay'),
                    'default' => [
                        [ 
                            'symbol' =>  'USDT',
                            'address' =>  'Es9vMFrzaCERmJfrF4H2FYD4KCoNkY11McCe8BenwNYB',
                            'image' =>  $this->getImageUrl('usdt.png'),
                            'active' => true
                        ],
                        [ 
                            'symbol' =>  'USDC',
                            'address' =>  'EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v',
                            'image' =>  $this->getImageUrl('usdc.png'),
                            'active' => true
                        ],
                        [ 
                            'symbol' =>  'BUSD',
                            'address' =>  '33fsBLA8djQm82RpHmE3SuVrPGtZBWNYExsEUeKX1HXX',
                            'image' =>  $this->getImageUrl('busd.png'),
                            'active' => true
                        ],
                        [ 
                            'symbol' =>  'DAI',
                            'address' =>  'EjmyN6qEC1Tf1JxiG1ae7UTJhUxSwk1TCWNWqxWV4J6o',
                            'image' =>  $this->getImageUrl('dai.png'),
                            'active' => true
                        ],
                        [ 
                            'symbol' =>  'ETH',
                            'address' =>  '2FPyTwcZLUg1MDrwsyoP4D6s1tM7hAkHYRjkNb5w6Pxk',
                            'image' =>  $this->getImageUrl('eth.png'),
                            'active' => true
                        ],
                        [ 
                            'symbol' =>  'MATIC',
                            'address' =>  'C7NNPWuZCNjZBfW5p6JvGsR8pUdsRpEdP1ZAhnoDwj7h',
                            'image' =>  $this->getImageUrl('matic.png'),
                            'active' => true
                        ],
                    ],
                    'sanitize' => function($currencies) {
                        if (is_array($currencies)) {
                            foreach ($currencies as $key => &$currency) {
                                $currency['symbol'] = strtoupper(sanitize_text_field($currency['symbol']));
                                $currency['address'] = sanitize_text_field($currency['address']);
                                $currency['image'] = sanitize_text_field($currency['image']); 
                            }
                        }

                        return $currencies;
                    },
                    'validate' => function($currencies) {
                        if (is_array($currencies)) {
                            foreach ($currencies as $key => $currency) {
                                if (empty($currency['symbol'])) {
                                    return esc_html__('Currency symbol cannot be empty.', 'solpay');
                                } elseif (empty($currency['address'])) {
                                    return esc_html__('Currency contract address cannot be empty.', 'solpay');
                                } elseif (strlen($currency['address']) < 44 || strlen($currency['address']) > 44) {
                                    return esc_html__('Currency contract address must consist of 44 characters.', 'solpay');
                                } elseif (empty($currency['image'])) {
                                    return esc_html__('Currency image cannot be empty.', 'solpay');
                                }  
                            }
                        } else {
                            return esc_html__('You must add at least one blockchain network!', 'solpay');
                        }
                    },
                    'fields'    => array(
                        array(
                            'title' => esc_html__('Symbol', 'solpay'),
                            'id'    => 'symbol',
                            'type'  => 'text'
                        ),
                        array(
                            'title' => esc_html__('Token address', 'solpay'),
                            'id'    => 'address',
                            'type'  => 'text'
                        ),
                        array(
                            'title' => esc_html__('Image', 'solpay'),
                            'id'    => 'image',
                            'type'  => 'upload'
                        ),
                        array(
                            'id'      => 'active',
                            'title'   => esc_html__('Active/Passive', 'solpay'),
                            'type'    => 'switcher',
                            'help'    => esc_html__('You can easily activate or deactivate Token without deleting it.', 'solpay'),
                            'default' => true,
                        ),
                    ),
                ),
            ) 
        ));

        self::createSection(array(
            'id'     => 'customTokenValues', 
            'title'  => esc_html__('Custom token values', 'solpay'),
            'icon'   => 'fa fa-money',
            'fields' => array(
                array(
                    'id'           => 'customTokens',
                    'type'         => 'group',
                    'title'        => esc_html__('Custom tokens', 'solpay'),
                    'button_title' => esc_html__('Add new', 'solpay'),
                    'help'         => esc_html__('You can assign values ​​corresponding to fiat currencies to your own custom tokens.', 'solpay'),
                    'sanitize' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => &$value) {
                                $value['symbol'] = strtoupper(sanitize_text_field($value['symbol']));
                                if (isset($value['fiatMoneys'])) {
                                    foreach ($value['fiatMoneys'] as $key => &$money) {
                                        $money['symbol'] = strtoupper(sanitize_text_field($money['symbol']));
                                        $money['value'] = floatval($money['value']);
                                    }
                                }
                            }
                        }
                        
                        return $val;
                    },
                    'validate' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => $value) {
                                if (empty($value['symbol'])) {
                                    return esc_html__('Symbol cannot be empty.', 'solpay');
                                } elseif (!isset($value['fiatMoneys'])) {
                                    return esc_html__('You must add at least one FIAT money value!', 'solpay');
                                } elseif (isset($value['fiatMoneys'])) {
                                    foreach ($value['fiatMoneys'] as $key => $money) {
                                        if (empty($money['symbol'])) {
                                            return esc_html__('FIAT money symbol cannot be empty.', 'solpay');
                                        } elseif (empty($money['value'])) {
                                            return esc_html__('FIAT money value cannot be empty.', 'solpay');
                                        }
                                    }
                                }
                            }
                        }
                    },
                    'fields' => array(
                        array(
                            'title' => esc_html__('Symbol', 'solpay'),
                            'id'    => 'symbol',
                            'type'  => 'text'
                        ),
                        array(
                            'id'           => 'fiatMoneys',
                            'type'         => 'group',
                            'title'        => esc_html__('FIAT Moneys', 'solpay'),
                            'button_title' => esc_html__('Add new', 'solpay'),
                            'fields'      => array(
                                array(
                                    'title' => esc_html__('Symbol', 'solpay'),
                                    'id'    => 'symbol',
                                    'type'  => 'text',
                                    'help'  => esc_html__('The symbol of the fiat currency you want to value (ISO Code)', 'solpay')
                                ),
                                array(
                                    'title' => esc_html__('Value', 'solpay'),
                                    'id'    => 'value',
                                    'type'  => 'number',
                                ),
                            ),
                        ),
                    ),
                ),
            ) 
        ));

        self::createSection(array(
            'id'     => 'tokenDiscountsRates', 
            'title'  => esc_html__('Token discounts', 'solpay'),
            'icon'   => 'fa fa-percent',
            'fields' => array(
                array(
                    'id'           => 'tokenDiscounts',
                    'type'         => 'group',
                    'title'        => esc_html__('Token discounts', 'solpay'),
                    'button_title' => esc_html__('Add new', 'solpay'),
                    'help'         => esc_html__('You can define shopping-specific discounts for tokens with the symbols of the tokens.', 'solpay'),
                    'sanitize' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => &$value) {
                                $value['symbol'] = strtoupper(sanitize_text_field($value['symbol']));
                                $value['rate'] = floatval($value['rate']);
                            }
                        }

                        return $val;
                    },
                    'validate' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => $value) {
                                if (empty($value['symbol'])) {
                                    return esc_html__('Symbol cannot be empty.', 'solpay');
                                } elseif (empty($value['rate'])) {
                                    return esc_html__('Discount rate cannot be empty.', 'solpay');
                                }
                            }
                        }
                    },
                    'fields'      => array(
                        array(
                            'title' => esc_html__('Symbol', 'solpay'),
                            'id'    => 'symbol',
                            'type'  => 'text'
                        ),
                        array(
                            'title' => esc_html__('Discount rate (in %)', 'solpay'),
                            'id'    => 'rate',
                            'type'  => 'number'
                        ),
                    ),
                ),
            ) 
        ));

        $converters = apply_filters(
            "SolPay/WooCommerce/Converters", 
            [
                'cryptocompare' => 'Crypto Compare',
                'coinmarketcap' => 'Coin Market Cap',
                'coingecko' => 'Coingecko'
            ]
        );

        $apiOptions = apply_filters(
            "SolPay/WooCommerce/ApiOptions", 
            []
        );

        self::createSection(array(
            'id'     => 'apis', 
            'title'  => esc_html__('API\'s', 'solpay'),
            'icon'   => 'fa fa-project-diagram',
            'fields' => array_merge(array(
                array(
                    'id' => 'converter',
                    'type'  => 'select',
                    'title' => esc_html__('Converter API', 'solpay'),
                    'options' => $converters,
                    'default' => 'cryptocompare'
                ),
                array(
                    'id' => 'coinMarketCapApiKey',
                    'type'  => 'text',
                    'title' => esc_html__('Coin Market Cap API', 'solpay'),
                    'dependency' => array('converter', '==', 'coinmarketcap'),
                    'sanitize' => function($val) {
                        return sanitize_text_field($val);
                    }
                )
            ), $apiOptions)
        ));
        
        do_action("SolPay/WooCommerce/AddOnSettings");

        // self::createSection(array(
        //     'id'     => 'license', 
        //     'title'  => esc_html__('License', 'solpay'),
        //     'icon'   => 'fa fa-key',
        //     'fields' => array(
        //         array(
        //             'id'    => 'license',
        //             'type'  => 'text',
        //             'title' => esc_html__('License (Purchase code)', 'solpay'),
        //             'sanitize' => function($val) {
        //                 return sanitize_text_field($val);
        //             },
        //             'validate' => function($val) {
        //                 $val = sanitize_text_field($val);
        //                 if (empty($val)) {
        //                     return esc_html__('License cannot be empty.', 'solpay');
        //                 } elseif (strlen($val) < 36 || strlen($val) > 36) {
        //                     return esc_html__('License must consist of 36 characters.', 'solpay');
        //                 }

        //                 /** @var object $data */
        //                 $data = LicenseVerifier::verify($val, Plugin::$instance->settingKey);
        //                 if (!$data->success) {
        //                     return esc_html__($data->message . " - Error code: " . $data->errorCode, 'solpay');
        //                 }
        //             }
        //         ),
        //     ) 
        // ));

        self::createSection(array(
            'id'     => 'backup', 
            'title'  => esc_html__('Backup', 'solpay'),
            'icon'   => 'fa fa-shield',
            'fields' => array(
                array(
                    'type'  => 'backup',
                    'title' => esc_html__('Backup', 'solpay')
                ),
            ) 
        ));
    }

    public static function deleteLicense(): void
    {
        $settings = Plugin::$instance->setting();
        if (isset($settings['license'])) {
            unset($settings['license']);
            update_option(Plugin::$instance->settingKey, $settings);
        }
    }

    public static function getTokenDiscounts() : array
    {
        $tokenDiscounts = Plugin::$instance->setting('tokenDiscounts');

        if (!empty(self::$tokenDiscounts) || !is_array($tokenDiscounts)) {
            return self::$tokenDiscounts;
        }

        foreach ($tokenDiscounts as $key => $token) {
            if (!$token['symbol']) continue;
            $tokenSymbol = strtoupper($token['symbol']);
            self::$tokenDiscounts[$tokenSymbol] = floatval($token['rate']);
        }

        return self::$tokenDiscounts;
    }

    public static function getCustomTokens() : array
    {
        $customTokens = Plugin::$instance->setting('customTokens');

        if (!empty(self::$customTokens) || !is_array($customTokens)) {
            return self::$customTokens;
        }

        foreach ($customTokens as $key => $token) {
            if (!$token['symbol']) continue;
            $tokenSymbol = strtoupper($token['symbol']);
            self::$customTokens[$tokenSymbol] = [];
            foreach ($token['fiatMoneys'] as $key => $fiatMoney) {
                $fiatMoneySymbol = strtoupper($fiatMoney['symbol']);
                self::$customTokens[$tokenSymbol][$fiatMoneySymbol] = floatval($fiatMoney['value']); 
            }
        }

        return self::$customTokens;
    }

    public static function getWallets() : array
    {
		$wallets = Plugin::$instance->setting('wallets');
        
        if (isset($wallets['Sollet-Extension'])) {
            unset($wallets['Sollet-Extension']);
            $wallets['Sollet (Extension)'] = '1';
        }

        if (!empty(self::$wallets) || !$wallets) {
            return self::$wallets;
        }
		
        self::$wallets = array_filter($wallets, function($val) {
            return $val;
        });

        return array_keys(self::$wallets);
    }

    public static function getCurrencies() : array
    {
		$currencies = Plugin::$instance->setting('currencies');
        $nativeCurrency = Plugin::$instance->setting('nativeCurrency');

        if (!empty(self::$currencies) || !$currencies) {
            return self::$currencies;
        }

        $currencies2 = [];
        foreach ($currencies as $key => &$currency) {

            // Active/Passive control
            if (isset($currency['active']) && $currency['active'] != '1') continue;

            unset($currency['active']);
            
            $currency['symbol'] = trim(strtoupper($currency['symbol']));
            $currency['address'] = trim($currency['address']);

            $currencies2[] = $currency;
        }
        
        if ($nativeCurrency) {
            self::$currencies = array_merge([[
                'symbol' => 'SOL',
                'address' => 'SOL',
                'image' => Plugin::$instance->getImageUrl('sol.png')
            ]], $currencies2);
        }

        return self::$currencies;
    }
}