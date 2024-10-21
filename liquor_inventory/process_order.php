<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $liquor_ids = $_POST['liquor_id'];
    $order_quantities = $_POST['order_quantity'];

    $insufficientStock = false;
    $errors = [];
    
    // Loop each liquor item
    foreach ($liquor_ids as $index => $liquor_id) {
        $quantity_requested = $order_quantities[$index];

        // Check stock availability for the liquor in the database
        $sql = "SELECT quantity, liquor_name FROM inventory WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $liquor_id);
        $stmt->execute();
        $stmt->bind_result($stock_quantity, $liquor_name);
        $stmt->fetch();
        $stmt->close();

        if ($stock_quantity < $quantity_requested) {
            // If there is not enough stock add to errors and set the flag
            $insufficientStock = true;
            $errors[] = $liquor_name . " only has " . $stock_quantity . " units in stock, but you ordered " . $quantity_requested . ".";
        }
    }

    // If there is insufficient stock show the error and stop the order
    if ($insufficientStock) {
        echo "<h3>Order Failed</h3>";
        echo "<p>There is not enough stock for the following items:</p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        exit;
    }

    // If stock is sufficient proceed with order placement
    foreach ($liquor_ids as $index => $liquor_id) {
        $quantity_ordered = $order_quantities[$index];

        // Reduce stock in inventory
        $sql = "UPDATE inventory SET quantity = quantity - ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity_ordered, $liquor_id);
        $stmt->execute();
        $stmt->close();

        // Insert order details into the orders table
        $sql = "INSERT INTO orders (liquor_id, quantity_ordered) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $liquor_id, $quantity_ordered);
        $stmt->execute();
        $stmt->close();
    }

    echo "<h3>Order Successful</h3>";
    echo "<p>Your order has been placed successfully.</p>";
}

$conn->close();
?>
