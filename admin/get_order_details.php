<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "giftshop";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, email, name, phone, address, notes, status FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    $sql_items = "SELECT product_name, quantity, price FROM order_items WHERE order_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);

    // Tính tổng tiền
    $total_price = 0;
    foreach ($items as $item) {
        $total_price += $item['quantity'] * $item['price'];
    }

    $stmt->close();
    $stmt_items->close();
    $conn->close();

    echo json_encode([
        'id' => $order['id'],
        'email' => $order['email'],
        'name' => $order['name'],
        'phone' => $order['phone'],
        'address' => $order['address'],
        'notes' => $order['notes'],
        'status' => $order['status'],
        'items' => $items,
        'total_price' => $total_price
    ]);
} else {
    echo json_encode(['error' => 'Order ID not provided']);
}
