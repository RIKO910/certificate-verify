<?php
class Certificate_Verification_Shortcodes {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('certificate_verification', array($this, 'verification_form'));
        add_shortcode('certificate_display', array($this, 'certificate_display'));
    }

    public function verification_form($atts) {
        wp_enqueue_style('cert-verification-frontend');
        wp_enqueue_script('cert-verification-frontend');

        ob_start();
        include CERT_VERIFICATION_PATH . 'templates/verification-form.php';
        return ob_get_clean();
    }

    public function certificate_display($atts) {
        wp_enqueue_style('cert-verification-frontend');

        $atts = shortcode_atts(array(
            'id' => '',
            'code' => ''
        ), $atts);

        if (empty($atts['id'])) {
            return '<div class="certificate-error">Certificate ID is required</div>';
        }

        $verification = Certificate_Verification::get_instance();
        $certificate = $verification->verify_certificate($atts['id'], $atts['code']);

        ob_start();
        if ($certificate) {
            include CERT_VERIFICATION_PATH . 'templates/certificate-template.php';
        } else {
            include CERT_VERIFICATION_PATH . 'templates/verification-result.php';
        }
        return ob_get_clean();
    }
}