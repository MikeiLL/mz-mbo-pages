<?php
/**
 * This file contains all the actions and functions to create the admin dashboard sections
 *
 * This file contains all the actions and functions to create the admin dashboard sections.
 * It should probably be refactored to use oop approach at least for the sake of consistency.
 *
 * @since 1.0.0
 *
 * @package MZMBOPAGES
 * 
 */
 
add_action ('admin_menu', 'mz_mbo_pages_settings_menu');

	function mz_mbo_pages_settings_menu() {
		//create submenu under Settings
		add_options_page ('MZ MBO Sandbox Settings', esc_attr__('MBO Sandbox Interface', 'mz_mbo_pages'),
		'manage_options', __FILE__, 'mz_mbo_pages_settings_page');
	}

	function mz_mbo_pages_settings_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<form action="options.php" method="post">
				<?php settings_fields('mz_mbo_pages_options'); ?>
				<?php do_settings_sections('mz_mbo_pages'); ?>
				<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</form>
		</div>
		<?php
	}

	// Register and define the settings
	add_action('admin_init', 'mz_mbo_pages_admin_init');

	function mz_mbo_pages_admin_init(){
		register_setting(
			'mz_mbo_pages_options',
			'mz_mbo_pages_options',
			'mz_mbo_pages_validate_options'
		);
		
		add_settings_section(
			'mz_mbo_pages',
			'MZ MBO Sandbox',
			'mz_mbo_pages_main',
			'mz_mbo_pages'
		);
		
		add_settings_field(
			'mz_mbo_pages_logfile_path',
			__('Path to a log File: ', 'mz_mbo_pages'),
			'mz_mbo_pages_logfile_path',
			'mz_mbo_pages',
			'mz_mbo_pages'
		);
		
		add_settings_field(
			'mz_mbo_pages_olg_api_calls',
			__('Log API calls ', 'mz_mbo_pages'),
			'mz_mbo_pages_log_api_calls',
			'mz_mbo_pages',
			'mz_mbo_pages'
		);

	}


	function mz_mbo_pages_main() {
		echo '<p>Content Directory Path: '. WP_CONTENT_DIR . '</p>';
	}	
	
	// Display and fill the form field
	function mz_mbo_pages_logfile_path() {
		// get option 'string' value from the database
		$options = get_option( 'mz_mbo_pages_options',__('Option Not Set', 'mz-mbo-pages') );
		$mz_mbo_pages_logfile_path = (isset($options['mz_mbo_pages_logfile_path'])) ? $options['mz_mbo_pages_logfile_path'] : __('Path to log file', 'mz-mbo-pages');

		// echo the field
		echo "<input id='mz_mbo_pages_options' name='mz_mbo_pages_options[mz_mbo_pages_logfile_path]' type='text' value='$mz_mbo_pages_logfile_path' />";
	}
	
	// Display and fill the form field
	function mz_mbo_pages_log_api_calls() {
				$options = get_option( 'mz_mbo_pages_options','' );
		printf(
	    '<input id="%1$s" name="mz_mbo_pages_options[%1$s]" type="checkbox" %2$s />',
	    'mz_mbo_pages_log_api_calls',
	    checked( isset($options['mz_mbo_pages_log_api_calls']) , true, false )
			);
		}
	
	
	// Validate user input (we want text only)
	function mz_mbo_pages_validate_options( $input ) {
	    foreach ($input as $key => $value)
	    {
				$valid[$key] = wp_strip_all_tags(preg_replace( '/\s/', '', $input[$key] ));
				if( $valid[$key] != $input[$key] )
				{
					add_settings_error(
						'mz_mbo_pages_text_string',
						'mz_mbo_pages_texterror',
						'Does not appear to be valid ',
						'error'
					);
				}
			}

		return $valid;
	}
?>