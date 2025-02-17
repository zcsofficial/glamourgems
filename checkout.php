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
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$cart_items = [];
$sql = "SELECT c.id, p.name, p.price, p.image_url, c.quantity 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
}

// Calculate total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glamour Gems | Checkout</title>
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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#9f7aea',
                        secondary: '#f6e5ff'
                    },
                    borderRadius: {
                        'none': '0px',
                        'sm': '4px',
                        DEFAULT: '8px',
                        'md': '12px',
                        'lg': '16px',
                        'xl': '20px',
                        '2xl': '24px',
                        '3xl': '32px',
                        'full': '9999px',
                        'button': '8px'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white">
    <div id="app">
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
            <!-- Checkout Form -->
            <section class="py-16">
                <div class="container mx-auto px-4">
                    <h2 class="playfair text-3xl font-bold text-center mb-12">Checkout</h2>
                    <?php if (empty($cart_items)): ?>
                        <p class="text-center text-lg text-gray-600">Your cart is empty. Start shopping now!</p>
                    <?php else: ?>
                        <form method="POST" action="confirm_order.php">
                            <!-- User Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                                <div>
                                    <h3 class="text-xl font-semibold mb-4">Billing Information</h3>
                                    <div class="space-y-6">
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                            <input type="text" name="name" id="name" value="<?php echo $_SESSION['name']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
                                        </div>
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                            <input type="email" name="email" id="email" value="<?php echo $_SESSION['email']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
                                        </div>
                                        <div>
                                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                            <input type="text" name="phone" id="phone" value="<?php echo $_SESSION['phone']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
                                        </div>
                                        <div>
                                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                            <textarea name="address" id="address" class="w-full px-4 py-2 border border-gray-300 rounded-md" rows="4" required><?php echo $_SESSION['address']; ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Summary -->
                                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                                    <h3 class="text-xl font-semibold mb-4">Order Summary</h3>
                                    <ul class="space-y-4">
                                        <?php foreach ($cart_items as $item): ?>
                                            <li class="flex justify-between">
                                                <span class="text-gray-800"><?php echo $item['name']; ?> (x<?php echo $item['quantity']; ?>)</span>
                                                <span class="text-gray-800">$<?php echo $item['price'] * $item['quantity']; ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="border-t border-gray-200 mt-4 pt-4">
                                        <div class="flex justify-between font-semibold">
                                            <span>Total</span>
                                            <span>$<?php echo number_format($total_price, 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm Order Button -->
                            <div class="mt-8 text-center">
                                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-full hover:bg-primary/90 transition-colors">
                                    Confirm Order
                                </button>
                            </div>
                        </form>
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

    <script>
        // Toggle user dropdown visibility
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }
    </script>
</body>
</html>
