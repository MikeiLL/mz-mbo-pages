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
 * Description: Create class CPT and populate from MBO results for overview of classes per 2 week period.
 * Version: 		1.0.0
 * Author: 			mZoo.org
 * Author URI: 		http://www.mZoo.org/
 * Plugin URI: 		http://www.mzoo.org/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: 	mz-mindbody-api
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
    if ( is_admin() && current_user_can( 'activate_plugins' ) && !is_plugin_active( 'mz-mindbody-api/mz-mindbody.php' ) ) {
        add_action( 'admin_notices', 'child_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

if ( ! function_exists( 'child_plugin_notice' ) ) {
	function child_plugin_notice(){
			?><div class="error"><p>Sorry, but Child Plugin requires the Parent plugin, MZ Mindbody API, to be installed and active.</p></div><?php
	}
}

add_action('plugins_loaded', 'mzmbo_init');

function mzmbo_init() {
	$mZmbo = MZ_Mindbody\MZMBO();
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
        require_once MZ_MBO_PAGES_DIR .'inc/sections.php';
        }
        
    public function load_pages() {
        require_once MZ_MBO_PAGES_DIR .'inc/functions.php';
        require_once MZ_MBO_PAGES_DIR .'inc/class-mzmbo-pages.php';
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

		require_once MZ_MBO_PAGES_DIR .'inc/functions.php';
		
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
			require_once MZ_MBO_PAGES_DIR .'inc/class-mzmbo-pages.php';
			$mz_mbo_pages = new MZMBO_Pages();
			add_shortcode('mz-mbo-list-classes', array($mz_mbo_pages, 'MZMBO_Pages'));
    }
 
    public function run() {
        $this->loader->run();
    }
 
    public function get_version() {
        return $this->version;
    }
 
}

function MZ_MBO_Pages_load_textdomain() {
		load_plugin_textdomain('mz-mindbody-api',false,dirname(plugin_basename(__FILE__)) . '/languages');
	}
	
add_action( 'plugins_loaded', 'MZ_MBO_Pages_load_textdomain' );

// Cron Job to pull in class overview data semi-weekly.
// Need following when using custom times
require_once MZ_MBO_PAGES_DIR .'inc/functions.php';

register_activation_hook(__FILE__, 'mZ_mbo_pages_activation');

// Need this file for class method
require_once MZ_MBO_PAGES_DIR .'inc/class-mzmbo-pages.php';
$pages_manager = new MZMBO_Pages();
add_action('make_pages_weekly', array($pages_manager, 'MZMBO_Pages'));

function mZ_mbo_pages_activation() {
	wp_schedule_event( current_time( 'timestamp' ), 'weekly', 'make_pages_weekly');
	// Run this once upon activation to populate class overview CPT elements
	// Need this file for class method
	// Is MZ_MBO_PAGES_DIR not yet declared?
	//require_once MZ_MBO_PAGES_DIR .'inc/class-mzmbo-pages.php';
	require_once(WP_PLUGIN_DIR . '/mz-mbo-pages/inc/class-mzmbo-pages.php');
	$pages_manager = new MZMBO_Pages;
	$pages_manager->get_mbo_results();
	flush_rewrite_rules();
}

// register deactivation to clear cache
	
register_deactivation_hook(__FILE__, 'mZ_mbo_pages_deactivation');

function mZ_mbo_pages_deactivation() {
	wp_clear_scheduled_hook('make_pages_weekly');
	flush_rewrite_rules();
}

//register uninstaller
register_uninstall_hook(__FILE__, 'mz-mbo-pages_uninstall');


function mZ_mbo_pages_uninstall(){
	//actions to perform once on plugin uninstall go here
	delete_option('mz_mbo_pages_options');
}

// BOF create class Classes
// TODO can we move this to functions.php file?
function create_mz_classes_cpt() {
	
		// include the custom post type class
		require_once(MZ_MBO_PAGES_DIR . 'inc/cpt.php');
		// create a book custom post type
		$classes = new CPT('classes');
		// create a genre taxonomy
		//$classes->register_taxonomy('class_type');
		// Set has'archive to true
		$classes->set('has_archive', True);
		// Set singular and plural names
		$classes->singular = 'Class';
		$classes->plural = 'Classes';
		// This may or may not be necessary and desired
		$classes->set('hierarchical', False);
		// Someone says to do this for archive page rewrite to work
		// but i'm reading rewrite should be false to use with taxonomy URLs
		$classes->set('rewrite', False);
		
		//i'm reading rewrite should be True to use with taxonomy URLs
		$classes->set('query_var', True);
		//will this make them come up in searches of the site?
		$classes->set('public', True);
		// Match plugin text domain
		$classes->set_textdomain('mz-mbo-pages');
		// define the columns to appear on the admin edit screen
		$classes->columns(array(
				'cb' => '<input type="checkbox" />',
				'title' => __('Title'),
				'teacher' => __('Teacher'),
				'class_time' => __('Time'),
				'level' => __('Level'),
				'classes_class_type' => __('Type')
		));
		
		// Our text domain to match plugin
		$classes->set_textdomain('mz-mbo-pages');
		// make rating and price columns sortable
		$classes->sortable(array(
				'teacher' => array('teacher', true),
				'class_time' => array('time', true),
				'classes_class_type' => array('class_type', true)
		));
		// use "pages" icon for post type
		$classes->menu_icon("dashicons-book-alt");
		//mz_pr($classes);
		
	}
	
	add_action('plugins_loaded', 'create_mz_classes_cpt');
//EOF create class Classes


// BOF filter column add taxonomy

add_action( 'manage_classes_posts_custom_column', 'my_manage_classes_columns', 10, 2 );

function my_manage_classes_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		/* If displaying the 'teacher' column. */
		case 'teacher' :

			/* Get the post meta. */
			$teacher = get_post_meta( $post_id, 'teacher', true );

			/* If no teacher is found, output a default message. */
			if ( empty( $teacher ) )
				echo __( 'Unknown' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				printf( __( '%s' ), $teacher );

			break;

		/* If displaying the 'class_type' column. */
		case 'classes_class_type' :

			/* Get the class types for the post. */
			$terms = get_the_terms( $post_id, 'classes_class_type' );

			/* If terms were found. */
			if ( !empty( $terms ) ) {

				$out = array();

				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'classes_class_type' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'classes_class_type', 'display' ) )
					);
				}
				/* Join the terms, separating them with a comma. */
				//echo join( ', ', $out );
			}

			/* If no terms were found, output a default message. */
			else {
				_e( 'No Class Type' );
			}

			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}


function create_class_type_taxonomies() {
	if (!taxonomy_exists('classes_class_type')) {
		register_taxonomy(
			'classes_class_type',
			'classes',
			array(
					'labels' => array(
							'name' => 'Class Type'
					),
					'show_ui' => true,
					'show_tagcloud' => false,
					'hierarchical' => false,
					'publicly_queryable' => true,
					'query_var' => true,
					'rewrite' => true
					)
				);
			}
		if (!taxonomy_exists('classes_level')) {
		register_taxonomy(
			'classes_level',
			'classes',
			array(
					'labels' => array(
							'name' => 'Level'
					),
					'show_ui' => true,
					'show_tagcloud' => false,
					'hierarchical' => false,
					'publicly_queryable' => true,
					'query_var' => true,
					'rewrite' => true
					)
				);
			}
		if (!taxonomy_exists('classes_time')) {
		register_taxonomy(
			'classes_time',
			'classes',
			array(
					'labels' => array(
							'name' => 'Time'
					),
					'show_ui' => true,
					'show_tagcloud' => false,
					'hierarchical' => false,
					'publicly_queryable' => true,
					'query_var' => true,
					'rewrite' => true
					)
				);
			}
		global $wp_rewrite;
		
		$class_type_structure = '/class-type/%classes_class_type%';
		$wp_rewrite->add_rewrite_tag("%classes_class_type%", '([^/]+)', "classes_class_type=");
		$wp_rewrite->add_permastruct('classes_class_type', $class_type_structure, false);
		}

add_action( 'init', 'create_class_type_taxonomies', 0 );

/*
Permalink code adapted from http://shibashake.com/wordpress-theme/custom-post-type-permalinks-part-2
*/
function classes_permalink_structure($post_link, $post, $permalink, $sample)
	{

	if (strpos($permalink, '%classes_class_type%') === FALSE) return $permalink;
     
        // Get post
        $post = get_post($post_id);
        if (!$post) return $permalink;
 				
        // Get taxonomy terms
        $terms = wp_get_object_terms($post->ID, 'classes_class_type');   
        if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) $taxonomy_slug = $terms[0]->slug;
        else $taxonomy_slug = 'all-class-types';
 
    return str_replace('%classes_class_type%', $taxonomy_slug, $permalink);

	}
//add_filter('post_type_link', 'classes_permalink_structure', 10, 4);

// Add filter to plugin init function
//add_filter('post_type_link', 'classes_class_type_permalink', 10, 3);   
// Adapted from get_permalink function in wp-includes/link-template.php
function classes_class_type_permalink($permalink, $post_id, $leavename) {
    $post = get_post($post_id);
    $rewritecode = array(
        '%year%',
        '%monthnum%',
        '%day%',
        '%hour%',
        '%minute%',
        '%second%',
        $leavename? '' : '%postname%',
        '%post_id%',
        '%category%',
        '%author%',
        $leavename? '' : '%pagename%',
    );
 
    if ( '' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
        $unixtime = strtotime($post->post_date);
     
        $category = '';
        if ( strpos($permalink, '%category%') !== false ) {
            $cats = get_the_category($post->ID);
            if ( $cats ) {
                usort($cats, '_usort_terms_by_ID'); // order by ID
                $category = $cats[0]->slug;
                if ( $parent = $cats[0]->parent )
                    $category = get_category_parents($parent, false, '/', true) . $category;
            }
            // show default category in permalinks, without
            // having to assign it explicitly
            if ( empty($category) ) {
                $default_category = get_category( get_option( 'default_category' ) );
                $category = is_wp_error( $default_category ) ? '' : $default_category->slug;
            }
        }
     
        $author = '';
        if ( strpos($permalink, '%author%') !== false ) {
            $authordata = get_userdata($post->post_author);
            $author = $authordata->user_nicename;
        }
     
        $date = explode(" ",date('Y m d H i s', $unixtime));
        $rewritereplace =
        array(
            $date[0],
            $date[1],
            $date[2],
            $date[3],
            $date[4],
            $date[5],
            $post->post_name,
            $post->ID,
            $category,
            $author,
            $post->post_name,
        );
        $permalink = str_replace($rewritecode, $rewritereplace, $permalink);
    } else { // if they're not using the fancy permalink option
    }
    return $permalink;
}
// BOF filter column add taxonomy

//Add events CPT
require_once(WP_PLUGIN_DIR . '/mz-mbo-pages/inc/workshops.php');
require_once(WP_PLUGIN_DIR . '/mz-mbo-pages/inc/workshops-options.php');
    
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

	
}//EOF Not Admin


?>
