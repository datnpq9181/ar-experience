(function() {
  const arButton = document.querySelector(".ar-button");
  const popup = document.getElementById("popup");
  const qrCodeImg = document.getElementById("qr-code-img");

  if (arButton && popup && qrCodeImg) {
    if (isMobile() || isTouchDevice()) {
      arButton.style.display = "none";
    }

    arButton.addEventListener("click", () => {
      if (isMobile() || isTouchDevice()) {
        modelViewer.activateAR();
      } else {
        const currentUrl = window.location.href;
        const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${currentUrl}`;
        qrCodeImg.setAttribute("src", qrCodeUrl);
        popup.style.display = "block";
        popup.classList.add("fade-in");
      }
    });

    // Đảm bảo hàm closePopup có thể truy cập từ HTML
    window.closePopup = function() {
      popup.classList.remove("fade-in");
      popup.classList.add("fade-out");
      setTimeout(() => {
        popup.style.display = "none";
        popup.classList.remove("fade-out");
      }, 300);
    };

    // Thêm sự kiện đóng popup cho nút đóng
    const closeButton = document.querySelector(".close-button");
    if (closeButton) {
      closeButton.addEventListener("click", closePopup);
    }
  } else {
    console.error("AR button or popup elements not found in the DOM.");
  }

  function isMobile() {
    const userAgent = window.navigator.userAgent;
    const mobileKeywords = [
      "Android",
      "iPhone",
      "iPad",
      "iPod",
      "BlackBerry",
      "Windows Phone"
    ];

    return mobileKeywords.some((keyword) => userAgent.includes(keyword));
  }

  function isTouchDevice() {
    return "ontouchstart" in window || navigator.maxTouchPoints;
  }
})();
