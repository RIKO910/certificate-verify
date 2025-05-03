<div class="wrap certificate-verification">
    <h1 class="wp-heading-inline"><?php _e('Certificate Verification System', 'certificate-verification'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=certificate_verification_add'); ?>" class="page-title-action"><?php _e('Add New', 'certificate-verification'); ?></a>
    <a href="<?php echo admin_url('admin.php?page=certificate_verification_import'); ?>" class="page-title-action"><?php _e('Import', 'certificate-verification'); ?></a>

    <hr class="wp-header-end">

    <?php
    if (isset($_GET['deleted'])) {
        if ($_GET['deleted'] === '1') {
            echo '<div class="notice notice-success"><p>' . __('Certificate deleted successfully.', 'certificate-verification') . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . __('Error deleting certificate.', 'certificate-verification') . '</p></div>';
        }
    }
    if (isset($_GET['added'])) {
        if ($_GET['added'] === '1') {
            echo '<div class="notice notice-success"><p>' . __('Certificate added successfully.', 'certificate-verification') . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . __('Error adding certificate.', 'certificate-verification') . '</p></div>';
        }
    }
    if (isset($_GET['updated'])) {
        if ($_GET['updated'] === '1') {
            echo '<div class="notice notice-success"><p>' . __('Certificate updated successfully.', 'certificate-verification') . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . __('Error updating certificate.', 'certificate-verification') . '</p></div>';
        }
    }
    settings_errors();
    ?>

    <div class="certificate-stats">
        <div class="stat-card">
            <h3><?php _e('Total Certificates', 'certificate-verification'); ?></h3>
            <p><?php echo number_format($total_certificates); ?></p>
        </div>
        <div class="stat-card">
            <h3><?php _e('Active Certificates', 'certificate-verification'); ?></h3>
            <p><?php echo number_format($active_certificates); ?></p>
        </div>
        <div class="stat-card">
            <h3><?php _e('Expired Certificates', 'certificate-verification'); ?></h3>
            <p><?php echo number_format($expired_certificates); ?></p>
        </div>
    </div>

    <form method="post">
        <input type="hidden" name="page" value="certificate_verification">
        <?php $certificates_table->search_box(__('Search Certificates', 'certificate-verification'), 'search_id'); ?>
    </form>

    <form id="certificates-filter" method="post">
        <input type="hidden" name="page" value="certificate_verification">
        <?php
        $certificates_table->display();
        ?>
    </form>
</div>

<style>
    .certificate-stats {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        flex: 1;
        background: #fff;
        padding: 20px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .stat-card h3 {
        margin-top: 0;
        font-size: 16px;
        color: #555;
    }

    .stat-card p {
        font-size: 28px;
        font-weight: bold;
        margin: 10px 0 0;
        color: #2c3e50;
    }

    @media (max-width: 782px) {
        .certificate-stats {
            flex-direction: column;
        }
    }
</style>