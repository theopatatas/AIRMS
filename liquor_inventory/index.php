<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquor Inventory Management</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <div class="container">
        <h1>Alcohol Inventory Record Management System</h1>

        <?php include 'inventory.php'; ?>
            
        <!-- Form to add/update liquor -->
        <form id="inventory-form" action="index.php" method="POST">
            <input type="hidden" name="id" id="id" value="<?php echo isset($editData['id']) ? $editData['id'] : ''; ?>">
            <input type="text" name="liquor_name" id="liquor-name" placeholder="Liquor Name" required value="<?php echo isset($editData['liquor_name']) ? htmlspecialchars($editData['liquor_name']) : ''; ?>">
            <input type="text" name="brand" id="brand" placeholder="Brand" required value="<?php echo isset($editData['brand']) ? htmlspecialchars($editData['brand']) : ''; ?>">
            <input type="number" name="quantity" id="quantity" placeholder="Quantity" required min="1" value="<?php echo isset($editData['quantity']) ? $editData['quantity'] : ''; ?>">
            <input type="text" name="location" id="location" placeholder="Location" required value="<?php echo isset($editData['location']) ? htmlspecialchars($editData['location']) : ''; ?>">
            <button type="submit">Add Liquor</button>
        </form>

        <h2>Inventory List</h2>
        <table id="inventory-table">
            <thead>
                <tr>
                    <th>Liquor Name</th>
                    <th>Brand</th>
                    <th>Quantity</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($result) && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["liquor_name"]) . "</td>
                                <td>" . htmlspecialchars($row["brand"]) . "</td>
                                <td>" . $row["quantity"] . "</td>
                                <td>" . htmlspecialchars($row["location"]) . "</td>
                                <td>
                                    <a href='index.php?edit=" . $row["id"] . "'>Edit</a> |
                                    <a href='index.php?delete=" . $row["id"] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>