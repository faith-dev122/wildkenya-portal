<?php
// ============================================================
// WildKenya — Animal Detail Page (pages/animal-detail.php)
// Shows full profile of a single wildlife species
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

// ---- Get animal ID from URL e.g. animal-detail.php?id=1 ----
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: animals.php");
    exit();
}

// ---- Fetch animal details ----
$stmt = $conn->prepare("SELECT * FROM animals WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: animals.php");
    exit();
}
$animal = $result->fetch_assoc();

// ---- Fetch parks where this animal is found ----
$parks_sql = "
    SELECT p.*
    FROM parks p
    JOIN park_animals pa ON p.id = pa.park_id
    WHERE pa.animal_id = ?
    ORDER BY p.featured DESC, p.name ASC
";
$parks_stmt = $conn->prepare($parks_sql);
$parks_stmt->bind_param("i", $id);
$parks_stmt->execute();
$parks       = $parks_stmt->get_result();
$total_parks = $parks->num_rows;

// ---- Fetch other animals (sidebar) ----
$others_stmt = $conn->prepare(
    "SELECT id, name, conservation_status FROM animals
     WHERE id != ? ORDER BY featured DESC LIMIT 8"
);
$others_stmt->bind_param("i", $id);
$others_stmt->execute();
$other_animals = $others_stmt->get_result();

// ---- Badge colour helper ----
function statusBadge($s) {
    return match($s) {
        'Critically Endangered' => 'danger',
        'Endangered'            => 'warning text-dark',
        'Vulnerable'            => 'primary',
        'Near Threatened'       => 'secondary',
        default                 => 'success'
    };
}

// ---- Status icon ----
function statusIcon($s) {
    return match($s) {
        'Critically Endangered' => '🔴',
        'Endangered'            => '🟠',
        'Vulnerable'            => '🔵',
        'Near Threatened'       => '⚫',
        default                 => '🟢'
    };
}

$badge = statusBadge($animal['conservation_status']);
?>

<!-- ============================================================
     ANIMAL HERO BANNER
============================================================ -->
<section class="text-white"
         style="min-height: 360px;
                background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.65)),
                url('../assets/images/animals/animal-<?= $animal['id'] ?>.jpg')
                center/cover no-repeat,
                linear-gradient(135deg, #1a3c2e, #2d6a4f);
                display:flex; align-items:flex-end;">
    <div class="container pb-4 pt-5">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="/wildkenya/" class="text-white-50">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="animals.php" class="text-white-50">Wildlife</a>
                </li>
                <li class="breadcrumb-item active text-white">
                    <?= htmlspecialchars($animal['name']) ?>
                </li>
            </ol>
        </nav>

        <!-- Status Badge -->
        <span class="badge bg-<?= $badge ?> mb-2 fs-6 px-3 py-2">
            <?= statusIcon($animal['conservation_status']) ?>
            <?= htmlspecialchars($animal['conservation_status']) ?>
        </span>

        <!-- Name -->
        <h1 class="display-5 fw-bold mb-1">
            <?= htmlspecialchars($animal['name']) ?>
        </h1>

        <!-- Scientific Name -->
        <p class="text-white-50 fst-italic mb-0 fs-5">
            <?= htmlspecialchars($animal['scientific_name']) ?>
        </p>

    </div>
</section>

<!-- ============================================================
     MAIN CONTENT
============================================================ -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">

            <!-- ---- LEFT: Animal Profile ---- -->
            <div class="col-lg-8">

                <!-- About -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="bi bi-info-circle-fill text-success me-2"></i>
                            About the <?= htmlspecialchars($animal['name']) ?>
                        </h4>
                        <p class="text-muted lh-lg">
                            <?= nl2br(htmlspecialchars($animal['description'])) ?>
                        </p>
                    </div>
                </div>

                <!-- Key Facts -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="bi bi-clipboard-data-fill text-success me-2"></i>
                            Key Facts
                        </h4>
                        <div class="row g-3">

                            <!-- Scientific Name -->
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="text-muted small mb-1">Scientific Name</p>
                                    <p class="fw-bold fst-italic mb-0">
                                        <?= htmlspecialchars($animal['scientific_name']) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Conservation Status -->
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="text-muted small mb-1">Conservation Status</p>
                                    <span class="badge bg-<?= $badge ?> fs-6">
                                        <?= statusIcon($animal['conservation_status']) ?>
                                        <?= htmlspecialchars($animal['conservation_status']) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Habitat -->
                            <?php if ($animal['habitat']): ?>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-tree-fill text-success me-1"></i>Habitat
                                    </p>
                                    <p class="fw-bold mb-0 small">
                                        <?= htmlspecialchars($animal['habitat']) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Diet -->
                            <?php if ($animal['diet']): ?>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-egg-fried text-warning me-1"></i>Diet
                                    </p>
                                    <p class="fw-bold mb-0 small">
                                        <?= htmlspecialchars($animal['diet']) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <!-- Conservation Status Explained -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="bi bi-shield-check-fill text-success me-2"></i>
                            Conservation Status Explained
                        </h4>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?php
                            $all_statuses = [
                                'Least Concern'         => ['success', '🟢'],
                                'Near Threatened'       => ['secondary', '⚫'],
                                'Vulnerable'            => ['primary', '🔵'],
                                'Endangered'            => ['warning', '🟠'],
                                'Critically Endangered' => ['danger', '🔴'],
                            ];
                            foreach ($all_statuses as $s => $meta):
                                $active = ($s === $animal['conservation_status'])
                                          ? 'border border-3' : 'opacity-50';
                            ?>
                            <span class="badge bg-<?= $meta[0] ?> <?= $active ?> p-2">
                                <?= $meta[1] ?> <?= $s ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-muted small mb-0">
                            The IUCN Red List of Threatened Species is the world's most
                            comprehensive inventory of the conservation status of biological species.
                            The <?= htmlspecialchars($animal['name']) ?> is currently classified as
                            <strong><?= htmlspecialchars($animal['conservation_status']) ?></strong>.
                        </p>
                    </div>
                </div>

                <!-- Parks Where Found -->
                <?php if ($total_parks > 0): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="bi bi-map-fill text-success me-2"></i>
                            Where to See Them
                            <span class="badge bg-success ms-2"><?= $total_parks ?> park<?= $total_parks > 1 ? 's' : '' ?></span>
                        </h4>
                        <div class="row g-3">
                            <?php while ($park = $parks->fetch_assoc()): ?>
                            <div class="col-sm-6">
                                <a href="park-detail.php?id=<?= $park['id'] ?>"
                                   class="text-decoration-none">
                                    <div class="d-flex align-items-center gap-3 p-3
                                                border rounded-3 park-link-card h-100">
                                        <div style="width:44px; height:44px; background:#1a3c2e;
                                                    border-radius:10px; display:flex;
                                                    align-items:center; justify-content:center;
                                                    font-size:1.3rem; flex-shrink:0;">🌍</div>
                                        <div>
                                            <p class="fw-bold small mb-0 text-dark">
                                                <?= htmlspecialchars($park['name']) ?>
                                            </p>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt"></i>
                                                <?= htmlspecialchars($park['county']) ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- ---- RIGHT: Sidebar ---- -->
            <div class="col-lg-4">

                <!-- Plan a Safari CTA -->
                <div class="card border-0 shadow-sm mb-4 text-center"
                     style="border-top: 4px solid #2d6a4f !important;">
                    <div class="card-body p-4">
                        <div style="font-size:3rem;">🧭</div>
                        <h5 class="fw-bold mt-2 mb-2">
                            Want to see the <?= htmlspecialchars(explode(' ', $animal['name'])[0]) ?>?
                        </h5>
                        <p class="text-muted small mb-3">
                            Book a guided safari to one of the
                            <?= $total_parks ?> park<?= $total_parks > 1 ? 's' : '' ?>
                            where this animal lives.
                        </p>
                        <a href="parks.php" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-map-fill me-2"></i>Browse Parks
                        </a>
                        <a href="guides.php" class="btn btn-outline-success w-100">
                            <i class="bi bi-person-badge-fill me-2"></i>Find a Guide
                        </a>
                    </div>
                </div>

                <!-- Other Animals -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Other Wildlife</h5>
                        <?php while ($oa = $other_animals->fetch_assoc()):
                            $ob = statusBadge($oa['conservation_status']);
                        ?>
                        <a href="animal-detail.php?id=<?= $oa['id'] ?>"
                           class="d-flex justify-content-between align-items-center
                                  text-decoration-none py-2 border-bottom">
                            <span class="small text-dark fw-bold">
                                <?= htmlspecialchars($oa['name']) ?>
                            </span>
                            <span class="badge bg-<?= $ob ?>" style="font-size:10px;">
                                <?= htmlspecialchars($oa['conservation_status']) ?>
                            </span>
                        </a>
                        <?php endwhile; ?>
                        <a href="animals.php"
                           class="btn btn-outline-success btn-sm w-100 mt-3">
                            View All Wildlife
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
.park-link-card {
    transition: background-color 0.2s, border-color 0.2s;
}
.park-link-card:hover {
    background-color: #f0faf4;
    border-color: #2d6a4f !important;
}
</style>

<?php require_once '../includes/footer.php'; ?>
