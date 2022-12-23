<?php defined('ABSPATH') || exit;

/**
 * Plugin Name: FLWR WooCommerce Pay
 * Version:     0.1.0
 * Plugin URI:  https://czlabs.io/
 * Description: Plugin for WooCommerce to receive payments via the Solana with FLWR Pay
 * Author: Flwr Pay by czlabs
 * Author URI:  https://czlabs.io
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.tr.html
 * Text Domain: FLWR pay
 * Domain Path: /languages
 * Tags: FLWR Pay FLWR Pay, Cryptocurrency, WooCommerce, Phantom, Sollet, Slope, Solflare, Torus, Bitcoin, Solana, Payment, Plugin, Blockchain
 * Requires at least: 5.0
 * Tested up to: 6.0
 * Requires PHP: 7.4
*/

require __DIR__ . '/vendor/autoload.php';
new \BeycanPress\SolPay\WooCommerce\Loader(__FILE__);