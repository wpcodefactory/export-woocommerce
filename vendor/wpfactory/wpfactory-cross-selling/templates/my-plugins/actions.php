<?php
/**
 * WPFactory Cross-Selling - My Plugins - Plugin actions template.
 *
 * Install free (or the wp.org link when installs aren't allowed) and Get Pro.
 * Pro-only plugins get a single Get Pro button.
 *
 * @version 1.2.2
 * @since   1.2.2
 * @author  WPFactory
 *
 * @var array  $item        Catalog item.
 * @var string $main        Main button class: wpfcs-ui-btn-primary (pick) or wpfcs-ui-btn-secondary (row).
 * @var bool   $pro_only    Pro-only plugin.
 * @var bool   $can_install Free plugins can be installed from the page.
 * @var string $wporg_url   wp.org page of the free plugin.
 */

use WPFactory\WPFactory_Cross_Selling\My_Plugins\Page;

defined( 'ABSPATH' ) || exit;
?>
<div class="wpfcs-mp-actions">
	<?php if ( $pro_only ) : ?>
		<?php echo Page::external_link( $item['pro_url'], __( 'Get Pro', 'wpfactory-cross-selling' ), 'wpfcs-ui-btn ' . $main . ' wpfcs-ui-btn-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in external_link(). ?>
	<?php else : ?>
		<?php if ( $can_install ) : ?>
			<button type="button" class="wpfcs-ui-btn <?php echo esc_attr( $main ); ?> wpfcs-ui-btn-sm wpfcs-mp-install" data-id="<?php echo esc_attr( $item['id'] ); ?>"><?php esc_html_e( 'Install free', 'wpfactory-cross-selling' ); ?></button>
		<?php else : ?>
			<?php echo Page::external_link( $wporg_url, __( 'View on WordPress.org', 'wpfactory-cross-selling' ), 'wpfcs-ui-btn ' . $main . ' wpfcs-ui-btn-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in external_link(). ?>
		<?php endif; ?>
		<?php if ( '' !== $item['pro_url'] ) : ?>
			<?php echo Page::external_link( $item['pro_url'], __( 'Get Pro', 'wpfactory-cross-selling' ), 'wpfcs-ui-btn wpfcs-ui-btn-text wpfcs-ui-btn-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in external_link(). ?>
		<?php endif; ?>
	<?php endif; ?>
</div>
