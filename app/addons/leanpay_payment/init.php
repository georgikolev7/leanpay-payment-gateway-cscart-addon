<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

fn_register_hooks(
    'prepare_checkout_payment_methods',
    'send_request',
    'parse_items',
    'add_shipment_in_items',
    'get_leanpay_checkout_url',
    'get_back_link',
    'validate',
    'built_request_data',
    'attached_items',
    'get_leanpay_settings',
    'update_leanpay_settings',
    'leanpay_get_logo_id',
    'get_order_by_timestamp'
);
