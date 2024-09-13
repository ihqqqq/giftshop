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

// Biến để lưu trạng thái đơn hàng
$order_success = false;

// Xử lý khi form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $email = $_POST['email'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $notes = $_POST['notes'];

    // Lấy dữ liệu giỏ hàng
    $cart_items = isset($_POST['cart_items']) ? json_decode($_POST['cart_items'], true) : [];


    // Kiểm tra xem $cart_items có phải là mảng không
    if (!is_array($cart_items) || empty($cart_items)) {
            $order_error = "Dữ liệu giỏ hàng không hợp lệ.";
        } else {
        // Lưu dữ liệu vào bảng `orders`
        $sql = "INSERT INTO orders (email, name, phone, address, notes) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $email, $name, $phone, $address, $notes);

        if ($stmt->execute()) {
            // Lấy ID của đơn hàng vừa tạo
            $order_id = $stmt->insert_id;

            // Lưu thông tin sản phẩm vào bảng `order_items`
            foreach ($cart_items as $item) {
                $product_name = $item['name'];
                $quantity = $item['quantity'];
                $price = $item['price'];

                $sql_item = "INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)";
                $stmt_item = $conn->prepare($sql_item);
                $stmt_item->bind_param("isid", $order_id, $product_name, $quantity, $price);
                $stmt_item->execute();
                $stmt_item->close();
            }

            $order_success = true;
            header("Location: admin/admin.php"); // Điều hướng đến trang admin sau khi đặt hàng thành công
            exit;
        } else {
            $order_error = "Lỗi khi lưu đơn hàng: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./assets/css/reset.css" />
    <link rel="stylesheet" href="./assets/css/payment.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />
    <title>Thanh toán</title>
</head>

<body>
    <div class="wrap">
        <main class="main">
            <header class="main-header">
                <h1 class="shop-name">
                    <a class="payment-header" href="#">ND Gift</a>
                </h1>
            </header>

            <article class="form-layout">
                <div class="col col-two">
                    <section class="section">
                        <div class="section__header">
                            <h2>Thông tin nhận hàng</h2>
                            <div class="section__login">
                                <i class="fa-solid fa-circle-user"></i>
                                <a href="#">Đăng nhập</a>
                            </div>
                        </div>
                        <div class="section__content">
                            <!-- Form đặt hàng -->
                            <form action="process_order.php" class="form-wrap" method="post">
                                <div class="form-control">
                                    <input type="email" name="email" placeholder="Email" class="field_input" required />
                                </div>
                                <div class="form-control">
                                    <input type="text" name="name" placeholder="Họ và tên" class="field_input" required />
                                </div>
                                <div class="form-control">
                                    <input type="text" name="phone" placeholder="Số điện thoại (tùy chọn)" class="field_input" />
                                </div>
                                <div class="form-control">
                                    <input type="text" name="address" placeholder="Địa chỉ (tùy chọn)" class="field_input" />
                                </div>
                                <textarea name="notes" class="field_input" rows="5" placeholder="Ghi chú (tùy chọn)"></textarea>

                                <!-- Thông tin giỏ hàng -->
                                <input type="hidden" name="cart_items" id="cart-items" value="" />

                                <div class="cart-buttons">
                                    <a href="cart.html" class="btn-back-to-cart">Quay về giỏ hàng</a>
                                    <button type="submit" class="btn-place-order">Đặt hàng</button>
                                </div>
                            </form>

                            <?php if ($order_success): ?>
                                <p style="color: green;">Đơn hàng đã được lưu thành công!</p>
                            <?php elseif (isset($order_error)): ?>
                                <p style="color: red;"><?php echo $order_error; ?></p>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>

                <div class="col col-two">
                    <section class="section">
                        <div class="section__header">
                            <h2>Vận chuyển</h2>
                            <div>Vui lòng nhập thông tin giao hàng</div>
                        </div>
                    </section>
                </div>
            </article>
        </main>

        <aside class="sidebar">
            <div class="sidebar__header">
                <h2 class="sidebar__title">Giỏ hàng của bạn</h2>
            </div>
            <div class="discount-code-container">
                <label for="discount-code" class="discount-code-label">Nhập mã giảm giá:</label>

                <input
                    type="text"
                    id="discount-code"
                    class="discount-code-input"
                    placeholder="Nhập mã giảm giá tại đây" />
                <button class="discount-code-button" disabled>Áp dụng</button>
            </div>

            <div class="sidebar__content">
                <div id="cart-items-container"></div>
                <div class="total-amount">
                    <h3>Tổng cộng:</h3>
                    <p id="total-amount">0₫</p>
                </div>
            </div>
        </aside>
    </div>
    <script src="./js/payment.js"></script>
    <script>console.log('Dữ liệu giỏ hàng trong Local Storage:', localStorage.getItem('cart'));
</script>


</body>

</html>