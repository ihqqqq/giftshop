<?php
// Kết nối tới cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "giftshop";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy order_id từ tham số GET
$order_id = $_GET['order_id'];

// Lấy thông tin sản phẩm từ bảng order_items
$sql_items = "SELECT product_name, quantity, price FROM order_items WHERE order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

if ($result_items->num_rows > 0) {
    echo '<h2>Thông tin sản phẩm</h2>';
    echo '<ul>';
    while ($item = $result_items->fetch_assoc()) {
        echo '<li>' . $item["product_name"] . ' - Số lượng: ' . $item["quantity"] . ' - Giá: ' . $item["price"] . '₫</li>';
    }
    echo '</ul>';
} else {
    echo 'Chưa có sản phẩm';
}

$stmt_items->close();
$conn->close();
