<?php
// Voeg kolommen toe aan het overzicht en verwijder ongewenste kolommen
function set_custom_edit_ai_shortcode_columns($columns) {
    // Voeg nieuwe kolommen toe
    $columns['shortcode'] = __('Shortcode', 'your_text_domain');
    $columns['seo_keyword'] = __('Keyword', 'your_text_domain');
    $columns['prompt_template'] = __('Template', 'your_text_domain');
    $columns['ai_prompt'] = __('Prompt', 'your_text_domain');
    $columns['page_content'] = __('Content', 'your_text_domain');
    $columns['counter'] = __('Counter', 'your_text_domain');
    $columns['actions'] = __('Actions', 'your_text_domain');

    // Verwijder ongewenste kolommen
    unset($columns['date']);

    return $columns;
}
add_filter('manage_ai_shortcode_posts_columns', 'set_custom_edit_ai_shortcode_columns');

// Vul de kolommen met data
function custom_ai_shortcode_column($column, $post_id) {
    switch ($column) {
        case 'shortcode':
            $post_title = get_the_title($post_id);
            $shortcode = '[ai_shortcode id="' . $post_id . '" title="' . esc_attr($post_title) . '"]';
            echo '<input type="text" readonly="readonly" value="' . esc_attr($shortcode) . '" style="width:100%;" onclick="this.select();">';
            break;

        case 'seo_keyword':
            $seo_keyword = get_post_meta($post_id, '_seo_keyword', true);
            echo '<input type="text" class="inline-edit-seo-keyword" value="' . esc_attr($seo_keyword) . '" data-post-id="' . $post_id . '">';
            break;

        case 'prompt_template':
            global $input_options;
            $prompt_template = get_post_meta($post_id, '_prompt_template', true);
            echo '<select class="inline-edit-prompt-template" data-post-id="' . $post_id . '">';
            foreach ($input_options as $key => $value) {
                $selected = ($prompt_template === $key) ? 'selected' : '';
                echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($key) . '</option>';
            }
            echo '</select>';
            break;

        case 'ai_prompt':
            $ai_prompt = get_post_meta($post_id, 'ai_prompt', true);
            echo '<textarea class="inline-edit-ai-prompt" data-post-id="' . $post_id . '" rows="3" style="width:100%;">' . esc_textarea($ai_prompt) . '</textarea>';
            break;

        case 'page_content':
            $post_content = get_post_field('post_content', $post_id);
            echo '<div>' . esc_html($post_content) . '</div>';
            break;

        case 'actions':
            echo '<div style="display: flex; gap: 10px;">';
            echo '<button class="button button-secondary save-button" data-post-id="' . $post_id . '">Save</button>';
            echo '<button class="button button-secondary generate-ai-content-button" data-post-id="' . $post_id . '">Generate</button>';
            echo '</div>';
            echo '<div style="margin-top:10px;">';
            echo '<button class="button button-primary save-and-generate-button" data-post-id="' . $post_id . '">Save & Generate</button>';
            echo '</div>';
            break;

        case 'counter':
            $post_content = get_post_field('post_content', $post_id);
            $word_count = str_word_count(strip_tags($post_content));
            echo '<div>' . esc_html($word_count) . '</div>';
            break;
    }
}
add_action('manage_ai_shortcode_posts_custom_column', 'custom_ai_shortcode_column', 10, 2);

// Sla de inline bewerkingsdata op
function save_inline_edit_data() {
    $post_id = intval($_POST['post_id']);
    if (isset($_POST['seo_keyword'])) {
        update_post_meta($post_id, '_seo_keyword', sanitize_text_field($_POST['seo_keyword']));
    }
    if (isset($_POST['prompt_template'])) {
        update_post_meta($post_id, '_prompt_template', sanitize_text_field($_POST['prompt_template']));
    }
    if (isset($_POST['ai_prompt'])) {
        update_post_meta($post_id, 'ai_prompt', sanitize_textarea_field($_POST['ai_prompt']));
    }
    wp_send_json_success();
    wp_die();
}
add_action('wp_ajax_save_inline_edit_data', 'save_inline_edit_data');

// Voeg JavaScript toe om inline edit functionaliteit te ondersteunen
function enqueue_inline_edit_js() {
    global $typenow;
    if ($typenow == 'ai_shortcode') {
        wp_enqueue_script('inline-edit-js', plugin_dir_url(__FILE__) . '../scripts/inline-edit.js', array('jquery'), '', true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_inline_edit_js');

// Voeg de nonce-waarde toe aan de overzichtspagina
function add_nonce_to_overview_page() {
  global $typenow;
  if ($typenow == 'ai_shortcode') {
      wp_nonce_field('ai_generate_content_nonce_action', 'ai_generate_content_nonce');
  }
}
add_action('admin_footer', 'add_nonce_to_overview_page');
?>