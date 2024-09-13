function updateCartItemCount() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  let totalItems = cart.reduce((acc, item) => acc + item.quantity, 0);

  const itemCountElement = document.querySelector(
    ".header__middle-item--info-t2"
  );
  if (itemCountElement) {
    itemCountElement.innerText = `(${totalItems}) sản phẩm`;
  }
}

document.querySelectorAll(".flashsale__product-btn-cart").forEach((button) => {
  button.addEventListener("click", function () {
    const productElement = this.closest(".flashsale__product-item"); 

    const productImage = productElement.querySelector(
      ".flashsale__product-thumb"
    ).src; 

    const productName = productElement
      .querySelector(".flashsale__product-name")
      .textContent.trim(); 

    let productPriceText = productElement.querySelector(
      ".flashsale__product-pricebox-left span:last-of-type"
    ).textContent;
    const productPrice = parseInt(productPriceText.replace(/\D/g, ""));

    addToCart(productName, productPrice, productImage);
  });
});


document.querySelectorAll(".bestseller__item-btn-cart").forEach((button) => {
  button.addEventListener("click", function () {
    const productElement = this.closest(".bestseller__item"); //lay san pham tu phan tu cha

    const productImage = productElement.querySelector(
      ".bestseller__item-img"
    ).src; //lay hinh anh

    const productName = productElement
      .querySelector(".bestseller__item-name")
      .textContent.trim(); //lay ten san pham

    //lay gia san pham loai bo cac ki tu khong phai so
    let productPriceText = productElement.querySelector(
      ".bestseller__item-price"
    ).textContent;
    const productPrice = parseInt(productPriceText.replace(/\D/g, ""));

    addToCart(productName, productPrice, productImage);
  });
});

function addToCart(productName, productPrice, productImage) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  const product = {
    name: productName,
    price: productPrice,
    image: productImage,
    quantity: 1,
  };

  const existingProduct = cart.find((item) => item.name === productName);

  if (existingProduct) {
    //neu san pham da co tang so luong
    existingProduct.quantity += 1;
  } else {
    //nguoc lai neu chua co them vao gio hang
    cart.push(product);
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartItemCount();

  alert("Đã thêm sản phẩm vào giỏ hàng");
}

//ham hien thi san pham vao gio hang
function displayCartItems() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const cartContainer = document.getElementById("cart-items-container");

  if (cartContainer) {
    cartContainer.innerHTML = "";

    //duyet qua cac phan tu trong gio hang va hien thi
    cart.forEach((item) => {
      //tao phan tu html cho moi san pham
      const cartItem = `
            <div class="cart-layout__item">
                <a href="" class="cart-layout__link">
                  <img src="${item.image}" alt="${item.name}" />
                </a>
                <div class="cart-layout__item-info">
                  <div class="cart-layout__item-info--name">
                    <a href="#!">${item.name}</a>
                    <a href="" class="cart-layout__item-remove" onclick="removeItem('${
                      item.name
                    }')">Xóa</a>
                  </div>
                  <div class="cart-layout__item-info--price">${item.price.toLocaleString()}₫</div>
                  <div class="cart-layout__item-info--form">
                    <div class="product-detail__form">
                      <div class="product-detail__form-group">
                        <div class="product-detail__form-quantity">
                          <div class="product-detail__form-quantity-control">
                            <button class="product-detail__form-btn1" onclick="decreaseQuantity('${
                              item.name
                            }')">-</button>
                            <input
                              type="text"
                              class="product-detail__form-mid"
                              value="${item.quantity}"
                              readonly
                            />
                            <button class="product-detail__form-btn2" onclick="increaseQuantity('${
                              item.name
                            }')">+</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="cart-layout__item-info--amount">${(
                    item.price * item.quantity
                  ).toLocaleString()}₫</div>
                </div>
              </div>
            `;

      //them phan tu vao container
      cartContainer.innerHTML += cartItem;
    });
  } else {
    console.error("Phần tử #cart-items-container không tồn tại");
  }
}

function decreaseQuantity(productName) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  productName = productName.trim();
  const product = cart.find((item) => item.name === productName);

  if (product && product.quantity > 1) {
    product.quantity -= 1;
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  displayCartItems();
  calculateTotal();
  updateCartItemCount();
}

function increaseQuantity(productName) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  productName = productName.trim();
  const product = cart.find((item) => item.name === productName);

  if (product) {
    product.quantity += 1;
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  displayCartItems();
  calculateTotal();
  updateCartItemCount();
}

function removeItem(productName) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  productName = productName.trim();
  cart = cart.filter((item) => item.name !== productName);

  localStorage.setItem("cart", JSON.stringify(cart));
  displayCartItems();
  updateCartItemCount();
}

document.addEventListener("DOMContentLoaded", function () {
  displayCartItems();
  updateCartItemCount();
});

function calculateTotal() {
  let total = 0;

  const cartItems = document.querySelectorAll(".cart-layout__item");

  cartItems.forEach(function (item) {
    let priceText = item
      .querySelector(".cart-layout__item-info--price")
      .innerText.trim();
    let priceNumber = parseFloat(priceText.replace(/\./g, "").replace("₫", ""));

    let quantityInput = item.querySelector(".product-detail__form-mid");
    let quantity = parseInt(quantityInput.value);

    if (!isNaN(priceNumber) && !isNaN(quantity) && quantity > 0) {
      let amount = priceNumber * quantity;
      item.querySelector(".cart-layout__item-info--amount").innerText =
        amount + ",000₫";
      total += amount;
    }
  });

  let totalElement = document.querySelector(".cart_layout__subtotal--total");
  if (totalElement) {
    totalElement.innerText = total + ",000  ₫";
  }
}

function updateCart() {
  const quantityInputs = document.querySelectorAll(".product-detail__form-mid");

  quantityInputs.forEach(function (input) {
    input.addEventListener("input", function () {
      calculateTotal();
    });
  });
}

document.addEventListener("DOMContentLoaded", function () {
  calculateTotal();
  updateCart();
  updateCartItemCount();
});
