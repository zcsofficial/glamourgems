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

// Fetch orders for the logged-in user
$orders = [];
if ($username) {
    $sql = "SELECT * FROM orders WHERE user_id = (SELECT id FROM users WHERE email = ?) ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $order_id = $_POST['cancel_order_id'];
    $sql = "UPDATE orders SET status = 'canceled' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    header("Location: myorders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Glamour Gems</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
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

    <!-- Header Section (using code from index.php) -->
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


    <main class="mt-16">
        <section class="container mx-auto px-4 py-12">
            <h2 class="playfair text-3xl font-bold text-center mb-8">My Orders</h2>

            <?php if (empty($orders)): ?>
                <p class="text-center text-gray-600">You have no orders yet.</p>
            <?php else: ?>
                <div class="space-y-8">
                    <?php foreach ($orders as $order): ?>
                        <div class="border p-6 rounded-lg shadow-md bg-white">
                            <h3 class="text-2xl font-semibold text-gray-800">Order #<?php echo $order['id']; ?></h3>
                            <p class="text-gray-600">Total: $<?php echo number_format($order['total_price'], 2); ?></p>
                            <p class="text-gray-600">Status: <span class="text-<?php echo $order['status'] === 'canceled' ? 'red' : 'green'; ?>-500"><?php echo ucfirst($order['status']); ?></span></p>
                            <p class="text-gray-600">Address: <?php echo $order['address']; ?></p>
                            <p class="text-gray-600">Phone: <?php echo $order['phone']; ?></p>

                            <?php if ($order['status'] === 'pending' || $order['status'] === 'confirmed'): ?>
                                <form method="POST" action="myorders.php" class="mt-4">
                                <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="text-primary">View Details</a>

                                    <input type="hidden" name="cancel_order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded-button">Cancel Order</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer Section (using code from index.php) -->
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
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Instagram</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Facebook</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Pinterest</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
