/* Feature 3: Notification banner (dismissal remembered with localStorage) */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var banner = document.getElementById("notice-banner");
    if (!banner) return;

    var noticeId = banner.dataset.noticeId || "default";
    var storageKey = "studenthub-notice-" + noticeId;

    if (localStorage.getItem(storageKey) === "dismissed") {
      banner.hidden = true;
      return;
    }

    var closeBtn = banner.querySelector(".notice-close");
    if (!closeBtn) return;

    closeBtn.addEventListener("click", function () {
      banner.hidden = true;
      localStorage.setItem(storageKey, "dismissed");
    });
  });
})();
