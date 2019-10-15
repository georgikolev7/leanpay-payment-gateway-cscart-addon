<?php

use Tygh\Registry;
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update' && $_REQUEST['addon'] == 'leanpay_payment' && (!empty($_REQUEST['leanpay_settings']) || !empty($_REQUEST['leanpay_logo_image_data']))) {
        $leanpay_settings = isset($_REQUEST['leanpay_settings']) ? $_REQUEST['leanpay_settings'] : array();

        fn_leanpay_payment_update_leanpay_settings($leanpay_settings);
    }
}

if ($mode == 'update') {
    if ($_REQUEST['addon'] == 'leanpay_payment') {
        Tygh::$app['view']->assign('leanpay_settings', fn_leanpay_payment_get_leanpay_settings());
    }
}
