<?php

class MZ_MBO_Pages_Pages {

	public $mz_mbo_globals;
	public $mz_timeframe;
	
	public function __construct(){
		require_once(WP_PLUGIN_DIR . '/mz-mindbody-api/' . 'inc/mz_mbo_init.inc');
		require_once(WP_PLUGIN_DIR . '/mz-mindbody-api/' . 'lib/schedule_objects.php');
		require_once(WP_PLUGIN_DIR . '/mz-mindbody-api/' . 'lib/html_table.class.php');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
	}

	
	public function mZ_mbo_pages_pages($message='no message', $atts=array(), $account=0) {
		
		$atts = shortcode_atts( array(
			'locations' => ''
				), $atts );
		
		$locations = $atts['locations'];
		
    //$options = get_option( 'mz_mbo_pages_options','' );
    $mz_date = date_i18n('Y-m-d',current_time('timestamp'));
    $mz_timeframe = array_slice(mz_getDateRange($mz_date, 14), 0, 1);
    $this->mz_timeframe = array_shift($mz_timeframe);
    
    // START caching configuration
		$mz_list_classes_cache = "mz_list_classes_cache";
		
		$mz_cache_reset = isset($this->mz_mbo_globals->options['mz_mindbody_clear_cache']) ? "on" : "off";

		if ( $mz_cache_reset == "on" )
		{
			delete_transient( $mz_list_classes_cache );
		}
		
		if ( false === ( $mz_all_class_data = get_transient( $mz_list_classes_cache ) ) ) {
			$mb = MZ_Mindbody_Init::instantiate_mbo_API();
			if ($account == 0) {
				$mz_all_class_data = $mb->GetClasses($this->mz_timeframe);
			}else{
				$mb->sourceCredentials['SiteIDs'][0] = $account; 
				$mz_all_class_data = $mb->GetClasses($this->mz_timeframe);
			}
			
			if ($mb == 'NO_SOAP_SERVICE') {
				mz_pr($mb);
			}
			//echo $mb->debug();

			//Cache the mindbody call for one week
			// TODO make cache timeout configurable.
			set_transient($mz_list_classes_cache, $mz_all_class_data, 7 * 60 * 60 * 24);
		} // End if transient not set
		// END caching configuration

		if(!empty($mz_all_class_data['GetClassesResult']['Classes']['Class']))
		{
			$mz_days = $this->makeNumericArray($mz_all_class_data['GetClassesResult']['Classes']['Class']);
			
			$mz_sorted = $this->sortClasses($mz_days, $this->mz_mbo_globals->time_format, $locations);
				 
			$args = array(
				'numberposts' => -1,
				//'nopaging' => true,
				'post_type' =>'yoga-event'
			);
			
			$all_yoga_classes = get_posts( $args );

			if (is_array($all_yoga_classes) && count($all_yoga_classes) >= 1) {
				// If there are already yoga-event posts, filter and update
				foreach ($all_yoga_classes as $key => $post) {
					// Compare each item returned from WPDB to MBO results
					foreach($mz_sorted as $unique => $class) {  
						// Define Content to update (only) the description:
							$page_body = $class->class_details;
							$schedule_day_times = new HTML_Table('schedule_listing');
							$schedule_day_times->addRow('header');
							$schedule_day_times->addCell('');
							foreach($class->non_specified_class_times as $class_time) {
								$schedule_day_times->addRow();
								$schedule_day_times->addCell($class_time);
							}
							$page_body .= $schedule_day_times->display();
							$my_mbo_title = wp_strip_all_tags( $class->className . ' ' . utf8_encode($class->teacher));
							if ($post->post_title == $my_mbo_title) :
								$yoga_class = array(
									'ID' => $post->ID,
									'post_content'  => $page_body
								);
								mz_pr($yoga_class);
								// If title already exists just update the content in WPDB
								$post_id = wp_update_post( $yoga_class );
								add_post_meta( $post_id, 'title', $class->className );
								add_post_meta( $post_id, 'teacher', $class->staffName );
								add_post_meta( $post_id, 'time', $class->startTime );
								add_post_meta( $post_id, 'type', $class->sessionTypeName );
								add_post_meta( $post_id, 'level', $class->level );
								wp_update_post( $yoga_class );
								// Remove this item from the WPDB array
								unset($all_yoga_classes[$key]);
								// Remove this item from the MBO result collection
								unset($mz_sorted[$unique]);
							endif;
						}
				 }
			} 
			
			if (is_array($all_yoga_classes) && count($all_yoga_classes) >= 1) {
				foreach ($all_yoga_classes as $key => $post) {
					// Now we'll clear out the rest of the WPDB CPT items
					wp_delete_post( $post->ID, true);
				}
			}

			foreach($mz_sorted as $unique => $class) { 
				// Create new CPT items for the rest of results from MBO not filtered by above update  
					// TODO Remove this which is URU specific
						if ($class->className == 'Admin') {continue;}
					// Define Content:
						$classimage = isset($class->classImage) ? $class->classImage : '';
						$staffImage = isset($class->staffImage) ? $class->staffImage : '';
						$level = $class->level;
						$staffName = $class->teacher;
						$page_body = $class->class_details;
						$schedule_day_times = new HTML_Table('schedule_listing');
						$schedule_day_times->addRow('header');
						$schedule_day_times->addCell('');
						foreach($class->non_specified_class_times as $class_time) {
							$schedule_day_times->addRow();
							$schedule_day_times->addCell($class_time);
						}
						$page_body .= $schedule_day_times->display();
					// Create post object
						$yoga_class = array(
							'post_title'    => wp_strip_all_tags( utf8_encode($class->className) . ' ' . utf8_encode($class->teacher) ),
							'post_content'  => $page_body,
							'post_status'   => 'publish',
							'post_type' => 'yoga-event',
							'post_author'   => 1,
							'comment_status' => 'closed'
						);
						mz_pr($yoga_class);
						// Insert the post into the database
						$post_id = wp_insert_post( $yoga_class );
						add_post_meta( $post_id, 'teacher', $class->staffName );
						add_post_meta( $post_id, 'time', $class->startTime );
						add_post_meta( $post_id, 'type', $class->sessionTypeName );
						add_post_meta( $post_id, 'level', $class->level );
			} // foreach($mz_sorted
				
		}//EO90F if Not Empty Classes
		//List Post Types
		//mZ_write_to_file('Updated pages at: ' . time());

	} // EOF mZ_mbo_pages_pages
	
	public function makeNumericArray($data) {
		return (isset($data[0])) ? $data : array($data);
	}
	
	public function sortClasses($mz_classes = array(), $time_format = "g:i a", $locations=1) {
	
		$mz_classesByDate = array();
	
		if(!is_array($locations)):
			$locations = array($locations);
		endif;
		
		$count = 0;
		$all_classes = array();

		foreach($mz_classes as $class)
		{
			
			$single_event = new Single_event($class, $daynum="", $hide=array(), $locations, $hide_cancelled=0,
																		$advanced=0, $show_registrants=0, $registrants_count=0, 
																		$calendar_format='overview');
																		
			$identifier = $single_event->level . '_' 
										. $single_event->sessionTypeName 
										. '_' . $single_event->staffName 
										. '_' . $single_event->className;
			
			$identifier = str_replace(' ', '_', $identifier);

			if(empty($all_classes[$identifier])) {
				$all_classes[$identifier] = $single_event;
				} else {
				$non_specific_class_time = date_i18n('l g:i a', strtotime($class['StartDateTime'])) . ' - ' .
																	 date_i18n('g:i a', strtotime($class['EndDateTime'])) . '&nbsp;' .
																	 '<span class="schedule_location">(' . $class['Location']['Name'] . ')</span>';
				if(is_array($all_classes[$identifier]->non_specified_class_times) && !in_array($non_specific_class_time, $all_classes[$identifier]->non_specified_class_times))
					array_push($all_classes[$identifier]->non_specified_class_times, $non_specific_class_time);
				}
		}
		ksort($all_classes);
		
		return $all_classes;
	} // EOF Sort Classes
	
} // EOF MZ_MBO_Pages_Pages Class

if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}
?>
