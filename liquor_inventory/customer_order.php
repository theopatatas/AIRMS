<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquor Ordering</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap">
    <link rel="stylesheet" href="CSS/order.css">
    <script>

const cart = [];

// add selected alcohol to the cart
function addToCart(button) {
    const liquorItem = button.parentElement;
    const select = liquorItem.querySelector('.select-liquor');
    const quantityInput = liquorItem.querySelector('.input-quantity');

    const liquorId = select.value;
    const liquorName = select.options[select.selectedIndex].text;
    const quantity = parseInt(quantityInput.value);

    if (liquorId && quantity > 0) {
        const existingItem = cart.find(item => item.id === liquorId);

        if (existingItem) {
            existingItem.quantity += quantity; // Update quantity if already in cart
        } else {
            cart.push({ id: liquorId, name: liquorName, quantity }); // Add new item to the cart
        }

        updateCartDisplay(); // Update the cart display
    }
}

// Update the cart display
function updateCartDisplay() {
    const cartContainer = document.getElementById('cart');
    const orderForm = document.getElementById('order-form');
    cartContainer.innerHTML = '';

    // Clear previous hidden inputs
    const previousInputs = document.querySelectorAll('.cart-input');
    previousInputs.forEach(input => input.remove());

    if (cart.length === 0) {
        cartContainer.innerHTML = '<p>Your cart is empty.</p>';
        return;
    }

    cart.forEach(item => {
        const cartItem = document.createElement('div');
        cartItem.classList.add('cart-item');
        cartItem.innerHTML = `${item.name} (Quantity: ${item.quantity})`;
        cartContainer.appendChild(cartItem);

        // Add hidden inputs for each item in the cart
        const hiddenIdInput = document.createElement('input');
        hiddenIdInput.type = 'hidden';
        hiddenIdInput.name = 'liquor_id[]';
        hiddenIdInput.value = item.id;
        hiddenIdInput.classList.add('cart-input');

        const hiddenQuantityInput = document.createElement('input');
        hiddenQuantityInput.type = 'hidden';
        hiddenQuantityInput.name = 'order_quantity[]';
        hiddenQuantityInput.value = item.quantity;
        hiddenQuantityInput.classList.add('cart-input');

        orderForm.appendChild(hiddenIdInput);
        orderForm.appendChild(hiddenQuantityInput);
    });
}

// Add an initial liquor selection field when the page loads
window.onload = function() {
    addLiquorSelection();
};

// Function to add another liquor selection field
function addLiquorSelection() {
    const liquorSelections = document.getElementById('liquor-selections');
    const newSelection = document.createElement('div');
    newSelection.classList.add('card', 'liquor-item');

    newSelection.innerHTML = `
        <select name="liquor_id[]" class="select-liquor" required>
            <option value="">Select Liquor</option>
            <?php
            include 'db_connection.php';
            $sql = "SELECT id, liquor_name, brand, quantity FROM inventory WHERE quantity > 0";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['liquor_name']) . " (" . htmlspecialchars($row['brand']) . ") - Available: " . $row['quantity'] . "</option>";
                }
            } else {
                echo "<option value=''>No liquor available</option>";
            }
            ?>
        </select>
        <input type="number" name="order_quantity[]" placeholder="Quantity" class="input-quantity" required min="1">
        <button type="button" class="add-to-cart-btn" onclick="addToCart(this)">Add to Cart</button>
    `;

    liquorSelections.appendChild(newSelection);
    applyTooltipToLiquorSelection();
}

// Function to display the full text of the selected liquor on hover
function applyTooltipToLiquorSelection() {
    const selects = document.querySelectorAll('.select-liquor');
    selects.forEach(select => {
        select.addEventListener('mouseover', function () {
            const selectedOption = this.options[this.selectedIndex];
            this.title = selectedOption.text;
        });
    });
}

document.addEventListener('DOMContentLoaded', applyTooltipToLiquorSelection);

// Prevent the default form submission behavior
document.getElementById('order-form').onsubmit = function (event) {
    event.preventDefault(); // Prevent form from submitting
    if (cart.length > 0) {
        // Optional: Clear the cart after submitting
        cart.length = 0; // Reset cart
        this.submit(); // Manually submit if the cart is not empty
    } else {
        alert('Your cart is empty. Please add items before placing an order.');
    }
};

    </script>
</head>
<body>
    <div class="container">
        <h1 class="title">Order Liquor</h1>

        <form id="order-form" action="process_order.php" method="POST">
            <div id="liquor-selections" class="liquor-selections">
                
            </div>

            <button type="submit" class="submit-btn">Place Order</button>
        </form>

        <h2 class="cart-title">Your Cart</h2>
        <div id="cart" class="cart"></div>
    </div>
</body>
</html>
