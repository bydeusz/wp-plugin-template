<?php
/*
    Plugin Name: WP AI Content Generator
    Plugin URI: https://www.bydeusz.com
    Description: A WordPress plugin that generates AI content for websites.
    Version: 1.0
    Author: Tadeusz de Ruijter
    Author URI: https://www.bydeusz.com
    License: GPL2
*/

// Load Composer Autoload
require_once __DIR__ . '/vendor/autoload.php';

// Load Admin Files
require_once __DIR__ . '/admin/CustomPostType.php';
require_once __DIR__ . '/admin/Shortcode.php';
require_once __DIR__ . '/admin/Keyword.php';
require_once __DIR__ . '/admin/Template.php';
require_once __DIR__ . '/admin/GenerateButton.php';
require_once __DIR__ . '/admin/PromptField.php';
require_once __DIR__ . '/admin/Settings.php';
require_once __DIR__ . '/admin/ContentCount.php';
require_once __DIR__ . '/admin/PageOverview.php';
require_once __DIR__ . '/admin/Order.php';
require_once __DIR__ . '/admin/BulkChange.php';

use OpenAI;

// Enqueue JavaScript File
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_script(
        'ai-shortcode-script', 
        plugin_dir_url(__FILE__) . 'scripts/ai-shortcode.js',
        array('jquery'), 
        null, 
        true
    );
});

// Register AJAX Action
add_action('wp_ajax_generate_ai_content', 'ai_generate_content_ajax');

// Handle AJAX Request
function ai_generate_content_ajax() {
    check_ajax_referer('ai_generate_content_nonce_action', '_ajax_nonce');

    // Get the Open AI API key
    $open_ai_api_key = get_open_ai_api_key();

    // Retrieve the prompt and post ID from the request
    $prompt = sanitize_text_field($_POST['prompt']);
    $post_id = intval($_POST['post_id']);

    if (!$post_id) {
        wp_send_json_error('Invalid request.');
        wp_die();
    }

    // Haal de content van de post op
    $post = get_post($post_id);
    if (!$post) {
        wp_send_json_error('Post not found.');
        wp_die();
    }
    $post_content = $post->post_content;

    // Retrieve the SEO keyword for the post
    $seo_keyword = get_seo_keyword($post_id);

    // Retrieve the prompt template for the current post
    $prompt_template = get_prompt_template($post_id);

    // The full prompt includes the prompt template and SEO keyword
    $fullPrompt = $prompt_template . " (" . $post_content . ") Gebruik het volgende SEO keyword: " . $seo_keyword . ". Begin elke zin met een hoofdletter. Gebruik verder geen hoofdletters, behalve voor namen of merken. Gebruik geen codeblokken maar schrijf direct in de HTML-opmaak. Hou ook rekening met de tone of voice die in de tekst wordt gebruikt. " . $prompt;

    // Instantiate OpenAI client with a hardcoded API key
    $client = OpenAI::client($open_ai_api_key);

    try {
        // Use the chat method to generate content
        $response = $client->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'user', 'content' => $fullPrompt],
            ],
        ]);

        // Retrieve the generated content from the response
        $generatedContent = $response->choices[0]->message->content;

        // Update the post content (replace existing content)
        wp_update_post(array(
            'ID' => $post_id,
            'post_content' => $generatedContent
        ));

        // Send the generated content as a successful response
        wp_send_json_success($generatedContent);

    } catch (Exception $e) {
        // Handle and log any errors
        wp_send_json_error('Failed to generate content: ' . $e->getMessage());
    }

    wp_die();
}
?>