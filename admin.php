<?php
session_start();
include 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
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
<body class="bg-gray-50">

    <div class="min-h-screen flex flex-col">

        <!-- Navbar -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-primary">Admin Panel</h1>
                <a href="logout.php" class="text-red-500 hover:underline">Logout</a>
            </div>
        </header>

        <!-- Content -->
        <div class="container mx-auto px-4 py-8">

            <!-- Product Management Section -->
            <section class="bg-white p-6 rounded-lg shadow-lg mb-8">
                <h2 class="text-3xl font-semibold text-primary mb-6">Manage Products</h2>
                
                <!-- Add Product Form -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-primary mb-4">Add New Product</h3>
                    <form action="add_product.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="text" name="name" placeholder="Product Name" class="w-full px-4 py-2 border rounded-lg">
                        <input type="text" name="price" placeholder="Price" class="w-full px-4 py-2 border rounded-lg">
                        <input type="file" name="image" class="w-full px-4 py-2 border rounded-lg">
                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg w-full">Add Product</button>
                    </form>
                </div>

                <!-- Product List -->
                <div class="overflow-x-auto">
                    <h3 class="text-xl font-semibold text-primary mb-4">Product List</h3>
                    <table class="w-full bg-white table-auto shadow-md rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-left">Price</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr class="border-b">
                                <td class="px-4 py-2"><?php echo $product['id']; ?></td>
                                <td class="px-4 py-2"><?php echo $product['name']; ?></td>
                                <td class="px-4 py-2">$<?php echo number_format($product['price'], 2); ?></td>
                                <td class="px-4 py-2">
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a> |
                                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Order Management Section -->
            <section class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-3xl font-semibold text-primary mb-6">Manage Orders</h2>
                
                <!-- Order List -->
                <div class="overflow-x-auto">
                    <h3 class="text-xl font-semibold text-primary mb-4">Order List</h3>
                    <table class="w-full bg-white table-auto shadow-md rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Order ID</th>
                                <th class="px-4 py-2 text-left">Customer</th>
                                <th class="px-4 py-2 text-left">Total Price</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr class="border-b">
                                <td class="px-4 py-2"><?php echo $order['id']; ?></td>
                                <td class="px-4 py-2"><?php echo $order['name']; ?></td>
                                <td class="px-4 py-2">$<?php echo number_format($order['total_price'], 2); ?></td>
                                <td class="px-4 py-2"><?php echo ucfirst($order['status']); ?></td>
                                <td class="px-4 py-2">
                                    <a href="update_order.php?id=<?php echo $order['id']; ?>" class="text-blue-500 hover:text-blue-700">Update</a> |
                                    <a href="delete_order.php?id=<?php echo $order['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

    </div>
</body>
</html>
