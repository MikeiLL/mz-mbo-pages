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
		add_options_page ('MZ MBO Pages Settings', esc_attr__('MBO Pages Interface', 'mz_mbo_pages'),
		'manage_options', __FILE__, 'mz_mbo_pages_settings_page');
	}

function mz_mbo_pages_settings_page() {

  // This function creates the output for the admin page.
  // It also checks the value of the $_POST variable to see whether
  // there has been a form submission. 

  // The check_admin_referer is a WordPress function that does some security
  // checking and is recommended good practice.

  // General check for user permissions.
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient pilchards to access this page.')    );
  }

  // Start building the page

  echo '<div class="wrap">';

  echo '<h2>Update/Reset Classes & Workshops</h2>';

  // Check whether the button has been pressed AND also check the nonce
  if (isset($_POST['test_button']) && check_admin_referer('test_button_clicked')) {
    // the button has been pressed AND we've passed the security check
    test_button_action();
  }

  echo '<form action="options-general.php?page='.__FILE__.'" method="post">';

  // this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
  wp_nonce_field('test_button_clicked');
  echo '<input type="hidden" value="true" name="test_button" />';
  submit_button('Reset All Classes & Workshops');
  echo '</form>';

  echo '</div>';

}

function test_button_action()
{
  echo '<div id="message" class="updated fade"><p>'
    .'Resetting All Classes & Workshops' . '</p></div>';
    
	require_once( MZ_MBO_PAGES_DIR .'lib/pages_class.php' );
	require_once( MZ_MBO_PAGES_DIR .'lib/functions.php' );
	
	mz_delete_all_posts('classes');
	echo '<br />';
	mz_delete_all_posts('workshops');
	echo '<br />';
	echo 'Repopulating from MindBody';
	
  $classes_pages = new MZ_MBO_Pages_Pages();
  $classes_pages->mZ_mbo_pages_pages('message');
  
}  
?>