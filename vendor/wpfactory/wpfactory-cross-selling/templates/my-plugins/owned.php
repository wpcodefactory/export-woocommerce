<?php
/**
 * WPFactory Cross-Selling - My Plugins - Installed plugins template.
 *
 * @version 1.2.2
 * @since   1.2.2
 * @author  WPFactory
 *
 * @var array $rows { item, chip_type, chip_label, action, action_url } Page::get_owned_rows().
 *                  action: "activate", "activate_pro", "upgrade", "settings" or "".
 */

use WPFactory\WPFactory_Cross_Selling\My_Plugins\Page;

defined( 'ABSPATH' ) || exit;
?>
<div class="wpfcs-ui-card wpfcs-mp-owned">
	<?php foreach ( $rows as $row ) : ?>
		<?php $item = $row['item']; ?>
		<div class="wpfcs-mp-owned-row" data-id="<?php echo esc_attr( $item['id'] ); ?>">
			<div class="wpfcs-mp-owned-name"><b><?php echo esc_html( $item['name'] ); ?></b> <span class="wpfcs-mp-muted"><?php echo esc_html( $item['desc'] ); ?></span></div>
			<span class="wpfcs-ui-chip wpfcs-ui-chip-<?php echo esc_attr( $row['chip_type'] ); ?> wpfcs-mp-chip"><?php echo esc_html( $row['chip_label'] ); ?></span>
			<div class="wpfcs-mp-owned-action">
				<?php if ( 'activate' === $row['action'] ) : ?>
					<button type="button" class="wpfcs-ui-btn wpfcs-ui-btn-secondary wpfcs-ui-btn-sm wpfcs-mp-activate" data-id="<?php echo esc_attr( $item['id'] ); ?>"><?php esc_html_e( 'Activate', 'wpfactory-cross-selling' ); ?></button>
				<?php elseif ( 'activate_pro' === $row['action'] ) : ?>
					<button type="button" class="wpfcs-ui-btn wpfcs-ui-btn-secondary wpfcs-ui-btn-sm wpfcs-mp-activate" data-id="<?php echo esc_attr( $item['id'] ); ?>"><?php esc_html_e( 'Activate Pro', 'wpfactory-cross-selling' ); ?></button>
				<?php elseif ( 'upgrade' === $row['action'] ) : ?>
					<?php echo Page::external_link( $row['action_url'], __( 'Upgrade to Pro', 'wpfactory-cross-selling' ), 'wpfcs-ui-btn wpfcs-ui-btn-secondary wpfcs-ui-btn-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in external_link(). ?>
				<?php elseif ( 'settings' === $row['action'] ) : ?>
					<a class="wpfcs-ui-a" href="<?php echo esc_url( $row['action_url'] ); ?>"><?php esc_html_e( 'Settings', 'wpfactory-cross-selling' ); ?></a>
				<?php endif; ?>
			</div>
			<p class="wpfcs-mp-error" role="alert" hidden></p>
		</div>
	<?php endforeach; ?>
	<div class="wpfcs-mp-allaccess">
		<div class="wpfcs-mp-allaccess-text">
			<b><?php esc_html_e( 'Get every WPFactory plugin with All Access', 'wpfactory-cross-selling' ); ?></b>
			<span class="wpfcs-mp-muted">
				<?php esc_html_e( 'Pro versions of all our plugins, for life. Bought a plugin from us in the last 60 days?', 'wpfactory-cross-selling' ); ?>
				<?php
				printf(
					/* translators: %s: "Contact us" link. */
					esc_html__( '%s and we\'ll count it toward your upgrade.', 'wpfactory-cross-selling' ),
					Page::external_link( Page::CONTACT_URL, __( 'Contact us', 'wpfactory-cross-selling' ), 'wpfcs-ui-a' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in external_link().
				);
				?>
			</span>
		</div>
		<?php
		/* translators: %s: price. */
		echo Page::external_link( Page::ALL_ACCESS_URL, sprintf( __( 'Get All Access: %s', 'wpfactory-cross-selling' ), '$499' ), 'wpfcs-ui-btn wpfcs-ui-btn-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in external_link().
		?>
	</div>
</div>
