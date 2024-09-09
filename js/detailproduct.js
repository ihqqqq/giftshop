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
