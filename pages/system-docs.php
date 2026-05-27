<?php
// ============================================================
// WildKenya — System Documentation (pages/system-docs.php)
// Course Outline: UML and WAC modelling for web applications
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';
?>

<section class="py-4 text-white" style="background-color:#1a3c2e;">
    <div class="container">
        <h2 class="fw-bold mb-0">📐 System Documentation & UML</h2>
        <p class="text-white-50 mb-0">
            Architecture, UML diagrams, and system design documentation — BIT3208
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">

        <!-- 3-Tier Architecture -->
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-2">
                    <i class="bi bi-layers-fill text-success me-2"></i>
                    System Architecture — 3-Tier Web Application
                </h4>
                <p class="text-muted small mb-4">
                    WildKenya follows the industry-standard 3-tier architecture as required
                    by the course outline (N-tier designs)
                </p>
                <div class="row g-3 text-center">
                    <!-- Tier 1 -->
                    <div class="col-md-4">
                        <div class="p-4 border rounded-3 h-100" style="border-color:#2d6a4f !important;">
                            <div style="font-size:2.5rem;">🖥️</div>
                            <h5 class="fw-bold mt-2">Tier 1 — Presentation</h5>
                            <span class="badge bg-success mb-2">Client Side</span>
                            <ul class="list-unstyled small text-muted text-start">
                                <li>• HTML5 — page structure</li>
                                <li>• CSS3 + Bootstrap 5 — styling</li>
                                <li>• JavaScript — interactivity</li>
                                <li>• Bootstrap Icons — UI icons</li>
                                <li>• Chart.js — data visualisation</li>
                            </ul>
                            <div class="mt-2 p-2 bg-light rounded small">
                                <strong>User interacts here</strong><br>
                                Browser renders pages served by Apache
                            </div>
                        </div>
                    </div>
                    <!-- Arrow -->
                    <div class="col-md-4">
                        <div class="p-4 border rounded-3 h-100" style="border-color:#2d6a4f !important;">
                            <div style="font-size:2.5rem;">⚙️</div>
                            <h5 class="fw-bold mt-2">Tier 2 — Application</h5>
                            <span class="badge bg-warning text-dark mb-2">Server Side</span>
                            <ul class="list-unstyled small text-muted text-start">
                                <li>• PHP 8 — business logic</li>
                                <li>• Apache — web server</li>
                                <li>• Session management</li>
                                <li>• Cookie handling</li>
                                <li>• Role-based access control</li>
                            </ul>
                            <div class="mt-2 p-2 bg-light rounded small">
                                <strong>Logic lives here</strong><br>
                                PHP processes requests and returns responses
                            </div>
                        </div>
                    </div>
                    <!-- Tier 3 -->
                    <div class="col-md-4">
                        <div class="p-4 border rounded-3 h-100" style="border-color:#2d6a4f !important;">
                            <div style="font-size:2.5rem;">🗄️</div>
                            <h5 class="fw-bold mt-2">Tier 3 — Data</h5>
                            <span class="badge bg-primary mb-2">Database</span>
                            <ul class="list-unstyled small text-muted text-start">
                                <li>• MySQL — relational database</li>
                                <li>• phpMyAdmin — management</li>
                                <li>• 7 normalised tables</li>
                                <li>• Foreign key constraints</li>
                                <li>• Prepared statements</li>
                            </ul>
                            <div class="mt-2 p-2 bg-light rounded small">
                                <strong>Data lives here</strong><br>
                                MySQL stores and retrieves all records
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Flow arrow -->
                <div class="text-center mt-4 p-3 bg-light rounded-3">
                    <code class="fs-6">
                        User (Browser) → HTTP Request → Apache → PHP → MySQL → PHP → HTTP Response → Browser
                    </code>
                </div>
            </div>
        </div>

        <!-- Use Case Diagram Text Representation -->
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-2">
                    <i class="bi bi-diagram-3-fill text-success me-2"></i>
                    Use Case Diagram — System Actors & Functions
                </h4>
                <p class="text-muted small mb-4">
                    UML Use Case Diagram showing all actors and their interactions with the system
                </p>

                <div class="row g-4">
                    <!-- Guest Actor -->
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 h-100">
                            <div class="text-center mb-3">
                                <div style="font-size:2.5rem;">🌍</div>
                                <h5 class="fw-bold">Guest</h5>
                                <span class="badge bg-secondary">Unauthenticated</span>
                            </div>
                            <ul class="list-unstyled small">
                                <li class="mb-1">📌 Browse parks</li>
                                <li class="mb-1">📌 Search parks</li>
                                <li class="mb-1">📌 View park detail</li>
                                <li class="mb-1">📌 Browse wildlife</li>
                                <li class="mb-1">📌 View animal profile</li>
                                <li class="mb-1">📌 Browse guides</li>
                                <li class="mb-1">📌 Register account</li>
                                <li class="mb-1">📌 Login</li>
                            </ul>
                        </div>
                    </div>
                    <!-- Tourist Actor -->
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 h-100" style="border-color:#2d6a4f !important;">
                            <div class="text-center mb-3">
                                <div style="font-size:2.5rem;">🧳</div>
                                <h5 class="fw-bold">Tourist</h5>
                                <span class="badge bg-success">Authenticated</span>
                            </div>
                            <ul class="list-unstyled small">
                                <li class="mb-1">📌 All Guest functions</li>
                                <li class="mb-1">📌 View dashboard</li>
                                <li class="mb-1">📌 Make booking</li>
                                <li class="mb-1">📌 Plan trip itinerary</li>
                                <li class="mb-1">📌 Leave park review</li>
                                <li class="mb-1">📌 Set preferences (cookies)</li>
                                <li class="mb-1">📌 View booking history</li>
                                <li class="mb-1">📌 Logout</li>
                            </ul>
                        </div>
                    </div>
                    <!-- Admin Actor -->
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 h-100" style="border-color:#dc3545 !important;">
                            <div class="text-center mb-3">
                                <div style="font-size:2.5rem;">🛡️</div>
                                <h5 class="fw-bold">Admin</h5>
                                <span class="badge bg-danger">Full Access</span>
                            </div>
                            <ul class="list-unstyled small">
                                <li class="mb-1">📌 All Tourist functions</li>
                                <li class="mb-1">📌 Access admin panel</li>
                                <li class="mb-1">📌 Create park</li>
                                <li class="mb-1">📌 Edit park</li>
                                <li class="mb-1">📌 Delete park</li>
                                <li class="mb-1">📌 Create animal</li>
                                <li class="mb-1">📌 Edit animal</li>
                                <li class="mb-1">📌 Delete animal</li>
                                <li class="mb-1">📌 View analytics</li>
                                <li class="mb-1">📌 View all users</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ERD Table -->
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-2">
                    <i class="bi bi-table text-success me-2"></i>
                    Entity Relationship Diagram (ERD) — Key Relationships
                </h4>
                <p class="text-muted small mb-4">
                    Shows how the 7 database tables are related to each other
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm small">
                        <thead class="table-dark">
                            <tr>
                                <th>Entity (Table)</th>
                                <th>Relationship</th>
                                <th>Entity (Table)</th>
                                <th>Type</th>
                                <th>Foreign Key</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>users</strong></td>
                                <td>makes</td>
                                <td><strong>bookings</strong></td>
                                <td><span class="badge bg-primary">One to Many</span></td>
                                <td><code>bookings.tourist_id → users.id</code></td>
                            </tr>
                            <tr>
                                <td><strong>users</strong></td>
                                <td>writes</td>
                                <td><strong>reviews</strong></td>
                                <td><span class="badge bg-primary">One to Many</span></td>
                                <td><code>reviews.user_id → users.id</code></td>
                            </tr>
                            <tr>
                                <td><strong>users</strong></td>
                                <td>has profile</td>
                                <td><strong>guides</strong></td>
                                <td><span class="badge bg-success">One to One</span></td>
                                <td><code>guides.user_id → users.id</code></td>
                            </tr>
                            <tr>
                                <td><strong>parks</strong></td>
                                <td>receives</td>
                                <td><strong>bookings</strong></td>
                                <td><span class="badge bg-primary">One to Many</span></td>
                                <td><code>bookings.park_id → parks.id</code></td>
                            </tr>
                            <tr>
                                <td><strong>parks</strong></td>
                                <td>receives</td>
                                <td><strong>reviews</strong></td>
                                <td><span class="badge bg-primary">One to Many</span></td>
                                <td><code>reviews.park_id → parks.id</code></td>
                            </tr>
                            <tr>
                                <td><strong>parks</strong></td>
                                <td>contains</td>
                                <td><strong>animals</strong></td>
                                <td><span class="badge bg-warning text-dark">Many to Many</span></td>
                                <td><code>park_animals (junction table)</code></td>
                            </tr>
                            <tr>
                                <td><strong>guides</strong></td>
                                <td>assigned to</td>
                                <td><strong>bookings</strong></td>
                                <td><span class="badge bg-primary">One to Many</span></td>
                                <td><code>bookings.guide_id → guides.id</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Security Architecture -->
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-2">
                    <i class="bi bi-shield-lock-fill text-success me-2"></i>
                    Security Architecture
                </h4>
                <p class="text-muted small mb-4">
                    Multi-layer security implementation as required by the course outline
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <h6 class="fw-bold mb-3">🔐 Authentication Layer</h6>
                            <ul class="small text-muted mb-0">
                                <li class="mb-1"><strong>password_hash(PASSWORD_BCRYPT)</strong> — passwords hashed with bcrypt before storage</li>
                                <li class="mb-1"><strong>password_verify()</strong> — hashes compared on login, plain text never stored</li>
                                <li class="mb-1"><strong>$_SESSION</strong> — server-side session stores user ID, name, email, role</li>
                                <li class="mb-1"><strong>session_destroy()</strong> — session fully cleared on logout</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <h6 class="fw-bold mb-3">🛡️ Database Security Layer</h6>
                            <ul class="small text-muted mb-0">
                                <li class="mb-1"><strong>Prepared statements</strong> — $conn->prepare() + bind_param() on all queries</li>
                                <li class="mb-1"><strong>No raw SQL concatenation</strong> — user input never inserted directly into queries</li>
                                <li class="mb-1"><strong>htmlspecialchars()</strong> — all output sanitised before rendering</li>
                                <li class="mb-1"><strong>filter_var(FILTER_VALIDATE_EMAIL)</strong> — email validated server-side</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <h6 class="fw-bold mb-3">🔑 Access Control Layer</h6>
                            <ul class="small text-muted mb-0">
                                <li class="mb-1"><strong>Role check on admin pages</strong> — $_SESSION['user_role'] === 'admin' required</li>
                                <li class="mb-1"><strong>Login redirect</strong> — protected pages redirect to login if not authenticated</li>
                                <li class="mb-1"><strong>3 roles</strong> — admin, tourist, guide with different permissions</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <h6 class="fw-bold mb-3">🍪 Cookie Security Layer</h6>
                            <ul class="small text-muted mb-0">
                                <li class="mb-1"><strong>setcookie() with expiry</strong> — cookies set with 30-day expiry</li>
                                <li class="mb-1"><strong>Path set to '/'</strong> — cookie accessible across entire site</li>
                                <li class="mb-1"><strong>Cookies for preferences only</strong> — no sensitive data stored in cookies</li>
                                <li class="mb-1"><strong>Sessions for authentication</strong> — login state uses server-side sessions not cookies</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tech Stack Summary -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-3">
                    <i class="bi bi-stack text-success me-2"></i>
                    Full Technology Stack Summary
                </h4>
                <div class="row g-3">
                    <?php
                    $stack = [
                        ['layer' => 'Presentation', 'tech' => 'HTML5, CSS3, Bootstrap 5, JavaScript', 'icon' => '🖥️'],
                        ['layer' => 'Styling', 'tech' => 'Bootstrap 5, Bootstrap Icons, Custom CSS', 'icon' => '🎨'],
                        ['layer' => 'Scripting', 'tech' => 'Vanilla JavaScript (ES6)', 'icon' => '⚡'],
                        ['layer' => 'Visualisation', 'tech' => 'Chart.js (bar, pie, doughnut charts)', 'icon' => '📊'],
                        ['layer' => 'Backend', 'tech' => 'PHP 8', 'icon' => '⚙️'],
                        ['layer' => 'Web Server', 'tech' => 'Apache (via XAMPP)', 'icon' => '🌐'],
                        ['layer' => 'Database', 'tech' => 'MySQL via MySQLi (PDO-style prepared statements)', 'icon' => '🗄️'],
                        ['layer' => 'Version Control', 'tech' => 'Git + GitHub', 'icon' => '📦'],
                        ['layer' => 'Dev Environment', 'tech' => 'XAMPP on Windows 11', 'icon' => '🛠️'],
                    ];
                    foreach ($stack as $item):
                    ?>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <span style="font-size:1.5rem;"><?= $item['icon'] ?></span>
                            <div>
                                <p class="fw-bold small mb-0"><?= $item['layer'] ?></p>
                                <p class="text-muted small mb-0"><?= $item['tech'] ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
