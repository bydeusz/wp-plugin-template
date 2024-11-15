<?php
// Callback to display the input field
function ai_shortcode_prompt_callback($post) {
    // Add a nonce field for security
    wp_nonce_field('ai_generate_content_nonce_action', 'ai_generate_content_nonce');

    // Retrieve the current value of the prompt field
    $ai_prompt = get_post_meta($post->ID, 'ai_prompt', true);

    echo '<p><label for="ai_prompt">You can customize your prompt even further with this field:</label></p>';
    echo '<textarea id="ai_prompt" name="ai_prompt" rows="3" style="width:100%;">' . esc_attr($ai_prompt) . '</textarea>';
    echo '<div id="ai_generated_content"></div>';
}

// Save the prompt field value
function save_ai_shortcode_prompt($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['ai_generate_content_nonce'])) {
        return $post_id;
    }
    $nonce = $_POST['ai_generate_content_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'ai_generate_content_nonce_action')) {
        return $post_id;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'ai_shortcode' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    }

    // Sanitize user input.
    $new_meta_value = (isset($_POST['ai_prompt']) ? sanitize_text_field($_POST['ai_prompt']) : '');

    // Update the meta field in the database.
    update_post_meta($post_id, 'ai_prompt', $new_meta_value);
}
add_action('save_post', 'save_ai_shortcode_prompt');
?>