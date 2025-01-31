<?php
// Add meta box to display the order input field in the sidebar of the edit screen
add_action('add_meta_boxes', 'order_meta_box');

function order_meta_box() {
    add_meta_box(
        'order_meta_box',         
        'Order',                   
        'order_meta_box_callback', 
        'ai_shortcode',                  
        'side',                           
        'high'
    );
}

// Callback function to display the order input field in the sidebar
function order_meta_box_callback($post) {
    // Retrieve current order based on post ID
    $order = get_post_meta($post->ID, '_order', true);
    echo '<input type="number" name="order" value="' . esc_attr($order) . '" style="width:100%;" required>';
}

// Save the order when the post is saved
add_action('save_post', 'save_order_meta_box');

function save_order_meta_box($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['order'])) {
        return $post_id;
    }

    // Sanitize user input.
    $order = sanitize_text_field($_POST['order']);

    // Update the meta field in the database.
    update_post_meta($post_id, '_order', $order);
}

function sort_ai_shortcodes_by_order($query) {
  // Check if we are in the admin area and the main query is for the 'ai_shortcode' post type
  if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'ai_shortcode') {
      // Set the query to order by the 'order' meta value
      $query->set('meta_key', '_order');
      $query->set('orderby', 'meta_value_num');
      $query->set('order', 'ASC'); // Change to 'DESC' for descending order
  }
}
add_action('pre_get_posts', 'sort_ai_shortcodes_by_order');
?>