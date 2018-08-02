<?php

use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

require_once ABSPATH . '/wp-content/plugins/mz-mindbody-api/inc/common/interfaces/class-retrieve.php';
require_once ABSPATH . '/wp-content/plugins/mz-mindbody-api/inc/schedule/class-schedule-item.php';

class MZMBO_Pages extends Interfaces\Retrieve {

	public $mz_mbo_globals;
	public $mz_time_frame;
	
	/**
	 * Store Class Owners Setting
	 *
	 */
	private $store_class_owners_setting;
	
	public function __construct(){
		require_once(MZ_MBO_PAGES_DIR .'inc/html_table.class.php');
	}

	/*
	* Generate a post of CPT 'classes' for each MBO class of type 'DropIn'
	*/
	public function get_mbo_results($message='no message', $atts=array(), $account=0) {

		$atts = shortcode_atts( array(
			'locations' => ''
				), $atts );
		
		$locations = $atts['locations'];
		    
		$mb = $this->instantiate_mbo_API();
		$mz_all_class_data = $mb->GetClasses($this->time_frame());
		if ($mb == 'NO_SOAP_SERVICE') {
			mz_pr($mb);
		}

		if(!empty($mz_all_class_data['GetClassesResult']['Classes']['Class']))
		{
			// We do have classes, so make a numeric array from them
			$mz_days = $this->makeNumericArray($mz_all_class_data['GetClassesResult']['Classes']['Class']);
			
			// Create our sorted array of Class objects
			$mz_sorted = $this->sortClasses($mz_days, 'g:i a', $locations);
				 
			$args = array(
				'numberposts' => -1,
				//'nopaging' => true,
				'post_type' =>'classes'
			);
			
			// Retrieve already created posts, if any
			$all_yoga_classes = get_posts( $args );
						
			if (is_array($all_yoga_classes) && count($all_yoga_classes) >= 1) {
				// If there are already class posts, filter and update
				foreach ($all_yoga_classes as $key => $post) {
					// Compare each item returned from WPDB to MBO results
					$this->update_class_posts($mz_sorted);
				 }
			} else {
				// No Class Posts have been populated yet so just create them
				$this->update_class_posts($mz_sorted);
			}
			
			if (is_array($all_yoga_classes) && count($all_yoga_classes) >= 1) {
				foreach ($all_yoga_classes as $key => $post) {
					// Now we'll clear out the rest of the WPDB CPT items
					// But ONLY for DropIns. We can leave Events in there for posterity
					if ($post->scheduleType == 'DropIn')
						wp_delete_post( $post->ID, true);
				}
			}

			$this->create_class_posts($mz_sorted);
				
		}//EO90F if Not Empty Classes

	} // EOF get_mbo_results
	
	/**
	 * Return Time Frame for request to MBO API
	 *
	 * @since 2.4.7
	 *
	 * Default time_frame is two dates, start of current week as set in WP, and seven days from "now.
     *
     * @throws \Exception
	 *
	 * @return array or start and end dates as required for MBO API
	 */
	public function time_frame($timestamp = null){
	    $timestamp = isset($timestamp) ? $timestamp : current_time( 'timestamp' );
		$start_time = new \Datetime( date_i18n('Y-m-d', $timestamp) );
		$end_time = new \Datetime( date_i18n('Y-m-d', $timestamp) );
		$di = new \DateInterval('P2W');
		$end_time->add($di);

		return array('StartDateTime'=> $start_time->format('Y-m-d'), 'EndDateTime'=> $end_time->format('Y-m-d'));
	}

	
	public function makeNumericArray($data) {
		return (isset($data[0])) ? $data : array($data);
	}
	
	/**
	 * Sort Classes
	 *
	 * Create an array of Single_Event objects, sorted by time of day.
	 */
	public function sortClasses($mz_classes = array(), $time_format = "g:i a", $locations=1) {
	
		$mz_classesByDate = array();
	
		if(!is_array($locations)):
			$locations = array($locations);
		endif;
		
		$count = 0;
		$all_classes = array();

		foreach($mz_classes as $class)
		{
			
			$single_event = new MZ_Mindbody\Inc\Schedule\Schedule_Item($class, array('mbo_pages_call' => 1));

			// Build a string to identify this as a unique class
			$identifier = $single_event->level . '_' 
										. $single_event->sessionTypeName 
										. '_' . $single_event->staffName 
										. '_' . $single_event->className;
			
			// Replace any additional spaces with underscores.
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
	
	/**
	* Update Posts for each unique class
	*/
	private function update_class_posts($classes){
		foreach($classes as $unique => $class) {  
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
				if ($message != 'no message')
					mz_pr($yoga_class);
				// If title already exists just update the content in WPDB
				$post_id = wp_update_post( $yoga_class );
				if ($class->scheduleType == 'DropIn') {
					add_post_meta( $post_id, 'title', $class->className );
					add_post_meta( $post_id, 'teacher', $class->staffName );
					add_post_meta( $post_id, 'time', date_i18n( 'g:i a', strtotime($schedule_item['StartDateTime']) ) );
					add_post_meta( $post_id, 'classes_class_type', $class->sessionTypeName );
					add_post_meta( $post_id, 'level', $class->level );
					wp_insert_term( $class->sessionTypeName, 'classes_class_type');
					wp_set_post_terms( $post_id, $class->sessionTypeName, 'classes_class_type');
					}
				wp_update_post( $yoga_class );
				// Remove this item from the WPDB array
				unset($all_yoga_classes[$key]);
				// Remove this item from the MBO result collection
				unset($classes[$unique]);
			endif;
		}
	}
	
	/**
	* Create Posts for each unique class
	*/
	private function create_class_posts($classes){
		foreach($classes as $unique => $class) { 
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
				
				if ($class->scheduleType == 'DropIn'):
					foreach($class->non_specified_class_times as $class_time) {
						$schedule_day_times->addRow();
						$schedule_day_times->addCell($class_time);
					}
				else: {
					//assume enrollment and add specific event time.
					$schedule_day_times->addRow();
					$schedule_day_times->addCell($class->startDateTime . ' - ' . $class->endDateTime);
					}
				endif;
				$page_body .= $schedule_day_times->display();
			// Create post object
			if ($class->scheduleType == 'DropIn'):
				$post_type = 'classes';
			else:
				$post_type = 'workshops';
			endif;
				$yoga_class = array(
					'post_title'    => wp_strip_all_tags( utf8_encode($class->className) . ' ' . utf8_encode($class->teacher) ),
					'post_content'  => $page_body,
					'post_status'   => 'publish',
					'post_type' => $post_type,
					'post_author'   => 1,
					'comment_status' => 'closed'
				);
				if ($message != 'no message')
					mz_pr($yoga_class);
				// Insert the post into the database
				$post_id = wp_insert_post( $yoga_class );
				if ($class->scheduleType == 'DropIn') {
					add_post_meta( $post_id, 'teacher', $class->staffName );
					add_post_meta( $post_id, 'time', date_i18n( 'g:i a', strtotime($schedule_item['StartDateTime']) ) );
					add_post_meta( $post_id, 'classes_class_type', $class->sessionTypeName );
					add_post_meta( $post_id, 'level', $class->level );
					wp_insert_term( $class->sessionTypeName, 'classes_class_type');
					wp_set_post_terms( $post_id, $class->sessionTypeName, 'classes_class_type');
				} else {
				//for now assume it's 'enrollment' aka workshop
					add_post_meta( $post_id, 'mz_pages_workshops_start_date', $class->startTimeStamp);
					add_post_meta( $post_id, 'mz_pages_workshops_end_date', $class->endTimeStamp);
				}
			} // foreach($mz_sorted
	}
	
} // EOF MZMBO_Pages Class

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
