/* Feature 6: Content slider (prev / next buttons, dots, keyboard arrows) */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".slider").forEach(function (slider) {
      var slides = Array.from(slider.querySelectorAll(".slide"));
      if (slides.length === 0) return;

      var prevBtn = slider.querySelector(".slider-prev");
      var nextBtn = slider.querySelector(".slider-next");
      var dotsWrap = slider.querySelector(".slider-dots");
      var index = 0;

      // Build one dot per slide
      if (dotsWrap) {
        dotsWrap.innerHTML = "";
        slides.forEach(function (_, i) {
          var dot = document.createElement("button");
          dot.type = "button";
          dot.setAttribute("role", "tab");
          dot.setAttribute("aria-label", "Go to slide " + (i + 1));
          dot.addEventListener("click", function () { show(i); });
          dotsWrap.appendChild(dot);
        });
      }
      var dots = dotsWrap ? Array.from(dotsWrap.children) : [];

      function show(newIndex) {
        index = (newIndex + slides.length) % slides.length; // wraps around
        slides.forEach(function (slide, i) { slide.hidden = i !== index; });
        dots.forEach(function (dot, i) {
          dot.setAttribute("aria-selected", String(i === index));
        });
      }

      if (prevBtn) prevBtn.addEventListener("click", function () { show(index - 1); });
      if (nextBtn) nextBtn.addEventListener("click", function () { show(index + 1); });

      slider.addEventListener("keydown", function (event) {
        if (event.key === "ArrowLeft") show(index - 1);
        if (event.key === "ArrowRight") show(index + 1);
      });

      show(0);
    });
  });
})();
