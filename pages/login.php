<?php
// ============================================================
// WildKenya — Login Page (pages/login.php)
// Secure login using PHP sessions and password_verify()
// Bonus Feature: Remember Me Functionality
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: /wildkenya/pages/dashboard.php");
    exit();
}

$error = '';

// ---- Show message if redirected from logout or register ----
$msg = $_GET['msg'] ?? '';

// ---- Handle Form Submission ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember_me']);

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = 'Please enter both your email and password.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';

    } else {
        // Look up user by email
        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role FROM users WHERE email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password against stored hash
            if (password_verify($password, $user['password'])) {

                // ---- Login Successful — Start Session ----
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email']= $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // ---- Bonus Feature: Remember Me ----
                // Token is derived from the password hash, so it
                // automatically becomes invalid if the password is changed
                if ($remember) {
                    $token = hash_hmac('sha256', (string)$user['id'], $user['password']);
                    $cookie_value = $user['id'] . ':' . $token;
                    setcookie('wildkenya_remember', $cookie_value, time() + (30 * 24 * 60 * 60), '/');
                }

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: /wildkenya/admin/");
                } else {
                    header("Location: /wildkenya/pages/dashboard.php");
                }
                exit();

            } else {
                // Wrong password — don't reveal which field is wrong (security)
                $error = 'Incorrect email or password. Please try again.';
            }
        } else {
            $error = 'Incorrect email or password. Please try again.';
        }
    }
}
?>

<!-- ============================================================
     LOGIN FORM
============================================================ -->
<section class="py-5" style="background-color: #f8f9fa; min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">

                <!-- Card -->
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div style="font-size: 2.5rem;">🦒</div>
                            <h2 class="fw-bold mt-2">Welcome Back</h2>
                            <p class="text-muted small">Login to your WildKenya account</p>
                        </div>

                        <!-- Logout success message -->
                        <?php if ($msg === 'logged_out'): ?>
                        <div class="alert alert-success alert-auto fade show small">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            You have been logged out successfully.
                        </div>
                        <?php endif; ?>

                        <!-- Error Alert -->
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible alert-auto fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form id="loginForm" method="POST" action="login.php" novalidate>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold small">
                                    Email Address
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       class="form-control"
                                       placeholder="you@example.com"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       autofocus
                                       required>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label for="password" class="form-label fw-bold small">
                                        Password
                                    </label>
                                </div>
                                <div class="input-group">
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="form-control"
                                           placeholder="Enter your password"
                                           required>
                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            onclick="togglePassword('password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-4 form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="remember_me"
                                       name="remember_me">
                                <label class="form-check-label small text-muted" for="remember_me">
                                    Remember me for 30 days
                                </label>
                            </div>

                            <!-- Submit -->
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>

                        </form>

                        <!-- Divider -->
                        <hr class="my-4">

                        <!-- Register Link -->
                        <p class="text-center text-muted small mb-0">
                            Don't have an account?
                            <a href="register.php" class="text-success fw-bold">Register Free</a>
                        </p>


                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
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
</script>

<?php require_once '../includes/footer.php'; ?>