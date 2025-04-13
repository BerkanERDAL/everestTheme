<?php
/**
 * The template for displaying footer.
 *
 * @package EverestTheme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$is_editor = isset( $_GET['elementor-preview'] );
$site_name = get_bloginfo( 'name' );
$tagline   = get_bloginfo( 'description', 'display' );
$footer_class = did_action( 'elementor/loaded' ) ? everest_get_footer_layout_class() : '';
$footer_nav_menu = wp_nav_menu( [
	'theme_location' => 'menu-2',
	'fallback_cb' => false,
	'container' => false,
	'echo' => false,
] );
?>
<footer id="site-footer" class="site-footer dynamic-footer <?php echo esc_attr( $footer_class ); ?>">
	<div class="footer-inner">
		<div class="site-branding show-<?php echo esc_attr( everest_elementor_get_setting( 'everest_footer_logo_type' ) ); ?>">
			<?php if ( has_custom_logo() && ( 'title' !== everest_elementor_get_setting( 'everest_footer_logo_type' ) || $is_editor ) ) : ?>
				<div class="site-logo <?php echo esc_attr( everest_show_or_hide( 'everest_footer_logo_display' ) ); ?>">
					<?php the_custom_logo(); ?>
				</div>
			<?php endif;

			if ( $site_name && ( 'logo' !== everest_elementor_get_setting( 'everest_footer_logo_type' ) ) || $is_editor ) : ?>
				<div class="site-title <?php echo esc_attr( everest_show_or_hide( 'everest_footer_logo_display' ) ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr__( 'Home', 'everest-elementor' ); ?>" rel="home">
						<?php echo esc_html( $site_name ); ?>
					</a>
				</div>
			<?php endif;

			if ( $tagline || $is_editor ) : ?>
				<p class="site-description <?php echo esc_attr( everest_show_or_hide( 'everest_footer_tagline_display' ) ); ?>">
					<?php echo esc_html( $tagline ); ?>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( $footer_nav_menu ) : ?>
			<nav class="site-navigation <?php echo esc_attr( everest_show_or_hide( 'everest_footer_menu_display' ) ); ?>" aria-label="<?php echo esc_attr__( 'Footer menu', 'everest-elementor' ); ?>">
				<?php
				// PHPCS - escaped by WordPress with "wp_nav_menu"
				echo $footer_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</nav>
		<?php endif; ?>

		<?php if ( '' !== everest_elementor_get_setting( 'everest_footer_copyright_text' ) || $is_editor ) : ?>
			<div class="copyright <?php echo esc_attr( everest_show_or_hide( 'everest_footer_copyright_display' ) ); ?>">
				<p><?php echo wp_kses_post( everest_elementor_get_setting( 'everest_footer_copyright_text' ) ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</footer>
