<div class="wrap certificate-verification">
    <h1><?php _e('Import Certificates', 'certificate-verification'); ?></h1>

    <?php if (isset($_GET['import']) && $_GET['import'] === 'complete') : ?>
        <div class="notice notice-success">
            <p><?php printf(__('Import completed successfully! %d certificates imported, %d failed.', 'certificate-verification'),
                    intval($_GET['success']),
                    intval($_GET['error'])); ?></p>
        </div>
    <?php elseif (isset($_GET['import']) && $_GET['import'] === 'error') : ?>
        <div class="notice notice-error">
            <p><?php _e('Error importing certificates. Please check the file format and try again.', 'certificate-verification'); ?></p>
        </div>
    <?php endif; ?>

    <div class="csv-import-section">
        <h2><?php _e('Import from CSV', 'certificate-verification'); ?></h2>
        <p class="description">
            <?php _e('Upload a CSV file containing certificate data. The file should have the following columns:', 'certificate-verification'); ?>
        </p>

        <ul>
            <li><?php _e('Certificate ID', 'certificate-verification'); ?></li>
            <li><?php _e('Student Name', 'certificate-verification'); ?></li>
            <li><?php _e('Course Type (diploma, university, individual, diploma_main, university_main)', 'certificate-verification'); ?></li>
            <li><?php _e('Course Name', 'certificate-verification'); ?></li>
            <li><?php _e('Institution', 'certificate-verification'); ?></li>
            <li><?php _e('Issue Date (YYYY-MM-DD)', 'certificate-verification'); ?></li>
            <li><?php _e('Expiry Date (YYYY-MM-DD) - optional', 'certificate-verification'); ?></li>
            <li><?php _e('Additional Data (JSON) - optional', 'certificate-verification'); ?></li>
        </ul>

        <div id="csv_preview"></div>

        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="import_certificates">
            <?php wp_nonce_field('import_certificates'); ?>

            <p>
                <input type="file" id="certificate_csv" name="certificate_csv" accept=".csv" required>
            </p>

            <p>
                <input type="submit" class="button button-primary" value="<?php _e('Import Certificates', 'certificate-verification'); ?>">
            </p>
        </form>

        <a href="<?php echo CERT_VERIFICATION_URL . 'assets/sample-certificates.csv'; ?>" class="download-sample">
            <?php _e('Download Sample CSV', 'certificate-verification'); ?>
        </a>
    </div>
</div>