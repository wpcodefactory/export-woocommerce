<?php
/**
 * WPFactory Cross-Selling - My Plugins - "All WPFactory plugins" template.
 *
 * @version 1.2.2
 * @since   1.2.2
 * @author  WPFactory
 *
 * @var array    $shown      Catalog items to list, top plugins first.
 * @var array    $tabs       Tab id => label, only tabs with plugins.
 * @var string   $first      Selected tab id.
 * @var string[] $picked_ids Ids shown in "Picked for your store".
 */

use WPFactory\WPFactory_Cross_Selling\My_Plugins\Page;

defined( 'ABSPATH' ) || exit;
?>
<section class="wpfcs-mp-section wpfcs-mp-catalog" aria-labelledby="wpfcs-mp-catalog-title">
	<div class="wpfcs-mp-catalog-head">
		<h2 id="wpfcs-mp-catalog-title" class="wpfcs-mp-h2"><?php esc_html_e( 'All WPFactory plugins', 'wpfactory-cross-selling' ); ?></h2>
		<label class="wpfcs-mp-search">
			<span><?php esc_html_e( 'Search', 'wpfactory-cross-selling' ); ?></span>
			<input type="search" class="wpfcs-ui-input" placeholder="<?php esc_attr_e( 'VAT, quantity, barcode...', 'wpfactory-cross-selling' ); ?>" aria-controls="wpfcs-mp-catalog-list">
		</label>
	</div>
	<div class="wpfcs-ui-seg wpfcs-mp-tabs" role="tablist" aria-label="<?php esc_attr_e( 'What you want to do', 'wpfactory-cross-selling' ); ?>">
		<?php foreach ( $tabs as $job => $label ) : ?>
			<button type="button" role="tab" id="wpfcs-mp-tab-<?php echo esc_attr( $job ); ?>" data-job="<?php echo esc_attr( $job ); ?>" aria-controls="wpfcs-mp-catalog-list" aria-selected="<?php echo $job === $first ? 'true' : 'false'; ?>" tabindex="<?php echo $job === $first ? '0' : '-1'; ?>"<?php echo $job === $first ? ' class="on"' : ''; ?>><?php echo esc_html( $label ); ?></button>
		<?php endforeach; ?>
	</div>
	<div class="wpfcs-ui-card wpfcs-mp-list" id="wpfcs-mp-catalog-list" role="tabpanel" aria-labelledby="wpfcs-mp-tab-<?php echo esc_attr( $first ); ?>">
		<?php foreach ( $shown as $item ) : ?>
			<div class="wpfcs-mp-row" data-id="<?php echo esc_attr( $item['id'] ); ?>" data-job="<?php echo esc_attr( $item['job'] ); ?>" data-top="<?php echo $item['top'] ? '1' : '0'; ?>" data-search="<?php echo esc_attr( strtolower( $item['name'] . ' ' . $item['desc'] ) ); ?>">
				<div class="wpfcs-mp-row-main">
					<?php $logo = Page::get_logo_src( $item ); ?>
					<?php if ( '' !== $logo ) : ?>
						<img class="wpfcs-mp-logo" src="<?php echo esc_url( $logo ); ?>" width="40" height="40" alt="" loading="lazy" decoding="async">
					<?php else : ?>
						<span class="wpfcs-mp-logo wpfcs-mp-logo-badge" aria-hidden="true"><?php echo esc_html( $item['abbr'] ); ?></span>
					<?php endif; ?>
					<div>
						<b><?php echo esc_html( $item['name'] ); ?></b>
						<?php if ( in_array( $item['id'], $picked_ids, true ) ) : ?>
							<span class="wpfcs-ui-chip wpfcs-ui-chip-info"><?php esc_html_e( 'Picked above', 'wpfactory-cross-selling' ); ?></span>
						<?php endif; ?>
						<div class="wpfcs-mp-muted"><?php echo esc_html( $item['desc'] ); ?></div>
					</div>
				</div>
				<?php Page::render_actions( $item, 'row' ); ?>
				<p class="wpfcs-mp-error" role="alert" hidden></p>
			</div>
		<?php endforeach; ?>
		<p class="wpfcs-mp-empty" hidden><?php esc_html_e( 'No plugin matches that search. Try a shorter word.', 'wpfactory-cross-selling' ); ?></p>
	</div>
</section>
