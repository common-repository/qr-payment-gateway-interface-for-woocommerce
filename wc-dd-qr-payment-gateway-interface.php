<?php
/* @wordpress-plugin
 * Plugin Name:       QR Payment Gateway Interface for WooCommerce
 * Plugin URI:        https://www.ddtickets.rs/wp/plugins/qr-payment-gateway-interface/
 * Description:       An Interface for WooCommerce that allows your customers to use the DD Payment Gateway for IPS Instant Payment to pay their orders by scanning a QR code using their mobile phone with a m-banking application
 * Version:           1.0.0
 * WC requires at least: 3.0
 * WC tested up to: 4.8
 * Author:            DD Ticketing Solutions
 * Author URI:        https://www.ddtickets.rs
 * Text Domain:       wc-dd-qr-payment-gateway-interface
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */


 
$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if(qrpgwifwc_is_woocommerce_active()){
	add_filter('woocommerce_payment_gateways', 'qrpgwifwc_add_qr_payment_gateway');
	function qrpgwifwc_add_qr_payment_gateway( $gateways ){
		$gateways[] = 'QRPGWIFWC_Payment_Gateway';
		return $gateways; 
	}

	add_action('plugins_loaded', 'qrpgwifwc_init_qr_payment_gateway');
	function qrpgwifwc_init_qr_payment_gateway(){
		require 'classes/class-wc-dd-qr-payment-gateway-interface.php';
	}

}

// Outputting QR dataset in hidden fields on post-checkout page
add_action( 'woocommerce_thankyou', 'qrpgwifwc_add_qr_postcheckout_hidden_field', 10, 1 );
function qrpgwifwc_add_qr_postcheckout_hidden_field( $order_id ) {

	// QR Payment gateway ID
	$payment_gateway_id = 'qr_payment';

	// Get an instance of the WC_Payment_Gateways object
	$payment_gateways   = WC_Payment_Gateways::instance();

	// Get the desired WC_Payment_Gateway object
	$payment_gateway    = $payment_gateways->payment_gateways()[$payment_gateway_id];

	global $wp;

	// Get Querystring parameters and sanitize them immediately
	$qskey = sanitize_text_field( $_GET["key"] ?? ""); //WP/WC order unique indentifier
	$qserr = sanitize_text_field( $_GET["qrerr"] ?? ""); //QR PGW payment status response
	$qsref = sanitize_text_field( $_GET["qrref"] ?? ""); //QR PGW payment reference number from merchant bank


	// Extra parts added to initial Querystring
	$qserrpair1 = "?qrerr=" . $qserr;
	$qserrpair2 = "&qrerr=" . $qserr;
	$qsrefpair = "&qrref=" . $qsref;

	$ok_url = $payment_gateway->ok_url; //Custom OK URL for PGW return point after processing
	$err_url = $payment_gateway->err_url; //Custom ERR URL for PGW return point if unavailable to process

	// Actual Querytring
	$curpage_querystring = $_SERVER['QUERY_STRING'];

	// Actual Querytring without Extra parts
	$curpage_querystring = str_replace( $qserrpair1, "", $curpage_querystring);
	$curpage_querystring = str_replace( $qserrpair2, "", $curpage_querystring);
	$curpage_querystring = str_replace( $qsrefpair, "", $curpage_querystring);

	if ( empty($ok_url) ) $ok_url = add_query_arg( $curpage_querystring, '', trailingslashit( home_url( $wp->request ) ) ); //Overwritten empty Custom OK URL

	// Parameter coding --------
	$keyoffset = (int) 0;
	$s = $ok_url;
	for ( $i=0; $i<strlen($s); $i++ ) $keyoffset += ord( substr($s, $i, 1) );
	$errqsval = 300 + $keyoffset;
	$iniqsval = -1 + $keyoffset;
	//--------------------------

	if ( empty($err_url) ) $err_url = add_query_arg( 'qrerr=' . $errqsval, '', $ok_url ); //Overwritten empty Custom ERR URL

	// Get the desired WC_Order object
	$order = new WC_Order( $order_id );    

	// payment status parameter preprocessing ------
	if ( $qserr == "" ) $qserr = (string) $iniqsval;
	if ( !is_numeric($qserr) ) $qserr = (string) $iniqsval;

	$ires = (int)($qserr);
	$ires -= $keyoffset;
	//----------------------------------------------

	// QR Order status
	$order_status		= $order->get_status();

	// QR Transaction reference code
	$payment_refcode = "n/a";

	// QR Payment Gateway response
	switch ( $ires ){
		case -1: //Payment pending
			//$payment_statustext = 'Pending payment';
			$payment_statustext = $payment_gateway->payment_message_hold;
			break;
		case 0: //Payment successful
			//$payment_statustext = 'Successful';
			$payment_statustext = $payment_gateway->payment_message_successful;

			// QR Payment status 
			$payment_status		= $payment_gateway->payment_status;

			// Mark as processing (payment received but the order is still not fulfilled)
			$order->update_status($payment_status, 'Payment');

			//Update the current order status varialble
			$order_status		= $order->get_status();

			//Get the reference code
			$payment_refcode = $qsref;
			break;
		case 5: //Payment rejected
			//$payment_statustext = 'Rejected';
			$payment_statustext = $payment_gateway->payment_message_rejected;
			break;
		case 82: //Payment timeout
			//$payment_statustext = 'Timeout';
			$payment_statustext = $payment_gateway->payment_message_timeout;
			break;
		case 100: //Payment aborted
			//$payment_statustext = 'Aborted';
			$payment_statustext = $payment_gateway->payment_message_aborted;
			break;
		case 300: //Payment error
			//$payment_statustext = 'Not proccessed';
			$payment_statustext = $payment_gateway->payment_message_error;
			break;
		default: //Unknown payment status
			//$payment_statustext = 'Unknown';
			$payment_statustext = $payment_gateway->payment_message_unknown;

	}

	// Payment status Block part #1 -------------------------

	echo '<h2>' . esc_html( $payment_gateway->payment_title ) . '</h2>';
	
	echo '<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">';
	echo '<li class="woocommerce-order-overview__order order">';
	echo 'Status: ' . '<strong>' . esc_html( $payment_statustext ) . '</strong>';
	echo '</li>';

	switch ( $ires ){
		case 0: //Payment successfull
			echo '<li class="woocommerce-order-overview__order order">';
			echo 'Reference: ' . '<strong>' . esc_html( $payment_refcode ) . '</strong>';
			echo '</li>';
			break;
		default:
	}

	echo '</ul>';
	//-------------------------------------------------------

	switch ( $order_status ){
		case "on-hold":

			// Payment status Block part #2 -------------------------
			$payment_instructions = $payment_gateway->payment_instructions;
			$payment_buttonname = $payment_gateway->payment_buttonname;
			$payment_instructions = str_replace( '[[payment_button]]', '"' . $payment_buttonname . '"', $payment_instructions);

		    echo '<p>' . esc_html( $payment_instructions ) . '</p>';

			echo '<form nane="qppgwform" method="post" action="' . esc_url_raw( $payment_gateway->pgw_url ) . '">';
			echo '<div align="center" id="qr_dataset_hidden_checkout_fields">';
			echo '<input type="hidden" class="input-hidden" name="POSGUID" id="pos_guid" value="' . esc_html( $payment_gateway->pos_guid ) . '">';
			echo '<input type="hidden" class="input-hidden" name="QRPaymentGatewayURL" id="pgw_url" value="' . esc_url_raw( $payment_gateway->pgw_url ) . '">';
			echo '<input type="hidden" class="input-hidden" name="QRPaymentReturnURL" id="ok_url" value="' . esc_url_raw( $ok_url ) . '">';
			echo '<input type="hidden" class="input-hidden" name="QRUnavailableGatewayURL" id="err_url" value="' . esc_url_raw( $err_url ) . '">';

			echo '<input type="hidden" class="input-hidden" name="QRPaymentLogGUID" id="order_log_id" value="' . esc_html( $order->get_order_key() ) . '">';
			echo '<input type="hidden" class="input-hidden" name="QRPaymentOrderID" id="order_id" value="' . esc_html( $order->get_id() ) . '">';
			echo '<input type="hidden" class="input-hidden" name="QRPaymentOrderPrice" id="order_price" value="' . esc_html( wc_format_decimal($order->get_total(),2) ) . '">';
			echo '<input type="hidden" class="input-hidden" name="QRPaymentOrderEmail" id="order_email" value="' . esc_html( $order->get_billing_email() ) . '">';

			echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/ips_logo.png' . '">';
			echo '<br>';
			echo '<input type="submit" class="button" name="' . esc_html( $order->get_payment_method() ) . '" value="' . esc_html( $payment_buttonname ) . '">';
			echo '<br>';

			echo '</div>';
			echo '</form>';
			//-------------------------------------------------------

			break;
	}

}


 // WooCommerce needs to be active prior to add the IPS QR Instant Payment as a new payment method
function qrpgwifwc_is_woocommerce_active() //Returns bool
{
	$active_plugins = (array) get_option('active_plugins', array());

	if (is_multisite()) {
		$active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	}

	return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
}
