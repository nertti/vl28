/* смена торговых предложений */
document.addEventListener('DOMContentLoaded', function () {

    const articleBlock = document.getElementById('productArticle');
    const priceBlock = document.getElementById('productPrice');
    const oldPriceBlock = document.getElementById('productOldPrice');

    document.querySelectorAll('.product-size-item').forEach(item => {

        item.addEventListener('click', function () {

            document.querySelectorAll('.product-size-item')
                .forEach(el => el.classList.remove('active'));

            this.classList.add('active');

            currentOfferId = this.dataset.offerId;

            if (articleBlock) {
                articleBlock.innerHTML =
                    'Артикул: ' + this.dataset.article;
            }

            if (priceBlock) {
                priceBlock.innerHTML = this.dataset.price;
            }

            if (oldPriceBlock) {

                oldPriceBlock.innerHTML = this.dataset.basePrice;

                if (
                    Number(this.dataset.priceValue) >=
                    Number(this.dataset.basePriceValue)
                ) {
                    oldPriceBlock.style.display = 'none';
                } else {
                    oldPriceBlock.style.display = '';
                }
            }

        });

    });

});

document.addEventListener('DOMContentLoaded', function () {

    const addToBasket = document.querySelector('#addToBasket');

    if (!addToBasket) {
        return;
    }

    const basketModal = new HystModal({
        linkAttributeName: 'data-hystmodal',

        beforeOpen() {

            const scrollbarWidth =
                window.innerWidth -
                document.documentElement.clientWidth;

            document.body.style.paddingRight =
                scrollbarWidth + 'px';
        },

        afterClose() {

            document.body.style.paddingRight = '';
        }
    });

    let isLoading = false;

    addToBasket.addEventListener('click', handleFormAddToBasket);

    function handleFormAddToBasket(event) {

        event.preventDefault();

        if (isLoading) {
            return;
        }

        isLoading = true;

        addToBasket.disabled = true;

        addToBasket.innerHTML = `
<span>Добавляем...</span>
`;

        const data = new FormData();

        data.append('PRODUCT_ID', currentOfferId);

        fetch('/ajax/addInBasket.php', {
            method: 'POST',
            body: data
        })
            .then(response => response.json())
            .then(result => {

                if (result.status === 'error') {

                    basketModal.open('#errorBasketModal');

                } else {

                    basketModal.open('#addBasketModal');

                    updateBasketCounter(result.count);
                }

            })
            .catch(error => {

                console.error(
                    'Ошибка при добавлении товара в корзину:',
                    error
                );

                basketModal.open('#errorBasketModal');

            })
            .finally(() => {

                isLoading = false;

                addToBasket.disabled = false;

                addToBasket.innerHTML = `
<span>Добавить в корзину</span>
    `;
            });
    }

    function updateBasketCounter(count) {

        const basketCounter =
            document.querySelector('#basket .col');

        if (!basketCounter) {
            return;
        }

        if (count !== undefined) {
            basketCounter.textContent = count;
        }
    }

});


/* добавление / удаление избранного */
document.addEventListener('DOMContentLoaded', function () {

    const favoriteModal = new HystModal({
        linkAttributeName: 'data-hystmodal',

        beforeOpen() {
            const scrollbarWidth =
                window.innerWidth - document.documentElement.clientWidth;

            document.body.style.paddingRight = scrollbarWidth + 'px';
        },

        afterClose() {
            document.body.style.paddingRight = '';
        }
    });

    document.querySelectorAll('.favor').forEach(button => {

        button.addEventListener('click', function (e) {

            e.preventDefault();

            const productId = this.dataset.item;

            toggleFavorite(productId, favoriteModal);

        });

    });

});

function toggleFavorite(productId, favoriteModal) {

    const formData = new FormData();

    formData.append('id', productId);

    fetch('/ajax/catalog.element/favorite.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {

            if (!result.success) {
                return;
            }

            const buttons = document.querySelectorAll(
                `.favor[data-item="${productId}"]`
            );

            if (result.action === 'add') {

                buttons.forEach(button => {
                    button.classList.add('active');
                });

                favoriteModal.open('#addFavoriteModal');

            } else {

                buttons.forEach(button => {
                    button.classList.remove('active');
                });

                favoriteModal.open('#delFavoriteModal');
            }

            updateFavoriteCounter(result.count);

        })
        .catch(error => {
            console.error('Favorite error:', error);
        });
}

function updateFavoriteCounter(count) {

    const counter = document.querySelector('#want .col');

    if (!counter) {
        return;
    }

    counter.textContent = count;
}
