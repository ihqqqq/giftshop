<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        input[type="file"] {
            margin-bottom: 15px;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .image-preview {
            margin-bottom: 15px;
        }

        .image-preview img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .back-to-list {
            margin-top: 20px;
        }

        .back-to-list a {
            text-decoration: none;
            color: #007bff;
        }

        .back-to-list a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Sửa sản phẩm</h1>

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

        // Nếu có ID sản phẩm được gửi, hiển thị thông tin sản phẩm để sửa
        if (isset($_GET['product_id'])) {
            $product_id = $_GET['product_id'];

            $sql = "SELECT * FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            if ($product) {
                echo '<form method="post" action="process_edit_product.php" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="' . htmlspecialchars($product['id']) . '">

                        <label for="name">Tên sản phẩm:</label>
                        <input type="text" id="name" name="name" value="' . htmlspecialchars($product['name']) . '" required><br>

                        <label for="price">Giá:</label>
                        <input type="text" id="price" name="price" value="' . htmlspecialchars($product['price']) . '" required><br>

                        <label for="quantity">Số lượng:</label>
                        <input type="number" id="quantity" name="quantity" value="' . htmlspecialchars($product['quantity']) . '" required><br>

                        <label for="description">Mô tả:</label>
                        <textarea id="description" name="description" rows="4" required>' . htmlspecialchars($product['description']) . '</textarea><br>

                        <label for="images">Ảnh sản phẩm:</label>
                        <input type="file" id="images" name="images[]" accept="image/*" multiple><br>';

                // Lấy và hiển thị hình ảnh hiện tại
                $sql_images = "SELECT image_path FROM product_images WHERE product_id = ?";
                $stmt_images = $conn->prepare($sql_images);
                $stmt_images->bind_param("i", $product_id);
                $stmt_images->execute();
                $images_result = $stmt_images->get_result();

                echo '<div class="image-preview">';
                while ($image = $images_result->fetch_assoc()) {
                    echo '<img src="' . htmlspecialchars($image['image_path']) . '" alt="Product Image">';
                }
                echo '</div>';

                echo '<button type="submit">Cập nhật sản phẩm</button>
                    </form>';
            } else {
                echo '<p>Sản phẩm không tồn tại.</p>';
            }
        } else {
            echo '<p>Vui lòng chọn sản phẩm để sửa.</p>';
        }

        $conn->close();
        ?>

        <div class="back-to-list">
            <a href="product-list.php">Quay lại danh sách sản phẩm</a>
        </div>
    </div>
</body>

</html>