<?php

class MZ_MBO_Pages_Pages {

	public $mz_mbo_globals;
	
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
    $mz_timeframe = array_shift($mz_timeframe);
    $mb = MZ_Mindbody_Init::instantiate_mbo_API();
		if ($mb == 'NO_SOAP_SERVICE') {
			mz_pr($mb);
			}
		$mz_all_class_data = $mb->GetClasses($mz_timeframe);
		if(!empty($mz_all_class_data['GetClassesResult']['Classes']['Class']))
		{
			$mz_days = $this->makeNumericArray($mz_all_class_data['GetClassesResult']['Classes']['Class']);
			
			//mz_pr($mz_days);
			$mz_sorted = $this->sortClasses($mz_days, $this->mz_mbo_globals->time_format, $locations);
			
			$tbl = new HTML_Table('', 'mz_all_our_classes' . ' ' . ' mz-schedule-horizontal mz-schedule-display');
			$tbl->addRow('header');
			// arguments: cell content, class, type (default is 'data' for td, pass 'header' for th)
			// can include associative array of optional additional attributes

			$tbl->addCell(__('Class Name', 'mz-mindbody-api'), 'mz_classDetails', 'header', array('scope'=>'header'));
			$tbl->addCell(__('Instructor', 'mz-mindbody-api'), 'mz_staffName', 'header', array('scope'=>'header'));
			$tbl->addCell(__('Class Type', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
			$tbl->addCell(__('Level', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
			$tbl->addTSection('tbody');
			
			foreach($mz_sorted as $unique => $class) {   
		
					//if ($class->className == 'Admin') {continue;}

					// start building table rows
					$link = new html_element('a');
					$link->set('href', '/yoga_classes/'.$class->sclassid.'/');
					$link->set('text', $class->className);
					$row_css_classes = 'mz_description_holder mz_schedule_table mz_location_';
					$tbl->addRow($row_css_classes);
					$tbl->addCell($link->build());
					$tbl->addCell($class->teacher);
					$tbl->addCell($class->sessionTypeName);
					$tbl->addCell($class->level);
			
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
			
			$single_event = new Single_event($class, $daynum="", $hide=array(), $locations, $hide_cancelled=0,
																		$advanced=0, $show_registrants=0, $registrants_count=0, 
																		$calendar_format='horizontal');
																		
			if(empty($all_classes[$single_event->className . '_' . $single_event->teacher . '_' . $single_event->level])) {
				$all_classes[$single_event->className . '_' . $single_event->teacher . '_' . $single_event->level] = $single_event;
				} 
		}
		return $all_classes;
	}
	
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
