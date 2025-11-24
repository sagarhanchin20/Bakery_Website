document.addEventListener('DOMContentLoaded', () => {
    
    const tableBody = document.getElementById('orders-body');
    const noOrdersMsg = document.getElementById('no-orders-message');
    const table = document.getElementById('orders-table');

    fetch('get_orders.php')
        .then(response => response.json())
        .then(data => {
            
            // 1. Check if user is logged in
            if (data.success === false && data.message === 'Not logged in') {
                window.location.href = 'Login.html';
                return;
            }

            const orders = data.orders;

            // 2. Check if there are orders
            if (orders.length === 0) {
                table.style.display = 'none';
                noOrdersMsg.style.display = 'block';
            } else {
                // 3. Loop through orders and create rows
                orders.forEach(order => {
                    
                    // Format Date (Simple JS way)
                    const dateObj = new Date(order.order_date);
                    const dateStr = dateObj.toLocaleDateString() + ' ' + dateObj.toLocaleTimeString();

                    // Format ID (Add leading zeros manually if needed, or use raw ID)
                    const orderId = '#' + String(order.order_id).padStart(4, '0');

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="order-id">${orderId}</td>
                        <td>${dateStr}</td>
                        <td>${order.items}</td>
                        <td>₹${order.total_amount}</td>
                    `;
                    tableBody.appendChild(row);
                });
            }
        })
        .catch(error => console.error('Error fetching orders:', error));
});