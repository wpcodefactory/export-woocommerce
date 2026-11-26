<?php
/**
 * WPFactory Cross-Selling - My Plugins - "Picked for your store" template.
 *
 * @version 1.2.2
 * @since   1.2.2
 * @author  WPFactory
 *
 * @var array $cards { item, reason }.
 */

use WPFactory\WPFactory_Cross_Selling\My_Plugins\Page;

defined( 'ABSPATH' ) || exit;
?>
<section class="wpfcs-mp-section" aria-labelledby="wpfcs-mp-picks-title">
	<div>
		<h2 id="wpfcs-mp-picks-title" class="wpfcs-mp-h2"><?php esc_html_e( 'Picked for your store', 'wpfactory-cross-selling' ); ?></h2>
		<p class="wpfcs-mp-note"><?php esc_html_e( 'Based on your store settings. Checked on this site, nothing is sent anywhere.', 'wpfactory-cross-selling' ); ?></p>
	</div>
	<div class="wpfcs-mp-picks">
		<?php foreach ( $cards as $card ) : ?>
			<?php $item = $card['item']; ?>
			<article class="wpfcs-ui-card wpfcs-mp-pick" data-id="<?php echo esc_attr( $item['id'] ); ?>">
				<div class="wpfcs-mp-reason"><?php echo esc_html( $card['reason'] ); ?></div>
				<div class="wpfcs-mp-pick-body">
					<h3 class="wpfcs-mp-h3"><?php echo esc_html( $item['name'] ); ?></h3>
					<p><?php echo esc_html( $item['desc'] ); ?></p>
				</div>
				<div class="wpfcs-mp-pick-foot">
					<?php Page::render_actions( $item, 'pick' ); ?>
				</div>
				<p class="wpfcs-mp-error" role="alert" hidden></p>
			</article>
		<?php endforeach; ?>
	</div>
</section>
