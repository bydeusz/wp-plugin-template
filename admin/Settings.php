<?php

// Add settings page
function ai_shortcode_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=ai_shortcode',
        'AI Shortcode Settings',          
        'Settings',                     
        'manage_options',                 
        'ai_shortcode_settings',       
        'ai_shortcode_settings_page' 
    );
}
add_action('admin_menu', 'ai_shortcode_add_settings_page');

// Settings page callback function
function ai_shortcode_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ai_shortcode_settings_group');
            do_settings_sections('ai_shortcode_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
function ai_shortcode_register_settings() {
    register_setting('ai_shortcode_settings_group', 'open_ai_api', 'sanitize_text_field');

    add_settings_section(
        'ai_shortcode_settings_section',
        'API Settings',
        null,
        'ai_shortcode_settings'
    );

    add_settings_field(
        'open_ai_api',
        'Open AI API',
        'ai_shortcode_open_ai_api_callback',
        'ai_shortcode_settings',
        'ai_shortcode_settings_section'
    );
}
add_action('admin_init', 'ai_shortcode_register_settings');

// Settings field callback function
function ai_shortcode_open_ai_api_callback() {
    $open_ai_api = get_option('open_ai_api');
    echo '<input type="password" id="open_ai_api" name="open_ai_api" value="' . esc_attr($open_ai_api) . '" size="50" />';
}

// Function to get the Open AI API key
function get_open_ai_api_key() {
    return get_option('open_ai_api');
}

// Display admin notice if API key is not set
function ai_shortcode_check_api_key() {
    if (!get_option('open_ai_api')) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('The Open AI API key is not set. Please go to the <a href="edit.php?post_type=ai_shortcode&page=ai_shortcode_settings">AI Shortcode Settings</a> and enter your API key.', 'ai_shortcode'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'ai_shortcode_check_api_key');
?>