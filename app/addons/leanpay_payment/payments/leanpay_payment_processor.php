<?php

use Tygh\Registry;
use Tygh\Http;
use Tygh\Session;

// Preventing direct access to the script, because it must be included by the "include" directive. The "BOOTSTRAP" constant is declared during system initialization.
defined('BOOTSTRAP') or die('Access denied');

// Here are two different contexts for running the script.
if (defined('PAYMENT_NOTIFICATION')) {
    
    /**
     * Receiving and processing the answer
     * from third-party services and payment systems.
     */
    if ($mode == 'complete' && !empty($_REQUEST['order_id'])) {

        $cart = &Tygh::$app['session']['cart'];

        $order_info = fn_leanpay_payment_get_order_by_timestamp($_REQUEST['order_id']);

        fn_finish_payment($order_info['order_id'], $order_info['payment_info'], false);

        fn_order_placement_routines('repay', $order_info['order_id'], 'A');

    } elseif ($mode == 'cancel') {

        $order = fn_leanpay_payment_get_order_by_timestamp($_REQUEST['order_id']);

        $response['order_status'] = 'N';
        $response["reason_text"] = __('text_transaction_cancelled');
        
        fn_finish_payment($order['order_id'], $response);
        fn_order_placement_routines('route', $order['order_id']);
    }
    
} else {

    $cart = &Tygh::$app['session']['cart'];
    $auth = \Tygh::$app['session']['auth'];

    try {

        /**
         * Running the necessary logics for payment acceptance
         * after the customer presses the "Submit my order" button.
         */        
        $leanpaySettings = fn_leanpay_payment_get_leanpay_settings();
        
        /**
         * Validation if isset merchant parameters and isset order some parameters.
         */
        fn_leanpay_payment_validate($leanpaySettings, $order_info);
        
        /**
        * Build LeanPAY request with items and some merchant parameters.
        */
       $buildRequest = fn_leanpay_payment_built_request_data(array('orderInfo' => $order_info, 'leanpaySettings' => $leanpaySettings));
       
       /**
         * Sent curl request.
         * Throw exception if result is empty or LeanPAY errorCode is not value 0.
        */
       $result = fn_leanpay_payment_send_request($buildRequest, $leanpaySettings);
       
       if (isset($result['token']))
       {
           fn_leanpay_payment_redirect_customer($result['token'], $leanpaySettings);
       }

    } catch (Exception $exception) {
        fn_set_notification('E', __('Error'), $exception->getMessage());
    }

}