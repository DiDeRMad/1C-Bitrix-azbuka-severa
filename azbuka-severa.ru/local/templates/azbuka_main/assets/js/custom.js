window.digiLayer = {
    cartState: function() {
		return new Promise((resolve, reject) => {
			$.ajax({
				url: '/local/ajax/updatesmallbasket.php',
				method: 'post',
				dataType: 'json',
				data: { mode: 'cartState' },
				success: function(data) {
					resolve(data); // data в формате { "offer_id": amount }
				},
				error: function(error) {
					reject(error);
				}
			});
		});
	},
    addToCart: function(offer_id, amount = 1) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/local/ajax/addbasketitem.php',
                method: 'post',
                dataType: 'json',
                data: { productId: offer_id, quantity: amount },
                success: function(response) {
                    window.digiLayer.cartState().then((state) => {
                        // Обновляем интерфейс корзины (например, счетчик)
                        updateCartUI(state);
                        resolve({ result: true });
                    });
                },
                error: function(error) {
                    reject(error);
                }
            });
        });
    },
	removeFromCart: function(offer_id) {
		return new Promise((resolve, reject) => {
			$.ajax({
				url: '/local/ajax/changequantity.php',
				method: 'post',
				dataType: 'json',
				data: { productId: offer_id, quantity: 0, action: 'remove' },
				success: function(response) {
					window.digiLayer.cartState().then((state) => {
						updateCartUI(state);
						resolve({ result: true });
					});
				},
				error: function(error) {
					reject(error);
				}
			});
		});
	},
    whishlistState: function() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/local/ajax/wishliststate.php',
                method: 'post',
                dataType: 'json',
                success: function(data) {
                    resolve(data); // data в формате массива [offer_id, offer_id]
                },
                error: function(error) {
                    reject(error);
                }
            });
        });
    },
    addToWishlist: function(offer_id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/local/ajax/wishlist.php',
                method: 'post',
                data: { itemId: offer_id },
                success: function(response) {
                    resolve({ result: true });
                },
                error: function(error) {
                    reject(error);
                }
            });
        });
    },
    removeFromWishlist: function(offer_id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/local/ajax/wishlist.php',
                method: 'post',
                data: { itemId: offer_id, remove: true },
                success: function(response) {
                    resolve({ result: true });
                },
                error: function(error) {
                    reject(error);
                }
            });
        });
    }
};
