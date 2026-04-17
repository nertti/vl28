<?php

use Bitrix\Sale\DiscountCouponsManager;
DiscountCouponsManager::clear(true);

file_put_contents(
    '/local/log_beacon.txt',
    date('Y-m-d H:i:s') . " beacon called\n",
    FILE_APPEND
);
echo json_encode(['status' => 'success']);