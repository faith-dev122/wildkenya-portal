<?php
// ============================================================
// WildKenya — Trip Planner (pages/trip-planner.php)
// Plan a multi-park safari itinerary
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';

// ---- Must be logged in ----
if (!isset($_SESSION['user_id'])) {
    header("Location: /wildkenya/pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error   = '';

// ---- Fetch all parks for selection ----
$parks = $conn->query("SELECT * FROM parks ORDER BY name ASC");
$parks_array = [];
while ($p = $parks->fetch_assoc()) {
    $parks_array[] = $p;
}

// ---- Handle Save Trip ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_trip'])) {
    $title       = trim($_POST['trip_title'] ?? '');
    $notes       = trim($_POST['trip_notes'] ?? '');
    $start_date  = trim($_POST['start_date'] ?? '');
    $selected    = $_POST['selected_parks'] ?? [];

    if (empty($title)) {
        $error = 'Please give your trip a name.';
    } elseif (empty($selected)) {
        $error = 'Please select at least one park for your itinerary.';
    } elseif (empty($start_date)) {
        $error = 'Please select a start date.';
    } else {
        // Store trip as JSON in the notes field (simple approach)
        $trip_data = json_encode([
            'parks'      => $selected,
            'start_date' => $start_date,
            'notes'      => $notes
        ]);

        // Save as a booking with status='pending' and special_requests = trip data
        // We use the first selected park as the primary park
        $first_park = (int)$selected[0];
        $ins = $conn->prepare("
            INSERT INTO bookings
                (tourist_id, park_id, booking_date, duration_days,
                 group_size, total_cost, status, special_requests)
            VALUES (?, ?, ?, ?, 1, 0, 'pending', ?)
        ");
        $duration = count($selected); // 1 day per park
        $ins->bind_param("iisis",
            $user_id, $first_park, $start_date, $duration, $trip_data
        );
        if ($ins->execute()) {
            $success = "Your trip plan \"$title\" has been saved!";
        } else {
            $error = 'Could not save trip. Please try again.';
        }
    }
}

// ---- Load user's saved trips ----
$saved_trips = $conn->prepare("
    SELECT b.*, p.name as park_name
    FROM bookings b
    JOIN parks p ON b.park_id = p.id
    WHERE b.tourist_id = ? AND b.group_size = 1 AND b.total_cost = 0
    ORDER BY b.created_at DESC LIMIT 5
");
$saved_trips->bind_param("i", $user_id);
$saved_trips->execute();
$trips = $saved_trips->get_result();
?>

<!-- ============================================================
     PAGE BANNER
============================================================ -->
<section class="py-4 text-white" style="background-color:#1a3c2e;">
    <div class="container">
        <h2 class="fw-bold mb-0">📍 Safari Trip Planner</h2>
        <p class="text-white-50 mb-0">
            Build your perfect multi-park Kenya wildlife itinerary
        </p>
    </div>
</section>

<!-- ============================================================
     PLANNER
============================================================ -->
<section class="py-5" style="background:#f4f6f9;">
    <div class="container">

        <!-- Alerts -->
        <?php if ($success): ?>
        <div class="alert alert-success alert-auto fade show mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($success) ?>
            — <a href="dashboard.php" class="alert-link">View in Dashboard →</a>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger fade show mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <div class="row g-4">

            <!-- ---- LEFT: Park Selector ---- -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-map-fill text-success me-2"></i>
                            Step 1 — Choose Your Parks
                        </h5>
                        <p class="text-muted small mb-3">
                            Click parks to add them to your itinerary
                        </p>

                        <!-- Search parks -->
                        <input type="text" id="park-search"
                               class="form-control form-control-sm mb-3"
                               placeholder="🔍 Search parks...">

                        <div id="park-list" style="max-height:400px; overflow-y:auto;">
                            <?php foreach ($parks_array as $park): ?>
                            <div class="park-item d-flex align-items-center gap-3 p-2
                                        border rounded-3 mb-2 cursor-pointer"
                                 data-id="<?= $park['id'] ?>"
                                 data-name="<?= htmlspecialchars($park['name']) ?>"
                                 data-region="<?= htmlspecialchars($park['region']) ?>"
                                 data-fee="<?= $park['entry_fee_citizen'] ?>"
                                 data-season="<?= htmlspecialchars($park['best_season'] ?? '') ?>"
                                 onclick="addPark(this)"
                                 style="cursor:pointer; transition:0.2s;">
                                <div style="width:36px; height:36px; background:#1a3c2e;
                                            border-radius:8px; display:flex; align-items:center;
                                            justify-content:center; font-size:1.1rem; flex-shrink:0;">
                                    🌍
                                </div>
                                <div class="flex-grow-1">
                                    <p class="fw-bold small mb-0">
                                        <?= htmlspecialchars($park['name']) ?>
                                    </p>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($park['region']) ?>
                                        &mdash; KES <?= number_format($park['entry_fee_citizen'], 0) ?>
                                    </small>
                                </div>
                                <i class="bi bi-plus-circle-fill text-success fs-5 add-icon"></i>
                                <i class="bi bi-check-circle-fill text-success fs-5 check-icon"
                                   style="display:none;"></i>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ---- RIGHT: Itinerary Builder ---- -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-calendar3 text-success me-2"></i>
                            Step 2 — Build Your Itinerary
                        </h5>
                        <p class="text-muted small mb-4">
                            Parks appear here as you select them
                        </p>

                        <!-- Itinerary Days -->
                        <div id="itinerary-container" class="mb-4">
                            <div id="empty-state" class="text-center py-4 text-muted">
                                <div style="font-size:3rem;">🗺️</div>
                                <p class="mt-2">Select parks from the left to build your itinerary</p>
                            </div>
                        </div>

                        <!-- Trip Summary -->
                        <div id="trip-summary"
                             class="p-3 rounded-3 mb-4"
                             style="background:#e8f5e9; border:1px solid #a5d6a7; display:none;">
                            <div class="row g-2 text-center">
                                <div class="col-4">
                                    <p class="text-muted small mb-0">Parks</p>
                                    <h5 class="fw-bold text-success mb-0" id="summary-parks">0</h5>
                                </div>
                                <div class="col-4">
                                    <p class="text-muted small mb-0">Days</p>
                                    <h5 class="fw-bold text-success mb-0" id="summary-days">0</h5>
                                </div>
                                <div class="col-4">
                                    <p class="text-muted small mb-0">Est. Cost</p>
                                    <h5 class="fw-bold text-success mb-0" id="summary-cost">KES 0</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Save Trip Form -->
                        <form method="POST" action="trip-planner.php" id="tripForm">
                            <input type="hidden" id="selected-parks-input" name="selected_parks">

                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-bold small">
                                        Trip Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="trip_title"
                                           class="form-control"
                                           placeholder="e.g. My Kenya Safari 2026"
                                           required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold small">
                                        Start Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="start_date"
                                           class="form-control"
                                           min="<?= date('Y-m-d') ?>"
                                           required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Notes</label>
                                <textarea name="trip_notes" class="form-control"
                                          rows="2"
                                          placeholder="Any notes about your trip..."></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" name="save_trip"
                                        id="save-btn"
                                        class="btn btn-success flex-fill py-2"
                                        disabled>
                                    <i class="bi bi-save-fill me-2"></i>Save Trip Plan
                                </button>
                                <button type="button" onclick="clearItinerary()"
                                        class="btn btn-outline-danger">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                            <p class="text-muted small mt-2 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Select at least one park to enable saving
                            </p>

                        </form>
                    </div>
                </div>

                <!-- Saved Trips -->
                <?php if ($trips->num_rows > 0): ?>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-bookmark-fill text-warning me-2"></i>
                            My Saved Trips
                        </h5>
                        <?php while ($trip = $trips->fetch_assoc()):
                            $data = json_decode($trip['special_requests'], true);
                        ?>
                        <div class="d-flex justify-content-between align-items-center
                                    border-bottom pb-2 mb-2">
                            <div>
                                <p class="fw-bold small mb-0">
                                    <?= htmlspecialchars($trip['park_name']) ?> & more
                                </p>
                                <small class="text-muted">
                                    <?= date('d M Y', strtotime($data['start_date'] ?? $trip['booking_date'])) ?>
                                    &mdash; <?= $trip['duration_days'] ?> day<?= $trip['duration_days'] > 1 ? 's' : '' ?>
                                </small>
                            </div>
                            <a href="booking.php?park_id=<?= $trip['park_id'] ?>"
                               class="btn btn-sm btn-outline-success">
                                Book Now
                            </a>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     JAVASCRIPT — Interactive Itinerary Builder
============================================================ -->
<script>
let selectedParks = [];

function addPark(el) {
    const id     = el.dataset.id;
    const name   = el.dataset.name;
    const region = el.dataset.region;
    const fee    = parseFloat(el.dataset.fee);
    const season = el.dataset.season;

    // Toggle off if already selected
    if (selectedParks.find(p => p.id === id)) {
        removePark(id);
        el.querySelector('.add-icon').style.display   = '';
        el.querySelector('.check-icon').style.display = 'none';
        el.style.backgroundColor = '';
        el.style.borderColor     = '';
        return;
    }

    // Add to selected
    selectedParks.push({ id, name, region, fee, season });
    el.querySelector('.add-icon').style.display   = 'none';
    el.querySelector('.check-icon').style.display = '';
    el.style.backgroundColor = '#e8f5e9';
    el.style.borderColor     = '#2d6a4f';

    renderItinerary();
}

function removePark(id) {
    selectedParks = selectedParks.filter(p => p.id !== id);

    // Reset park item styling
    const el = document.querySelector(`.park-item[data-id="${id}"]`);
    if (el) {
        el.querySelector('.add-icon').style.display   = '';
        el.querySelector('.check-icon').style.display = 'none';
        el.style.backgroundColor = '';
        el.style.borderColor     = '';
    }

    renderItinerary();
}

function renderItinerary() {
    const container   = document.getElementById('itinerary-container');
    const emptyState  = document.getElementById('empty-state');
    const summary     = document.getElementById('trip-summary');
    const saveBtn     = document.getElementById('save-btn');
    const parksInput  = document.getElementById('selected-parks-input');

    if (selectedParks.length === 0) {
        container.innerHTML = '';
        container.appendChild(emptyState);
        emptyState.style.display = 'block';
        summary.style.display    = 'none';
        saveBtn.disabled         = true;
        parksInput.value         = '';
        return;
    }

    emptyState.style.display = 'none';
    summary.style.display    = 'block';
    saveBtn.disabled         = false;

    let html      = '';
    let totalCost = 0;

    selectedParks.forEach((park, index) => {
        totalCost += park.fee;
        html += `
        <div class="d-flex gap-3 align-items-start p-3 border rounded-3 mb-2 bg-white">
            <div class="text-center" style="min-width:40px;">
                <div class="rounded-circle bg-success text-white d-flex align-items-center
                             justify-content-center fw-bold mx-auto"
                     style="width:32px; height:32px; font-size:0.85rem;">
                    ${index + 1}
                </div>
                ${index < selectedParks.length - 1
                    ? '<div style="width:2px; height:24px; background:#2d6a4f; margin:4px auto;"></div>'
                    : ''}
            </div>
            <div class="flex-grow-1">
                <p class="fw-bold mb-0">${park.name}</p>
                <small class="text-muted">
                    📍 ${park.region}
                    ${park.season ? ' &mdash; ☀️ Best: ' + park.season.substring(0, 30) : ''}
                </small>
                <br>
                <small class="text-success fw-bold">
                    KES ${park.fee.toLocaleString()}/person entry fee
                </small>
            </div>
            <button type="button" onclick="removePark('${park.id}')"
                    class="btn btn-sm btn-outline-danger">
                <i class="bi bi-x"></i>
            </button>
        </div>`;
    });

    container.innerHTML = html;

    // Update summary
    document.getElementById('summary-parks').textContent = selectedParks.length;
    document.getElementById('summary-days').textContent  = selectedParks.length;
    document.getElementById('summary-cost').textContent  =
        'KES ' + totalCost.toLocaleString();

    // Update hidden input
    parksInput.value = selectedParks.map(p => p.id).join(',');
}

// Park search filter
document.getElementById('park-search').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.park-item').forEach(item => {
        const name   = item.dataset.name.toLowerCase();
        const region = item.dataset.region.toLowerCase();
        item.style.display = (name.includes(q) || region.includes(q)) ? '' : 'none';
    });
});

// Fix: convert comma-separated string to array before form submit
document.getElementById('tripForm').addEventListener('submit', function () {
    const raw    = document.getElementById('selected-parks-input').value;
    const ids    = raw.split(',').filter(Boolean);
    const form   = this;

    // Remove old hidden inputs
    form.querySelectorAll('input[name="selected_parks[]"]').forEach(e => e.remove());

    // Add one hidden input per park id
    ids.forEach(id => {
        const inp  = document.createElement('input');
        inp.type   = 'hidden';
        inp.name   = 'selected_parks[]';
        inp.value  = id;
        form.appendChild(inp);
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
