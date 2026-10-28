// ======================================================
// STUDENT REGISTRATION FORM VALIDATION (Practical 5)
// ======================================================

// Page to open after a successful registration.
// Change this if your dashboard is in another folder, e.g. "student/dashboard.html"
const DASHBOARD_URL = "dashboard.html";

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
    const successMessage = document.getElementById("successMessage");

    // ================= REGULAR EXPRESSIONS =================

    // Only letters and spaces, 3 to 50 characters
    const nameRegex = /^[A-Za-z\s]{3,50}$/;

    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    // Indian 10-digit mobile number (starts with 6-9)
    const mobileRegex = /^[6-9][0-9]{9}$/;

    // Password: minimum 8 characters, at least one uppercase, one lowercase,
    // one number and one special character (@$!%*?&)
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

    // ================= ERROR FUNCTION =================
    function showError(input, errorId, message) {
        input.classList.remove("valid");
        input.classList.add("invalid");
        input.setAttribute("aria-invalid", "true");
        document.getElementById(errorId).textContent = message;
    }

    // ================= SUCCESS FUNCTION =================
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
            showError(fullname, "fullnameError", "Name must be 3-50 characters, letters and spaces only.");
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
            showError(mobile, "mobileError", "Enter a valid 10-digit mobile number starting with 6-9.");
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
                "Use 8+ characters with uppercase, lowercase, a number and a special character (@$!%*?&)."
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
        } else if (score <= 1) {
            strengthBar.style.width = "20%";
            strengthBar.style.backgroundColor = "#dc3545";
            strengthText.textContent = "Password strength: Weak";
        } else if (score === 2) {
            strengthBar.style.width = "40%";
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

    // ================= REAL-TIME VALIDATION =================
    fullname.addEventListener("input", validateName);
    email.addEventListener("input", validateEmail);

    // Mobile: strip anything that is not a digit as the user types
    mobile.addEventListener("input", function () {
        mobile.value = mobile.value.replace(/\D/g, "");
        validateMobile();
    });

    password.addEventListener("input", function () {
        updatePasswordStrength();
        validatePassword();

        if (confirmPassword.value !== "") {
            validateConfirmPassword();
        }
    });

    confirmPassword.addEventListener("input", validateConfirmPassword);
    course.addEventListener("change", validateCourse);
    year.addEventListener("change", validateYear);
    terms.addEventListener("change", validateTerms);

    document.querySelectorAll('input[name="gender"]').forEach(function (radio) {
        radio.addEventListener("change", validateGender);
    });

    // ================= FORM SUBMISSION =================
    registerForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const nameValid = validateName();
        const emailValid = validateEmail();
        const mobileValid = validateMobile();
        const passwordValid = validatePassword();
        const confirmPasswordValid = validateConfirmPassword();
        const courseValid = validateCourse();
        const yearValid = validateYear();
        const genderValid = validateGender();
        const termsValid = validateTerms();

        const isFormValid =
            nameValid &&
            emailValid &&
            mobileValid &&
            passwordValid &&
            confirmPasswordValid &&
            courseValid &&
            yearValid &&
            genderValid &&
            termsValid;

        if (isFormValid) {
            successMessage.textContent = "Registration successful! Redirecting to your dashboard...";

            // In a real application, data would be sent to the server here.

            // Go to the student dashboard after a short pause
            setTimeout(function () {
                window.location.href = DASHBOARD_URL;
            }, 1000);
        } else {
            successMessage.textContent = "";

            // Move keyboard focus to the first field with an error
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