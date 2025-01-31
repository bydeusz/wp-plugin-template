<?php
function add_bulk_change_div() {
    global $typenow;
    if ($typenow == 'ai_shortcode') {
        echo '<div style="display: flex; justify-content: center; align-items: center; margin: 20px 0 0 0; padding-right: 20px;">';

        // Outer container
        echo '<div style="position: relative; margin: 20px 0; padding: 10px 20px; border: 1px solid #ccc; background-color: #fff; width: 100%;">';
        
        // Header with accordion toggle
        echo '<div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleAccordion()">';
        echo '<h2 style="margin: 10px 0;">Bulk Change</h2>';
        echo '<button type="button" id="accordion-toggle" style="border: none; background: none; font-size: 16px; cursor: pointer;">&#9660;</button>';
        echo '</div>';
        
        // Accordion content
        echo '<div id="accordion-content" style="display: none; margin-top: 20px;">';
        
        // Keyword input
        echo '<div style="margin-bottom: 20px;">';
        echo '<label for="keyword" style="display: block; margin-bottom: 5px;">Keyword:</label>';
        echo '<input type="text" id="keyword" name="keyword" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">';
        echo '</div>';
        
        // Prompt input
        echo '<div style="margin-bottom: 20px;">';
        echo '<label for="prompt" style="display: block; margin-bottom: 5px;">Prompt:</label>';
        echo '<textarea id="prompt" name="prompt" style="width: 100%; height: 100px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"></textarea>';
        echo '</div>';
        
        // Buttons container
        echo '<div style="display: flex; justify-content: flex-end; margin-top: 20px;">';
        echo '<button type="button" class="button button-secondary" id="bulk-save-button" style="margin-right: 10px; padding: 10px 20px; border-radius: 4px;">Save</button>';
        echo '<button type="button" class="button button-primary" id="bulk-generate-button" style="padding: 10px 20px; border-radius: 4px;">Generate</button>';
        echo '</div>';
        
        // Progress container
        echo '<div id="progress-container" style="margin-top: 20px; display: none;">';
        echo '<div id="progress-bar" style="width: 0%; height: 20px; background-color: green; transition: width 0.3s;"></div>';
        echo '<p id="progress-text" style="margin-top: 10px;">0 / 0 shortcodes processed</p>';
        echo '</div>';
        
        echo '</div>'; 
        
        echo '</div>';
        echo '</div>';
    }
}
add_action('all_admin_notices', 'add_bulk_change_div');

// Handle AJAX request to update all keywords
function update_all_keywords() {
    // Check if the keyword is set and not empty
    if (!isset($_POST['keyword']) || empty(trim($_POST['keyword']))) {
        wp_send_json_error('Keyword is not set or is empty.');
        wp_die();
    }

    // Sanitize the keyword
    $keyword = sanitize_text_field($_POST['keyword']);

    // Get all posts of type 'ai_shortcode'
    $args = array(
        'post_type' => 'ai_shortcode',
        'posts_per_page' => -1,
    );
    $posts = get_posts($args);

    // Update the keyword for each post
    foreach ($posts as $post) {
        // Retrieve the current keyword
        $current_keyword = get_post_meta($post->ID, '_seo_keyword', true);

        // Only update if the current keyword is NOT empty
        if (!empty($current_keyword)) {
            update_post_meta($post->ID, '_seo_keyword', $keyword);
        }
    }

    wp_send_json_success('Only posts with existing keywords were updated successfully.');
    wp_die();
}
add_action('wp_ajax_update_all_keywords', 'update_all_keywords');


// Handle AJAX request to update all prompts
function update_all_prompts() {
    // Check if the prompt is set and not empty
    if (!isset($_POST['prompt']) || empty($_POST['prompt'])) {
        wp_send_json_error('Prompt is not set or is empty.');
        wp_die();
    }

    // Sanitize the prompt
    $prompt = sanitize_text_field($_POST['prompt']);

    // Get all posts of type 'ai_shortcode'
    $args = array(
        'post_type' => 'ai_shortcode',
        'posts_per_page' => -1,
    );
    $posts = get_posts($args);

    // Update the prompt for each post
    foreach ($posts as $post) {
        update_post_meta($post->ID, 'ai_prompt', $prompt);
    }

    wp_send_json_success('All prompts updated successfully.');
    wp_die();
}
add_action('wp_ajax_update_all_prompts', 'update_all_prompts');

// Handle AJAX request to bulk generate content for all shortcodes
function bulk_generate_all_shortcodes() {
    // Check if the current step is set
    if (!isset($_POST['step'])) {
        wp_send_json_error('Step is not set.');
        wp_die();
    }

    $step = intval($_POST['step']);

    // Get all posts of type 'ai_shortcode'
    $args = array(
        'post_type' => 'ai_shortcode',
        'posts_per_page' => -1,
    );
    $posts = get_posts($args);
    $total_posts = count($posts);

    if ($step >= $total_posts) {
        wp_send_json_success('All shortcodes generated successfully.');
        wp_die();
    }

    $post = $posts[$step];

    // Retrieve the prompt and other necessary data
    $prompt = get_post_meta($post->ID, 'ai_prompt', true);
    $seo_keyword = get_seo_keyword($post->ID);
    $prompt_template = get_prompt_template($post->ID);
    $post_content = $post->post_content;

    // Combine the prompt template, post content, and SEO keyword
    $full_prompt = $prompt_template . " (" . $post_content . ") Gebruik het volgende SEO keyword: " . $seo_keyword . ". Begin elke zin met een hoofdletter. Gebruik verder geen hoofdletters, behalve voor namen of merken. Gebruik geen codeblokken maar schrijf direct in de HTML-opmaak. Hou ook rekening met de tone of voice die in de tekst wordt gebruikt. " . $prompt;

    // Generate content using the OpenAI API
    $generated_content = generate_ai_content($full_prompt);

    // Update the post content with the generated content
    wp_update_post(array(
        'ID' => $post->ID,
        'post_content' => $generated_content
    ));

    // Update the progress
    $processed_posts = $step + 1;
    set_transient('bulk_generate_progress', array('processed' => $processed_posts, 'total' => $total_posts), 3600);

    wp_send_json_success(array('processed' => $processed_posts, 'total' => $total_posts));
    wp_die();
}
add_action('wp_ajax_bulk_generate_all_shortcodes', 'bulk_generate_all_shortcodes');

// Handle AJAX request to get bulk generate progress
function get_bulk_generate_progress() {
    $progress = get_transient('bulk_generate_progress');
    if ($progress) {
        wp_send_json_success($progress);
    } else {
        wp_send_json_error('No progress data found.');
    }
}
add_action('wp_ajax_get_bulk_generate_progress', 'get_bulk_generate_progress');

// Function to generate AI content using the OpenAI API
function generate_ai_content($prompt) {
    $open_ai_api_key = get_open_ai_api_key();
    $client = OpenAI::client($open_ai_api_key);

    try {
        $response = $client->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        // Retrieve the generated content from the response
        return $response->choices[0]->message->content;
    } catch (Exception $e) {
        // Handle and log any errors
        error_log('Failed to generate content: ' . $e->getMessage());
        return 'Failed to generate content.';
    }
}

// Enqueue the bulk-change.js script
function enqueue_bulk_change_js() {
    wp_enqueue_script(
        'bulk-change-script', 
        plugin_dir_url(__FILE__) . '../scripts/bulk-change.js', 
        array('jquery'), 
        null, 
        true
    );
}
add_action('admin_enqueue_scripts', 'enqueue_bulk_change_js');
?>