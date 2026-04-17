<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Sale\DiscountCouponsManager;

header('Content-Type: application/json');

Loader::includeModule('sale');
Loader::includeModule('catalog');

// Инициализируем глобального пользователя
global $USER;

$data = json_decode(file_get_contents('php://input'), true);
$promoCode = trim($data['promo'] ?? '');

if (!$promoCode) {
    echo json_encode(['status' => 'error', 'message' => 'Введите промокод']);
    exit;
}

// ОПРЕДЕЛЯЕМ КТО ДЕЛАЕТ ЗАКАЗ
// Если пользователь авторизован, берем его ID, иначе ID анонимной корзины
$userId = ($USER instanceof CUser && $USER->IsAuthorized()) ? $USER->GetID() : \Bitrix\Sale\Fuser::getId();
$siteId = \Bitrix\Main\Context::getCurrent()->getSite();

if (!$siteId) $siteId = 's1'; // Страховка для SITE_ID

// 1. Сначала купон
DiscountCouponsManager::clear(true);
DiscountCouponsManager::add($promoCode);

// 2. Загружаем корзину именно для этого пользователя
$fUserId = \Bitrix\Sale\Fuser::getId();
$basket = Sale\Basket::loadItemsForFUser($fUserId, $siteId);
$basket->refreshData(['PRICE', 'COUPON']);

// 3. Создаем заказ С УКАЗАНИЕМ КОРРЕКТНОГО USER_ID
// Это критично для проверки ограничений по группам пользователей!
$order = Sale\Order::create($siteId, $userId); 
$order->setBasket($basket);

// 4. Расчет
$discounts = $order->getDiscount();
$discounts->calculate();
$res = $discounts->getApplyResult(true);

$isApplied = false;
if (!empty($res['COUPON_LIST'][$promoCode])) {
    if ($res['COUPON_LIST'][$promoCode]['APPLY'] === 'Y') {
        $isApplied = true;
    }
}

if (!$isApplied) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Промокод не применим для вашей группы пользователя или состава корзины',
        'debug' => [
            'USER_ID' => $userId,
            'GROUPS' => ($USER instanceof CUser) ? $USER->GetUserGroupArray() : 'none',
            'COUPON_RES' => $res['COUPON_LIST'][$promoCode] ?? 'not_found'
        ]
    ]);
    exit;
}

// 5. Итог
$discountSum = 0;
foreach ($res['PRICES']['BASKET'] as $item) {
    $discountSum += $item['DISCOUNT'];
}

echo json_encode([
    'status' => 'success',
    'discount' => round($discountSum),
    'is_auth' => $USER->IsAuthorized() ? 'Y' : 'N'
]);
