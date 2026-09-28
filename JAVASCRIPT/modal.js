/* Feature 4: Modal popup (open button, close button, overlay click, Escape, focus handling) */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var lastTrigger = null;

    function openModal(modal, trigger) {
      lastTrigger = trigger;
      modal.hidden = false;
      document.body.classList.add("modal-open");
      var focusTarget = modal.querySelector("input, textarea, select, button, [href]");
      if (focusTarget) focusTarget.focus();
    }

    function closeModal(modal) {
      modal.hidden = true;
      document.body.classList.remove("modal-open");
      if (lastTrigger) lastTrigger.focus();
    }

    // Any button with data-modal-target="<modal id>" opens that modal
    document.querySelectorAll("[data-modal-target]").forEach(function (trigger) {
      var modal = document.getElementById(trigger.dataset.modalTarget);
      if (!modal) return;
      trigger.addEventListener("click", function () { openModal(modal, trigger); });
    });

    document.querySelectorAll(".modal-overlay").forEach(function (modal) {
      var closeBtn = modal.querySelector(".modal-close");
      if (closeBtn) closeBtn.addEventListener("click", function () { closeModal(modal); });

      // Click on the dark background closes it
      modal.addEventListener("click", function (event) {
        if (event.target === modal) closeModal(modal);
      });

      modal.addEventListener("keydown", function (event) {
        if (event.key === "Escape") { closeModal(modal); return; }

        // Keep Tab focus inside the open dialog
        if (event.key === "Tab") {
          var items = modal.querySelectorAll("button, [href], input, textarea, select");
          if (!items.length) return;
          var first = items[0];
          var last = items[items.length - 1];
          if (event.shiftKey && document.activeElement === first) {
            event.preventDefault(); last.focus();
          } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault(); first.focus();
          }
        }
      });
    });
  });
})();
