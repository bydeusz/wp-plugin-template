jQuery(document).ready(function($) {
    $('#generate_ai_content').on('click', function() {
        const prompt = $('#ai_prompt').val();
        const postId = $('#post_ID').val();
        $(this).prop('disabled', true);
        $(this).html('Generating... <span class="ai-loading-spinner"></span>');
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'generate_ai_content',
                prompt: prompt,
                post_id: postId,
                _ajax_nonce: $('#ai_generate_content_nonce').val()
            },
            success: function(response) {
                $('#generate_ai_content').prop('disabled', false).text('Generate AI Content');
                if (response.success) {
                    $('#ai_generated_content').html('<p>Content generated successfully! The content has been replaced in the post.</p>');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    $('#ai_generated_content').html('<p>Error: ' + response.data + '</p>');
                }
            },
            error: function() {
                $('#generate_ai_content').prop('disabled', false).text('Generate AI Content');
                $('#ai_generated_content').html('<p>Error: An error occurred while generating content.</p>');
            }
        });
    });
});