<?php
//require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

?>

<style>
    body {
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        background-color: #f0f4f8;
    }

    .success-container {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 600px;
        width: 90%;
    }

    .success-icon {
        font-size: 72px;
        color: #000000;
        margin-bottom: 20px;
    }

    .success-title {
        color: #000000;
        font-size: 28px;
        margin-bottom: 15px;
    }

    .success-message {
        color: #34495E;
        font-size: 18px;
        line-height: 1.5;
        margin-bottom: 25px;
    }

    .order-details {
        background: #F8F9FA;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .details-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 5px 0;
        border-bottom: 1px solid #E9ECEF;
    }

    .details-label {
        color: #666;
        font-weight: 500;
    }

    .details-value {
        color: #222;
        font-weight: 600;
    }

    .continue-button {
        background-color: #000000;
        color: white;
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .continue-button:hover {
        background-color: #000000;
    }
</style>
<div class="success-container">
    <h1 class="success-title">Неожиданная ошибка!</h1>
    <p class="success-message">
        Просим прощения, при оплате заказа произошла ошибка, попробуйте позже
    </p>
    <div class="order-details">
        <div class="details-item">
            <span class="details-label">Номер заказа:</span>
            <span class="details-value">#<?=$orderId?></span>
        </div>
        <div class="details-item">
            <span class="details-label">Сумма:</span>
            <span class="details-value"><?=number_format($_GET['Amount'] / 100, 2, ',', ' ')?> ₽</span>
        </div>
    </div>

    <button class="continue-button" onclick="window.location.href='/catalog'">
        Вернуться в магазин
    </button>
</div>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
