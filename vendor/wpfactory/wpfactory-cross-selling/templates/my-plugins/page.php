<?php
/**
 * WPFactory Cross-Selling - My Plugins - Page template.
 *
 * @version 1.2.2
 * @since   1.2.2
 * @author  WPFactory
 *
 * @var array  $owned   Installed catalog items, active first (Page::sort_owned()).
 * @var object $state   Site_State.
 * @var array  $picks   Rules_Engine::get_picks().
 * @var array  $items   Catalog items.
 * @var bool   $woo     WooCommerce is active.
 * @var string $subline Line under the title.
 */

use WPFactory\WPFactory_Cross_Selling\My_Plugins\Page;

defined( 'ABSPATH' ) || exit;
?>
<div class="wpfcs-my-plugins">
	<?php Page::render_topbar(); ?>
	<main class="wpfcs-ui-page wpfcs-mp-page">
		<hr class="wp-header-end">
		<section class="wpfcs-mp-section" aria-labelledby="wpfcs-mp-title">
			<div>
				<h1 id="wpfcs-mp-title" class="wpfcs-mp-h1"><?php esc_html_e( 'My Plugins', 'wpfactory-cross-selling' ); ?></h1>
				<p class="wpfcs-mp-lead"><?php echo esc_html( $subline ); ?></p>
			</div>
			<?php Page::render_owned( $owned, $state ); ?>
		</section>
		<?php if ( ! empty( $picks ) ) : ?>
			<?php Page::render_picks( $picks ); ?>
		<?php endif; ?>
		<?php Page::render_catalog( $items, $state, $woo, wp_list_pluck( $picks, 'id' ) ); ?>
	</main>
</div>
