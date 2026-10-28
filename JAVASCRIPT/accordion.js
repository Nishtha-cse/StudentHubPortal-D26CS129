(function () {
  "use strict";

  document.addEventListener("click", function (event) {
    var trigger = event.target.closest(".accordion-trigger");
    if (!trigger) return;

    var panelId = trigger.getAttribute("aria-controls");
    var panel = panelId ? document.getElementById(panelId) : null;
    var isExpanded = trigger.getAttribute("aria-expanded") === "true";

    trigger.setAttribute("aria-expanded", String(!isExpanded));
    if (panel) panel.hidden = isExpanded;
  });
})();