<div class="wrap certificate-verification">
    <h1><?php _e('Add New Certificate', 'certificate-verification'); ?></h1>

    <?php settings_errors(); ?>

    <div class="certificate-form">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="add_certificate">
            <?php wp_nonce_field('add_certificate'); ?>

            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row">
                        <label for="certificate_id"><?php _e('Certificate ID', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="certificate_id" name="certificate_id" class="regular-text" required>
                        <p class="description"><?php _e('Unique identifier for this certificate', 'certificate-verification'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="student_name"><?php _e('Student Name', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="student_name" name="student_name" class="regular-text" required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="course_type"><?php _e('Course Type', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <select id="course_type" name="course_type" required>
                            <option value=""><?php _e('Select Course Type', 'certificate-verification'); ?></option>
                            <option value="diploma"><?php _e('Diploma Course', 'certificate-verification'); ?></option>
                            <option value="university"><?php _e('University Course', 'certificate-verification'); ?></option>
                            <option value="individual"><?php _e('Individual Course', 'certificate-verification'); ?></option>
                            <option value="diploma_main"><?php _e('Diploma Main Course', 'certificate-verification'); ?></option>
                            <option value="university_main"><?php _e('University Main Course', 'certificate-verification'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="course_name"><?php _e('Course Name', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="course_name" name="course_name" class="regular-text" required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="institution"><?php _e('Institution', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="institution" name="institution" class="regular-text" required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="issue_date"><?php _e('Issue Date', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="date" id="issue_date" name="issue_date" class="certificate-datepicker" required>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="has_expiry"><?php _e('Has Expiry Date?', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="has_expiry" name="has_expiry" value="1">
                        <label for="has_expiry"><?php _e('This certificate expires', 'certificate-verification'); ?></label>
                    </td>
                </tr>

                <tr id="expiry_date_field">
                    <th scope="row">
                        <label for="expiry_date"><?php _e('Expiry Date', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="date" id="expiry_date" name="expiry_date" class="certificate-datepicker">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="additional_data"><?php _e('Additional Data', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <textarea id="additional_data" name="additional_data" rows="5" class="large-text"></textarea>
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