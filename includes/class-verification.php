<?php
class Certificate_Verification {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_verify_certificate', array($this, 'ajax_verify_certificate'));
        add_action('wp_ajax_nopriv_verify_certificate', array($this, 'ajax_verify_certificate'));
    }

    public function verify_certificate($certificate_id, $verification_code = '') {
        $db = Certificate_Verification_Database::get_instance();
        $certificate = $db->get_certificate($certificate_id, $verification_code);

        if (!$certificate) {
            return false;
        }

        // Check if certificate is expired
        if ($certificate->expiry_date && strtotime($certificate->expiry_date) < time()) {
            return 'expired';
        }

        // Check if certificate is active
        if (!$certificate->is_active) {
            return 'inactive';
        }

        return $certificate;
    }

    public function ajax_verify_certificate() {
        check_ajax_referer('certificate_verification_nonce', 'security');

        $certificate_id = sanitize_text_field($_POST['certificate_id']);
        $verification_code = isset($_POST['verification_code']) ? sanitize_text_field($_POST['verification_code']) : '';

        $result = $this->verify_certificate($certificate_id, $verification_code);

        if (is_object($result)) {
            wp_send_json_success(array(
                'status' => 'valid',
                'certificate' => $result
            ));
        } elseif ($result === 'expired') {
            wp_send_json_success(array(
                'status' => 'expired',
                'message' => __('This certificate has expired.', 'certificate-verification')
            ));
        } elseif ($result === 'inactive') {
            wp_send_json_success(array(
                'status' => 'inactive',
                'message' => __('This certificate is no longer valid.', 'certificate-verification')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Certificate not found. Please check the ID and verification code.', 'certificate-verification')
            ));
        }
    }

    public function generate_certificate_pdf($certificate_id) {
        // This would be implemented with a PDF generation library like TCPDF or Dompdf
        // For now, we'll just return a placeholder
        return 'PDF generation would happen here for certificate ' . $certificate_id;
    }
}