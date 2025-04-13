<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register Customizer controls for header & footer.
 *
 * @return void
 */
function everest_customizer_register( $wp_customize ) {
	require_once get_template_directory() . '/includes/customizer/customizer-action-links.php';

	$wp_customize->add_section(
		'everest-options',
		[
			'title' => esc_html__( 'Header & Footer', 'everest-elementor' ),
			'capability' => 'edit_theme_options',
		]
	);

	$wp_customize->add_setting(
		'everest-header-footer',
		[
			'sanitize_callback' => false,
			'transport' => 'refresh',
		]
	);

	$wp_customize->add_control(
		new EverestTheme\Includes\Customizer\everest_Customizer_Action_Links(
			$wp_customize,
			'everest-header-footer',
			[
				'section' => 'everest-options',
				'priority' => 20,
			]
		)
	);
}
add_action( 'customize_register', 'everest_customizer_register' );

/**
 * Register Customizer controls for Elementor Pro upsell.
 *
 * @return void
 */
function everest_customizer_register_elementor_pro_upsell( $wp_customize ) {
	if ( function_exists( 'elementor_pro_load_plugin' ) ) {
		return;
	}

	require_once get_template_directory() . '/includes/customizer/customizer-upsell.php';

	$wp_customize->add_section(
		new EverestTheme\Includes\Customizer\everest_Customizer_Upsell(
			$wp_customize,
			'everest-upsell-elementor-pro',
			[
				'heading' => esc_html__( 'Customize your entire website with Elementor Pro', 'everest-elementor' ),
				'description' => esc_html__( 'Build and customize every part of your website, including Theme Parts with Elementor Pro.', 'everest-elementor' ),
				'button_text' => esc_html__( 'Upgrade Now', 'everest-elementor' ),
				'button_url' => 'https://elementor.com/pro/?utm_source=everest-theme-customize&utm_campaign=gopro&utm_medium=wp-dash',
				'priority' => 999999,
			]
		)
	);
}
add_action( 'customize_register', 'everest_customizer_register_elementor_pro_upsell' );

/**
 * Enqueue Customizer CSS.
 *
 * @return void
 */
function everest_customizer_styles() {

	$min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_style(
		'everest-elementor-customizer',
		get_template_directory_uri() . '/customizer' . $min_suffix . '.css',
		[],
		everest_ELEMENTOR_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'everest_customizer_styles' );
