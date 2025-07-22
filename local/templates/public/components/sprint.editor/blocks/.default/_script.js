/* Общие скрипты для блоков */

/*accordion*/
document.addEventListener("DOMContentLoaded", function (e) {
    let titles = document.getElementsByClassName("sp-accordion-title");
    for (let titleIndex = 0; titleIndex < titles.length; titleIndex++) {
        if (!titles[titleIndex].classList.contains('sp-accordion__initialized')) {
            titles[titleIndex].classList.add('sp-accordion__initialized');
            titles[titleIndex].addEventListener("click", function () {
                let panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    this.classList.remove("sp-accordion-title__active");
                    panel.style.display = "none";
                } else {
                    this.classList.add("sp-accordion-title__active");
                    panel.style.display = "block";
                }
            });
        }
    }


    const swiper = new Swiper('.swiper', {
        // Базовые настройки
        loop: true, // бесконечная прокрутка
        spaceBetween: 30, // отступ между слайдами

        // Навигация
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },

        // Пагинация
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },

        // Автопрокрутка
        autoplay: {
            delay: 5000 // смена слайда каждые 5 секунд
        }
    });
});

