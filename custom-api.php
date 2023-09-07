<?php
/**
 * Plugin Name:       Custom API
 * Plugin URI:        https://webshore.io
 * Description:       A plugin starter with encrypted API key functionality for WordPress
 * Version:           0.1.0
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            H. Liebel
 * Author URI:        https://webshore.io
 * Text Domain:       webshr
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use Webshr\CustomAPI\Settings_Page;
use Webshr\CustomAPI\REST_Endpoint;

// Setup
define('WEBSHRAPIKEY_DIR', plugin_dir_path(__FILE__));
define('WEBSHRAPIKEY_FILE', __FILE__);

// Includes
$rootFiles = glob(WEBSHRAPIKEY_DIR . 'includes/*.php');
$subdirectoryFiles = glob(WEBSHRAPIKEY_DIR . 'includes/**/*.php');
$allFiles = array_merge($rootFiles, $subdirectoryFiles);

foreach($allFiles as $filename) {
  include_once($filename);
}

function custom_api_init() {
  $settingsPage = new Settings_Page();
  $APIEndpoint = new REST_Endpoint();

  // Fire hooks
  add_action('admin_menu', [$settingsPage, 'register_api_settings_page']);
  add_action('admin_post_external_api', [$settingsPage, 'submit_api_key']);
}

add_action('plugins_loaded', 'custom_api_init');