<?php

class MZ_MBO_Pages_Pages {

	private $mz_mbo_globals;
	
	public function __construct(){
		require_once(WP_PLUGIN_DIR . '/mz-mindbody-api/' . 'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
	}

	
	public function mZ_mbo_pages_pages($message='no message', $atts=array(), $account=0) {
	
    $options = get_option( 'mz_mbo_pages_options','' );
    if (isset($options['mz_mbo_pages_log_api_calls']) && $options['mz_mbo_pages_log_api_calls'] == True):
			mZ_write_to_file($message, $options['mz_mbo_pages_logfile_path']);
		endif;
	}

}//EOF MZ_MBO_Staff

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
