<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

fn_define('LEANPAY_DEFAULT_GROUP_NAME', 'Client Max Interest 6.95%, Vendor Max Interest 0%');

fn_register_hooks(
    'gather_additional_products_data_post',
    'get_product_data_post',
    'prepare_checkout_payment_methods',
    'checkout_select_default_payment_method',
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
