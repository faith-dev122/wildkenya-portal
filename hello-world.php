<?php
// ============================================================
// WildKenya — Hello World Test Page
// Week 1 Requirement: First PHP + database connection test
// ============================================================

// Test database connection
$conn = mysqli_connect("localhost", "root", "", "wildkenya");
$db_status = $conn ? "Connected Successfully ✅" : "Connection Failed ❌";
$db_colour = $conn ? "#198754" : "#dc3545";

// PHP variables demonstration (Week 3)
$project_name = "WildKenya";
$student_tech = "PHP + MySQL";
$server_name  = $_SERVER['SERVER_NAME'];
$php_version  = phpversion();
$date_today   = date('l, d F Y');
$time_now     = date('H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello World — WildKenya BIT3208</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <!-- Hello World Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-5 text-center">
                    <div style="font-size:4rem;">🦒</div>
                    <h1 class="fw-bold mt-3" style="color:#2d6a4f;">Hello World!</h1>
                    <h4 class="text-muted">Welcome to <?= $project_name ?></h4>
                    <p class="lead">
                        PHP is working correctly on this server.<br>
                        This confirms the local development environment is ready.
                    </p>
                    <span class="badge bg-success fs-6 px-4 py-2">
                        BIT3208 — Advanced Web Design and Development
                    </span>
                </div>
            </div>

            <!-- PHP Variables Demo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        PHP Variables Demo (Week 3 — Task 1)
                    </h5>
                    <table class="table table-sm table-bordered">
                        <thead class="table-dark small">
                            <tr>
                                <th>PHP Variable</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>$project_name</code></td>
                                <td><?= htmlspecialchars($project_name) ?></td>
                            </tr>
                            <tr>
                                <td><code>$student_tech</code></td>
                                <td><?= htmlspecialchars($student_tech) ?></td>
                            </tr>
                            <tr>
                                <td><code>$_SERVER['SERVER_NAME']</code></td>
                                <td><?= htmlspecialchars($server_name) ?></td>
                            </tr>
                            <tr>
                                <td><code>phpversion()</code></td>
                                <td><?= htmlspecialchars($php_version) ?></td>
                            </tr>
                            <tr>
                                <td><code>date('l, d F Y')</code></td>
                                <td><?= htmlspecialchars($date_today) ?></td>
                            </tr>
                            <tr>
                                <td><code>date('H:i:s')</code></td>
                                <td><?= htmlspecialchars($time_now) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Database Connection Test -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        Database Connection Test (Week 1 — Fig 5)
                    </h5>
                    <div class="p-3 rounded-3 text-white text-center fw-bold fs-5"
                         style="background:<?= $db_colour ?>;">
                        <?= $db_status ?>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded-3">
                        <p class="small mb-1 fw-bold">Connection Code Used:</p>
                        <code class="small">
                            $conn = mysqli_connect("localhost", "root", "", "wildkenya");<br>
                            if($conn) { echo "Connected Successfully"; }
                        </code>
                    </div>
                    <?php if ($conn): ?>
                    <div class="mt-3">
                        <p class="small fw-bold mb-2">Tables in wildkenya database:</p>
                        <?php
                        $tables = mysqli_query($conn, "SHOW TABLES");
                        while ($t = mysqli_fetch_array($tables)) {
                            echo '<span class="badge bg-success me-1 mb-1">' .
                                 htmlspecialchars($t[0]) . '</span>';
                        }
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PHP Echo Demo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        PHP echo + Conditional Statements Demo (Week 4)
                    </h5>
                    <?php
                    $user  = "Student";
                    $score = 85;

                    // Conditional statements
                    if ($score >= 70) {
                        $grade   = "A";
                        $message = "Excellent work!";
                    } elseif ($score >= 60) {
                        $grade   = "B";
                        $message = "Good work!";
                    } else {
                        $grade   = "C";
                        $message = "Keep improving!";
                    }
                    ?>
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <code>
                            $score = <?= $score ?>;<br>
                            if ($score >= 70) { echo "Grade: A"; }<br>
                            // Output: Grade <?= $grade ?> — <?= $message ?>
                        </code>
                    </div>
                    <div class="alert alert-success mb-0">
                        Hello, <strong><?= $user ?></strong>!
                        Your score is <strong><?= $score ?></strong>.
                        Grade: <strong><?= $grade ?></strong> — <?= $message ?>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="text-center">
                <a href="/wildkenya/" class="btn btn-success px-4">
                    ← Back to WildKenya Homepage
                </a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
