<?php
session_start();
include 'config.php';

$products = [];
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
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
                    <a href="#" class="font-['Pacifico'] text-2xl text-primary">logo</a>
                    
                    <nav class="hidden md:flex space-x-8">
                        <a href="#" class="text-gray-700 hover:text-primary">Home</a>
                        <a href="#" class="text-gray-700 hover:text-primary">Jewelry</a>
                        <a href="#" class="text-gray-700 hover:text-primary">Fashion</a>
                        <a href="#" class="text-gray-700 hover:text-primary">New Arrivals</a>
                        <a href="#" class="text-gray-700 hover:text-primary">Sale</a>
                    </nav>
                    
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 w-64 bg-gray-50 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                                <i class="ri-search-line"></i>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <button id="accountBtn" class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-primary relative">
                                <i class="ri-user-line text-xl"></i>
                            </button>
                            <button id="cartBtn" class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-primary relative">
                                <i class="ri-shopping-bag-line text-xl"></i>
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-xs rounded-full flex items-center justify-center"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="mt-16">
            <section class="relative h-[600px] bg-cover bg-center" style="background-image: url('https://public.readdy.ai/ai/img_res/53650b078e4cdf1a909887b3d8c18cfe.jpg')">
                <div class="absolute inset-0 bg-black/30"></div>
                <div class="container mx-auto px-4 h-full flex items-center">
                    <div class="relative max-w-2xl text-white">
                        <h1 class="playfair text-5xl font-bold mb-6">Discover Timeless Elegance</h1>
                        <p class="text-lg mb-8">Explore our curated collection of exquisite jewelry and fashion pieces that define luxury and sophistication.</p>
                        <button class="bg-primary text-white px-8 py-3 rounded-button text-lg font-medium hover:bg-primary/90 transition-colors">Shop Now</button>
                    </div>
                </div>
            </section>

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
            <section class="py-16">
                <div class="container mx-auto px-4">
                    <div class="flex items-center justify-between mb-12">
                        <h2 class="playfair text-3xl font-bold">New Arrivals</h2>
                        <a href="#" class="text-primary hover:underline">View All</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <?php foreach ($products as $product): ?>
                        <div class="group">
                            <div class="relative mb-4 rounded-lg overflow-hidden">
                                <img src="<?php echo $product['image_url']; ?>" class="w-full aspect-square object-cover">
                                <button class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-white text-gray-900 px-6 py-2 rounded-button opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Add to Cart</button>
                            </div>
                            <h3 class="text-lg font-medium mb-2"><?php echo $product['name']; ?></h3>
                            <p class="text-gray-600">$<?php echo $product['price']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="py-16 bg-secondary">
                <div class="container mx-auto px-4">
                    <div class="max-w-3xl mx-auto text-center">
                        <h2 class="playfair text-3xl font-bold mb-6">Subscribe to Our Newsletter</h2>
                        <p class="text-gray-600 mb-8">Stay updated with our latest collections and exclusive offers.</p>
                        <form class="flex gap-4 max-w-md mx-auto">
                            <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-button border-none text-sm">
                            <button type="submit" class="bg-primary text-white px-6 py-3 rounded-button whitespace-nowrap hover:bg-primary/90 transition-colors">Subscribe</button>
                        </form>
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
        <div id="loginModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Sign In</h2>
                    <button class="text-gray-400 hover:text-gray-600" onclick="toggleLoginModal()">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>
                <form class="space-y-4" action="login.php" method="POST">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-2 rounded-button border-gray-200 focus:border-primary focus:ring-primary text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" class="w-full px-4 py-2 rounded-button border-gray-200 focus:border-primary focus:ring-primary text-sm" required>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-primary hover:underline">Forgot password?</a>
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-2 rounded-button hover:bg-primary/90 transition-colors">Sign In</button>
                    <p class="text-center text-sm text-gray-600">
                        Don't have an account? 
                        <a href="#" class="text-primary hover:underline">Register</a>
                    </p>
                </form>
            </div>
        </div>

        <div id="cartModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Shopping Cart</h2>
                    <button class="text-gray-400 hover:text-gray-600" onclick="toggleCartModal()">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                        <img src="https://public.readdy.ai/ai/img_res/fccdff1e67103a9ca60998dda801ac95.jpg" class="w-20 h-20 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="font-medium">Diamond Pendant Necklace</h3>
                            <p class="text-gray-600">$1,299.00</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">-</button>
                            <span class="w-8 text-center">1</span>
                            <button class="w