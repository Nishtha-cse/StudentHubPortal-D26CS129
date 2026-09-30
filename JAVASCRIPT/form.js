const registerForm = document.getElementById("registerForm");

if (registerForm) {

    // ================= GET ELEMENTS =================
    const fullname = document.getElementById("fullname");
    const email = document.getElementById("email");
    const mobile = document.getElementById("mobile");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");
    const course = document.getElementById("course");
    const year = document.getElementById("year");
    const terms = document.getElementById("terms");
    const captchaInput = document.getElementById("captchaInput");

    // ================= REGULAR EXPRESSIONS =================

    // Only letters and spaces, 3 to 50 characters
    const nameRegex = /^[A-Za-z\s]{3,50}$/;

    // Basic email format: something@something.domain
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    // Indian mobile number: 10 digits, first digit 6-9
    const mobileRegex = /^[6-9][0-9]{9}$/;

    // Password: minimum 8 characters, at least one uppercase letter,
    // one lowercase letter, one number and one special character
    // (?=.*X) is a "lookahead": it checks that X exists somewhere in the text
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

    // ================= HELPER FUNCTIONS =================
    function showError(input, errorId, message) {
        input.classList.remove("valid");
        input.classList.add("invalid");
        input.setAttribute("aria-invalid", "true");
        document.getElementById(errorId).textContent = message;
    }

    function showValid(input, errorId) {
        input.classList.remove("invalid");
        input.classList.add("valid");
        input.setAttribute("aria-invalid", "false");
        document.getElementById(errorId).textContent = "";
    }

    // ================= NAME VALIDATION =================
    function validateName() {
        const value = fullname.value.trim();

        if (value === "") {
            showError(fullname, "fullnameError", "Full name is required.");
            return false;
        }
        if (!nameRegex.test(value)) {
            showError(fullname, "fullnameError", "Name must contain only letters and spaces.");
            return false;
        }

        showValid(fullname, "fullnameError");
        return true;
    }

    // ================= EMAIL VALIDATION =================
    function validateEmail() {
        const value = email.value.trim();

        if (value === "") {
            showError(email, "emailError", "Email is required.");
            return false;
        }
        if (!emailRegex.test(value)) {
            showError(email, "emailError", "Enter a valid email address.");
            return false;
        }

        showValid(email, "emailError");
        return true;
    }

    // ================= MOBILE VALIDATION =================
    function validateMobile() {
        const value = mobile.value.trim();

        if (value === "") {
            showError(mobile, "mobileError", "Mobile number is required.");
            return false;
        }
        if (!mobileRegex.test(value)) {
            showError(mobile, "mobileError", "Enter a valid 10-digit mobile number.");
            return false;
        }

        showValid(mobile, "mobileError");
        return true;
    }

    // ================= PASSWORD VALIDATION =================
    function validatePassword() {
        const value = password.value;

        if (value === "") {
            showError(password, "passwordError", "Password is required.");
            return false;
        }
        if (!passwordRegex.test(value)) {
            showError(
                password,
                "passwordError",
                "Password must contain 8+ characters, uppercase, lowercase, number and special character."
            );
            return false;
        }

        showValid(password, "passwordError");
        return true;
    }

    // ================= CONFIRM PASSWORD =================
    function validateConfirmPassword() {
        const value = confirmPassword.value;

        if (value === "") {
            showError(confirmPassword, "confirmPasswordError", "Please confirm your password.");
            return false;
        }
        if (value !== password.value) {
            showError(confirmPassword, "confirmPasswordError", "Passwords do not match.");
            return false;
        }

        showValid(confirmPassword, "confirmPasswordError");
        return true;
    }

    // ================= COURSE VALIDATION =================
    function validateCourse() {
        if (course.value === "") {
            showError(course, "courseError", "Please select your course.");
            return false;
        }

        showValid(course, "courseError");
        return true;
    }

    // ================= YEAR VALIDATION =================
    function validateYear() {
        if (year.value === "") {
            showError(year, "yearError", "Please select your academic year.");
            return false;
        }

        showValid(year, "yearError");
        return true;
    }

    // ================= GENDER VALIDATION =================
    function validateGender() {
        const selectedGender = document.querySelector('input[name="gender"]:checked');
        const error = document.getElementById("genderError");

        if (!selectedGender) {
            error.textContent = "Please select your gender.";
            return false;
        }

        error.textContent = "";
        return true;
    }

    // ================= TERMS VALIDATION =================
    function validateTerms() {
        const error = document.getElementById("termsError");

        if (!terms.checked) {
            error.textContent = "You must accept the Terms & Conditions.";
            return false;
        }

        error.textContent = "";
        return true;
    }

    // ==================================================
    // PASSWORD STRENGTH METER
    // One point for each rule the password satisfies (0 to 5)
    // ==================================================
    function updatePasswordStrength() {
        const value = password.value;
        const strengthBar = document.getElementById("strengthBar");
        const strengthText = document.getElementById("strengthText");

        let score = 0;
        if (value.length >= 8) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[a-z]/.test(value)) score++;
        if (/[0-9]/.test(value)) score++;
        if (/[@$!%*?&]/.test(value)) score++;

        if (value === "") {
            strengthBar.style.width = "0";
            strengthText.textContent = "Password strength: \u2014";
        } else if (score <= 2) {
            strengthBar.style.width = score <= 1 ? "20%" : "40%";
            strengthBar.style.backgroundColor = "#dc3545";
            strengthText.textContent = "Password strength: Weak";
        } else if (score === 3) {
            strengthBar.style.width = "60%";
            strengthBar.style.backgroundColor = "#e0a020";
            strengthText.textContent = "Password strength: Medium";
        } else if (score === 4) {
            strengthBar.style.width = "80%";
            strengthBar.style.backgroundColor = "#2d8f5f";
            strengthText.textContent = "Password strength: Good";
        } else {
            strengthBar.style.width = "100%";
            strengthBar.style.backgroundColor = "#198754";
            strengthText.textContent = "Password strength: Strong";
        }
    }

    // ==================================================
    // ADVANCED EXTENSION: CUSTOM CAPTCHA USING CANVAS
    // (delete this whole section + the captcha block in the HTML to remove it)
    // ==================================================
    const canvas = document.getElementById("captchaCanvas");
    const ctx = canvas.getContext("2d");
    let currentCaptcha = "";

    function generateCaptchaText() {
        // 0, O, 1, I and L are left out because they look alike
        const chars = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";
        let text = "";
        for (let i = 0; i < 6; i++) {
            text += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return text;
    }

    function drawCaptcha() {
        currentCaptcha = generateCaptchaText();

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = "#f4f6f8";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // random noise lines
        for (let i = 0; i < 5; i++) {
            ctx.strokeStyle = "rgba(79, 70, 229, " + (0.15 + Math.random() * 0.2) + ")";
            ctx.beginPath();
            ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
            ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
            ctx.stroke();
        }

        // the characters, each slightly rotated and shifted
        const spacing = canvas.width / (currentCaptcha.length + 1);
        for (let j = 0; j < currentCaptcha.length; j++) {
            ctx.save();
            ctx.translate(spacing * (j + 1), canvas.height / 2 + (Math.random() * 10 - 5));
            ctx.rotate(Math.random() * 0.5 - 0.25);
            ctx.font = "bold 24px Arial";
            ctx.fillStyle = "#1c2430";
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillText(currentCaptcha.charAt(j), 0, 0);
            ctx.restore();
        }

        // random noise dots
        for (let k = 0; k < 30; k++) {
            ctx.fillStyle = "rgba(28, 36, 48, 0.2)";
            ctx.beginPath();
            ctx.arc(Math.random() * canvas.width, Math.random() * canvas.height, 1, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function validateCaptcha() {
        const value = captchaInput.value.trim().toUpperCase();

        if (value === "") {
            showError(captchaInput, "captchaError", "Please enter the code shown above.");
            return false;
        }
        if (value !== currentCaptcha) {
            showError(captchaInput, "captchaError", "Code does not match. Try again.");
            return false;
        }

        showValid(captchaInput, "captchaError");
        return true;
    }

    document.getElementById("refreshCaptcha").addEventListener("click", function () {
        drawCaptcha();
        captchaInput.value = "";
        captchaInput.classList.remove("valid", "invalid");
        captchaInput.removeAttribute("aria-invalid");
        document.getElementById("captchaError").textContent = "";
    });

    drawCaptcha();

    // ================= REAL-TIME VALIDATION (INTERMEDIATE EXTENSION) =================
    // "input" fires on every key press / paste; "change" fires when a choice is made
    fullname.addEventListener("input", validateName);
    email.addEventListener("input", validateEmail);

    mobile.addEventListener("input", function () {
        mobile.value = mobile.value.replace(/\D/g, "");   // allow digits only
        validateMobile();
    });

    password.addEventListener("input", function () {
        updatePasswordStrength();
        validatePassword();

        // re-check confirm password if the user already typed something there
        if (confirmPassword.value !== "") {
            validateConfirmPassword();
        }
    });

    confirmPassword.addEventListener("input", validateConfirmPassword);
    course.addEventListener("change", validateCourse);
    year.addEventListener("change", validateYear);
    terms.addEventListener("change", validateTerms);
    captchaInput.addEventListener("input", validateCaptcha);

    document.querySelectorAll('input[name="gender"]').forEach(function (radio) {
        radio.addEventListener("change", validateGender);
    });

    // ================= FORM SUBMISSION =================
    registerForm.addEventListener("submit", function (event) {
        event.preventDefault();   // stop the page from reloading

        // Every validator runs (no short-circuit) so all errors show together
        const nameValid = validateName();
        const emailValid = validateEmail();
        const mobileValid = validateMobile();
        const passwordValid = validatePassword();
        const confirmPasswordValid = validateConfirmPassword();
        const courseValid = validateCourse();
        const yearValid = validateYear();
        const genderValid = validateGender();
        const captchaValid = validateCaptcha();
        const termsValid = validateTerms();

        const isFormValid =
            nameValid && emailValid && mobileValid && passwordValid &&
            confirmPasswordValid && courseValid && yearValid &&
            genderValid && captchaValid && termsValid;

        const successMessage = document.getElementById("successMessage");

        if (isFormValid) {
            successMessage.textContent = "Registration successful!";

            // In a real application,
            // data would be sent to the server here.
        } else {
            successMessage.textContent = "";

            // Keyboard users: move focus to the first field with an error
            const firstInvalid = registerForm.querySelector(".invalid");
            if (firstInvalid) {
                firstInvalid.focus();
            } else if (!genderValid) {
                document.querySelector('input[name="gender"]').focus();
            } else if (!termsValid) {
                terms.focus();
            }
        }
    });
}