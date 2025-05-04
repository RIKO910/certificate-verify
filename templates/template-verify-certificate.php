<?php
/*
Template Name: Verify Certificate
*/

get_header(); ?>

    <div class="container">
        <?php
        if (isset($_GET['id'])) {
            $certificate_id = sanitize_text_field($_GET['id']);
            $verification_code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';

            $db = Certificate_Verification_Database::get_instance();
            $certificate = $db->get_certificate($certificate_id, $verification_code);

            if ($certificate) {
                include CERT_VERIFICATION_PATH . 'templates/certificate-template.php';
            } else {
                include CERT_VERIFICATION_PATH . 'templates/verification-result.php';
            }
        } else {
            echo '<div class="alert alert-warning">Please provide a certificate ID</div>';
            echo do_shortcode('[certificate_verification]');
        }
        ?>
    </div>

<?php get_footer(); ?>