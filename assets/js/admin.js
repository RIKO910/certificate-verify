jQuery(document).ready(function($) {
    // Datepicker for issue and expiry dates
    $('.certificate-datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });

    // Generate certificate ID if empty
    $('#certificate_id').on('focus', function() {
        if ($(this).val() === '') {
            var prefix = certificate_verification_admin.certificate_prefix || 'CERT-';
            var randomNum = Math.floor(1000 + Math.random() * 9000);
            $(this).val(prefix + randomNum);
        }
    });

    // Toggle expiry date field
    $('#has_expiry').on('change', function() {
        if ($(this).is(':checked')) {
            $('#expiry_date_field').show();
        } else {
            $('#expiry_date_field').hide();
        }
    }).trigger('change');

    // Bulk actions
    $('#doaction, #doaction2').on('click', function(e) {
        var action = $(this).siblings('select').val();

        if (action === 'export_csv') {
            e.preventDefault();
            exportCertificatesToCSV();
        }
    });

    // Export certificates to CSV
    function exportCertificatesToCSV() {
        var certificateIds = [];
        $('input[name="certificate_ids[]"]:checked').each(function() {
            certificateIds.push($(this).val());
        });

        if (certificateIds.length === 0) {
            alert('Please select at least one certificate to export.');
            return;
        }

        var form = $('<form>', {
            method: 'post',
            action: certificate_verification_admin.ajax_url
        });

        form.append($('<input>', {
            type: 'hidden',
            name: 'action',
            value: 'export_certificates_csv'
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'security',
            value: certificate_verification_admin.nonce
        }));

        $.each(certificateIds, function(index, id) {
            form.append($('<input>', {
                type: 'hidden',
                name: 'certificate_ids[]',
                value: id
            }));
        });

        $('body').append(form);
        form.submit();
        form.remove();
    }

    // CSV import preview
    $('#certificate_csv').on('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;

        var reader = new FileReader();
        reader.onload = function(e) {
            var contents = e.target.result;
            var lines = contents.split('\n');
            var previewHtml = '<h3>CSV Preview (first 5 rows)</h3>';
            previewHtml += '<table class="wp-list-table widefat fixed striped">';

            for (var i = 0; i < Math.min(5, lines.length); i++) {
                previewHtml += '<tr>';
                var cells = lines[i].split(',');
                for (var j = 0; j < cells.length; j++) {
                    previewHtml += '<td>' + cells[j] + '</td>';
                }
                previewHtml += '</tr>';
            }

            previewHtml += '</table>';
            $('#csv_preview').html(previewHtml);
        };
        reader.readAsText(file);
    });
});