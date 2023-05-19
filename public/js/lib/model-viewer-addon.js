const arButton = document.querySelector(".ar-button");
        const popup = document.getElementById("popup");
        const qrCodeImg = document.getElementById("qr-code-img");
        if (isMobile()||isTouchDevice()) {
          arButton.style.display = "none";
        }

          arButton.addEventListener("click", () => {
            if (isMobile()||isTouchDevice()) {
              modelViewer.activateAR();
            } else {
              const currentUrl = window.location.href;
              const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${currentUrl}`;
              qrCodeImg.setAttribute("src", qrCodeUrl);
              popup.style.display = "block";
            }
          });

          function closePopup() {
            popup.style.display = "none";
          }

          const getMobileOS = () => {
                const ua = navigator.userAgent
                if (/android/i.test(ua)) {
                  return "Android"
                }
                else if ((/iPad|iPhone|iPod/.test(ua))
                  || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)){
                  return "iOS"
                }
                return "Other"
              }
          function isTouchDevice() {
                    return 'ontouchstart' in window || navigator.maxTouchPoints;
              }
          function isMobile() {
            const userAgent = window.navigator.userAgent;
          const mobileKeywords = ['Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
          
          return mobileKeywords.some(keyword => userAgent.includes(keyword));
                
          
        }