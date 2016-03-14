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
    $this_plugin_dir = MZ_MBO_PAGES_DIR ;
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
        mz_pr($this_plugin_dir . '/templates/' . $template_name);
    }
    if ( $load && $located != '' ) {
        load_template( $located, $require_once );
    }
    return $located;
}

?>