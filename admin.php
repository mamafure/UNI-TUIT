<?php
include 'db.php';

// ACCESS CONTROL: Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

include 'admin_ui.php';

// Unique students who have at least one registration
$sql = "SELECT users.id, users.username, users.email, users.phone, users.program,
        COUNT(registrations.id) AS total_mods,
        SUM(registrations.status = 'Pending') AS pending_mods
        FROM users
        JOIN registrations ON users.id = registrations.user_id
        GROUP BY users.id
        ORDER BY users.username ASC";
$result = mysqli_query($conn, $sql);
$students = [];
while ($row = mysqli_fetch_assoc($result)) { $students[] = $row; }

admin_page_start('Student Registrations', 'students', [
    'heading' => 'Student Registrations',
    'sub' => 'Every student who has applied for modules, with their current activity.',
]);
?>
    <div class="panel">
        <div class="panel-head">
            <div>
                <span class="eyebrow">Directory</span>
                <h2>All Students</h2>
            </div>
            <?php if ($students): ?>
            <label class="search"><i class="fas fa-magnifying-glass"></i>
                <input type="search" id="studentSearch" placeholder="Search name, email or program" aria-label="Search students">
            </label>
            <?php endif; ?>
        </div>

        <?php if (!$students): ?>
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <p>No students have selected modules yet.</p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table id="studentTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Contact</th>
                        <th>Program</th>
                        <th>Modules</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($students as $s):
                    $uid = (int)$s['id'];
                    $initial = strtoupper(mb_substr($s['username'], 0, 1));
                    $pend = (int)$s['pending_mods'];
                    $mods = (int)$s['total_mods'];
                ?>
                    <tr class="clickable" onclick="location.href='admin_student_details.php?user_id=<?php echo $uid; ?>'">
                        <td><div class="person"><span class="avatar"><?php echo admin_h($initial); ?></span><strong><?php echo admin_h($s['username']); ?></strong></div></td>
                        <td><?php echo admin_h($s['email']); ?><br><small><?php echo admin_h($s['phone']); ?></small></td>
                        <td><?php echo admin_h($s['program']); ?></td>
                        <td><div class="pill-group">
                            <span class="count-pill"><?php echo $mods; ?> <?php echo $mods === 1 ? 'module' : 'modules'; ?></span>
                            <?php if ($pend > 0): ?><span class="badge badge-pending"><?php echo $pend; ?> pending</span><?php endif; ?>
                        </div></td>
                        <td><a href="admin_student_details.php?user_id=<?php echo $uid; ?>" class="btn btn-outline">View <i class="fas fa-arrow-right"></i></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

<script>
    var box = document.getElementById('studentSearch');
    if (box) box.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#studentTable tbody tr').forEach(function (tr) {
            tr.style.display = tr.textContent.toLowerCase().indexOf(q) === -1 ? 'none' : '';
        });
    });
</script>
<?php admin_page_end();
