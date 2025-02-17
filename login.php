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

// Fetch products
$products = [];
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
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
        <!-- Hero Section -->
        <section class="relative h-[600px] bg-cover bg-center" style="background-image: url('https://public.readdy.ai/ai/img_res/53650b078e4cdf1a909887b3d8c18cfe.jpg')">
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="container mx-auto px-4 h-full flex items-center">
                <div class="relative max-w-2xl text-white">
                    <h1 class="text-5xl font-bold mb-6">Discover Timeless Elegance</h1>
                    <p class="text-lg mb-8">Explore our curated collection of exquisite jewelry and fashion pieces that define luxury and sophistication.</p>
                    <a href="shop.php" class="bg-primary text-white px-8 py-3 rounded-button text-lg font-medium hover:bg-primary/90 transition-colors">Shop Now</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Featured Categories -->
            <section class="py-16 bg-gray-50">
                <div class="container mx-auto px-4">
                    <h2 class="playfair text-3xl font-bold text-center mb-12">Featured Categories</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <a href="#" class="group relative h-80 rounded-lg overflow-hidden">
                            <img src="https://public.readdy.ai/ai/img_res/1e8185a56cc1c49dc82f66f1871d2ea3.jpg" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <h3 class="text-white text-2xl font-medium">Necklaces</h3>
                            </div>
                        </a>
                        <a href="#" class="group relative h-80 rounded-lg overflow-hidden">
                            <img src="https://public.readdy.ai/ai/img_res/c0c264a3c83e25ebe08ded3035f42908.jpg" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <h3 class="text-white text-2xl font-medium">Rings</h3>
                            </div>
                        </a>
                        <a href="#" class="group relative h-80 rounded-lg overflow-hidden">
                            <img src="https://public.readdy.ai/ai/img_res/2a32050a3b1177171104feec88e4e78d.jpg" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <h3 class="text-white text-2xl font-medium">Bracelets</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
            
            <!-- New Arrivals -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-12">
            <h2 class="playfair text-3xl font-bold">New Arrivals</h2>
            <a href="#" class="text-primary hover:underline">View All</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <?php
            // Fetch the latest 4 products from the database
            $sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 4";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($product = $result->fetch_assoc()) {
                    ?>
                    <div class="group">
                        <div class="relative mb-4 rounded-lg overflow-hidden">
                            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="w-full aspect-square object-cover">
                            <button class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-white text-gray-900 px-6 py-2 rounded-button opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Add to Cart</button>
                        </div>
                        <h3 class="text-lg font-semibold"><?php echo $product['name']; ?></h3>
                        <p class="text-gray-600 mt-2"><?php echo '$' . number_format($product['price'], 2); ?></p>
                    </div>
                    <?php
                }
            } else {
                echo '<p>No new arrivals found.</p>';
            }
            ?>
        </div>
    </div>
</section>

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

        <div id="accountModal" class="fixed inset-0 bg-black/50 hidden">
    <div class="flex justify-center items-center h-full">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96">
            <h2 class="text-2xl font-bold mb-6">Login / Register</h2>
            <form action="login_register.php" method="post">
                <input type="email" name="email" class="w-full mb-4 px-4 py-2 border border-gray-300 rounded" placeholder="Email">
                <input type="password" name="password" class="w-full mb-4 px-4 py-2 border border-gray-300 rounded" placeholder="Password">
                <button type="submit" class="w-full bg-primary text-white py-2 rounded">Login</button>
            </form>

            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">Don't have an account? <a href="register.php" class="text-primary font-semibold">Register</a></p>
                <p class="text-sm text-gray-600">Forgot your password? <a href="forgot_password.php" class="text-primary font-semibold">Reset it here</a></p>
            </div>
        </div>
    </div>
</div>


    <script>
        function toggleAccountModal() {
            document.getElementById('accountModal').classList.toggle('hidden');
        }
    </script>
</body>
</html>
