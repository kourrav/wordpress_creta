<?php
/**
 * Afterpay Checkout Instalments Display
 * @var WC_Gateway_Afterpay $this
 */

# Process Region-based Assets
$region_assets = include 'assets.php';

$checkout_page_cta          =   $region_assets['checkout_page_cta'];
$checkout_page_first_step   =   $region_assets['checkout_page_first_step'];
$checkout_page_footer       =   $region_assets['checkout_page_footer'];


if ($this->settings['testmode'] != 'production') {
    ?><p class="afterpay-test-mode-warning-text"><?php _e( 'TEST MODE ENABLED', 'woo_afterpay' ); ?></p><?php
}

$order_button_text = !empty($this->order_button_text) ? $this->order_button_text : 'Place order';

?>
<ul class="form-list">
    <li class="form-alt">
        <div class="instalment-info-container" id="afterpay-checkout-instalment-info-container">
            <p class="header-text">
                <?php _e( $checkout_page_cta, 'woo_afterpay' ); ?>
                <strong><?php echo $this->display_price_html($order_total); ?></strong>
            </p>
            <div class="instalment-wrapper">
                <div class="instalment">
                    <p class="instalment-header-text"><?php echo $this->display_price_html($instalments[0]); ?></p>
                    <div class="img-wrapper"><img src="<?php echo $this->get_static_url(); ?>checkout/circle_1@2x.png" alt="First Instalment" /></div>
                    <p class="instalment-footer-text"><?php _e( $checkout_page_first_step, 'woo_afterpay' ); ?></p>
                </div>
                <div class="instalment">
                    <p class="instalment-header-text"><?php echo $this->display_price_html($instalments[1]); ?></p>
                    <div class="img-wrapper"><img src="<?php echo $this->get_static_url(); ?>checkout/circle_2@2x.png" alt="2 weeks later" /></div>
                    <p class="instalment-footer-text"><?php _e( '2 weeks later', 'woo_afterpay' ); ?></p>
                </div>
                <div class="instalment">
                    <p class="instalment-header-text"><?php echo $this->display_price_html($instalments[2]); ?></p>
                    <div class="img-wrapper"><img src="<?php echo $this->get_static_url(); ?>checkout/circle_3@2x.png" alt="4 weeks later" /></div>
                    <p class="instalment-footer-text"><?php _e( '4 weeks later', 'woo_afterpay' ); ?></p>
                </div>
                <div class="instalment">
                    <p class="instalment-header-text"><?php echo $this->display_price_html($instalments[3]); ?></p>
                    <div class="img-wrapper"><img src="<?php echo $this->get_static_url(); ?>checkout/Circle_4@2x.png	" alt="6 weeks later" /></div>
                    <p class="instalment-footer-text"><?php _e( '6 weeks later', 'woo_afterpay' ); ?></p>
                </div>
            </div>
        </div>
        <p class="footer-text">
            <?php _e( $checkout_page_footer . " \"{$order_button_text}\".", 'woo_afterpay' ); ?>
            <br />
            <!-- <a href="https://www.afterpay.com/terms" target="_blank"><?php _e( 'Terms &amp; Conditions', 'woo_afterpay' ); ?></a> -->
        </p>
    </li>
</ul>
<div class="what-is-afterpay-container">
    <a id="checkout-what-is-afterpay-link" href="#afterpay-what-is-modal" target="_blank"><?php _e( 'What is Afterpay?', 'woo_afterpay' ); ?></a>
 </div>
