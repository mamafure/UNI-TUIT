<?php
include 'db.php';
include 'flash.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

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
$pending_count = count($pending);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Approvals | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #1e3a8a; --bg: #f1f5f9; --accent: #f59e0b; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); margin: 0; display: flex; }

        .sidebar { width: 250px; background: var(--primary); height: 100vh; color: white; padding: 20px; position: fixed; }
        .sidebar h2 { border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }
        .sidebar a { color: white; text-decoration: none; display: flex; justify-content: space-between; align-items: center; padding: 12px; margin: 5px 0; border-radius: 8px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); }
        .nav-badge { background: var(--accent); color: white; font-size: 11px; font-weight: bold; padding: 2px 8px; border-radius: 10px; }

        .main { margin-left: 250px; width: 100%; padding: 40px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #64748b; text-transform: uppercase; font-size: 13px; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; }

        .action-group { display: flex; gap: 8px; }
        .btn-approve, .btn-reject { border: none; cursor: pointer; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; }
        .btn-approve { background: #10b981; color: white; }
        .btn-approve:hover { background: #059669; }
        .btn-reject { background: #fee2e2; color: #991b1b; }
        .btn-reject:hover { background: #fecaca; }

        .empty-state { padding: 60px 20px; text-align: center; color: #94a3b8; }
        .empty-state i { font-size: 50px; margin-bottom: 15px; display: block; }

        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:3000; align-items:center; justify-content:center; padding:20px; }
        .modal-overlay.open { display:flex; }
        .modal-box { background:white; border-radius:14px; padding:28px; max-width:360px; width:100%; text-align:center; box-shadow:0 20px 50px rgba(0,0,0,0.3); }
        .btn-cancel { flex:1; background:#f1f5f9; color:#475569; border:none; padding:10px; border-radius:6px; cursor:pointer; font-weight:bold; }
        .btn-cancel:hover { background:#e2e8f0; }
    </style>
</head>
<body>

<?php flash_render(); ?>

<div class="sidebar">
    <h2>UNI-TUIT Admin</h2>
    <a href="admin.php"><i class="fas fa-users"></i> Student List</a>
    <a href="admin_pending.php" style="background:rgba(255,255,255,0.1)">
        <span><i class="fas fa-hourglass-half"></i> Pending Approvals</span>
        <?php if ($pending_count > 0): ?><span class="nav-badge"><?php echo $pending_count; ?></span><?php endif; ?>
    </a>
    <a href="admin_subjects.php"><i class="fas fa-book"></i> Subject Reports</a>
    <a href="services.php"><i class="fas fa-tools"></i> Services</a>
    <a href="logout.php" style="margin-top: 50px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
    <h1>Pending Approvals</h1>
    <p>Every module registration waiting on your confirmation, across all students, in one place.</p>

    <div class="card">
        <?php if ($pending_count === 0): ?>
            <div class="empty-state">
                <i class="fas fa-circle-check"></i>
                <p>Nothing waiting on you right now — every submitted registration has been reviewed.</p>
            </div>
        <?php else: ?>
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
                    <?php foreach ($pending as $row): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['email']); ?><br><small><?php echo htmlspecialchars($row['phone']); ?></small></td>
                            <td><?php echo htmlspecialchars($row['module_name']); ?></td>
                            <td><?php echo number_format((float) $row['fee']); ?> Tsh</td>
                            <td>
                                <div class="action-group">
                                    <a href="approve.php?id=<?php echo (int) $row['id']; ?>&user_id=<?php echo (int) $row['user_id']; ?>&return=pending&action=approve" class="btn-approve">
                                        <i class="fas fa-check"></i> Approve
                                    </a>
                                    <a href="approve.php?id=<?php echo (int) $row['id']; ?>&user_id=<?php echo (int) $row['user_id']; ?>&return=pending&action=reject"
                                       class="btn-reject"
                                       onclick="return confirmReject(this, '<?php echo htmlspecialchars(addslashes($row['module_name']), ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars(addslashes($row['username']), ENT_QUOTES, 'UTF-8'); ?>')">
                                        <i class="fas fa-xmark"></i> Reject
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div id="rejectConfirm" class="modal-overlay">
    <div class="modal-box">
        <i class="fas fa-triangle-exclamation" style="font-size:32px; color:#991b1b;"></i>
        <h3 style="margin:14px 0 6px;">Reject this registration?</h3>
        <p id="rejectConfirmText" style="color:#64748b; font-size:14px; margin:0 0 20px;"></p>
        <div style="display:flex; gap:10px;">
            <button type="button" class="btn-cancel" onclick="closeRejectConfirm()">Cancel</button>
            <a id="rejectConfirmLink" href="#" class="btn-reject" style="flex:1; text-align:center; padding:10px;">Yes, Reject</a>
        </div>
    </div>
</div>

<script>
    function confirmReject(link, moduleName, studentName) {
        document.getElementById('rejectConfirmText').textContent =
            'This will mark "' + moduleName + '" as rejected for ' + studentName + '.';
        document.getElementById('rejectConfirmLink').setAttribute('href', link.getAttribute('href'));
        document.getElementById('rejectConfirm').classList.add('open');
        return false;
    }

    function closeRejectConfirm() {
        document.getElementById('rejectConfirm').classList.remove('open');
    }

    document.getElementById('rejectConfirm').addEventListener('click', function (e) {
        if (e.target === this) closeRejectConfirm();
    });
</script>

</body>
</html>
