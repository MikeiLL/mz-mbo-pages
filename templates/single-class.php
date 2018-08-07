<?php
/**
 * The template for displaying all single classes
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
 
function mz_pages_post_nav($tp)
{
	$wp2 = array();
	$i=0;
	$pOrder = array(
			'post_type'         =>	'classes',
			'orderby'		=>	'title',
			'order'			=>	'ASC',
			'nopaging'		=>	true,
			'posts_count'	=>	5
				);
	$workPosts = get_posts($pOrder);
	foreach($workPosts as $wpost)
	{
		$pid = $wpost->ID;
		$wp2[$i] = $pid;
		$i++;
	}
	$pnum = array_search($tp,$wp2);
	
	//initialize in case empty
	$pre = '';
	$nxt = '';
	
	if($pnum-1 >= 0)
	{
		$pre = '<a href="'.get_permalink($wp2[$pnum-1]).'" title="'.get_the_title($wp2[$pnum-1]).'" rel="prev"><span class="meta-nav">&larr;</span> '.get_the_title($wp2[$pnum-1]).'</a>';
	}
	if($pnum+1 < count($wp2))
	{
		$nxt = '<a href="'.get_permalink($wp2[$pnum+1]).'" title="'.get_the_title($wp2[$pnum+1]).'" rel="next">'.get_the_title($wp2[$pnum+1]).' <span class="meta-nav">&rarr;</span></a>';
	}
	?>
		<nav class="navigation post-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'twentythirteen' ); ?></h1>
			<div class="nav-links">
		<?php echo $pre . $nxt; ?>
			</div><!-- .nav-links -->
		</nav><!-- .navigation -->
	<?php } ?>
	
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
			
				<?php
				function mz_mbo_pages_add_to_content ($content) {
					$content .= '<h4>Visit the schedule page for more info and to register for classes.</h4>';
					return $content;
				}
				?>
				<?php 
				//mz_pr(wp_get_theme()->get_page_templates() ); 
				//mz_pr(get_page_template() );
				//mz_pr(is_page_template('classes') );
				
				?>
				<?php add_filter( 'the_content', 'mz_mbo_pages_add_to_content', 50 ); ?>
				<?php $content = get_template_part( 'content', $name = 'classes' ) ?>
				
				<?php if( is_singular('classes') ) : 
					mz_pages_post_nav(get_the_ID());
				else:
					twentythirteen_post_nav(); 
				endif;
				?>
				<?php if (is_singular( 'classes' )): ?>
					<nav class="navigation paging-navigation" role="navigation">
						<h1 class="screen-reader-text">Return to Class Overview</h1>
						<div class="nav-links">
							<div class="nav-previous">
								<a href="<?php echo get_post_type_archive_link( 'classes' ); ?>">
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