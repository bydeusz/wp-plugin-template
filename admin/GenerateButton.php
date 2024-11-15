<?php

// Include the Settings file
require_once __DIR__ . '/Settings.php';

// Get the Open AI API key
$open_ai_api_key = get_open_ai_api_key();

// Callback to display the generate button
function ai_shortcode_generate_button_callback($post) {
    global $open_ai_api_key;
    $disabled = empty($open_ai_api_key) ? 'disabled' : '';
    echo '<p><button type="button" class="button button-primary" id="generate_ai_content" style="background-color: green;" ' . $disabled . '>Generate AI Content</button></p>';
}
?>