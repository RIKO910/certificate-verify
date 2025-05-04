<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Certificates_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct(array(
            'singular' => 'certificate',
            'plural' => 'certificates',
            'ajax' => false
        ));
    }

    public function get_columns() {
        return array(
            'cb' => '<input type="checkbox">',
            'certificate_id' => __('Certificate ID', 'certificate-verification'),
            'student_name' => __('Student Name', 'certificate-verification'),
            'course_type' => __('Course Type', 'certificate-verification'),
            'course_name' => __('Course Name', 'certificate-verification'),
            'institution' => __('Institution', 'certificate-verification'),
            'issue_date' => __('Issue Date', 'certificate-verification'),
            'status' => __('Status', 'certificate-verification'),
            'actions' => __('Actions', 'certificate-verification')
        );
    }

    protected function get_sortable_columns() {
        return array(
            'certificate_id' => array('certificate_id', false),
            'student_name' => array('student_name', false),
            'issue_date' => array('issue_date', false)
        );
    }

    protected function column_default($item, $column_name) {
        return isset($item[$column_name]) ? $item[$column_name] : '';
    }

    protected function column_cb($item) {
        return sprintf('<input type="checkbox" name="certificate_ids[]" value="%s">', $item['certificate_id']);
    }

    protected function column_certificate_id($item) {
        $title = '<strong>' . $item['certificate_id'] . '</strong>';
        $actions = array(
            'edit' => sprintf('<a href="?page=certificate_verification_add&action=edit&certificate_id=%s">%s</a>',
                $item['certificate_id'],
                __('Edit', 'certificate-verification')),
            'delete' => sprintf('<a href="%s" onclick="return confirm(\'%s\')">%s</a>',
                wp_nonce_url(
                    admin_url('admin.php?page=certificate_verification&action=delete&certificate_id=' . $item['certificate_id']),
                    'delete_certificate_' . $item['certificate_id']
                ),
                __('Are you sure?', 'certificate-verification'),
                __('Delete', 'certificate-verification'))
        );

        return $title . $this->row_actions($actions);
    }

    protected function column_status($item) {
        $current_date = date('Y-m-d');
        $status = 'active';
        $label = __('Active', 'certificate-verification');

        if (!$item['is_active']) {
            $status = 'inactive';
            $label = __('Inactive', 'certificate-verification');
        } elseif ($item['expiry_date'] && $item['expiry_date'] < $current_date) {
            $status = 'expired';
            $label = __('Expired', 'certificate-verification');
        }

        return sprintf('<span class="certificate-status %s">%s</span>', $status, $label);
    }

    protected function column_actions($item) {
        return sprintf(
            '<a href="%s" target="_blank" class="button">%s</a>',
            home_url('/verify-certificate?id=' . urlencode($item['certificate_id'])),
            __('View', 'certificate-verification')
        );
    }

    public function prepare_items() {
        global $wpdb;

        $db = Certificate_Verification_Database::get_instance();

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $per_page = $this->get_items_per_page('certificates_per_page', 20);
        $current_page = $this->get_pagenum();
        $total_items = $db->count_certificates();

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page
        ));

        $this->items = $db->get_all_certificates($per_page, $current_page);
    }

    protected function get_bulk_actions() {
        return array(
            'export_csv' => __('Export to CSV', 'certificate-verification'),
            'delete' => __('Delete', 'certificate-verification')
        );
    }
}