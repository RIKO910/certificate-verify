<div class="certificate-template">
    <div class="certificate-seal"><?php _e('Seal', 'certificate-verification'); ?></div>

    <div class="certificate-header">
        <h1><?php _e('Certificate of Completion', 'certificate-verification'); ?></h1>
        <div class="institution"><?php echo esc_html($certificate->institution); ?></div>
    </div>

    <div class="certificate-body">
        <div class="award-text">
            <?php _e('This is to certify that', 'certificate-verification'); ?>
        </div>

        <div class="recipient-name">
            <?php echo esc_html($certificate->student_name); ?>
        </div>

        <div class="course-name">
            <?php printf(__('has successfully completed the %s course in %s', 'certificate-verification'),
                esc_html($certificate->course_name),
                esc_html($certificate->course_type === 'diploma' ? __('Diploma', 'certificate-verification') : __('University', 'certificate-verification'))); ?>
        </div>
    </div>

    <div class="certificate-footer">
        <div class="signature">
            <div class="signature-line"></div>
            <div class="signature-name"><?php _e('Director of Studies', 'certificate-verification'); ?></div>
        </div>

        <div class="signature">
            <div><?php echo date_i18n(get_option('date_format'), strtotime($certificate->issue_date)); ?></div>
            <div class="signature-line"></div>
            <div class="signature-name"><?php _e('Date', 'certificate-verification'); ?></div>
        </div>
    </div>

    <div class="certificate-id">
        <?php printf(__('Certificate ID: %s', 'certificate-verification'), esc_html($certificate->certificate_id)); ?>
    </div>
</div>

<div class="certificate-actions">
    <button class="button print-certificate">
        <i class="dashicons dashicons-printer"></i> <?php _e('Print Certificate', 'certificate-verification'); ?>
    </button>

    <button class="button download-pdf" data-certificate-id="<?php echo esc_attr($certificate->certificate_id); ?>">
        <i class="dashicons dashicons-download"></i> <?php _e('Download PDF', 'certificate-verification'); ?>
    </button>

    <a href="<?php echo home_url('/verify-certificate'); ?>" class="button">
        <?php _e('Verify Another Certificate', 'certificate-verification'); ?>
    </a>
</div>

<style>
    .certificate-actions {
        max-width: 800px;
        margin: 0 auto 30px;
        text-align: center;
    }


    .certificate-actions .button {
        margin: 0 10px 10px;
        display: inline-flex;
        align-items: center;
    }


    .certificate-actions .dashicons {
        margin-right: 5px;
    }

    @media print {
        .certificate-actions {
            display: none;
        }
    }
</style>