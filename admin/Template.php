<?php
function prompt_template_meta_box() {
    add_meta_box(
        'prompt_template_meta_box',         
        'Prompt Template',                   
        'prompt_template_meta_box_callback', 
        'ai_shortcode',                  
        'normal',                           
        'high'
    );
}
add_action('add_meta_boxes', 'prompt_template_meta_box');

$input_options = [
    'H1' => 'Schrijf een H1 voor mijn pagina. Gebruikt de tekst binnen de haakjes als inspiratie en plaats deze in een <h1>-element. Begin de zin altijd met het keyword.',
    'H2' => 'Herschrijf de tekst binnen de haakjes voor een H2 en plaats de tekst in een <h2>-element. Verwerk het keyword in de tekst, maar het hoeft niet aan het begin te staan. Gebruik geen andere HTML-elementen.',
    'H3' => 'Herschrijf de tekst binnen de haakjes voor een H3 en plaats de tekst in een <h3>-element. Verwerk het keyword in de tekst, maar het hoeft niet aan het begin te staan. Gebruik geen andere HTML-elementen.',
    'Text' => 'Herschrijf de tekst binnen de haakjes. Verwerk het keyword in de tekst en gebruik alleen het HTML-element <p>.',
    'List' => 'Genereer een lijst en plaats deze in een <ul>- of <ol>-element, afhankelijk van de context. Zorg ervoor dat elk item in een <li>-element staat. Gebruik de tekst tussen de haakjes als inspiratie. Gebruik uitsluitend <ul>, <ol>, en <li>-elementen.',
    'Meta title' => 'Schrijf een meta titel voor de pagina. Gebruik deze structuur: Keyword | USP [Top USP]. Deze tekst mag maximaal 60 characters zijn. Gebruik de tekst binnen de haakjes als inspiratie.',
    'Meta description' => 'Schrijf een meta description voor de pagina. Deze tekst mag maximaal 160 characters zijn. Gebruik de tekst binnen de haakjes als inspiratie.',
];

function prompt_template_meta_box_callback($post) {
    global $input_options;
    wp_nonce_field('save_prompt_template_meta_box_data', 'prompt_template_nonce');
    $prompt_template = get_post_meta($post->ID, '_prompt_template', true);
    echo '<p>Select Prompt Template:</p>';
    echo '<select name="prompt_template" style="width:100%;">';
    foreach ($input_options as $key => $value) {
        $selected = ($prompt_template === $key) ? 'selected' : '';
        echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($key) . '</option>';
    }
    echo '</select>';
}

function save_prompt_template_meta_box_data($post_id) {
    if (!isset($_POST['prompt_template_nonce'])) {
        return $post_id;
    }
    $nonce = $_POST['prompt_template_nonce'];
    if (!wp_verify_nonce($nonce, 'save_prompt_template_meta_box_data')) {
        return $post_id;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    if (!isset($_POST['prompt_template'])) {
        return $post_id;
    }
    $prompt_template = sanitize_text_field($_POST['prompt_template']);
    update_post_meta($post_id, '_prompt_template', $prompt_template);
}
add_action('save_post', 'save_prompt_template_meta_box_data');

// Function to get the prompt template value
function get_prompt_template($post_id) {
    global $input_options;

    $prompt_template_key = get_post_meta($post_id, '_prompt_template', true);

    return isset($input_options[$prompt_template_key]) ? $input_options[$prompt_template_key] : '';
}
?>