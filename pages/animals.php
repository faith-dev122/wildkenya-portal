<?php
// ============================================================
// WildKenya — Wildlife Page (pages/animals.php)
// Shows all Kenya wildlife with search and filter
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

// ---- Get search and filter values from URL ----
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status'])  : '';

// ---- Build SQL query dynamically ----
$sql    = "SELECT * FROM animals WHERE 1=1";
$params = [];
$types  = '';

if ($search !== '') {
    $sql   .= " AND (name LIKE ? OR scientific_name LIKE ? OR habitat LIKE ?)";
    $like   = '%' . $search . '%';
    $params = array_merge($params, [$like, $like, $like]);
    $types .= 'sss';
}

if ($status !== '') {
    $sql   .= " AND conservation_status = ?";
    $params = array_merge($params, [$status]);
    $types .= 's';
}

$sql .= " ORDER BY featured DESC, name ASC";

// ---- Run query ----
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$total  = $result->num_rows;

// ---- Count animals per conservation status (for the filter buttons) ----
$status_counts = [];
$count_result  = $conn->query("SELECT conservation_status, COUNT(*) as c FROM animals GROUP BY conservation_status");
while ($row = $count_result->fetch_assoc()) {
    $status_counts[$row['conservation_status']] = $row['c'];
}

// ---- Helper: badge colour per conservation status ----
function statusBadge($s) {
    return match($s) {
        'Critically Endangered' => 'danger',
        'Endangered'            => 'warning text-dark',
        'Vulnerable'            => 'primary',
        'Near Threatened'       => 'secondary',
        default                 => 'success'
    };
}
?>

<!-- ============================================================
     PAGE BANNER
============================================================ -->
<section class="py-5 text-white text-center"
         style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                url('https://images.unsplash.com/photo-1474511320723-9a56873867b5?w=1400')
                center/cover no-repeat;">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">🦁 Kenya Wildlife</h1>
        <p class="lead text-white-50">
            Discover the remarkable animals that call Kenya home
        </p>
    </div>
</section>

<!-- ============================================================
     CONSERVATION STATUS QUICK FILTER BUTTONS
============================================================ -->
<section class="py-3 bg-light border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="small fw-bold text-muted me-2">FILTER BY STATUS:</span>

            <a href="animals.php"
               class="btn btn-sm <?= $status === '' ? 'btn-dark' : 'btn-outline-dark' ?>">
                All
                <span class="badge bg-white text-dark ms-1">
                    <?= array_sum($status_counts) ?>
                </span>
            </a>

            <?php
            $statuses = [
                'Critically Endangered' => 'danger',
                'Endangered'            => 'warning',
                'Vulnerable'            => 'primary',
                'Near Threatened'       => 'secondary',
                'Least Concern'         => 'success',
            ];
            foreach ($statuses as $s => $color):
                if (!isset($status_counts[$s])) continue;
            ?>
            <a href="animals.php?status=<?= urlencode($s) ?><?= $search ? '&search='.urlencode($search) : '' ?>"
               class="btn btn-sm <?= $status === $s ? "btn-$color" : "btn-outline-$color" ?>">
                <?= $s ?>
                <span class="badge bg-white text-dark ms-1"><?= $status_counts[$s] ?></span>
            </a>
            <?php endforeach; ?>

        </div>
    </div>
</section>

<!-- ============================================================
     SEARCH BAR
============================================================ -->
<section class="py-3 border-bottom">
    <div class="container">
        <form method="GET" action="animals.php" class="row g-2 align-items-end">

            <!-- Keep status filter when searching -->
            <?php if ($status): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
            <?php endif; ?>

            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by name, scientific name or habitat..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>

            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-search me-1"></i>Search
                </button>
                <?php if ($search || $status): ?>
                    <a href="animals.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>Clear
                    </a>
                <?php endif; ?>
            </div>

        </form>
    </div>
</section>

<!-- ============================================================
     RESULTS COUNT
============================================================ -->
<section class="pt-4 pb-2">
    <div class="container">
        <p class="text-muted mb-0">
            <?php if ($search || $status): ?>
                Showing <strong><?= $total ?></strong> animal<?= $total !== 1 ? 's' : '' ?>
                <?= $search ? " matching <strong>\"" . htmlspecialchars($search) . "\"</strong>" : '' ?>
                <?= $status ? " — status: <strong>" . htmlspecialchars($status) . "</strong>" : '' ?>
            <?php else: ?>
                Showing all <strong><?= $total ?></strong> wildlife species
            <?php endif; ?>
        </p>
    </div>
</section>

<!-- ============================================================
     ANIMALS GRID
============================================================ -->
<section class="pb-5 pt-3">
    <div class="container">

        <?php if ($total === 0): ?>
        <!-- No Results -->
        <div class="text-center py-5">
            <div style="font-size: 4rem;">🔍</div>
            <h4 class="mt-3">No animals found</h4>
            <p class="text-muted">Try a different search term or clear the filter.</p>
            <a href="animals.php" class="btn btn-success">View All Wildlife</a>
        </div>

        <?php else: ?>
        <div class="row g-4">
            <?php while ($animal = $result->fetch_assoc()):
                $badge = statusBadge($animal['conservation_status']);
            ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 animal-card">

                    <!-- Animal Image -->
                    <div style="height: 200px; overflow: hidden;
                                background: #2d6a4f; position: relative;">
                        <img src="../assets/images/animals/animal-<?= $animal['id'] ?>.jpg"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
                             class="w-100 h-100 object-fit-cover"
                             alt="<?= htmlspecialchars($animal['name']) ?>">
                        <!-- Fallback -->
                        <div style="display:none; height:200px; align-items:center;
                                    justify-content:center; font-size:3.5rem;">🐾</div>

                        <!-- Featured badge -->
                        <?php if ($animal['featured']): ?>
                        <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                            ⭐ Spotlight
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="card-body">

                        <!-- Conservation Status Badge -->
                        <span class="badge bg-<?= $badge ?> mb-2">
                            <?= htmlspecialchars($animal['conservation_status']) ?>
                        </span>

                        <!-- Name -->
                        <h5 class="card-title fw-bold mb-1">
                            <?= htmlspecialchars($animal['name']) ?>
                        </h5>

                        <!-- Scientific Name -->
                        <p class="fst-italic text-muted small mb-2">
                            <?= htmlspecialchars($animal['scientific_name']) ?>
                        </p>

                        <!-- Description Preview -->
                        <p class="card-text text-muted small mb-3">
                            <?= htmlspecialchars(substr($animal['description'], 0, 110)) ?>...
                        </p>

                        <!-- Habitat & Diet -->
                        <div class="small text-muted">
                            <?php if ($animal['habitat']): ?>
                            <p class="mb-1">
                                <i class="bi bi-tree-fill text-success me-1"></i>
                                <?= htmlspecialchars($animal['habitat']) ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($animal['diet']): ?>
                            <p class="mb-0">
                                <i class="bi bi-egg-fried text-warning me-1"></i>
                                <?= htmlspecialchars($animal['diet']) ?>
                            </p>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Button -->
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <a href="animal-detail.php?id=<?= $animal['id'] ?>"
                           class="btn btn-outline-success w-100">
                            <i class="bi bi-info-circle me-1"></i>Full Profile
                        </a>
                    </div>

                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- ============================================================
     CONSERVATION INFO BANNER
============================================================ -->
<section class="py-4 text-white text-center" style="background-color: #1a3c2e;">
    <div class="container">
        <p class="mb-1 fw-bold">🌿 Kenya Wildlife Conservation</p>
        <p class="text-white-50 small mb-0">
            Kenya is home to over 25,000 animal species. Support conservation efforts through
            <a href="https://www.kws.go.ke" target="_blank" class="text-white">Kenya Wildlife Service</a>.
        </p>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
