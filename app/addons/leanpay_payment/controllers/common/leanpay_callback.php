<?php

use Tygh\Registry;
use Tygh\Http;
use Tygh\Session;

try {

    file_put_contents('leanpay_callback.txt', print_r($_REQUEST, true), FILE_APPEND);

} catch (Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), $e->getCode();
}

exit();
