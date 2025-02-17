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

// Handle quantity update and removal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update all quantities
    if (isset($_POST['update_cart'])) {
        foreach ($cart_items as $item) {
            if (isset($_POST['cart_id_' . $item['id']]) && isset($_POST['quantity_' . $item['id']])) {
                $cart_id = $_POST['cart_id_' . $item['id']];
                $new_quantity = $_POST['quantity_' . $item['id']];
                if ($new_quantity > 0 && !empty($cart_id)) {
                    $update_query = "UPDATE cart SET quantity = $new_quantity WHERE id = $cart_id";
                    $conn->query($update_query);
                }
            }
        }
    } 
    // Remove item from cart
    elseif (isset($_POST['remove_item'])) {
        if (isset($_POST['cart_id']) && !empty($_POST['cart_id'])) {
            $cart_id = $_POST['cart_id'];
            $remove_query = "DELETE FROM cart WHERE id = $cart_id";
            $conn->query($remove_query);
        }
    }
    // Redirect to refresh cart page
    header("Location: cart.php");
    exit();
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
    <title>Glamour Gems | Cart</title>
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
            <!-- Cart Items -->
            <section class="py-16">
                <div class="container mx-auto px-4">
                    <h2 class="playfair text-3xl font-bold text-center mb-12">Your Cart</h2>
                    <?php if (empty($cart_items)): ?>
                        <p class="text-center text-lg text-gray-600">Your cart is empty. Start shopping now!</p>
                    <?php else: ?>
                        <form method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg shadow-md">
                                        <img src="<?php echo $item['image_url']; ?>" alt="Product" class="w-32 h-32 object-cover rounded-lg">
                                        <div class="flex-1 ml-4">
                                            <h4 class="font-semibold text-xl"><?php echo $item['name']; ?></h4>
                                            <p class="text-gray-600">Price: $<?php echo $item['price']; ?></p>
                                            <p class="text-gray-600">Subtotal: $<?php echo $item['price'] * $item['quantity']; ?></p>
                                            <div class="mt-2">
                                                <input type="number" name="quantity_<?php echo $item['id']; ?>" value="<?php echo $item['quantity']; ?>" class="w-16 text-center border border-gray-300 rounded-md" min="1">
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <button type="submit" name="remove_item" value="remove" class="bg-red-500 text-white py-1 px-4 rounded-md">
                                                Remove
                                            </button>
                                            <input type="hidden" name="cart_id_<?php echo $item['id']; ?>" value="<?php echo $item['id']; ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Update Cart Button -->
                            <div class="mt-8 text-right">
                                <button type="submit" name="update_cart" class="bg-primary text-white px-6 py-3 rounded-full hover:bg-primary/90 transition-colors">Update Cart</button>
                            </div>
                        </form>

                        <!-- Total Price -->
                        <section class="bg-gray-50 py-12">
                <div class="container mx-auto px-4 flex items-center justify-between">
                    <div>
                        <p class="text-lg font-semibold text-gray-700">Total Price: $<?php echo number_format($total_price, 2); ?></p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="shop.php" class="bg-primary text-white px-8 py-3 rounded-md text-lg">Continue Shopping</a>
                        <a href="checkout.php" class="bg-green-500 text-white px-8 py-3 rounded-md text-lg">Proceed to Checkout</a>
                    </div>
                </div>
            </section>
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
