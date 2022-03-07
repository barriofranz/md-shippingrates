<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://morningstardigital.com.au/
 * @since             1.0.0
 * @package           Md_Master_Shipping_Rates
 *
 * @wordpress-plugin
 * Plugin Name:       MD Master Shipping Rates
 * Plugin URI:        https://morningstardigital.com.au/
 * Description:       Adds a Master of shipping rates feature
 * Version:           1.0.1
 * Author:            Morningstar Digital
 * Author URI:        https://morningstardigital.com.au/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       md-master-shipping-rates
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MD_MASTER_SHIPPING_RATES_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-md-master-shipping-rates-activator.php
 */
function activate_md_master_shipping_rates() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-md-master-shipping-rates-activator.php';
	Md_Master_Shipping_Rates_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-md-master-shipping-rates-deactivator.php
 */
function deactivate_md_master_shipping_rates() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-md-master-shipping-rates-deactivator.php';
	Md_Master_Shipping_Rates_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_md_master_shipping_rates' );
register_deactivation_hook( __FILE__, 'deactivate_md_master_shipping_rates' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-md-master-shipping-rates.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_md_master_shipping_rates() {

	$plugin = new Md_Master_Shipping_Rates();
	$plugin->run();

}
run_md_master_shipping_rates();


///////////////////////////////////

add_filter( 'woocommerce_get_sections_shipping', 'md_msr_add_section' );
function md_msr_add_section( $sections ) {
	$sections['master-rates'] = 'Master Rates';
	return $sections;

}

// add_action( 'woocommerce_update_options_shipping', 'update_settings', 20, 1);
//
// function update_settings($tab) {
// 	// echo '<pre>222';print_r($tab);echo '</pre>';
// 	// die();
//     // woocommerce_update_options( get_settings() );
// }

add_filter( 'woocommerce_get_settings_shipping', 'md_msr_all_settings', 10, 2 );
function md_msr_all_settings( $settings, $current_section ) {
	/**
	 * Check the current section is what we want
	 **/

	if ( $current_section == 'master-rates' ) {

		$pluginList = get_option( 'active_plugins' );
		$plugin = 'woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php';
		if ( in_array( $plugin , $pluginList ) ) {
			// echo 'Requires table rates plugin';
			// Plugin 'mg-post-contributors' is Active
		} else {
			echo 'Plugin table rates is inactive';
			return;
		}

		$settings_slider = array();

		// Add Title to the Settings
		// $settings_slider[] = array( 'name' => 'Master Table of Rates', 'type' => 'title', 'desc' => '', 'id' => 'master-rates' );
		// // Add first checkbox option
		// $settings_slider[] = array(
		// 		'name' => __( 'Enable Free shipping notices' ),
		// 		'type' => 'checkbox',
		// 		'desc' => __( 'Show the free shipping threshold on product page'),
		// 		'id'	=> 'enable'
		//
		// );
		// $settings_slider[] = array(
		// 		'name' => __( 'Activate' ),
		// 		'type' => 'customtype',
		// 		'desc' => __( 'Activate plugin'),
		// 		'desc_tip' => true,
		// 		'class' => 'button-secondary',
		// 		'id'	=> 'activate',
		//
		// );
		//



		$zones = WC_Shipping_Zones::get_zones();
		// $methods = array_map(function($zone) {
		//     return $zone['shipping_methods'];
		// }, $zones);
		// foreach ($zones as $zone) {
		// 	if(isset($zone['shipping_methods'])) {
		// 		// echo '<pre>';print_r($zone['shipping_methods']);echo '</pre>';
		// 		foreach ($zone['shipping_methods'] as $method) {
		// 			echo $method->title . '<br>';
		// 		}
		// 	} else {
		// 		echo '<pre>';print_r($zone);echo '</pre>';
		// 	}
		// }

		// $shippingRates = new BETRS_Table_Rates();
		$betrsTableOptions = new BETRS_Table_Options();
		// $shippingRates->init_conditions;
		// echo '<pre>';print_r($betrsTableOptions->generate_conditions_section);echo '</pre>';

		include plugin_dir_path( __FILE__ ) . 'public/partials/md-master-shipping-rates-public-menu.php';
		// $settings_slider[] = array( 'type' => 'sectionend', 'id' => 'md_msr_section_shipping_end' );
		return $settings_slider;

	} else {
		return $settings;
	}

}


add_action("wp_ajax_msr_update_shipping_option", "msr_update_shipping_option");
function msr_update_shipping_option()
{
	$dataOption = $_POST['dataOption'];
	$datas = [];
	parse_str($_POST['datas'], $datas);
	$betrsTableOptions = new BETRS_Table_Rates();
	$_POST = $datas;

	foreach ($datas['option_title'] as $key => $val){
		if( !isset($_POST['default_select'][$key]) || $_POST['default_select'][$key] == 'off' ) {
			unset($_POST['default_select'][$key]);
		}
		if( !isset($_POST['hide_ops'][$key]) || $_POST['hide_ops'][$key] == 'off' ) {
			unset($_POST['hide_ops'][$key]);
		}
		if( !isset($_POST['disable_op'][$key]) || $_POST['disable_op'][$key] == 'off' ) {
			unset($_POST['disable_op'][$key]);
		}
		if( !isset($_POST['combine_desc']) || $_POST['combine_desc'][$key] == 'off' ) {
			unset($_POST['combine_desc'][$key]);
		}
		if( !isset($_POST['recursive_op'][$key]) || $_POST['recursive_op'][$key] == 'off' ) {
			unset($_POST['recursive_op'][$key]);
		}
	}

	$betrsTableOptions->process_table_rates($dataOption);

	$shippingOptions = get_option( 'betrs_shipping_options-2');

	die();
}
