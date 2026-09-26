<?php
include 'db.php';
include 'flash.php';
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$u_id = $_SESSION['user_id'];

// Admin accounts (e.g. a session opened before admin routing existed) belong on the dashboard
$role_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT role FROM users WHERE id = " . (int)$u_id));
if ($role_row && $role_row['role'] === 'admin') {
    $_SESSION['admin_id'] = (int)$u_id;
    $_SESSION['role'] = 'admin';
    header("Location: admin.php");
    exit();
}

// 1. Define all available subjects in your system
$all_system_modules = ["Database System", "OS", "Networking", "Web Programming", "Calculus"];
$max_modules = count($all_system_modules);

// 2. Fetch the user's current selections
$query = "SELECT * FROM registrations WHERE user_id = '$u_id'";
$result = mysqli_query($conn, $query);

$selected_subjects = [];
$total_fee = 0;
$draft_count = 0;
$pending_count = 0;
$registered_count = 0;
$rejected_count = 0;
$user_total_count = 0;

while($row = mysqli_fetch_assoc($result)) {
    $selected_subjects[] = $row;
    $user_total_count++;

    if($row['status'] == 'Draft') $draft_count++;
    if($row['status'] == 'Pending') $pending_count++;
    if($row['status'] == 'Registered') $registered_count++;
    if($row['status'] == 'Rejected') $rejected_count++;

    // Rejected modules don't count toward what the student owes
    if($row['status'] != 'Rejected') $total_fee += $row['fee'];
}

// Modules still "in play" (not rejected) — used to know when everything active is Registered
$active_total = $user_total_count - $rejected_count;

$btn_main_text = ($user_total_count > 0) ? "+ Add Another Subject" : "Select Your Subjects";
$display_name = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
$js_name = json_encode($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Learning Dashboard | UNI-TUIT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #10254e;
            --ink-2: #16327a;
            --ink-soft: #52618a;
            --paper: #faf8f3;
            --paper-2: #f2ede0;
            --line: #e4dfd3;
            --gold: #c99a3b;
            --gold-light: #e6c878;
            --success: #1c7a4d;
            --success-bg: #e7f4ec;
            --danger: #a63a3a;
            --danger-bg: #fbecec;
            --radius: 16px;
            --shadow: 0 20px 45px -20px rgba(16, 37, 78, 0.35);
        }

        * { box-sizing: border-box; }

        body { font-family: 'Inter', sans-serif; background: var(--paper); color: var(--ink); margin: 0; -webkit-font-smoothing: antialiased; }
        h1, h2, h3 { font-family: 'Fraunces', serif; margin: 0; }
        a { color: inherit; }
        button { font-family: inherit; }

        :focus-visible { outline: 2px solid var(--gold); outline-offset: 3px; }

        .eyebrow { font-family: 'IBM Plex Mono', monospace; font-size: 11.5px; letter-spacing: 0.16em; text-transform: uppercase; font-weight: 600; }

        .btn { border: none; cursor: pointer; border-radius: 999px; font-weight: 600; font-size: 14.5px; padding: 11px 24px; transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-gold { background: var(--gold); color: var(--ink); }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-1px); box-shadow: 0 8px 18px -6px rgba(201, 154, 59, 0.6); }
        .btn-ghost-light { background: rgba(255,255,255,0.08); color: #fff; border: 1.5px solid rgba(255,255,255,0.35); }
        .btn-ghost-light:hover { border-color: #fff; background: rgba(255,255,255,0.16); }
        .btn-outline { background: transparent; color: var(--ink); border: 1.5px solid var(--line); }
        .btn-outline:hover { border-color: var(--ink); }
        .btn-block { width: 100%; justify-content: center; padding: 15px 24px; font-size: 15.5px; }

        /* ---------- Portal header ---------- */
        .portal-header { background: var(--paper); padding: 16px 6%; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 100; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand-mark { width: 36px; height: 36px; border-radius: 50%; background: var(--ink); color: var(--gold-light); display: flex; align-items: center; justify-content: center; font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 13px; border: 1px solid var(--gold); flex-shrink: 0; }
        .brand-word { font-family: 'Fraunces', serif; font-size: 19px; font-weight: 700; color: var(--ink); }
        .portal-actions { display: flex; align-items: center; gap: 10px; }
        .icon-btn { width: 40px; height: 40px; border-radius: 50%; border: 1.5px solid var(--line); background: var(--paper); display: flex; align-items: center; justify-content: center; color: var(--ink-soft); text-decoration: none; transition: 0.2s; }
        .icon-btn:hover { border-color: var(--ink); color: var(--ink); }

        /* ---------- Welcome band ---------- */
        .portal-hero { background: radial-gradient(circle at 85% 0%, rgba(201,154,59,0.2), transparent 45%), linear-gradient(135deg, var(--ink) 0%, var(--ink-2) 100%); padding: 44px 6% 60px; }
        .portal-hero-inner { max-width: 1000px; margin: 0 auto; }
        .portal-hero .eyebrow { color: var(--gold-light); }
        .portal-hero h1 { color: #fff; font-size: 2rem; margin: 10px 0 8px; }
        .portal-hero p { color: rgba(255,255,255,0.75); margin: 0; }

        .stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 16px; margin-top: 32px; }
        .stat-tile { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.14); border-radius: 14px; padding: 16px 18px; }
        .stat-tile strong { display: block; font-family: 'Fraunces', serif; font-size: 1.5rem; color: #fff; }
        .stat-tile span { font-family: 'IBM Plex Mono', monospace; font-size: 10.5px; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(255,255,255,0.55); }

        /* ---------- Main ---------- */
        .portal-main { max-width: 1000px; margin: -34px auto 60px; padding: 0 6%; }
        .summary-card { background: #fff; border: 1px solid var(--line); border-radius: 20px; padding: 32px; box-shadow: var(--shadow); }
        .card-head { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 8px; flex-wrap: wrap; gap: 8px; }
        .card-head .eyebrow { color: var(--gold); }
        .card-head h2 { font-size: 1.4rem; }

        .empty-state { text-align: center; padding: 50px 10px; }
        .empty-state i { font-size: 44px; color: var(--line); margin-bottom: 14px; }
        .empty-state p { color: var(--ink-soft); margin: 0; }

        .subject-item { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 16px 4px; border-bottom: 1px solid var(--line); border-left: 3px solid transparent; padding-left: 14px; }
        .subject-item:first-of-type { margin-top: 18px; }
        .subject-item.status-draft { border-left-color: #94a3b8; }
        .subject-item.status-pending { border-left-color: var(--gold); }
        .subject-item.status-registered { border-left-color: var(--success); }
        .subject-item.status-rejected { border-left-color: var(--danger); }
        .subject-name { font-size: 15.5px; font-weight: 600; }
        .subject-fee { margin: 3px 0 0; font-family: 'IBM Plex Mono', monospace; font-size: 11.5px; color: var(--ink-soft); }

        .badge { font-family: 'IBM Plex Mono', monospace; padding: 5px 12px; border-radius: 999px; font-size: 10.5px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; white-space: nowrap; }
        .badge-draft { background: #eef1f6; color: #475569; }
        .badge-pending { background: #faf1dd; color: #93701e; border: 1px solid var(--gold); }
        .badge-registered { background: var(--success-bg); color: var(--success); border: 1px solid var(--success); }
        .badge-rejected { background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger); }

        .total-row { text-align: right; font-family: 'Fraunces', serif; font-size: 1.25rem; font-weight: 700; padding: 22px 4px 4px; color: var(--ink); }
        .total-row span { font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 600; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.08em; display: block; margin-bottom: 4px; }

        .congrats-box, .waiting-box { border-radius: 14px; text-align: center; margin-top: 22px; padding: 24px; }
        .congrats-box { background: var(--success-bg); border: 1.5px solid var(--success); }
        .congrats-box i { font-size: 34px; color: var(--success); margin-bottom: 8px; }
        .congrats-box h3 { color: var(--success); font-size: 1.2rem; }
        .congrats-box p { color: var(--success); opacity: 0.85; margin: 6px 0 0; }
        .waiting-box { color: #93701e; background: #fdf8ee; border: 1.5px dashed var(--gold); font-size: 14.5px; }
        .rejected-box { color: var(--danger); background: var(--danger-bg); border: 1.5px dashed var(--danger); font-size: 14.5px; text-align: left; }
        .rejected-box i { margin-right: 4px; }

        .browse-link { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 26px; color: var(--ink); font-weight: 600; text-decoration: none; padding: 14px; border: 1.5px dashed var(--line); border-radius: 12px; transition: 0.2s; }
        .browse-link:hover { border-color: var(--gold); color: var(--gold); }
        .all-explored { text-align: center; margin-top: 26px; padding-top: 18px; border-top: 1px solid var(--line); color: var(--ink-soft); font-size: 13.5px; }

        /* ---------- Confirm modal ---------- */
        .modal { display: none; position: fixed; inset: 0; z-index: 2000; background: rgba(10, 20, 45, 0.65); align-items: center; justify-content: center; padding: 24px; }
        .modal.open { display: flex; }
        .confirm-card { width: 100%; max-width: 420px; background: var(--paper); border-radius: 18px; box-shadow: 0 40px 80px -20px rgba(0,0,0,0.5); overflow: hidden; }
        .confirm-head { background: linear-gradient(135deg, var(--ink), var(--ink-2)); color: #fff; padding: 26px 28px; }
        .confirm-head .eyebrow { color: var(--gold-light); }
        .confirm-head h3 { font-size: 1.3rem; margin: 8px 0 0; }
        .confirm-body { padding: 24px 28px 28px; }
        .confirm-body p { color: var(--ink-soft); line-height: 1.6; margin: 0 0 22px; }
        .confirm-body strong { color: var(--ink); }
        .confirm-actions { display: flex; gap: 10px; }
        .confirm-actions .btn { flex: 1; }

        @media (max-width: 720px) {
            .portal-hero { padding: 36px 5% 54px; }
            .portal-hero h1 { font-size: 1.6rem; }
            .stat-row { grid-template-columns: repeat(2, 1fr); }
            .portal-main { padding: 0 5%; margin-top: -26px; }
            .summary-card { padding: 24px 20px; }
            .subject-item { flex-wrap: wrap; }
            .total-row { text-align: left; }
        }

        @media (max-width: 420px) {
            .portal-header { padding: 14px 5%; }
            .brand-word { font-size: 17px; }
            .stat-row { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

<?php flash_render(); ?>

<header class="portal-header">
    <a class="brand" href="home.php">
        <span class="brand-mark">UT</span>
        <span class="brand-word">UNI&middot;TUIT</span>
    </a>
    <div class="portal-actions">
        <a href="subject_list.php" class="btn btn-outline">Browse Modules</a>
        <a href="logout.php" class="icon-btn" title="Log out" aria-label="Log out"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</header>

<section class="portal-hero">
    <div class="portal-hero-inner">
        <span class="eyebrow">Student Portal</span>
        <h1>Welcome back, <?php echo $display_name; ?></h1>
        <p>Track your modules and registration status below.</p>

        <div class="stat-row">
            <div class="stat-tile"><strong><?php echo $draft_count; ?></strong><span>Draft</span></div>
            <div class="stat-tile"><strong><?php echo $pending_count; ?></strong><span>Pending</span></div>
            <div class="stat-tile"><strong><?php echo $registered_count; ?></strong><span>Registered</span></div>
            <?php if ($rejected_count > 0): ?>
            <div class="stat-tile"><strong><?php echo $rejected_count; ?></strong><span>Rejected</span></div>
            <?php endif; ?>
            <div class="stat-tile"><strong><?php echo number_format($total_fee); ?></strong><span>Total Tsh</span></div>
        </div>
    </div>
</section>

<div class="portal-main">
    <div class="summary-card">
        <div class="card-head">
            <div>
                <span class="eyebrow">My Tuition Modules</span>
                <h2><i class="fas fa-list-check" style="color:var(--gold); font-size:1.1rem;"></i>&nbsp; Registration List</h2>
            </div>
        </div>

        <?php if($user_total_count == 0): ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>Your registration list is currently empty.</p>
            </div>
        <?php else: ?>
            <?php foreach($selected_subjects as $sub):
                $status_slug = strtolower($sub['status']);
                $module_name = htmlspecialchars($sub['module_name'], ENT_QUOTES, 'UTF-8');
                $status_label = htmlspecialchars($sub['status'], ENT_QUOTES, 'UTF-8');
            ?>
                <div class="subject-item status-<?php echo $status_slug; ?>">
                    <div>
                        <div class="subject-name"><?php echo $module_name; ?></div>
                        <p class="subject-fee">Fee: <?php echo number_format($sub['fee']); ?> Tsh</p>
                    </div>
                    <span class="badge badge-<?php echo $status_slug; ?>"><?php echo $status_label; ?></span>
                </div>
            <?php endforeach; ?>

            <div class="total-row"><span>Total Tuition</span><?php echo number_format($total_fee); ?> Tsh</div>
        <?php endif; ?>

        <!-- Logic for Messages and Buttons -->
        <?php
        if ($draft_count > 0) {
            echo '<button class="btn btn-gold btn-block" onclick="openConfirm()">Submit for Registration</button>';
        }
        elseif ($pending_count > 0 && $registered_count < $active_total) {
            echo '<div class="waiting-box"><i class="fas fa-hourglass-half"></i> All your selections have been submitted. Please wait for the Admin to confirm your registration.</div>';
        }
        elseif ($registered_count > 0 && $active_total > 0 && $registered_count == $active_total) {
            echo '<div class="congrats-box">
                    <i class="fas fa-award"></i>
                    <h3>Congratulations!</h3>
                    <p>You are now a verified student of <strong>UNI-TUIT</strong>. Excellence awaits you!</p>
                  </div>';
        }

        if ($rejected_count > 0) {
            $rejected_names = array_map(function($s) {
                return htmlspecialchars($s['module_name'], ENT_QUOTES, 'UTF-8');
            }, array_filter($selected_subjects, function($s) { return $s['status'] === 'Rejected'; }));
            echo '<div class="rejected-box"><i class="fas fa-circle-exclamation"></i> <strong>' . implode(', ', $rejected_names) . '</strong> '
                . ($rejected_count > 1 ? 'were not approved.' : 'was not approved.')
                . ' Contact the admin for details, or browse modules to pick another.</div>';
        }
        ?>

        <!-- SMART BUTTON LOGIC: Hide button if user has all subjects -->
        <?php if ($user_total_count < 2): ?>
            <a href="subject_list.php" class="browse-link">
                <i class="fas fa-plus"></i> <?php echo $btn_main_text; ?>
            </a>
        <?php else: ?>
            <div class="all-explored">
                <i class="fas fa-check-double" style="color:var(--gold);"></i>
                You have explored all available modules in our current catalog.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- CONFIRM SUBMISSION MODAL -->
<div id="confirmModal" class="modal">
    <div class="confirm-card">
        <div class="confirm-head">
            <span class="eyebrow">Ready to submit?</span>
            <h3>Brilliant choices, <?php echo $display_name; ?></h3>
        </div>
        <div class="confirm-body">
            <p id="confirmMsg"></p>
            <div class="confirm-actions">
                <button class="btn btn-outline" onclick="closeConfirm()">Cancel</button>
                <button class="btn btn-gold" id="sendToAdminBtn" onclick="submitToAdmin()">Send to Admin</button>
            </div>
        </div>
    </div>
</div>

<script>
    var STUDENT_NAME = <?php echo $js_name; ?>;
    var DRAFT_COUNT = <?php echo (int)$draft_count; ?>;
    var TOTAL_FEE = <?php echo json_encode(number_format($total_fee)); ?>;

    function openConfirm() {
        document.getElementById('confirmMsg').innerHTML =
            'You have <strong>' + DRAFT_COUNT + '</strong> module(s) ready for submission.<br>' +
            'Total Tuition Investment: <strong>' + TOTAL_FEE + ' Tsh</strong>.<br><br>' +
            'Send these to the Admin for final approval?';
        document.getElementById('confirmModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeConfirm() {
        document.getElementById('confirmModal').classList.remove('open');
        document.body.style.overflow = '';
    }

    function submitToAdmin() {
        var btn = document.getElementById('sendToAdminBtn');
        btn.disabled = true;
        btn.style.opacity = '0.75';
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending&hellip;';
        window.location.href = 'process_submission.php';
    }

    document.getElementById('confirmModal').addEventListener('click', function (e) {
        if (e.target === this) closeConfirm();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeConfirm();
    });
</script>

</body>
</html>
