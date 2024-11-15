<?php
// Add the meta box
function content_count_meta_box() {
    add_meta_box(
        'content_count_meta_box',         
        'Content counter',                   
        'content_count_meta_box_callback', 
        'ai_shortcode',
        'side',                           
        'high'
    );
}
add_action('add_meta_boxes', 'content_count_meta_box');

// Callback function to display the content count in the sidebar
function content_count_meta_box_callback($post) {
    // Retrieve the content of the post
    $content = get_post_field('post_content', $post->ID);
    // Calculate the word count
    $word_count = str_word_count(strip_tags($content));
    // Display the word count
    echo '<p>Word Count: ' . $word_count . '</p>';
}

// Save the meta box data (if needed, but in this case, we are just displaying data)
function save_content_count_meta_box_data($post_id) {
    // No need to save anything for this meta box
}
add_action('save_post', 'save_content_count_meta_box_data');
?>