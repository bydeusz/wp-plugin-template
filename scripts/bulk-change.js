jQuery(document).ready(function($) {
    $('#bulk-save-button').on('click', function() {
        var keyword = $('#keyword').val();
        var prompt = $('#prompt').val();

        // Send AJAX request to update all keywords if not empty
        if (keyword.trim() !== '') {
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'update_all_keywords',
                    keyword: keyword
                },
                success: function(response) {
                    if (response.success) {
                        // Send AJAX request to update all prompts if not empty
                        if (prompt.trim() !== '') {
                            $.ajax({
                                url: ajaxurl,
                                method: 'POST',
                                data: {
                                    action: 'update_all_prompts',
                                    prompt: prompt
                                },
                                success: function(response) {
                                    if (response.success) {
                                        location.reload();
                                    } else {
                                        alert('Error: ' + response.data);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.log('AJAX error:', status, error);
                                    alert('An error occurred while updating prompts.');
                                }
                            });
                        } else {
                            location.reload();
                        }
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error);
                    alert('An error occurred while updating keywords.');
                }
            });
        } else if (prompt.trim() !== '') {
            // Send AJAX request to update all prompts if keyword is empty but prompt is not
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'update_all_prompts',
                    prompt: prompt
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error);
                    alert('An error occurred while updating prompts.');
                }
            });
        }
    });

    $('#bulk-generate-button').on('click', function() {
        // Show the progress bar
        $('#progress-container').show();
        $('#progress-bar').css('width', '0%');
        $('#progress-text').text('0 / 0 shortcodes processed');

        // Disable the generate button and change its text
        $(this).prop('disabled', true).text('Generating...');

        // Start the bulk generation process
        bulkGenerateShortcodes(0);
    });

    function bulkGenerateShortcodes(step) {
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'bulk_generate_all_shortcodes',
                step: step
            },
            success: function(response) {
                if (response.success) {
                    var processed = response.data.processed;
                    var total = response.data.total;
                    updateProgressBar(processed, total);

                    if (processed < total) {
                        bulkGenerateShortcodes(processed);
                    } else {
                        location.reload();
                    }
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', status, error);
                alert('An error occurred while generating shortcodes.');
            }
        });
    }

    // Function to update the progress bar
    function updateProgressBar(processed, total) {
        var percentage = (processed / total) * 100;
        $('#progress-bar').css('width', percentage + '%');
        $('#progress-text').text(processed + ' / ' + total + ' shortcodes processed');
    }
});

function toggleAccordion() {
    const content = document.getElementById('accordion-content');
    const toggleButton = document.getElementById('accordion-toggle');
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        toggleButton.innerHTML = '&#9650;'; // Change to up arrow
    } else {
        content.style.display = 'none';
        toggleButton.innerHTML = '&#9660;'; // Change to down arrow
    }
}