<div class="verification-result <?php echo is_string($certificate) ? $certificate : 'invalid'; ?>">
    <div class="result-icon">
        <?php if (is_string($certificate) && $certificate === 'expired') : ?>
            <i class="dashicons dashicons-warning"></i>
        <?php else : ?>
            <i class="dashicons dashicons-no"></i>
        <?php endif; ?>
    </div>

    <h3>
        <?php if (is_string($certificate)) : ?>
            <?php if ($certificate === 'expired') : ?>
                <?php _e('This certificate has expired.', 'certificate-verification'); ?>
            <?php elseif ($certificate === 'inactive') : ?>
                <?php _e('This certificate is no longer valid.', 'certificate-verification'); ?>
            <?php endif; ?>
        <?php else : ?>
            <?php _e('Certificate not found. Please check the ID and verification code.', 'certificate-verification'); ?>
        <?php endif; ?>
    </h3>

    <p><?php _e('If you believe this is an error, please contact the issuing institution.', 'certificate-verification'); ?></p>

    <a href="<?php echo home_url('/verify-certificate'); ?>" class="button">
        <?php _e('Verify Another Certificate', 'certificate-verification'); ?>
    </a>
</div>