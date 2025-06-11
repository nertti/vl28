<?php

class OrderHandler
{
    public function onOrderPaid($order_id, $arFields)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/log.txt', print_r($arFields, 1), FILE_APPEND);
    }
}
