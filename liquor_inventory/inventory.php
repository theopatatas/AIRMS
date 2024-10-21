<?php
include 'db_connection.php';

// Handle form submission (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
    $liquor_name = $conn->real_escape_string(trim($_POST['liquor_name']));
    $brand = $conn->real_escape_string(trim($_POST['brand']));
    $quantity = (int)$_POST['quantity'];
    $location = $conn->real_escape_string(trim($_POST['location']));

    if ($id) {
        // Update existing item
        $sql = "UPDATE inventory SET liquor_name=?, brand=?, quantity=?, location=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssisi', $liquor_name, $brand, $quantity, $location, $id);
    } else {
        // Insert new item
        $sql = "INSERT INTO inventory (liquor_name, brand, quantity, location) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssis', $liquor_name, $brand, $quantity, $location);
    }

    if ($stmt->execute()) {
        // Redirect to the same page after successful submission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit; // Ensure script stops executing after the redirect
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Handle delete operation
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM inventory WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        // Redirect to avoid accidental resubmission on page reload
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Retrieve the inventory data from the database
$sql = "SELECT id, liquor_name, brand, quantity, location FROM inventory";
$result = $conn->query($sql);

// Fetch the data for editing
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $sql = "SELECT id, liquor_name, brand, quantity, location FROM inventory WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $editData = $result->fetch_assoc();
    }
    $stmt->close();
}

$conn->close();
?>