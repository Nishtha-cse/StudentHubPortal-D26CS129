/* Feature 5: Collapsible FAQ accordion */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".accordion-trigger").forEach(function (trigger) {
      trigger.addEventListener("click", function () {
        var panelId = trigger.getAttribute("aria-controls");
        var panel = panelId ? document.getElementById(panelId) : null;
        var isExpanded = trigger.getAttribute("aria-expanded") === "true";

        trigger.setAttribute("aria-expanded", String(!isExpanded));
        if (panel) panel.hidden = isExpanded;
      });
    });
  });
})();
