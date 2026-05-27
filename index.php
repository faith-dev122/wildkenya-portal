<?php
// ============================================================
// WildKenya — Homepage (index.php)
// ============================================================
require_once 'config/db.php';
require_once 'includes/header.php';
require_once 'includes/nav.php';

// Fetch featured parks (marked as featured in DB)
$parks_sql   = "SELECT * FROM parks WHERE featured = 1 LIMIT 4";
$parks_result = $conn->query($parks_sql);

// Fetch featured animals
$animals_sql   = "SELECT * FROM animals WHERE featured = 1 LIMIT 4";
$animals_result = $conn->query($animals_sql);

// Count totals for stats bar
$total_parks   = $conn->query("SELECT COUNT(*) as c FROM parks")->fetch_assoc()['c'];
$total_animals = $conn->query("SELECT COUNT(*) as c FROM animals")->fetch_assoc()['c'];
$total_guides  = $conn->query("SELECT COUNT(*) as c FROM guides")->fetch_assoc()['c'];
?>

<!-- ============================================================
     HERO SECTION
============================================================ -->
<section class="hero-section text-white d-flex align-items-center"
         style="min-height: 88vh;
                background: linear-gradient(rgba(0,0,0,0.50), rgba(0,0,0,0.55)),
                url('https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=1600')
                center/cover no-repeat;">
    <div class="container text-center">
        <span class="badge bg-success fs-6 mb-3 px-3 py-2">🇰🇪 Kenya's #1 Safari Portal</span>
        <h1 class="display-3 fw-bold mb-3">Discover Kenya's Wild Heart</h1>
        <p class="lead fs-4 mb-4 mx-auto" style="max-width:650px;">
            Explore world-class national parks, encounter incredible wildlife,
            and book expert safari guides — all in one place.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="pages/parks.php" class="btn btn-success btn-lg px-4 py-2">
                <i class="bi bi-map-fill me-2"></i>Explore Parks
            </a>
            <a href="pages/animals.php" class="btn btn-outline-light btn-lg px-4 py-2">
                <i class="bi bi-camera-fill me-2"></i>View Wildlife
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     STATS BAR
============================================================ -->
<section class="py-3" style="background-color: #111;">
    <div class="container">
        <div class="row text-center">
            <div class="col-6 col-md-3 py-2">
                <h3 class="fw-bold text-success mb-0"><?= $total_parks ?></h3>
                <small class="text-white-50">National Parks</small>
            </div>
            <div class="col-6 col-md-3 py-2">
                <h3 class="fw-bold text-success mb-0"><?= $total_animals ?>+</h3>
                <small class="text-white-50">Wildlife Species</small>
            </div>
            <div class="col-6 col-md-3 py-2">
                <h3 class="fw-bold text-success mb-0">50+</h3>
                <small class="text-white-50">Expert Guides</small>
            </div>
            <div class="col-6 col-md-3 py-2">
                <h3 class="fw-bold text-success mb-0">1,000+</h3>
                <small class="text-white-50">Happy Tourists</small>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURED PARKS
============================================================ -->
<section class="py-5">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">🗺️ Featured Parks</h2>
                <p class="text-muted mb-0">Kenya's most celebrated wildlife destinations</p>
            </div>
            <a href="pages/parks.php" class="btn btn-outline-success">
                View All <?= $total_parks ?> Parks →
            </a>
        </div>

        <div class="row g-4">
            <?php while ($park = $parks_result->fetch_assoc()): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0 park-card">

                    <!-- Park Image (uses a placeholder until you add real images) -->
                    <div style="height:190px; overflow:hidden; background:#1a3c2e;">
                        <img src="assets/images/parks/park-<?= $park['id'] ?>.jpg"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
                             class="w-100 h-100 object-fit-cover"
                             alt="<?= htmlspecialchars($park['name']) ?>">
                        <!-- Fallback placeholder -->
                        <div style="display:none; height:190px; align-items:center;
                                    justify-content:center; font-size:3rem;">🌍</div>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-success"><?= htmlspecialchars($park['region']) ?></span>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($park['county']) ?></small>
                        </div>
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($park['name']) ?></h5>
                        <p class="card-text text-muted small">
                            <?= htmlspecialchars(substr($park['description'], 0, 100)) ?>...
                        </p>
                        <p class="small mb-0">
                            <i class="bi bi-currency-exchange text-success"></i>
                            From <strong>KES <?= number_format($park['entry_fee_citizen'], 0) ?></strong>/adult (citizen)
                        </p>
                    </div>

                    <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                        <a href="pages/park-detail.php?id=<?= $park['id'] ?>"
                           class="btn btn-success w-100">
                            <i class="bi bi-binoculars me-1"></i>Explore Park
                        </a>
                    </div>

                </div>
            </div>
            <?php endwhile; ?>
        </div>

    </div>
</section>

<!-- ============================================================
     WILDLIFE SPOTLIGHT
============================================================ -->
<section class="py-5 bg-light">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">🦁 Wildlife Spotlight</h2>
                <p class="text-muted mb-0">Meet the remarkable animals of Kenya</p>
            </div>
            <a href="pages/animals.php" class="btn btn-outline-success">
                View All Wildlife →
            </a>
        </div>

        <div class="row g-4">
            <?php while ($animal = $animals_result->fetch_assoc()):
                // Pick badge colour based on conservation status
                $status_badge = match($animal['conservation_status']) {
                    'Critically Endangered' => 'danger',
                    'Endangered'            => 'warning text-dark',
                    'Vulnerable'            => 'primary',
                    'Near Threatened'       => 'secondary',
                    default                 => 'success'
                };
            ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0 animal-card">

                    <div style="height:190px; overflow:hidden; background:#2d6a4f;">
                        <img src="assets/images/animals/animal-<?= $animal['id'] ?>.jpg"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
                             class="w-100 h-100 object-fit-cover"
                             alt="<?= htmlspecialchars($animal['name']) ?>">
                        <div style="display:none; height:190px; align-items:center;
                                    justify-content:center; font-size:3rem;">🐾</div>
                    </div>

                    <div class="card-body">
                        <span class="badge bg-<?= $status_badge ?> mb-2 small">
                            <?= htmlspecialchars($animal['conservation_status']) ?>
                        </span>
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($animal['name']) ?></h5>
                        <p class="card-text text-muted small fst-italic mb-1">
                            <?= htmlspecialchars($animal['scientific_name']) ?>
                        </p>
                        <p class="card-text text-muted small">
                            <?= htmlspecialchars(substr($animal['description'], 0, 90)) ?>...
                        </p>
                    </div>

                    <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                        <a href="pages/animal-detail.php?id=<?= $animal['id'] ?>"
                           class="btn btn-outline-success w-100">
                            Learn More
                        </a>
                    </div>

                </div>
            </div>
            <?php endwhile; ?>
        </div>

    </div>
</section>

<!-- ============================================================
     FIND A GUIDE CTA
============================================================ -->
<section class="py-5 text-white text-center" style="background-color: #1a3c2e;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <h2 class="fw-bold mb-3">Ready to Plan Your Safari? 🧭</h2>
                <p class="lead mb-4 text-white-50">
                    Browse certified Kenyan safari guides with local expertise,
                    select your parks, and book your dream wildlife adventure in minutes.
                </p>
                <a href="pages/guides.php" class="btn btn-light btn-lg me-3 px-4">
                    <i class="bi bi-person-badge-fill me-2"></i>Find a Guide
                </a>
                <a href="pages/register.php" class="btn btn-outline-light btn-lg px-4">
                    Sign Up Free
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
