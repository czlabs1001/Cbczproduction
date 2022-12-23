<?php

namespace BeycanPress\SolPay\WooCommerce\PluginHero;

use \CSF;

abstract class Setting
{
    use Helpers;

    /**
     * @var string
     */
    private static $prefix;

    /**
     * @param string $prefix
     * @param string $title
     * @param string|null $parent
     * @return void
     */
    public function __construct(string $prefix, string $title, string $parent = null)
    {
        self::$prefix = $prefix;
        
        require_once $this->phDir . 'csf/csf.php';

        $params = array(

            'framework_title'         => $title . ' <small>By FLWR Pay</small>',

            // menu settings
            'menu_title'              => $title,
            'menu_slug'               => $prefix,
            'menu_capability'         => 'manage_options',
            'menu_position'           => null,
            'menu_hidden'             => false,

            // menu extras
            'show_bar_menu'           => false,
            'show_sub_menu'           => false,
            'show_network_menu'       => true,
            'show_in_customizer'      => false,

            'show_search'             => true,
            'show_reset_all'          => true,
            'show_reset_section'      => true,
            'show_footer'             => true,
            'show_all_options'        => true,
            'sticky_header'           => true,
            'save_defaults'           => true,
            'ajax_save'               => true,
            
            // database model
            'transient_time'          => 0,

            // contextual help
            'contextual_help'         => array(),

            // typography options
            'enqueue_webfont'         => false,
            'async_webfont'           => false,

            // others
            'output_css'              => false,

            // theme
            'theme'                   => 'dark',

            // external default values
            'defaults'                => array(),

        );

        if (!is_null($parent)) {
            $params['menu_type'] = 'submenu';
            $params['menu_parent'] = $parent;
        }

        CSF::createOptions($prefix, $params);
    }

    /**
     * @param array $params
     * @return void
     */
    public static function createSection(array $params)
    {
        CSF::createSection(self::$prefix, $params);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        return Plugin::$instance->setting($key);
    }

}