<?php
// Shortcode to display AI Shortcode post content
function ai_shortcode_display($atts) {
    $atts = shortcode_atts(array(
        'id' => ''
    ), $atts, 'ai_shortcode');

    $post_id = $atts['id'];
    $post = get_post($post_id);

    if ($post && $post->post_type == 'ai_shortcode') {
        return apply_filters('the_content', $post->post_content);
    }

    return '';
}
add_shortcode('ai_shortcode', 'ai_shortcode_display');

// Add meta box to display the AI Shortcode in the sidebar of the edit screen
add_action('add_meta_boxes', 'ai_shortcode_meta_box');

function ai_shortcode_meta_box() {
    add_meta_box(
        'ai_shortcode_meta_box',         
        'AI Shortcode',                   
        'ai_shortcode_meta_box_callback', 
        'ai_shortcode',                  
        'normal',                           
        'high'
    );
}

// Callback function to display the shortcode in the sidebar
function ai_shortcode_meta_box_callback($post) {
    $shortcode = '[ai_shortcode id="' . $post->ID . '" title="' . get_the_title($post->ID) . '"]';
    echo '<p>Copy this shortcode:</p>';
    echo '<input type="text" readonly="readonly" value="' . esc_attr($shortcode) . '" style="width:100%;">';
}
?>