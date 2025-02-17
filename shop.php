<?php
session_start();
include 'config.php';

// Fetch products from the database
$products = [];
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$username = isset($_SESSION['name']) ? $_SESSION['name'] : null; // Get username from session if logged in

// Cart counter logic
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Fetch the cart count for this user
    $cart_query = "SELECT COUNT(*) as cart_count FROM cart WHERE user_id = $user_id";
    $cart_result = $conn->query($cart_query);
    $cart_count = $cart_result->fetch_assoc()['cart_count'];
}

// Add to Cart Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    if (isset($_SESSION['user_id'])) {
        $product_id = $_POST['product_id'];
        $user_id = $_SESSION['user_id'];
        
        // Insert the product into the cart
        $insert_cart_query = "INSERT INTO cart (user_id, product_id) VALUES ($user_id, $product_id)";
        if ($conn->query($insert_cart_query) === TRUE) {
            // Update cart count
            $cart_count++;
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        // If the user is not logged in, redirect to login page
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glamour Gems | Luxury Jewelry & Fashion</title>
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
            <!-- Products Listing -->
            <section class="py-16">
                <div class="container mx-auto px-4">
                    <h2 class="playfair text-3xl font-bold text-center mb-12">Our Products</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <?php foreach ($products as $product): ?>
                            <div class="group">
                                <div class="relative mb-4">
                                    <img src="<?php echo $product['image_url']; ?>" alt="Product" class="w-full h-72 object-cover rounded-lg transition-transform duration-500 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button class="bg-primary text-white px-4 py-2 rounded-full">View</button>
                                    </div>
                                </div>
                                <h4 class="font-semibold text-xl mb-2"><?php echo $product['name']; ?></h4>
                                <p class="text-gray-600 mb-4"><?php echo $product['description']; ?></p>
                                <span class="text-lg font-bold text-primary">$<?php echo $product['price']; ?></span>
                                <form action="shop.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="mt-4 w-full bg-primary text-white py-2 rounded-full hover:bg-primary/90 transition-colors">Add to Cart</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
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

    <!-- Login Modal -->
    <div id="accountModal" class="fixed inset-0 flex items-center justify-center bg-black/60 hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full md:w-96">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold">Login to Your Account</h3>
                <button class="text-gray-500" onclick="toggleAccountModal()">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
            <form method="POST" action="login.php">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="mt-1 px-3 py-2 w-full bg-gray-100 border border-gray-300 rounded-md" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" class="mt-1 px-3 py-2 w-full bg-gray-100 border border-gray-300 rounded-md" required>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-2 rounded-full hover:bg-primary/90">Login</button>
            </form>
            <div class="mt-4 text-center">
                <span>Don't have an account? <a href="signup.php" class="text-primary">Sign Up</a></span>
            </div>
        </div>
    </div>

    <script>
        function toggleAccountModal() {
            const modal = document.getElementById('accountModal');
            modal.classList.toggle('hidden');
        }
    </script>
</body>
</html>
