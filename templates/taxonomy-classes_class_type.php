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

get_header();
	
	$type = 'classes';
	$page_taxonomy = get_query_var( 'classes_class_type', 'yoga' );
	wp_reset_query();

	$args=array(
    'post_type' => $type,
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'ignore_sticky_posts'=> 1,
    'tax_query' => array(
			array(
				'taxonomy' => 'classes_class_type',
				'field'    => 'slug',
				'terms'    => $page_taxonomy,
			),
		)
	);

	$query = null;
	$query = new WP_Query($args);

?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php if ( $query->have_posts() ) : ?>
			<header class="archive-header">
				<h1 class="archive-title"><?php echo ucfirst(str_replace('-', ' ', get_query_var( 'classes_class_type', 'yoga'))) ?> Classes</h1>
			</header><!-- .archive-header -->
			<div class="full-width-entry-content">
				<div id="mz-mbo-pages" class="mz_mbo_schedule">

							<?php
							$tbl = new HTML_Table('', 'mz-schedule-filter mz_all_our_classes' . ' ' . ' mz-schedule-horizontal mz-schedule-display');
							$tbl->addRow('header');
							$tbl->addCell(__('Class Name', 'mz-mindbody-api'), 'mz_classDetails', 'header', array('scope'=>'header'));
							$tbl->addCell(__('Instructor', 'mz-mindbody-api'), 'mz_staffName', 'header', array('scope'=>'header'));
							$tbl->addCell(__('Class Type', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
							//$tbl->addCell(__('Level', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
							$tbl->addTSection('tbody');
							?>

							<?php /* The loop */ ?>
							<?php while ( $query->have_posts() ) : $query->the_post(); ?>
									<?php
									$class_type = wp_get_post_terms( $query->post->ID, 'classes_class_type' );
									$teacher = get_post_meta($query->post->ID, 'teacher');
									$class_title_link = new html_element('a');
									$class_title_link->set('href', get_the_permalink($post));
									// remove "with so and so from the title
									$event_title_sans_instructor = explode(__("with", 'mz-mbo-pages'), get_the_title());
									$class_title_link->set('text', $event_title_sans_instructor[0]);
									$row_css_classes = 'mz_description_holder mz_schedule_table mz_location_';
									$tbl->addRow($row_css_classes);
									$tbl->addCell($class_title_link->build());
									$tbl->addCell($teacher[0]);
									$tbl->addCell($class_type[0]->name);
									//$tbl->addCell(get_field('level'));
									?>
							<?php endwhile; ?>
			
			
							<?php echo $tbl->display() ?>
				
			
						<?php
							function add_filter_table() {
									wp_enqueue_script('mz_mbo_pages_script', plugins_url('/mz-mindbody-api/dist/scripts/mz_filtertable.js'), array('jquery'), null, true);
									wp_enqueue_style('mz_mbo_pages_style', plugins_url('/mz-mindbody-api/dist/styles/main.css'));
								}

							function initialize_filter() {
								wp_localize_script('mz_mbo_pages_script', 'mz_mindbody_api_i18n', array(
								'filter_default' => __('by teacher, class type', 'mz-mindbody-api'),
								'quick_1' => __('Hot Yoga', 'mz-mindbody-api'),
								'quick_2' => __('', 'mz-mindbody-api'),
								'quick_3' => __('Beginner', 'mz-mindbody-api'),
								'label' => __('Filter', 'mz-mindbody-api'),
								'selector' => __('All Locations', 'mz-mindbody-api'),
								'Locations_dict' => array()
								));
							}
							add_action('wp_footer', 'add_filter_table', 10);
							add_action('wp_footer', 'initialize_filter');
						?>
						<?php //twentythirteen_paging_nav(); ?>

					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
					<?php endif; ?>
					</div>
			</div><!-- #mbo-schedule -->
		</div><!-- #content -->
	</div><!-- #primary -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>

		<!-- Start mZ_mindbody-api filterTable configuration -->
		<script type="text/javascript">
			$(document).ready(function() {
				var stripeTable = function(table) { //stripe the table (jQuery selector)
						table.find('tr').removeClass('striped').filter(':visible:even').addClass('striped');
					};

					$('table.mz-schedule-filter').filterTable({
						callback: function(term, table) { stripeTable(table); }, //call the striping after every change to the filter term
						placeholder: mz_mindbody_api_i18n.filter_default,
						highlightClass: 'alt',
						inputType: 'search',
						label: mz_mindbody_api_i18n.label,
						selector: mz_mindbody_api_i18n.selector,
						quickListClass: 'mz_quick_filter',
						quickList: [],
						locations: mz_mindbody_api_i18n.Locations_dict
					});					
				
					stripeTable($('table.mz-schedule-filter')); //stripe the table for the first time
				});
		</script>
		<!-- End mZ_mindbody-api filterTable configuration -->	
