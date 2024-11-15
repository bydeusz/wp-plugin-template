<?php
// Add meta box to display the SEO Keyword in the sidebar of the edit screen
add_action('add_meta_boxes', 'seo_keyword_meta_box');

function seo_keyword_meta_box() {
    add_meta_box(
        'seo_keyword_meta_box',         
        'SEO Keyword',                   
        'seo_keyword_meta_box_callback', 
        'ai_shortcode',                  
        'normal',                           
        'high'
    );
}

// Callback function to display the SEO Keyword input field in the sidebar
function seo_keyword_meta_box_callback($post) {
    // Retrieve current keyword based on post ID
    $seo_keyword = get_post_meta($post->ID, '_seo_keyword', true);
    echo '<p>Enter SEO Keyword:</p>';
    echo '<input type="text" name="seo_keyword" value="' . esc_attr($seo_keyword) . '" style="width:100%;" required>';
}

// Save the SEO Keyword when the post is saved
add_action('save_post', 'save_seo_keyword_meta_box');

function save_seo_keyword_meta_box($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['seo_keyword'])) {
        return $post_id;
    }

    // Sanitize user input.
    $seo_keyword = sanitize_text_field($_POST['seo_keyword']);

    // Update the meta field in the database.
    update_post_meta($post_id, '_seo_keyword', $seo_keyword);
}

// Function to retrieve the SEO keyword for a given post ID
function get_seo_keyword($post_id) {
    return get_post_meta($post_id, '_seo_keyword', true);
}
?>
