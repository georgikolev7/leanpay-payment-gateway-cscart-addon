<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'fetch') {

    // Get all current LeanPay settings
    $leanpay_settings = fn_leanpay_payment_get_leanpay_settings();
    
    // Stop the process if API is in demo mode
    if ($leanpay_settings['leanpay_demo'] == 'Y') exit();
    
    // Fetch all installments from LeanPay API
    $response = fn_leanpay_payment_get_installments($leanpay_settings);
    
    if (!empty($response['groups']))
    {
        $groups = $response['groups'];
        
        foreach ($groups as $group) {
            $group_id = fn_leanpay_payment_process_installment_group($group);
        }
    }
    
    exit();
}