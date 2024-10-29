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
require_once( plugin_dir_path( __FILE__ ) . 'xml.php' );
require_once(ABSPATH . 'wp-admin/includes/file.php');
class configFile
{
	/* Instance of Xml Parser object @var crxml */
	private static $xmlParser=null;

	/* Path to config.xml file  @var String */
	private static $path = null;

	/* Check if object has been initialized or not * @var boolean  */
	private static $init = false;

	/**
     * Construct won't be called inside this class and is uncallable from
     * the outside. This prevents instantiating this class.
     * This is by purpose, because we want a static class.
     */
    private function __construct() {}

	private static function init()
    {
    	if (self::$init)
    		return;
    	self::$PATH_CONFIG=plugin_dir_path( __FILE__ ) .'config.xml';
    	self::$xmlParser = new crxml();
    	self::$xmlParser->loadXML($wp_filesystem->get_contents(self::$PATH_CONFIG));
    	self::$init=true;

    }
    
    /**
     * Get value of a key
     * @param  String $key key in xml file
     * @return String      Value of target key
     */
    public static function get($key){
    	self::init();
    	return self::$xmlParser->config->$key;
    }

    /**
     * Set matching key with a value from config file
     * @param String $key   Key of value
     * @param String $value New Value
     */
    public static function set($key, $value){
    	 self::init();
    	 self::$xmlParser->config->active = $value;
    	 $wp_filesystem->put_contents(self::$PATH_CONFIG, self::$xmlParser->xml());
    }
}
