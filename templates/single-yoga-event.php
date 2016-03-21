<?php
/**
 * The template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', get_post_format() ); ?>
				
				<p>Visit the schedule page for more info and to register for the class.</p>
				
				<?php if( is_singular('yoga-event') ) : ?>
				<?php 
				else:
					twentythirteen_post_nav(); 
				endif;
				?>
				<?php if (is_singular( 'yoga-event' )): ?>
					<nav class="navigation paging-navigation" role="navigation">
						<h1 class="screen-reader-text">Return to Class Overview</h1>
						<div class="nav-links">
							<div class="nav-previous">
								<a href="<?php echo get_post_type_archive_link( 'yoga-event' ); ?>">
									<span class="meta-nav">←</span>
									Return to Class Overview
								</a>
							</div>
						</div>
					</nav>
				<?php endif; ?>

				<?php comments_template(); ?>

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>