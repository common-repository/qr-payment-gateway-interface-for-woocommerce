<?php 

class QRPGWIFWC_Payment_Gateway extends WC_Payment_Gateway{

    private $order_status;

	public function __construct(){
		$this->id = 'qr_payment';
		$this->method_title = __('IPS QR Instant Payment','wc-dd-qr-payment-gateway-interface');
		$this->method_description = __('IPS QR Insant Payment redirects customers to the QR Payment Gateway to pay their order by scanning the IPS QR code.','wc-dd-qr-payment-gateway-interface');		

		$this->init_form_fields();
		$this->init_settings();

		$this->enabled = $this->get_option('enabled');
		$this->title = $this->get_option('title');
		$this->description = $this->get_option('description');

		$this->order_status = $this->get_option('order_status');
		$this->payment_status = $this->get_option('payment_status');
		$this->payment_title = $this->get_option('payment_title');
		$this->payment_instructions = $this->get_option('payment_instructions');
		$this->payment_buttonname = $this->get_option('payment_buttonname');

		$this->pos_guid = $this->get_option('pos_guid');
		$this->pgw_url  = $this->get_option('pgw_url');
		$this->ok_url	= $this->get_option('ok_url');		
		$this->err_url	= $this->get_option('err_url');

		$this->payment_message_hold			= $this->get_option('payment_message_hold');
		$this->payment_message_successful	= $this->get_option('payment_message_successful');
		$this->payment_message_rejected		= $this->get_option('payment_message_rejected');
		$this->payment_message_timeout		= $this->get_option('payment_message_timeout');
		$this->payment_message_aborted		= $this->get_option('payment_message_aborted');
		$this->payment_message_error		= $this->get_option('payment_message_error');
		$this->payment_message_unknown		= $this->get_option('payment_message_unknown');

		add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));

	}

	public function init_form_fields(){
				$this->form_fields = array(
					'enabled' => array(
					'title' 		=> __( 'Enable/Disable', 'wc-dd-qr-payment-gateway-interface' ),
					'type' 			=> 'checkbox',
					'label' 		=> __( 'Enable IPS QR Instant Payment', 'wc-dd-qr-payment-gateway-interface' ),
					'default' 		=> 'yes'
					),

		            'title' => array(
						'title' 		=> __( 'Method Title', 'wc-dd-qr-payment-gateway-interface' ),
						'type' 			=> 'text',
						'css'			=> 'width:600px;',
						'default'		=> __( 'IPS QR Instant Payment', 'wc-dd-qr-payment-gateway-interface' ),
						'description' 	=> __( 'How you will introduce the IPS QR Instant Payment to the customer', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
					'description' => array(
						'title'			=> __( 'Customer Message', 'wc-dd-qr-payment-gateway-interface' ),
						'type'			=> 'textarea',
						'css'			=> 'width:600px;',
						'default'		=> 'Insant QR Payment via m-banking application using the IPS QR scan option',
						'description' 	=> __( 'The message which you want it to appear to the customer in the checkout page.', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> false,
					),
					'order_status' => array(
						'title'			=> __( 'Order Status After Checkout', 'wc-dd-qr-payment-gateway-interface' ),
						'type'			=> 'select',
						'options'		=> wc_get_order_statuses(),
						'default'		=> 'wc-on-hold',
						'description' 	=> __( 'The default order status if this gateway used in payment.', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
					'payment_status' => array(
						'title'			=> __( 'Order Status After Payment', 'wc-dd-qr-payment-gateway-interface' ),
						'type'			=> 'select',
						'options'		=> wc_get_order_statuses(),
						'default'		=> 'wc-processing',
						'description' 	=> __( 'The default order status after successfull payment if this gateway used in payment.', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
					'pos_guid' => array(
						'title'			=> __( 'POS Global Unique Identifer', 'wc-dd-qr-payment-gateway-interface' ),
						'type' 			=> 'text',
						'css'			=> 'width:600px;',
						'default'		=> __( '', 'wc-dd-qr-payment-gateway-interface' ),
						'description' 	=> __( 'Provided by your QR Payment Gateway Provider', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
					'pgw_url' => array(
						'title'			=> __( 'QR Payment Gateway URL', 'wc-dd-qr-payment-gateway-interface' ),
						'type'			=> 'textarea',
						'css'			=> 'width:600px;',
						'default'		=> '',
						'description' 	=> __( 'The URL of the QR Payment Gateway to which the customer is redirected on checkout', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> false,
					),
					'ok_url' => array(
						'title'			=> __( 'QR completed URL', 'wc-dd-qr-payment-gateway-interface' ),
						'type'			=> 'textarea',
						'css'			=> 'width:600px;',
						'default'		=> '',
						'description' 	=> __( 'The URL of the page where the customer is redirected after completing QR payment. If empty then the PostCheckoout page is used', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> false,
					),
					'err_url' => array(
						'title'			=> __( 'QR unvailble URL', 'wc-dd-qr-payment-gateway-interface' ),
						'type'			=> 'textarea',
						'css'			=> 'width:600px;',
						'default'		=> '',
						'description' 	=> __( 'The URL of the page where the customer is redirected in case of unavailable QR Payment Gateway. If empty then the PostCheckoout page is used with err parameter', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> false,
					),
					'payment_title' => array(
						'title'			=> __( 'QR Payment Status', 'wc-dd-qr-payment-gateway-interface' ),
						'type'			=> 'text',
						'css'			=> 'width:600px;',
						'default'		=> 'Payment status',
						'description' 	=> __( 'The title of the QR payment block' ),
						'desc_tip'		=> false,
					),
					'payment_instructions' => array(
						'title'			=> __( 'QR Payment Instructions', 'wc-dd-qr-payment-gateway-interface' ),
						'type'			=> 'textarea',
						'css'			=> 'width:600px;',
						'default'		=> 'Your payment is not completed. Please click on the [[payment_button]] button to open the IPS QR payment gateway and make your Instant Payment',
						'description' 	=> __( 'The instructions for the customer to complete the Instant Payment process' ),
						'desc_tip'		=> false,
					),
					'payment_buttonname' => array(
						'title'			=> __( 'Instant Payment button text', 'wc-dd-qr-payment-gateway-interface' ),
						'type' 			=> 'text',
						'css'			=> 'width:600px;',
						'default'		=> __( 'QR Instant Pay', 'wc-dd-qr-payment-gateway-interface' ),
						'description' 	=> __( 'The text placed on je IPS QR Payment Button', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),

					'payment_message_hold' => array(
						'title'			=> __( 'Status On hold text', 'wc-dd-qr-payment-gateway-interface' ),
						'type' 			=> 'text',
						'css'			=> 'width:300px;',
						'default'		=> __( 'On hold', 'wc-dd-qr-payment-gateway-interface' ),
						'description' 	=> __( '', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
					'payment_message_successful' => array(
						'title'			=> __( 'Status Successful text', 'wc-dd-qr-payment-gateway-interface' ),
						'type' 			=> 'text',
						'css'			=> 'width:300px;',
						'default'		=> __( 'Successful', 'wc-dd-qr-payment-gateway-interface' ),
						'description' 	=> __( '', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
					'payment_message_rejected' => array(
						'title'			=> __( 'Status Rejected text', 'wc-dd-qr-payment-gateway-interface' ),
						'type' 			=> 'text',
						'css'			=> 'width:300px;',
						'default'		=> __( 'Rejected', 'wc-dd-qr-payment-gateway-interface' ),
						'description' 	=> __( '', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
					'payment_message_timeout' => array(
						'title'			=> __( 'Status Timeout text', 'wc-dd-qr-payment-gateway-interface' ),
						'type' 			=> 'text',
						'css'			=> 'width:300px;',
						'default'		=> __( 'Timeout', 'wc-dd-qr-payment-gateway-interface' ),
						'description' 	=> __( '', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
					'payment_message_aborted' => array(
						'title'			=> __( 'Status Aborted text', 'wc-dd-qr-payment-gateway-interface' ),
						'type' 			=> 'text',
						'css'			=> 'width:300px;',
						'default'		=> __( 'Aborted', 'wc-dd-qr-payment-gateway-interface' ),
						'description' 	=> __( '', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
					'payment_message_error' => array(
						'title'			=> __( 'Status Error text', 'wc-dd-qr-payment-gateway-interface' ),
						'type' 			=> 'text',
						'css'			=> 'width:300px;',
						'default'		=> __( 'Error', 'wc-dd-qr-payment-gateway-interface' ),
						'description' 	=> __( '', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
					'payment_message_unknown' => array(
						'title'			=> __( 'Status Unknown text', 'wc-dd-qr-payment-gateway-interface' ),
						'type' 			=> 'text',
						'css'			=> 'width:300px;',
						'default'		=> __( 'Unknown', 'wc-dd-qr-payment-gateway-interface' ),
						'description' 	=> __( '', 'wc-dd-qr-payment-gateway-interface' ),
						'desc_tip'		=> true,
					),
			 );
	}

	public function admin_options() {
		?>
		<h3><?php _e( 'IPS QR Payment Settings', 'wc-dd-qr-payment-gateway-interface' ); ?></h3>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<table class="form-table">
							<?php $this->generate_settings_html();?>
						</table><!--/.form-table-->
					</div>

				</div>
				<div class="clear"></div>

				<?php
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new WC_Order( $order_id );

		// Mark as on-hold (default) or custom set, but this will automatically be updated depending on the reaponse of the IPS QR payment status
		$order->update_status($this->order_status, 'Payment');

		// Reduce stock levels
		wc_reduce_stock_levels( $order_id );

		// Add a note if submited
		if(isset($_POST[ $this->id.'-admin-note']) && trim($_POST[ $this->id.'-admin-note'])!=''){
			$admin_note = sanitize_textarea_field( $_POST[ $this->id.'-admin-note'] ); //Sanitizing input
			$order->add_order_note(esc_html($admin_note),1); //escape html before adding
		}

		// Remove cart
		$woocommerce->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result' => 'success',
			'redirect' => $this->get_return_url( $order )
		);	
	}

}
