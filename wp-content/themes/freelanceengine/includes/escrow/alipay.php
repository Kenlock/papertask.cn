<?php

/**
 * register post type fre_order to handle escrow order
 * @author Dakachi
 */
function fre_register_order() {
    register_post_type('fre_order', $args = array(
        'labels' => array(
            'name' => __('Fre Order', ET_DOMAIN) ,
            'singular_name' => __('Fre Order', ET_DOMAIN)
        ) ,
        'hierarchical' => true,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
    ));
}
add_action('init', 'fre_register_order');

/**
 * enqueue script to open modal accept bid
 * @author Dakachi
 */
function fre_enqueue_escrow() {
  $role = ae_user_role();

    if (is_singular(PROJECT) && $role != "EMPLOYER_VIP") {
      //var_dump($role);exit;
        wp_enqueue_script('escrow-accept', TEMPLATEURL . '/assets/js/accept-alipay-bid.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'
        ) , ET_VERSION, true);
    }
}
add_action('wp_print_scripts', 'fre_enqueue_escrow');

/**
 * ajax callback to setup bid info and send to client
 * @author Dakachi
 */
function fre_get_accept_bid_info() {
    $bid_id = $_GET['bid_id'];
    global $user_ID;
    $error = array(
        'success' => false,
        'msg' => __('Invalid bid', ET_DOMAIN)
    );
    if (!isset($_REQUEST['bid_id'])) {
        wp_send_json($error);
    }
    $bid_id = $_REQUEST['bid_id'];
    $bid = get_post($bid_id);

    // check bid is valid
    if (!$bid || is_wp_error($bid) || $bid->post_type != BID) {
        wp_send_json($error);
    }

    $bid_budget = get_post_meta($bid_id, 'bid_budget', true);

    // get commission settings
    $commission = ae_get_option('commission', 0);
    $commission_fee = $commission;

    // caculate commission fee by percent
    $commission_type = ae_get_option('commission_type');
    if ($commission_type != 'currency') {
        $commission_fee = ((float)($bid_budget * (float)$commission)) / 100;
    }

    $commission = fre_price_format($commission_fee);
    $payer_of_commission = ae_get_option('payer_of_commission', 'project_owner');
    if ($payer_of_commission == 'project_owner') {
        $total = (float)$bid_budget + (float)$commission_fee;
    }
    else {
        $commission = 0;
        $total = $bid_budget;
    }
    $data = array(
        'budget'=>$bid_budget,
        'commission'=>$commission,
        'total'=>$total
        );
    $data = apply_filters( 'ae_accept_bid_infor', $data);
    wp_send_json(array(
        'success' => true,
        'data' => array(
            'budget' => fre_price_format($data['budget']) ,
            'budget_transfer' => $data['budget'],
            'commission' => $data['commission'],
            'bid_id' => $bid_id,
            'total' => fre_price_format($data['total'])
        )
    ));
}
add_action('wp_ajax_ae-accept-bid-info', 'fre_get_accept_bid_info');

/**
 * ajax callback process bid escrow and send redirect url to client
 *
 * @author Dakachi
 */
function fre_escrow_bid() {
    global $user_ID;
    $error = array(
        'success' => false,
        'msg' => __('Invalid bid', ET_DOMAIN)
    );
    if (!isset($_REQUEST['bid_id'])) {
        wp_send_json($error);
    }
    $bid_id = $_REQUEST['bid_id'];
    $bid = get_post($bid_id);

    // check bid is valid
    if (!$bid || is_wp_error($bid) || $bid->post_type != BID) {
        wp_send_json($error);
    }

    // currency settings
    $currency = ae_get_option('currency');
    $currency = $currency['code'];

    $bid_budget = get_post_meta($bid_id, 'bid_budget', true);

    // get commission settings
    $commission = ae_get_option('commission');
    $commission_fee = $commission;

    // caculate commission fee by percent
    $commission_type = (string)ae_get_option('commission_type');
    if ($commission_type == 'percent') {
        $commission_fee = ($bid_budget * $commission) / 100;
    }
    $payer_of_commission = ae_get_option('payer_of_commission', 'project_owner');
    if ($payer_of_commission == 'project_owner') {
        $total = (float)$bid_budget + (float)$commission_fee;
    }
    else {
        $total = $bid_budget;
        $bid_budget = (float)$total - (float)$commission_fee;
    }
    $escrow_data = array(
        'total'=> $total,
        'currency'=>$currency,
        'bid_budget'=> $bid_budget,
        'commission_fee'=> $commission_fee,
        'payer_of_commission'=>$payer_of_commission,
        'bid_author'=> $bid->post_author,
        'bid_id' => $bid_id
        );

        $return_url = et_get_page_link('process-payment', array(
            'paymentType' => 'alipay'

        ));
    		$parameter = array(
    				"service" => "create_direct_pay_by_user",
    				"partner" => trim(ae_get_option("alipay_partner_id")),
    				"seller_email" => trim(ae_get_option("alipay_partner_id")),
    				"payment_type"	=> "1",
    				"notify_url"	=> "",
    				"return_url"	=> $return_url,
    				"out_trade_no"	=> $bid_id,
    				"subject"	=> $bid_id,
    				"total_fee"	=> $total,
    				"body"	=> $bid_id,
    				"show_url"	=> "",
    				"anti_phishing_key"	=> "",
    				"exter_invoke_ip"	=> "",
    				"_input_charset"	=> strtolower('utf-8'),
            "sign_type"   => strtoupper('MD5'),
            "cacert"    => getcwd().'\\cacert.pem'
    		);
    		$alipaySubmit = new AlipaySubmit($alipay_config);
    		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "Redirecting to alipay...");
    		return $html_text;
}
add_action('wp_ajax_ae-escrow-bid', 'fre_escrow_bid');

/**
 * dispute process execute payment and send money to freelancer
 * @since 1.3
 * @author Dakachi
 */
function fre_execute_payment() {
    // only the admin or the user have manage_options cap can execute the dispute
    if (!current_user_can('manage_options')) {
        wp_send_json(array(
            'success' => false,
            'msg' => __("You do not have permission to do this action.", ET_DOMAIN)
        ));
    }
    $project_id = $_REQUEST['project_id'];
    $bid_accepted = get_post_meta($project_id, 'accepted', true);

    // cho nay co the dung action
    $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
    do_action('ae_escrow_execute', $project_id, $bid_id_accepted);
    // execute payment and send money to freelancer
    $pay_key = get_post_meta($bid_id_accepted, 'fre_paykey', true);
    if ($pay_key) {
        $ppadaptive = AE_PPAdaptive::get_instance();
        $response = $ppadaptive->executePayment($pay_key);

        if (strtoupper($response->responseEnvelope->ack) == 'SUCCESS') {

            // success update order data
            $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
            if ($order) {
                wp_update_post(array(
                    'ID' => $order,
                    'post_status' => 'completed'
                ));
            }

            // success update project status
            wp_update_post(array(
                'ID' => $project_id,
                'post_status' => 'disputed'
            ));

            /**
             * do action after admin finish dispute and execute send payment to freelancer
             * @param int $project_id
             * @param int $bid_id_accepted
             * @param int $order
             * @since 1.3
             * @author Dakachi
             */
            do_action('fre_dispute_execute_payment', $project_id, $bid_id_accepted, $order);

            // send mail
            $mail = Fre_Mailing::get_instance();
            $mail->execute($project_id, $bid_id_accepted);

            wp_send_json(array(
                'success' => true,
                'msg' => __("Send payment successful.", ET_DOMAIN)
            ));
        }
        else {
            wp_send_json(array(
                'success' => false,
                'msg' => $response->error[0]->message
            ));
        }
    }
    else {
        wp_send_json(array(
            'success' => false,
            'msg' => __("Invalid paykey.", ET_DOMAIN)
        ));
    }
}
add_action('wp_ajax_execute_payment', 'fre_execute_payment');

/**
 * dispute process refund payment to employer
 * @since 1.3
 * @author Dakachi
 */
function fre_refund_payment() {
    if (!current_user_can('manage_options')) {
        wp_send_json(array(
            'success' => false,
            'msg' => __("You do not have permission to do this action.", ET_DOMAIN)
        ));
    }
    $project_id = $_REQUEST['project_id'];

    // cho nay co the dung action
    $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
    do_action('ae_escrow_refund', $project_id, $bid_id_accepted);
    // execute payment and send money to freelancer
    $pay_key = get_post_meta($bid_id_accepted, 'fre_paykey', true);
    if ($pay_key) {
        $ppadaptive = AE_PPAdaptive::get_instance();
        $response = $ppadaptive->Refund($pay_key);

        if (strtoupper($response->responseEnvelope->ack) == 'SUCCESS') {

            // success update order data
            $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
            if ($order) {
                wp_update_post(array(
                    'ID' => $order,
                    'post_status' => 'refund'
                ));
            }

            // success update project status
            wp_update_post(array(
                'ID' => $project_id,
                'post_status' => 'disputed'
            ));

            /**
             * do action after admin finish dispute and refund payment
             * @param int $project_id
             * @param int $bid_id_accepted
             * @param int $order
             * @since 1.3
             * @author Dakachi
             */
            do_action('fre_dispute_refund_payment', $project_id, $bid_id_accepted, $order);

            $mail = Fre_Mailing::get_instance();
            $mail->refund($project_id, $bid_id_accepted);

            // send json back
            wp_send_json(array(
                'success' => true,
                'msg' => __("Send payment successful.", ET_DOMAIN) ,
                'data' => $response
            ));
        }
        else {
            wp_send_json(array(
                'success' => false,
                'msg' => $response->error[0]->message
            ));
        }
    }
    else {
        wp_send_json(array(
            'success' => false,
            'msg' => __("Invalid paykey.", ET_DOMAIN)
        ));
    }
}
add_action('wp_ajax_refund_payment', 'fre_refund_payment');

/**
 * ajax callback to transfer payment to freelancer
 * @since 1.3
 * @author Dakachi
 */
function fre_transfer_money() {
    if (current_user_can('manage_options')) {
        $project_id = $_REQUEST['project_id'];

        // cho nay co the dung action
        $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
        // success update order data
        /*$order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
        if ($order) {
            wp_update_post(array(
                'ID' => $order,
                'post_status' => 'finish'
            ));
        }else{
          $payment_status = get_post_meta($current->ID, 'payment_status', true);

          add_post_meta( $bid_id_accepted, 'payment_status', 1 );
        }*/

        add_post_meta( $bid_id_accepted, 'payment_status', 1 );

        // send mail
        $mail = Fre_Mailing::get_instance();
        $mail->execute($project_id, $bid_id_accepted);

        // send json back
        wp_send_json(array(
            'success' => true,
            'msg' => __("Payment successful.", ET_DOMAIN) ,
            'data' => $order
        ));


        // execute payment and send money to freelancer
        $pay_key = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        if ($pay_key) {
            $ppadaptive = AE_PPAdaptive::get_instance();
            $response = $ppadaptive->executePayment($pay_key);
            if (strtoupper($response->responseEnvelope->ack) == 'SUCCESS') {

                // success update order data
                $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                if ($order) {
                    wp_update_post(array(
                        'ID' => $order,
                        'post_status' => 'finish'
                    ));
                }

                // send mail
                $mail = Fre_Mailing::get_instance();
                $mail->execute($project_id, $bid_id_accepted);

                // send json back
                wp_send_json(array(
                    'success' => true,
                    'msg' => __("Payment refund successful.", ET_DOMAIN) ,
                    'data' => $response
                ));
            }
            else {
                wp_send_json(array(
                    'success' => false,
                    'msg' => $response->error[0]->message
                ));
            }
        }
        else {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Invalid paykey.", ET_DOMAIN)
            ));
        }
    }
}
add_action('wp_ajax_transfer_money', 'fre_transfer_money');

/**
 * finish project, send money when freelancer review project
 * @param int $project_id
 * @since 1.3
 * @author Dakachi
 */
function fre_finish_escrow($project_id) {
    if (ae_get_option('use_escrow')) {
        $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
        $credit_api = ae_get_option( 'escrow_credit_settings' );

        if(isset($credit_api['use_credit_escrow']) && $credit_api['use_credit_escrow']){
            do_action('fre_finish_escrow', $project_id, $bid_id_accepted);
        }else if (!ae_get_option('manual_transfer')) {
            // cho nay co the dung action
            do_action('fre_finish_escrow', $project_id, $bid_id_accepted);
            // execute payment and send money to freelancer
            $pay_key = get_post_meta($bid_id_accepted, 'fre_paykey', true);
            if ($pay_key) {
                $ppadaptive = AE_PPAdaptive::get_instance();
                $response = $ppadaptive->executePayment($pay_key);
                if (strtoupper($response->responseEnvelope->ack) == 'SUCCESS') {

                    // success update order data
                    $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                    if ($order) {
                        wp_update_post(array(
                            'ID' => $order,
                            'post_status' => 'finish'
                        ));
                        $mail = Fre_Mailing::get_instance();
                        $mail->alert_transfer_money($project_id, $bid_id_accepted);
                    }
                }
            }
        }
        else {
            $mail = Fre_Mailing::get_instance();
            $mail->alert_transfer_money($project_id, $bid_id_accepted);
        }
    }
}
add_action('fre_freelancer_review_employer', 'fre_finish_escrow');
/**
 * Add escrow account field
 * @param bool true/false
 * @return string $html
 * @since FrE-v1.7
 * @package AE_ESCROW
 * @category PPADAPTIVE
 * @author Tambh
 */
function ae_ppadaptive_recipient_field(){
    global $user_ID;
    $author_id  =   get_query_var( 'author' );
    
    $uid = $user_ID;
    if($author_id && $author_id != $user_ID) {
        $uid = $author_id;
    }

    ob_start();
    if( !et_load_mobile() ){
     ?>
    <div class="form-group">
        <div class="form-group-control">
            <label><?php _e('Your Paypal Account', ET_DOMAIN) ?></label>
            <input type="text" class="form-control" id="paypal" name="paypal" value="<?php echo get_user_meta( $uid, 'paypal', true ); ?>" placeholder="<?php _e('Enter your paypal email', ET_DOMAIN) ?>">
        </div>
    </div>
    <div class="clearfix"></div>
<?php
    }
    else{ ?>
    <div class="form-group-mobile">
        <label><?php _e('Your Paypal Account', ET_DOMAIN) ?></label>
        <input type="text" id="paypal" value="<?php echo get_user_meta( $uid, 'paypal', true ); ?>" name="paypal" placeholder="<?php _e('Enter your paypal email', ET_DOMAIN) ?>">
    </div>
<?php }
        $html = ob_get_clean();
        $html = apply_filters('ae_escrow_recipient_field_html', $html);
        echo $html;
    }
add_action('ae_escrow_recipient_field', 'ae_ppadaptive_recipient_field');
