<?php
// ============================================================
// WildKenya — User Dashboard (pages/dashboard.php)
// Shows user bookings, profile and quick actions
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

// ---- Protect this page — must be logged in ----
if (!isset($_SESSION['user_id'])) {
    header("Location: /wildkenya/pages/login.php");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// ---- Fetch user's bookings ----
$bookings_sql = "
    SELECT b.*, p.name AS park_name, p.county,
           u.name AS guide_name
    FROM bookings b
    JOIN parks p ON b.park_id = p.id
    LEFT JOIN guides g ON b.guide_id = g.id
    LEFT JOIN users u ON g.user_id = u.id
    WHERE b.tourist_id = ?
    ORDER BY b.created_at DESC
";
$stmt = $conn->prepare($bookings_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
$total_bookings = $bookings->num_rows;

// ---- Fetch user details ----
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// ---- Count stats ----
$confirmed = $conn->prepare("SELECT COUNT(*) as c FROM bookings WHERE tourist_id = ? AND status = 'confirmed'");
$confirmed->bind_param("i", $user_id);
$confirmed->execute();
$confirmed_count = $confirmed->get_result()->fetch_assoc()['c'];

$pending = $conn->prepare("SELECT COUNT(*) as c FROM bookings WHERE tourist_id = ? AND status = 'pending'");
$pending->bind_param("i", $user_id);
$pending->execute();
$pending_count = $pending->get_result()->fetch_assoc()['c'];

// ---- Status badge helper ----
function bookingBadge($status) {
    return match($status) {
        'confirmed'  => 'success',
        'pending'    => 'warning text-dark',
        'cancelled'  => 'danger',
        'completed'  => 'primary',
        default      => 'secondary'
    };
}
?>

<!-- ============================================================
     PAGE HEADER
============================================================ -->
<section class="py-4 text-white" style="background-color: #1a3c2e;">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <!-- Avatar Circle -->
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                 style="width:55px; height:55px; background-color:#2d6a4f; font-size:1.4rem;">
                <?= strtoupper(substr($user_name, 0, 1)) ?>
            </div>
            <div>
                <h3 class="fw-bold mb-0">Welcome back, <?= htmlspecialchars(explode(' ', $user_name)[0]) ?>! 👋</h3>
                <p class="text-white-50 mb-0 small">
                    <?= ucfirst($user_role) ?> Account &mdash;
                    Member since <?= date('F Y', strtotime($user['created_at'])) ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     STATS CARDS
============================================================ -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <div class="row g-3">

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div style="font-size:1.8rem;">🗺️</div>
                    <h4 class="fw-bold text-success mb-0"><?= $total_bookings ?></h4>
                    <small class="text-muted">Total Bookings</small>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div style="font-size:1.8rem;">✅</div>
                    <h4 class="fw-bold text-success mb-0"><?= $confirmed_count ?></h4>
                    <small class="text-muted">Confirmed</small>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div style="font-size:1.8rem;">⏳</div>
                    <h4 class="fw-bold text-warning mb-0"><?= $pending_count ?></h4>
                    <small class="text-muted">Pending</small>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div style="font-size:1.8rem;">🌍</div>
                    <h4 class="fw-bold text-primary mb-0">10</h4>
                    <small class="text-muted">Parks Available</small>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     MAIN CONTENT
============================================================ -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">

            <!-- ---- LEFT: Bookings Table ---- -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-calendar3 text-success me-2"></i>My Bookings
                        </h5>
                        <a href="booking.php" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>New Booking
                        </a>
                    </div>
                    <div class="card-body px-4">

                        <?php if ($total_bookings === 0): ?>
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div style="font-size:3rem;">🦁</div>
                            <h5 class="mt-3">No bookings yet</h5>
                            <p class="text-muted small">
                                Start planning your safari adventure!
                            </p>
                            <a href="parks.php" class="btn btn-success">
                                Explore Parks
                            </a>
                        </div>

                        <?php else: ?>
                        <!-- Bookings Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light small text-muted">
                                    <tr>
                                        <th>#</th>
                                        <th>Park</th>
                                        <th>Date</th>
                                        <th>Days</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 1;
                                while ($booking = $bookings->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="text-muted small"><?= $i++ ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($booking['park_name']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i>
                                            <?= htmlspecialchars($booking['county']) ?>
                                        </small>
                                    </td>
                                    <td class="small">
                                        <?= date('d M Y', strtotime($booking['booking_date'])) ?>
                                    </td>
                                    <td class="small text-center">
                                        <?= $booking['duration_days'] ?>
                                    </td>
                                    <td class="small">
                                        KES <?= number_format($booking['total_cost'], 0) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= bookingBadge($booking['status']) ?>">
                                            <?= ucfirst($booking['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <!-- ---- RIGHT: Profile + Quick Links ---- -->
            <div class="col-lg-4">

                <!-- Profile Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-person-circle text-success me-2"></i>My Profile
                        </h5>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-person text-success mt-1"></i>
                                <span><?= htmlspecialchars($user['name']) ?></span>
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-envelope text-success mt-1"></i>
                                <span><?= htmlspecialchars($user['email']) ?></span>
                            </li>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-shield-check text-success mt-1"></i>
                                <span><?= ucfirst($user['role']) ?> Account</span>
                            </li>
                            <?php if ($user['phone']): ?>
                            <li class="mb-2 d-flex gap-2">
                                <i class="bi bi-telephone text-success mt-1"></i>
                                <span><?= htmlspecialchars($user['phone']) ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-lightning-fill text-warning me-2"></i>Quick Links
                        </h5>
                        <div class="d-grid gap-2">
                            <a href="parks.php" class="btn btn-outline-success btn-sm text-start">
                                <i class="bi bi-map-fill me-2"></i>Browse Parks
                            </a>
                            <a href="animals.php" class="btn btn-outline-success btn-sm text-start">
                                <i class="bi bi-camera-fill me-2"></i>View Wildlife
                            </a>
                            <a href="guides.php" class="btn btn-outline-success btn-sm text-start">
                                <i class="bi bi-person-badge-fill me-2"></i>Find a Guide
                            </a>
                            <a href="booking.php" class="btn btn-success btn-sm text-start">
                                <i class="bi bi-calendar-plus-fill me-2"></i>Book a Safari
                            </a>
                            <hr class="my-1">
                            <a href="/wildkenya/logout.php"
                               class="btn btn-outline-danger btn-sm text-start">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
