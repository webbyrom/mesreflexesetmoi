document.addEventListener("DOMContentLoaded", () => {
          const images = document.querySelectorAll(".Mrm-Pr-img");
        
          const observer = new IntersectionObserver(
            (entries) => {
              entries.forEach(entry => {
                const img = entry.target;
        
                if (entry.isIntersecting) {
                  img.style.webkitMaskSize = "200% 200%";
                  img.style.maskSize = "200% 200%";
                  img.style.filter = "grayscale(0%)";
                } else {
                  img.style.webkitMaskSize = "0% 0%";
                  img.style.maskSize = "0% 0%";
                  img.style.filter = "grayscale(100%)";
                }
              });
            },
            {
              threshold: 0.3 // Ajuste si tu veux un effet plus tôt ou plus tard
            }
          );
        
          images.forEach(img => observer.observe(img));
});
        