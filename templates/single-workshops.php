<?php

/**
 * content-workshops.php
 * Template for "workshops" custom post type content.
 */

?>

<?php
	// Variables
	$options = get_option( 'mz_mbo_pages_options','' ); // Events options
	$start_date = get_post_meta( $post->ID, 'mz_pages_workshops_start_date', true ); // Event start date
	$end_date = get_post_meta( $post->ID, 'mz_pages_workshops_end_date', true ); // Event end date
	
	mz_pr(get_post_meta( $post->ID));
?>

	<article class="container">

		<header>
			<h1><?php the_title(); ?></h1>
		</header>

		<?php get_template_part( 'content', get_post_format() ); ?>

		<?php edit_post_link( __( 'Edit', 'mz-mindbody-api' ), '<p>', '</p>' ); ?>

	</article>

