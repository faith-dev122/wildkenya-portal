<?php
// ============================================================
// WildKenya — Analytics & Reporting (admin/analytics.php)
// Course Outline: Dynamic HTML generation + Database components
// Weekly Breakdown: Analytics & Reporting with Chart.js
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';

// ---- Protect: Admin only ----
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /wildkenya/pages/login.php");
    exit();
}

// ---- Fetch data for charts ----

// 1. Bookings per park (top 10)
$bookings_per_park = $conn->query("
    SELECT p.name, COUNT(b.id) as total
    FROM parks p
    LEFT JOIN bookings b ON b.park_id = p.id
    GROUP BY p.id, p.name
    ORDER BY total DESC
    LIMIT 10
");

$park_labels   = [];
$park_counts   = [];
while ($row = $bookings_per_park->fetch_assoc()) {
    $park_labels[] = $row['name'];
    $park_counts[] = (int)$row['total'];
}

// 2. Users by role
$users_by_role = $conn->query("
    SELECT role, COUNT(*) as total
    FROM users
    GROUP BY role
");
$role_labels = [];
$role_counts = [];
while ($row = $users_by_role->fetch_assoc()) {
    $role_labels[] = ucfirst($row['role']);
    $role_counts[] = (int)$row['total'];
}

// 3. Booking status breakdown
$booking_status = $conn->query("
    SELECT status, COUNT(*) as total
    FROM bookings
    GROUP BY status
");
$status_labels = [];
$status_counts = [];
while ($row = $booking_status->fetch_assoc()) {
    $status_labels[] = ucfirst($row['status']);
    $status_counts[] = (int)$row['total'];
}

// 4. Animals by conservation status
$conservation_data = $conn->query("
    SELECT conservation_status, COUNT(*) as total
    FROM animals
    GROUP BY conservation_status
    ORDER BY total DESC
");
$cons_labels = [];
$cons_counts = [];
while ($row = $conservation_data->fetch_assoc()) {
    $cons_labels[] = $row['conservation_status'];
    $cons_counts[] = (int)$row['total'];
}

// 5. Summary stats
$total_parks      = $conn->query("SELECT COUNT(*) as c FROM parks")->fetch_assoc()['c'];
$total_animals    = $conn->query("SELECT COUNT(*) as c FROM animals")->fetch_assoc()['c'];
$total_users      = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_bookings   = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
$total_reviews    = $conn->query("SELECT COUNT(*) as c FROM reviews")->fetch_assoc()['c'];
$total_revenue    = $conn->query("SELECT SUM(total_cost) as s FROM bookings")->fetch_assoc()['s'] ?? 0;
?>

<!-- ADMIN NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color:#111;">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="/wildkenya/admin/">
            🛡️ WildKenya Admin
        </a>
        <div class="d-flex gap-2">
            <a href="/wildkenya/" class="btn btn-outline-light btn-sm">
                <i class="bi bi-globe2 me-1"></i>View Site
            </a>
            <a href="/wildkenya/logout.php" class="btn btn-danger btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="d-flex" style="min-height:calc(100vh - 56px);">

    <!-- SIDEBAR -->
    <div class="d-none d-lg-block text-white py-4 px-3"
         style="width:220px; background:#1a1a1a; flex-shrink:0;">
        <p class="text-white-50 small fw-bold mb-2 px-2">MANAGE</p>
        <ul class="list-unstyled mb-4">
            <li class="mb-1">
                <a href="index.php" class="d-flex align-items-center gap-2 px-2 py-2 rounded text-white-50 text-decoration-none sidebar-link">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="mb-1">
                <a href="parks.php" class="d-flex align-items-center gap-2 px-2 py-2 rounded text-white-50 text-decoration-none sidebar-link">
                    <i class="bi bi-map-fill"></i> Parks
                </a>
            </li>
            <li class="mb-1">
                <a href="animals.php" class="d-flex align-items-center gap-2 px-2 py-2 rounded text-white-50 text-decoration-none sidebar-link">
                    <i class="bi bi-camera-fill"></i> Animals
                </a>
            </li>
            <li class="mb-1">
                <a href="analytics.php" class="d-flex align-items-center gap-2 px-2 py-2 rounded text-white text-decoration-none bg-success">
                    <i class="bi bi-bar-chart-fill"></i> Analytics
                </a>
            </li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1 p-4" style="background:#f4f6f9;">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Analytics & Reporting</h3>
                <p class="text-muted small mb-0">
                    Live data visualisations powered by Chart.js
                </p>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div style="font-size:1.6rem;">🗺️</div>
                    <h3 class="fw-bold text-success mb-0"><?= $total_parks ?></h3>
                    <small class="text-muted">Parks</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div style="font-size:1.6rem;">🦁</div>
                    <h3 class="fw-bold text-success mb-0"><?= $total_animals ?></h3>
                    <small class="text-muted">Animals</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div style="font-size:1.6rem;">👤</div>
                    <h3 class="fw-bold text-primary mb-0"><?= $total_users ?></h3>
                    <small class="text-muted">Users</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div style="font-size:1.6rem;">📅</div>
                    <h3 class="fw-bold text-warning mb-0"><?= $total_bookings ?></h3>
                    <small class="text-muted">Bookings</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div style="font-size:1.6rem;">⭐</div>
                    <h3 class="fw-bold text-info mb-0"><?= $total_reviews ?></h3>
                    <small class="text-muted">Reviews</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div style="font-size:1.6rem;">💰</div>
                    <h3 class="fw-bold text-success mb-0" style="font-size:1rem;">
                        KES <?= number_format($total_revenue, 0) ?>
                    </h3>
                    <small class="text-muted">Revenue</small>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row g-4 mb-4">

            <!-- Bookings per Park Bar Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-bar-chart-fill text-success me-2"></i>
                            Bookings per Park
                        </h5>
                        <p class="text-muted small mb-3">
                            Total number of safari bookings for each national park
                        </p>
                        <canvas id="bookingsChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Users by Role Pie Chart -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-pie-chart-fill text-success me-2"></i>
                            Users by Role
                        </h5>
                        <p class="text-muted small mb-3">
                            Distribution of registered user account types
                        </p>
                        <canvas id="usersChart" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row g-4">

            <!-- Booking Status Doughnut -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-circle-half text-success me-2"></i>
                            Booking Status
                        </h5>
                        <p class="text-muted small mb-3">
                            Breakdown of all bookings by current status
                        </p>
                        <canvas id="statusChart" height="220"></canvas>
                    </div>
                </div>
            </div>

            <!-- Conservation Status Bar Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-shield-fill text-success me-2"></i>
                            Wildlife Conservation Status
                        </h5>
                        <p class="text-muted small mb-3">
                            Number of species per IUCN conservation status category
                        </p>
                        <canvas id="conservationChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ============================================================
// Chart 1 — Bookings per Park (Horizontal Bar)
// ============================================================
const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
new Chart(bookingsCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($park_labels) ?>,
        datasets: [{
            label: 'Total Bookings',
            data:  <?= json_encode($park_counts) ?>,
            backgroundColor: 'rgba(45, 106, 79, 0.7)',
            borderColor:     'rgba(45, 106, 79, 1)',
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// ============================================================
// Chart 2 — Users by Role (Pie)
// ============================================================
const usersCtx = document.getElementById('usersChart').getContext('2d');
new Chart(usersCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($role_labels) ?>,
        datasets: [{
            data: <?= json_encode($role_counts) ?>,
            backgroundColor: [
                'rgba(220, 53, 69, 0.8)',
                'rgba(45, 106, 79, 0.8)',
                'rgba(13, 110, 253, 0.8)',
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// ============================================================
// Chart 3 — Booking Status (Doughnut)
// ============================================================
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($status_labels) ?>,
        datasets: [{
            data: <?= json_encode($status_counts) ?>,
            backgroundColor: [
                'rgba(255, 193, 7, 0.8)',
                'rgba(45, 106, 79, 0.8)',
                'rgba(220, 53, 69, 0.8)',
                'rgba(13, 110, 253, 0.8)',
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// ============================================================
// Chart 4 — Conservation Status (Bar)
// ============================================================
const consCtx = document.getElementById('conservationChart').getContext('2d');
new Chart(consCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($cons_labels) ?>,
        datasets: [{
            label: 'Number of Species',
            data:  <?= json_encode($cons_counts) ?>,
            backgroundColor: [
                'rgba(220, 53, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(13, 110, 253, 0.8)',
                'rgba(108, 117, 125, 0.8)',
                'rgba(45, 106, 79, 0.8)',
            ],
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>

<style>
.sidebar-link:hover { background-color:rgba(255,255,255,0.08); color:white !important; }
</style>

</body>
</html>
