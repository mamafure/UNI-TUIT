<?php
include 'db.php';
include 'flash.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: admin_login.php"); exit(); }

include 'admin_ui.php';

$user_id = (int)($_GET['user_id'] ?? 0);

$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));
if (!$student) {
    flash_set('error', 'That student could not be found.');
    header("Location: admin.php");
    exit();
}

$regs = [];
$reg_query = mysqli_query($conn, "SELECT * FROM registrations WHERE user_id = $user_id ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($reg_query)) { $regs[] = $row; }

admin_page_start('Student: ' . $student['username'], 'students', [
    'eyebrow' => 'Student profile',
    'heading' => $student['username'],
    'sub' => 'Review this student\'s modules and confirm or reject submissions.',
    'stats' => false,
    'back' => ['admin.php', 'Back to Student List'],
]);
?>
    <div class="panel">
        <div class="profile">
            <span class="avatar"><?php echo admin_h(strtoupper(mb_substr($student['username'], 0, 1))); ?></span>
            <div>
                <h2><?php echo admin_h($student['username']); ?></h2>
                <div class="profile-meta">
                    <span><i class="fas fa-envelope"></i><?php echo admin_h($student['email']); ?></span>
                    <span><i class="fas fa-phone"></i><?php echo admin_h($student['phone']); ?></span>
                    <span><i class="fas fa-graduation-cap"></i><?php echo admin_h($student['program']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <div>
                <span class="eyebrow">Registration list</span>
                <h2>Selected Modules</h2>
            </div>
        </div>

        <?php if (!$regs): ?>
            <div class="empty-state"><i class="fas fa-book-open"></i><p>This student hasn't selected any modules yet.</p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Module</th><th>Fee</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($regs as $row):
                    $status = $row['status'];
                    $reg_id = (int)$row['id'];
                    $base = "approve.php?id={$reg_id}&user_id={$user_id}";
                ?>
                    <tr>
                        <td><strong><?php echo admin_h($row['module_name']); ?></strong></td>
                        <td class="mono"><?php echo number_format((float)$row['fee']); ?> Tsh</td>
                        <td><span class="badge badge-<?php echo admin_h(strtolower($status)); ?>"><?php echo admin_h($status); ?></span></td>
                        <td>
                            <?php if ($status === 'Pending'): ?>
                                <div class="action-group">
                                    <a href="<?php echo $base; ?>&action=approve" class="btn btn-approve"><i class="fas fa-check"></i> Approve</a>
                                    <a href="<?php echo $base; ?>&action=reject" class="btn btn-reject"
                                       onclick="return confirmReject(this, <?php echo admin_h(json_encode($row['module_name'])); ?>)"><i class="fas fa-xmark"></i> Reject</a>
                                </div>
                            <?php elseif ($status === 'Draft'): ?>
                                <span class="muted"><i class="fas fa-clock"></i> Not submitted by student yet</span>
                            <?php elseif ($status === 'Registered'): ?>
                                <span class="muted"><i class="fas fa-circle-check" style="color:var(--success)"></i> Confirmed</span>
                            <?php elseif ($status === 'Rejected'): ?>
                                <span class="muted"><i class="fas fa-circle-xmark" style="color:var(--danger)"></i> Rejected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

<?php admin_reject_modal(); admin_page_end();
