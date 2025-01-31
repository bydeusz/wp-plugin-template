jQuery(document).ready(function($) {
    // Functie om inline bewerkingsdata op te slaan
    function saveInlineEditData(postId, callback) {
        var seoKeyword = $('.inline-edit-seo-keyword[data-post-id="' + postId + '"]').val();
        var promptTemplate = $('.inline-edit-prompt-template[data-post-id="' + postId + '"]').val();
        var aiPrompt = $('.inline-edit-ai-prompt[data-post-id="' + postId + '"]').val();
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'save_inline_edit_data',
                post_id: postId,
                seo_keyword: seoKeyword,
                prompt_template: promptTemplate,
                ai_prompt: aiPrompt
            },
            success: function(response) {
                console.log('Data saved successfully');
                if (callback) callback();
            },
            error: function() {
                console.log('Error saving data');
            }
        });
    }

    // Functie om AI content te genereren
    function generateAIContent(postId, $button) {
        var aiPrompt = $('.inline-edit-ai-prompt[data-post-id="' + postId + '"]').val();
        var nonce = $('#ai_generate_content_nonce').val();
        // Disable de knop en verander de tekst
        $button.prop('disabled', true).text('Generating...');
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'generate_ai_content',
                post_id: postId,
                prompt: aiPrompt,
                _ajax_nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    console.log('AI content generated successfully');
                    location.reload(true); // Forceer een harde refresh van de pagina
                } else {
                    console.log('Error generating AI content: ' + response.data);
                    // Herstel de knopstatus bij fout
                    $button.prop('disabled', false).text('Save & Generate');
                }
            },
            error: function() {
                console.log('Error generating AI content');
                alert('Error generating AI content');
                // Herstel de knopstatus bij fout
                $button.prop('disabled', false).text('Save & Generate');
            }
        });
    }

    // Functie om zowel te saven als AI content te genereren
    function saveAndGenerate(postId) {
        var $button = $('.save-and-generate-button[data-post-id="' + postId + '"]');
        $button.prop('disabled', true).text('Saving & Generating...');
        saveInlineEditData(postId, function() {
            generateAIContent(postId, $button);
        });
    }

    // Event listener voor de save button
    $(document).on('click', '.save-button', function() {
        var postId = $(this).data('post-id');
        saveInlineEditData(postId);
    });

    // Event listener voor de generate AI content button
    $(document).on('click', '.generate-ai-content-button', function() {
        var postId = $(this).data('post-id');
        generateAIContent(postId, $(this));
    });

    // Event listener voor de save and generate button
    $(document).on('click', '.save-and-generate-button', function() {
        var postId = $(this).data('post-id');
        saveAndGenerate(postId);
    });
});