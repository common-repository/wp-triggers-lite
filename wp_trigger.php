<?php
/*
Plugin Name: WP Triggers Lite
Plugin URI: http://www.wptriggers.com/
Description: The Lite version creates simple trigger boxes to get visitors more involved in your site! Upgrade today for more features like shortcode support, URL redirect, submit button options, stats and more!  <a href="http://www.wptriggers.com/upgrade">Click here to upgrade to WP TRIGGERS PRO</a>
Author: WP Triggers
Author URI: http://www.wptriggers.com/
Version: 2.5.3

*/

If (! Class_Exists ( 'wpTrigger' )) {

	global $wpdb;

	define( 'WPTGG_VERSION' , '1.0' );

	define( 'WPTGG_PLUGIN_PATH', str_replace("\\", "/", plugin_dir_path(__FILE__) ) ); //use for include files to other files

	define( 'WPTGG_PLUGIN_URL' , plugins_url( '/', __FILE__ ) );

	define( 'WPTGG_TABLE' , $wpdb->prefix . "trigger" );

	

	class wpTrigger

	{

		function  __construct()

		{

			//run on activation of plugin

			register_activation_hook( __FILE__, array('wpTrigger', 'sc_run_on_activation') );

			

			//run on deactivation of plugin

			register_deactivation_hook( __FILE__, array('wpTrigger', 'sc_run_on_deactivation') );

			

			add_action('init', array('wpTrigger', 'page_load_control'));

			add_action('admin_menu',  array('wpTrigger', 'create_admin_menu'));			

			add_action('admin_print_styles', array('wpTrigger', 'admin_add_css_file') );

			add_action('admin_print_scripts', array('wpTrigger', 'admin_add_js_file') );

			

			//---front end ----

			add_action('wp_footer', array('wpTrigger', 'front_include_css_js') );

			add_shortcode('wptrigger', array('wpTrigger', 'get_trigger_process')) ;

		}

		

		static function sc_run_on_activation()

		{

			$pluginOptions = get_option('wptgg_info');

			

			if( false === $pluginOptions )

			{

				global $wpdb ;

				if ( !empty($wpdb->charset) )

					$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

				if ( !empty($wpdb->collate) )

					$charset_collate .= " COLLATE $wpdb->collate";

				

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				

				$sql_arr=array(

						"CREATE TABLE IF NOT EXISTS `" . WPTGG_TABLE . "` (

						      `ID` int(11) NOT NULL AUTO_INCREMENT,

						      `box_name` varchar(50) NOT NULL,

						      `box_info` longtext NOT NULL,

						      `no_found` longtext NOT NULL,

						      `show_chk` varchar(10) NOT NULL,

						      `create_datetime` datetime NOT NULL,

						      PRIMARY KEY (`ID`) 						      

				    		) $charset_collate"

				    	);

		    		

				foreach($sql_arr as $sql)

				{

					dbDelta($sql);

				}

		    	

		    	update_option("wptgg_info", WPTGG_VERSION) ;

			}			

		}

		

		static function sc_run_on_deactivation(){}

		

		static function page_load_control()

		{

			include( WPTGG_PLUGIN_PATH . "includes/function.php" ) ;

			wpTrigger::set_load_page() ;	

		}

		

		static function add_css_file($stylesheet_arr)

		{

			if( !is_array($stylesheet_arr) )return;

			

			foreach($stylesheet_arr as $stylesheet)

			{

				$myStyleDir = WPTGG_PLUGIN_PATH. 'css/'.$stylesheet.'.css';

				$myStyleUrl = WPTGG_PLUGIN_URL. 'css/'.$stylesheet.'.css';

				wp_register_style('wptgg_'.$stylesheet, $myStyleUrl);

				wp_enqueue_style( 'wptgg_'.$stylesheet);

			}			

		}

	

		static function add_js_file($jsfile_arr)

		{

			if( !is_array($jsfile_arr) )return ;

			

			foreach($jsfile_arr as $jsfile)

			{

				$myJsDir = WPTGG_PLUGIN_PATH. 'js/'.$jsfile.'.js';

				$myJsUrl = WPTGG_PLUGIN_URL. 'js/'.$jsfile.'.js';

				wp_register_script('wptgg_'.$jsfile, $myJsUrl);

				wp_enqueue_script( 'wptgg_'.$jsfile);	

			}			

		}	

		

		//-----admin panel---

		static function set_load_page()

		{

			global $wptgg_page_action ;

			if( $_GET["page"] == "wp-trigger" )$wptgg_page_action = "trigger_list" ;

			if( $_GET["page"] == "add-trigger" || ( $_GET["page"] == "wp-trigger" && $_GET["trigger_id"] ))$wptgg_page_action = "trigger_add" ;

		}

		

		static function create_admin_menu()

		{

	    	add_menu_page('WP Triggers', 'WP Triggers', 'administrator', 'wp-trigger', array('wpTrigger','trigger_list_page'), WPTGG_PLUGIN_URL . "img/wp-triggers-icon.png");

	    	add_submenu_page('wp-trigger', 'All Triggers','All Triggers', 'administrator', 'wp-trigger',array('wpTrigger','trigger_list_page'));

		    add_submenu_page('wp-trigger', 'New Trigger','New Trigger', 'administrator', 'add-trigger',array('wpTrigger','add_trigger_page'));		 					

		}

		

		static function admin_add_css_file()

		{			

			global $wptgg_page_action ;

			if( $wptgg_page_action == "trigger_list" )$cssfiles = array("main", "pages/admin/trigger_list");

			if( $wptgg_page_action == "trigger_add" )$cssfiles = array("main", "pages/admin/add_trigger");

			

			wpTrigger::add_css_file($cssfiles) ;	

		}

		

		static function admin_add_js_file()

		{

			global $wptgg_page_action ;

			if( $wptgg_page_action == "trigger_list" )$jsfiles = array("main", "pages/admin/trigger_list");

			if( $wptgg_page_action == "trigger_add" )$jsfiles = array("lib/jquery.json-2.3", "main", "pages/admin/add_trigger");

			

			wpTrigger::add_js_file($jsfiles) ;

		}

		

		static function trigger_list_page()

		{

			if( $_GET["page"] == "wp-trigger" && $_GET["trigger_id"] ){

				wpTrigger::add_trigger_page() ;

			}else{

				include( WPTGG_PLUGIN_PATH . "includes/pages/admin/trigger_list.php" ) ;

			}

				

		}

		

		static function add_trigger_page()

		{

			include( WPTGG_PLUGIN_PATH . "includes/pages/admin/add_trigger.php" ) ;	

		}

		

		//---fron end----

		static function front_include_css_js()

		{

			//$jsfiles = array("lib/jquery", "pages/front/trigger_process") ;

			//wpTrigger::add_js_file($jsfiles) ;

			

			echo "<script type='text/javascript'>var wptgg_ajaxurl = '" . admin_url("admin-ajax.php") . "'</script>" ;

			echo "<style>

					.wptgg_loading{

						background-image: url( '" . WPTGG_PLUGIN_URL . "img/ajax-loader.gif' ) ;

						padding:0px 7px;

						background-repeat: no-repeat;

					}

				</style>" ;

			wp_enqueue_script('wptgg_trigger_process', WPTGG_PLUGIN_URL. 'js/pages/front/trigger_process.js', array('jquery'), WPTGG_VERSION, true);

		}

		

		static function get_trigger_process($param)

		{

			ob_start();	

			include( WPTGG_PLUGIN_PATH . "includes/pages/front/trigger_process.php" ) ;	

			$content = ob_get_contents() ;

			ob_end_clean() ;

			return $content ;

		}		

	}	

	$wptrigger=new wpTrigger();	

}

add_action('wp_ajax_get_trigger_set', array( 'wptggBackEnd', 'ajax_get_trigger_one_set' ) );

add_action('wp_ajax_get_display_trigger', array( 'wptggFrontEnd', 'get_display_trigger' ) );

add_action('wp_ajax_nopriv_get_display_trigger', array( 'wptggFrontEnd', 'get_display_trigger' ) );

?>