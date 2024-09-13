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

// Kiểm tra nếu ID sản phẩm được gửi qua phương thức POST
if (isset($_POST['id'])) {
    $product_id = intval($_POST['id']);

    if ($product_id) {
        // Xóa ảnh liên quan từ cơ sở dữ liệu
        $sql_images = "SELECT image_path FROM product_images WHERE product_id = ?";
        $stmt_images = $conn->prepare($sql_images);
        $stmt_images->bind_param("i", $product_id);
        $stmt_images->execute();
        $images_result = $stmt_images->get_result();

        while ($image = $images_result->fetch_assoc()) {
            $image_path = $image['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path); // Xóa tệp ảnh từ hệ thống
            }
        }

        // Xóa thông tin ảnh trong cơ sở dữ liệu
        $sql_delete_images = "DELETE FROM product_images WHERE product_id = ?";
        $stmt_delete_images = $conn->prepare($sql_delete_images);
        $stmt_delete_images->bind_param("i", $product_id);
        $stmt_delete_images->execute();

        // Xóa sản phẩm từ cơ sở dữ liệu
        $sql_delete_product = "DELETE FROM products WHERE id = ?";
        $stmt_delete_product = $conn->prepare($sql_delete_product);
        $stmt_delete_product->bind_param("i", $product_id);

        if ($stmt_delete_product->execute()) {
            echo "Sản phẩm đã được xóa.";
        } else {
            echo "Lỗi khi xóa sản phẩm: " . $stmt_delete_product->error;
        }

        $stmt_delete_product->close();
        $stmt_delete_images->close();
        $stmt_images->close();
    } else {
        echo "ID sản phẩm không hợp lệ.";
    }
} else {
    echo "ID sản phẩm không được gửi.";
}

$conn->close();
