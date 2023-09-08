<?php

namespace Webshr\CustomAPI;

class SettingsPage {

    public function __construct() {
        add_action('admin_menu', [$this, 'register_api_settings_page']);
        add_action('admin_post_external_api', [$this, 'submit_api_key']);
    }

    public function register_api_settings_page() {
        add_submenu_page(
            'tools.php', // Add our page under the "Tools" menu
            'API Keys', // Title in menu
            'API Keys', // Page title
            'manage_options', // permissions
            'api-keys', // slug for our page
            [$this, 'add_api_keys_callback'] // Callback to render the page
        );
    }
 
    // The admin page containing the form
    public function add_api_keys_callback() {
        $data_encryption = new DataEncryption();
        $api_key = get_option('api_key');

        if($api_key) {
            $api_key = $data_encryption->decrypt($api_key);
        }

        ?>
        <div class="wrap">
            <h2>API key settings</h2>
            <?php
            // Check if status is 1 which means a successful options save just happened
            if(isset($_GET['status']) && $_GET['status'] == 1): ?>
                <div class="notice notice-success inline">
                    <p>Options Saved!</p>
                </div>
            <?php endif; ?>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                <h3>Your API Key</h3>
                <!-- The nonce field is a security feature to avoid submissions from outside WP admin -->
                <?php wp_nonce_field('api_options_verify'); ?>
                <input type="password" name="api_key" placeholder="Enter API Key" value="<?php echo $api_key ? esc_attr($api_key) : ''; ?>">
                <input type="hidden" name="action" value="external_api">			 
                <input type="submit" name="submit" id="submit" class="update-button button button-primary" value="Update API Key" />
            </form> 
        </div>
        <?php
    }

    // Submit functionality
    public function submit_api_key() {
        // Make sure user actually has the capability to edit the options
        if (!current_user_can('edit_theme_options')) {
            wp_die("You do not have permission to view this page.");
        }
    
        // pass in the nonce ID from our form's nonce field - if the nonce fails this will kill script
        check_admin_referer('api_options_verify');
    
        if (isset($_POST['api_key'])) {
            $data_encryption = new DataEncryption();
            $submitted_api_key = sanitize_text_field($_POST['api_key']);
            $api_key = $data_encryption->encrypt($submitted_api_key);
    
            $api_exists = get_option('api_key');
    
            if (!empty($api_key) && !empty($api_exists)) {
                update_option('api_key', $api_key);
            } else {
                add_option('api_key', $api_key);
            }
        }
        // Redirect to same page with status=1 to show our options updated banner
        wp_redirect($_SERVER['HTTP_REFERER'] . '&status=1');
    }
}