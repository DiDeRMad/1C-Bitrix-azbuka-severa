document.addEventListener("DOMContentLoaded", function () {
    // Функция для инициализации кнопок добавления в корзину
    function initAddToCartButtons() {
        document.querySelectorAll(".add-to-cart").forEach(function (button) {
            button.addEventListener("click", function (e) {
                e.preventDefault();
                const productId = this.dataset.productId; // ID товара
                const quantity = 1; // Количество товара
                // AJAX-запрос для добавления товара
                BX.ajax({
                    url: '/bitrix/components/bitrix/catalog.add.to.cart/ajax.php', // URL для обработки добавления
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        sessid: BX.bitrix_sessid(),
                        action: 'add',
                        product_id: productId,
                        quantity: quantity
                    },
                    onsuccess: function (response) {
                        if (response.STATUS === 'OK') {
                            updateBasket(); // Если товар добавлен, обновляем корзину
                        } else {
                            alert('Ошибка добавления товара в корзину.');
                        }
                    },
                    onfailure: function () {
                        alert('Ошибка связи с сервером.');
                    }
                });
            });
        });
    }
    // Функция для обновления корзины
    function updateBasket() {
        BX.ajax({
            url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php', // URL для обновления корзины
            method: 'POST',
            dataType: 'html',
            data: {
                sessid: BX.bitrix_sessid(),
                site_id: BX.message('SITE_ID'),
                basketAction: 'recalculate'
            },
            onsuccess: function (html) {
                const basketContainer = document.querySelector('.basket-container');
                if (basketContainer) {
                    basketContainer.innerHTML = html; // Обновляем содержимое корзины
                }
            },
            onfailure: function () {
                alert('Ошибка обновления корзины.');
            }
        });
    }
    // Запускаем инициализацию кнопок добавления в корзину
    initAddToCartButtons();
});