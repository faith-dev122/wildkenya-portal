<?php
// ============================================================
// WildKenya — Park Detail Page (pages/park-detail.php)
// Shows full information about a single park
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

// ---- Get park ID from URL e.g. park-detail.php?id=1 ----
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect if no valid ID given
if ($id <= 0) {
    header("Location: parks.php");
    exit();
}

// ---- Fetch park details ----
$stmt = $conn->prepare("SELECT * FROM parks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Redirect if park not found
if ($result->num_rows === 0) {
    header("Location: parks.php");
    exit();
}
$park = $result->fetch_assoc();

// ---- Fetch animals found in this park ----
$animals_sql = "
    SELECT a.*
    FROM animals a
    JOIN park_animals pa ON a.id = pa.animal_id
    WHERE pa.park_id = ?
    ORDER BY a.name ASC
";
$animals_stmt = $conn->prepare($animals_sql);
$animals_stmt->bind_param("i", $id);
$animals_stmt->execute();
$animals = $animals_stmt->get_result();

// ---- Fetch reviews for this park ----
$reviews_sql = "
    SELECT r.*, u.name AS reviewer_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.park_id = ?
    ORDER BY r.created_at DESC
";
$reviews_stmt = $conn->prepare($reviews_sql);
$reviews_stmt->bind_param("i", $id);
$reviews_stmt->execute();
$reviews        = $reviews_stmt->get_result();
$total_reviews  = $reviews->num_rows;

// ---- Average rating ----
$avg_stmt = $conn->prepare(
    "SELECT AVG(rating) as avg_rating FROM reviews WHERE park_id = ?"
);
$avg_stmt->bind_param("i", $id);
$avg_stmt->execute();
$avg_rating = round($avg_stmt->get_result()->fetch_assoc()['avg_rating'] ?? 0, 1);

// ---- Handle review submission ----
$review_success = '';
$review_error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $review_error = 'Please login to leave a review.';
    } else {
        $rating  = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            $review_error = 'Please select a rating between 1 and 5.';
        } elseif (empty($comment)) {
            $review_error = 'Please write a comment.';
        } else {
            $rev_stmt = $conn->prepare(
                "INSERT INTO reviews (user_id, park_id, rating, comment) VALUES (?, ?, ?, ?)"
            );
            $rev_stmt->bind_param("iiis", $_SESSION['user_id'], $id, $rating, $comment);
            if ($rev_stmt->execute()) {
                $review_success = 'Your review has been submitted!';
                // Refresh page to show new review
                header("Location: park-detail.php?id=$id&reviewed=1");
                exit();
            }
        }
    }
}

if (isset($_GET['reviewed'])) {
    $review_success = 'Your review has been submitted successfully!';
}

// ---- Star rating helper ----
function stars($rating) {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $rating
            ? '<i class="bi bi-star-fill text-warning"></i>'
            : '<i class="bi bi-star text-warning"></i>';
    }
    return $html;
}
?>

<!-- ============================================================
     PARK HERO BANNER
============================================================ -->
<section class="text-white"
         style="min-height: 380px;
                background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.6)),
                url('../assets/images/parks/park-<?= $park['id'] ?>.jpg')
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
                    <a href="parks.php" class="text-white-50">Parks</a>
                </li>
                <li class="breadcrumb-item active text-white">
                    <?= htmlspecialchars($park['name']) ?>
                </li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap gap-2 mb-2">
            <span class="badge bg-success"><?= htmlspecialchars($park['region']) ?></span>
            <?php if ($park['featured']): ?>
                <span class="badge bg-warning text-dark">⭐ Featured</span>
            <?php endif; ?>
        </div>

        <h1 class="display-5 fw-bold mb-1"><?= htmlspecialchars($park['name']) ?></h1>

        <p class="text-white-50 mb-0">
            <i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($park['county']) ?> County
            <?php if ($avg_rating > 0): ?>
                &nbsp;|&nbsp;
                <?= stars($avg_rating) ?>
                <span class="ms-1"><?= $avg_rating ?>/5 (<?= $total_reviews ?> review<?= $total_reviews !== 1 ? 's' : '' ?>)</span>
            <?php endif; ?>
        </p>
    </div>
</section>

<!-- ============================================================
     MAIN CONTENT
============================================================ -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">

            <!-- ---- LEFT: Park Info ---- -->
            <div class="col-lg-8">

                <!-- About -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="bi bi-info-circle-fill text-success me-2"></i>About this Park
                        </h4>
                        <p class="text-muted lh-lg">
                            <?= nl2br(htmlspecialchars($park['description'])) ?>
                        </p>
                    </div>
                </div>

                <!-- Park Details Grid -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="bi bi-list-check text-success me-2"></i>Park Details
                        </h4>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="text-success fs-4">📍</div>
                                    <div>
                                        <p class="fw-bold mb-0 small">Location</p>
                                        <p class="text-muted small mb-0">
                                            <?= htmlspecialchars($park['county']) ?> County,
                                            <?= htmlspecialchars($park['region']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php if ($park['size_km2']): ?>
                            <div class="col-sm-6">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="text-success fs-4">📐</div>
                                    <div>
                                        <p class="fw-bold mb-0 small">Park Size</p>
                                        <p class="text-muted small mb-0">
                                            <?= number_format($park['size_km2'], 0) ?> km²
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($park['best_season']): ?>
                            <div class="col-sm-6">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="text-success fs-4">☀️</div>
                                    <div>
                                        <p class="fw-bold mb-0 small">Best Season</p>
                                        <p class="text-muted small mb-0">
                                            <?= htmlspecialchars($park['best_season']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-sm-6">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="text-success fs-4">🎟️</div>
                                    <div>
                                        <p class="fw-bold mb-0 small">Entry Fees</p>
                                        <p class="text-muted small mb-0">
                                            Citizens: KES <?= number_format($park['entry_fee_citizen'], 0) ?><br>
                                            Residents: KES <?= number_format($park['entry_fee_resident'], 0) ?><br>
                                            Non-residents: USD <?= number_format($park['entry_fee_nonresident']/100, 0) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wildlife in this Park -->
                <?php if ($animals->num_rows > 0): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="bi bi-camera-fill text-success me-2"></i>
                            Wildlife in this Park
                            <span class="badge bg-success ms-2"><?= $animals->num_rows ?></span>
                        </h4>
                        <div class="row g-3">
                            <?php while ($animal = $animals->fetch_assoc()):
                                $badge = match($animal['conservation_status']) {
                                    'Critically Endangered' => 'danger',
                                    'Endangered'            => 'warning text-dark',
                                    'Vulnerable'            => 'primary',
                                    'Near Threatened'       => 'secondary',
                                    default                 => 'success'
                                };
                            ?>
                            <div class="col-sm-6 col-md-4">
                                <a href="animal-detail.php?id=<?= $animal['id'] ?>"
                                   class="text-decoration-none">
                                    <div class="d-flex align-items-center gap-2 p-2
                                                border rounded-3 h-100
                                                animal-card-mini">
                                        <div style="width:40px; height:40px; background:#2d6a4f;
                                                    border-radius:8px; display:flex;
                                                    align-items:center; justify-content:center;
                                                    font-size:1.2rem; flex-shrink:0;">🐾</div>
                                        <div>
                                            <p class="fw-bold small mb-0 text-dark">
                                                <?= htmlspecialchars($animal['name']) ?>
                                            </p>
                                            <span class="badge bg-<?= $badge ?>"
                                                  style="font-size:10px;">
                                                <?= htmlspecialchars($animal['conservation_status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Reviews Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="bi bi-chat-dots-fill text-success me-2"></i>
                            Visitor Reviews
                            <?php if ($total_reviews > 0): ?>
                                <span class="badge bg-success ms-2"><?= $total_reviews ?></span>
                            <?php endif; ?>
                        </h4>

                        <!-- Success Message -->
                        <?php if ($review_success): ?>
                        <div class="alert alert-success alert-auto">
                            <i class="bi bi-check-circle-fill me-2"></i><?= $review_success ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($total_reviews === 0): ?>
                            <p class="text-muted">No reviews yet. Be the first to review!</p>
                        <?php else: ?>
                            <?php while ($review = $reviews->fetch_assoc()): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="rounded-circle bg-success text-white d-flex
                                                    align-items-center justify-content-center fw-bold"
                                             style="width:36px; height:36px; font-size:0.9rem;">
                                            <?= strtoupper(substr($review['reviewer_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="fw-bold mb-0 small">
                                                <?= htmlspecialchars($review['reviewer_name']) ?>
                                            </p>
                                            <small class="text-muted">
                                                <?= date('d M Y', strtotime($review['created_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div><?= stars($review['rating']) ?></div>
                                </div>
                                <p class="text-muted small mt-2 mb-0">
                                    <?= htmlspecialchars($review['comment']) ?>
                                </p>
                            </div>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <!-- Leave a Review Form -->
                        <h5 class="fw-bold mt-4 mb-3">Leave a Review</h5>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="alert alert-info small">
                                <i class="bi bi-info-circle me-1"></i>
                                Please <a href="login.php">login</a> to leave a review.
                            </div>
                        <?php else: ?>
                        <?php if ($review_error): ?>
                            <div class="alert alert-danger small"><?= $review_error ?></div>
                        <?php endif; ?>
                        <form method="POST" action="park-detail.php?id=<?= $id ?>">
                            <!-- Star Rating -->
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Your Rating</label>
                                <div class="d-flex gap-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               name="rating" id="star<?= $i ?>"
                                               value="<?= $i ?>" required>
                                        <label class="form-check-label" for="star<?= $i ?>">
                                            <?= $i ?>⭐
                                        </label>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <!-- Comment -->
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Your Comment</label>
                                <textarea name="comment" class="form-control" rows="3"
                                          placeholder="Share your experience at this park..."
                                          required></textarea>
                            </div>
                            <button type="submit" name="submit_review"
                                    class="btn btn-success">
                                <i class="bi bi-send-fill me-1"></i>Submit Review
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- ---- RIGHT: Sidebar ---- -->
            <div class="col-lg-4">

                <!-- Book Now Card -->
                <div class="card border-0 shadow-sm mb-4"
                     style="border-top: 4px solid #2d6a4f !important;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-calendar-check-fill text-success me-2"></i>
                            Plan Your Visit
                        </h5>
                        <div class="mb-3">
                            <p class="small text-muted mb-1">Entry Fee (Citizen)</p>
                            <h4 class="fw-bold text-success">
                                KES <?= number_format($park['entry_fee_citizen'], 0) ?>
                                <small class="text-muted fs-6 fw-normal">/adult</small>
                            </h4>
                        </div>
                        <?php if ($park['best_season']): ?>
                        <p class="small text-muted">
                            <i class="bi bi-sun-fill text-warning me-1"></i>
                            <strong>Best time:</strong> <?= htmlspecialchars($park['best_season']) ?>
                        </p>
                        <?php endif; ?>
                        <a href="booking.php?park_id=<?= $park['id'] ?>"
                           class="btn btn-success w-100 mb-2">
                            <i class="bi bi-calendar-plus-fill me-2"></i>Book This Park
                        </a>
                        <a href="guides.php?park_id=<?= $park['id'] ?>"
                           class="btn btn-outline-success w-100">
                            <i class="bi bi-person-badge-fill me-2"></i>Find a Guide
                        </a>
                    </div>
                </div>

                <!-- Quick Facts -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Quick Facts</h5>
                        <table class="table table-sm table-borderless small">
                            <tr>
                                <td class="text-muted">Region</td>
                                <td class="fw-bold"><?= htmlspecialchars($park['region']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">County</td>
                                <td class="fw-bold"><?= htmlspecialchars($park['county']) ?></td>
                            </tr>
                            <?php if ($park['size_km2']): ?>
                            <tr>
                                <td class="text-muted">Size</td>
                                <td class="fw-bold"><?= number_format($park['size_km2'], 0) ?> km²</td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-muted">Wildlife</td>
                                <td class="fw-bold"><?= $animals_stmt->get_result()->num_rows ?? $animals->num_rows ?> species</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Reviews</td>
                                <td class="fw-bold"><?= $total_reviews ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Other Parks -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Other Parks</h5>
                        <?php
                        $other = $conn->prepare(
                            "SELECT id, name, region FROM parks
                             WHERE id != ? ORDER BY featured DESC LIMIT 5"
                        );
                        $other->bind_param("i", $id);
                        $other->execute();
                        $others = $other->get_result();
                        while ($op = $others->fetch_assoc()):
                        ?>
                        <a href="park-detail.php?id=<?= $op['id'] ?>"
                           class="d-flex justify-content-between align-items-center
                                  text-decoration-none py-2 border-bottom">
                            <span class="small text-dark">
                                <?= htmlspecialchars($op['name']) ?>
                            </span>
                            <span class="badge bg-light text-success border">
                                <?= htmlspecialchars($op['region']) ?>
                            </span>
                        </a>
                        <?php endwhile; ?>
                        <a href="parks.php" class="btn btn-outline-success btn-sm w-100 mt-3">
                            View All Parks
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
.animal-card-mini {
    transition: background-color 0.2s;
    cursor: pointer;
}
.animal-card-mini:hover {
    background-color: #f0faf4;
    border-color: #2d6a4f !important;
}
</style>

<?php require_once '../includes/footer.php'; ?>
