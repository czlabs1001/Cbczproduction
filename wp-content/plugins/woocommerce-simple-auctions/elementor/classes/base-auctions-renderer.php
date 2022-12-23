<?php
namespace ElementorPro\Modules\Woocommerce\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $woocommerce_auctions;
require_once $woocommerce_auctions->plugin_path . 'classes/class-wc-shortcode-simple-auctions.php';

abstract class Base_Auctions_Renderer extends \WC_Shortcode_Simple_Auctions {

	/**
	 * Override original `get_content` that returns an HTML wrapper even if no results found.
	 *
	 * @return string Products HTML
	 */
	public function get_content() {
		$result = $this->get_query_results();
		if ( empty( $result->total ) ) {
			return '';
		}

		return parent::get_content();
	}
}
