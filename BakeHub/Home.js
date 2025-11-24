document.addEventListener('DOMContentLoaded', () => {

    // --- 1. SECURITY & PERSONALIZATION CHECK ---
    fetch('get_user_data.php')
        .then(response => response.json())
        .then(data => {
            if (!data.logged_in) {
                window.location.href = 'Login.html';
            } else {
                const nameSpan = document.getElementById('user-name-display');
                if (nameSpan) {
                    nameSpan.textContent = data.name;
                }
            }
        })
        .catch(error => console.error('Error checking session:', error));


    // --- 2. CATEGORY FILTER LOGIC (UPDATED) ---
    const categoryFilters = document.querySelectorAll('.category-item');
    const productCards = document.querySelectorAll('.product-card');
    const sectionTitle = document.querySelector('.featured h2');

    categoryFilters.forEach(filter => {
        filter.addEventListener('click', () => {
            
            const filterValue = filter.getAttribute('data-filter');
            
            // Update Title
            if (filterValue === 'all') {
                sectionTitle.textContent = "All"; 
            } else {
                const categoryName = filter.querySelector('p').textContent;
                sectionTitle.textContent = categoryName;
            }

            // Filter Cards
            productCards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');

                if (filterValue === 'all') {
                    // --- CHANGE IS HERE: Show EVERYTHING ---
                    card.style.display = 'block'; 
                } else {
                    // Specific category shows EVERYTHING in that category
                    if (filterValue === cardCategory) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });


    // --- 3. CART LOGIC ---
    const cartLink = document.getElementById('cart-link'); 
    const cartSidebar = document.getElementById('cart-sidebar');
    const closeCartBtn = document.getElementById('close-cart');
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const cartItemsContainer = document.getElementById('cart-items-container');
    const cartTotalEl = document.getElementById('cart-total');
    const cartCountEl = document.getElementById('cart-count');

    let cart = [];

    // Open cart
    if (cartLink) {
        cartLink.addEventListener('click', (e) => {
            e.preventDefault();
            cartSidebar.classList.add('active');
        });
    }

    // Close cart
    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', () => {
            cartSidebar.classList.remove('active');
        });
    }

    // Add to cart buttons
    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productCard = button.closest('.product-card');
            const name = productCard.querySelector('h3').innerText;
            const price = productCard.querySelector('.price').innerText;
            const imageSrc = productCard.querySelector('img').src;

            const item = {
                name: name,
                price: price,
                imageSrc: imageSrc
            };

            cart.push(item);
            renderCartItems();
        });
    });

    // Remove from cart
    if (cartItemsContainer) {
        cartItemsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-from-cart')) {
                const name = e.target.closest('.cart-item').dataset.name;
                const itemIndex = cart.findIndex(item => item.name === name);

                if (itemIndex > -1) {
                    cart.splice(itemIndex, 1);
                }
                renderCartItems();
            }
        });
    }


    // --- 4. ORDER NOW LOGIC ---
    const orderNowBtn = document.querySelector('.order-now-btn');
    const orderOverlay = document.getElementById('orderOverlay');
    const closePopupBtn = document.getElementById('closePopupBtn');

    if (orderNowBtn) {
        orderNowBtn.addEventListener('click', () => {
            
            if (cart.length === 0) {
                alert("Your cart is empty! Please add items first.");
                return;
            }

            const itemNames = cart.map(item => item.name).join(', ');
            
            let totalAmount = 0;
            cart.forEach(item => {
                totalAmount += parseFloat(item.price.replace('₹', ''));
            });

            // Send data to PHP
            fetch('place_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    items: itemNames,
                    total: totalAmount
                })
            })
            .then(response => response.text()) 
            .then(text => {
                try {
                    const result = JSON.parse(text);
                    
                    if (result.success) {
                        cart = [];
                        renderCartItems();
                        cartSidebar.classList.remove('active'); 
                        orderOverlay.classList.add('show'); 
                    } else {
                        alert("Server Error: " + result.message);
                    }
                } catch (e) {
                    console.error("PHP Crash:", text);
                    alert("Order error. Check Console.");
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
            });
        });
    }

    // Close Popup Logic
    if (closePopupBtn) {
        closePopupBtn.addEventListener('click', () => {
            orderOverlay.classList.remove('show');
        });
    }

    if (orderOverlay) {
        orderOverlay.addEventListener('click', (e) => {
            if (e.target === orderOverlay) {
                orderOverlay.classList.remove('show');
            }
        });
    }


    // --- 5. RENDER FUNCTION ---
    function renderCartItems() {
        cartItemsContainer.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p style="text-align:center; color: #888;">Your cart is empty.</p>';
        } else {
             cart.forEach(item => {
                const cartItem = document.createElement('div');
                cartItem.classList.add('cart-item');
                cartItem.dataset.name = item.name; 

                cartItem.innerHTML = `
                    <img src="${item.imageSrc}" alt="${item.name}">
                    <div class="cart-item-info">
                        <h4>${item.name}</h4>
                        <p>${item.price}</p>
                    </div>
                    <i class="fas fa-times remove-from-cart"></i>
                `;
                cartItemsContainer.appendChild(cartItem);
                total += parseFloat(item.price.replace('₹', ''));
            });
        }
        if(cartTotalEl) cartTotalEl.innerText = `₹${total.toFixed(2)}`;
        if(cartCountEl) cartCountEl.innerText = cart.length;
    }

    // Initial render
    renderCartItems();

});