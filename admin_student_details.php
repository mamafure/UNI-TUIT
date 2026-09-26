<?php
include 'db.php';
include 'flash.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: admin_login.php"); exit(); }

$user_id = (int) $_GET['user_id'];

// Get student personal details
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$student = mysqli_fetch_assoc($user_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Details: <?php echo $student['username']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f1f5f9; padding: 40px; }
        .container { max-width: 900px; margin: auto; }
        .back-btn { text-decoration: none; color: #1e3a8a; font-weight: bold; margin-bottom: 20px; display: inline-block; }
        
        .profile-card { background: white; padding: 30px; border-radius: 15px; display: flex; gap: 30px; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .profile-card i { font-size: 50px; color: #cbd5e1; }
        
        .subject-table { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px; text-align: left; color: #64748b; }
        td { padding: 15px; border-top: 1px solid #f1f5f9; }

        .status { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-Draft { background: #e2e8f0; color: #475569; }
        .status-Pending { background: #fef3c7; color: #92400e; }
        .status-Registered { background: #d1fae5; color: #065f46; }
        .status-Rejected { background: #fee2e2; color: #991b1b; }

        tr.row-actionable { transition: background 0.15s; }
        tr.row-actionable:hover { background: #f8fafc; }

        .action-group { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .btn-approve, .btn-reject { border: none; cursor: pointer; padding: 7px 14px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold; }
        .btn-approve { background: #10b981; color: white; }
        .btn-approve:hover { background: #059669; }
        .btn-reject { background: #fee2e2; color: #991b1b; }
        .btn-reject:hover { background: #fecaca; }
        .waiting-note { color: #94a3b8; font-size: 12px; font-style: italic; }
    </style>
</head>
<body>

<?php flash_render(); ?>

<div class="container">
    <a href="admin.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Student List</a>

    <div class="profile-card">
        <i class="fas fa-user-circle"></i>
        <div>
            <h1 style="margin:0;"><?php echo htmlspecialchars($student['username'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <p style="margin:5px 0; color:#64748b;">
                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($student['email'], ENT_QUOTES, 'UTF-8'); ?> |
                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($student['phone'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p style="margin:0; font-weight:bold; color:#1e3a8a;">Program: <?php echo htmlspecialchars($student['program'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>

    <h3>Selected Modules</h3>
    <div class="subject-table">
        <table>
            <thead>
                <tr>
                    <th>Module Name</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $reg_query = mysqli_query($conn, "SELECT * FROM registrations WHERE user_id = '$user_id'");
                while($row = mysqli_fetch_assoc($reg_query)) {
                    $module_name = htmlspecialchars($row['module_name'], ENT_QUOTES, 'UTF-8');
                    $status = $row['status'];
                    $reg_id = (int) $row['id'];
                    echo "<tr>";
                    echo "<td><strong>{$module_name}</strong></td>";
                    echo "<td>" . number_format((float) $row['fee']) . " Tsh</td>";
                    echo "<td><span class='status status-{$status}'>{$status}</span></td>";
                    echo "<td>";
                    if ($status == 'Pending') {
                        echo "<div class='action-group'>
                                <a href='approve.php?id={$reg_id}&user_id={$user_id}&action=approve' class='btn-approve'><i class='fas fa-check'></i> Approve</a>
                                <a href='approve.php?id={$reg_id}&user_id={$user_id}&action=reject' class='btn-reject' onclick=\"return confirmReject(this, '{$module_name}')\"><i class='fas fa-xmark'></i> Reject</a>
                              </div>";
                    } elseif ($status == 'Draft') {
                        echo "<span class='waiting-note'><i class='fas fa-clock'></i> Not submitted by student yet</span>";
                    } elseif ($status == 'Registered') {
                        echo "<i class='fas fa-check-circle' style='color:#10b981;'></i> Confirmed";
                    } elseif ($status == 'Rejected') {
                        echo "<i class='fas fa-circle-xmark' style='color:#991b1b;'></i> Rejected";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
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

<style>
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:3000; align-items:center; justify-content:center; padding:20px; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:white; border-radius:14px; padding:28px; max-width:360px; width:100%; text-align:center; box-shadow:0 20px 50px rgba(0,0,0,0.3); }
    .btn-cancel { flex:1; background:#f1f5f9; color:#475569; border:none; padding:10px; border-radius:6px; cursor:pointer; font-weight:bold; }
    .btn-cancel:hover { background:#e2e8f0; }
</style>

<script>
    function confirmReject(link, moduleName) {
        document.getElementById('rejectConfirmText').textContent =
            'This will mark "' + moduleName + '" as rejected. The student will see this on their dashboard.';
        document.getElementById('rejectConfirmLink').setAttribute('href', link.getAttribute('href'));
        document.getElementById('rejectConfirm').classList.add('open');
        return false; // stop the original click's navigation; the modal link handles it
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