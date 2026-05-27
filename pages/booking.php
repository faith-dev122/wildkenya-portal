<?php
// ============================================================
// WildKenya — Booking Page (pages/booking.php)
// Book a safari — select park, guide, date and group size
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

// ---- Must be logged in to book ----
if (!isset($_SESSION['user_id'])) {
    header("Location: /wildkenya/pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error   = '';

// ---- Pre-select park or guide from URL ----
$preselect_park  = isset($_GET['park_id'])  ? (int)$_GET['park_id']  : 0;
$preselect_guide = isset($_GET['guide_id']) ? (int)$_GET['guide_id'] : 0;

// ---- Fetch all parks for dropdown ----
$parks = $conn->query("SELECT id, name, entry_fee_citizen FROM parks ORDER BY name ASC");

// ---- Fetch all guides for dropdown ----
$guides_result = $conn->query("
    SELECT g.id, g.price_per_day, g.rating, u.name
    FROM guides g
    JOIN users u ON g.user_id = u.id
    WHERE g.available = 1
    ORDER BY g.rating DESC
");

// ---- Handle booking form submission ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {

    $park_id      = (int)($_POST['park_id'] ?? 0);
    $guide_id     = !empty($_POST['guide_id']) ? (int)$_POST['guide_id'] : null;
    $booking_date = trim($_POST['booking_date'] ?? '');
    $duration     = (int)($_POST['duration_days'] ?? 1);
    $group_size   = (int)($_POST['group_size'] ?? 1);
    $special_req  = trim($_POST['special_requests'] ?? '');

    // ---- Validation ----
    if ($park_id <= 0) {
        $error = 'Please select a national park.';

    } elseif (empty($booking_date)) {
        $error = 'Please select a booking date.';

    } elseif (strtotime($booking_date) < strtotime('today')) {
        $error = 'Booking date cannot be in the past.';

    } elseif ($duration < 1 || $duration > 30) {
        $error = 'Duration must be between 1 and 30 days.';

    } elseif ($group_size < 1 || $group_size > 20) {
        $error = 'Group size must be between 1 and 20 people.';

    } else {
        // ---- Calculate total cost ----
        // Fetch park entry fee
        $fee_stmt = $conn->prepare(
            "SELECT entry_fee_citizen FROM parks WHERE id = ?"
        );
        $fee_stmt->bind_param("i", $park_id);
        $fee_stmt->execute();
        $park_fee = $fee_stmt->get_result()->fetch_assoc()['entry_fee_citizen'];

        // Fetch guide price if selected
        $guide_price = 0;
        if ($guide_id) {
            $gp_stmt = $conn->prepare(
                "SELECT price_per_day FROM guides WHERE id = ?"
            );
            $gp_stmt->bind_param("i", $guide_id);
            $gp_stmt->execute();
            $guide_price = $gp_stmt->get_result()->fetch_assoc()['price_per_day'] ?? 0;
        }

        // Total = (park fee × group size × duration) + (guide price × duration)
        $total_cost = ($park_fee * $group_size * $duration) + ($guide_price * $duration);

        // ---- Insert booking ----
        $ins = $conn->prepare("
            INSERT INTO bookings
                (tourist_id, guide_id, park_id, booking_date,
                 duration_days, group_size, total_cost,
                 status, special_requests)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)
        ");
        $ins->bind_param(
            "iiisiiis",
            $user_id, $guide_id, $park_id, $booking_date,
            $duration, $group_size, $total_cost, $special_req
        );

        if ($ins->execute()) {
            $booking_id = $conn->insert_id;
            $success    = "Your safari booking has been submitted successfully!";
        } else {
            $error = 'Booking failed. Please try again.';
        }
    }
}
?>

<!-- ============================================================
     PAGE BANNER
============================================================ -->
<section class="py-4 text-white"
         style="background-color: #1a3c2e;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="/wildkenya/" class="text-white-50">Home</a>
                </li>
                <li class="breadcrumb-item active text-white">Book a Safari</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">📅 Book Your Safari</h2>
        <p class="text-white-50 mb-0">
            Select your park, guide and dates to plan your adventure
        </p>
    </div>
</section>

<!-- ============================================================
     BOOKING FORM
============================================================ -->
<section class="py-5" style="background:#f4f6f9;">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <!-- ---- FORM ---- -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-calendar-plus-fill text-success me-2"></i>
                            Safari Booking Details
                        </h4>

                        <!-- Success Message -->
                        <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong><?= $success ?></strong>
                            <br>
                            <small>
                                Booking reference: <strong>#<?= $booking_id ?? '' ?></strong>
                                — Status: <span class="badge bg-warning text-dark">Pending</span>
                            </small>
                            <div class="mt-3 d-flex gap-2">
                                <a href="dashboard.php" class="btn btn-success btn-sm">
                                    <i class="bi bi-speedometer2 me-1"></i>View My Bookings
                                </a>
                                <a href="booking.php" class="btn btn-outline-success btn-sm">
                                    Book Another
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Error Message -->
                        <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!$success): ?>
                        <form method="POST" action="booking.php" id="bookingForm">

                            <!-- Step 1: Select Park -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    1. Select National Park
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="park_id" id="park_id"
                                        class="form-select form-select-lg"
                                        onchange="updateCost()" required>
                                    <option value="">— Choose a park —</option>
                                    <?php while ($park = $parks->fetch_assoc()): ?>
                                    <option value="<?= $park['id'] ?>"
                                            data-fee="<?= $park['entry_fee_citizen'] ?>"
                                            <?= ($preselect_park === (int)$park['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($park['name']) ?>
                                        (KES <?= number_format($park['entry_fee_citizen'], 0) ?>/person)
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Step 2: Select Guide (optional) -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    2. Select a Safari Guide
                                    <span class="text-muted fw-normal">(optional)</span>
                                </label>
                                <select name="guide_id" id="guide_id"
                                        class="form-select"
                                        onchange="updateCost()">
                                    <option value="">— No guide (self-guided) —</option>
                                    <?php while ($g = $guides_result->fetch_assoc()): ?>
                                    <option value="<?= $g['id'] ?>"
                                            data-price="<?= $g['price_per_day'] ?>"
                                            <?= ($preselect_guide === (int)$g['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g['name']) ?>
                                        — KES <?= number_format($g['price_per_day'], 0) ?>/day
                                        (⭐ <?= number_format($g['rating'], 1) ?>)
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                                <small class="text-muted">
                                    A guide enhances your safari experience significantly
                                </small>
                            </div>

                            <!-- Step 3: Date & Duration -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        3. Safari Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="booking_date"
                                           class="form-control form-control-lg"
                                           min="<?= date('Y-m-d') ?>"
                                           value="<?= $_POST['booking_date'] ?? '' ?>"
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        Duration (days) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="duration_days"
                                           id="duration_days"
                                           class="form-control form-control-lg"
                                           min="1" max="30" value="1"
                                           onchange="updateCost()" required>
                                </div>
                            </div>

                            <!-- Step 4: Group Size -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    4. Group Size <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="number" name="group_size"
                                           id="group_size"
                                           class="form-control"
                                           style="max-width:120px;"
                                           min="1" max="20" value="1"
                                           onchange="updateCost()" required>
                                    <span class="text-muted small">
                                        people (max 20 per booking)
                                    </span>
                                </div>
                            </div>

                            <!-- Step 5: Special Requests -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    5. Special Requests
                                    <span class="text-muted fw-normal">(optional)</span>
                                </label>
                                <textarea name="special_requests"
                                          class="form-control" rows="3"
                                          placeholder="e.g. wheelchair access, dietary requirements, specific animals to see..."><?= htmlspecialchars($_POST['special_requests'] ?? '') ?></textarea>
                            </div>

                            <!-- Cost Summary -->
                            <div class="p-3 rounded-3 mb-4"
                                 style="background:#e8f5e9; border:1px solid #a5d6a7;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="fw-bold mb-0">Estimated Total Cost</p>
                                        <small class="text-muted" id="cost-breakdown">
                                            Select a park to see cost
                                        </small>
                                    </div>
                                    <h3 class="fw-bold text-success mb-0" id="total_cost_display">
                                        KES 0
                                    </h3>
                                </div>
                                <input type="hidden" name="total_cost" id="total_cost_hidden" value="0">
                            </div>

                            <!-- Submit -->
                            <button type="submit" name="submit_booking"
                                    class="btn btn-success btn-lg w-100 py-3 fw-bold">
                                <i class="bi bi-calendar-check-fill me-2"></i>
                                Confirm Safari Booking
                            </button>

                            <p class="text-muted small text-center mt-3 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Booking status will be <strong>Pending</strong> until confirmed
                                by our team. No payment is taken at this stage.
                            </p>

                        </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <!-- ---- SIDEBAR ---- -->
            <div class="col-lg-4">

                <!-- Booking Tips -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-lightbulb-fill text-warning me-2"></i>
                            Booking Tips
                        </h5>
                        <ul class="list-unstyled small text-muted">
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                Book at least 2 weeks in advance for the best guide availability
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                Visit Maasai Mara July–October for the Great Migration
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                Early morning game drives offer the best wildlife sightings
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                A certified guide dramatically improves your experience
                            </li>
                            <li class="d-flex gap-2">
                                <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                Minimum 1 day recommended per park
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- My Recent Bookings -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-clock-history text-success me-2"></i>
                            My Recent Bookings
                        </h5>
                        <?php
                        $recent = $conn->prepare("
                            SELECT b.*, p.name as park_name
                            FROM bookings b
                            JOIN parks p ON b.park_id = p.id
                            WHERE b.tourist_id = ?
                            ORDER BY b.created_at DESC LIMIT 3
                        ");
                        $recent->bind_param("i", $user_id);
                        $recent->execute();
                        $recent_bookings = $recent->get_result();
                        ?>
                        <?php if ($recent_bookings->num_rows === 0): ?>
                        <p class="text-muted small">No bookings yet.</p>
                        <?php else: ?>
                        <?php while ($rb = $recent_bookings->fetch_assoc()):
                            $status_color = match($rb['status']) {
                                'confirmed'  => 'success',
                                'pending'    => 'warning text-dark',
                                'cancelled'  => 'danger',
                                'completed'  => 'primary',
                                default      => 'secondary'
                            };
                        ?>
                        <div class="border-bottom pb-2 mb-2 small">
                            <div class="d-flex justify-content-between">
                                <strong><?= htmlspecialchars($rb['park_name']) ?></strong>
                                <span class="badge bg-<?= $status_color ?>">
                                    <?= ucfirst($rb['status']) ?>
                                </span>
                            </div>
                            <span class="text-muted">
                                <?= date('d M Y', strtotime($rb['booking_date'])) ?>
                                &mdash; KES <?= number_format($rb['total_cost'], 0) ?>
                            </span>
                        </div>
                        <?php endwhile; ?>
                        <a href="dashboard.php" class="btn btn-outline-success btn-sm w-100 mt-2">
                            View All Bookings
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     JAVASCRIPT — Live Cost Calculator
============================================================ -->
<script>
function updateCost() {
    const parkSelect  = document.getElementById('park_id');
    const guideSelect = document.getElementById('guide_id');
    const duration    = parseInt(document.getElementById('duration_days').value) || 1;
    const groupSize   = parseInt(document.getElementById('group_size').value) || 1;

    const selectedPark  = parkSelect.options[parkSelect.selectedIndex];
    const selectedGuide = guideSelect.options[guideSelect.selectedIndex];

    const parkFee    = parseFloat(selectedPark?.dataset?.fee  || 0);
    const guidePrice = parseFloat(selectedGuide?.dataset?.price || 0);

    const parkTotal  = parkFee * groupSize * duration;
    const guideTotal = guidePrice * duration;
    const total      = parkTotal + guideTotal;

    document.getElementById('total_cost_display').textContent =
        'KES ' + total.toLocaleString();
    document.getElementById('total_cost_hidden').value = total;

    // Breakdown text
    let breakdown = '';
    if (parkFee > 0) {
        breakdown += `Park: KES ${parkFee.toLocaleString()} × ${groupSize} people × ${duration} day(s)`;
    }
    if (guidePrice > 0) {
        breakdown += ` + Guide: KES ${guidePrice.toLocaleString()} × ${duration} day(s)`;
    }
    if (!breakdown) breakdown = 'Select a park to see cost';

    document.getElementById('cost-breakdown').textContent = breakdown;
}

// Run on page load if park is pre-selected
document.addEventListener('DOMContentLoaded', updateCost);
</script>

<?php require_once '../includes/footer.php'; ?>
