document.addEventListener("DOMContentLoaded", function () {
  displayCartItems();
  calculateTotal();
  // Chuyển đổi dữ liệu từ Local Storage sang JSON và gán vào trường hidden
  const cartItems = JSON.parse(localStorage.getItem("cart")) || [];
  document.getElementById("cart-items").value = JSON.stringify(cartItems);

  // Log dữ liệu giỏ hàng
  console.log("Dữ liệu giỏ hàng:", JSON.stringify(cartItems));
});

function displayCartItems() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const cartContainer = document.getElementById("cart-items-container");

  if (cartContainer) {
    cartContainer.innerHTML = "";

    cart.forEach((item) => {
      const cartItemHTML = `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.name}" class="cart-item-image"/>
                    <div class="cart-item-details">
                        <h3 class="cart-item-name">${item.name}</h3>
                        <p class="cart-item-price">${item.price.toLocaleString()}₫</p>
                        <p class="cart-item-quantity">Số lượng: ${item.quantity}</p>
                        <p class="cart-item-total">${(item.price * item.quantity).toLocaleString()}₫</p>
                    </div>
                </div>
            `;
      cartContainer.innerHTML += cartItemHTML;
    });
  } else {
    console.error("Phần tử #cart-items-container không tồn tại");
  }
}

function calculateTotal() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  let total = 0;

  cart.forEach((item) => {
    total += item.price * item.quantity;
  });

  const totalElement = document.getElementById("total-amount");
  if (totalElement) {
    totalElement.innerText = total.toLocaleString() + "₫";
  }
}

document.addEventListener('DOMContentLoaded', function () {
    const cartItemsContainer = document.getElementById('cart-items-container');
    const cartItems = [];

    cartItemsContainer.querySelectorAll('.cart-item').forEach(item => {
        const name = item.querySelector('.cart-item-name').textContent;
        const priceText = item.querySelector('.cart-item-price').textContent;
        const quantityText = item.querySelector('.cart-item-quantity').textContent;
        const price = parseFloat(priceText.replace('₫', '').replace(',', ''));
        const quantity = parseInt(quantityText.replace('Số lượng: ', ''));

        cartItems.push({
            name: name,
            quantity: quantity,
            price: price
        });
    });

    document.getElementById('cart-items').value = JSON.stringify(cartItems);
})


document.querySelector("form").addEventListener("submit", function (event) {
  const cartItemsField = document.getElementById("cart-items");
  console.log(
    "Giá trị của trường cart-items khi gửi form:",
    cartItemsField.value
  );
});

