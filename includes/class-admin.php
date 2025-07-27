<?php
class Certificate_Verification_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {

        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_import_certificates', array($this, 'handle_csv_import'));
        add_action('admin_init', array($this, 'handle_certificate_actions'));
        add_action('admin_post_certificate_verification_add_certificate', array($this, 'process_add_certificate'));

    }

    public function add_admin_menu() {
        add_menu_page(
            __('Certificate Verification', 'certificate-verification'),
            __('Certificates', 'certificate-verification'),
            'manage_options',
            'certificate_verification',
            array($this, 'admin_dashboard'),
            'dashicons-awards',
            30
        );

        add_submenu_page(
            'certificate_verification',
            __('All Certificates', 'certificate-verification'),
            __('All Certificates', 'certificate-verification'),
            'manage_options',
            'certificate_verification',
            array($this, 'admin_dashboard')
        );

        add_submenu_page(
            'certificate_verification',
            __('Add New Certificate', 'certificate-verification'),
            __('Add New', 'certificate-verification'),
            'manage_options',
            'certificate_verification_add',
            array($this, 'add_certificate_page')
        );

        add_submenu_page(
            'certificate_verification',
            __('Import Certificates', 'certificate-verification'),
            __('Import', 'certificate-verification'),
            'manage_options',
            'certificate_verification_import',
            array($this, 'import_certificates_page')
        );

        add_submenu_page(
            'certificate_verification',
            __('Settings', 'certificate-verification'),
            __('Settings', 'certificate-verification'),
            'manage_options',
            'certificate_verification_settings',
            array($this, 'settings_page')
        );
    }

    public function register_settings() {
        register_setting('certificate_verification_settings', 'certificate_verification_options');

        add_settings_section(
            'general_settings',
            __('General Settings', 'certificate-verification'),
            array($this, 'general_settings_section_callback'),
            'certificate_verification_settings'
        );

        add_settings_field(
            'default_institution',
            __('Default Institution', 'certificate-verification'),
            array($this, 'default_institution_callback'),
            'certificate_verification_settings',
            'general_settings'
        );

        add_settings_field(
            'certificate_prefix',
            __('Certificate ID Prefix', 'certificate-verification'),
            array($this, 'certificate_prefix_callback'),
            'certificate_verification_settings',
            'general_settings'
        );
    }

    public function general_settings_section_callback() {
        echo '<p>' . __('Configure general settings for the certificate verification system.', 'certificate-verification') . '</p>';
    }

    public function default_institution_callback() {
        $options = get_option('certificate_verification_options');
        echo '<input type="text" id="default_institution" name="certificate_verification_options[default_institution]" value="' . esc_attr($options['default_institution'] ?? '') . '" class="regular-text">';
    }

    public function certificate_prefix_callback() {
        $options = get_option('certificate_verification_options');
        echo '<input type="text" id="certificate_prefix" name="certificate_verification_options[certificate_prefix]" value="' . esc_attr($options['certificate_prefix'] ?? 'CERT-') . '" class="regular-text">';
        echo '<p class="description">' . __('This will be prepended to all certificate IDs.', 'certificate-verification') . '</p>';
    }

    public function admin_dashboard() {
        // Include the WP_List_Table class if not already loaded
        if (!class_exists('WP_List_Table')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
        }

        // Include our custom list table class
        require_once CERT_VERIFICATION_PATH . 'includes/class-certificates-list-table.php';

        // Create an instance of our custom list table
        $certificates_table = new Certificates_List_Table();
        $certificates_table->prepare_items();

        // Get certificate counts
        $total_certificates = $this->get_certificate_count();
        $active_certificates = $this->get_certificate_count('active');
        $expired_certificates = $this->get_certificate_count('expired');

        // Include the dashboard template
        include CERT_VERIFICATION_PATH . 'templates/admin/dashboard.php';
    }

    private function get_certificate_count($status = 'all') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'certificate_verification';

        $query = "SELECT COUNT(*) FROM $table_name";

        switch ($status) {
            case 'active':
                $query .= " WHERE is_active = 1 AND (expiry_date IS NULL OR expiry_date >= CURDATE())";
                break;
            case 'expired':
                $query .= " WHERE (expiry_date IS NOT NULL AND expiry_date < CURDATE()) OR is_active = 0";
                break;
        }

        return $wpdb->get_var($query);
    }

    public function add_certificate_page() {
        $certificate = null;

        // Check if this is an edit request
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['certificate_id'])) {
            $db = Certificate_Verification_Database::get_instance();
            $certificate = $db->get_certificate(sanitize_text_field($_GET['certificate_id']));

            if (!$certificate) {
                wp_die(__('Certificate not found.', 'certificate-verification'));
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('add_certificate')) {
            $this->process_add_certificate();
        }

        include CERT_VERIFICATION_PATH . 'templates/admin/add-certificate.php';
    }

    public function process_add_certificate() {
        // Verify nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'add_certificate')) {
            wp_die(__('Security check failed.', 'certificate-verification'));
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'certificate-verification'));
        }

        // Prepare certificate data
        $data = array(
            'certificate_id' => sanitize_text_field($_POST['certificate_id']),
            'student_name' => sanitize_text_field($_POST['student_name']),
            'course_type' => sanitize_text_field($_POST['course_type']),
            'course_name' => sanitize_text_field($_POST['course_name']),
            'institution' => sanitize_text_field($_POST['institution']),
            'issue_date' => sanitize_text_field($_POST['issue_date']),
            'is_active' => 1
        );

        // Handle optional fields
        if (!empty($_POST['expiry_date'])) {
            $data['expiry_date'] = sanitize_text_field($_POST['expiry_date']);
        } else {
            $data['expiry_date'] = null;
        }

        if (!empty($_POST['additional_data'])) {
            $data['additional_data'] = maybe_serialize(json_decode(stripslashes($_POST['additional_data']), true));
        }

        $db = Certificate_Verification_Database::get_instance();

        // Check if this is an update
        if (!empty($_POST['original_certificate_id'])) {
            $original_id = sanitize_text_field($_POST['original_certificate_id']);
            $result = $db->update_certificate($original_id, $data);
        } else {
            // New certificate
            $options = get_option('certificate_verification_options');
            $data['verification_code'] = wp_generate_password(8, false);
            $result = $db->add_certificate($data);
        }

        // Handle result
        if ($result) {
            wp_redirect(admin_url('admin.php?page=certificate_verification&updated=1'));
            exit;
        } else {
            $error = __('Error saving certificate.', 'certificate-verification');
            if (!empty($_POST['original_certificate_id'])) {
                wp_redirect(admin_url('admin.php?page=certificate_verification_add&action=edit&certificate_id=' . urlencode($_POST['original_certificate_id']) . '&error=' . urlencode($error)));
            } else {
                wp_redirect(admin_url('admin.php?page=certificate_verification_add&error=' . urlencode($error)));
            }
            exit;
        }
    }

    public function import_certificates_page() {
        include CERT_VERIFICATION_PATH . 'templates/admin/import-certificates.php';
    }

    public function handle_csv_import() {
        check_admin_referer('import_certificates');

        if (empty($_FILES['certificate_csv']['tmp_name'])) {
            wp_redirect(add_query_arg('import', 'error', wp_get_referer()));
            exit;
        }

        $file = $_FILES['certificate_csv']['tmp_name'];
        $handle = fopen($file, 'r');

        if ($handle === false) {
            wp_redirect(add_query_arg('import', 'error', wp_get_referer()));
            exit;
        }

        $db = Certificate_Verification_Database::get_instance();
        $success_count = 0;
        $error_count = 0;

        // Skip header row
        fgetcsv($handle);

        while (($data = fgetcsv($handle)) !== false) {
            $certificate_data = array(
                'certificate_id' => sanitize_text_field($data[0]),
                'student_name' => sanitize_text_field($data[1]),
                'course_type' => sanitize_text_field($data[2]),
                'course_name' => sanitize_text_field($data[3]),
                'institution' => sanitize_text_field($data[4]),
                'issue_date' => sanitize_text_field($data[5]),
                'expiry_date' => !empty($data[6]) ? sanitize_text_field($data[6]) : null,
                'additional_data' => isset($data[7]) ? maybe_serialize($data[7]) : ''
            );

            $result = $db->add_certificate($certificate_data);

            if ($result) {
                $success_count++;
            } else {
                $error_count++;
            }
        }

        fclose($handle);

        wp_redirect(add_query_arg(array(
            'import' => 'complete',
            'success' => $success_count,
            'error' => $error_count
        ), wp_get_referer()));
        exit;
    }

    public function handle_certificate_actions() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['certificate_id'])) {
            $this->delete_certificate();
        }
    }

    private function delete_certificate() {
        if (!isset($_GET['certificate_id']) || !isset($_GET['_wpnonce'])) {
            wp_die(__('Invalid request.', 'certificate-verification'));
        }

        $certificate_id = sanitize_text_field($_GET['certificate_id']);
        $nonce = sanitize_text_field($_GET['_wpnonce']);

        // Verify nonce
        if (!wp_verify_nonce($nonce, 'delete_certificate_' . $certificate_id)) {
            wp_die(__('Security check failed.', 'certificate-verification'));
        }

        $db = Certificate_Verification_Database::get_instance();
        $result = $db->delete_certificate($certificate_id);

        if ($result) {
            wp_redirect(admin_url('admin.php?page=certificate_verification&deleted=1'));
            exit;
        } else {
            wp_redirect(admin_url('admin.php?page=certificate_verification&deleted=0'));
            exit;
        }
    }

    public function settings_page() {
        include CERT_VERIFICATION_PATH . 'templates/admin/settings.php';
    }
}