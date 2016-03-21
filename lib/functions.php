<?php
 
// BOF Add our own templates
// http://wordpress.stackexchange.com/questions/88371/how-do-you-create-an-archive-for-a-custom-post-type-from-a-plugin

add_filter('archive_template', 'mbo_pages_yoga_event_archive_template');
add_filter('single_template', 'mbo_pages_single_event_template');

function mbo_pages_yoga_event_archive_template($template) {
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

/* Filter the single_template with our custom function*/

function mbo_pages_single_event_template($single) {
    global $wp_query, $post;

/* Checks for single template by post type */
if ($post->post_type == "yoga-event"){
        $templates[] = 'single-yoga-event.php';
        $template = mbo_pages_locate_plugin_template($templates);
    }
    else { return mbo_pages_locate_plugin_template(array('single.php')); }
    return $template;
}
// EOF Add our own templates

// Include CPT in search results
function search_filter($query) {
  if ( !is_admin() && $query->is_main_query() ) {
    if ($query->is_search) {
      $query->set('post_type', array( 'post', 'yoga-event' ) );
    }
  }
}

add_action('pre_get_posts','search_filter');

// Override posts count result for yoga-events
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



?>