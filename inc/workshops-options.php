<?php

/**
 * Events Options
 */


	/**
	 * Theme Options Fields
	 * Each option field requires its own uniquely named function. Select options and radio buttons also require an additional uniquely named function with an array of option choices.
	 */

	// Labels

	function mz_pages_workshops_settings_field_labels_register() {
		$options = mz_pages_workshops_get_theme_options();
		?>
		<input type="text" name="mz_pages_workshops_theme_options[labels_register]" id="labels_register" value="<?php echo esc_attr( $options['labels_register'] ); ?>" />
		<label class="description" for="labels_register"><?php _e( 'Register Button Label (default: Register)', 'mz-mindbody-api' ); ?></label>
		<?php
	}

	// Hide Calendar/Map

	function mz_pages_workshops_settings_field_hide_google_calendar() {
		$options = mz_pages_workshops_get_theme_options();
		?>
		<label for="hide_google_calendar">
			<input type="checkbox" name="mz_pages_workshops_theme_options[hide_google_calendar]" id="hide_google_calendar" <?php checked( 'on', $options['hide_google_calendar'] ); ?> />
			<?php _e( 'Hide Google Calendar', 'mz-mindbody-api' ); ?>
		</label>
		<?php
	}

	function mz_pages_workshops_settings_field_hide_ical_invite() {
		$options = mz_pages_workshops_get_theme_options();
		?>
		<label for="hide_ical_invite">
			<input type="checkbox" name="mz_pages_workshops_theme_options[hide_ical_invite]" id="hide_ical_invite" <?php checked( 'on', $options['hide_ical_invite'] ); ?> />
			<?php _e( 'Hide iCal Invite', 'mz-mindbody-api' ); ?>
		</label>
		<?php
	}

	function mz_pages_workshops_settings_field_hide_google_maps() {
		$options = mz_pages_workshops_get_theme_options();
		?>
		<label for="hide_google_maps">
			<input type="checkbox" name="mz_pages_workshops_theme_options[hide_google_maps]" id="hide_google_maps" <?php checked( 'on', $options['hide_google_maps'] ); ?> />
			<?php _e( 'Hide Google Maps', 'mz-mindbody-api' ); ?>
		</label>
		<?php
	}

	// Page

	function mz_pages_workshops_settings_field_page_slug() {
		$options = mz_pages_workshops_get_theme_options();
		?>
		<input type="text" name="mz_pages_workshops_theme_options[page_slug]" id="page_slug" value="<?php echo esc_attr( $options['page_slug'] ); ?>" />
		<label class="description" for="page_slug">
			<?php _e( 'The URL path for your events page (ex. if you put <code>events</code>, your upcoming events would be displayed at <code>yourwebsite.com/events/upcoming</code> and your past events would be displayed at <code>yourwebsite.com/events/past</code>', 'mz-mindbody-api' ); ?> <strong><?php _e( 'Note:', 'mz-mindbody-api' ); ?></strong> <em><?php _e( 'It this doesn\'t work, try saving your options again. It\'s absurd, but it works.', 'mz-mindbody-api' ); ?></em>
		</label>
		<?php
	}

	function mz_pages_workshops_settings_field_page_title_upcoming() {
		$options = mz_pages_workshops_get_theme_options();
		?>
		<input type="text" name="mz_pages_workshops_theme_options[page_title_upcoming]" id="page_title_upcoming" value="<?php echo esc_attr( $options['page_title_upcoming'] ); ?>" />
		<label class="description" for="page_title_upcoming">
			<?php _e( 'Title to display on the page where all of your upcoming events are listed', 'mz-mindbody-api' ); ?></em>
		</label>
		<?php
	}

	function mz_pages_workshops_settings_field_page_content_upcoming() {
		$options = mz_pages_workshops_get_theme_options();
		?>
		<?php wp_editor(
			stripslashes( mz_pages_get_jetpack_markdown( $options, 'page_content_upcoming' ) ),
			'page_content_upcoming',
			array(
				'wpautop' => false,
				'textarea_name' => 'mz_pages_workshops_theme_options[page_content_upcoming]',
				'textarea_rows' => 4,
			)
		); ?>
		<label class="description" for="page_content_upcoming"><?php _e( 'Content to display at the top of the page where all of your upcoming events are listed', 'mz-mindbody-api' ); ?></label>
		<?php
	}

	function mz_pages_workshops_settings_field_page_title_past() {
		$options = mz_pages_workshops_get_theme_options();
		?>
		<input type="text" name="mz_pages_workshops_theme_options[page_title_past]" id="page_title_past" value="<?php echo esc_attr( $options['page_title_past'] ); ?>" />
		<label class="description" for="page_title_past">
			<?php _e( 'Title to display on the page where all of your past events are listed', 'mz-mindbody-api' ); ?></em>
		</label>
		<?php
	}

	function mz_pages_workshops_settings_field_page_content_past() {
		$options = mz_pages_workshops_get_theme_options();
		?>
		<?php wp_editor(
			stripslashes( mz_pages_get_jetpack_markdown( $options, 'page_content_past' ) ),
			'page_content_past',
			array(
				'wpautop' => false,
				'textarea_name' => 'mz_pages_workshops_theme_options[page_content_past]',
				'textarea_rows' => 4,
			)
		); ?>
		<label class="description" for="page_content"><?php _e( 'Content to display at the top of the page where all of your past events are listed', 'mz-mindbody-api' ); ?></label>
		<?php
	}



	/**
	 * Theme Option Defaults & Sanitization
	 * Each option field requires a default value under mz_pages_workshops_get_theme_options(), and an if statement under mz_pages_workshops_theme_options_validate();
	 */

	// Get the current options from the database.
	// If none are specified, use these defaults.
	function mz_pages_workshops_get_theme_options() {
		$saved = (array) get_option( 'mz_pages_workshops_theme_options' );
		$defaults = array(
			'labels_register' => 'Register',
			'hide_google_calendar' => 'off',
			'hide_ical_invite' => 'off',
			'hide_google_maps' => 'off',
			'page_slug' => 'workshops',
			'page_title_upcoming' => 'Upcoming Workshops',
			'page_content_upcoming' => '',
			'page_content_upcoming_markdown' => '',
			'page_title_past' => 'Past Workshops',
			'page_content_past' => '',
			'page_content_past_markdown' => '',
		);

		$defaults = apply_filters( 'mz_pages_workshops_default_theme_options', $defaults );

		$options = wp_parse_args( $saved, $defaults );
		$options = array_intersect_key( $options, $defaults );

		return $options;
	}

	// Sanitize and validate updated theme options
	function mz_pages_workshops_theme_options_validate( $input ) {
		$output = array();

		// Labels

		if ( isset( $input['labels_register'] ) && ! empty( $input['labels_register'] ) )
			$output['labels_register'] = wp_filter_nohtml_kses( $input['labels_register'] );

		// Hide calendar/maps

		if ( isset( $input['hide_google_calendar'] ) )
			$output['hide_google_calendar'] = 'on';

		if ( isset( $input['hide_ical_invite'] ) )
			$output['hide_ical_invite'] = 'on';

		if ( isset( $input['hide_google_maps'] ) )
			$output['hide_google_maps'] = 'on';

		// Page content

		if ( isset( $input['page_slug'] ) && ! empty( $input['page_slug'] ) )
			$output['page_slug'] = wp_filter_nohtml_kses( $input['page_slug'] );

		if ( isset( $input['page_title_upcoming'] ) && ! empty( $input['page_title_upcoming'] ) )
			$output['page_title_upcoming'] = wp_filter_nohtml_kses( $input['page_title_upcoming'] );

		if ( isset( $input['page_content_upcoming'] ) && ! empty( $input['page_content_upcoming'] ) ) {
			$output['page_content_upcoming'] = mz_mbo_pages_process_jetpack_markdown( wp_filter_post_kses( $input['page_content_upcoming'] ) );
			$output['page_content_upcoming_markdown'] = wp_filter_post_kses( $input['page_content_upcoming'] );
		}

		if ( isset( $input['page_title_past'] ) && ! empty( $input['page_title_past'] ) )
			$output['page_title_past'] = wp_filter_nohtml_kses( $input['page_title_past'] );

		if ( isset( $input['page_content_past'] ) && ! empty( $input['page_content_past'] ) ) {
			$output['page_content_past'] = mz_mbo_pages_process_jetpack_markdown( wp_filter_post_kses( $input['page_content_past'] ) );
			$output['page_content_past_markdown'] = wp_filter_post_kses( $input['page_content_past'] );
		}

		return apply_filters( 'mz_pages_workshops_theme_options_validate', $output, $input );
	}



	/**
	 * Theme Options Menu
	 * Each option field requires its own add_settings_field function.
	 */

	// Create theme options menu
	// The content that's rendered on the menu page.
	function mz_pages_workshops_theme_options_render_page() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Workshops Options', 'mz-mindbody-api' ); ?></h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'mz_pages_workshops_options' );
					do_settings_sections( 'events_options' );
					wp_nonce_field( 'mz_pages_workshops_update_options_nonce', 'mz_pages_workshops_update_options_process' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	// Register the theme options page and its fields
	function mz_pages_workshops_theme_options_init() {

		// Register a setting and its sanitization callback
		// register_setting( $option_group, $option_name, $sanitize_callback );
		// $option_group - A settings group name.
		// $option_name - The name of an option to sanitize and save.
		// $sanitize_callback - A callback function that sanitizes the option's value.
		register_setting( 'mz_pages_workshops_options', 'mz_pages_workshops_theme_options', 'mz_pages_workshops_theme_options_validate' );


		// Register our settings field group
		// add_settings_section( $id, $title, $callback, $page );
		// $id - Unique identifier for the settings section
		// $title - Section title
		// $callback - // Section callback (we don't want anything)
		// $page - // Menu slug, used to uniquely identify the page. See mz_pages_workshops_theme_options_add_page().
		add_settings_section( 'labels', __( 'Labels', 'mz-mindbody-api' ),  '__return_false', 'events_options' );
		add_settings_section( 'hides', __( 'Hide Calendar and Maps', 'mz-mindbody-api' ),  '__return_false', 'events_options' );
		add_settings_section( 'page_content', __( 'Page Content', 'mz-mindbody-api' ),  '__return_false', 'events_options' );


		// Register our individual settings fields
		// add_settings_field( $id, $title, $callback, $page, $section );
		// $id - Unique identifier for the field.
		// $title - Setting field title.
		// $callback - Function that creates the field (from the Theme Option Fields section).
		// $page - The menu page on which to display this field.
		// $section - The section of the settings page in which to show the field.
		add_settings_field( 'labels_register', __( 'Register', 'mz-mindbody-api' ), 'mz_pages_workshops_settings_field_labels_register', 'events_options', 'labels' );

		add_settings_field( 'hide_google_calendar', __( 'Google Calendar', 'mz-mindbody-api' ), 'mz_pages_workshops_settings_field_hide_google_calendar', 'events_options', 'hides' );
		add_settings_field( 'hide_ical_invite', __( 'iCal Invite', 'mz-mindbody-api' ), 'mz_pages_workshops_settings_field_hide_ical_invite', 'events_options', 'hides' );
		add_settings_field( 'hide_google_maps', __( 'Google Maps', 'mz-mindbody-api' ), 'mz_pages_workshops_settings_field_hide_google_maps', 'events_options', 'hides' );

		add_settings_field( 'page_slug', __( 'URL Path', 'mz-mindbody-api' ), 'mz_pages_workshops_settings_field_page_slug', 'events_options', 'page_content' );
		add_settings_field( 'page_title_upcoming', __( 'Page Title Upcoming', 'mz-mindbody-api' ), 'mz_pages_workshops_settings_field_page_title_upcoming', 'events_options', 'page_content' );
		add_settings_field( 'page_content_upcoming', __( 'Page Content Upcoming', 'mz-mindbody-api' ), 'mz_pages_workshops_settings_field_page_content_upcoming', 'events_options', 'page_content' );
		add_settings_field( 'page_title_past', __( 'Page Title Past', 'mz-mindbody-api' ), 'mz_pages_workshops_settings_field_page_title_past', 'events_options', 'page_content' );
		add_settings_field( 'page_content_past', __( 'Page Content Past', 'mz-mindbody-api' ), 'mz_pages_workshops_settings_field_page_content_past', 'events_options', 'page_content' );
	}
	add_action( 'admin_init', 'mz_pages_workshops_theme_options_init' );

	// Add the theme options page to the admin menu
	// Use add_theme_page() to add under Appearance tab (default).
	// Use add_menu_page() to add as it's own tab.
	// Use add_submenu_page() to add to another tab.
	function mz_pages_workshops_theme_options_add_page() {

		// add_theme_page( $page_title, $menu_title, $capability, $menu_slug, $function );
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function );
		// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		// $page_title - Name of page
		// $menu_title - Label in menu
		// $capability - Capability required
		// $menu_slug - Used to uniquely identify the page
		// $function - Function that renders the options page
		// $theme_page = add_theme_page( __( 'Theme Options', 'mz-mindbody-api' ), __( 'Theme Options', 'mz-mindbody-api' ), 'edit_theme_options', 'events_options', 'mz_pages_workshops_theme_options_render_page' );

		// $theme_page = add_menu_page( __( 'Theme Options', 'mz-mindbody-api' ), __( 'Theme Options', 'mz-mindbody-api' ), 'edit_theme_options', 'events_options', 'mz_pages_workshops_theme_options_render_page' );
		$theme_page = add_submenu_page( 'edit.php?post_type=workshops', __( 'Options', 'mz-mindbody-api' ), __( 'Options', 'mz-mindbody-api' ), 'edit_theme_options', 'events_options', 'mz_pages_workshops_theme_options_render_page' );
	}
	add_action( 'admin_menu', 'mz_pages_workshops_theme_options_add_page' );



	// Restrict access to the theme options page to admins
	function mz_pages_workshops_option_page_capability( $capability ) {
		return 'edit_theme_options';
	}
	add_filter( 'option_page_capability_mz_pages_workshops_options', 'mz_pages_workshops_option_page_capability' );



	// Update events URL
	function mz_pages_workshops_refresh_slug() {
		if ( isset( $_POST['mz_pages_workshops_update_options_process'] ) ) {
			if ( wp_verify_nonce( $_POST['mz_pages_workshops_update_options_process'], 'mz_pages_workshops_update_options_nonce' ) ) {
				flush_rewrite_rules();
			}
		}
	}
	add_action( 'init', 'mz_pages_workshops_refresh_slug' );