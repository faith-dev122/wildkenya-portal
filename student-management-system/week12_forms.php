<?php
/*
 * Week 12 – ASP.NET Concepts implemented in PHP (our environment is XAMPP,
 * so we demonstrate the SAME concepts the ASP.NET notes teach):
 *
 *   ASP.NET concept            ->  PHP equivalent used here
 *   ------------------------------------------------------------------
 *   Web Form + Server Controls ->  HTML form processed by PHP (POST-back)
 *   DropDownList control       ->  <select> populated by a foreach loop
 *   Label control              ->  echo into the page
 *   GridView control           ->  dynamic <table> from a MySQL query
 *   Session object             ->  $_SESSION superglobal
 *   Control structures (C#)    ->  if/else, switch, for, foreach in PHP
 */
require "includes/auth_check.php";
require "config/db.php";

$courses = ["Computer Science", "Information Technology", "Software Engineering", "Business IT"];
$greeting = ""; $chosen = "";

// Control structure demo: if/else based on server time (like C# in Page_Load)
$hour = (int)date("H");
if ($hour < 12)      { $timeGreeting = "Good morning"; }
elseif ($hour < 17)  { $timeGreeting = "Good afternoon"; }
else                 { $timeGreeting = "Good evening"; }

if ($_SERVER["REQUEST_METHOD"] === "POST") {   // ASP.NET "PostBack"
    $chosen   = trim($_POST["course"]);
    $greeting = "$timeGreeting, " . htmlspecialchars($_SESSION['full_name']) .
                "! You selected: " . htmlspecialchars($chosen);
}

// GridView equivalent: fetch students for the dynamic table
$result = mysqli_query($conn, "SELECT reg_no, full_name, course FROM students ORDER BY full_name");

include "includes/header.php";
?>
<div class="card">
  <h1>Week 12 – Web Forms &amp; Control Structures (ASP.NET concepts in PHP)</h1>
  <p><?php echo $timeGreeting; ?>! This page reproduces the ASP.NET Web Forms exercise using PHP.</p>

  <form method="POST" action="week12_forms.php">
    <label>Select your course (DropDownList equivalent)</label>
    <select name="course">
      <?php foreach ($courses as $c): ?>
        <option value="<?php echo htmlspecialchars($c); ?>" <?php if ($chosen === $c) echo "selected"; ?>>
          <?php echo htmlspecialchars($c); ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn">Submit (PostBack)</button>
  </form>

  <?php if ($greeting): ?>
    <div class="msg success" style="margin-top:14px;"><?php echo $greeting; ?></div>
  <?php endif; ?>
</div>

<div class="card">
  <h2>GridView Equivalent – Dynamic Student Table</h2>
  <div class="table-wrap">
    <table>
      <tr><th>Reg No</th><th>Full Name</th><th>Course</th></tr>
      <?php while ($row = mysqli_fetch_assoc($result)): // for-loop / GridView binding ?>
        <tr>
          <td><?php echo htmlspecialchars($row['reg_no']); ?></td>
          <td><?php echo htmlspecialchars($row['full_name']); ?></td>
          <td><?php echo htmlspecialchars($row['course']); ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>

<div class="card">
  <h2>Session Management Comparison (Java vs PHP vs ASP.NET)</h2>
  <div class="table-wrap">
    <table>
      <tr><th>Feature</th><th>Java Servlets/JSP</th><th>PHP</th><th>ASP.NET</th></tr>
      <tr><td>Create session</td><td>request.getSession()</td><td>session_start()</td><td>Automatic (Session object)</td></tr>
      <tr><td>Store value</td><td>session.setAttribute()</td><td>$_SESSION["key"] = value</td><td>Session["key"] = value</td></tr>
      <tr><td>Read value</td><td>session.getAttribute()</td><td>$_SESSION["key"]</td><td>Session["key"]</td></tr>
      <tr><td>End session</td><td>session.invalidate()</td><td>session_destroy()</td><td>Session.Abandon()</td></tr>
      <tr><td>Cookies</td><td>Cookie class + addCookie()</td><td>setcookie() / $_COOKIE</td><td>Response.Cookies</td></tr>
    </table>
  </div>
</div>
<?php include "includes/footer.php"; ?>
