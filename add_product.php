<?php
session_start();
include 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get product data from the form
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $stock = isset($_POST['stock']) ? $_POST['stock'] : 0;

    // Image upload logic
    if (isset($_FILES['image'])) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageSize = $_FILES['image']['size'];
        $imageError = $_FILES['image']['error'];
        $imageType = $_FILES['image']['type'];

        // Check for image errors
        if ($imageError === 0) {
            // Get file extension
            $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

            // Define allowed extensions
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($imageExt, $allowedExts)) {
                // Set a unique name for the image
                $imageNewName = uniqid('', true) . '.' . $imageExt;
                $imageDestination = 'uploads/' . $imageNewName;

                // Move the image to the upload folder
                if (move_uploaded_file($imageTmpName, $imageDestination)) {
                    $image_url = $imageDestination; // Set the image URL
                } else {
                    $error_message = "Failed to upload image.";
                }
            } else {
                $error_message = "Invalid image type. Allowed types: jpg, jpeg, png, gif.";
            }
        } else {
            $error_message = "There was an error uploading the image.";
        }
    }

    // Insert the product into the database
    if (!isset($error_message)) {
        $sql = "INSERT INTO products (name, description, price, stock, image_url, category) 
                VALUES ('$name', '$description', '$price', '$stock', '$image_url', '$category')";

        if ($conn->query($sql) === TRUE) {
            header("Location: admin.php");
            exit();
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product | Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-primary">Add New Product</h1>
                <a href="admin.php" class="text-blue-500 hover:underline">Back to Admin Panel</a>
            </div>
        </header>

        <div class="container mx-auto px-4 py-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-3xl font-semibold text-primary mb-6">Product Details</h2>

                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="add_product.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="name" class="block text-lg font-medium text-primary">Product Name</label>
                        <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded-lg" placeholder="Enter product name">
                    </div>

                    <div>
                        <label for="description" class="block text-lg font-medium text-primary">Description</label>
                        <textarea id="description" name="description" rows="4" required class="w-full px-4 py-2 border rounded-lg" placeholder="Enter product description"></textarea>
                    </div>

                    <div>
                        <label for="price" class="block text-lg font-medium text-primary">Price</label>
                        <input type="number" id="price" name="price" required step="0.01" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter product price">
                    </div>

                    <div>
                        <label for="stock" class="block text-lg font-medium text-primary">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter stock quantity" min="0" value="0">
                    </div>

                    <div>
                        <label for="category" class="block text-lg font-medium text-primary">Category</label>
                        <input type="text" id="category" name="category" required class="w-full px-4 py-2 border rounded-lg" placeholder="Enter product category">
                    </div>

                    <div>
                        <label for="image" class="block text-lg font-medium text-primary">Product Image</label>
                        <input type="file" id="image" name="image" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg w-full">Add Product</button>
                </form>
            </div>
        </div>

    </div>

</body>
</html>
