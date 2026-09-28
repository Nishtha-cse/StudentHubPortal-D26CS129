/* Register form: after a successful submit, go to the dashboard page */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("register-form");
    if (!form) return;

    var password = document.getElementById("password");
    var confirm = document.getElementById("confirmpassword");

    // Show a message if the two passwords do not match
    function checkMatch() {
      confirm.setCustomValidity(
        password.value !== confirm.value ? "Passwords do not match." : ""
      );
    }
    password.addEventListener("input", checkMatch);
    confirm.addEventListener("input", checkMatch);

    form.addEventListener("submit", function (event) {
      event.preventDefault();          // stop the normal form post
      checkMatch();
      if (!form.checkValidity()) {     // required / email / match checks
        form.reportValidity();
        return;
      }
      window.location.href = "dashboard.html";   // redirect
    });
  });
})();
