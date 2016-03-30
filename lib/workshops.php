<?php

/**
 * Add an events custom post type
 */


	// Load required files
	require_once( dirname( __FILE__) . '/workshops-options.php' );

// BOF create class Workshops
// TODO can we move this to functions.php file?
function create_mz_workshops_cpt() {
	
		// include the custom post type class
		require_once(MZ_MBO_PAGES_DIR . 'lib/cpt.php');
		// create a book custom post type
		$classes = new CPT('workshops');
		// create a genre taxonomy
		$classes->register_taxonomy('event-type');
		// Set has'archive to true
		$classes->set('has_archive', True);
		// Set singular and plural names
		$classes->singular = 'Workshop';
		$classes->plural = 'Workshops';
		// This may or may not be necessary and desired
		$classes->set('hierarchical', False);
		// Someone says to do this for archive page reqrite to work
		$classes->set('rewrite', 'workshops');
		// Match plugin text domain
		$classes->set_textdomain('mz-mbo-pages');
		// define the columns to appear on the admin edit screen
		/*$classes->columns(array(
				'cb' => '<input type="checkbox" />',
				'title' => __('Title'),
				'teacher' => __('Teacher'),
				'time' => __('Time'),
				'level' => __('Level'),
				'type' => __('Type')
		));*/
		
		// Our text domain to match plugin
		$classes->set_textdomain('mz-mbo-pages');
		// make rating and price columns sortable
		/*$classes->sortable(array(
				'teacher' => array('teacher', true),
				'time' => array('time', true),
				'type' => array('type', true)
		));*/
		// use "pages" icon for post type
		$classes->menu_icon("dashicons-calendar-alt");
		//mz_pr($classes);
	}
	
	add_action('plugins_loaded', 'create_mz_workshops_cpt');
//EOF create class Workshops

	/**
	 * Create the metabox
	 */
	function mz_pages_workshops_create_events_metabox() {
		add_meta_box( 'mz_pages_workshops_metabox', 'Workshop Details', 'mz_pages_workshops_render_metabox', 'workshops', 'normal', 'default');
	}
	add_action( 'add_meta_boxes', 'mz_pages_workshops_create_events_metabox' );



	/**
	 * Create the metabox default values
	 */
	function mz_pages_workshops_metabox_defaults() {
		return array(
			'time_start_hour' => '',
			'time_start_minutes' => '',
			'time_start_ampm' => '',
			'time_end_hour' => '',
			'time_end_minutes' => '',
			'time_end_ampm' => '',
			'date_timezone' => '',
			'location_venue' => '',
			'location_venue_url' => '',
			'location_address' => '',
			'location_city' => '',
			'location_state' => '',
			'location_zip' => '',
			'location_country' => '',
			'hide_google_calendar' => 'off',
			'hide_ical_invite' => 'off',
			'hide_google_maps' => 'off',
			'register_url' => '',
			'register_text' => '',
		);
	}



	/**
	 * Render the metabox
	 */
	function mz_pages_workshops_render_metabox() {

		// Variables
		global $post;
		$options = mz_pages_workshops_get_theme_options(); // Events options
		$saved = get_post_meta( $post->ID, 'mz_pages_workshops_details', true );
		$defaults = mz_pages_workshops_metabox_defaults();
		$details = wp_parse_args( $saved, $defaults );
		$start_date = get_post_meta( $post->ID, 'mz_pages_workshops_start_date', true );
		$end_date = get_post_meta( $post->ID, 'mz_pages_workshops_end_date', true );

		?>

			<h3>Date and Time</h3>

			<!-- Start Date -->
			<div>
				<label for="mz_pages_workshops_date_start"><?php _e( 'Start Date', 'mz-mindbody-api' ); ?></label>
				<input type="text" class="medium-text workshops-datepicker" id="mz_pages_workshops_date_start" name="mz_pages_workshops_date_start" value="<?php echo ( empty( $start_date ) ? esc_attr( date( 'm/d/Y', current_time( 'timestamp' ) ) ) : esc_attr( date( 'm/d/Y', $start_date ) ) ); ?>" placeholder="MM/DD/YYYY">

				@

				<label class="screen-reader-text" for="mz_pages_workshops_time_start_hour"><?php _e( 'Start Time Hour', 'mz-mindbody-api' ); ?></label>
				<select id="mz_pages_workshops_time_start_hour" name="mz_pages_workshops[time_start_hour]">
					<option <?php selected( $details['time_start_hour'], '' ); ?> value="">HH</option>
					<?php
						foreach ( range( 1, 12 ) as $num ) :
					?>
						<option <?php selected( $details['time_start_hour'], $num ); if ( empty( $details['time_start_hour'] ) && $num === 8 ) { echo 'selected'; } ?> value="<?php echo esc_attr( $num ); ?>"><?php echo esc_html( $num ); ?></option>
					<?php endforeach; ?>
				</select>

				<label class="screen-reader-text" for="mz_pages_workshops_time_start_minutes"><?php _e( 'Start Time Minutes', 'mz-mindbody-api' ); ?></label>
				<select id="mz_pages_workshops_time_start_minutes" name="mz_pages_workshops[time_start_minutes]">
					<option <?php selected( $details['time_start_minutes'], '00' ); selected( $details['time_start_minutes'], '' ); ?> value="00">00</option>
					<option <?php selected( $details['time_start_minutes'], '15' ); ?> value="15">15</option>
					<option <?php selected( $details['time_start_minutes'], '30' ); ?> value="30">30</option>
					<option <?php selected( $details['time_start_minutes'], '45' ); ?> value="45">45</option>
				</select>

				<label class="screen-reader-text" for="mz_pages_workshops_time_start_ampm"><?php _e( 'Start Time AM/PM', 'mz-mindbody-api' ); ?></label>
				<select id="mz_pages_workshops_time_start_ampm" name="mz_pages_workshops[time_start_ampm]">
					<option <?php selected( $details['time_start_ampm'], 'am' ); selected( $details['time_start_ampm'], '' ); ?> value="am">am</option>
					<option <?php selected( $details['time_start_ampm'], 'pm' ); ?> value="pm">pm</option>
				</select>
			</div>
			<br>

			<!-- End Date -->
			<div>
				<label for="mz_pages_workshops_date_end"><?php _e( 'End Date', 'mz-mindbody-api' ); ?></label>
				<input type="text" class="medium-text workshops-datepicker" id="mz_pages_workshops_date_end" name="mz_pages_workshops_date_end" value="<?php echo ( empty( $end_date ) ? '' : esc_attr( date( 'm/d/Y', $end_date ) ) ); ?>" placeholder="MM/DD/YYYY">

				@

				<label class="screen-reader-text" for="mz_pages_workshops_time_end_hour"><?php _e( 'End Time Hour', 'mz-mindbody-api' ); ?></label>
				<select id="mz_pages_workshops_time_end_hour" name="mz_pages_workshops[time_end_hour]">
					<option <?php selected( $details['time_end_hour'], '' ); ?> value="">HH</option>
					<?php
						foreach ( range( 1, 12 ) as $num ) :
					?>
						<option <?php selected( $details['time_start_hour'], $num ); if ( empty( $details['time_end_hour'] ) && $num === 5 ) { echo 'selected'; } ?> value="<?php echo esc_attr( $num ); ?>"><?php echo esc_html( $num ); ?></option>
					<?php endforeach; ?>
				</select>

				<label class="screen-reader-text" for="mz_pages_workshops_time_end_minutes"><?php _e( 'End Time Minutes', 'mz-mindbody-api' ); ?></label>
				<select id="mz_pages_workshops_time_end_minutes" name="mz_pages_workshops[time_end_minutes]">
					<option <?php selected( $details['time_start_minutes'], '00' ); selected( $details['time_end_minutes'], '' ); ?> value="00">00</option>
					<option <?php selected( $details['time_start_minutes'], '15' ); ?> value="15">15</option>
					<option <?php selected( $details['time_start_minutes'], '30' ); ?> value="30">30</option>
					<option <?php selected( $details['time_start_minutes'], '45' ); ?> value="45">45</option>
				</select>

				<label class="screen-reader-text" for="mz_pages_workshops_time_end_ampm"><?php _e( 'End Time AM/PM', 'mz-mindbody-api' ); ?></label>
				<select id="mz_pages_workshops_time_end_ampm" name="mz_pages_workshops[time_end_ampm]">
					<option <?php selected( $details['time_start_ampm'], 'am' ); ?> value="am">am</option>
					<option <?php selected( $details['time_start_ampm'], 'pm' ); selected( $details['time_end_ampm'], '' ); ?> value="pm">pm</option>
				</select>
			</div>
			<br>

			<!-- Timezone -->
			<div>
				<label for="mz_pages_workshops_date_timezone"><?php _e( 'Timezone', 'mz-mindbody-api' ); ?></label>
				<select id="mz_pages_workshops_date_timezone" name="mz_pages_workshops[date_timezone]">
					<option <?php selected( $details['date_timezone'], '' ); ?> value="">WordPress Default</option>
					<?php
						$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
						foreach ( $timezones as $timezone ) :
					?>
						<option <?php selected( $details['date_timezone'], $timezone ); ?> value="<?php echo esc_attr( $timezone ); ?>"><?php echo esc_html( $timezone ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<h3>Location</h3>

			<div>
				<label for="mz_pages_workshops_location_venue"><?php _e( 'Venue', 'mz-mindbody-api' ); ?></label>
				<input type="text" class="large-text" id="mz_pages_workshops_location_venue" name="mz_pages_workshops[location_venue]" value="<?php echo esc_attr( $details['location_venue'] ); ?>" placeholder="Awesome Event Location">
			</div>
			<br>

			<div>
				<label for="mz_pages_workshops_location_venue_url"><?php _e( 'Venue Website', 'mz-mindbody-api' ); ?></label>
				<input type="url" class="large-text" id="mz_pages_workshops_location_venue_url" name="mz_pages_workshops[location_venue_url]" value="<?php echo esc_attr( $details['location_venue_url'] ); ?>" placeholder="http://example.com">
			</div>
			<br>

			<div>
				<label for="mz_pages_workshops_location_address"><?php _e( 'Address', 'mz-mindbody-api' ); ?></label>
				<input type="text" class="large-text" id="mz_pages_workshops_location_address" name="mz_pages_workshops[location_address]" value="<?php echo esc_attr( $details['location_address'] ); ?>" placeholder="123 Somewhere Street">
			</div>
			<br>

			<div>
				<label for="mz_pages_workshops_location_city"><?php _e( 'City', 'mz-mindbody-api' ); ?></label>
				<input type="text" class="large-text" id="mz_pages_workshops_location_city" name="mz_pages_workshops[location_city]" value="<?php echo esc_attr( $details['location_city'] ); ?>" placeholder="Boston">
			</div>
			<br>

			<div>
				<label for="mz_pages_workshops_location_state"><?php _e( 'State', 'mz-mindbody-api' ); ?></label>
				<input type="text" class="large-text" id="mz_pages_workshops_location_state" name="mz_pages_workshops[location_state]" value="<?php echo esc_attr( $details['location_state'] ); ?>" placeholder="MA">
			</div>
			<br>

			<div>
				<label for="mz_pages_workshops_location_zip"><?php _e( 'Zipcode', 'mz-mindbody-api' ); ?></label>
				<input type="text" class="large-text" id="mz_pages_workshops_location_zip" name="mz_pages_workshops[location_zip]" value="<?php echo esc_attr( $details['location_zip'] ); ?>" placeholder="12345">
			</div>
			<br>

			<div>
				<label for="mz_pages_workshops_"><?php _e( 'Country', 'mz-mindbody-api' ); ?></label>
				<input type="text" class="large-text" id="mz_pages_workshops_location_country" name="mz_pages_workshops[location_country]" value="<?php echo esc_attr( $details['location_country'] ); ?>" placeholder="United States">
			</div>
			<br>

			<div>
				<label for="mz_pages_workshops_hide_google_calendar">
					<input type="checkbox" id="mz_pages_workshops_hide_google_calendar" name="mz_pages_workshops[hide_google_calendar]" <?php checked( 'on', $details['hide_google_calendar'] ); ?> value="on">
					<?php _e( 'Hide Google Calendar link on just this event', 'mz-mindbody-api' ); ?>
				</label>
				<br>
				<label for="mz_pages_workshops_hide_ical_invite">
					<input type="checkbox" id="mz_pages_workshops_hide_ical_invite" name="mz_pages_workshops[hide_ical_invite]" <?php checked( 'on', $details['hide_ical_invite'] ); ?> value="on">
					<?php _e( 'Hide iCal invite on just this event', 'mz-mindbody-api' ); ?>
				</label>
				<br>
				<label for="mz_pages_workshops_hide_google_maps">
					<input type="checkbox" id="mz_pages_workshops_hide_google_maps" name="mz_pages_workshops[hide_google_maps]" <?php checked( 'on', $details['hide_google_maps'] ); ?> value="on">
					<?php _e( 'Hide Google Maps on just this event', 'mz-mindbody-api' ); ?>
				</label>
				<br>
			</div>

			<h3>Event Registration</h3>

			<div>
				<label for="mz_pages_workshops_"><?php _e( 'Registration Website', 'mz-mindbody-api' ); ?></label>
				<input type="url" class="large-text" id="mz_pages_workshops_register_url" name="mz_pages_workshops[register_url]" value="<?php echo esc_attr( $details['register_url'] ); ?>" placeholder="http://example.com">
			</div>
			<br>

			<div>
				<label for="mz_pages_workshops_"><?php _e( 'Registration Text', 'mz-mindbody-api' ); ?> (<?php _e( 'default' ); ?>: <?php echo esc_html( $options['labels_register'] ); ?>)</label>
				<input type="text" class="large-text" id="mz_pages_workshops_register_text" name="mz_pages_workshops[register_text]" value="<?php echo esc_attr( $details['register_text'] ); ?>">
			</div>
			<br>

			<script type="text/javascript">
				jQuery(document).ready(function(){
					// jQuery( '.workshops-datepicker' ).datetimepicker({
					// 	startDate: new Date(),
					// 	timeFormat: 'h:mm tt',
					// 	ampm: true
					// });
					jQuery( '.workshops-datepicker' ).datepicker();
				});
			</script>
		<?php

		// Security field
		wp_nonce_field( 'workshops-metabox-nonce', 'workshops-metabox-process' );

	}



	/**
	 * Save the metabox
	 * @param  Number $post_id The post ID
	 * @param  Array  $post    The post data
	 */
	function mz_pages_workshops_save_metabox( $post_id, $post ) {

		if ( !isset( $_POST['workshops-metabox-process'] ) ) return;

		// Verify data came from edit screen
		if ( !wp_verify_nonce( $_POST['workshops-metabox-process'], 'workshops-metabox-nonce' ) ) {
			return $post->ID;
		}

		// Verify user has permission to edit post
		if ( !current_user_can( 'edit_post', $post->ID )) {
			return $post->ID;
		}

		// Check that events details are being passed along
		if ( !isset( $_POST['mz_pages_workshops'] ) ) {
			return $post->ID;
		}

		// Sanitize all data
		$sanitized = array();
		foreach ( $_POST['mz_pages_workshops'] as $key => $detail ) {
			// if ( in_array( $key, array( 'date_start', 'date_end' ) ) ) {
			// 	$sanitized[$key] = strtotime( $detail );
			// 	continue;
			// }
			$sanitized[$key] = wp_filter_nohtml_kses( $detail );
		}

		// Update data in database
		$timezone = ( empty( $details['date_timezone'] ) ? get_option('timezone_string') : $details['date_timezone'] );
		update_post_meta( $post->ID, 'mz_pages_workshops_details', $sanitized );
		update_post_meta( $post->ID, 'mz_pages_workshops_start_date', strtotime( $_POST['mz_pages_workshops_date_start'] ) );
		update_post_meta( $post->ID, 'mz_pages_workshops_end_date', strtotime( $_POST['mz_pages_workshops_date_end'] ) );

	}
	add_action('save_post', 'mz_pages_workshops_save_metabox', 1, 2);



	/**
	 * Save events data to revisions
	 * @param  Number $post_id The post ID
	 */
	function mz_pages_workshops_save_revisions( $post_id ) {

		// Check if it's a revision
		$parent_id = wp_is_post_revision( $post_id );

		// If is revision
		if ( $parent_id ) {

			// Get the data
			$parent = get_post( $parent_id );
			$event_start_date = get_post_meta( $parent->ID, 'mz_pages_workshops_start_date', true );
			$event_end_date = get_post_meta( $parent->ID, 'mz_pages_workshops_end_date', true );
			$event_details = get_post_meta( $parent->ID, 'mz_pages_workshops_details', true );

			// If data exists, add to revision
			if ( !empty( $event_start_date ) ) {
				add_metadata( 'post', $post_id, 'mz_pages_workshops_start_date', $event_start_date );
			}

			if ( !empty( $event_end_date ) ) {
				add_metadata( 'post', $post_id, 'mz_pages_workshops_end_date', $event_end_date );
			}

			if ( !empty( $event_details ) && is_array( $event_details ) ) {
				$event_defaults = mz_pages_workshops_metabox_defaults();
				foreach ( $event_defaults as $key => $value ) {
					if ( array_key_exists( $key, $event_details ) ) {
						add_metadata( 'post', $post_id, 'mz_pages_workshops_details_' . $key, $event_details[$key] );
					}
				}
			}

		}

	}
	add_action( 'save_post', 'mz_pages_workshops_save_revisions' );



	/**
	 * Restore events data with post revisions
	 * @param  Number $post_id     The post ID
	 * @param  Number $revision_id The revision ID
	 */
	function mz_pages_workshops_restore_revisions( $post_id, $revision_id ) {

		// Variables
		$post = get_post( $post_id );
		$revision = get_post( $revision_id );
		$event_start_date = get_metadata( 'post', $revision->ID, 'mz_pages_workshops_start_date', true );
		$event_end_date = get_metadata( 'post', $revision->ID, 'mz_pages_workshops_end_date', true );
		$event_defaults = mz_pages_workshops_metabox_defaults();
		$event_details = array();

		// Update content
		if ( !empty( $event_start_date ) ) {
			update_post_meta( $post_id, 'mz_pages_workshops_start_date', $event_start_date );
		}
		if ( !empty( $event_end_date ) ) {
			update_post_meta( $post_id, 'mz_pages_workshops_end_date', $event_end_date );
		}
		foreach ( $event_defaults as $key => $value ) {
			$event_detail_revision = get_metadata( 'post', $revision->ID, 'mz_pages_workshops_details_' . $key, true );
			if ( isset( $event_detail_revision ) ) {
				$event_details[$key] = $event_detail_revision;
			}
		}
		update_post_meta( $post_id, 'mz_pages_workshops_details', $event_details );

	}
	add_action( 'wp_restore_post_revision', 'mz_pages_workshops_restore_revisions', 10, 2 );



	/**
	 * Get the data to display on the revisions page
	 * @param  Array $fields The fields
	 * @return Array The fields
	 */
	function mz_pages_workshops_get_revisions_fields( $fields ) {
		$event_defaults = mz_pages_workshops_metabox_defaults();
		$fields['mz_pages_workshops_start_date'] = 'Event Start Date';
		$fields['mz_pages_workshops_end_date'] = 'Event End Date';
		foreach ( $event_defaults as $key => $value ) {
			$fields['mz_pages_workshops_details_' . $key] = ucfirst( $key );
		}
		return $fields;
	}
	add_filter( '_wp_post_revision_fields', 'mz_pages_workshops_get_revisions_fields' );



	/**
	 * Display the data on the revisions page
	 * @param  String|Array $value The field value
	 * @param  Array        $field The field
	 */
	function mz_pages_workshops_display_revisions_fields( $value, $field ) {
		global $revision;
		return get_metadata( 'post', $revision->ID, $field, true );
	}
	add_filter( '_wp_post_revision_field_my_meta', 'mz_pages_workshops_display_revisions_fields', 10, 2 );



	/**
	 * Load jQuery date-picker
	 */
	function mz_pages_workshops_load_cpt_events_scripts() {
		global $post_type;
		if( 'workshops' !== $post_type ) return;
		wp_enqueue_script( 'jquery-ui-datepicker' );
		// wp_enqueue_script( 'jquery-ui-timepicker', '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ) );
		wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
		// wp_enqueue_style( 'jquery-ui-timepickerpicker-style' , '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.css');
	}
	add_action( 'admin_enqueue_scripts', 'mz_pages_workshops_load_cpt_events_scripts', 10, 1 );



	/**
	 * Exclude past or upcoming events from events query
	 * @param Array $query The database query
	 */
	function mz_pages_workshops_filter_query( $query ) {

		if ( is_admin() || !isset( $query->query['post_type'] ) || $query->query['post_type'] !== 'workshops' || !isset( $query->query['date'] )  || !$query->is_main_query() ) return $query;

		//Get original meta query
		$meta_query = $query->get('meta_query');

		// if filtering by past events
		if ( $query->query['date'] === 'past' ) {
			//Add our meta query to the original meta queries
			$meta_query[] = array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key' => 'mz_pages_workshops_start_date',
						'value' => strtotime( 'today', current_time( 'timestamp' ) ),
						'compare' => '<',
						'type' => 'NUMERIC',
					),
					array(
						'key' => 'mz_pages_workshops_end_date',
						'value' => strtotime( 'today', current_time( 'timestamp' ) ),
						'compare' => '<',
						'type' => 'NUMERIC',
					),
				),
				array(
					'key' => 'mz_pages_workshops_start_date',
					'value' => array(''),
					'compare' => 'NOT IN',
				),
			);
			$query->set( 'meta_query', $meta_query );
			$query->set('meta_key', 'mz_pages_workshops_start_date');
			$query->set('orderby', 'meta_value_num');
			$query->set('order', 'DESC');
		}

		// If filtering by upcoming events
		if ( $query->query['date'] === 'upcoming' ) {
			//Add our meta query to the original meta queries
			$meta_query[] = array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key' => 'mz_pages_workshops_start_date',
						'value' => strtotime( 'today', current_time( 'timestamp' ) ),
						'compare' => '>=',
						'type' => 'NUMERIC',
					),
					array(
						'key' => 'mz_pages_workshops_end_date',
						'value' => strtotime( 'today', current_time( 'timestamp' ) ),
						'compare' => '>=',
						'type' => 'NUMERIC',
					),
				),
				array(
					'key' => 'mz_pages_workshops_start_date',
					'value' => array(''),
					'compare' => 'NOT IN',
				),
			);
			$query->set( 'meta_query', $meta_query );
			$query->set('meta_key', 'mz_pages_workshops_start_date');
			$query->set('orderby', 'meta_value_num');
			$query->set('order', 'ASC');
		}

	}
	add_action( 'pre_get_posts', 'mz_pages_workshops_filter_query' );



	/**
	 * Add rewrite rules
	 */
	function mz_pages_workshops_add_rewrite_rules() {
		$options = mz_pages_workshops_get_theme_options();
		add_rewrite_rule('^' . $options['page_slug'] . '/past/?$', 'index.php?post_type=workshops&date=past', 'top');
		add_rewrite_rule('^' . $options['page_slug'] . '/past/page/([0-9]{1,})/?$', 'index.php?post_type=workshops&date=past&paged=$matches[1]', 'top');
		add_rewrite_rule('^' . $options['page_slug'] . '/upcoming/?$', 'index.php?post_type=workshops&date=upcoming', 'top');
		add_rewrite_rule('^' . $options['page_slug'] . '/upcoming/page/([0-9]{1,})/?$', 'index.php?post_type=workshops&date=upcoming&paged=$matches[1]', 'top');
	}
	add_action( 'init', 'mz_pages_workshops_add_rewrite_rules' );



	/**
	 * Add custom query variable
	 * @param  Array $vars Existing query variables
	 */
	function mz_pages_workshops_events_add_query_vars( $vars ){
		$vars[] = 'date';
		$vars[] = 'ical';
		return $vars;
	}
	add_filter( 'query_vars', 'mz_pages_workshops_events_add_query_vars' );



	/**
	 * Redirect "all events" page to upcoming events
	 */
	function keel_redirect_all_events_page() {

		// Only run for events
		if ( !is_post_type_archive( 'workshops' ) ) return;

		// Variables
		$date = get_query_var( 'date' );
		$options = mz_pages_workshops_get_theme_options();

		// If date specified, don't redirect
		if ( !empty( $date ) ) return;

		// Redirect to "upcoming events"
		wp_safe_redirect( site_url() . '/' . $options['page_slug'] . '/upcoming', '301' );
		exit;

	}
	add_action( 'template_redirect', 'keel_redirect_all_events_page' );



	/**
	 * Convert multiple time strings into a single timestamp
	 * @param  string $day     Day timestamp
	 * @param  string $hour    Hour
	 * @param  string $minutes Minutes
	 * @param  string $ampm    am/pm
	 * @return string          timestamp
	 */
	function mz_pages_workshops_strings_to_timestamp( $day, $hour, $mins, $ampm, $timezone ) {
		$day = date( 'F j, Y', $day );
		if ( empty( $hour ) ) { $hour = '12'; }
		if ( empty( $mins ) ) { $mins = '00'; }
		if ( empty( $ampm ) ) { $ampm = 'am'; }
		return strtotime( $day . ' ' . $hour . ':' . $mins . $ampm . ' ' . $timezone );
	}


	/**
	 * Convert timestamps into calendar format
	 * @param String $timestamp The timestamp to convert
	 * @link http://stackoverflow.com/a/6589203
	 * @link https://gist.github.com/jakebellacera/635416
	 */
	function mz_pages_workshops_timestamp_to_calendar( $timestamp = null ) {

	    if ( empty( $timestamp) ) {
	        $timestamp = current_time( 'timestamp' );
	    }

	    return date('Ymd\THis\Z', $timestamp);

	    $date = date( 'Ymd\TH:i:s', $timestamp );
	}



	/**
	 * Convert multiple time strings into a single timestamp
	 * @param  string $day     Day timestamp
	 * @param  string $hour    Hour
	 * @param  string $minutes Minutes
	 * @param  string $ampm    am/pm
	 * @return string          Calendar format timestamp
	 */
	function mz_pages_workshops_string_to_calendar( $day, $hour, $minutes, $ampm, $timezone, $offset = 0 ) {
		$timestamp = mz_pages_workshops_strings_to_timestamp( $day, $hour, $minutes, $ampm, $timezone );
		$timestamp = $timestamp + $offset;
		return mz_pages_workshops_timestamp_to_calendar( $timestamp );
	}



	/**
	 * Escape strings for inclusion in a .ics file
	 * @param  String $string The string to escape
	 * @return String         The escaped string
	 * @link https://gist.github.com/jakebellacera/635416
	 */
	function mz_pages_workshops_escape_string( $string ) {
		return preg_replace('/([\,;])/','\\\$1', $string);
	}



	/**
	 * Generate an .ics file on-the-fly
	 * @link https://github.com/moderntribe/the-events-calendar
	 * @license GNU https://github.com/moderntribe/the-events-calendar/blob/release/122/license.txt
	 */
	function mz_pages_workshops_generate_ical_invite() {

		if ( !get_query_var( 'ical' ) ) return;

		// Variables
		$event_id = get_query_var( 'ical' );
		$event = get_post( $event_id );
		$start_date = get_post_meta( $event_id, 'mz_pages_workshops_start_date', true ); // Event start date
		$end_date = get_post_meta( $event_id, 'mz_pages_workshops_end_date', true ); // Event end date
		$details = get_post_meta( $event_id, 'mz_pages_workshops_details', true ); // Details for this event

		// Checks
		$has_end_date = !empty( $end_date );
		$has_end_time = !empty( $details['time_end_hour'] ) && !empty( $details['time_end_minutes'] ) && !empty( $details['time_end_ampm'] );

		// Strings
		$timezone = ( empty( $details['date_timezone'] ) ? get_option('timezone_string') : $details['date_timezone'] );
		$calendar_timestamp_start = mz_pages_workshops_string_to_calendar( $start_date, $details['time_start_hour'], $details['time_start_minutes'], $details['time_start_ampm'], $timezone );
		$calendar_timestamp_end = ( $has_end_date && $has_end_time ? mz_pages_workshops_string_to_calendar( $end_date, $details['time_end_hour'], $details['time_end_minutes'], $details['time_end_ampm'], $timezone ) : mz_pages_workshops_string_to_calendar( $start_date, $details['time_start_hour'], $details['time_start_minutes'], $details['time_start_ampm'], $timezone, 3600 ) );
		$calendar_location =
			( empty( $details['location_address'] ) && !empty( $details['location_venue'] ) ? $details['location_venue'] . ', ' : '' ) .
			( !empty( $details['location_address'] ) ? $details['location_address'] . ', ' : '' ).
			( !empty( $details['location_city'] ) ? $details['location_city'] . ', ' : '' ) .
			( !empty( $details['location_state'] ) ? $details['location_state'] . ' ' : '' ) .
			( !empty( $details['location_zip'] ) ? $details['location_zip'] . ' ' : '' ) .
			$details['location_country'];

		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename=invite_' . str_replace( ' ', '_', mz_pages_workshops_escape_string( esc_html( $event->post_title ) ) ) . '_' . esc_html( $event_id ) . '.ics');
		$output  = "BEGIN:VCALENDAR\r\n";
		$output .= "VERSION:2.0\r\n";
		$output .= "PRODID:-//" . get_bloginfo( 'name' ) . " - ECPv4.0.5//NONSGML v1.0//EN\r\n";
		$output .= "CALSCALE:GREGORIAN\r\n";
		$output .= "METHOD:PUBLISH\r\n";
		$output .= "X-WR-CALNAME:" . get_bloginfo( 'name' ) . "\r\n";
		$output .= "X-ORIGINAL-URL:" . site_url() . "\r\n";
		$output .= "X-WR-CALDESC:" . get_bloginfo( 'name' ) ." Events\r\n";
		$output .= "BEGIN:VEVENT\r\n";
		$output .= 'DTSTART;TZID="' . esc_html( $timezone ) . '":' . esc_html( $calendar_timestamp_start ) . "\r\n";
		$output .= 'DTEND;TZID="' . esc_html( $timezone ) . '":' . esc_html( $calendar_timestamp_end ) . "\r\n";
		$output .= "DTSTAMP:" . esc_html( current_time( 'timestamp' ) ) . "\r\n";
		$output .= "CREATED:" . esc_html( current_time( 'timestamp' ) ) . "\r\n";
		$output .= "LAST-MODIFIED:" . esc_html( current_time( 'timestamp' ) ) . "\r\n";
		$output .= "UID:" . esc_html( $event_id ) . "_"  . esc_html( $calendar_timestamp_start ) . "_" . esc_html( $calendar_timestamp_end ) . "@" . esc_html( parse_url( site_url( '/' ), PHP_URL_HOST ) ) . "\r\n";
		$output .= "SUMMARY:" . mz_pages_workshops_escape_string( esc_html( $event->post_title ) ) . "\r\n";
		$output .= "LOCATION:" . mz_pages_workshops_escape_string( esc_html( $calendar_location ) ) . "\r\n";
		$output .= "DESCRIPTION:\r\n";
		$output .= "URL:" . mz_pages_workshops_escape_string( get_permalink( $event_id ) ) . "\r\n";
		$output .= "END:VEVENT\r\n";
		$output .= "END:VCALENDAR";
		echo $output;
		die();
	}
	add_action( 'init', 'mz_pages_workshops_generate_ical_invite' );