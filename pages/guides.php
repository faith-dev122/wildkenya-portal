<?php
// ============================================================
// WildKenya — Guides Page (pages/guides.php)
// Browse registered safari tour guides
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

// ---- Get search/filter from URL ----
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$park_id = isset($_GET['park_id']) ? (int)$_GET['park_id'] : 0;

// ---- Build query ----
$sql = "
    SELECT g.*, u.name, u.email, u.phone,
           p.name AS park_name
    FROM guides g
    JOIN users u ON g.user_id = u.id
    LEFT JOIN parks p ON p.id = ?
    WHERE g.available = 1
";
$params = [$park_id];
$types  = 'i';

if ($search !== '') {
    $sql   .= " AND (u.name LIKE ? OR g.specialization LIKE ? OR g.languages LIKE ?)";
    $like   = '%' . $search . '%';
    $params = array_merge($params, [$like, $like, $like]);
    $types .= 'sss';
}

$sql .= " ORDER BY g.rating DESC, g.years_experience DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$guides = $stmt->get_result();
$total  = $guides->num_rows;

// ---- Fetch parks for filter dropdown ----
$parks = $conn->query("SELECT id, name FROM parks ORDER BY name ASC");
?>

<!-- ============================================================
     PAGE BANNER
============================================================ -->
<section class="py-5 text-white text-center"
         style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                url('https://images.unsplash.com/photo-1535941339077-2dd1c7963098?w=1400')
                center/cover no-repeat;">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">🧭 Safari Guides</h1>
        <p class="lead text-white-50">
            Expert Kenyan guides with local knowledge and years of experience
        </p>
    </div>
</section>

<!-- ============================================================
     SEARCH BAR
============================================================ -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <form method="GET" action="guides.php" class="row g-3 align-items-end">

            <!-- Search -->
            <div class="col-md-5">
                <label class="form-label fw-bold small">SEARCH GUIDES</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Name, specialization or language..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>

            <!-- Park Filter -->
            <div class="col-md-4">
                <label class="form-label fw-bold small">FILTER BY PARK</label>
                <select name="park_id" class="form-select">
                    <option value="0">All Parks</option>
                    <?php while ($p = $parks->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>"
                        <?= ($park_id === (int)$p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i>Search
                </button>
                <?php if ($search || $park_id): ?>
                <a href="guides.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
                <?php endif; ?>
            </div>

        </form>
    </div>
</section>

<!-- ============================================================
     RESULTS
============================================================ -->
<section class="py-5">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="text-muted mb-0">
                <?php if ($total > 0): ?>
                    <strong><?= $total ?></strong> guide<?= $total !== 1 ? 's' : '' ?> available
                <?php else: ?>
                    No guides found
                <?php endif; ?>
            </p>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'guide'): ?>
            <a href="dashboard.php" class="btn btn-outline-success btn-sm">
                <i class="bi bi-person-badge-fill me-1"></i>My Guide Profile
            </a>
            <?php endif; ?>
        </div>

        <?php if ($total === 0): ?>
        <!-- ---- No guides yet ---- -->
        <div class="text-center py-5">
            <div style="font-size:4rem;">🧭</div>
            <h4 class="mt-3">No guides registered yet</h4>
            <p class="text-muted">
                Guides will appear here once they register and set up their profile.
            </p>
            <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-success">
                Register as a Guide
            </a>
            <?php endif; ?>
        </div>

        <?php else: ?>
        <div class="row g-4">
            <?php while ($guide = $guides->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm guide-card">
                    <div class="card-body p-4">

                        <!-- Guide Header -->
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <!-- Avatar -->
                            <div class="rounded-circle text-white fw-bold d-flex
                                        align-items-center justify-content-center flex-shrink-0"
                                 style="width:56px; height:56px; font-size:1.3rem;
                                        background-color:#2d6a4f;">
                                <?= strtoupper(substr($guide['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">
                                    <?= htmlspecialchars($guide['name']) ?>
                                </h5>
                                <!-- Star Rating -->
                                <div class="d-flex align-items-center gap-1">
                                    <?php
                                    $rating = round($guide['rating']);
                                    for ($i = 1; $i <= 5; $i++):
                                    ?>
                                    <i class="bi bi-star<?= $i <= $rating ? '-fill' : '' ?>
                                               text-warning" style="font-size:12px;"></i>
                                    <?php endfor; ?>
                                    <span class="small text-muted ms-1">
                                        <?= number_format($guide['rating'], 1) ?>
                                    </span>
                                </div>
                            </div>
                            <!-- Certified Badge -->
                            <?php if ($guide['certified']): ?>
                            <span class="badge bg-success ms-auto">
                                <i class="bi bi-patch-check-fill me-1"></i>Certified
                            </span>
                            <?php endif; ?>
                        </div>

                        <!-- Bio -->
                        <?php if ($guide['bio']): ?>
                        <p class="text-muted small mb-3">
                            <?= htmlspecialchars(substr($guide['bio'], 0, 120)) ?>...
                        </p>
                        <?php endif; ?>

                        <!-- Details -->
                        <div class="row g-2 small mb-3">
                            <?php if ($guide['languages']): ?>
                            <div class="col-12">
                                <i class="bi bi-translate text-success me-1"></i>
                                <strong>Languages:</strong>
                                <?= htmlspecialchars($guide['languages']) ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($guide['specialization']): ?>
                            <div class="col-12">
                                <i class="bi bi-binoculars-fill text-success me-1"></i>
                                <strong>Specialization:</strong>
                                <?= htmlspecialchars($guide['specialization']) ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($guide['years_experience']): ?>
                            <div class="col-12">
                                <i class="bi bi-award-fill text-warning me-1"></i>
                                <strong>Experience:</strong>
                                <?= $guide['years_experience'] ?> years
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Price & Book Button -->
                        <div class="d-flex align-items-center justify-content-between
                                    pt-3 border-top">
                            <div>
                                <p class="text-muted small mb-0">Rate</p>
                                <h5 class="fw-bold text-success mb-0">
                                    KES <?= number_format($guide['price_per_day'], 0) ?>
                                    <small class="text-muted fw-normal fs-6">/day</small>
                                </h5>
                            </div>
                            <a href="booking.php?guide_id=<?= $guide['id'] ?>"
                               class="btn btn-success">
                                <i class="bi bi-calendar-plus-fill me-1"></i>Book Guide
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- ============================================================
     BECOME A GUIDE CTA
============================================================ -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Are you a Safari Guide? 🧭</h3>
                <p class="text-muted mb-0">
                    Join WildKenya and connect with tourists from around the world.
                    Register as a guide to showcase your expertise and grow your business.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-success btn-lg px-4">
                    <i class="bi bi-person-plus-fill me-2"></i>Register as a Guide
                </a>
                <?php else: ?>
                <a href="dashboard.php" class="btn btn-success btn-lg px-4">
                    <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.guide-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    border-radius: 12px !important;
}
.guide-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12) !important;
}
</style>

<?php require_once '../includes/footer.php'; ?>
