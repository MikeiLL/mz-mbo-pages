<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Thirteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="archive-header">
				<h1 class="archive-title">An Overview of our Classes</h1>
			</header><!-- .archive-header -->
			<?php
			$tbl = new HTML_Table('', 'mz_all_our_classes' . ' ' . ' mz-schedule-horizontal mz-schedule-display');
			$tbl->addRow('header');
			$tbl->addCell(__('Class Name', 'mz-mindbody-api'), 'mz_classDetails', 'header', array('scope'=>'header'));
			$tbl->addCell(__('Instructor', 'mz-mindbody-api'), 'mz_staffName', 'header', array('scope'=>'header'));
			$tbl->addCell(__('Class Type', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
			$tbl->addCell(__('Level', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
			$tbl->addTSection('tbody');
			?>

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
					<?php
					$link = new html_element('a');
					$link->set('href', get_the_permalink());
					// remove "with so and so from the title
					$event_title_sans_instructor = explode(__("with", 'mz-mbo-pages'), get_the_title());
					$link->set('text', $event_title_sans_instructor[0]);
					$row_css_classes = 'mz_description_holder mz_schedule_table mz_location_';
					$tbl->addRow($row_css_classes);
					$tbl->addCell($link->build());
					$tbl->addCell(get_field('teacher'));
					$tbl->addCell(get_field('type'));
					$tbl->addCell(get_field('level'));
					?>
			<?php endwhile; ?>
			<?php echo $tbl->display() ?>
			<?php twentythirteen_paging_nav(); ?>

		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>