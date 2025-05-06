<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login / Sign Up - Tech Store</title>

    <!-- External CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Mulish:wght@400;600;700&family=Tektur:wght@400;600&display=swap"
        rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/login.css" />
</head>

<body>
    <!-- Main Container -->
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center p-0 auth-container">
        <div class="auth-card card shadow-lg border-0 overflow-hidden" data-aos="fade-up" data-aos-duration="1000">
            <div class="row g-0 h-100 position-relative">
                <!-- Form Section -->
                <div class="col-lg-6 form-section">
                    <div class="form-wrapper">
                        <!-- Login Form -->
                        <form id="login-form" class="auth-form active-form" method="POST" action="authenticate.php">
                            <div class="text-center mb-4" data-aos="fade-down" data-aos-delay="200">
                                <h2 class="form-title">Login</h2>
                                <p class="form-subtitle">
                                    Login to access your account
                                </p>
                            </div>

                            <div class="form-group position-relative mb-3">
                                <input type="email" class="form-control" id="login-email" placeholder="Email address"
                                    name="login-email" required />
                                <label for="login-email" class="form-label">Email address</label>
                            </div>

                            <div class="form-group position-relative mb-3">
                                <input type="password" class="form-control" id="login-password" name="login-password"
                                    placeholder="Password" required />
                                <label for="login-password" class="form-label">Password</label>
                                <span class="password-toggle-icon"><i class="fas fa-eye"></i></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="remember-me" />
                                    <div class="toggle-track">
                                        <div class="toggle-thumb"></div>
                                    </div>
                                    <span class="toggle-label">Remember me</span>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                                Login
                            </button>
                            <p class="text-center text-muted mb-3">
                                Don't have an account?
                                <a href="#" class="switch-form-link" data-form="signup">Sign up</a>
                            </p>

                            <div class="separator my-4">
                                <span>Or login with</span>
                            </div>

                            <div class="social-login-buttons">
                                <button type="button" class="btn btn-outline-secondary w-100 social-login-btn">
                                    <i class="fab fa-google me-2"></i>Login
                                    with Google
                                </button>
                            </div>
                        </form>

                        <!-- Signup Form -->
                        <form id="signup-form" class="auth-form" method="POST" action="authenticate.php">
                            <div data-aos="fade-down" data-aos-delay="200">
                                <h2 class="signup-title">Sign Up</h2>
                                <p class="signup-subtitle">
                                    Create your new account
                                </p>
                            </div>

                            <div class="signup-form-content">
                                <div class="form-group position-relative mb-3">
                                    <input type="text" class="form-control" id="signup-name" name="signup-name"
                                        placeholder="Full Name" required />
                                    <label for="signup-name" class="form-label">Full Name</label>
                                </div>

                                <div class="form-group position-relative mb-3">
                                    <input type="email" class="form-control" id="signup-email" name="signup-email"
                                        placeholder="Email address" required />
                                    <label for="signup-email" class="form-label">Email address</label>
                                </div>

                                <div class="form-group position-relative mb-3">
                                    <div class="phone-input-wrapper">
                                        <input type="tel" class="form-control" id="signup-phone" name="signup-phone"
                                            placeholder="Phone number" required />
                                        <label for="signup-phone" class="form-label">Phone number</label>
                                        <span class="phone-prefix">+20</span>
                                    </div>
                                </div>

                                <div class="form-group position-relative mb-3">
                                    <input type="password" class="form-control" id="signup-password"
                                        name="signup-password" placeholder="Password" required minlength="8" />
                                    <label for="signup-password" class="form-label">Password</label>
                                    <span class="password-toggle-icon"><i class="fas fa-eye"></i></span>
                                </div>

                                <div class="password-strength mb-2">
                                    <div class="strength-meter">
                                        <div class="strength-meter-fill" data-strength="0"></div>
                                    </div>
                                    <div class="strength-text">
                                        Password Strength: <span>Weak</span>
                                    </div>
                                </div>

                                <div class="password-requirements mb-3">
                                    <p class="requirements-title">
                                        Password must contain:
                                    </p>
                                    <div class="requirements-list">
                                        <div class="requirement-item">
                                            <i class="fas fa-circle"></i><span>At least 8 characters</span>
                                        </div>
                                        <div class="requirement-item">
                                            <i class="fas fa-circle"></i><span>At least one uppercase
                                                letter</span>
                                        </div>
                                        <div class="requirement-item">
                                            <i class="fas fa-circle"></i><span>At least one lowercase
                                                letter</span>
                                        </div>
                                        <div class="requirement-item">
                                            <i class="fas fa-circle"></i><span>At least one number</span>
                                        </div>
                                        <div class="requirement-item">
                                            <i class="fas fa-circle"></i><span>At least one special
                                                character (!@#$%^&*)</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group position-relative mb-3">
                                    <input type="password" class="form-control" id="signup-confirm-password"
                                        placeholder="Confirm Password" required />
                                    <label for="signup-confirm-password" class="form-label">Confirm Password</label>
                                    <span class="password-toggle-icon"><i class="fas fa-eye"></i></span>
                                    <div class="invalid-feedback">
                                        Passwords do not match.
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                                    Sign Up
                                </button>
                                <p class="text-center text-muted mb-0">
                                    Already have an account?
                                    <a href="#" class="switch-form-link" data-form="login">Login</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Image Section -->
                <div
                    class="col-lg-6 d-none d-lg-flex flex-column align-items-center justify-content-center p-5 image-section">
                    <h1 class="brand-name mb-4" data-aos="fade-left" data-aos-delay="400">
                        Quantum
                    </h1>
                    <div class="image-container" data-aos="zoom-in" data-aos-delay="600">
                        <img src="images/freepik__background__10485.png" alt="login image"
                            class="section-image login-image" />
                        <img src="images/freepik__background__24334.png" alt="signup image"
                            class="section-image signup-image" />
                    </div>
                    <p class="brand-slogan text-center typewriter-text" data-text='"Fast, Smart, Limitless Shopping"'>
                        "Fast, Smart, Limitless Shopping"
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Popups -->
    <div class="popup-overlay" id="newsletter-popup">
        <div class="popup-content">
            <button class="close-popup">
                <i class="fas fa-times"></i>
            </button>
            <h2>Subscribe to our Newsletter</h2>
            <p>Join thousands getting emails in their inbox.</p>
            <form id="newsletter-form">
                <div class="form-group position-relative">
                    <input type="email" class="form-control" id="newsletter-email" placeholder="email@example.com"
                        required />
                    <div class="invalid-feedback">
                        Please enter a valid email address.
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    Subscribe
                </button>
            </form>
        </div>
    </div>

    <div class="popup-overlay" id="success-popup">
        <div class="popup-content">
            <h2>You signed up successfully!</h2>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="login.js"></script>
</body>

</html>