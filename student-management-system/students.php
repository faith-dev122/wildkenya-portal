<?php
/*
 * Week 10 – Dynamic HTML generation: the table rows below are generated
 *           by a PHP while-loop from a MySQL result set (JSP <% %> -> PHP).
 * Week 11 – Read + Search of the full CRUD set, with success/error
 *           messages and PreparedStatement (mysqli prepared statements).
 * Week 13 – Search by name, registration number or course, with a
 *           friendly "no records found" message; role-based access:
 *           only ADMIN sees Edit/Delete controls.
 */
require "includes/auth_check.php";
require "config/db.php";

$msg    = isset($_GET['msg'])   ? $_GET['msg']   : "";
$type   = isset($_GET['type'])  ? $_GET['type']  : "success";
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$field  = isset($_GET['field'])  ? $_GET['field'] : "full_name";

// Whitelist the searchable column (never put user input in column names)
$allowed = ["full_name", "reg_no", "course"];
if (!in_array($field, $allowed)) { $field = "full_name"; }

if ($search !== "") {
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE $field LIKE ? ORDER BY full_name");
    $like = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, "s", $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM students ORDER BY full_name");
}

include "includes/header.php";
?>
<div class="card">
  <h1>Student Records</h1>

  <?php if ($msg): ?>
    <div class="msg <?php echo $type === 'error' ? 'error' : 'success'; ?>">
      <?php echo htmlspecialchars($msg); ?>
    </div>
  <?php endif; ?>

  <!-- Week 13: Search facility -->
  <form method="GET" action="students.php" class="searchbar">
    <select name="field">
      <option value="full_name" <?php if($field=="full_name") echo "selected"; ?>>Name</option>
      <option value="reg_no"    <?php if($field=="reg_no")    echo "selected"; ?>>Registration Number</option>
      <option value="course"    <?php if($field=="course")    echo "selected"; ?>>Course</option>
    </select>
    <input type="text" name="search" placeholder="Search students..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit" class="btn" style="margin-top:0;">Search</button>
    <a href="students.php" class="btn secondary" style="margin-top:0;">Clear</a>
  </form>

  <div class="table-wrap">
  <?php if (mysqli_num_rows($result) > 0): ?>
    <table>
      <tr>
        <th>#</th><th>Reg No</th><th>Full Name</th><th>Email</th><th>Course</th>
        <?php if ($_SESSION['role'] === 'admin'): ?><th>Actions</th><?php endif; ?>
      </tr>
      <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo htmlspecialchars($row['reg_no']); ?></td>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['course']); ?></td>
        <?php if ($_SESSION['role'] === 'admin'): // Week 13: role-based access ?>
        <td>
          <a class="btn small" href="edit_student.php?id=<?php echo $row['id']; ?>">Edit</a>
          <a class="btn small danger" href="delete_student.php?id=<?php echo $row['id']; ?>"
             onclick="return confirm('Delete <?php echo htmlspecialchars($row['full_name']); ?>? This cannot be undone.');">Delete</a>
        </td>
        <?php endif; ?>
      </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <div class="msg info">No records found<?php echo $search ? " for \"" . htmlspecialchars($search) . "\"" : ""; ?>. Try a different search term.</div>
  <?php endif; ?>
  </div>
</div>
<?php include "includes/footer.php"; ?>
