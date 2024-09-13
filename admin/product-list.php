<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            margin-bottom: 1rem;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 0.5rem;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .action-buttons button {
            display: inline-block;
            padding: 0.5rem 1rem;
            color: #fff;
            border-radius: 4px;
            font-weight: bold;
            margin-right: 0.5rem;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }

        .action-buttons .edit {
            background-color: #007bff;
        }

        .action-buttons .edit:hover {
            background-color: #0056b3;
        }

        .action-buttons .delete {
            background-color: #dc3545;
        }

        .action-buttons .delete:hover {
            background-color: #c82333;
        }

        .view-images-btn {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .view-images-btn:hover {
            background-color: #218838;
        }

        /* Lightbox Styles */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .lightbox-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
        }

        .lightbox-content img {
            max-width: 100%;
            max-height: 80vh;
            margin: 0.5rem;
        }

        .lightbox .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 2rem;
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Danh sách sản phẩm</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Mô tả</th>
                    <th>Ảnh</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
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

                $sql = "SELECT * FROM products";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['price']) . " VNĐ</td>";
                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";

                        // Lấy ảnh sản phẩm
                        $product_id = $row['id'];
                        $sql_images = "SELECT image_path FROM product_images WHERE product_id = ?";
                        $stmt_images = $conn->prepare($sql_images);
                        $stmt_images->bind_param("i", $product_id);
                        $stmt_images->execute();
                        $images_result = $stmt_images->get_result();

                        $images = [];
                        while ($image = $images_result->fetch_assoc()) {
                            $images[] = htmlspecialchars($image['image_path']);
                        }

                        echo "<td>";
                        if (!empty($images)) {
                            echo "<button class='view-images-btn' onclick='openLightbox(" . json_encode($images) . ")'>Xem ảnh</button>";
                        } else {
                            echo "Không có ảnh";
                        }
                        echo "</td>";

                        echo "<td class='action-buttons'>";
                        echo "<button class='edit' onclick='editProduct(" . htmlspecialchars($row['id']) . ")'>Sửa</button>";
                        echo "<button class='delete' data-id='" . htmlspecialchars($row['id']) . "' onclick='deleteProduct(this)'>Xóa</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Không có sản phẩm nào.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox" onclick="closeLightbox()">
        <span class="close">&times;</span>
        <div id="lightbox-content" class="lightbox-content"></div>
    </div>

    <script>
        function openLightbox(images) {
            const lightboxContent = document.getElementById('lightbox-content');
            lightboxContent.innerHTML = ''; // Xóa nội dung cũ

            images.forEach(src => {
                const img = document.createElement('img');
                img.src = src;
                lightboxContent.appendChild(img);
            });

            document.getElementById('lightbox').style.display = 'flex';
        }

        function closeLightbox() {
            document.getElementById('lightbox').style.display = 'none';
        }

        function deleteProduct(button) {
            const productId = button.getAttribute('data-id');

            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                // Gửi yêu cầu AJAX để xóa sản phẩm
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_product.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            button.closest('tr').remove();
                            alert('Sản phẩm đã được xóa.');
                        } else {
                            alert('Đã xảy ra lỗi khi xóa sản phẩm.');
                        }
                    }
                };

                xhr.send('id=' + encodeURIComponent(productId));
            }
        }

        function editProduct(productId) {
            // Thay đổi URL của trang chỉnh sửa sản phẩm
            window.location.href = 'edit_product.php?product_id=' + productId;
        }
    </script>
</body>

</html>