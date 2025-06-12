<div class="wrap certificate-verification">
    <h1><?php _e('Certificate Verification Settings', 'certificate-verification'); ?></h1>

    
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
        settings_fields('certificate_verification_settings');
        do_settings_sections('certificate_verification_settings');
        submit_button();
        ?>
    </form>

    <div class="settings-extra">
        
        <h2><?php _e('Certificate Template Settings', 'certificate-verification'); ?></h2>

        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="update_certificate_template">
            <?php wp_nonce_field('update_certificate_template'); ?>

            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row">
                        <label for="certificate_logo"><?php _e('Institution Logo', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="certificate_logo" name="certificate_logo" class="regular-text" value="<?php echo esc_attr(get_option('certificate_verification_logo')); ?>">
                        <button class="button upload-logo"><?php _e('Upload Logo', 'certificate-verification'); ?></button>
                        <p class="description"><?php _e('URL of your institution logo to display on certificates', 'certificate-verification'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="certificate_watermark"><?php _e('Watermark Text', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="certificate_watermark" name="certificate_watermark" class="regular-text" value="<?php echo esc_attr(get_option('certificate_verification_watermark')); ?>">
                        <p class="description"><?php _e('Text to display as watermark on certificates', 'certificate-verification'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="certificate_signature"><?php _e('Signature Image', 'certificate-verification'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="certificate_signature" name="certificate_signature" class="regular-text" value="<?php echo esc_attr(get_option('certificate_verification_signature')); ?>">
                        <button class="button upload-signature"><?php _e('Upload Signature', 'certificate-verification'); ?></button>
                        <p class="description"><?php _e('URL of signature image for certificates', 'certificate-verification'); ?></p>
                    </td>
                </tr>
                </tbody>
            </table>

            <p class="submit">
                <input type="submit" class="button button-primary" value="<?php _e('Save Template Settings', 'certificate-verification'); ?>">
            </p>
        </form>
    </div>
</div>

<style>
    .settings-extra {
        margin-top: 40px;
        padding: 20px;
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
</style>

<script>
    jQuery(document).ready(function($) {
        // Handle logo upload
        $('.upload-logo').on('click', function(e) {
            e.preventDefault();

            var button = $(this);
            var input = $('#certificate_logo');

            var frame = wp.media({
                title: 'Select Institution Logo',
                multiple: false,
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Use this logo'
                }
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                input.val(attachment.url);
            });

            frame.open();
        });

        // Handle signature upload
        $('.upload-signature').on('click', function(e) {
            e.preventDefault();

            var button = $(this);
            var input = $('#certificate_signature');

            var frame = wp.media({
                title: 'Select Signature Image',
                multiple: false,
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Use this signature'
                }
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                input.val(attachment.url);
            });

            frame.open();
        });
    });
</script>