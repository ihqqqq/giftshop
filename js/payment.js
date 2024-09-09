

document.addEventListener("DOMContentLoaded", function () {
  displayCartItems();
  calculateTotal();
});

function displayCartItems() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const cartContainer = document.getElementById("cart-items-container");

  if (cartContainer) {
    cartContainer.innerHTML = "";

    cart.forEach((item) => {
      const cartItemHTML = `
                <div class="cart-item">
                    <img src="${item.image}" alt="${
        item.name
      }" class="cart-item-image"/>
                    <div class="cart-item-details">
                        <h3 class="cart-item-name">${item.name}</h3>
                        <p class="cart-item-price">${item.price.toLocaleString()}₫</p>
                        <p class="cart-item-quantity">Số lượng: ${
                          item.quantity
                        }</p>
                        <p class="cart-item-total">${(
                          item.price * item.quantity
                        ).toLocaleString()}₫</p>
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
