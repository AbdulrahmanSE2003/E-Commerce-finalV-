// IDEA: Wishlist */

// IDEA: Start Login*/
document.addEventListener("DOMContentLoaded", () => {
    // Prevent animations during page load
    document.body.classList.add("preload");
    setTimeout(() => document.body.classList.remove("preload"), 100);

    // Initialize AOS
    AOS.init({
        once: true,
        duration: 1000,
        easing: "ease-out-cubic",
    });

    // Get all necessary elements
    const loginForm = document.getElementById("login-form");
    console.log(loginForm);
    const signupForm = document.getElementById("signup-form");
    const formTitle = document.getElementById("form-title");
    const formSubtitle = document.getElementById("form-subtitle");
    const switchLinks = document.querySelectorAll(".switch-form-link");
    const passwordToggles = document.querySelectorAll(".password-toggle-icon");
    const loginEmail = document.getElementById("login-email");
    const loginPassword = document.getElementById("login-password");
    const signupName = document.getElementById("signup-name");
    const signupEmail = document.getElementById("signup-email");
    const signupPhone = document.getElementById("signup-phone");
    const signupPassword = document.getElementById("signup-password");
    const signupConfirmPassword = document.getElementById(
        "signup-confirm-password"
    );
    const newsletterPopup = document.getElementById("newsletter-popup");
    const successPopup = document.getElementById("success-popup");
    const closePopupBtn = document.querySelector(".close-popup");
    const newsletterForm = document.getElementById("newsletter-form");
    const newsletterEmail = document.getElementById("newsletter-email");
    const authCard = document.querySelector(".auth-card");
    const brandSlogan = document.querySelector(".brand-slogan");
    const phrases = ["Join us now", '"Fast, Smart, Limitless Shopping"'];
    let currentPhrase = 0;
    let animationInterval;

    const strengthMeter = document.querySelector(".strength-meter-fill");
    const strengthText = document.querySelector(".strength-text");

    // Initialize typewriter effect on page load
    brandSlogan.classList.remove("typewriter-text");
    brandSlogan.style.width = "0";
    void brandSlogan.offsetWidth;
    brandSlogan.classList.add("typewriter-text");

    // Function to show popup
    const showPopup = (popup) => {
        popup.classList.add("active");
    };

    // Function to hide popup
    const hidePopup = (popup) => {
        popup.classList.remove("active");
    };

    // Function to show success and redirect
    const showSuccessAndRedirect = () => {
        showPopup(successPopup);
        setTimeout(() => {
            hidePopup(successPopup);
            // Switch to login form
            authCard.classList.remove("signup-active");
            setTimeout(() => {
                signupForm.classList.remove("active-form");
                loginForm.classList.add("active-form");
            }, 300);
        }, 2000);
    };

    // Email validation function
    const validateEmail = (email) => {
        const re =
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    };

    // Function to handle email validation and error display
    const handleEmailValidation = (emailInput) => {
        const email = emailInput.value;
        const errorMessage =
            emailInput.parentElement.querySelector(".error-message");

        if (errorMessage) {
            errorMessage.remove();
        }

        if (email && !validateEmail(email)) {
            const error = document.createElement("div");
            error.className = "error-message";
            error.textContent = "Please enter a valid email address";
            emailInput.parentElement.appendChild(error);
            emailInput.classList.add("is-invalid");
            return false;
        } else {
            emailInput.classList.remove("is-invalid");
            return true;
        }
    };

    // Function to validate phone number
    const validatePhone = (phone) => {
        // Remove any non-digit characters
        const cleanPhone = phone.replace(/\D/g, "");
        // Check if it's a valid Egyptian mobile number (10 digits starting with 1)
        return /^1[0-9]{9}$/.test(cleanPhone);
    };

    // Function to handle phone validation
    const handlePhoneValidation = (phoneInput) => {
        // Get only the digits
        let phone = phoneInput.value.replace(/\D/g, "");

        // Ensure the input starts with 1 if user tries to enter other numbers first
        if (phone.length > 0 && phone[0] !== "1") {
            phone = "1" + phone;
        }

        // Limit to 10 digits
        phone = phone.slice(0, 10);

        // Format the display (but keep only digits in value)
        phoneInput.value = phone;

        const isValid = validatePhone(phone);
        phoneInput.classList.toggle("is-invalid", !isValid);
        return isValid;
    };

    // Password validation checks
    const passwordChecks = {
        length: {
            check: (password) => password.length >= 8,
            element: document.querySelector(".requirement-item:nth-child(1)"),
        },
        uppercase: {
            check: (password) => /[A-Z]/.test(password),
            element: document.querySelector(".requirement-item:nth-child(2)"),
        },
        lowercase: {
            check: (password) => /[a-z]/.test(password),
            element: document.querySelector(".requirement-item:nth-child(3)"),
        },
        number: {
            check: (password) => /[0-9]/.test(password),
            element: document.querySelector(".requirement-item:nth-child(4)"),
        },
        special: {
            check: (password) => /[!@#$%^&*(),.?":{}|<>]/.test(password),
            element: document.querySelector(".requirement-item:nth-child(5)"),
        },
    };

    // Update password requirements UI
    const updateRequirements = (password) => {
        Object.keys(passwordChecks).forEach((check) => {
            const requirement = passwordChecks[check];
            const isValid = requirement.check(password);
            requirement.element.classList.toggle("valid", isValid);
        });
    };

    // Calculate and update password strength
    const updatePasswordStrength = (password) => {
        if (!password) {
            strengthMeter.setAttribute("data-strength", "0");
            strengthText.setAttribute("data-strength", "0");
            strengthText.querySelector("span").textContent = "Empty";
            return 0;
        }

        // Count how many requirements are met
        const meetsLength = password.length >= 8;
        const meetsUppercase = /[A-Z]/.test(password);
        const meetsLowercase = /[a-z]/.test(password);
        const meetsNumber = /[0-9]/.test(password);
        const meetsSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        const strength = [
            meetsLength,
            meetsUppercase,
            meetsLowercase,
            meetsNumber,
            meetsSpecial,
        ].filter(Boolean).length;

        // Map strength score to 0-4 range
        const strengthLevel = Math.min(Math.floor(strength), 4);
        const strengthLabels = [
            "Empty",
            "Very Weak",
            "Weak",
            "Medium",
            "Strong",
        ];

        // Update UI safely
        requestAnimationFrame(() => {
            strengthMeter.setAttribute("data-strength", String(strengthLevel));
            strengthText.setAttribute("data-strength", String(strengthLevel));
            strengthText.querySelector("span").textContent =
                strengthLabels[strengthLevel];
        });

        return strengthLevel;
    };

    // Validate password match
    const validatePasswordMatch = () => {
        const password = signupPassword.value;
        const confirmPassword = signupConfirmPassword.value;
        const feedback =
            signupConfirmPassword.parentElement.querySelector(
                ".invalid-feedback"
            );

        if (password !== confirmPassword) {
            signupConfirmPassword.classList.add("is-invalid");
            feedback.style.display = "block";
            return false;
        } else {
            signupConfirmPassword.classList.remove("is-invalid");
            feedback.style.display = "none";
            return true;
        }
    };

    // Validate password strength for form submission
    const validatePasswordStrength = () => {
        const password = signupPassword.value;
        const strength = updatePasswordStrength(password);
        return strength >= 3; // Require at least "Medium" strength
    };

    // Animation utility functions
    const animateForm = (form, animationClass) => {
        form.classList.add(animationClass);
        form.addEventListener(
            "animationend",
            () => {
                form.classList.remove(animationClass);
            },
            { once: true }
        );
    };

    const handleFormValidation = (form, isValid) => {
        if (isValid) {
            animateForm(form, "pulse");
            return true;
        } else {
            animateForm(form, "shake");
            return false;
        }
    };

    // Event Listeners
    loginEmail.addEventListener("input", () =>
        handleEmailValidation(loginEmail)
    );
    signupEmail.addEventListener("input", () =>
        handleEmailValidation(signupEmail)
    );

    signupPassword.addEventListener("input", (e) => {
        const password = e.target.value;
        updateRequirements(password);
        updatePasswordStrength(password);
    });

    signupConfirmPassword.addEventListener("input", validatePasswordMatch);

    // Handle login form submission
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const isEmailValid = handleEmailValidation(loginEmail);
        const isPasswordValid = loginPassword.value.trim().length > 0;

        if (handleFormValidation(loginForm, isEmailValid && isPasswordValid)) {
            animateForm(loginForm, "fade-out");
            // Continue with login logic
        }
    });

    // Handle signup form submission
    signupForm.addEventListener("submit", (e) => {
        e.preventDefault();

        // Validate all fields
        const isNameValid = signupName.value.trim().length > 0;
        const isEmailValid = handleEmailValidation(signupEmail);
        const isPhoneValid = handlePhoneValidation(signupPhone);
        const isPasswordValid = validatePasswordStrength();
        const isPasswordMatch = validatePasswordMatch();

        // Add invalid class to empty name field
        if (!isNameValid) {
            signupName.classList.add("is-invalid");
        }

        // Check if all validations pass
        if (
            isNameValid &&
            isEmailValid &&
            isPhoneValid &&
            isPasswordValid &&
            isPasswordMatch
        ) {
            // Add success animation
            animateForm(signupForm, "pulse");

            // Show newsletter popup after successful validation
            setTimeout(() => {
                showPopup(newsletterPopup);
            }, 400);
        } else {
            // Show error animation
            animateForm(signupForm, "shake");
        }
    });

    // Add pulse animation to form inputs on focus
    const addInputFocusAnimation = (input) => {
        input.addEventListener("focus", () => {
            input.parentElement.classList.add("pulse");
        });
        input.addEventListener("blur", () => {
            input.parentElement.classList.remove("pulse");
        });
    };

    // Apply focus animation to all form inputs
    document.querySelectorAll(".form-control").forEach(addInputFocusAnimation);

    // Handle newsletter form submission
    newsletterForm.addEventListener("submit", (e) => {
        e.preventDefault();
        if (validateEmail(newsletterEmail.value)) {
            hidePopup(newsletterPopup);
            showSuccessAndRedirect();
        } else {
            newsletterEmail.classList.add("is-invalid");
        }
    });

    // Close popup when clicking X button
    closePopupBtn.addEventListener("click", () => {
        hidePopup(newsletterPopup);
        showSuccessAndRedirect();
    });

    // Validate newsletter email on input
    newsletterEmail.addEventListener("input", () => {
        if (validateEmail(newsletterEmail.value)) {
            newsletterEmail.classList.remove("is-invalid");
        }
    });

    // Reset AOS animations when switching forms
    const resetAnimations = () => {
        document.querySelectorAll("[data-aos]").forEach((element) => {
            element.classList.remove("aos-animate");
            setTimeout(() => {
                element.classList.add("aos-animate");
            }, 10);
        });
    };

    // Update form switching function
    const switchForms = (toSignup) => {
        if (toSignup) {
            authCard.classList.add("signup-active");
            setTimeout(() => {
                loginForm.classList.remove("active-form");
                signupForm.classList.add("active-form");
                resetAnimations();
            }, 300);
        } else {
            authCard.classList.remove("signup-active");
            setTimeout(() => {
                signupForm.classList.remove("active-form");
                loginForm.classList.add("active-form");
                resetAnimations();
            }, 300);
        }
    };

    // Function to handle text animation
    const animateText = () => {
        brandSlogan.classList.remove("typewriter-text");
        brandSlogan.style.width = "0";
        brandSlogan.textContent = phrases[currentPhrase];
        currentPhrase = (currentPhrase + 1) % phrases.length;

        void brandSlogan.offsetWidth; // Force reflow

        brandSlogan.classList.add("typewriter-text");
    };

    // Function to start animation
    const startAnimation = () => {
        animateText();
        animationInterval = setInterval(animateText, 4000);
    };

    // Function to stop animation
    const stopAnimation = () => {
        clearInterval(animationInterval);
        brandSlogan.classList.remove("typewriter-text");
        brandSlogan.style.width = "0";
        brandSlogan.textContent = '"Fast, Smart, Limitless Shopping"';

        // Force reflow
        void brandSlogan.offsetWidth;

        // Add animation back
        brandSlogan.classList.add("typewriter-text");
    };

    // Handle form switching
    switchLinks.forEach((link) => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            const targetForm = link.getAttribute("data-form");
            switchForms(targetForm === "signup");

            if (targetForm === "signup") {
                startAnimation();
            } else {
                stopAnimation();
            }
        });
    });

    // Password visibility toggle
    passwordToggles.forEach((toggle) => {
        toggle.addEventListener("click", () => {
            const passwordField = toggle.parentElement.querySelector(
                'input[type="password"], input[type="text"]'
            );
            const icon = toggle.querySelector("i");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });
    });

    // Add input event listeners for real-time validation
    signupName.addEventListener("input", () => {
        signupName.classList.toggle(
            "is-invalid",
            signupName.value.trim().length === 0
        );
    });

    // Add input event listener for phone field
    signupPhone.addEventListener("input", (e) => {
        handlePhoneValidation(e.target);
    });

    // Add focus event to handle placeholder
    signupPhone.addEventListener("focus", (e) => {
        if (!e.target.value) {
            e.target.placeholder = "1XXXXXXXXX";
        }
    });

    signupPhone.addEventListener("blur", (e) => {
        if (!e.target.value) {
            e.target.placeholder = "Phone number";
        }
    });
});

// IDEA: End Login*/
