<script src="https://securepay.tinkoff.ru/html/payForm/js/tinkoff_v2.js"></script>
<form class="payform-tbank" name="payform-tbank" id="payform-tbank">
    <input class="payform-tbank-row" type="hidden" name="terminalkey" value="TBankTest">
    <input class="payform-tbank-row" type="hidden" name="frame" value="false">
    <input class="payform-tbank-row" type="hidden" name="language" value="ru">
    <input class="payform-tbank-row" type="hidden" name="receipt" value="">
    <input class="payform-tbank-row" type="text" placeholder="Сумма заказа" name="amount" required>
    <input class="payform-tbank-row" type="hidden" placeholder="Номер заказа" name="order">
    <input class="payform-tbank-row" type="text" placeholder="Описание заказа" name="description">
    <input class="payform-tbank-row" type="text" placeholder="ФИО плательщика" name="name">
    <input class="payform-tbank-row" type="email" placeholder="E-mail" name="email">
    <input class="payform-tbank-row" type="tel" placeholder="Контактный телефон" name="phone">
    <input class="payform-tbank-row payform-tbank-btn" type="submit" value="Оплатить">
</form>
<script type="text/javascript">
    const TPF = document.getElementById("payform-tbank");

    TPF.addEventListener("submit", function (e) {
        e.preventDefault();
        const {description, amount, email, phone, receipt} = TPF;

        if (receipt) {
            if (!email.value && !phone.value)
                return alert("Поле E-mail или Phone не должно быть пустым");

            TPF.receipt.value = JSON.stringify({
                "EmailCompany": "mail@mail.com",
                "Taxation": "patent",
                "FfdVersion": "1.2",
                "Items": [
                    {
                        "Name": description.value || "Оплата",
                        "Price": Math.round(amount.value * 100),
                        "Quantity": 1.00,
                        "Amount": Math.round(amount.value * 100),
                        "PaymentMethod": "full_prepayment",
                        "PaymentObject": "service",
                        "Tax": "none",
                        "MeasurementUnit": "pc"
                    }
                ]
            });
        }
        pay(TPF);
    })
</script>
<style>
    .payform-tbank {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        margin: 2px auto;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        max-width: 250px;
    }
    .payform-tbank-row {
        margin: 2px;
        border-radius: 4px;
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        -webkit-transition: 0.3s;
        -o-transition: 0.3s;
        transition: 0.3s;
        border: 1px solid #DFE3F3;
        padding: 15px;
        outline: none;
        background-color: #DFE3F3;
        font-size: 15px;
    }
    .payform-tbank-row:focus {
        background-color: #FFFFFF;
        border: 1px solid #616871;
        border-radius: 4px;
    }
    .payform-tbank-btn {
        background-color: #FBC520;
        border: 1px solid #FBC520;
        color: #3C2C0B;
    }
    .payform-tbank-btn:hover {
        background-color: #FAB619;
        border: 1px solid #FAB619;
    }
</style><?php
