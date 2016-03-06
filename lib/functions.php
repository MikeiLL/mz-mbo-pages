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

?>