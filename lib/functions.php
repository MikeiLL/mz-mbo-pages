<?php
 
// BOF Add our own templates
// http://wordpress.stackexchange.com/questions/88371/how-do-you-create-an-archive-for-a-custom-post-type-from-a-plugin

add_filter('archive_template', 'mbo_pages_class_archive_template');
add_filter('single_template', 'mbo_pages_single_class_template');

function mbo_pages_class_archive_template($template) {
    global $wp_query;
    if (is_post_type_archive('classes')) {
        $templates[] = 'archive-classes.php';
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

/* Filter the single_template with our custom function*/

function mbo_pages_single_class_template($single) {
    global $wp_query, $post;

		/* Checks for single template by post type */
		if ($post->post_type == "classes"){
        $templates[] = 'single-class.php';
        $template = mbo_pages_locate_plugin_template($templates);
    } else if ($post->post_type == "workshops"){
        $templates[] = 'single-workshops.php';
        $template = mbo_pages_locate_plugin_template($templates);
    } else if ($post->post_type != 'post') {
        $templates[] = 'single' . str_replace(' ', '-', $post->post_type) . '.php';
        $template = mbo_pages_locate_plugin_template($templates);
    } else {
        $templates[] = 'single.php';
        $template = mbo_pages_locate_plugin_template($templates);
    }
    return $template;
}
// EOF Add our own templates


// Include CPT in search results
function search_filter($query) {
  if ( !is_admin() && $query->is_main_query() ) {
    if ($query->is_search) {
      $query->set('post_type', array( 'post', 'classes' ) );
    }
  }
}

add_action('pre_get_posts','search_filter');

// Override posts count result for classs
function list_all_classes( $query ) {
    if ( is_admin() || ! $query->is_main_query() )
        return;

    if ( is_home() ) {
        // Display only 1 post for the original blog archive
        $query->set( 'posts_per_page', -1 );
        return;
    }

    if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'classes' ) {
        // Display all posts for a custom post type called 'classes'
        $query->set('posts_per_page', -1 );
				$query->set('orderby', 'meta_value');	
				$query->set('meta_key', 'type');	 
				$query->set('order', 'ASC'); 
        
        return;
    }
}
add_action( 'pre_get_posts', 'list_all_classes', 1 );

//BOF Cron Jobs

function add_new_intervals($schedules) 
{
	// add weekly and monthly intervals
	$schedules['weekly'] = array(
		'interval' => 604800,
		'display' => __('Once Weekly')
	);
	
	$schedules['semiweekly'] = array(
		'interval' => 1209600,
		'display' => __('Every Two Weeks')
	);

	$schedules['monthly'] = array(
		'interval' => 2635200,
		'display' => __('Once a month')
	);
	
	$schedules['every_three_minutes'] = array(
            'interval'  => 180,
            'display'   => __( 'Every 3 Minutes', 'mz-mbo-pages' )
    );

	return $schedules;
}
add_filter( 'cron_schedules', 'add_new_intervals');

	/**
	 * Convert markdown to HTML using Jetpack
	 * @param  string $content Markdown content
	 * @return string          Converted content
	 */
	function mz_pages_process_jetpack_markdown( $content ) {

		// If markdown class is defined, convert content
		if ( class_exists( 'WPCom_Markdown' ) ) {

			// Get markdown library
			jetpack_require_lib( 'markdown' );

			// Return converted content
			return WPCom_Markdown::get_instance()->transform( $content );

		}

		// Else, return content
		return $content;

	}

	/**
	 * Get saved markdown content if it exists and Jetpack is active. Otherwise, get HTML.
	 * @param  array  $options  Array with HTML and markdown content
	 * @param  string $name     The name of the content
	 * @param  string $suffix   The suffix to denote the markdown version of the content
	 * @return string           The content
	 */
	function mz_pages_get_jetpack_markdown( $options, $name, $suffix = '_markdown' ) {

		// If markdown class is defined, get markdown content
		if ( class_exists( 'WPCom_Markdown' ) && array_key_exists( $name . $suffix, $options ) && !empty( $options[$name . $suffix] ) ) {
			return $options[$name . $suffix];
		}

		// Else, return HTML
		return $options[$name];

	}


	/**
	 * Get saved markdown content if it exists and Jetpack is active. Otherwise, get HTML.
	 * @param  array  $options  Array with HTML and markdown content
	 * @param  string $name     The name of the content
	 * @param  string $suffix   The suffix to denote the markdown version of the content
	 * @return string           The content
	 */	
	function mz_delete_all_posts ($post_type) {
		$mycustomposts = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => '-1') );
		$count = 0;
		 foreach( $mycustomposts as $mypost ) {
			 // Delete's each post.
			 wp_delete_post( $mypost->ID, true);
			 $count++;
			// Set to False if you want to send them to Trash.
		 }
		 if($count >= 1):
		 	echo "Deleted " . $count . ' ' . $post_type . '.';
		 else:
		 	echo "No posts deleted.";
		 	mz_pr($mycustomposts);
		 endif;
	}
?>