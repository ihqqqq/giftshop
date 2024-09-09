document.addEventListener("DOMContentLoaded", function () {
  const addToCartButton = document.getElementById("add-to-cart-btn");

  if (addToCartButton) {
    addToCartButton.addEventListener("click", function (event) {
      event.preventDefault(); 

      const productImageElement = document.querySelector(".main-image");
      if (productImageElement) {
        const productImage = productImageElement.src; 
        const productName = document
          .querySelector(".product-detail__title")
          .textContent.trim();
        const productPriceText = document.querySelector(
          ".product-detail__price span"
        ).textContent;
        const productPrice = parseInt(productPriceText.replace(/\D/g, ""), 10);

        addToCart(productName, productPrice, productImage);

        updateCartItemCount();
      } else {
        console.error(
          ""
        );
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const addToCartButton = document.getElementById("add-to-cart-btn");

  if (addToCartButton) {
    addToCartButton.addEventListener("click", function (event) {
      event.preventDefault(); // Ngăn chặn hành vi mặc định của nút submit

      // Lấy thông tin sản phẩm từ trang chi tiết
      const productImage = document.querySelector("main-image").src;
      const productName = document
        .querySelector(".product-detail__title")
        .textContent.trim();
      const productPriceText = document.querySelector(
        ".product-detail__price span"
      ).textContent;
      const productPrice = parseInt(productPriceText.replace(/\D/g, ""));

      // Thêm sản phẩm vào giỏ hàng
      addToCart(productName, productPrice, productImage);

      // Hiển thị thông báo
      alert("Sản phẩm đã được thêm vào giỏ hàng");

      // Cập nhật số lượng sản phẩm trong giỏ hàng
      updateCartItemCount();
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const thumbImages = document.querySelectorAll(".thumb-image");
  const mainImage = document.querySelector(".main-image");

  thumbImages.forEach(function (thumb) {
    thumb.addEventListener("click", function () {
      mainImage.classList.add("slide-left");

      setTimeout(function () {
        mainImage.src = thumb.src;
        mainImage.classList.remove("slide-left");
        mainImage.classList.add("show");
      }, 500);

      setTimeout(function () {
        mainImage.classList.remove("show");
      }, 1000);
    });
  });
});
