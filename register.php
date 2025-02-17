<?php
session_start();
include 'config.php';

// Session timeout logic (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: login_register.php");
    exit();
}
$_SESSION['last_activity'] = time();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle registration
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Check if the email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['error'] = "Email already exists!";
            header("Location: login_register.php");
            exit();
        } else {
            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password);
            $stmt->execute();

            $_SESSION['success'] = "Registration successful! You can now log in.";
            header("Location: login_register.php");
            exit();
        }
    }

    // Handle login
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if the email exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name']; // Store name instead of username
                $_SESSION['email'] = $user['email'];

                header("Location: index.php");
                exit();
            } else {
                $_SESSION['error'] = "Invalid email or password!";
                header("Location: login_register.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid email or password!";
            header("Location: login_register.php");
            exit();
        }
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
        /* Center the login/register modal */
        #accountModal, #registerModal {
            display: flex;
            align-items: center;
            justify-content: center;
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
                            <!-- Show logged-in name if exists -->
                            <?php if (isset($_SESSION['name'])): ?>
                                <span class="text-gray-700"><?php echo $_SESSION['name']; ?></span>
                                <button id="accountBtn" class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-primary relative" onclick="toggleAccountModal()">
                                    <i class="ri-user-line text-xl"></i>
                                </button>
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

        <!-- Hero and other sections omitted for brevity -->

        <!-- Login and Register Modal -->
        <div id="accountModal" class="fixed inset-0 bg-black/50 hidden justify-center items-center">
            <div class="bg-white p-8 rounded-lg w-[90%] sm:w-[400px] space-y-4">
                <h2 class="text-xl font-bold">Login or Register</h2>
                <form action="login_register.php" method="POST">
                    <input type="email" name="email" placeholder="Email" class="w-full p-3 rounded-md border border-gray-300" required>
                    <input type="password" name="password" placeholder="Password" class="w-full p-3 rounded-md border border-gray-300" required>
                    <div class="flex justify-between items-center">
                        <button href="login_register.php" type="submit" class="bg-primary text-white px-8 py-2 rounded-button">Login</button>
                        <a href="#" class="text-sm text-primary hover:underline">Forgot password?</a>
                    </div>
                    <p class="text-sm text-center mt-4">Don't have an account? <a href="#" onclick="toggleRegisterModal()" class="text-primary hover:underline">Register</a></p>
                </form>
            </div>
        </div>

        <div id="registerModal" class="fixed inset-0 bg-black/50 hidden justify-center items-center">
            <div class="bg-white p-8 rounded-lg w-[90%] sm:w-[400px] space-y-4">
                <h2 class="text-xl font-bold">Register</h2>
                <form action="login_register.php" method="POST">
                    <input type="text" name="name" placeholder="Full Name" class="w-full p-3 rounded-md border border-gray-300" required>
                    <input type="email" name="email" placeholder="Email" class="w-full p-3 rounded-md border border-gray-300" required>
                    <input type="password" name="password" placeholder="Password" class="w-full p-3 rounded-md border border-gray-300" required>
                    <button type="submit" class="bg-primary text-white px-8 py-2 rounded-button">Register</button>
                    <p class="text-sm text-center mt-4">Already have an account? <a href="#" onclick="toggleAccountModal()" class="text-primary hover:underline">Login</a></p>
                </form>
            </div>
        </div>

        <!-- JavaScript to toggle modals -->
        <script>
            function toggleAccountModal() {
                const modal = document.getElementById('accountModal');
                modal.classList.toggle('hidden');
            }

            function toggleRegisterModal() {
                const modal = document.getElementById('registerModal');
                modal.classList.toggle('hidden');
            }
        </script>
    </div>
</body>
</html>
