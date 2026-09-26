<?php
include 'db.php';
include 'flash.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

include 'admin_ui.php';

$query = "SELECT registrations.id, registrations.module_name, registrations.fee,
                 users.id AS user_id, users.username, users.email, users.phone
          FROM registrations
          JOIN users ON registrations.user_id = users.id
          WHERE registrations.status = 'Pending'
          ORDER BY registrations.id ASC";
$result = mysqli_query($conn, $query);

$pending = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pending[] = $row;
}

admin_page_start('Pending Approvals', 'pending', [
    'heading' => 'Pending Approvals',
    'sub' => 'Every module registration waiting on your confirmation, across all students.',
]);
?>
    <div class="panel">
        <div class="panel-head">
            <div>
                <span class="eyebrow">Review queue</span>
                <h2>Waiting on you</h2>
            </div>
        </div>

        <?php if (!$pending): ?>
            <div class="empty-state">
                <i class="fas fa-circle-check"></i>
                <p>Nothing waiting on you right now &mdash; every submitted registration has been reviewed.</p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Contact</th>
                        <th>Module</th>
                        <th>Fee</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pending as $row):
                    $rid = (int)$row['id']; $uid = (int)$row['user_id'];
                    $base = "approve.php?id={$rid}&user_id={$uid}&return=pending";
                ?>
                    <tr>
                        <td><div class="person"><span class="avatar"><?php echo admin_h(strtoupper(mb_substr($row['username'], 0, 1))); ?></span><strong><?php echo admin_h($row['username']); ?></strong></div></td>
                        <td><?php echo admin_h($row['email']); ?><br><small><?php echo admin_h($row['phone']); ?></small></td>
                        <td><strong><?php echo admin_h($row['module_name']); ?></strong></td>
                        <td class="mono"><?php echo number_format((float)$row['fee']); ?> Tsh</td>
                        <td>
                            <div class="action-group">
                                <a href="<?php echo $base; ?>&action=approve" class="btn btn-approve"><i class="fas fa-check"></i> Approve</a>
                                <a href="<?php echo $base; ?>&action=reject" class="btn btn-reject"
                                   onclick="return confirmReject(this, <?php echo admin_h(json_encode($row['module_name'])); ?>, <?php echo admin_h(json_encode($row['username'])); ?>)"><i class="fas fa-xmark"></i> Reject</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

<?php admin_reject_modal(); admin_page_end();
