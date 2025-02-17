<?php
session_start();
include 'config.php';

// Fetch user details if logged in
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

// Fetch order details
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
$orderDetails = [];
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$orderResult = $stmt->get_result();
if ($orderResult->num_rows > 0) {
    $orderDetails = $orderResult->fetch_assoc();
}

// Fetch products in the order
$orderProducts = [];
$sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$productResult = $stmt->get_result();
if ($productResult->num_rows > 0) {
    while ($row = $productResult->fetch_assoc()) {
        $orderProducts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | Glamour Gems</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#9f7aea',
                        secondary: '#f6e5ff'
                    },
                    borderRadius: {
                        'button': '8px'
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-white">

<header class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <a href="#" class="font-['Pacifico'] text-2xl text-primary">Glamour Gems</a>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobileMenuBtn" class="text-gray-700 hover:text-primary" onclick="toggleMobileMenu()">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </div>

            <!-- Navigation for Desktop -->
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

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden absolute left-0 right-0 top-16 bg-white border-t border-gray-200 hidden">
        <nav class="flex flex-col space-y-4 px-4 py-4">
            <a href="index.php" class="text-gray-700 hover:text-primary">Home</a>
            <a href="shop.php" class="text-gray-700 hover:text-primary">Jewelry</a>
            <a href="#" class="text-gray-700 hover:text-primary">Fashion</a>
            <a href="#" class="text-gray-700 hover:text-primary">New Arrivals</a>
            <a href="#" class="text-gray-700 hover:text-primary">Sale</a>

            <?php if ($username): ?>
                <a href="myorders.php" class="text-gray-700 hover:text-primary">Orders</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<script>
    // Toggle mobile menu
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.toggle('hidden');
    }
</script>


    <main class="mt-16 container mx-auto px-4 py-8">
        <h2 class="text-2xl font-semibold text-primary mb-6">Order Details</h2>

        <!-- Order Information -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold mb-4">Order ID: #<?php echo $orderDetails['id']; ?></h3>
            <p><strong>Date:</strong> <?php echo $orderDetails['created_at']; ?></p>
            <p><strong>Status:</strong> <?php echo $orderDetails['status']; ?></p>
            <p><strong>Total Amount:</strong> $<?php echo number_format($orderDetails['total_amount'], 2); ?></p>
        </div>

        <!-- Products in Order -->
        <h3 class="text-xl font-semibold text-primary mb-4">Products in this Order</h3>
        <table class="min-w-full bg-white shadow-lg rounded-lg overflow-hidden">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4 text-left">Product</th>
                    <th class="py-2 px-4 text-left">Quantity</th>
                    <th class="py-2 px-4 text-left">Price</th>
                    <th class="py-2 px-4 text-left">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderProducts as $product): ?>
                    <tr class="border-b">
                        <td class="py-2 px-4"><?php echo $product['product_name']; ?></td>
                        <td class="py-2 px-4"><?php echo $product['quantity']; ?></td>
                        <td class="py-2 px-4">$<?php echo number_format($product['price'], 2); ?></td>
                        <td class="py-2 px-4">$<?php echo number_format($product['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <footer class="bg-gray-900 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <a href="#" class="font-['Pacifico'] text-2xl text-white mb-6 block">Glamour Gems</a>
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
                        <li><a href="#" class="text-gray-400 hover:text-white">Bracelets</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Earrings</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Rings</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-medium mb-4">Newsletter</h3>
                    <form>
                        <input type="email" placeholder="Enter your email" class="px-4 py-2 rounded-lg w-full mb-4 text-gray-700">
                        <button type="submit" class="bg-primary text-white py-2 px-4 rounded-lg w-full">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
