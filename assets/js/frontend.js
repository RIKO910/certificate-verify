jQuery(document).ready(function($) {
    // Handle verification form submission
    $('.certificate-verification-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var button = form.find('button[type="submit"]');
        var originalText = button.text();
        var certificateId = form.find('#certificate_id').val();
        var verificationCode = form.find('#verification_code').val();

        // Show loading state
        button.prop('disabled', true).text('Verifying...');

        // AJAX request
        $.ajax({
            url: certificate_verification.ajax_url,
            type: 'POST',
            data: {
                action: 'verify_certificate',
                security: certificate_verification.nonce,
                certificate_id: certificateId,
                verification_code: verificationCode
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.status === 'valid') {
                        // Redirect to certificate display page
                        window.location.href = certificate_verification.certificate_url + '?id=' + encodeURIComponent(certificateId) + '&code=' + encodeURIComponent(verificationCode);
                    } else {
                        // Show error message
                        var resultHtml = '<div class="verification-result ' + response.data.status + '">';
                        resultHtml += '<div class="result-icon">';

                        if (response.data.status === 'expired') {
                            resultHtml += '<i class="dashicons dashicons-warning"></i>';
                        } else {
                            resultHtml += '<i class="dashicons dashicons-no"></i>';
                        }

                        resultHtml += '</div>';
                        resultHtml += '<h3>' + response.data.message + '</h3>';
                        resultHtml += '</div>';

                        form.after(resultHtml);
                        $('html, body').animate({
                            scrollTop: form.next().offset().top - 100
                        }, 500);
                    }
                } else {
                    // Show error message
                    var resultHtml = '<div class="verification-result invalid">';
                    resultHtml += '<div class="result-icon"><i class="dashicons dashicons-no"></i></div>';
                    resultHtml += '<h3>' + response.data.message + '</h3>';
                    resultHtml += '</div>';

                    form.after(resultHtml);
                    $('html, body').animate({
                        scrollTop: form.next().offset().top - 100
                    }, 500);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                button.prop('disabled', false).text(originalText);
            }
        });
    });

    // Print certificate button
    $(document).on('click', '.print-certificate', function() {
        window.print();
    });

    // Download PDF button
    $(document).on('click', '.download-pdf', function() {
        var certificateId = $(this).data('certificate-id');
        var button = $(this);
        var originalText = button.text();

        button.prop('disabled', true).text('Generating PDF...');

        $.ajax({
            url: certificate_verification.ajax_url,
            type: 'POST',
            data: {
                action: 'generate_certificate_pdf',
                security: certificate_verification.nonce,
                certificate_id: certificateId
            },
            success: function(response) {
                if (response.success) {
                    // Create a temporary link to download the PDF
                    var link = document.createElement('a');
                    link.href = response.data.pdf_url;
                    link.download = 'certificate_' + certificateId + '.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert('An error occurred while generating the PDF.');
            },
            complete: function() {
                button.prop('disabled', false).text(originalText);
            }
        });
    });
});