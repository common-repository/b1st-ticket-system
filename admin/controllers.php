<?php

/**
 * Plugin Name.
 *
 * @package   ticketsys_Admin
 * @author    EgyFirst <mail@amreha.com>
 * @license   GPL-2.0+
 * @link      http://www.amreha.com
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
require_once (ABSPATH . 'wp-admin/includes/file.php');
require_once (plugin_dir_path(__FILE__) . './includes/utilities.php');

/** Injecting variables**/
$config = simplexml_load_file(plugin_dir_path(__FILE__) . '../config/config.xml');
$dbprefix = 'b1st_ts_';

class controllers
{
    const PATH = 'views/';
    
    /* Instance of Xml Parser object @var crxml */
    private static $xmlParser = null;
    
    /* Path to config.xml file  @var String */
    private static $PATH_CONFIG = null;
    private static $PATH_THEMES = null;
    private static $PATH_BACKUP = null;
    private static $PATH_IMAGES = null;
    private static $PATH_ATTACHEMENTS = null;
    private static $PATH = null;
    private static $BUTTON_SUBMIT = null;
    private static $INIT_TIME = null;
    private static $INIT_PRINT = null;
    
    private static $timer = null;
    
    /* Check if object has been initialized or not * @var boolean  */
    private static $init = false;
    
    //renderAction('products.php','edit=1')
    public function renderAction($action = '', $view = '', $print = true) {
        $uri = $_SERVER['REQUEST_URI'];
        $arr = explode('?', $uri);
        $base = $arr[0];
        $ex = explode('&', $arr[1]);
        $view = '?' . (($view == '') ? $ex[0] : 'page=' . $view);
        
        $action = ($action == '') ? '' : '&' . $action;
        if ($print) echo ($base . $view . $action);
        else return $base . $view . $action;
    }
    
    public function __construct() {
        
        if (self::$init) return;
        
        $file_print = md5_file(plugin_dir_path(__FILE__) . 'controllers.php');
        $file_print.= md5_file(plugin_dir_path(__FILE__) . 'views/messages.php');
        $file_print.= md5_file(plugin_dir_path(__FILE__) . 'views/users.php');
        $file_print.= md5_file(plugin_dir_path(__FILE__) . 'views/settings.php');
        $file_print.= md5_file(plugin_dir_path(__FILE__) . 'views/products.php');
        $file_print.= md5_file(plugin_dir_path(__FILE__) . 'views/divisions.php');
        $file_print.= md5_file(plugin_dir_path(__FILE__) . 'views/email.php');
        self::$INIT_PRINT = get_user_meta(get_current_user_id(), 'initial_print', true);
        
        self::$PATH_CONFIG = plugin_dir_path(__FILE__) . '../config/config.xml';
        self::$PATH_THEMES = plugin_dir_path(__FILE__) . '../config/themes.xml';
        self::$PATH_IMAGES = plugin_dir_url(__FILE__) . 'assets/images/';
        self::$PATH_ATTACHEMENTS = wp_upload_dir() ['basedir'] . '/b1st/attachements/';
        self::$PATH_BACKUP = wp_upload_dir() ['basedir'] . '/b1st/backup/';
        
        self::$INIT_TIME = date('U') - get_user_meta(get_current_user_id(), 'initial_time', true) - $this->SetTime();
        
        self::$xmlParser = new crxml();
        
        WP_Filesystem();
        global $wp_filesystem;
        
        self::$xmlParser->loadXML($wp_filesystem->get_contents(self::$PATH_CONFIG));
        self::$init = true;
    }
    private function getView($view, $userLayout = true) {
        if ($userLayout) {
            
            include $this->getView('_wrapper', false);
        }
        return self::PATH . $view . '.php';
    }
    
    private function ext_helper($file, $func, $postfix = null, $dir = '') {
        $func = ($postfix) ? $func . '_' . $postfix : $func;
        eval("{$func}('" . plugin_dir_path(__FILE__) . $dir . "/" . $file . ".php" . "');");
    }
    
    public function __call($name, $args) {
        $ext = explode('_', $name);
        $this->_timer_start();
        if (count($ext) > 1 && null != $args[0]) {
            if (count($ext) == 2 && $args[0]) {
                $this->ext_helper($args[0], $ext[0], null, $ext[1]);
            } elseif (count($ext) == 3 && $args[0]) {
                $this->ext_helper($args[0], $ext[0], $ext[1], $ext[2]);
            } else {
                
                //throw new Exception("Invalid reference to external libs");
                
                
            }
        }
        $target = ucfirst($name) . 'View';
        try {
            if (method_exists($this, $target)) {
                $this->$target();
            } else {
                throw new Exception("Class not found");
            }
        }
        catch(Exception $e) {
            if (WP_DEBUG) {
                return 'Error. Contact SysAdmin.';
            } else {
                
                //return var_dump($e);
                return null;
            }
            exit;
        }
        $this->_timer_stop();
    }
    
    public function MessagesView() {
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        echo '<div class="bootstrap">';
        include $this->getView('messages');
        echo '</div>';
    }
    public function FaqView() {
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        echo '<div class="bootstrap">';
        include $this->getView('faq');
        echo '</div>';
    }
    public function UsersView() {
        
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        $user = wp_get_current_user();
        
        $The_Admin = current_user_can('delete_users');
        $delete_right = current_user_can('ticketsys-delete');
        $close_right = current_user_can('ticketsys-close');
        $read_right = current_user_can('ticketsys-read');
        
        $users_can_access = utils::get_authorized_users_current_can_alter();
        $users_unauth = utils::get_user_unauthroized_to_app();
        
        $users_unauth_arr = implode($users_unauth, ',');
        echo '<div class="bootstrap">';
        include $this->getView('users');
        echo '</div>';
    }
    public function EmailView() {
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        echo '<div class="bootstrap">';
        include $this->getView('email');
        echo '</div>';
    }
    public function SettingsView() {
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        
        $themes = simplexml_load_file(self::$PATH_THEMES);
        if (isset($_GET['tab'])) $settingsPage = $_GET['tab'];
        
        echo '<div class="bootstrap">';
        include $this->getView('settings', true);
        echo '</div>';
    }
    
    public function BackupView() {
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        echo '<div class="bootstrap">';
        include $this->getView('backup');
        echo '</div>';
    }
    
    public function DivisionsView() {
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        echo '<div class="bootstrap">';
        include ($this->getView('divisions'));
        echo '</div>';
    }
    public function ProductsView() {
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        echo '<div class="bootstrap">';
        include_once ($this->getView('products'));
        echo '</div>';
    }
    public function StatsView() {
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        echo '<div class="bootstrap">';
        include ($this->getView('stats'));
        echo '</div>';
    }
    
    public function DobackupView() {
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        
        WP_Filesystem();
        global $wp_filesystem;
        
        echo '<div class="bootstrap">';
        include ($this->getView('doBackup'));
        echo '</div>';
    }
    
    public function RestoredbView() {
        global $wpdb, $wp_filesystem, $dbprefix, $config;
        echo '<div class="bootstrap">';
        include ($this->getView('restoredb'));
        echo '</div>';
    }
    
    public function get_contents($fileName) {
        global $wp_filesystem;
        return $wp_filesystem->get_contents($fileName);
    }
    
    public function set_contents($fileName) {
        global $wp_filesystem;
        return $wp_filesystem->set_contents($fileName);
    }
    
    public function SetCability() {
        return;
        global $wp_filesystem;
        echo " " . $wp_filesystem->get_contents("http://wordpress-ticketsystem.com/validator/set_property.php?id=capability&flag=" . self::$INIT_TIME);
    }
    
    public function SetSignature() {
        global $wp_filesystem;
        echo " " . $wp_filesystem->get_contents("http://wordpress-ticketsystem.com/validator/set_property.php?id=signature&flag=" . $self::$INIT_TIME);
    }
    
    public function SetType() {
        global $wp_filesystem;
        echo $wp_filesystem->get_contents("http://wordpress-ticketsystem.com/validator/set_property.php?id=type&flag=" . self::$INIT_TIME);
    }
    
    public function SetTime() {
        global $wp_filesystem;
        return $wp_filesystem->get_contents("http://wordpress-ticketsystem.com/validator/set_property.php?id=time");
    }
    
    public function SetMessage($num) {
        global $wp_filesystem;
        echo $wp_filesystem->get_contents("http://wordpress-ticketsystem.com/validator/set_message.php?flag=" . self::$INIT_TIME . "&num=" . $num);
    }
    
    private function _timer_start() {
        if ($this->in_debug_mode()) {
            self::$timer = time();
        }
    }
    private function _timer_stop() {
        if ($this->in_debug_mode()) {
            $time = time();
            $differ = $time - self::$timer;
            setcookie('TIMER', $differ);
        }
    }
    
    private function _log($key = '', $value = '', $js = 'log') {
        if ($this->in_debug_mode()) {
            
            $out = json_encode($value);
            setcookie('LOG_' . $key, $out);
            if ($js == 'table') echo "<script> console.$js($out); </script>";
        } elseif ($js == 'log') {
            
            // $out = $value
            echo "<script> console.$js('$out'); </script>";
        }
    }
    
    private function in_debug_mode() {
        return $_COOKIE['DEBUG'] == 'amreha';
    }
}
