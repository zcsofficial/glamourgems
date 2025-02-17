<?php
session_start();
include 'config.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$order_id = intval($_GET['id']);
$order = null;

// Fetch order details
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $order = $result->fetch_assoc();
} else {
    header("Location: admin.php");
    exit();
}

// Allowed status values
$allowed_statuses = ['pending', 'processing', 'shipped', 'delivered', 'canceled'];

// Update order status
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_status = trim($_POST['status']);

    // Validate status input
    if (!in_array($new_status, $allowed_statuses)) {
        $_SESSION['error'] = "Invalid order status!";
        header("Location: admin.php");
        exit();
    }

    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $order_id);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Order status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update order status.";
    }
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order | Glamour Gems</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">Update Order Status</h2>
            
            <form action="" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-600 mb-2">Order ID:</label>
                    <input type="text" value="<?php echo $order['id']; ?>" disabled class="w-full px-3 py-2 border rounded bg-gray-200">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 mb-2">Current Status:</label>
                    <input type="text" value="<?php echo ucfirst($order['status']); ?>" disabled class="w-full px-3 py-2 border rounded bg-gray-200">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 mb-2">Update Status:</label>
                    <select name="status" class="w-full px-3 py-2 border rounded">
                        <?php foreach ($allowed_statuses as $status): ?>
                            <option value="<?= $status ?>" <?php if ($order['status'] == $status) echo 'selected'; ?>>
                                <?= ucfirst($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mt-6 flex justify-between">
                    <a href="admin.php" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
