<?php
/*
Plugin Name: Certificate Verification System
Description: A plugin to verify diploma and university course certificates
Version: 1.0
Author: Your Name
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('CERT_VERIFICATION_VERSION', '1.0');
define('CERT_VERIFICATION_PATH', plugin_dir_path(__FILE__));
define('CERT_VERIFICATION_URL', plugin_dir_url(__FILE__));

// Include required files
require_once CERT_VERIFICATION_PATH . 'includes/class-database.php';
require_once CERT_VERIFICATION_PATH . 'includes/class-shortcodes.php';
require_once CERT_VERIFICATION_PATH . 'includes/class-admin.php';
require_once CERT_VERIFICATION_PATH . 'includes/class-verification.php';

class Certificate_Verification_System {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array('Certificate_Verification_Database', 'activate'));
        register_deactivation_hook(__FILE__, array('Certificate_Verification_Database', 'deactivate'));

        // Initialize components
        add_action('plugins_loaded', array($this, 'init'));

        // Load assets
        add_action('wp_enqueue_scripts', array($this, 'load_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'load_admin_assets'));
    }

    public function init() {
        Certificate_Verification_Database::get_instance();
        Certificate_Verification_Shortcodes::get_instance();
        Certificate_Verification_Admin::get_instance();
        Certificate_Verification::get_instance();
    }

    public function load_frontend_assets() {
        wp_enqueue_style(
            'cert-verification-frontend',
            CERT_VERIFICATION_URL . 'assets/css/frontend.css',
            array(),
            CERT_VERIFICATION_VERSION
        );

        wp_enqueue_script(
            'cert-verification-frontend',
            CERT_VERIFICATION_URL . 'assets/js/frontend.js',
            array('jquery'),
            CERT_VERIFICATION_VERSION,
            true
        );
    }

    public function load_admin_assets($hook) {
        if (strpos($hook, 'certificate_verification') === false) {
            return;
        }

        wp_enqueue_style(
            'cert-verification-admin',
            CERT_VERIFICATION_URL . 'assets/css/admin.css',
            array(),
            CERT_VERIFICATION_VERSION
        );

        wp_enqueue_script(
            'cert-verification-admin',
            CERT_VERIFICATION_URL . 'assets/js/admin.js',
            array('jquery'),
            CERT_VERIFICATION_VERSION,
            true
        );
    }
}

Certificate_Verification_System::get_instance();