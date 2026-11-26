<?php
/**
 * WPFactory Cross-Selling - My Plugins - Top bar template.
 *
 * The WPFactory submenu as a top bar, as in the AI Assistant and Helpdesk screens.
 *
 * @version 1.2.2
 * @since   1.2.2
 * @author  WPFactory
 *
 * @var array  $links { href, label, current } Page::get_topbar_links().
 * @var bool   $dot   Show the new-plugins dot on the current tab.
 * @var string $logo  WPFactory logo URL.
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="wpfcs-ui-topbar wpfcs-mp-topbar">
	<div class="wpfcs-ui-brand"><img class="wpfcs-mp-brand-logo" src="<?php echo esc_url( $logo ); ?>" width="32" height="25" alt=""><?php esc_html_e( 'WPFactory', 'wpfactory-cross-selling' ); ?></div>
	<nav class="wpfcs-ui-nav" aria-label="<?php esc_attr_e( 'WPFactory', 'wpfactory-cross-selling' ); ?>">
		<?php foreach ( $links as $link ) : ?>
			<a href="<?php echo esc_url( $link['href'] ); ?>"<?php echo $link['current'] ? ' class="on" aria-current="page"' : ''; ?>>
				<?php echo esc_html( $link['label'] ); ?>
				<?php if ( $link['current'] && $dot ) : ?>
					<span class="wpfcs-mp-dot" role="img" aria-label="<?php esc_attr_e( 'New plugins available', 'wpfactory-cross-selling' ); ?>"></span>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</nav>
</header>
