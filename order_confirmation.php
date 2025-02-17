<?php
session_start();
include 'config.php';

// Ensure user is logged in
$username = null;
$userRole = null;

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT name, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    if ($userData) {
        $username = $userData['name'];
        $userRole = $userData['role'];
    }
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You need to log in to view your order.";
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id']; // Order ID passed via URL

// Fetch order details
$sql = "SELECT o.id, o.total_price, o.status, o.created_at, o.updated_at, 
                o.name, o.email, o.phone, o.address 
        FROM orders o
        WHERE o.id = $order_id AND o.user_id = {$_SESSION['user_id']}";
$order_result = $conn->query($sql);
$order = $order_result->fetch_assoc();

// Fetch order items
$order_items = [];
$order_items_sql = "SELECT oi.product_id, oi.quantity, oi.price, p.name, p.image_url
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = $order_id";
$order_items_result = $conn->query($order_items_sql);

if ($order_items_result->num_rows > 0) {
    while ($row = $order_items_result->fetch_assoc()) {
        $order_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glamour Gems | Order Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        :where([class^="ri-"])::before { content: "\f3c2"; }
        body {
            font-family: 'Poppins', sans-serif;
        }
        .playfair {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="bg-white">
    <div id="app">
    <header class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="#" class="font-['Pacifico'] text-2xl text-primary">Glamour Gems</a>

                <nav class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-primary">Home</a>
                    <a href="shop.php" class="text-gray-700 hover:text-primary">Jewelry</a>
                    <a href="#" class="text-gray-700 hover:text-primary">Fashion</a>
                    <a href="#" class="text-gray-700 hover:text-primary">New Arrivals</a>
                    <a href="#" class="text-gray-700 hover:text-primary">Sale</a>

                    <?php if ($username): ?>
                        <a href="myorders.php" class="text-gray-700 hover:text-primary">Orders</a>
                    <?php endif; ?>
                </nav>

                <div class="flex items-center space-x-6">
                    <div class="relative">
                        <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 w-64 bg-gray-50 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                            <i class="ri-search-line"></i>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <?php if ($username): ?>
                            <!-- User Dropdown -->
                            <div class="relative group">
                                <button class="text-gray-700 hover:text-primary flex items-center space-x-2">
                                    <span>Welcome, <?php echo $username; ?></span>
                                    <i class="ri-arrow-down-s-line"></i>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                    <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                                    <a href="myorders.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Orders</a>
                                    <?php if ($userRole === 'admin'): ?>
                                        <a href="admin.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100">Admin Panel</a>
                                    <?php endif; ?>
                                    <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <button id="accountBtn" class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-primary relative" onclick="toggleAccountModal()">
                                <i class="ri-user-line text-xl"></i>
                            </button>
                        <?php endif; ?>

                        <button id="cartBtn" class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-primary relative" onclick="location.href='cart.php'">
                            <i class="ri-shopping-bag-line text-xl"></i>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-xs rounded-full flex items-center justify-center">3</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

        <main class="mt-16">
            <!-- Order Confirmation -->
            <section class="py-16">
                <div class="container mx-auto px-4">
                    <h2 class="playfair text-3xl font-bold text-center mb-12">Order Confirmation</h2>

                    <?php if ($order): ?>
                        <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                            <h3 class="text-2xl font-semibold mb-4">Order #<?php echo $order['id']; ?> - <?php echo ucfirst($order['status']); ?></h3>
                            <p class="text-lg">Order placed on: <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                            <p class="text-lg">Total Price: $<?php echo number_format($order['total_price'], 2); ?></p>

                            <h4 class="mt-6 text-xl font-semibold">Shipping Information</h4>
                            <p><strong>Name:</strong> <?php echo $order['name']; ?></p>
                            <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                            <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
                            <p><strong>Address:</strong> <?php echo nl2br($order['address']); ?></p>
                        </div>

                        <h3 class="text-xl font-semibold mt-8 mb-4">Order Items</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <?php foreach ($order_items as $item): ?>
                                <div class="bg-gray-50 p-4 rounded-lg shadow-md flex items-center">
                                    <img src="<?php echo $item['image_url']; ?>" alt="Product" class="w-32 h-32 object-cover rounded-lg">
                                    <div class="ml-4 flex-1">
                                        <h4 class="font-semibold text-xl"><?php echo $item['name']; ?></h4>
                                        <p class="text-gray-600">Price: $<?php echo $item['price']; ?></p>
                                        <p class="text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                                        <p class="text-gray-600">Subtotal: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-lg text-gray-600">Order not found or you do not have permission to view it. <br><a href="login.php" class="text-primary">Login to check your order.</a></p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
        <footer class="bg-gray-900 text-white py-16">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <a href="#" class="font-['Pacifico'] text-2xl text-white mb-6 block">logo</a>
                        <p class="text-gray-400">Discover timeless elegance with our curated collection of luxury jewelry and fashion pieces.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">About Us</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">FAQs</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Shipping</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium mb-4">Categories</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">Necklaces</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Rings</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Bracelets</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Earrings</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20">
                                <i class="ri-facebook-fill"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20">
                                <i class="ri-instagram-fill"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20">
                                <i class="ri-pinterest-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                    <p>&copy; 2025 Glamour Gems. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
