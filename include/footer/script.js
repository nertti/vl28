// Ждем загрузки DOM
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик клика по кнопке избранного
    document.querySelectorAll('.favor').forEach(element => {
        element.addEventListener('click', function(e) {
            var favorID = this.dataset.item;
            var doAction = this.classList.contains('active') ? 'delete' : 'add';

            addFavorite(favorID, doAction);
        });
    });
});

// Функция добавления в избранное
function addFavorite(id, action) {
    const param = new FormData();
    param.append('id', id);
    param.append('action', action);

    fetch('/ajax/favorite.php', {
        method: 'POST',
        body: param
    })
        .then(response => response.json())
        .then(result => {
            const favorElement = document.querySelector(`.favor[data-item="${id}"]`);
            console.log(result)
            if (result === 1) {
                favorElement.classList.add('active');
                updateWishCount(true);
            } else if (result === 2) {
                favorElement.classList.remove('active');
                updateWishCount(false);
            }
            location.reload(); // Перезагружаем страницу

        })
        .catch(error => console.error('Ошибка:', error));
}

// Функция обновления счетчика
function updateWishCount(increment = true) {
    const countElement = document.querySelector('#want .col');
    if (!countElement) return;

    let wishCount = parseInt(countElement.textContent);
    wishCount += increment ? 1 : -1;
    countElement.textContent = wishCount;
}