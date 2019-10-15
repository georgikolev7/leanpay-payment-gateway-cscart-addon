<?php

use Tygh\Registry;
use Tygh\Settings;
use Tygh\Http;

if (!defined('AREA')) {
    die('Access denied');
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