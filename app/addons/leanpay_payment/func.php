<?php

use Tygh\Registry;
use Tygh\Settings;
use Tygh\Http;

if (!defined('AREA')) {
    die('Access denied');
}

function fn_leanpay_payment_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
    $param['product_id'] = $product_data['product_id'];
    
    if ((round($product_data['price']) >= 100) && (round($product_data['price']) <= 3000))
    {
        $price = round($product_data['price']);
        $group_id = fn_leanpay_payment_get_default_group_id();
        $layouts_data = db_get_array("SELECT * FROM ?:leanpay_installments_data WHERE group_id = ?s AND amount = ?s", $group_id, $price);
        
        $product_data['leanpay_installments'] = $layouts_data;
        // Downpayment (polog)
        $product_data['leanpay_downpayment'] = fn_leanpay_payment_get_downpayment($price);
    } else {
        $product_data['leanpay_installments'] = false;
        $product_data['leanpay_downpayment'] = false;
    }
}

function fn_leanpay_payment_get_downpayment($price)
{
    switch($price) {
        case (($price < 1000)):
            return 0;
        break;
        case (($price > 1000) && ($price < 2000)):
            return 100;
        break;
        case (($price > 2000) && ($price < 3000)):
            return 150;
        break;
        case ($price == 3000):
            return 200;
        break;
    }
}

function fn_leanpay_payment_get_default_group_id()
{
    $group_row = db_get_row("SELECT id FROM ?:leanpay_installments_groups WHERE name = ?i LIMIT 1", LEANPAY_DEFAULT_GROUP_NAME);
    
    if (!empty($group_row)) {
        return $group_row['id'];
    }
    
    return false;
}

/**
 * Process LeanPay installments and save it
 * @param array
 * @return
 */
function fn_leanpay_payment_process_installment_group($data)
{
    if ($data['groupName'] === LEANPAY_DEFAULT_GROUP_NAME) {
        $hash = $data['groupId'];
        $group_row = db_get_row("SELECT id FROM ?:leanpay_installments_groups WHERE hash = ?i", $hash);
        $group_data['hash'] = $hash;
        $group_data['name'] = $data['groupName'];
        
        if (empty($group_row)) {
            $group_row['id'] = db_query("INSERT INTO ?:leanpay_installments_groups ?e", $group_data);
        }
        
        foreach ($data['loanAmounts'] as $loadAmount) {
            $installment_data = [
                'group_id' => $group_row['id'],
                'amount' => $loadAmount['loanAmount'],
                'months' => $loadAmount['possibleInstallments'][0]['numberOfMonths'],
                'installment' => $loadAmount['possibleInstallments'][0]['installmentAmout']
            ];
            
            db_query("REPLACE INTO ?:leanpay_installments_data ?e", $installment_data);
        }
    }
}

/**
 * Apply LeanPay limits and disable the payment gateway
 * @param $cart
 * @param $sec
 * @param $payment_tabs
 * @return
 */
function fn_leanpay_payment_prepare_checkout_payment_methods(&$cart, &$sec, &$payment_tabs)
{   
    if ((isset($cart)) && (isset($cart['total'])))
    {
        if ((round($cart['total']) < 100) || (round($cart['total']) > 3000))
        {
            foreach ($payment_tabs as $g_key => $group) {
                foreach ($group as $p_key => $payment) {
                    if ($payment['payment'] == 'LeanPay') {
                        unset($payment_tabs[$g_key][$p_key]);
                    }
                }
                if (empty($payment_tabs[$g_key])) {
                    unset($payment_tabs[$g_key]);
                }
            }
        }
    }
}

function fn_leanpay_payment_checkout_select_default_payment_method(&$cart, &$payment_methods, &$completed_steps)
{
    $available_payment_ids = array();
    foreach ($payment_methods as $group) {
        foreach ($group as $method) {
            $available_payment_ids[] = $method['payment_id'];
        }
    }
    
    // Change default payment if it doesn't exists
    if (floatval($cart['total']) != 0 && !in_array($cart['payment_id'], $available_payment_ids)) {
        $cart['payment_id'] = reset($available_payment_ids);
        $cart['payment_method_data'] = fn_get_payment_method_data($cart['payment_id']);
    }
}

/**
 * Build LeanPAY Request data.
 *
 * @param array $params
 * @return array
 */
function fn_leanpay_payment_built_request_data(array $params){

    $orderInfo = $params['orderInfo'];
    $leanpaySettings = $params['leanpaySettings'];

    $backLink = fn_leanpay_payment_get_back_link((int)$orderInfo['order_id']);

    $api_key = ($leanpaySettings['leanpay_demo'] == 'Y') ? $leanpaySettings['api_demo_key'] : $leanpaySettings['api_key'];
    $currentLanguage = Registry::get('settings.Appearance.frontend_default_language');
    
    $data = array(
        'vendorApiKey' => $api_key,
        'vendorTransactionId' => strtotime('now') . '-' . $orderInfo['order_id'],
        'amount' => $orderInfo['total'],
        'successUrl' => $backLink['successUrl'],
        'errorUrl' => $backLink['errorUrl'],
        'vendorPhoneNumber' => $orderInfo['phone'],
        'vendorFirstName' => $orderInfo['firstname'],
        'vendorLastName' => $orderInfo['lastname'],
        'vendorAddress' => $orderInfo['b_address'],
        'vendorZip' => $orderInfo['b_zipcode'],
        'vendorCity' => $orderInfo['b_city'],
        'language' => $currentLanguage
    );

    return $data;
}

/**
 * @param $id
 * @return mixed
 */
function fn_leanpay_payment_get_order_by_timestamp($id){
    $condition = fn_get_company_condition('?:orders.company_id');
    $order = db_get_row("SELECT * FROM ?:orders WHERE ?:orders.timestamp = ?i $condition", $id);  

    return $order;
}

/**
 * @param $id
 * @return mixed
 */
function fn_leanpay_payment_get_order_by_id($id){
    $condition = fn_get_company_condition('?:orders.company_id');
    $order = db_get_row("SELECT * FROM ?:orders WHERE ?:orders.order_id = ?i $condition", $id);  

    return $order;
}

/**
 * Curl request.
 *
 * @param null $data
 * @return mixed
 * @throws Exception
 */
function fn_leanpay_payment_send_request($data = null, $settings)
{
    $ch = curl_init();
    
    $api_url = fn_leanpay_payment_get_unipay_checkout_url($settings) . '/vendor/token';

    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json'
        )
    );    

    $result = curl_exec($ch);    
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = json_decode($result, true);

    if($status !== 200){
        throw new Exception("Error: call to URL failed with status: $status, response: $response");
    }

    if($response === null){
        throw new Exception("LeanPAY Response is null");
    }

    return $response;
}

/**
 * Curl request to fetch installments
 *
 * @return mixed
 * @throws Exception
 */
function fn_leanpay_payment_get_installments($settings)
{
    $ch = curl_init();
    
    $api_url = fn_leanpay_payment_get_unipay_checkout_url($settings) . '/vendor/installment-plans';
    $api_key = ($settings['leanpay_demo'] == 'Y') ? $settings['api_demo_key'] : $settings['api_key'];

    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['vendorApiKey' => $api_key]));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json'
        )
    );

    $result = curl_exec($ch);    
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = json_decode($result, true);

    if($status !== 200){
        throw new Exception("Error: call to URL failed with status: $status, response: $response");
    }

    if($response === null){
        throw new Exception("LeanPAY Response is null");
    }

    return $response;
}

/**
 * LeanPAY Checkout live url.
 *
 * @return string
 */
function fn_leanpay_payment_get_unipay_checkout_url($settings)
{
    return ($settings['leanpay_demo'] == 'Y') ? 'https://lapp.leanpay.si' : 'https://app.leanpay.si';
}

function fn_leanpay_payment_redirect_customer($token, $settings)
{
    $api_url = fn_leanpay_payment_get_unipay_checkout_url($settings) . '/vendor/checkout';
    
    $html = '<html><head><style></style></head><body>
    <form id="form" action="' . $api_url . '" method="post">
    <input type="hidden" name="token" value="' . $token . '"/>
    </form>
    <script>document.getElementById("form").submit();</script>
    </body></html>';
    
    echo $html;
}

/**
 * Build backLink.
 * Generate Cancel and confirm links.
 *
 * @param $orderId
 * @return string
 */
function fn_leanpay_payment_get_back_link($orderId){
    $successUrl = fn_url("payment_notification.complete?payment=leanpay_payment_processor&order_id=" . $orderId, AREA, 'current');
    $errorUrl = fn_url("payment_notification.cancel?payment=leanpay_payment_processor&order_id=" . $orderId, AREA, 'current');

    return array('successUrl' => $successUrl, 'errorUrl' => $errorUrl);
}

/**
 * @param $lang_code
 * @return mixed
 */
function fn_leanpay_payment_get_leanpay_settings($lang_code = DESCR_SL)
{
    $leanpay_settings = Settings::instance()->getValues('leanpay_payment', 'ADDON');

    $leanpay_settings['general']['callback_url'] = fn_url('leanpay_callback', 'C');

    return $leanpay_settings['general'];
}


/**
 * Throw exception if empty API key.
 *
 * @param $leanpaySettings
 * @param $orderInfo
 * @throws Exception
 */
function fn_leanpay_payment_validate($leanpaySettings, $orderInfo){    
    
    if (empty($leanpaySettings['api_key']) && empty($leanpaySettings['api_demo_key'])) {
        throw new Exception('No API key defined');
    }

    if (empty($orderInfo['order_id'])) {
        throw new Exception('Order ID is empty');
    }

    if (empty($orderInfo['total'])) {
        throw new Exception('Order price is empty');
    }

}


/**
 * @param $settings
 * @return $void
 */
function fn_leanpay_payment_update_leanpay_settings($settings)
{
    foreach ($settings as $setting_name => $setting_value) {
        Settings::instance()->updateValue($setting_name, $setting_value);
    }
}

/**
 * return @void
 */
function fn_leanpay_delete_payment_processors()
{
    db_query("DELETE FROM ?:payment_descriptions WHERE payment_id IN (SELECT payment_id FROM ?:payments WHERE processor_id IN (SELECT processor_id FROM ?:payment_processors WHERE processor_script IN ('leanpay_payment_processor.php')))");
    db_query("DELETE FROM ?:payments WHERE processor_id IN (SELECT processor_id FROM ?:payment_processors WHERE processor_script IN ('leanpay_payment_processor.php'))");
    db_query("DELETE FROM ?:payment_processors WHERE processor_script IN ('leanpay_payment_processor.php')");
}

?>