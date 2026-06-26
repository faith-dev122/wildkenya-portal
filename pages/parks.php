<?php
// ============================================================
// WildKenya — Parks Page (pages/parks.php)
// Shows all Kenya national parks with search and filter
// Week 8 Update: CSS Grid and Mobile-First responsive layout
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$region = isset($_GET['region']) ? trim($_GET['region'])  : '';

$sql    = "SELECT * FROM parks WHERE 1=1";
$params = [];
$types  = '';

if ($search !== '') {
    $sql    .= " AND (name LIKE ? OR county LIKE ? OR description LIKE ?)";
    $like    = '%' . $search . '%';
    $params  = array_merge($params, [$like, $like, $like]);
    $types  .= 'sss';
}

if ($region !== '') {
    $sql    .= " AND region = ?";
    $params  = array_merge($params, [$region]);
    $types  .= 's';
}

$sql .= " ORDER BY featured DESC, name ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$total  = $result->num_rows;

$regions_result = $conn->query("SELECT DISTINCT region FROM parks ORDER BY region ASC");
?>

<!-- PAGE BANNER -->
<section class="py-5 text-white text-center"
         style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                url('https://images.unsplash.com/photo-1516426122078-c23e76319801?w=1400')
                center/cover no-repeat;">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Kenya National Parks</h1>
        <p class="lead text-white-50">
            Explore <?= $conn->query("SELECT COUNT(*) as c FROM parks")->fetch_assoc()['c'] ?>
            of Kenya's most magnificent wildlife destinations
        </p>
    </div>
</section>

<!-- SEARCH AND FILTER BAR -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <form method="GET" action="parks.php" class="row g-3 align-items-end">

            <div class="col-md-5">
                <label class="form-label fw-bold small text-muted">SEARCH PARKS</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="e.g. Maasai Mara, Tsavo, Nairobi..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">FILTER BY REGION</label>
                <select name="region" class="form-select">
                    <option value="">All Regions</option>
                    <?php while ($r = $regions_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($r['region']) ?>"
                            <?= ($region === $r['region']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['region']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                <?php if ($search || $region): ?>
                    <a href="parks.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Clear
                    </a>
                <?php endif; ?>
            </div>

        </form>
    </div>
</section>

<!-- RESULTS COUNT -->
<section class="pt-4 pb-2">
    <div class="container">
        <p class="text-muted mb-0">
            <?php if ($search || $region): ?>
                Showing <strong><?= $total ?></strong> result<?= $total !== 1 ? 's' : '' ?>
                <?= $search ? " for <strong>\"" . htmlspecialchars($search) . "\"</strong>" : '' ?>
                <?= $region ? " in <strong>" . htmlspecialchars($region) . "</strong>" : '' ?>
            <?php else: ?>
                Showing all <strong><?= $total ?></strong> parks
            <?php endif; ?>
        </p>
        <p class="text-muted small mt-1 mb-0">
            Responsive layout: 1 column on mobile, 2 columns on tablet, 3 columns on desktop
        </p>
    </div>
</section>

<!-- ============================================================
     PARKS GRID
     Week 8: Uses CSS Grid defined in style.css (.parks-grid)
     Mobile-First: 1 col (mobile) - 2 col (768px) - 3 col (1024px)
============================================================ -->
<section class="pb-5 pt-3">
    <div class="container">

        <?php if ($total === 0): ?>
        <div class="text-center py-5">
            <div style="font-size: 4rem;">🔍</div>
            <h4 class="mt-3">No parks found</h4>
            <p class="text-muted">Try a different search term or clear the filter.</p>
            <a href="parks.php" class="btn btn-success">View All Parks</a>
        </div>

        <?php else: ?>

        <!-- CSS Grid container defined in assets/css/style.css -->
        <div class="parks-grid">
            <?php while ($park = $result->fetch_assoc()): ?>

            <div class="park-card card shadow-sm border-0">

                <div style="height: 200px; overflow: hidden; background: #1a3c2e; position: relative;">
                    <img src="../assets/images/parks/park-<?= $park['id'] ?>.jpg"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
                         class="w-100 h-100 object-fit-cover"
                         alt="<?= htmlspecialchars($park['name']) ?>">
                    <div style="display:none; height:200px; align-items:center;
                                justify-content:center; font-size:3.5rem;">🌍</div>

                    <?php if ($park['featured']): ?>
                    <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                        Featured
                    </span>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-success"><?= htmlspecialchars($park['region']) ?></span>
                        <small class="text-muted">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($park['county']) ?>
                        </small>
                    </div>

                    <h5 class="card-title fw-bold mb-2">
                        <?= htmlspecialchars($park['name']) ?>
                    </h5>

                    <p class="card-text text-muted small mb-3">
                        <?= htmlspecialchars(substr($park['description'], 0, 120)) ?>...
                    </p>

                    <div class="row g-2 small text-muted mb-2">
                        <?php if ($park['size_km2']): ?>
                        <div class="col-6">
                            <i class="bi bi-arrows-fullscreen text-success me-1"></i>
                            <?= number_format($park['size_km2'], 0) ?> km²
                        </div>
                        <?php endif; ?>
                        <?php if ($park['best_season']): ?>
                        <div class="col-6">
                            <i class="bi bi-sun text-warning me-1"></i>
                            <?= htmlspecialchars(substr($park['best_season'], 0, 25)) ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <p class="small mb-0">
                        <i class="bi bi-currency-exchange text-success"></i>
                        From <strong>KES <?= number_format($park['entry_fee_citizen'], 0) ?></strong>/adult (citizen)
                    </p>
                </div>

                <div class="card-footer bg-transparent border-0 pb-3">
                    <a href="park-detail.php?id=<?= $park['id'] ?>"
                       class="btn btn-success w-100">
                        <i class="bi bi-binoculars me-1"></i>Explore Park
                    </a>
                </div>

            </div>

            <?php endwhile; ?>
        </div>

        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>