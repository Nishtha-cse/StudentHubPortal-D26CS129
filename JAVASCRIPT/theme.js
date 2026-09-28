/* Feature 1: Light / Dark theme switcher (remembered with localStorage) */
(function () {
  "use strict";
  var STORAGE_KEY = "studenthub-theme";
  var root = document.documentElement;

  function currentTheme() {
    return root.getAttribute("data-theme") === "dark" ? "dark" : "light";
  }

  function updateLabel(toggle) {
    var isDark = currentTheme() === "dark";
    toggle.setAttribute("aria-pressed", String(isDark));
    toggle.textContent = isDark ? "\u2600\uFE0F Light mode" : "\uD83C\uDF19 Dark mode";
  }

  document.addEventListener("DOMContentLoaded", function () {
    // Restore saved choice (the tiny script in <head> already avoids a flash)
    var stored = localStorage.getItem(STORAGE_KEY);
    if (stored === "dark" || stored === "light") {
      root.setAttribute("data-theme", stored);
    }

    var toggle = document.getElementById("theme-toggle");
    if (!toggle) return;
    updateLabel(toggle);

    toggle.addEventListener("click", function () {
      var next = currentTheme() === "dark" ? "light" : "dark";
      root.setAttribute("data-theme", next);
      localStorage.setItem(STORAGE_KEY, next);
      updateLabel(toggle);
    });
  });
})();
