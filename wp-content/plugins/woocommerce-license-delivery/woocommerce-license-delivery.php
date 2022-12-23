<?php
/**
 * @link              http://highthemes.com
 * @package           WooCommerce_License_Delivery
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce License Delivery
 * Plugin URI:        http://highthemes.com
 * Description:       A WooCommerce Addon for selling license codes, gift cards, digital pins, etc
 * Version:           2.1.5
 * Author:            HighThemes
 * Author URI:        http://highthemes.com/
 * Text Domain:       highthemes
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WC_LD_PLUGIN_FILE', __FILE__ );
define( 'WC_LD_PLUGIN_VER', '2.1.5' );

/**
 * update plugin to 1.5 version changes depracted will be removed from next versions
 */
function wc_ldm_upgrade_license_table( $upgrader_object, $options ) {
    $current_plugin_path_name = plugin_basename( __FILE__ );

    if ($options['action'] == 'update' && $options['type'] == 'plugin' ){
       foreach($options['plugins'] as $each_plugin){
          if ($each_plugin==$current_plugin_path_name){
             // .......................... YOUR CODES .............
             global $wpdb;
             $table=$wpdb->prefix . 'wc_ld_license_codes';
             $check = $wpdb->get_row(sprintf("SELECT * FROM %s LIMIT 1", $table));
            //Add column if not present.
            if(!isset($check->license_code5)){
                $wpdb->query(sprintf("ALTER TABLE %s ADD license_code5 text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL",$table));
            }

          }
       }
    }
}
add_action( 'upgrader_process_complete', 'wc_ldm_upgrade_license_table',10, 2);
/**
 * the core class
 */
require plugin_dir_path( __FILE__ ) . 'classes/class-woocommerce-license-delivery.php';

/**
 * activate the plugin
 */
function activate_wc_license_delivery() {
	require_once plugin_dir_path( __FILE__ ) . 'classes/class-wc-ld-activator.php';
	WC_LD_Activator::activate();
}
/**
 * deactivate the plugin
 */
function deactivate_wc_license_delivery() {
	require_once plugin_dir_path( __FILE__ ) . 'classes/class-wc-ld-deactivator.php';
	WC_LD_Deactivator::deactivate();
}

/**
 * register the activation/deactivation hooks
 */
register_activation_hook( __FILE__, 'activate_wc_license_delivery' );
register_deactivation_hook( __FILE__, 'deactivate_wc_license_delivery' );

/**
 * run the plugin
 */
function run_woocommerce_license_delivery() {
	new WooCommerce_License_Delivery();
}
// start plugin
add_action( 'plugins_loaded', 'run_woocommerce_license_delivery', 10 );