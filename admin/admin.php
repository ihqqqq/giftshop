    <?php
    session_start();

    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }

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

    // Xử lý cập nhật trạng thái
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];

        $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $status, $order_id);

        if ($stmt->execute()) {
            echo "<p>Trạng thái đơn hàng đã được cập nhật.</p>";
        } else {
            echo "<p>Lỗi khi cập nhật trạng thái: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_order'])) {
        $order_id = $_POST['order_id'];

        $conn->begin_transaction();

        try {
            $delete_items_sql = "DELETE FROM order_items WHERE order_id = ?";
            $stmt_items = $conn->prepare($delete_items_sql);
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $stmt_items->close();


            $delete_sql = "DELETE FROM orders WHERE id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $stmt->close();


            $conn->commit();

            echo "<p>Đơn hàng đã được xóa.</p>";
        } catch (Exception $e) {

            $conn->rollback();
            echo "<p>Lỗi khi xóa đơn hàng: " . $e->getMessage() . "</p>";
        }
    }


    $sql = "SELECT id, email, name, phone, address, notes, status FROM orders";
    $result = $conn->query($sql);
    ?>

    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../assets/css/admin.css">
        <title>Quản lý đơn hàng</title>
    </head>

    <body>
        <div class="container">
            <div class="menu">
                <a href="product-list.php">Danh sách sản phẩm</a>
                <a href="add_product.php">Thêm sản phẩm</a>
                <a href="add_order.php" class="btn">Thêm đơn hàng mới</a>
                <a href="logout.php" class="logout-button">Đăng xuất</a>
            </div>

            <h1>Danh sách đơn hàng</h1>

            <?php
            if ($result->num_rows > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Email</th><th>Tên</th><th>Số điện thoại</th><th>Địa chỉ</th><th>Ghi chú</th><th>Trạng thái giao hàng</th><th>Thông tin sản phẩm</th><th>Thay đổi trạng thái</th><th>Hành động</th></tr>';

                // Hiển thị dữ liệu đơn hàng
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row["id"] . '</td>';
                    echo '<td>' . $row["email"] . '</td>';
                    echo '<td>' . $row["name"] . '</td>';
                    echo '<td>' . $row["phone"] . '</td>';
                    echo '<td>' . $row["address"] . '</td>';
                    echo '<td>' . $row["notes"] . '</td>';

                    // Hiển thị trạng thái
                    if ($row["status"] == 'Đã giao') {
                        echo '<td class="status-delivered">Đã giao</td>';
                    } elseif ($row["status"] == 'Hủy') {
                        echo '<td class="status-cancelled">Hủy</td>';
                    } else {
                        echo '<td class="status-pending">Đang xử lý</td>';
                    }

                    // Hiển thị thông tin sản phẩm
                    $order_id = $row["id"];
                    $sql_items = "SELECT product_name, quantity, price FROM order_items WHERE order_id = ?";
                    $stmt_items = $conn->prepare($sql_items);
                    $stmt_items->bind_param("i", $order_id);
                    $stmt_items->execute();
                    $result_items = $stmt_items->get_result();

                    // Trong vòng lặp hiển thị đơn hàng
                    echo '<td> <button class="view-details" data-order-id="' . $row["id"] . '">Xem chi tiết</button> </td>';



                    // Form cập nhật trạng thái và xóa đơn hàng
                    echo '<td>
                    <form method="post" action="">
                        <input type="hidden" name="order_id" value="' . $row["id"] . '">
                        <select name="status">
                            <option value="Đang xử lý"' . ($row["status"] == 'Đang xử lý' ? ' selected' : '') . '>Đang xử lý</option>
                            <option value="Đã giao"' . ($row["status"] == 'Đã giao' ? ' selected' : '') . '>Đã giao</option>
                            <option value="Hủy"' . ($row["status"] == 'Hủy' ? ' selected' : '') . '>Hủy</option>
                        </select>
                        <button type="submit" name="update_status" class="btn">Cập nhật</button>
                    </form>
                </td>';

                    echo '<td class="action-buttons">
                    <form method="post" action="">
                        <input type="hidden" name="order_id" value="' . $row["id"] . '">
                        <button type="submit" name="delete_order">Xóa</button>
                    </form>
                </td>';

                    echo '</tr>';
                }

                echo '</table>';
            } else {
                echo '<p class="no-data">Không có đơn hàng nào.</p>';
            }

            $conn->close();
            ?>
            <div class="back-to-home">
                <a href="../index.html">Quay lại trang chủ</a>
            </div>

            <div id="productModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <div id="modal-body">
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.dataset.orderId;

                    fetch(`get_order_details.php?order_id=${orderId}`)
                        .then(response => response.json())
                        .then(data => {
                            const modalBody = document.getElementById('modal-body');
                            modalBody.innerHTML = `
                            <h2>Chi tiết đơn hàng #${data.id}</h2>
                            <p>Email: ${data.email}</p>
                            <p>Tên: ${data.name}</p>
                            <p>Số điện thoại: ${data.phone}</p>
                            <p>Địa chỉ: ${data.address}</p>
                            <p>Ghi chú: ${data.notes}</p>
                            <p>Trạng thái: ${data.status}</p>
                            <h3>Sản phẩm</h3>
                            <ul>
                                ${data.items.map(item => `
                                    <li>${item.product_name} - Số lượng: ${item.quantity} - Giá: ${item.price}₫</li>
                                `).join('')}
                            </ul>
                            <p><strong>Tổng tiền: ${data.total_price}₫</strong></p>
                        `;
                            document.getElementById('productModal').style.display = 'block';
                        })
                        .catch(error => console.error('Error fetching order details:', error));
                });
            });

            document.querySelector('.modal .close').addEventListener('click', function() {
                document.getElementById('productModal').style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                if (event.target == document.getElementById('productModal')) {
                    document.getElementById('productModal').style.display = 'none';
                }
            });
        </script>

    </body>

    </html>