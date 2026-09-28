/* Feature 2: Hamburger menu (mobile navigation) */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var toggle = document.querySelector(".nav-toggle");
    var nav = document.getElementById("primary-nav");
    if (!toggle || !nav) return;

    var navList = nav.querySelector("ul");
    if (!navList) return;

    function closeMenu() {
      navList.classList.remove("is-open");
      toggle.setAttribute("aria-expanded", "false");
    }

    toggle.addEventListener("click", function (event) {
      event.stopPropagation();
      var isOpen = navList.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", String(isOpen));
    });

    // Close after choosing a link
    navList.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", closeMenu);
    });

    // Close on outside click and on Escape (keyboard users)
    document.addEventListener("click", function (event) {
      if (!nav.contains(event.target)) closeMenu();
    });
    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") closeMenu();
    });
  });
})();
