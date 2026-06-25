document.addEventListener('click', async function (e) {

    const favoriteBtn = e.target.closest('.favor');

    if (!favoriteBtn) {
        return;
    }

    e.preventDefault();

    const productId = favoriteBtn.dataset.item;
    const productCard = favoriteBtn.closest('.product');

    const productName = favoriteBtn.dataset.name;
    const productImage = favoriteBtn.dataset.image;

    try {

        const response = await fetch('/ajax/catalog.element/favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + productId
        });

        const result = await response.json();

        if (!result.success) {
            return;
        }

        if (result.action === 'delete') {

            document.getElementById('delFavoriteImage').src = productImage;
            document.getElementById('delFavoriteImage').alt = productName;
            document.getElementById('delFavoriteName').textContent = productName;

            const myModal = new HystModal({
                linkAttributeName: 'data-hystmodal'
            });
            myModal.open('#delFavoriteModal');

            if (productCard) {
                productCard.remove();
            }

            const products = document.querySelectorAll('.product');

            if (!products.length) {
                const emptyBlock = document.querySelector('.favorites-empty');

                if (emptyBlock) {
                    emptyBlock.style.display = 'block';
                }
            }

            const favoriteCounter = document.querySelector('.favorite-count');

            if (favoriteCounter) {
                favoriteCounter.textContent = result.count;
            }
        }

    } catch (error) {
        console.error(error);
    }

});