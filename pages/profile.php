<?php
// ============================================================
// WildKenya — Profile Management (pages/profile.php)
// Bonus Feature: User Profile Management + Password Reset
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

// Protect this page — must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$message = '';
$error   = '';

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// ---- HANDLE: Update profile details ----
if (isset($_POST['update_profile'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $upd = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        $upd->bind_param("sssi", $name, $email, $phone, $user_id);
        if ($upd->execute()) {
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;
            $message = "Profile updated successfully.";
            $user['name']  = $name;
            $user['email'] = $email;
            $user['phone'] = $phone;
        } else {
            $error = "Could not update profile. Email may already be in use.";
        }
    }
}

// ---- HANDLE: Change password (Bonus Feature: Password Reset) ----
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password      = $_POST['new_password'];
    $confirm_password  = $_POST['confirm_password'];

    if (!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif (strlen($new_password) < 8) {
        $error = "New password must be at least 8 characters.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $upd->bind_param("si", $new_hash, $user_id);
        if ($upd->execute()) {
            $message = "Password changed successfully.";
        } else {
            $error = "Could not change password.";
        }
    }
}
?>

<section class="py-4 text-white" style="background-color:#1a3c2e;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php" class="text-white-50">Dashboard</a></li>
                <li class="breadcrumb-item active text-white">My Profile</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">My Profile</h2>
        <p class="text-white-50 mb-0">Manage your account details and password</p>
    </div>
</section>

<section class="py-5" style="background:#f4f6f9;">
    <div class="container">

        <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Profile Details -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-1">Account Details</h4>
                        <p class="text-muted small mb-4">Update your personal information</p>

                        <form method="POST" action="profile.php">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control"
                                       value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" name="phone" class="form-control"
                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                       placeholder="e.g. +254 700 000 000">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Account Role</label>
                                <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" disabled>
                                <small class="text-muted">Role cannot be changed by the user</small>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-success px-4">
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-1">Change Password</h4>
                        <p class="text-muted small mb-4">Update your password regularly to keep your account secure</p>

                        <form method="POST" action="profile.php">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">New Password</label>
                                <input type="password" name="new_password" class="form-control"
                                       minlength="8" required>
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control"
                                       minlength="8" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-outline-success px-4">
                                Update Password
                            </button>
                        </form>

                        <hr class="my-4">
                        <p class="small text-muted mb-0">
                            Your password is stored using bcrypt hashing and is never visible
                            to anyone, including site administrators. Changing your password
                            here immediately invalidates the old password.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
