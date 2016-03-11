<?php

class MZ_MBO_Pages_Pages {

	public $mz_mbo_globals;
	public $mz_timeframe;
	
	public function __construct(){
		require_once(WP_PLUGIN_DIR . '/mz-mindbody-api/' . 'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
	}

	
	public function mZ_mbo_pages_pages($message='no message', $atts=array(), $account=0) {
		$atts = shortcode_atts( array(
			'type' => 'week',
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

			//Cache the mindbody call for 24 hour2
			// TODO make cache timeout configurable.
			set_transient($mz_list_classes_cache, $mz_all_class_data, 7 * 60 * 60 * 24);
		} // End if transient not set
		// END caching configuration
		

		if(!empty($mz_all_class_data['GetClassesResult']['Classes']['Class']))
		{
			$mz_days = $this->makeNumericArray($mz_all_class_data['GetClassesResult']['Classes']['Class']);
			
			$mz_sorted = $this->sortClasses($mz_days, $this->mz_mbo_globals->time_format, $locations);
			
			$tbl = new HTML_Table('', 'mz_all_our_classes' . ' ' . ' mz-schedule-horizontal mz-schedule-display');
			$tbl->addRow('header');
			// arguments: cell content, class, type (default is 'data' for td, pass 'header' for th)
			// can include associative array of optional additional attributes

			$tbl->addCell(__('Class Name', 'mz-mindbody-api'), 'mz_classDetails', 'header', array('scope'=>'header'));
			$tbl->addCell(__('Instructor', 'mz-mindbody-api'), 'mz_staffName', 'header', array('scope'=>'header'));
			$tbl->addCell(__('Class Type', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
			//$tbl->addCell(__('Level', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
			$tbl->addTSection('tbody');
			
			//delete all previously created posts:
				 
			$args = array(
				'numberposts' => 200,
				'post_type' =>'mzclassorevent'
			);
			
			$all_yoga_classes = get_posts( $args );
			
			if (is_array($all_yoga_classes)) {
				 foreach ($all_yoga_classes as $post) {
			// what you want to do;
						 wp_delete_post( $post->ID, true);
						 echo "Deleted Post: ".$post->title."\r\n";
				 }
			}
			
			foreach($mz_sorted as $unique => $class) {   
					// Define Content:
						$classimage = isset($class->classImage) ? $class->classImage : '';
						$staffImage = isset($class->staffImage) ? $class->staffImage : '';
						$level = $class->level;
						$staffName = $class->teacher;
						$page_body = $class->class_details;
						
					// Create post object
						$yoga_class = array(
							'post_title'    => wp_strip_all_tags( $class->className . ' ' . html_entity_decode($class->teacher) ),
							'post_content'  => $page_body,
							'post_status'   => 'publish',
							'post_type' => 'yogaevent',
							'post_author'   => 1,
							'guid' => $class->sclassid,
							'comment_status' => 'closed'
						);
 
						// Insert the post into the database
						$post_id = wp_insert_post( $yoga_class );

					//if ($class->className == 'Admin') {continue;}
					// start building table rows
					$link = new html_element('a');
					$link->set('href', '/yoga-event/'.$post_id.'/');
					$link->set('text', $class->className);
					$row_css_classes = 'mz_description_holder mz_schedule_table mz_location_';
					$tbl->addRow($row_css_classes);
					$tbl->addCell($link->build());
					$tbl->addCell($class->staffName);
					$tbl->addCell($class->sessionTypeName);
					//$tbl->addCell($class->level);
					/*$v->title = $class->className;

						$v->body = $page_body;*/
						
			
			} // foreach($mz_sorted
					
		}//EOF if Not Empty Classes
		return $tbl->display();
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
			
			if ($class['Staff']['Name'] == 'Ashley Knight') {
				//mz_pr($class['ClassScheduleID']);
				//mz_pr(date_i18n('Y-m-d H:i:s', strtotime($class['StartDateTime'])));
				//echo "<hr />";
			}
			$single_event = new Single_event($class, $daynum="", $hide=array(), $locations, $hide_cancelled=0,
																		$advanced=0, $show_registrants=0, $registrants_count=0, 
																		$calendar_format='events');
																		
			$identifier = $single_event->level . '_' 
										. $single_event->sessionTypeName 
										. '_' . $single_event->staffName 
										. '_' . $single_event->className;
			
			$identifier = str_replace(' ', '_', $identifier);

			if(empty($all_classes[$identifier])) {
				$all_classes[$identifier] = $single_event;
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
