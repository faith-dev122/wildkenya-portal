<?php
// ============================================================
// WildKenya — Admin Animals Manager (admin/animals.php)
// Full CRUD: Create, Read, Update, Delete animals
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
// HANDLE: DELETE
// ============================================================
if ($action === 'delete' && $edit_id > 0) {
    $del = $conn->prepare("DELETE FROM animals WHERE id = ?");
    $del->bind_param("i", $edit_id);
    if ($del->execute()) {
        $success = 'Animal deleted successfully.';
    } else {
        $error = 'Could not delete animal.';
    }
    $action = 'list';
}

// ============================================================
// HANDLE: SAVE (Add or Edit)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_animal'])) {

    $name        = trim($_POST['name'] ?? '');
    $sci_name    = trim($_POST['scientific_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status      = trim($_POST['conservation_status'] ?? '');
    $habitat     = trim($_POST['habitat'] ?? '');
    $diet        = trim($_POST['diet'] ?? '');
    $featured    = isset($_POST['featured']) ? 1 : 0;
    $post_id     = (int)($_POST['animal_id'] ?? 0);

    $valid_statuses = [
        'Least Concern','Near Threatened','Vulnerable',
        'Endangered','Critically Endangered'
    ];

    if (empty($name) || empty($description) || empty($status)) {
        $error  = 'Please fill in all required fields.';
        $action = $post_id > 0 ? 'edit' : 'add';

    } elseif (!in_array($status, $valid_statuses)) {
        $error  = 'Please select a valid conservation status.';
        $action = $post_id > 0 ? 'edit' : 'add';

    } else {
        if ($post_id > 0) {
            // UPDATE
            $stmt = $conn->prepare("
                UPDATE animals SET
                    name=?, scientific_name=?, description=?,
                    conservation_status=?, habitat=?, diet=?, featured=?
                WHERE id=?
            ");
            $stmt->bind_param(
                "ssssssi i",
                $name, $sci_name, $description,
                $status, $habitat, $diet, $featured, $post_id
            );
            // fix spacing in bind_param
            $stmt = $conn->prepare("
                UPDATE animals SET
                    name=?, scientific_name=?, description=?,
                    conservation_status=?, habitat=?, diet=?, featured=?
                WHERE id=?
            ");
            $stmt->bind_param("ssssssii",
                $name, $sci_name, $description,
                $status, $habitat, $diet, $featured, $post_id
            );
            if ($stmt->execute()) {
                $success = "Animal \"$name\" updated successfully!";
            } else {
                $error = 'Update failed. Please try again.';
            }
        } else {
            // INSERT
            $stmt = $conn->prepare("
                INSERT INTO animals
                    (name, scientific_name, description,
                     conservation_status, habitat, diet, featured)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssssssi",
                $name, $sci_name, $description,
                $status, $habitat, $diet, $featured
            );
            if ($stmt->execute()) {
                $success = "Animal \"$name\" added successfully!";
            } else {
                $error = 'Could not add animal. Please try again.';
            }
        }
        $action = 'list';
    }
}

// ---- Fetch animal for editing ----
$edit_animal = null;
if ($action === 'edit' && $edit_id > 0) {
    $ea = $conn->prepare("SELECT * FROM animals WHERE id = ?");
    $ea->bind_param("i", $edit_id);
    $ea->execute();
    $edit_animal = $ea->get_result()->fetch_assoc();
    if (!$edit_animal) $action = 'list';
}

// ---- Fetch all animals ----
$animals = $conn->query(
    "SELECT * FROM animals ORDER BY featured DESC, name ASC"
);

// ---- Badge colour ----
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
        <ul class="list-unstyled">
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
                          text-white-50 text-decoration-none sidebar-link">
                    <i class="bi bi-map-fill"></i> Parks
                </a>
            </li>
            <li class="mb-1">
                <a href="animals.php"
                   class="d-flex align-items-center gap-2 px-2 py-2 rounded
                          text-white text-decoration-none bg-success">
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
        <!-- LIST VIEW -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Manage Animals</h3>
                <p class="text-muted small mb-0">
                    <?= $animals->num_rows ?> species in the database
                </p>
            </div>
            <a href="animals.php?action=add" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i>Add New Animal
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark small">
                        <tr>
                            <th>#</th>
                            <th>Animal</th>
                            <th>Scientific Name</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($animal = $animals->fetch_assoc()):
                        $badge = statusBadge($animal['conservation_status']);
                    ?>
                    <tr>
                        <td class="text-muted small"><?= $animal['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($animal['name']) ?></strong>
                            <br>
                            <small class="text-muted">
                                <?= htmlspecialchars(substr($animal['habitat'] ?? '', 0, 40)) ?>
                            </small>
                        </td>
                        <td class="small fst-italic text-muted">
                            <?= htmlspecialchars($animal['scientific_name']) ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $badge ?>">
                                <?= htmlspecialchars($animal['conservation_status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($animal['featured']): ?>
                            <span class="badge bg-warning text-dark">⭐ Yes</span>
                            <?php else: ?>
                            <span class="badge bg-light text-muted">No</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/wildkenya/pages/animal-detail.php?id=<?= $animal['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary"
                                   target="_blank" title="View">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="animals.php?action=edit&id=<?= $animal['id'] ?>"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="animals.php?action=delete&id=<?= $animal['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   title="Delete"
                                   onclick="return confirm('Delete <?= addslashes($animal['name']) ?>?')">
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
        <!-- ADD / EDIT FORM -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="animals.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
            <div>
                <h3 class="fw-bold mb-0">
                    <?= $action === 'edit' ? 'Edit Animal' : 'Add New Animal' ?>
                </h3>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="animals.php">
                    <input type="hidden" name="animal_id"
                           value="<?= $edit_animal['id'] ?? 0 ?>">

                    <div class="row g-3">

                        <!-- Name -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">
                                Common Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="e.g. African Lion"
                                   value="<?= htmlspecialchars($edit_animal['name'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- Featured -->
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox"
                                       name="featured" id="featured"
                                       <?= ($edit_animal['featured'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="featured">
                                    ⭐ Wildlife Spotlight
                                </label>
                            </div>
                        </div>

                        <!-- Scientific Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Scientific Name</label>
                            <input type="text" name="scientific_name" class="form-control"
                                   placeholder="e.g. Panthera leo"
                                   value="<?= htmlspecialchars($edit_animal['scientific_name'] ?? '') ?>">
                        </div>

                        <!-- Conservation Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">
                                Conservation Status <span class="text-danger">*</span>
                            </label>
                            <select name="conservation_status" class="form-select" required>
                                <option value="">Select Status</option>
                                <?php
                                $statuses = [
                                    'Least Concern','Near Threatened','Vulnerable',
                                    'Endangered','Critically Endangered'
                                ];
                                foreach ($statuses as $s):
                                    $sel = (($edit_animal['conservation_status'] ?? '') === $s)
                                           ? 'selected' : '';
                                ?>
                                <option value="<?= $s ?>" <?= $sel ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Habitat -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Habitat</label>
                            <input type="text" name="habitat" class="form-control"
                                   placeholder="e.g. Open savannah, grassland"
                                   value="<?= htmlspecialchars($edit_animal['habitat'] ?? '') ?>">
                        </div>

                        <!-- Diet -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Diet</label>
                            <input type="text" name="diet" class="form-control"
                                   placeholder="e.g. Carnivore — wildebeest, zebra"
                                   value="<?= htmlspecialchars($edit_animal['diet'] ?? '') ?>">
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-bold small">
                                Description <span class="text-danger">*</span>
                            </label>
                            <textarea name="description" class="form-control" rows="6"
                                      placeholder="Full description of this animal..."
                                      required><?= htmlspecialchars($edit_animal['description'] ?? '') ?></textarea>
                        </div>

                        <!-- Submit -->
                        <div class="col-12 d-flex gap-2 pt-2">
                            <button type="submit" name="save_animal"
                                    class="btn btn-success px-4">
                                <i class="bi bi-save-fill me-2"></i>
                                <?= $action === 'edit' ? 'Save Changes' : 'Add Animal' ?>
                            </button>
                            <a href="animals.php" class="btn btn-outline-secondary">
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
.sidebar-link:hover { background-color:rgba(255,255,255,0.08); color:white !important; }
</style>

</body>
</html>
