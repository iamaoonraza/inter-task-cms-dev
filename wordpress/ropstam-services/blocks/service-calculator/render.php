<?php
/**
 * Front end output of the block.
 *
 * @package RopstamServices
 */

defined( 'ABSPATH' ) || exit;

$ropstam_services = ropstam_services_get_recent();
$ropstam_addons   = ropstam_services_get_addons();

wp_enqueue_script( 'ropstam-services-calculator' );
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<?php if ( empty( $ropstam_services ) ) : ?>
		<p><?php esc_html_e( 'No services are available right now.', 'ropstam-services' ); ?></p>
	<?php else : ?>
		<ul class="rs-services">
			<?php
			foreach ( $ropstam_services as $ropstam_service ) :
				$ropstam_price = get_post_meta( $ropstam_service->ID, 'base_price', true );
				$ropstam_days  = (int) get_post_meta( $ropstam_service->ID, 'delivery_time_days', true );
				?>
				<li class="rs-service">
					<h3 class="rs-service__title">
						<a href="<?php echo esc_url( get_permalink( $ropstam_service ) ); ?>">
							<?php echo esc_html( get_the_title( $ropstam_service ) ); ?>
						</a>
					</h3>

					<?php if ( $ropstam_days > 0 ) : ?>
						<p class="rs-service__delivery">
							<?php
							/* translators: %d: number of days. */
							echo esc_html( sprintf( _n( 'Delivery in %d day', 'Delivery in %d days', $ropstam_days, 'ropstam-services' ), $ropstam_days ) );
							?>
						</p>
					<?php endif; ?>

					<?php if ( ! is_numeric( $ropstam_price ) ) : ?>
						<p class="rs-service__total"><?php esc_html_e( 'Contact us for pricing', 'ropstam-services' ); ?></p>
					<?php else : ?>
						<div data-service-calculator data-base-price="<?php echo esc_attr( ropstam_services_to_cents( $ropstam_price ) ); ?>">
							<?php if ( ! empty( $ropstam_addons ) ) : ?>
								<?php // Stays hidden until the script runs, because the toggles do nothing without it. ?>
								<fieldset class="rs-addons" hidden>
									<legend><?php esc_html_e( 'Optional add-ons', 'ropstam-services' ); ?></legend>
									<?php foreach ( $ropstam_addons as $ropstam_addon ) : ?>
										<label class="rs-addon">
											<input type="checkbox" data-price="<?php echo esc_attr( ropstam_services_to_cents( $ropstam_addon['price'] ) ); ?>">
											<span><?php echo esc_html( $ropstam_addon['label'] ); ?></span>
											<span class="rs-addon__price">+<?php echo esc_html( ropstam_services_format_price( $ropstam_addon['price'] ) ); ?></span>
										</label>
									<?php endforeach; ?>
								</fieldset>
							<?php endif; ?>

							<p class="rs-service__total">
								<?php esc_html_e( 'Estimated price:', 'ropstam-services' ); ?>
								<output data-total><?php echo esc_html( ropstam_services_format_price( $ropstam_price ) ); ?></output>
							</p>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
