<?php
// silence is gold
require_once dirname(__FILE__) . '/settings.php';
if(ae_get_option('use_escrow') ) {
	$alipay_setting =  ae_get_option('escrow_alipay_api');
	$alipay_partner_id = $alipay_setting['alipay_partner_id'];
	//var_dump($alipay_setting);exit;
	if($alipay_partner_id){
		require_once dirname(__FILE__) . '/alipay/alipay_submit.class.php';
		require_once dirname(__FILE__) . '/alipay.php';
	} else{
		require_once dirname(__FILE__) . '/ppadaptive.php';
		require_once dirname(__FILE__) . '/paypal.php';
	}

}
