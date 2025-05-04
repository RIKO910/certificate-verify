<?php
class Certificate_Verification_Database {

    private static $instance = null;
    private $table_name;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'certificate_verification';

        add_action('plugins_loaded', array($this, 'check_for_updates'));
    }

    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'certificate_verification';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            certificate_id varchar(20) NOT NULL,
            student_name varchar(100) NOT NULL,
            course_type varchar(50) NOT NULL,
            course_name varchar(100) NOT NULL,
            institution varchar(100) NOT NULL,
            issue_date date NOT NULL,
            expiry_date date DEFAULT NULL,
            verification_code varchar(32) NOT NULL,
            additional_data text DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY certificate_id (certificate_id),
            KEY verification_code (verification_code)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Add sample data if table is empty
        if ($wpdb->get_var("SELECT COUNT(*) FROM $table_name") == 0) {
            self::add_sample_data();
        }
    }

    private static function add_sample_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'certificate_verification';

        $sample_data = array(
            array(
                'certificate_id' => 'DIP-' . wp_rand(1000, 9999),
                'student_name' => 'John Doe',
                'course_type' => 'diploma',
                'course_name' => 'Advanced Web Development',
                'institution' => 'Tech University',
                'issue_date' => date('Y-m-d', strtotime('-1 month')),
                'expiry_date' => date('Y-m-d', strtotime('+2 years')),
                'verification_code' => wp_generate_password(8, false),
                'is_active' => 1
            ),
            array(
                'certificate_id' => 'UNI-' . wp_rand(1000, 9999),
                'student_name' => 'Jane Smith',
                'course_type' => 'university',
                'course_name' => 'Computer Science',
                'institution' => 'Global University',
                'issue_date' => date('Y-m-d', strtotime('-6 months')),
                'expiry_date' => date('Y-m-d', strtotime('+4 years')),
                'verification_code' => wp_generate_password(8, false),
                'is_active' => 1
            )
        );

        foreach ($sample_data as $data) {
            $wpdb->insert($table_name, $data);
        }
    }

    public static function deactivate() {
        // Optionally clean up on deactivation
        // global $wpdb;
        // $table_name = $wpdb->prefix . 'certificate_verification';
        // $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    public function check_for_updates() {
        // Future version updates can be handled here
    }

    public function get_certificate($certificate_id) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE certificate_id = %s", $certificate_id)
        );
    }

    public function add_certificate($data) {
        global $wpdb;

        $defaults = array(
            'verification_code' => wp_generate_password(8, false),
            'created_at' => current_time('mysql'),
            'is_active' => 1
        );

        $data = wp_parse_args($data, $defaults);

        return $wpdb->insert($this->table_name, $data);
    }

    public function update_certificate($original_id, $data) {
        global $wpdb;

        return $wpdb->update(
            $this->table_name,
            $data,
            array('certificate_id' => $original_id),
            null,
            array('%s')
        );
    }

    public function delete_certificate($certificate_id) {
        global $wpdb;

        return $wpdb->delete(
            $this->table_name,
            array('certificate_id' => $certificate_id),
            array('%s')
        );
    }

    public function get_all_certificates($per_page = 20, $page_number = 1) {
        global $wpdb;

        $offset = ($page_number - 1) * $per_page;

        $sql = "SELECT * FROM {$this->table_name}";

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            $sql .= ' ORDER BY created_at DESC';
        }

        $sql .= " LIMIT %d OFFSET %d";

        return $wpdb->get_results(
            $wpdb->prepare($sql, $per_page, $offset),
            ARRAY_A
        );
    }

    public function count_certificates() {
        global $wpdb;

        return $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
    }
}