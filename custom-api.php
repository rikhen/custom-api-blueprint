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

namespace Webshr\CustomAPI;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Require autoload file.
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
  require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

use Webshr\CustomAPI\SettingsPage;
use Webshr\CustomAPI\RestEndpoint;

// Setup
define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ));
define( 'PLUGIN', plugin_basename( __FILE__ ));

// Initializes the plugin by creating instances of the DataEncryption, SettingsPage and RestEndpoint classes.
function plugin_init() {
  $data_encryption = new DataEncryption();
  $settings_page = new SettingsPage($data_encryption);
  $APIEndpoint = new RestEndpoint();

  // Fire hooks to register the API settings page and submit the API key.
  add_action('admin_menu', [$settings_page, 'register_api_settings_page']);
  add_action('admin_post_external_api', [$settings_page, 'submit_api_key']);
}

add_action('plugins_loaded', __NAMESPACE__ . '\\plugin_init');