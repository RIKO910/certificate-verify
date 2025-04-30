<div class="certificate-verification-form">
    <h2><?php _e('Verify Certificate', 'certificate-verification'); ?></h2>
    <form id="certificate_verification" method="post">
        <div class="form-group">
            <label for="certificate_id"><?php _e('Certificate ID', 'certificate-verification'); ?></label>
            <input type="text" id="certificate_id" name="certificate_id" required placeholder="DIP-1234">
        </div>

        <div class="form-group">
            <label for="verification_code"><?php _e('Verification Code', 'certificate-verification'); ?></label>
            <input type="text" id="verification_code" name="verification_code" placeholder="<?php _e('Optional', 'certificate-verification'); ?>">
            <p class="description"><?php _e('For enhanced security, enter the verification code if you have one.', 'certificate-verification'); ?></p>
        </div>

        <button type="submit"><?php _e('Verify Certificate', 'certificate-verification'); ?></button>
    </form>
</div>

<script type="text/javascript">
    var certificate_verification = {
        ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('certificate_verification_nonce'); ?>',
        certificate_url: '<?php echo home_url('/certificate'); ?>'
    };
</script>