<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #1a3c2e;">
    <div class="container">

        <!-- Brand Logo -->
        <a class="navbar-brand fw-bold fs-4" href="/wildkenya/">
            🦒 WildKenya
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav Links -->
        <div class="collapse navbar-collapse" id="navMenu">

            <!-- Left Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/wildkenya/pages/parks.php">
                        <i class="bi bi-map-fill me-1"></i>Parks
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/wildkenya/pages/animals.php">
                        <i class="bi bi-camera-fill me-1"></i>Wildlife
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/wildkenya/pages/guides.php">
                        <i class="bi bi-person-badge-fill me-1"></i>Guides
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/wildkenya/pages/trip-planner.php">
                        <i class="bi bi-calendar3 me-1"></i>Trip Planner
                    </a>
                </li>
            </ul>

            <!-- Right Links — Login / Dashboard -->
            <ul class="navbar-nav align-items-lg-center gap-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <span class="nav-link text-warning">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/wildkenya/pages/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-bold" href="/wildkenya/admin/">
                            <i class="bi bi-shield-lock-fill me-1"></i>Admin
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm" href="/wildkenya/logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/wildkenya/pages/login.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-success btn-sm px-3" href="/wildkenya/pages/register.php">
                            Register Free
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

        </div>
    </div>
</nav>
