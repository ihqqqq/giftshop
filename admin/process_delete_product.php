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

// Kiểm tra nếu có ID sản phẩm được gửi
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Xóa sản phẩm
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // Điều hướng trở lại trang danh sách sản phẩm với thông báo thành công
        header("Location: product_list.php?message=Xóa sản phẩm thành công");
    } else {
        // Điều hướng trở lại trang danh sách sản phẩm với thông báo lỗi
        header("Location: product_list.php?message=Lỗi khi xóa sản phẩm");
    }

    $stmt->close();
} else {
    // Điều hướng trở lại trang danh sách sản phẩm với thông báo lỗi
    header("Location: product_list.php?message=ID sản phẩm không hợp lệ");
}

$conn->close();
