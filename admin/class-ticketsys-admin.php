<?php

/**
 * Plugin Name.
 *
 * @package   ticketsys_Admin
 * @author    EgyFirst 
 * @license   GPL-2.0+
 * @link      email@example.com
 * @copyright 2014 EgyFirst
 */
/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-ticketsys.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package ticketsys_Admin
 * @author  Your Name <email@example.com>
 */
require_once('controllers.php');
require_once('includes/nocsrf.php');

class ticketsys_Admin {

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;
    private $controllers = null;

    /**
     * Slug of the plugin screen.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    private function __construct() {
        WP_Filesystem();
        global $wp_filesystem;
        global $wpdb;
        $plugin = ticketsys::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();
        $this->controllers = new controllers();
        // Load admin style sheet and JavaScript.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        //add_action('init', array($this, 'formActions'));
        // Add the options page and menu item.
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('activated_plugin', array($this, 'save_error'));
        // Add an action link pointing to the options page.
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_slug . '.php');
        add_filter('plugin_action_links_' . $plugin_basename, array($this, 'add_action_links'));

        add_action('init', array($this, 'login_action'));
        add_action('init', array($this, 'ajax_actions'));
        add_filter('query_vars', array($this, 'query_vars'));
    }

    public function ajax_actions() {

        if (isset($_GET['page']) && $_GET['page'] == 'ticketsys-dobackup-settings') {
            $this->controllers->doBackupView();
            exit;
        }
        elseif (isset($_GET['page']) && $_GET['page'] == 'ticketsys-restoredb-settings') {
            $this->controllers->RestoredbView();
            exit;
        }
    }

    public function login_action() {
        global $current_user;
        global $wpdb;
        $dbprefix = 'b1st_ts_';
        if (is_user_logged_in() && !session_id()) {
            $_SESSION['username'] = $current_user->user_login;
            $_SESSION['id'] = $current_user->ID;
            $_SESSION['email'] = $current_user->user_email;
            $_SESSION['close_right'] = $current_user->has_cap('ticketsys-close');
            $_SESSION['delete_right'] = $current_user->has_cap('ticketsys-delete');
            $_SESSION['read_right'] = $current_user->has_cap('ticketsys-read');
            $_SESSION['admin'] = is_admin();
            $token = NoCSRF::generate('csrf_token');
            $_SESSION['csrf_token_all'] = $token;
        }
        $this->rewrites();
        ob_start();
    }

    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @TODO:
     *
     * - Rename "ticketsys" to the name your plugin
     *
     * @since     1.0.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_styles() {
        wp_register_style('bootstrap-css', plugins_url('assets/styles/bootstrap.css', __FILE__), false, '1.0.0');
        #wp_register_style( 'bootstrap-responsive-css', plugins_url('assets/styles/bootstrap-responsive.css', __FILE__ ), false, '1.0.0' );

        wp_enqueue_style('bootstrap-css');
        #wp_enqueue_style( 'bootstrap-responsive-css' );
    }

    /**
     * Register and enqueue admin-specific JavaScript.
     *
     * @TODO:
     *
     * - Rename "ticketsys" to the name your plugin
     *
     * @since     1.0.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_scripts() {
        wp_register_script('bootstrap-js', plugins_url('assets/js/bootstrap.min.js', __FILE__), array('jquery'), '1.0.0');
        wp_register_script('raphael-js', plugins_url('assets/js/raphael-2.1.0-min.js', __FILE__), array('jquery'), '1.0.0');
        wp_register_script('colorwheel-js', plugins_url('assets/js/colorwheel.js', __FILE__), array('jquery'), '1.0.0');
        wp_enqueue_script('bootstrap-js');
        wp_enqueue_script('raphael-js');
        wp_enqueue_script('colorwheel-js');
    }

    public function rewrites() {
        
    }

    public function query_vars($query_vars) {
        $query_vars[0] = 'edit';
        $query_vars[0] = 'edited';
        return $query_vars;
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        include_once('controllers.php');
        $this->plugin_screen_hook_suffix = add_menu_page(
                __('Ticketing System', $this->plugin_slug), __('Ticketing System', $this->plugin_slug), 'ticketsys-manage', $this->plugin_slug, '#'
        );
        $this->renderMenu('messages', 'users', 'products', 'divisions', 'email', 'backup', 'stats', 'faq', 'settings');
        $this->renderPrivate();
        global $submenu;
        unset($submenu[$this->plugin_slug][0]);
    }

    private function renderPrivate() {
        $this->plugin_screen_hook_suffix = add_submenu_page(
                __('Ticketing System', $this->plugin_slug), __('Ticketing System', $this->plugin_slug), '', 'ticketsys-manage', $this->plugin_slug . '-' . 'dobackup' . '-settings', array($this->controllers, 'dobackup')
        );

        // $this->plugin_screen_hook_suffix =   add_options_page(__( 'Ticketing System').' Plugin Options', __( 'Ticketing System'), 'manage_options', __( 'Ticketing System').'_options', array($this->controllers, 'settings'));
        $this->plugin_screen_hook_suffix = add_submenu_page(
                __('Ticketing System', $this->plugin_slug), __('Ticketing System', $this->plugin_slug), '', 'ticketsys-manage', $this->plugin_slug . '-' . 'restoredb' . '-settings', array($this->controllers, 'restoredb')
        );
    }

    private function renderMenu() {
        foreach (func_get_args() as $param) {
            $this->renderMenuItem($param);
        }
    }

    private function renderMenuItem($pageName) {
        $page = strtolower($pageName);
        $this->plugin_screen_hook_suffix = add_submenu_page(
                $this->plugin_slug, ucfirst(__($page, 'ticketsys')), ucfirst(__($page, 'ticketsys')), 'ticketsys-manage', $this->plugin_slug . '-' . $page . '-settings', array($this->controllers, $page)
        );
    }
    public function formActions() {
        if (isset($_GET['restore'])) {
            echo 'working';
            exit;
        }
    }
    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links($links) {
        return array_merge(
                array(
            'settings' => '<a href="' . admin_url('admin.php?page=ticketsys-settings-settings') . '">' . __('Settings', $this->plugin_slug) . '</a>'
                ), $links
        );
    }

    /**
     * NOTE:     Actions are points in the execution of a page or process
     *           lifecycle that WordPress fires.
     *
     *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    1.0.0
     */
    public function action_method_name() {
        // @TODO: Define your action hook callback here
    }

    /**
     * NOTE:     Filters are points of execution in which WordPress modifies data
     *           before saving it or sending it to the browser.
     *
     *           Filters: http://codex.wordpress.org/Plugin_API#Filters
     *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since    1.0.0
     */
    public function filter_method_name() {
        // @TODO: Define your filter hook callback here
    }

    public static function save_error() {
        update_option('plugin_error', ob_get_contents());
        echo get_option('plugin_error');
        exit;
    }

}
