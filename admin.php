<?php
session_start();
include 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) ) {
    header("Location: index.php");
    exit();
}

// Fetch all products
$products = [];
$product_sql = "SELECT * FROM products";
$product_result = $conn->query($product_sql);

if ($product_result->num_rows > 0) {
    while ($row = $product_result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fetch all orders
$orders = [];
$order_sql = "SELECT orders.id, orders.user_id, users.name, orders.total_price, orders.status, orders.created_at 
              FROM orders 
              JOIN users ON orders.user_id = users.id 
              ORDER BY orders.created_at DESC";
$order_result = $conn->query($order_sql);

if ($order_result->num_rows > 0) {
    while ($row = $order_result->fetch_assoc()) {
        $orders[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Glamour Gems</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <header class="bg-white shadow-sm">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-primary">Admin Panel</h1>
                <a href="logout.php" class="text-red-500 hover:underline">Logout</a>
            </div>
        </header>

        <!-- Content -->
        <div class="container mx-auto px-4 py-8">
            <h2 class="text-3xl font-bold mb-6">Manage Products</h2>
            
            <!-- Product Management -->
            <div class="bg-white p-6 rounded-lg shadow mb-8">
                <h3 class="text-xl font-semibold mb-4">Add New Product</h3>
                <form action="add_product.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="text" name="name" placeholder="Product Name" class="w-full px-4 py-2 border rounded">
                    <input type="text" name="price" placeholder="Price" class="w-full px-4 py-2 border rounded">
                    <input type="file" name="image" class="w-full px-4 py-2 border rounded">
                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded">Add Product</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Product List</h3>
                <table class="w-full bg-white border-collapse border">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">Price</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo $product['id']; ?></td>
                            <td class="border px-4 py-2"><?php echo $product['name']; ?></td>
                            <td class="border px-4 py-2">$<?php echo number_format($product['price'], 2); ?></td>
                            <td class="border px-4 py-2">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="text-blue-500">Edit</a> |
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="text-red-500" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Order Management -->
            <h2 class="text-3xl font-bold mt-12 mb-6">Manage Orders</h2>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4">Order List</h3>
                <table class="w-full bg-white border-collapse border">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-4 py-2">Order ID</th>
                            <th class="border px-4 py-2">Customer</th>
                            <th class="border px-4 py-2">Total Price</th>
                            <th class="border px-4 py-2">Status</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo $order['id']; ?></td>
                            <td class="border px-4 py-2"><?php echo $order['name']; ?></td>
                            <td class="border px-4 py-2">$<?php echo number_format($order['total_price'], 2); ?></td>
                            <td class="border px-4 py-2"><?php echo ucfirst($order['status']); ?></td>
                            <td class="border px-4 py-2">
                                <a href="update_order.php?id=<?php echo $order['id']; ?>" class="text-blue-500">Update</a> |
                                <a href="delete_order.php?id=<?php echo $order['id']; ?>" class="text-red-500" onclick="return confirm('Delete order?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</body>
</html>
