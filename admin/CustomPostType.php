<?php

// Hook into the 'init' action
add_action('init', 'register_ai_shortcode_post_type');

function register_ai_shortcode_post_type() {
    $labels = array(
        'name'               => _x('AI Shortcodes', 'post type general name'),
        'singular_name'      => _x('AI Shortcode', 'post type singular name'),
        'menu_name'          => _x('AI Shortcodes', 'admin menu'),
        'name_admin_bar'     => _x('AI Shortcode', 'add new on admin bar'),
        'add_new'            => _x('Add New', 'ai_shortcode'),
        'add_new_item'       => __('Add New Shortcode'),
        'new_item'           => __('New Shortcode'),
        'edit_item'          => __('Edit Shortcode'),
        'view_item'          => __('View Shortcode'),
        'all_items'          => __('Shortcodes'),
        'search_items'       => __('Search Shortcodes'),
        'parent_item_colon'  => __('Parent Shortcodes:'),
        'not_found'          => __('No Shortcodes found.'),
        'not_found_in_trash' => __('No Shortcodes found in Trash.')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'ai-shortcode'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 100, // Place it at the end of the admin menu
        'supports'           => array('title', 'editor')
    );

    register_post_type('ai_shortcode', $args);
}

// Add meta boxes to the AI Shortcode post type edit screen
add_action('add_meta_boxes', 'ai_shortcode_add_meta_boxes');

function ai_shortcode_add_meta_boxes() {
    add_meta_box(
        'ai_prompt_meta_box',           // ID
        'Extra prompt',         // Title
        'ai_shortcode_prompt_callback', // Callback function
        'ai_shortcode',                 // Post type
        'normal',                       // Context
        'high'                          // Priority
    );

    add_meta_box(
        'ai_generate_button_meta_box',  // ID
        'Generate AI Content',          // Title
        'ai_shortcode_generate_button_callback', // Callback function
        'ai_shortcode',                 // Post type
        'side',                         // Context
        'high'                          // Priority
    );
}

?>