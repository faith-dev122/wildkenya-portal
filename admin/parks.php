<?php
// ============================================================
// WildKenya — Admin Parks Manager (admin/parks.php)
// Full CRUD: Create, Read, Update, Delete parks
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';

// ---- Protect: Admin only ----
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /wildkenya/pages/login.php");
    exit();
}

$success = '';
$error   = '';
$action  = $_GET['action'] ?? 'list';
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ============================================================
// HANDLE: DELETE park
// ============================================================
if ($action === 'delete' && $edit_id > 0) {
    $del = $conn->prepare("DELETE FROM parks WHERE id = ?");
    $del->bind_param("i", $edit_id);
    if ($del->execute()) {
        $success = 'Park deleted successfully.';
    } else {
        $error = 'Could not delete park.';
    }
    $action = 'list';
}

// ============================================================
// HANDLE: ADD or EDIT park form submission
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_park'])) {

    $name           = trim($_POST['name'] ?? '');
    $county         = trim($_POST['county'] ?? '');
    $region         = trim($_POST['region'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $fee_citizen    = (float)($_POST['entry_fee_citizen'] ?? 0);
    $fee_resident   = (float)($_POST['entry_fee_resident'] ?? 0);
    $fee_nonresident= (float)($_POST['entry_fee_nonresident'] ?? 0);
    $best_season    = trim($_POST['best_season'] ?? '');
    $size_km2       = (float)($_POST['size_km2'] ?? 0);
    $featured       = isset($_POST['featured']) ? 1 : 0;
    $post_id        = (int)($_POST['park_id'] ?? 0);

    if (empty($name) || empty($county) || empty($region) || empty($description)) {
        $error  = 'Please fill in all required fields.';
        $action = $post_id > 0 ? 'edit' : 'add';
    } else {
        if ($post_id > 0) {
            // UPDATE existing park
            $stmt = $conn->prepare("
                UPDATE parks SET
                    name=?, county=?, region=?, description=?,
                    entry_fee_citizen=?, entry_fee_resident=?, entry_fee_nonresident=?,
                    best_season=?, size_km2=?, featured=?
                WHERE id=?
            ");
            $stmt->bind_param(
                "ssssdddsdii",
                $name, $county, $region, $description,
                $fee_citizen, $fee_resident, $fee_nonresident,
                $best_season, $size_km2, $featured, $post_id
            );
            if ($stmt->execute()) {
                $success = "Park \"$name\" updated successfully!";
            } else {
                $error = 'Update failed. Please try again.';
            }
        } else {
            // INSERT new park
            $stmt = $conn->prepare("
                INSERT INTO parks
                    (name, county, region, description,
                     entry_fee_citizen, entry_fee_resident, entry_fee_nonresident,
                     best_season, size_km2, featured)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssssdddsdi",
                $name, $county, $region, $description,
                $fee_citizen, $fee_resident, $fee_nonresident,
                $best_season, $size_km2, $featured
            );
            if ($stmt->execute()) {
                $success = "Park \"$name\" added successfully!";
            } else {
                $error = 'Could not add park. Please try again.';
            }
        }
        $action = 'list';
    }
}

// ---- Fetch park for editing ----
$edit_park = null;
if ($action === 'edit' && $edit_id > 0) {
    $ep = $conn->prepare("SELECT * FROM parks WHERE id = ?");
    $ep->bind_param("i", $edit_id);
    $ep->execute();
    $edit_park = $ep->get_result()->fetch_assoc();
    if (!$edit_park) $action = 'list';
}

// ---- Fetch all parks for list view ----
$parks = $conn->query("SELECT * FROM parks ORDER BY featured DESC, name ASC");
?>

<!-- ADMIN NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color:#111;">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="/wildkenya/admin/">🛡️ WildKenya Admin</a>
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

<div class="d-flex" style="min-height: calc(100vh - 56px);">

    <!-- SIDEBAR -->
    <div class="d-none d-lg-block text-white py-4 px-3"
         style="width:220px; background-color:#1a1a1a; flex-shrink:0;">
        <p class="text-white-50 small fw-bold mb-2 px-2">MANAGE</p>
        <ul class="list-unstyled mb-4">
            <li class="mb-1">
                <a href="index.php"
                   class="d-flex align-items-center gap-2 px-2 py-2 rounded
                          text-white-50 text-decoration-none sidebar-link">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="mb-1">
                <a href="parks.php"
                   class="d-flex align-items-center gap-2 px-2 py-2 rounded
                          text-white text-decoration-none bg-success">
                    <i class="bi bi-map-fill"></i> Parks
                </a>
            </li>
            <li class="mb-1">
                <a href="animals.php"
                   class="d-flex align-items-center gap-2 px-2 py-2 rounded
                          text-white-50 text-decoration-none sidebar-link">
                    <i class="bi bi-camera-fill"></i> Animals
                </a>
            </li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1 p-4" style="background:#f4f6f9;">

        <!-- Alerts -->
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible alert-auto fade show">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
        <!-- ============================================================
             LIST VIEW
        ============================================================ -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Manage Parks</h3>
                <p class="text-muted small mb-0">
                    <?= $parks->num_rows ?> parks in the database
                </p>
            </div>
            <a href="parks.php?action=add" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i>Add New Park
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark small">
                        <tr>
                            <th>#</th>
                            <th>Park Name</th>
                            <th>County</th>
                            <th>Region</th>
                            <th>Fee (Citizen)</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($park = $parks->fetch_assoc()): ?>
                    <tr>
                        <td class="text-muted small"><?= $park['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($park['name']) ?></strong>
                            <br>
                            <small class="text-muted">
                                <?= number_format($park['size_km2'], 0) ?> km²
                            </small>
                        </td>
                        <td class="small"><?= htmlspecialchars($park['county']) ?></td>
                        <td>
                            <span class="badge bg-success">
                                <?= htmlspecialchars($park['region']) ?>
                            </span>
                        </td>
                        <td class="small">KES <?= number_format($park['entry_fee_citizen'], 0) ?></td>
                        <td>
                            <?php if ($park['featured']): ?>
                            <span class="badge bg-warning text-dark">⭐ Yes</span>
                            <?php else: ?>
                            <span class="badge bg-light text-muted">No</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/wildkenya/pages/park-detail.php?id=<?= $park['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary"
                                   target="_blank" title="View">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="parks.php?action=edit&id=<?= $park['id'] ?>"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="parks.php?action=delete&id=<?= $park['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   title="Delete"
                                   onclick="return confirm('Delete <?= addslashes($park['name']) ?>? This cannot be undone.')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php else: ?>
        <!-- ============================================================
             ADD / EDIT FORM
        ============================================================ -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="parks.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
            <div>
                <h3 class="fw-bold mb-0">
                    <?= $action === 'edit' ? 'Edit Park' : 'Add New Park' ?>
                </h3>
                <p class="text-muted small mb-0">
                    <?= $action === 'edit'
                        ? 'Update the details for ' . htmlspecialchars($edit_park['name'])
                        : 'Fill in the details to add a new Kenya national park' ?>
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="parks.php">
                    <!-- Hidden ID for edit -->
                    <input type="hidden" name="park_id"
                           value="<?= $edit_park['id'] ?? 0 ?>">

                    <div class="row g-3">

                        <!-- Park Name -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">
                                Park Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="e.g. Maasai Mara National Reserve"
                                   value="<?= htmlspecialchars($edit_park['name'] ?? $_POST['name'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- Featured -->
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox"
                                       name="featured" id="featured"
                                       <?= ($edit_park['featured'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="featured">
                                    ⭐ Featured on Homepage
                                </label>
                            </div>
                        </div>

                        <!-- County -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">
                                County <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="county" class="form-control"
                                   placeholder="e.g. Narok"
                                   value="<?= htmlspecialchars($edit_park['county'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- Region -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">
                                Region <span class="text-danger">*</span>
                            </label>
                            <select name="region" class="form-select" required>
                                <option value="">Select Region</option>
                                <?php
                                $regions = ['Rift Valley','Coast','Central','Northern Kenya','Nairobi'];
                                foreach ($regions as $r):
                                    $sel = (($edit_park['region'] ?? '') === $r) ? 'selected' : '';
                                ?>
                                <option value="<?= $r ?>" <?= $sel ?>><?= $r ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-bold small">
                                Description <span class="text-danger">*</span>
                            </label>
                            <textarea name="description" class="form-control" rows="5"
                                      placeholder="Full description of the park..."
                                      required><?= htmlspecialchars($edit_park['description'] ?? '') ?></textarea>
                        </div>

                        <!-- Entry Fees -->
                        <div class="col-12">
                            <label class="form-label fw-bold small">Entry Fees (KES)</label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Citizen</label>
                            <input type="number" name="entry_fee_citizen" class="form-control"
                                   value="<?= $edit_park['entry_fee_citizen'] ?? 0 ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Resident</label>
                            <input type="number" name="entry_fee_resident" class="form-control"
                                   value="<?= $edit_park['entry_fee_resident'] ?? 0 ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Non-Resident</label>
                            <input type="number" name="entry_fee_nonresident" class="form-control"
                                   value="<?= $edit_park['entry_fee_nonresident'] ?? 0 ?>">
                        </div>

                        <!-- Best Season & Size -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Best Season to Visit</label>
                            <input type="text" name="best_season" class="form-control"
                                   placeholder="e.g. July to October, January to February"
                                   value="<?= htmlspecialchars($edit_park['best_season'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Size (km²)</label>
                            <input type="number" name="size_km2" class="form-control"
                                   step="0.01"
                                   value="<?= $edit_park['size_km2'] ?? 0 ?>">
                        </div>

                        <!-- Submit -->
                        <div class="col-12 d-flex gap-2 pt-2">
                            <button type="submit" name="save_park"
                                    class="btn btn-success px-4">
                                <i class="bi bi-save-fill me-2"></i>
                                <?= $action === 'edit' ? 'Save Changes' : 'Add Park' ?>
                            </button>
                            <a href="parks.php" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <?php endif; ?>
    </div>
</div>

<style>
.sidebar-link:hover { background-color: rgba(255,255,255,0.08); color:white !important; }
</style>

</body>
</html>
