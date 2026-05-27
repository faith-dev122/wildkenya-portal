<?php
// ============================================================
// WildKenya — Register Page (pages/register.php)
// New user registration with secure password hashing
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: /wildkenya/pages/dashboard.php");
    exit();
}

$error   = '';
$success = '';

// ---- Handle Form Submission ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect and sanitize inputs
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $role     = $_POST['role'] ?? 'tourist';
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // ---- Server-side Validation ----
    if (empty($name) || strlen($name) < 2) {
        $error = 'Please enter your full name.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';

    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';

    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';

    } elseif (!in_array($role, ['tourist', 'guide'])) {
        $error = 'Please select a valid account type.';

    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = 'An account with that email already exists. Please login.';
        } else {
            // Hash password securely using bcrypt
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // Insert new user
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $name, $email, $hashed, $role, $phone);

            if ($stmt->execute()) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!-- ============================================================
     REGISTER FORM
============================================================ -->
<section class="py-5" style="background-color: #f8f9fa; min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <!-- Card -->
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div style="font-size: 2.5rem;">🦒</div>
                            <h2 class="fw-bold mt-2">Create Account</h2>
                            <p class="text-muted small">Join WildKenya and start planning your safari</p>
                        </div>

                        <!-- Error Alert -->
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible alert-auto fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <!-- Success Alert -->
                        <?php if ($success): ?>
                        <div class="alert alert-success fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?= htmlspecialchars($success) ?>
                            <a href="login.php" class="alert-link ms-2">Login now →</a>
                        </div>
                        <?php endif; ?>

                        <!-- Registration Form -->
                        <?php if (!$success): ?>
                        <form id="registerForm" method="POST" action="register.php" novalidate>

                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold small">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       class="form-control"
                                       placeholder="e.g. Faith Wanjiku"
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                                       required>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold small">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       class="form-control"
                                       placeholder="you@example.com"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       required>
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-bold small">
                                    Phone Number <span class="text-muted">(optional)</span>
                                </label>
                                <input type="tel"
                                       id="phone"
                                       name="phone"
                                       class="form-control"
                                       placeholder="+254 7XX XXX XXX"
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>

                            <!-- Account Type -->
                            <div class="mb-3">
                                <label class="form-label fw-bold small">
                                    Account Type <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="role"
                                               id="role_tourist" value="tourist"
                                               <?= (($_POST['role'] ?? 'tourist') === 'tourist') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-success w-100" for="role_tourist">
                                            <i class="bi bi-person-fill me-1"></i>Tourist
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="role"
                                               id="role_guide" value="guide"
                                               <?= (($_POST['role'] ?? '') === 'guide') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-success w-100" for="role_guide">
                                            <i class="bi bi-person-badge-fill me-1"></i>Safari Guide
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold small">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="form-control"
                                           placeholder="Minimum 8 characters"
                                           required>
                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            onclick="togglePassword('password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <!-- Password Strength Bar -->
                                <div class="progress mt-2" style="height: 5px;">
                                    <div id="password-strength"
                                         class="progress-bar"
                                         style="width: 0%; transition: width 0.3s;"></div>
                                </div>
                                <small id="strength-text" class="text-muted"></small>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label fw-bold small">
                                    Confirm Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           id="confirm_password"
                                           name="confirm_password"
                                           class="form-control"
                                           placeholder="Repeat your password"
                                           required>
                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            onclick="togglePassword('confirm_password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                                <i class="bi bi-person-plus-fill me-2"></i>Create My Account
                            </button>

                        </form>
                        <?php endif; ?>

                        <!-- Login Link -->
                        <hr class="my-4">
                        <p class="text-center text-muted small mb-0">
                            Already have an account?
                            <a href="login.php" class="text-success fw-bold">Login here</a>
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
// Show/hide password toggle
function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

// Password strength checker
document.getElementById('password').addEventListener('input', function () {
    const val      = this.value;
    const bar      = document.getElementById('password-strength');
    const text     = document.getElementById('strength-text');
    let   strength = 0;

    if (val.length >= 8)          strength++;
    if (/[A-Z]/.test(val))        strength++;
    if (/[0-9]/.test(val))        strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const colors = ['', 'danger', 'warning', 'info', 'success'];
    const labels = ['', 'Weak', 'Fair', 'Good', 'Strong 💪'];

    bar.className  = `progress-bar bg-${colors[strength]}`;
    bar.style.width = (strength * 25) + '%';
    text.textContent = val.length > 0 ? 'Strength: ' + (labels[strength] || '') : '';
});
</script>

<?php require_once '../includes/footer.php'; ?>
