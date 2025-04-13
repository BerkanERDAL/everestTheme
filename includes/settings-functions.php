<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'admin_menu', 'everest_elementor_settings_page' );
add_action( 'init', 'everest_elementor_tweak_settings', 0 );

/**
 * Register theme settings page.
 */
function everest_elementor_settings_page() {

	$menu_hook = '';

	$menu_hook = add_theme_page(
		esc_html__( 'everest Theme Settings', 'everest-elementor' ),
		esc_html__( 'Theme Settings', 'everest-elementor' ),
		'manage_options',
		'everest-theme-settings',
		'everest_elementor_settings_page_render'
	);

	add_action( 'load-' . $menu_hook, function() {
		add_action( 'admin_enqueue_scripts', 'everest_elementor_settings_page_scripts', 10 );
	} );

}

/**
 * Register settings page scripts.
 */
function everest_elementor_settings_page_scripts() {

	$dir = get_template_directory() . '/assets/js';
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$handle = 'everest-admin';
	$asset_path = "$dir/everest-admin.asset.php";
	$asset_url = get_template_directory_uri() . '/assets/js';
	if ( ! file_exists( $asset_path ) ) {
		throw new \Error( 'You need to run `npm run build` for the "everest-theme" first.' );
	}
	$script_asset = require( $asset_path );

	wp_enqueue_script(
		$handle,
		"$asset_url/$handle$suffix.js",
		$script_asset['dependencies'],
		$script_asset['version']
	);

	wp_set_script_translations( $handle, 'everest-elementor' );

	wp_enqueue_style(
		$handle,
		"$asset_url/$handle$suffix.css",
		[ 'wp-components' ],
		$script_asset['version']
	);

	$plugins = get_plugins();

	if ( ! isset( $plugins['elementor/elementor.php'] ) ) {
		$action_link_type = 'install-elementor';
		$action_link_url = wp_nonce_url(
			add_query_arg(
				[
					'action' => 'install-plugin',
					'plugin' => 'elementor',
				],
				admin_url( 'update.php' )
			),
			'install-plugin_elementor'
		);
	} elseif ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		$action_link_type = 'activate-elementor';
		$action_link_url = wp_nonce_url( 'plugins.php?action=activate&plugin=elementor/elementor.php', 'activate-plugin_elementor/elementor.php' );
	} elseif ( everest_header_footer_experiment_active() && ! everest_header_footer_experiment_active() ) {
		$action_link_type = 'activate-header-footer-experiment';
		$action_link_url = wp_nonce_url( 'admin.php?page=elementor#tab-experiments' );
	} elseif ( everest_header_footer_experiment_active() ) {
		$action_link_type = 'style-header-footer';
		$action_link_url = wp_nonce_url( 'post.php?post=' . get_option( 'elementor_active_kit' ) . '&action=elementor' );
	} else {
		$action_link_type = '';
		$action_link_url = '';
	}

	wp_localize_script(
		$handle,
		'everestAdminData',
		[
			'actionLinkType' => $action_link_type,
			'actionLinkURL' => $action_link_url,
			'templateDirectoryURI' => get_template_directory_uri(),
		]
	);
}

/**
 * Render settings page wrapper element.
 */
function everest_elementor_settings_page_render() {
	?>
	<div id="everest-elementor-settings"></div>
	<?php

	add_action( 'admin_footer', 'everest_elementor_settings_page_footer' );
}

function everest_elementor_settings_page_footer() {
	$notifications = everest_elementor_get_theme_notifications()->get_notifications_by_conditions();
	?>
	<style>
		#everest-elementor-notifications-dialog {
			max-height: 80vh;
			padding: 20px;
			border: 1px solid #ccc;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
		}
		#everest-elementor-notifications-dialog::backdrop {
			background-color: rgba(0, 0, 0, 0.5);
		}
		#everest-elementor-notifications-dialog h2 {
			font-size: 1.5em;
		}
		#everest-elementor-notifications-dialog h3 {
			font-size: 1.1em;
		}
		#everest-elementor-notifications-dialog .close-notifications-dialog {
			position: absolute;
			inset-block-start: 20px;
			inset-inline-end: 20px;
			font-size: 26px;
			background: none;
			border: none;
			aspect-ratio: 1;
			cursor: pointer;
		}
	</style>
	<script>
		document.addEventListener( 'DOMContentLoaded', function() {
			const closeDialogBtn = document.querySelector( '#everest-elementor-notifications-dialog button.close-notifications-dialog' );
			const dialog = document.getElementById( 'everest-elementor-notifications-dialog' );

			closeDialogBtn.addEventListener( 'click', function() {
				dialog.close();
			} );
		} );
	</script>
	<dialog id="everest-elementor-notifications-dialog" aria-labelledby="everest-elementor-notifications-dialog-heading">
		<button autofocus class="close-notifications-dialog" aria-label="<?php echo esc_attr__( 'Close changelog', 'everest-elementor' ); ?>"> &times; </button>
		<h2 id="everest-elementor-notifications-dialog-heading"><?php echo esc_html__( 'Changelog:', 'everest-elementor' ); ?></h2>
		<?php foreach ( $notifications as $item ) : ?>
			<h3><?php echo esc_html( $item['title'] ); ?></h3>
			<p><?php echo wp_kses_post( $item['description'] ); ?></p>
		<?php endforeach; ?>
	</dialog>
	<?php
}

/**
 * Theme tweaks & settings.
 */
function everest_elementor_tweak_settings() {

	$settings_group = 'everest_elementor_settings';

	$settings = [
		'DESCRIPTION_META_TAG' => '_description_meta_tag',
		'SKIP_LINK' => '_skip_link',
		'HEADER_FOOTER' => '_header_footer',
		'PAGE_TITLE' => '_page_title',
		'everest_STYLE' => '_everest_style',
		'everest_THEME' => '_everest_theme',
	];

	everest_elementor_register_settings( $settings_group, $settings );
	everest_elementor_render_tweaks( $settings_group, $settings );
}

/**
 * Register theme settings.
 */
function everest_elementor_register_settings( $settings_group, $settings ) {

	foreach ( $settings as $setting_key => $setting_value ) {
		register_setting(
			$settings_group,
			$settings_group . $setting_value,
			[
				'default' => '',
				'show_in_rest' => true,
				'type' => 'string',
			]
		);
	}

}

/**
 * Run a tweek only if the user requested it.
 */
function everest_elementor_do_tweak( $setting, $tweak_callback ) {

	$option = get_option( $setting );
	if ( isset( $option ) && ( 'true' === $option ) && is_callable( $tweak_callback ) ) {
		$tweak_callback();
	}

}

/**
 * Render theme tweaks.
 */
function everest_elementor_render_tweaks( $settings_group, $settings ) {

	everest_elementor_do_tweak( $settings_group . $settings['DESCRIPTION_META_TAG'], function() {
		remove_action( 'wp_head', 'everest_elementor_add_description_meta_tag' );
	} );

	everest_elementor_do_tweak( $settings_group . $settings['SKIP_LINK'], function() {
		add_filter( 'everest_elementor_enable_skip_link', '__return_false' );
	} );

	everest_elementor_do_tweak( $settings_group . $settings['HEADER_FOOTER'], function() {
		add_filter( 'everest_elementor_header_footer', '__return_false' );
	} );

	everest_elementor_do_tweak( $settings_group . $settings['PAGE_TITLE'], function() {
		add_filter( 'everest_elementor_page_title', '__return_false' );
	} );

	everest_elementor_do_tweak( $settings_group . $settings['everest_STYLE'], function() {
		add_filter( 'everest_elementor_enqueue_style', '__return_false' );
	} );

	everest_elementor_do_tweak( $settings_group . $settings['everest_THEME'], function() {
		add_filter( 'everest_elementor_enqueue_theme_style', '__return_false' );
	} );

}
