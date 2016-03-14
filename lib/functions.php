<?php
//For Testing
if ( ! function_exists( 'mZ_write_to_file' ) ) {
	function mZ_write_to_file($message, $file_path='')
	{
			$file_path = ( ($file_path == '') || !file_exists($file_path) ) ? WP_CONTENT_DIR . '/mbo_debug_log.txt' : $file_path;
			$header = date('l dS \o\f F Y h:i:s A', strtotime("now")) . " \nMessage:\t ";

			if (is_array($message)) {
					$header = "\nMessage is array.\n";
					$message = print_r($message, true);
			}
			$message .= "\n";
			file_put_contents(
					$file_path, 
					$header . $message, 
					FILE_APPEND | LOCK_EX
			);
	}
}
//Format arrays for display in development
if ( ! function_exists( 'mz_pr' ) ) {
	function mz_pr($message) {
		echo "<pre>";
		print_r($message);
		echo "</pre>";
	}
}

//BOF create yoga-event CPT
function create_mz_event_cpt() {
	
		// include the custom post type class
		require_once(MZ_MBO_PAGES_DIR . 'lib/cpt.php');
		// create a book custom post type
		$yoga_events = new CPT('yoga-event');
		// create a genre taxonomy
		$yoga_events->register_taxonomy('event-type');
		// Set has'archive to true
		$yoga_events->set('has_archive', True);
		// This may or may not be necessary and desired
		$yoga_events->set('hierarchical', False);
		// Someone says to do this for archive page reqrite to work
		$yoga_events->set('rewrite', 'yoga-event');
		// Match plugin text domain
		$yoga_events->set_textdomain('mz-mbo-pages');
		// define the columns to appear on the admin edit screen
		$yoga_events->columns(array(
				'cb' => '<input type="checkbox" />',
				'title' => __('Title'),
				'teacher' => __('Teacher'),
				'time' => __('Time'),
				'level' => __('Level'),
				'type' => __('Type')
		));
		
		// Our text domain to match plugin
		$yoga_events->set_textdomain('mz-mbo-pages');
		// make rating and price columns sortable
		$yoga_events->sortable(array(
				'teacher' => array('teacher', true),
				'time' => array('time', true),
				'type' => array('type', true)
		));
		// use "pages" icon for post type
		$yoga_events->menu_icon("dashicons-book-alt");
		//mz_pr($yoga_events);
	}
	
	add_action('plugins_loaded', 'create_mz_event_cpt');
//EOF create yoga-event CPT
 
// BOF Add our own page template
// http://wordpress.stackexchange.com/questions/88371/how-do-you-create-an-archive-for-a-custom-post-type-from-a-plugin

add_filter('archive_template', 'mbo_pages_yoga_event_template');

function mbo_pages_yoga_event_template($template) {
    global $wp_query;
    if (is_post_type_archive('yoga-event')) {
        $templates[] = 'archive-yoga-event.php';
        $template = mbo_pages_locate_plugin_template($templates);
    }
    return $template;
}

function mbo_pages_locate_plugin_template($template_names, $load = false, $require_once = true ) {
    if (!is_array($template_names)) {
        return '';
    }
    $located = '';  
    $this_plugin_dir = MZ_MBO_PAGES_DIR;
    foreach ( $template_names as $template_name ) {
        if ( !$template_name )
            continue;
        if ( file_exists(STYLESHEETPATH . '/' . $template_name)) {
            $located = STYLESHEETPATH . '/' . $template_name;
            break;
        } elseif ( file_exists(TEMPLATEPATH . '/' . $template_name) ) {
            $located = TEMPLATEPATH . '/' . $template_name;
            break;
        } elseif ( file_exists( $this_plugin_dir . '/templates/' . $template_name) ) {
            $located =  $this_plugin_dir . '/templates/' . $template_name;
            break;
        }
    }
    if ( $load && $located != '' ) {
        load_template( $located, $require_once );
    }
    return $located;
}
// EOF Add our own page template

// Include CPT in search results
function search_filter($query) {
  if ( !is_admin() && $query->is_main_query() ) {
    if ($query->is_search) {
      $query->set('post_type', array( 'post', 'yoga-event' ) );
    }
  }
}

add_action('pre_get_posts','search_filter');

// Everride posts count result for yoga-events
function list_all_yoga_events( $query ) {
    if ( is_admin() || ! $query->is_main_query() )
        return;

    if ( is_home() ) {
        // Display only 1 post for the original blog archive
        $query->set( 'posts_per_page', -1 );
        return;
    }

    if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'yoga-event' ) {
        // Display 50 posts for a custom post type called 'yoga-event'
        $query->set('posts_per_page', -1 );
				$query->set('orderby', 'meta_value');	
				$query->set('meta_key', 'type');	 
				$query->set('order', 'ASC'); 
        
        return;
    }
}
add_action( 'pre_get_posts', 'list_all_yoga_events', 1 );
?>