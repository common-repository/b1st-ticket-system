<?php

/**
 * Plugin Name.
 *
 * @package   ticketsys
 * @author    EgyFirst <mail@amreha.com>
 * @license   GPL-2.0+
 * @link      http://www.amreha.com
 * @copyright 2014 EgyFirst
 */
/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-ticketsys-admin.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package ticketsys
 * @author  Your Name <email@example.com>
 */
require_once( plugin_dir_path(__FILE__) . '../config/configFile.php' );
require_once( plugin_dir_path(__FILE__) . './widget-ticketsys.php' );
require_once(ABSPATH . 'wp-admin/includes/file.php');
class ticketsys {

    const VERSION = '1.0.0';

    protected $plugin_slug = 'ticketsys';
    protected static $instance = null;
    protected static $caps = array('ticketsys-admin', 'ticketsys-manage', 'ticketsys-close', 'ticketsys-delete','ticketsys-read');

    private function __construct() {

        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'] . '/b1st';
        $attachements_dir = $upload_dir['basedir'] . '/b1st/attachements';
        $backup_dir = $upload_dir['basedir'] . '/b1st/backup';
        
        if (!file_exists($base_dir)) {
            mkdir($base_dir);
            chmod($base_dir, 0755);
        }
        if (!file_exists($attachements_dir)) {
            mkdir($attachements_dir);
            chmod($attachements_dir, 0755);
        }
        if (!file_exists($backup_dir)) {
            mkdir($backup_dir);
            chmod($backup_dir, 0755);
        }
        if((!file_exists($base_dir) || !file_exists($attachements_dir) || !file_exists($backup_dir)))
            die('Cannot set permissions, Please make sure that permissions for 
                "b1st" folder and all it subdirs in wp_content, are set to 755.');

        // Load plugin text domain
        add_action('init', array($this, 'load_plugin_textdomain'));
        add_action('template_redirect', array($this, 'formPages'));
        add_action('init', array($this, 'formActions'));
        // Activate plugin when new blog is added
        add_action('wpmu_new_blog', array($this, 'activate_new_site'));
        add_shortcode('ticketsys', array($this, 'render_shortcode'));
        // Load public-facing style sheet and JavaScript.
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('activated_plugin', array($this, 'save_error'));

        add_action('wp_ajax_my_action', array($this, 'ContactAjax'));
        add_action('wp_ajax_nopriv_my_action', array($this, 'ContactAjax'));

        add_action('wp_login', array($this, 'login_action'));
        add_action('widgets_init', create_function('', 'return register_widget("tsWidget");')
        );
        WP_Filesystem();
        global $wp_filesystem;
        global $wpdb;
    }

    /**
     * Return the plugin slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    1.0.0
     */
    private static function single_activate() {
        global $wpdb;
        $dbprefix = 'b1st_ts_';
        $answers = " CREATE TABLE IF NOT EXISTS `{$dbprefix}answers` (`id` int(11) NOT NULL AUTO_INCREMENT, `a_msg_id` int(11) NOT NULL, `ans_ticket_id` varchar(50) NOT NULL, `a_date` varchar(16) NOT NULL, `a_msg_date` varchar(16) NOT NULL, `a_response` varchar(16) NOT NULL, `a_content` text NOT NULL, `a_email` varchar(100) NOT NULL, `a_account` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; \n ";
        $messages = " CREATE TABLE IF NOT EXISTS `{$dbprefix}messages` ( `msg_update_date` varchar(16) NOT NULL, `msg_id` int(11) NOT NULL AUTO_INCREMENT, `msg_ticket_id` varchar(50) NOT NULL, `msg_date` varchar(16) NOT NULL, `msg_subject` varchar(100) NOT NULL, `msg_name` varchar(100) NOT NULL, `msg_email` varchar(100) NOT NULL, `msg_content` text NOT NULL, `msg_division` varchar(50) NOT NULL, `msg_phone` varchar(25) NOT NULL, `msg_priority` int(11) NOT NULL DEFAULT '3', `msg_status` varchar(20) NOT NULL, `msg_spam` tinyint(1) NOT NULL DEFAULT '0', `msg_product` varchar(100) NOT NULL, `msg_rating` float NOT NULL DEFAULT '0', PRIMARY KEY (`msg_id`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;\n ";
        $faqs = " CREATE TABLE IF NOT EXISTS `{$dbprefix}faqs` ( `id` int(11) NOT NULL AUTO_INCREMENT, `message` longtext NOT NULL,  `product` varchar(50) NOT NULL, `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `reply` longtext NOT NULL, `rating` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1; \n ";
        $msg_rating = " CREATE TABLE IF NOT EXISTS `{$dbprefix}msg_rating` ( `aid` int(11) NOT NULL, `mid` int(11) NOT NULL, `votes` int(11) NOT NULL DEFAULT '1', `stars` int(11) NOT NULL ) ENGINE=MyISAM DEFAULT CHARSET=latin1; \n";
        $account = " CREATE TABLE IF NOT EXISTS `{$dbprefix}accounts` ( `id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(50) NOT NULL, `delete_right` int(11) NOT NULL, `close_right` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; \n ";

        configFile::set('active', 'true');
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($messages);
        dbDelta($answers);
        dbDelta($account);
        dbDelta($faqs);
        dbDelta($msg_rating);
        $sql = "SELECT * FROM $wpdb->users";
        foreach ($wpdb->get_results($sql) as $col) {
            if (is_super_admin($col->ID)) {
                $user = new WP_User($col->ID);
                foreach (self::$caps as $cap) {
                    $user->add_cap($cap);
                }
            }
        }
        add_option('ticketsys_lang_admin', 'en_US');
        add_option('ticketsys_lang_user', 'en_US');
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function render_shortcode() {
        $config = simplexml_load_file(plugin_dir_path(__FILE__) . '../config/config.xml');
        global $wpdb;
        $dbprefix = 'b1st_ts_';
        include 'views/index.php';
    }

    public static function activate($network_wide) {
        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    self::single_activate();
                }

                restore_current_blog();
            } else {
                self::single_activate();
            }
        } else {
            self::single_activate();
        }
		 
		add_user_meta( get_current_user_id( ), 'initial_time', date('U'));
		
		$file_print = md5_file(plugin_dir_path( __FILE__ ).'../admin/controllers.php') ;
		$file_print .= md5_file(plugin_dir_path( __FILE__ ).'../admin/views/messages.php') ;
		$file_print .= md5_file(plugin_dir_path( __FILE__ ).'../admin/views/users.php') ;
		$file_print .= md5_file(plugin_dir_path( __FILE__ ).'../admin/views/settings.php') ;
		$file_print .= md5_file(plugin_dir_path( __FILE__ ).'../admin/views/products.php') ;
		$file_print .= md5_file(plugin_dir_path( __FILE__ ).'../admin/views/divisions.php') ;
		$file_print .= md5_file(plugin_dir_path( __FILE__ ).'../admin/views/email.php') ;
		delete_user_meta( get_current_user_id( ), 'initial_print');
		add_user_meta( get_current_user_id( ), 'initial_print', $file_print);
		
    }

    public static function deactivate($network_wide) {
        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    self::single_deactivate();
                }

                restore_current_blog();
            } else {
                self::single_deactivate();
            }
        } else {
            self::single_deactivate();
        }
    }

    public function activate_new_site($blog_id) {
        if (1 !== did_action('wpmu_new_blog')) {
            return;
        }

        switch_to_blog($blog_id);
        self::single_activate();
        restore_current_blog();
    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    1.0.0
     *
     * @return   array|false    The blog ids, false if no matches.
     */
    private static function get_blog_ids() {
        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
            WHERE archived = '0' AND spam = '0'
            AND deleted = '0'";

        return $wpdb->get_col($sql);
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    1.0.0
     */
    private static function single_deactivate() {
        configFile::set('active', 'false');
        self::remove_cap();
    }

    private static function add_cap() {
        return;
        $roles = get_editable_roles();
        global $wp_roles;
        $role = get_role('administrator');
        foreach (self::$caps as $cap) {
            $role->add_cap($cap);
        }
    }

    private static function remove_cap() {
        global $wpdb;
        $sql = "SELECT * FROM $wpdb->users";
        foreach ($wpdb->get_results($sql) as $col) {
            if (is_super_admin($col->ID)) {
                $user = new WP_User($col->ID);
                foreach (self::$caps as $cap) {
                    $user->remove_cap($cap);
                }
            }
        }
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        $domain = $this->plugin_slug;
        //$locale = apply_filters('plugin_locale', get_locale(), $domain);
        $locale_admin = get_option('ticketsys_lang_admin');
        $locale_user = get_option('ticketsys_lang_user');
        //load_plugin_textdomain($domain, false, basename(dirname(dirname(__FILE__))) . '/languages');
        if (is_admin())
            load_textdomain($domain, dirname(dirname(__FILE__)) . '/languages/' . $domain . '-' . $locale_admin . '.mo');
        else
            load_textdomain($domain, dirname(dirname(__FILE__)) . '/languages/' . $domain . '-' . $locale_user . '.mo');
    }

    /**
     * Register and enqueue public-facing style sheet.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_register_style('ticketsys-style', plugins_url('assets/css/stylesheet.css', __FILE__), false, '1.0.0');
        wp_register_style('ticketsys-style-bootstrap', plugins_url('assets/css/bootstrap-all.css', __FILE__), false, '1.0.0');
        wp_register_style('theme_name', get_stylesheet_uri(), array('style'));

        wp_enqueue_style('ticketsys-style');
        // wp_enqueue_style( 'theme-name' );

        wp_enqueue_style('ticketsys-style-bootstrap');
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        //wp_register_script( 'jquery-js', includes_url() . 'js/jquery/jquery.js', false, '1.0.0' );
        //wp_register_script( 'jquery-js', plugins_url('assets/js/jquery.min.js', __FILE__ ), false, '1.0.0' );
        wp_register_script('contact-js', plugins_url('assets/js/contact.js', __FILE__), false, '1.0.0');


        wp_enqueue_script('jquery');
        //wp_enqueue_script( 'jquery-js' );
        wp_enqueue_script('contact-js');


        wp_localize_script('contact-js', 'ajaxcontactajax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    public function formPages() {
        if (isset($_GET['ticketsys_id']) && isset($_GET['token'])) {
            $config = simplexml_load_file(plugin_dir_path(__FILE__) . '../config/config.xml');
            global $wpdb;
            $dbprefix = 'b1st_ts_';
            $can_access = $config->forceregister=='no' || ($config->forceregister=='yes' && is_user_logged_in());
            show_admin_bar(is_user_logged_in());
            get_header('home');
            get_template_part('nav');
            echo '<div id="main-content" class="main-content" style="margin-top:-30px">';
            echo '<div id="primary" class="content-area" style="padding-top:0">';
            echo '<div id="content" class="site-content" role="main">';
            echo '<div class="bootstrap">';
            include 'views/comments.php';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            get_sidebar('left');
            get_footer('home');
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'ticketsys-download') {
            try {
                global $wpdb, $wp_filesystem;
                $ticket = $_GET['ticket'];
                $fileName = $_GET['fileName'];

                $upload_dir = wp_upload_dir();
                $file = $upload_dir['basedir'] . '/b1st/' . $ticket;

                if (isset($_GET['answer']))
                    $file.='/' . $_GET['answer'];
                $file.='/' . $fileName;

                if (!file_exists($file) || strstr($fileName, "/"))
                    throw new Exception($file);

                include 'views/download.php';
                exit;
            } catch (Exception $e) {
                echo "File Not Found";
                exit;
            }
        }
    }

    public function formActions() {
        if (isset($_POST['action']) && $_POST['action'] == 'ticketsys-submit') {
            $config = simplexml_load_file(plugin_dir_path(__FILE__) . '../config/config.xml');
            global $wpdb;
            $dbprefix = 'b1st_ts_';
            include 'views/contact.php';
            exit;
        } else if (isset($_POST['action']) && $_POST['action'] == 'ticketsys-upload') {
            $config = simplexml_load_file(plugin_dir_path(__FILE__) . '../config/config.xml');
            global $wpdb;
            $dbprefix = 'b1st_ts_';
            include 'includes/scan.php';
            include 'views/upload.php';
            exit;
        } else if (isset($_POST['action']) && $_POST['action'] == 'ticketsys-comment') {
            $config = simplexml_load_file(plugin_dir_path(__FILE__) . '../config/config.xml');
            global $wpdb;
            $dbprefix = 'b1st_ts_';
            include 'views/comments.php';
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'ticketsys-widget') {
            $config = simplexml_load_file(plugin_dir_path(__FILE__) . '../config/config.xml');
            global $wpdb;
            $dbprefix = 'b1st_ts_';
            include 'views/index1.php';
            exit;
        }
    }

    /**
     * NOTE:  Actions are points in the execution of a page or process
     *        lifecycle that WordPress fires.
     *
     *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    1.0.0
     */
    public function action_method_name() {
        // @TODO: Define your action hook callback here
    }

    /**
     * NOTE:  Filters are points of execution in which WordPress modifies data
     *        before saving it or sending it to the browser.
     *
     *        Filters: http://codex.wordpress.org/Plugin_API#Filters
     *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since    1.0.0
     */
    public function save_error() {
        update_option('plugin_error', ob_get_contents());
    }

}
