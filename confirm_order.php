<?php
session_start();
include 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$cart_items = [];
$sql = "SELECT c.id, p.name, p.price, p.image_url, c.quantity, p.id AS product_id
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
} else {
    // If the cart is empty, redirect to the cart page
    header("Location: cart.php");
    exit();
}

// Process order when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Calculate total price
    $total_price = 0;
    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // Set default status to 'pending' for the new order
    $status = 'pending';  // Default status value

    // Insert order into the orders table
    $order_sql = "INSERT INTO orders (user_id, name, email, phone, address, total_price, status) 
                  VALUES ($user_id, '$name', '$email', '$phone', '$address', $total_price, '$status')";
    if ($conn->query($order_sql) === TRUE) {
        $order_id = $conn->insert_id;

        // Insert order items into the order_items table
        foreach ($cart_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $subtotal = $price * $quantity;

            // Check if the product exists in the products table
            $check_product_sql = "SELECT id FROM products WHERE id = $product_id";
            $product_result = $conn->query($check_product_sql);
            if ($product_result->num_rows > 0) {
                // Insert the item into the order_items table
                $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal)
                                   VALUES ($order_id, $product_id, $quantity, $price, $subtotal)";
                $conn->query($order_item_sql);
            } else {
                // If the product doesn't exist, stop the process
                echo "Error: Product ID $product_id not found in the products table.";
                exit();
            }
        }

        // Clear the cart after successful order creation
        $clear_cart_sql = "DELETE FROM cart WHERE user_id = $user_id";
        $conn->query($clear_cart_sql);

        // Redirect to the order confirmation page
        header("Location: order_confirmation.php?order_id=$order_id");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!-- HTML for the order confirmation page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order | Glamour Gems</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-white">
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Confirm Your Order</h2>
        <form action="confirm_order.php" method="POST">
            <div class="space-y-4">
                <label for="name" class="block text-lg">Name</label>
                <input type="text" id="name" name="name" required class="w-full p-2 border rounded-md">

                <label for="email" class="block text-lg">Email</label>
                <input type="email" id="email" name="email" required class="w-full p-2 border rounded-md">

                <label for="phone" class="block text-lg">Phone</label>
                <input type="text" id="phone" name="phone" required class="w-full p-2 border rounded-md">

                <label for="address" class="block text-lg">Shipping Address</label>
                <textarea id="address" name="address" required class="w-full p-2 border rounded-md"></textarea>

                <button type="submit" class="bg-primary text-white px-8 py-3 rounded-md">Place Order</button>
            </div>
        </form>
    </div>
</body>
</html>
