<?php

use Tygh\Registry;
use Tygh\Settings;
use Tygh\Http;

if (!defined('AREA')) {
    die('Access denied');
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
    if ((round($cart['total']) < 200) || (round($cart['total']) > 3000))
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
 * @param $unipaySettings
 * @param $orderInfo
 * @throws Exception
 */
function fn_leanpay_payment_validate($unipaySettings, $orderInfo){    
    
    if (empty($unipaySettings['api_key']) && empty($unipaySettings['api_demo_key'])) {
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