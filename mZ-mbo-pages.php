<?php
/**
 * This file contains main plugin class and, defines and plugin loader.
 *
 * The mZoo MBO Pages plugin is for use in development of MZ-mbo-api plugin.
 *
 * @package MZMBOPAGES
 *
 * @wordpress-plugin
 * Plugin Name: 	mZoo MBO Pages
 * Description: Create virtual pages based on results from MZ-mbo-api plugin.
 * Version: 		1.0.0
 * Author: 			mZoo.org
 * Author URI: 		http://www.mZoo.org/
 * Plugin URI: 		http://www.mzoo.org/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: 	mz-mbo-pages
 * Domain Path: 	/languages
*/

if ( !defined( 'WPINC' ) ) {
    die;
}

//define plugin path and directory
define( 'MZ_MBO_PAGES_DIR', plugin_dir_path( __FILE__ ) );
define( 'MZ_MBO_PAGES_URL', plugin_dir_url( __FILE__ ) );

add_action( 'admin_init', 'mbo_pages_has_mindbody_api' );
function mbo_pages_has_mindbody_api() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) && !is_plugin_active( 'mz-mindbody-api/mZ-mindbody-api.php' ) ) {
        add_action( 'admin_notices', 'child_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function child_plugin_notice(){
    ?><div class="error"><p>Sorry, but Child Plugin requires the Parent plugin to be installed and active.</p></div><?php
}

/**
 * The MZ Mindbody API Admin defines all the functionality for the dashboard
 * of the plugin.
 *
 * This class defines version and loads the actions and functions
 * that create the dashboard.
 *
 * @since    2.1.0
 */
class MZ_MBO_Pages_Admin {
    
    protected $version;
 
    public function __construct( $version ) {
        $this->version = $version;
        $this->load_sections();
        //$this->load_pages();
        //$this->run_sandbox();
        }
        
    private function load_sections() {
        require_once MZ_MBO_PAGES_DIR .'lib/sections.php';
        }
        
    public function load_pages() {
        require_once MZ_MBO_PAGES_DIR .'lib/functions.php';
        require_once MZ_MBO_PAGES_DIR .'inc/list_classes.php';
        }
 
    public function run_sandbox($message) {
 	
 				$mz_mbo_pages = new MZ_MBO_Pages_Pages();
 		
        $mz_mbo_pages->mZ_mbo_pages_pages($message);

    		}
}

/**
 * The MZ Mindbody API Loader class is responsible
 * coordinating most of the actions and filters used in the plugin.
 *
 * This class maintains two internal collections - one for actions, one for
 * hooks - each of which are coordinated through external classes that
 * register the various hooks through this class. Note that the actions
 * specific to the admin sections are loaded in /lib/sections.php
 *
 * @since    2.1.0
 */
class MZ_MBO_Pages_Loader {
    /**
     * A reference to the collection of actions used throughout the plugin.
     *
     * @access protected
     * @var    array    $actions    The array of actions that are defined throughout the plugin.
     */
    protected $actions;
 
    /**
     * A reference to the collection of filters used throughout the plugin.
     *
     * @access protected
     * @var    array    $actions    The array of filters that are defined throughout the plugin.
     */
    protected $filters;
 
    /**
     * Instantiates the plugin by setting up the data structures that will
     * be used to maintain the actions and the filters.
     */
    public function __construct() {
 
        $this->actions = array();
        $this->filters = array();
 
    }
    
    /**
     * Registers the actions with WordPress and the respective objects and
     * their methods.
     *
     * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
     * @param  object    $component   The object that contains the method to be called when the hook is fired.
     * @param  string    $callback    The function that resides on the specified component.
     */ 
    public function add_action( $hook, $component, $callback ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback );
    }

    /**
     * Registers the filters with WordPress and the respective objects and
     * their methods.
     *
     * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
     * @param  object    $component   The object that contains the method to be called when the hook is fired.
     * @param  string    $callback    The function that resides on the specified component.
     */ 
    public function add_filter( $hook, $component, $callback ) {
    mz_pr($hook);
        $this->filters = $this->add( $this->filters, $hook, $component, $callback );
    }
    
    /**
     * Registers the filters with WordPress and the respective objects and
     * their methods.
     *
     * @access private
     *
     * @param  array     $hooks       The collection of existing hooks to add to the collection of hooks.
     * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
     * @param  object    $component   The object that contains the method to be called when the hook is fired.
     * @param  string    $callback    The function that resides on the specified component.
     *
     * @return array                  The collection of hooks that are registered with WordPress via this class.
     */ 
    private function add( $hooks, $hook, $component, $callback ) {
    
        $hooks[] = array(
            'hook'      => $hook,
            'component' => $component,
            'callback'  => $callback
        );
 
 				//die("messed up somehow");
        return $hooks;
 
    }
 
     /**
     * Calls the add methods for above referenced filters and actions and registers them with WordPress.
     */
    public function run() {
 		
        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        }
 
        foreach ( $this->actions as $hook ) {
        	if (($hook['callback'] == 'instantiate_mbo_API') && ($hook['component'] == 'MZ_Mindbody_Init')) {
        		add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        		//mz_pr(MZ_MBO_Instances::$instances_of_MBO);
        	}else{
            	add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
            }
        }
 
    }
 
}


class MZ_MBO_Pages {
 
    protected $loader;
 
    protected $plugin_slug;
 
    protected $version;
 
    public function __construct() {
 
        $this->plugin_slug = 'mz-mbo-pages';
        $this->version = '1.0.0';
 
        $this->load_dependencies();
        $this->define_main_hooks();
        $this->add_shortcodes();
 
    }
 
    private function load_dependencies() {
        	
		foreach ( glob( plugin_dir_path( __FILE__ )."inc/*.php" ) as $file )
			include_once $file;
	
		//Functions

		require_once MZ_MBO_PAGES_DIR .'lib/functions.php';
        	
        $this->loader = new MZ_MBO_Pages_Loader();
        
    }
 
    private function define_admin_hooks() {
 
        $admin = new MZ_MBO_Pages_Admin( $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
        $this->loader->add_action( 'add_meta_boxes', $admin, 'add_meta_box' );
    }
    
    private function define_main_hooks() {
 
        $this->loader->add_action( 'init', $this, 'myStartSession' );
        $this->loader->add_action( 'wp_logout', $this, 'myStartSession' );
        $this->loader->add_action( 'wp_login', $this, 'myEndSession' );
        
        }

    public function myStartSession() {
			if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
				  session_start();
				}
		}

    public function myEndSession() {
			session_destroy ();
		}
 
 		private function add_shortcodes() {
 	
 				$mz_mbo_pages = new MZ_MBO_Pages_Pages();
 		
        add_shortcode('mz-mbo-list-classes', array($mz_mbo_pages, 'mZ_mbo_pages_pages'));

    }
 
    public function run() {
        $this->loader->run();
    }
 
    public function get_version() {
        return $this->version;
    }
 
}

function MZ_MBO_Pages_load_textdomain() {
	load_plugin_textdomain('mz-mbo-pages',false,dirname(plugin_basename(__FILE__)) . '/languages');
	}
add_action( 'plugins_loaded', 'MZ_MBO_Pages_load_textdomain' );

function mZ_mbo_pages_activation() {
	//Don't know if there's anything we need to do here.
}

function mZ_mbo_pages_deactivation() {
	//Don't know if there's anything we need to do here.
}

//register uninstaller
register_uninstall_hook(__FILE__, 'mz-mbo-pages_uninstall');

function mZ_mbo_pages_uninstall(){
	//actions to perform once on plugin uninstall go here
	delete_option('mz_mbo_pages_options');
}

if (!function_exists( 'mZ_latest_jquery' )){
	function mZ_latest_jquery(){
		//	Use latest jQuery release
		if( !is_admin() ){
			wp_deregister_script('jquery');
			wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"), false, '');
			wp_enqueue_script('jquery');
		}
	}
	add_action('wp_enqueue_scripts', 'mZ_latest_jquery');
}


    
if ( is_admin() )
{     
	$admin_backend = new MZ_MBO_Pages_Admin('2.1.0');
	//Start Ajax Signup

}
else
{// non-admin enqueues, actions, and filters

function run_mz_mbo_pages() {
 
    $mz_mbo = new MZ_MBO_Pages();
    $mz_mbo->run();
 
}
 
run_mz_mbo_pages();
	

    
	require_once(MZ_MBO_PAGES_DIR .'lib/virtual_page_maker.php'); 
    // this code segment requires the WordPress environment

    //$vp =  new Virtual_Themed_Pages_MZoo();
    //$vp->add('#/yoga_classes/\d*#i', 'mytest_contentfunc');
   add_action('init', 'mz_mbo_virtual_page');

	function mz_mbo_virtual_page() {
    $vp = new Virtual_Themed_Pages_MZoo();
    $vp->add('#/yoga_classes/\d*#i', 'mz_mbo_virtual_page_seo');
}

    // Example of content generating function
    // Must set $this->body even if empty string
  function mz_mbo_virtual_page_seo($v, $url) {
			// START caching configuration
			$mz_single_event_cache = "mz_single_event_cache";
			
			
			$mz_date = date_i18n('Y-m-d',current_time('timestamp'));
			$mz_timeframe = array_slice(mz_getDateRange($mz_date, 14), 0, 1);
			//While we still need to support php 5.2 and can't use [0] on above
			$mz_timeframe = array_shift($mz_timeframe);
			
			$mz_cache_reset = isset($virtual_pager->mz_mbo_globals->options['mz_mindbody_clear_cache']) ? "on" : "off";

			if ( $mz_cache_reset == "on" )
			{
			delete_transient( $mz_single_event_cache );
			}

			if ( false === ( $mz_single_event_data = get_transient( $mz_single_event_cache ) ) ) {
			$mb = MZ_Mindbody_Init::instantiate_mbo_API();
			if (True) { // In case we add account later
				$mz_single_event_data = $mb->GetClasses($mz_timeframe);
			}else{
				$mb->sourceCredentials['SiteIDs'][0] = $account; 
				$mz_single_event_data = $mb->GetClasses($mz_timeframe);
			}
			
			$mz_single_event_data = $mb->GetClasses($mz_timeframe);

			//echo $mb->debug();

			//Cache the mindbody call for 24 hour2
			// TODO make cache timeout configurable.
			set_transient($mz_single_event_cache, $mz_single_event_data, 28 * 60 * 60 * 24);
			} // End if transient not set
			// END caching configuration
			// extract an id from the URL
			$id = 'none';
			if (preg_match('#(\d+)#', $url, $m))
					$id = $m[1];
			// could wp_die() if id not extracted successfully...
			$page_maker = new MZ_MBO_Pages_Pages();
			$mz_days = $page_maker->makeNumericArray($mz_single_event_data['GetClassesResult']['Classes']['Class']);
			
			$mz_sorted = $page_maker->sortClasses($mz_days, $page_maker->mz_mbo_globals->time_format, $locations=1);
			foreach ($mz_sorted as $class) {
					if ($class->sclassid != $id){
						continue;
					} else {

						$v->title = $class->className;
						$classimage = isset($class->classImage) ? $class->classImage : '';
						$staffImage = isset($class->staffImage) ? $class->staffImage : '';
						$level = $class->level;
						$staffName = $class->teacher;
						$page_body = $class->class_details;
						$v->body = $page_body;
						$v->template = 'page'; // optional
						$v->subtemplate = 'billing'; // optional
						$v->slug = $url;
						break;
					} // else
				} // Foreach $mz_schedule_data['GetClassesResult']
			} // geo_seoMagic

	
}//EOF Not Admin


?>
