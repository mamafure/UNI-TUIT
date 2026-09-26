<?php
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

include 'admin_ui.php';

$subject_name = (string)($_GET['name'] ?? '');

// Confirmed students for THIS subject (prepared statement: name comes from the URL)
$stmt = mysqli_prepare($conn, "SELECT users.username, users.email, users.phone, users.program
          FROM registrations
          JOIN users ON registrations.user_id = users.id
          WHERE registrations.module_name = ? AND registrations.status = 'Registered'
          ORDER BY users.username ASC");
mysqli_stmt_bind_param($stmt, 's', $subject_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rows = [];
while ($row = mysqli_fetch_assoc($result)) { $rows[] = $row; }
mysqli_stmt_close($stmt);

admin_page_start('Class List: ' . $subject_name, 'subjects', [
    'eyebrow' => 'Official class list',
    'heading' => $subject_name,
    'sub' => 'Confirmed students registered for this module.',
    'stats' => false,
    'back' => ['admin_subjects.php', 'Back to Subjects'],
]);
?>
    <div class="panel">
        <div class="panel-head">
            <div>
                <span class="eyebrow">Enrolment</span>
                <h2><?php echo count($rows); ?> confirmed <?php echo count($rows) === 1 ? 'student' : 'students'; ?></h2>
            </div>
        </div>

        <?php if ($rows): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Student</th><th>Email</th><th>Phone</th><th>Program</th></tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><div class="person"><span class="avatar"><?php echo admin_h(strtoupper(mb_substr($row['username'], 0, 1))); ?></span><strong><?php echo admin_h($row['username']); ?></strong></div></td>
                        <td><?php echo admin_h($row['email']); ?></td>
                        <td class="mono"><?php echo admin_h($row['phone']); ?></td>
                        <td><?php echo admin_h($row['program']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <p>No students have been officially registered for this module yet.</p>
            </div>
        <?php endif; ?>
    </div>
<?php admin_page_end();
