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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
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
                <a href="shop.php" class="text-primary font-semibold">View All</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg"><?php echo $product['name']; ?></h3>
                        <p class="text-sm text-gray-500 mb-4"><?php echo $product['description']; ?></p>
                        <span class="text-primary font-bold text-xl">$<?php echo $product['price']; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12">
                <div>
                    <h3 class="text-lg font-semibold">About Glamour Gems</h3>
                    <p class="text-sm">A luxury jewelry and fashion e-commerce platform offering exclusive collections.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Customer Service</h3>
                    <ul>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white">Shipping & Returns</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Follow Us</h3>
                    <ul class="flex space-x-6">
                        <li><a href="#" class="text-gray-400 hover:text-white"><i class="ri-facebook-line"></i></a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white"><i class="ri-instagram-line"></i></a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white"><i class="ri-twitter-line"></i></a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Contact Us</h3>
                    <p class="text-sm">support@glamourgems.com</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
