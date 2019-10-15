<?php

use Tygh\Registry;
use Tygh\Http;
use Tygh\Session;

// Preventing direct access to the script, because it must be included by the "include" directive. The "BOOTSTRAP" constant is declared during system initialization.
defined('BOOTSTRAP') or die('Access denied');

// Here are two different contexts for running the script.
if (defined('PAYMENT_NOTIFICATION')) {
    

} else {

    $cart = &Tygh::$app['session']['cart'];
    $auth = \Tygh::$app['session']['auth'];

    try {

        

    } catch (Exception $exception) {
        fn_set_notification('E', __('Error'), $exception->getMessage());
    }

    fn_print_r("Sending data");

}