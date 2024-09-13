<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            margin-bottom: 1.5rem;
            color: #333;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        input[type="file"] {
            border: none;
            padding: 0;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error {
            color: #dc3545;
            margin-top: 1rem;
        }

        .success {
            color: #28a745;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Thêm Sản phẩm</h1>
        <form action="process_add_product.php" method="post" enctype="multipart/form-data">
            <label for="name">Tên sản phẩm:</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="price">Giá:</label>
            <input type="number" id="price" name="price" step="0.01" required><br><br>

            <label for="quantity">Số lượng:</label>
            <input type="number" id="quantity" name="quantity" required><br><br>

            <label for="description">Mô tả:</label>
            <textarea id="description" name="description" required></textarea><br><br>

            <label for="images">Ảnh sản phẩm (chọn nhiều ảnh):</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple required><br><br>

            <input type="submit" value="Thêm sản phẩm">
        </form>
    </div>
</body>

</html>