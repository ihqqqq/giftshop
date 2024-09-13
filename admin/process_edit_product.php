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

// Kiểm tra nếu dữ liệu đã được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];

    // Cập nhật thông tin sản phẩm
    $sql = "UPDATE products SET name=?, price=?, quantity=?, description=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdiis", $name, $price, $quantity, $description, $product_id);

    if ($stmt->execute()) {
        // Nếu có hình ảnh mới được tải lên, xử lý chúng
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            // Xóa tất cả hình ảnh cũ của sản phẩm
            $sql_delete_images = "DELETE FROM product_images WHERE product_id=?";
            $stmt_delete_images = $conn->prepare($sql_delete_images);
            $stmt_delete_images->bind_param("i", $product_id);
            $stmt_delete_images->execute();

            // Xử lý hình ảnh mới
            $upload_dir = 'uploads/';
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['images']['tmp_name'][$key];
                    $basename = basename($name);
                    $image_path = $upload_dir . $basename;
                    move_uploaded_file($tmp_name, $image_path);

                    // Lưu hình ảnh vào cơ sở dữ liệu
                    $sql_insert_image = "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)";
                    $stmt_insert_image = $conn->prepare($sql_insert_image);
                    $stmt_insert_image->bind_param("is", $product_id, $image_path);
                    $stmt_insert_image->execute();
                }
            }
        }

        echo "<p>Cập nhật sản phẩm thành công!</p>";
    } else {
        echo "<p>Cập nhật sản phẩm thất bại: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
<a href="product-list.php">Quay lại danh sách sản phẩm</a>