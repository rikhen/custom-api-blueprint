<?php

namespace Webshr\CustomAPI;

class SettingsPage {

    private $data_encryption;

    public function __construct( DataEncryption $data_encryption ) {
        $this->data_encryption = $data_encryption;
        add_action('admin_menu', [$this, 'register_api_settings_page']);
        add_action('admin_post_external_api', [$this, 'submit_api_key']);
    }

    /**
     * Registers an API settings page in the WordPress admin dashboard.
     */
    public function register_api_settings_page() {
        add_submenu_page(
            'tools.php', // Parent slug
            'API Keys', // Title in menu
            'API Keys', // Page title
            'manage_options', // Permissions required to view this page
            'api-keys', // Slug for our page
            [$this, 'add_api_keys_callback'] // Callback to render the page
        );
    }
 
    /**
     * Callback function for the admin page containing the API key form.
     */
    public function add_api_keys_callback() {
        // Decrypt the API key from the database
        $api_key = $this->data_encryption->decrypt(get_option('api_key', ''));

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

    /**
     * Handles the submission of an API key.
     */
    public function submit_api_key() {
        // Make sure user actually has the capability to edit the options
        if (!current_user_can('edit_theme_options')) {
            wp_die("You do not have permission to view this page.");
        }
    
        // Verify the nonce to ensure the request came from our form
        check_admin_referer('api_options_verify');
    
        // If an API key was submitted, sanitize it and encrypt it
        if (isset($_POST['api_key'])) {
            $submitted_api_key = sanitize_text_field($_POST['api_key']);
            $api_key = $this->data_encryption->encrypt($submitted_api_key);

            // Check if an API key already exists in the database
            $api_exists = get_option('api_key');
    
            // If an API key exists, update it. Otherwise, add a new option.
            if (!empty($api_key) && !empty($api_exists)) {
                update_option('api_key', $api_key, 'no');
            } else {
                add_option('api_key', $api_key);
            }
        }
        // Redirect to same page with status=1 to show our options updated banner
        wp_redirect(wp_get_referer() . '&status=1');
    }
}