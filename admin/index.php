<?php
// ============================================================
// WildKenya — Admin Dashboard (admin/index.php)
// Only accessible by users with role = 'admin'
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';

// ---- Protect: Admin only ----
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /wildkenya/pages/login.php");
    exit();
}

// ---- Fetch stats for dashboard cards ----
$total_parks    = $conn->query("SELECT COUNT(*) as c FROM parks")->fetch_assoc()['c'];
$total_animals  = $conn->query("SELECT COUNT(*) as c FROM animals")->fetch_assoc()['c'];
$total_users    = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_bookings = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
$total_reviews  = $conn->query("SELECT COUNT(*) as c FROM reviews")->fetch_assoc()['c'];
$total_guides   = $conn->query("SELECT COUNT(*) as c FROM guides")->fetch_assoc()['c'];

$pending_bookings   = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='pending'")->fetch_assoc()['c'];
$confirmed_bookings = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='confirmed'")->fetch_assoc()['c'];

// ---- Recent users ----
$recent_users = $conn->query(
    "SELECT id, name, email, role, created_at FROM users
     ORDER BY created_at DESC LIMIT 5"
);

// ---- Recent bookings ----
$recent_bookings = $conn->query(
    "SELECT b.*, p.name as park_name, u.name as tourist_name
     FROM bookings b
     JOIN parks p ON b.park_id = p.id
     JOIN users u ON b.tourist_id = u.id
     ORDER BY b.created_at DESC LIMIT 5"
);
?>

<!-- ============================================================
     ADMIN NAVBAR
============================================================ -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color:#111;">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="/wildkenya/admin/">
            🛡️ WildKenya Admin
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white-50 small d-none d-md-block">
                <i class="bi bi-person-fill me-1"></i>
                <?= htmlspecialchars($_SESSION['user_name']) ?>
            </span>
            <a href="/wildkenya/" class="btn btn-outline-light btn-sm">
                <i class="bi bi-globe2 me-1"></i>View Site
            </a>
            <a href="/wildkenya/logout.php" class="btn btn-danger btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="d-flex" style="min-height: calc(100vh - 56px);">

    <!-- ============================================================
         SIDEBAR
    ============================================================ -->
    <div class="d-none d-lg-block text-white py-4 px-3"
         style="width:220px; background-color:#1a1a1a; flex-shrink:0;">

        <p class="text-white-50 small fw-bold mb-2 px-2">MANAGE</p>
        <ul class="list-unstyled mb-4">
            <li class="mb-1">
                <a href="index.php"
                   class="d-flex align-items-center gap-2 px-2 py-2 rounded text-white
                          text-decoration-none bg-success">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="mb-1">
                <a href="parks.php"
                   class="d-flex align-items-center gap-2 px-2 py-2 rounded text-white-50
                          text-decoration-none sidebar-link">
                    <i class="bi bi-map-fill"></i> Parks
                    <span class="badge bg-success ms-auto"><?= $total_parks ?></span>
                </a>
            </li>
            <li class="mb-1">
                <a href="animals.php"
                   class="d-flex align-items-center gap-2 px-2 py-2 rounded text-white-50
                          text-decoration-none sidebar-link">
                    <i class="bi bi-camera-fill"></i> Animals
                    <span class="badge bg-success ms-auto"><?= $total_animals ?></span>
                </a>
            </li>
        </ul>

        <p class="text-white-50 small fw-bold mb-2 px-2">USERS</p>
        <ul class="list-unstyled mb-4">
            <li class="mb-1">
                <a href="users.php"
                   class="d-flex align-items-center gap-2 px-2 py-2 rounded text-white-50
                          text-decoration-none sidebar-link">
                    <i class="bi bi-people-fill"></i> All Users
                    <span class="badge bg-secondary ms-auto"><?= $total_users ?></span>
                </a>
            </li>
            <li class="mb-1">
                <a href="bookings.php"
                   class="d-flex align-items-center gap-2 px-2 py-2 rounded text-white-50
                          text-decoration-none sidebar-link">
                    <i class="bi bi-calendar3"></i> Bookings
                    <?php if ($pending_bookings > 0): ?>
                    <span class="badge bg-warning text-dark ms-auto"><?= $pending_bookings ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>

        <p class="text-white-50 small fw-bold mb-2 px-2">SITE</p>
        <ul class="list-unstyled">
            <li class="mb-1">
                <a href="/wildkenya/"
                   class="d-flex align-items-center gap-2 px-2 py-2 rounded text-white-50
                          text-decoration-none sidebar-link">
                    <i class="bi bi-house-fill"></i> View Site
                </a>
            </li>
        </ul>
    </div>

    <!-- ============================================================
         MAIN CONTENT
    ============================================================ -->
    <div class="flex-grow-1 p-4" style="background:#f4f6f9;">

        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Dashboard</h3>
                <p class="text-muted small mb-0">
                    Welcome back, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?>!
                    Today is <?= date('l, d F Y') ?>
                </p>
            </div>
        </div>

        <!-- ---- Stats Cards ---- -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div style="font-size:1.8rem;">🗺️</div>
                    <h3 class="fw-bold text-success mb-0"><?= $total_parks ?></h3>
                    <small class="text-muted">Parks</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div style="font-size:1.8rem;">🦁</div>
                    <h3 class="fw-bold text-success mb-0"><?= $total_animals ?></h3>
                    <small class="text-muted">Animals</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div style="font-size:1.8rem;">👤</div>
                    <h3 class="fw-bold text-primary mb-0"><?= $total_users ?></h3>
                    <small class="text-muted">Users</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div style="font-size:1.8rem;">📅</div>
                    <h3 class="fw-bold text-warning mb-0"><?= $total_bookings ?></h3>
                    <small class="text-muted">Bookings</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div style="font-size:1.8rem;">⭐</div>
                    <h3 class="fw-bold text-info mb-0"><?= $total_reviews ?></h3>
                    <small class="text-muted">Reviews</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div style="font-size:1.8rem;">🧭</div>
                    <h3 class="fw-bold text-secondary mb-0"><?= $total_guides ?></h3>
                    <small class="text-muted">Guides</small>
                </div>
            </div>
        </div>

        <!-- ---- Quick Action Buttons ---- -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 d-flex flex-wrap gap-2">
                        <a href="parks.php?action=add" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>Add New Park
                        </a>
                        <a href="animals.php?action=add" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>Add New Animal
                        </a>
                        <a href="parks.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-map-fill me-1"></i>Manage Parks
                        </a>
                        <a href="animals.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-camera-fill me-1"></i>Manage Animals
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <!-- ---- Recent Users ---- -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-2 px-4
                                d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-people-fill text-success me-2"></i>Recent Users
                        </h6>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light small text-muted">
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($u = $recent_users->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-success text-white
                                                        d-flex align-items-center justify-content-center"
                                                 style="width:28px;height:28px;font-size:11px;">
                                                <?= strtoupper(substr($u['name'],0,1)) ?>
                                            </div>
                                            <div>
                                                <p class="mb-0 small fw-bold">
                                                    <?= htmlspecialchars($u['name']) ?>
                                                </p>
                                                <p class="mb-0 text-muted" style="font-size:11px;">
                                                    <?= htmlspecialchars($u['email']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $u['role']==='admin' ? 'danger' : ($u['role']==='guide' ? 'info' : 'secondary') ?>">
                                            <?= ucfirst($u['role']) ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('d M Y', strtotime($u['created_at'])) ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ---- Booking Status Summary ---- -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-2 px-4">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-calendar3 text-success me-2"></i>
                            Booking Overview
                        </h6>
                    </div>
                    <div class="card-body px-4 pb-4">

                        <!-- Status Bars -->
                        <?php
                        $statuses = [
                            'pending'   => ['warning', 'Pending'],
                            'confirmed' => ['success', 'Confirmed'],
                            'completed' => ['primary', 'Completed'],
                            'cancelled' => ['danger',  'Cancelled'],
                        ];
                        foreach ($statuses as $s => $meta):
                            $count = $conn->query(
                                "SELECT COUNT(*) as c FROM bookings WHERE status='$s'"
                            )->fetch_assoc()['c'];
                            $pct = $total_bookings > 0
                                   ? round(($count / $total_bookings) * 100) : 0;
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-bold"><?= $meta[1] ?></span>
                                <span class="text-muted"><?= $count ?> bookings</span>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar bg-<?= $meta[0] ?>"
                                     style="width:<?= $pct ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if ($total_bookings === 0): ?>
                        <p class="text-muted small text-center py-3">
                            No bookings yet
                        </p>
                        <?php endif; ?>

                        <div class="mt-3 p-3 bg-light rounded-3 text-center">
                            <p class="mb-0 small text-muted">Total Bookings</p>
                            <h3 class="fw-bold text-success mb-0"><?= $total_bookings ?></h3>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.sidebar-link:hover {
    background-color: rgba(255,255,255,0.08);
    color: white !important;
}
</style>

</body>
</html>
