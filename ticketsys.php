<?php
/**
 *
 * @package   ticketsys
 * @author    EgyFirst <mail@amreha.com>
 * @license   GPL-2.0+
 * @link      http://www.amreha.com
 * @copyright 2014 EgyFirst
 *
 * @wordpress-plugin
 * Plugin Name:       ticketsys
 * Plugin URI:       
 * Description:       Ticketing system plugin for wordpress
 * Version:           1.0.8
 * Author:      	  EgyFirst Sostware
 * Author URI:        http://www.egyfirst.com
 * Text Domain:       ticketsys
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-ticketsys.php' );

#when plugin is activated
register_activation_hook( __FILE__, array( 'ticketsys', 'activate' ) );

#when the plugin is deactivated
register_deactivation_hook( __FILE__, array( 'ticketsys', 'deactivate' ) );

#when any plugin is loaded
add_action( 'plugins_loaded', array( 'ticketsys', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
// var_dump($wp_roles);
// exit;
if ( true ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-ticketsys-admin.php' );
	add_action( 'plugins_loaded', array( 'ticketsys_Admin', 'get_instance' ) );

}
function faq_func( $atts ) {
	$dbprefix = "b1st_ts_";
	wp_enqueue_style( 'spry-style', plugins_url( '/public/assets/css/SpryAccordion.css' , __FILE__ ) );

	$config = simplexml_load_file(plugin_dir_path( __FILE__ ).'/config/config.xml');
    global $wpdb;
	$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
	$length = 5;
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
	$id = $randomString;
	$data = "";
	$faqs = "";
	if($atts['collapsed'] == "true" || $atts['collapsed'] == "") {
	$data .= '
	<script type="text/javascript">
		//var ts = jQuery.noConflict();
		jQuery(function(ts) {
			ts("#'.$id.' .faq-panel .faq-tab .msg").click(function() {
				ts(this).parent().parent().children(".faq-content").slideToggle();
					ts(this).parent().parent().toggleClass("open");
			});
		});
		</script><style>#'.$id.' .faq-content {display: none;}</style>';
	} else {
	$data .= '
	<script type="text/javascript">
		//var ts = jQuery.noConflict();
		jQuery(function(ts) {
			ts("#'.$id.' .faq-panel .faq-tab .msg").toggleClass("open");
			ts("#'.$id.' .faq-panel .faq-tab .msg").click(function() {
				ts(this).parent().parent().children(".faq-content").slideToggle();
					ts(this).parent().parent().toggleClass("open");
			});
		});
		</script>';
	}

	if(isset($atts['product']))
		$faqs = $wpdb->get_results("SELECT * FROM {$dbprefix}faqs WHERE product = '{$atts['product']}' order by product");
	else
		$faqs = $wpdb->get_results("SELECT * FROM {$dbprefix}faqs order by product");
		global $wp;
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		
		$data .= '<div id="'.$id.'" class="Accordion" tabindex="0">';
		$products = array();
		foreach($faqs as $faq) {
			
			$product = "";
			if(!in_array($faq->product, $products)) {
				$product = $faq->product;
				array_push($products, $faq->product);
			}
			
			if($product)
				$data .= "<h3>".ucwords($product)."</h3>";
			$data .= '<div class="AccordionPanel faq-panel">
					<div class="faq-tab AccordionPanelTab"><div class="msg">'.$faq->message.'</div>';
			$data .= '</div>
					<div class="faq-content AccordionPanelContent">'.$faq->reply.'</div>
				  </div>';
		}
		$data .= '</div>';
	return $data;
}
add_shortcode('faq', 'faq_func');
