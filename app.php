<?php 
/*
Plugin Name: WooCommerce Update Shipment Tracking
Description: WooCommerce update shipment tracking by uploading csv
Author: Yasar Yousuf <yousuf802@gmail.com>
Author URI:        yasaryousuf.me
Version: 0.1
Text Domain: wc-update-shipment-tracking
*/

define("WCUST_PATH", plugin_dir_path( __FILE__ ));
define("WCUST_VIEW_PATH", plugin_dir_path( __FILE__ ) . "view/");
define("WCUST_ASSETSURL", plugins_url("assets/", __FILE__));

require_once (ABSPATH . 'wp-includes/class-phpass.php');

class Autoloader {
    static public function loader($className) {
        $filename = WCUST_PATH . "src/" . str_replace("\\", '/', $className) . ".php";
        if (file_exists($filename)) {
            include($filename);
            if (class_exists($className)) {
                return TRUE;
            }
        }
        return FALSE;
    }
}
spl_autoload_register('Autoloader::loader');


add_action('plugins_loaded', array('WcustAction', 'init'));
add_action('plugins_loaded', array('WcustAjaxAction', 'init'));
add_action('plugins_loaded', array('WcustEnqueue', 'init'));
add_action('plugins_loaded', array('WcustMenu', 'init'));
add_action('plugins_loaded', array('WcustShortCode', 'init'));

?>
