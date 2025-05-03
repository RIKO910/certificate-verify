<div class="wrap certificate-verification">
    <h1><?php echo isset($certificate) ? __('Edit Certificate', 'certificate-verification') : __('Add New Certificate', 'certificate-verification'); ?></h1>

    <?php
    if (isset($_GET['error'])) {
        echo '<div class="notice notice-error"><p>' . esc_html($_GET['error']) . '</p></div>';
    }
    settings_errors();
    ?>

    <div class="certificate-form">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="certificate_verification_add_certificate">
            <?php wp_nonce_field('add_certificate'); ?>

            <?php if (isset($certificate)) : ?>
                <input type="hidden" name="original_certificate_id" value="<?php echo esc_attr($certificate->certificate_id); ?>">
            <?php endif; ?>

            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row">
                        <label for="certificate_id"><?php _e('Certificate ID', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="certificate_id" name="certificate_id" class="regular-text" required
                               value="<?php echo isset($certificate) ? esc_attr($certificate->certificate_id) : ''; ?>">
                        <p class="description"><?php _e('Unique identifier for this certificate', 'certificate-verification'); ?></p>
                    </td>
                </tr>

                <!-- Repeat for other fields (student_name, course_type, etc.) -->
                <tr>
                    <th scope="row">
                        <label for="student_name"><?php _e('Student Name', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="student_name" name="student_name" class="regular-text" required
                               value="<?php echo isset($certificate) ? esc_attr($certificate->student_name) : ''; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="course_type"><?php _e('Course Type', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <select id="course_type" name="course_type" required>
                            <option value=""><?php _e('Select Course Type', 'certificate-verification'); ?></option>
                            <option value="diploma" <?php selected(isset($certificate) && $certificate->course_type === 'diploma'); ?>>
                                <?php _e('Diploma Course', 'certificate-verification'); ?>
                            </option>
                            <option value="university" <?php selected(isset($certificate) && $certificate->course_type === 'university'); ?>>
                                <?php _e('University Course', 'certificate-verification'); ?>
                            </option>
                            <!-- Add other course types with selected() conditions -->
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="course_name"><?php _e('Course Name', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input value="<?php echo isset($certificate) ? esc_attr($certificate->course_name) : ''; ?>" type="text" id="course_name" name="course_name" class="regular-text" required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="institution"><?php _e('Institution', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input value="<?php echo isset($certificate) ? esc_attr($certificate->institution) : ''; ?>" type="text" id="institution" name="institution" class="regular-text" required>
                    </td>
                </tr>

                <!-- Continue with other fields (course_name, institution, etc.) -->

                <tr>
                    <th scope="row">
                        <label for="issue_date"><?php _e('Issue Date', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="date" id="issue_date" name="issue_date" class="certificate-datepicker" required
                               value="<?php echo isset($certificate) ? esc_attr($certificate->issue_date) : ''; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="has_expiry"><?php _e('Has Expiry Date?', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="has_expiry" name="has_expiry" value="1"
                            <?php checked(isset($certificate) && !empty($certificate->expiry_date)); ?>>
                        <label for="has_expiry"><?php _e('This certificate expires', 'certificate-verification'); ?></label>
                    </td>
                </tr>

                <tr id="expiry_date_field">
                    <th scope="row">
                        <label for="expiry_date"><?php _e('Expiry Date', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="date" id="expiry_date" name="expiry_date" class="certificate-datepicker"
                               value="<?php echo isset($certificate) && !empty($certificate->expiry_date) ? esc_attr($certificate->expiry_date) : ''; ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="additional_data"><?php _e('Additional Data', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                            <textarea id="additional_data" name="additional_data" rows="5" class="large-text"><?php
                                if (isset($certificate) && !empty($certificate->additional_data)) {
                                    $additional_data = maybe_unserialize($certificate->additional_data);
                                    echo esc_textarea(json_encode($additional_data, JSON_PRETTY_PRINT));
                                }
                                ?></textarea>
                        <p class="description"><?php _e('Any additional information in JSON format', 'certificate-verification'); ?></p>
                    </td>
                </tr>
                </tbody>
            </table>

            <p class="submit">
                <input type="submit" class="button button-primary" value="<?php _e('Save Certificate', 'certificate-verification'); ?>">
                <a href="<?php echo admin_url('admin.php?page=certificate_verification'); ?>" class="button"><?php _e('Cancel', 'certificate-verification'); ?></a>
            </p>
        </form>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        // Toggle expiry date field
        $('#has_expiry').on('change', function() {
            if ($(this).is(':checked')) {
                $('#expiry_date_field').show();
                $('#expiry_date').prop('required', true);
            } else {
                $('#expiry_date_field').hide();
                $('#expiry_date').prop('required', false);
            }
        }).trigger('change');

        // Set today's date as default for issue date
        $('#issue_date').val(new Date().toISOString().split('T')[0]);

        // Auto-generate certificate ID if empty
        $('#certificate_id').on('focus', function() {
            if ($(this).val() === '') {
                var prefix = '<?php echo isset($options['certificate_prefix']) ? esc_js($options['certificate_prefix']) : "CERT-"; ?>';
                var randomNum = Math.floor(1000 + Math.random() * 9000);
                $(this).val(prefix + randomNum);
            }
        });
    });
</script>