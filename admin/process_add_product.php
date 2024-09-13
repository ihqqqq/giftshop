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

// Lấy dữ liệu từ form
$name = $_POST['name'];
$price = $_POST['price'];
$quantity = $_POST['quantity'];
$description = $_POST['description'];

// Thêm sản phẩm vào bảng `products`
$sql = "INSERT INTO products (name, price, quantity, description) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sdss", $name, $price, $quantity, $description);

if ($stmt->execute()) {
    $product_id = $stmt->insert_id;

    // Xử lý upload ảnh
    if (isset($_FILES['images']) && $_FILES['images']['error'][0] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $image_paths = [];
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $image_name = basename($_FILES['images']['name'][$key]);
            $image_path = $upload_dir . $image_name;

            if (move_uploaded_file($tmp_name, $image_path)) {
                $image_paths[] = $image_path;
            } else {
                echo "Lỗi khi tải lên ảnh: " . $image_name . "<br>";
            }
        }

        // Lưu thông tin ảnh vào bảng `product_images`
        foreach ($image_paths as $image_path) {
            $sql_image = "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)";
            $stmt_image = $conn->prepare($sql_image);
            $stmt_image->bind_param("is", $product_id, $image_path);
            $stmt_image->execute();
        }
    }

    // Chuyển hướng thẳng đến trang product-list.php
    header("Location: product-list.php");
    exit(); // Dừng việc thực thi tiếp các lệnh khác sau khi chuyển hướng
} else {
    echo "Lỗi khi thêm sản phẩm: " . $stmt->error;
}

$conn->close();
