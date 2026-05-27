<?php
// ============================================================
// WildKenya — Cookies Demo (pages/preferences.php)
// Demonstrates cookie-based user preference storage
// Course Outline: Session management and cookies
// CAT 2 Requirement: Shopping cart persistence via cookies
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

$message = '';

// ---- HANDLE: Save preferences via cookies ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_prefs'])) {

    $region    = $_POST['preferred_region'] ?? 'All';
    $font_size = $_POST['font_size'] ?? 'medium';
    $currency  = $_POST['currency'] ?? 'KES';

    // Set cookies that expire in 30 days (30 * 24 * 60 * 60 seconds)
    setcookie('pref_region',    $region,    time() + (30 * 24 * 60 * 60), '/');
    setcookie('pref_font_size', $font_size, time() + (30 * 24 * 60 * 60), '/');
    setcookie('pref_currency',  $currency,  time() + (30 * 24 * 60 * 60), '/');

    $message = 'success';
}

// ---- HANDLE: Clear cookies ----
if (isset($_POST['clear_prefs'])) {
    // Delete cookies by setting expiry in the past
    setcookie('pref_region',    '', time() - 3600, '/');
    setcookie('pref_font_size', '', time() - 3600, '/');
    setcookie('pref_currency',  '', time() - 3600, '/');

    $message = 'cleared';
}

// ---- READ: Current cookie values ----
$current_region    = $_COOKIE['pref_region']    ?? 'Not set';
$current_font_size = $_COOKIE['pref_font_size'] ?? 'Not set';
$current_currency  = $_COOKIE['pref_currency']  ?? 'Not set';

// ---- Fetch regions from database for dropdown ----
$regions_result = $conn->query(
    "SELECT DISTINCT region FROM parks ORDER BY region ASC"
);
?>

<!-- PAGE BANNER -->
<section class="py-4 text-white" style="background-color:#1a3c2e;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="/wildkenya/" class="text-white-50">Home</a>
                </li>
                <li class="breadcrumb-item active text-white">
                    User Preferences
                </li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">
            🍪 User Preferences (Cookies Demo)
        </h2>
        <p class="text-white-50 mb-0">
            Your preferences are saved as cookies — they persist even after you close the browser
        </p>
    </div>
</section>

<section class="py-5" style="background:#f4f6f9;">
    <div class="container">
        <div class="row g-4">

            <!-- LEFT: Preference Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <h4 class="fw-bold mb-1">
                            <i class="bi bi-sliders text-success me-2"></i>
                            Set Your Preferences
                        </h4>
                        <p class="text-muted small mb-4">
                            These are saved as browser cookies and remembered for 30 days
                        </p>

                        <!-- Alerts -->
                        <?php if ($message === 'success'): ?>
                        <div class="alert alert-success alert-auto fade show">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Preferences saved as cookies! They will be remembered for <strong>30 days</strong>.
                        </div>
                        <?php elseif ($message === 'cleared'): ?>
                        <div class="alert alert-warning alert-auto fade show">
                            <i class="bi bi-trash-fill me-2"></i>
                            All preference cookies have been cleared.
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="preferences.php">

                            <!-- Preferred Region -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    Preferred Region
                                </label>
                                <select name="preferred_region" class="form-select">
                                    <option value="All"
                                        <?= ($current_region === 'All' || $current_region === 'Not set') ? 'selected' : '' ?>>
                                        All Regions
                                    </option>
                                    <?php while ($r = $regions_result->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($r['region']) ?>"
                                        <?= ($current_region === $r['region']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['region']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                                <small class="text-muted">
                                    Parks will be pre-filtered by this region on your next visit
                                </small>
                            </div>

                            <!-- Font Size -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    Text Size Preference
                                </label>
                                <div class="d-flex gap-3">
                                    <?php foreach (['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'] as $val => $label): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               name="font_size" id="fs_<?= $val ?>"
                                               value="<?= $val ?>"
                                               <?= ($current_font_size === $val || ($current_font_size === 'Not set' && $val === 'medium')) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="fs_<?= $val ?>">
                                            <?= $label ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted">
                                    Adjusts text size across the site
                                </small>
                            </div>

                            <!-- Currency -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    Preferred Currency
                                </label>
                                <select name="currency" class="form-select">
                                    <?php
                                    $currencies = ['KES' => 'KES — Kenyan Shilling', 'USD' => 'USD — US Dollar', 'EUR' => 'EUR — Euro', 'GBP' => 'GBP — British Pound'];
                                    foreach ($currencies as $code => $label):
                                    ?>
                                    <option value="<?= $code ?>"
                                        <?= ($current_currency === $code) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">
                                    Entry fees will be displayed in this currency
                                </small>
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" name="save_prefs"
                                        class="btn btn-success px-4">
                                    <i class="bi bi-save-fill me-2"></i>Save Preferences
                                </button>
                                <button type="submit" name="clear_prefs"
                                        class="btn btn-outline-danger">
                                    <i class="bi bi-trash-fill me-2"></i>Clear Cookies
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Cookie Inspector -->
            <div class="col-lg-5">

                <!-- Current Cookie Values -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-eye-fill text-success me-2"></i>
                            Current Cookie Values
                        </h5>
                        <p class="text-muted small mb-3">
                            This panel reads the cookies currently stored in your browser
                        </p>

                        <table class="table table-sm table-bordered small">
                            <thead class="table-light">
                                <tr>
                                    <th>Cookie Name</th>
                                    <th>Value Stored</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>pref_region</code></td>
                                    <td>
                                        <span class="badge <?= $current_region !== 'Not set' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= htmlspecialchars($current_region) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>pref_font_size</code></td>
                                    <td>
                                        <span class="badge <?= $current_font_size !== 'Not set' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= htmlspecialchars($current_font_size) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><code>pref_currency</code></td>
                                    <td>
                                        <span class="badge <?= $current_currency !== 'Not set' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= htmlspecialchars($current_currency) ?>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            You can also inspect these in Chrome:
                            <strong>F12 → Application → Cookies → localhost</strong>
                        </p>
                    </div>
                </div>

                <!-- Explanation Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-book-fill text-warning me-2"></i>
                            How Cookies Work
                        </h5>
                        <div class="small text-muted">
                            <p class="mb-2">
                                <strong>Sessions vs Cookies:</strong>
                            </p>
                            <ul class="mb-3">
                                <li><strong>$_SESSION</strong> — stored on the <em>server</em>, deleted when browser closes</li>
                                <li><strong>Cookies</strong> — stored in the <em>browser</em>, persist after browser closes</li>
                            </ul>
                            <p class="mb-2">
                                <strong>setcookie() syntax used here:</strong>
                            </p>
                            <code class="d-block p-2 bg-light rounded mb-2" style="font-size:10px;">
                                setcookie('name', 'value', expiry, path);
                            </code>
                            <p class="mb-0">
                                Cookies expire in <strong>30 days</strong> from when they are set.
                                Clearing them sets the expiry to the <strong>past</strong>,
                                which tells the browser to delete them immediately.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
