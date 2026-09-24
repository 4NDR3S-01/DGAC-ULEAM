<?php
/**
 * Plantilla de página estática
 *
 * @package uleam-calidad
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<header class="page-header">
		<h1><?php the_title(); ?></h1>
	</header>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
	<?php
endwhile;
?>

<?php get_footer(); ?>
