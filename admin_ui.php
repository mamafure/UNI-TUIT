<?php
// Shared chrome for every admin page: same paper/ink/gold look as the student portal (home).
// Usage:
//   admin_page_start('Page title', 'students|pending|subjects', ['heading' => ..., 'sub' => ..., 'eyebrow' => ..., 'stats' => true|false, 'back' => [url, label]]);
//   ... page content (usually inside <div class="panel"> ... </div>) ...
//   admin_page_end();            // add admin_reject_modal() before it on pages with Reject buttons

if (!function_exists('flash_render')) { require __DIR__ . '/flash.php'; }

// Session-bound CSRF token for admin POST forms
function admin_csrf() {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
    return $_SESSION['csrf'];
}

function admin_h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function admin_page_start($title, $active, $o = []) {
    global $conn;

    $stat = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT
            (SELECT COUNT(DISTINCT user_id) FROM registrations) AS students,
            (SELECT COUNT(*) FROM registrations WHERE status = 'Pending') AS pending,
            (SELECT COUNT(*) FROM registrations WHERE status = 'Registered') AS registered,
            (SELECT COALESCE(SUM(fee), 0) FROM registrations WHERE status = 'Registered') AS revenue"));
    $pending_count = (int)$stat['pending'];

    $eyebrow = $o['eyebrow'] ?? 'Admin Console';
    $heading = $o['heading'] ?? $title;
    $sub     = $o['sub'] ?? '';
    $show_stats = $o['stats'] ?? true;
    $back    = $o['back'] ?? null;
    $admin_name = admin_h($_SESSION['username'] ?? 'Admin');

    $nav = [
        'students' => ['admin', 'fa-users', 'Students'],
        'pending'  => ['admin_pending', 'fa-hourglass-half', 'Pending'],
        'subjects' => ['admin_subjects', 'fa-book', 'Subjects'],
        'services' => ['services', 'fa-tools', 'Services'],
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo admin_h($title); ?> | UNI-TUIT Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #10254e; --ink-2: #16327a; --ink-soft: #52618a;
            --paper: #faf8f3; --paper-2: #f2ede0; --line: #e4dfd3;
            --gold: #c99a3b; --gold-light: #e6c878;
            --success: #1c7a4d; --success-bg: #e7f4ec;
            --danger: #a63a3a; --danger-bg: #fbecec;
            --shadow: 0 20px 45px -20px rgba(16, 37, 78, 0.35);
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--paper); color: var(--ink); margin: 0; -webkit-font-smoothing: antialiased; }
        h1, h2, h3 { font-family: 'Fraunces', serif; margin: 0; }
        a { color: inherit; }
        button { font-family: inherit; }
        :focus-visible { outline: 2px solid var(--gold); outline-offset: 3px; }
        .eyebrow { font-family: 'IBM Plex Mono', monospace; font-size: 11.5px; letter-spacing: 0.16em; text-transform: uppercase; font-weight: 600; }

        /* Buttons */
        .btn { border: none; cursor: pointer; border-radius: 999px; font-weight: 600; font-size: 13.5px; padding: 9px 18px; transition: transform .15s ease, box-shadow .15s ease, background .15s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; white-space: nowrap; }
        .btn-gold { background: var(--gold); color: var(--ink); }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-1px); box-shadow: 0 8px 18px -6px rgba(201,154,59,.6); }
        .btn-outline { background: transparent; color: var(--ink); border: 1.5px solid var(--line); }
        .btn-outline:hover { border-color: var(--ink); }
        .btn-approve { background: var(--success); color: #fff; }
        .btn-approve:hover { background: #166a42; transform: translateY(-1px); }
        .btn-reject { background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger); }
        .btn-reject:hover { background: #f6dcdc; }

        /* Header */
        .portal-header { background: var(--paper); padding: 14px 6%; display: flex; justify-content: space-between; align-items: center; gap: 16px; border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 100; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; }
        .brand-mark { width: 36px; height: 36px; border-radius: 50%; background: var(--ink); color: var(--gold-light); display: flex; align-items: center; justify-content: center; font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 13px; border: 1px solid var(--gold); flex-shrink: 0; }
        .brand-word { font-family: 'Fraunces', serif; font-size: 19px; font-weight: 700; color: var(--ink); }
        .brand-tag { font-family: 'IBM Plex Mono', monospace; font-size: 10px; letter-spacing: .12em; text-transform: uppercase; color: var(--gold); border: 1px solid var(--gold); border-radius: 999px; padding: 2px 8px; margin-left: 2px; }
        .admin-nav { display: flex; align-items: center; gap: 4px; overflow-x: auto; scrollbar-width: none; }
        .admin-nav::-webkit-scrollbar { display: none; }
        .admin-nav a { display: inline-flex; align-items: center; gap: 8px; padding: 9px 15px; border-radius: 999px; text-decoration: none; font-size: 14px; font-weight: 500; color: var(--ink-soft); white-space: nowrap; transition: .2s; }
        .admin-nav a:hover { color: var(--ink); background: var(--paper-2); }
        .admin-nav a.active { background: var(--ink); color: #fff; }
        .nav-badge { background: var(--gold); color: var(--ink); font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 600; padding: 1px 8px; border-radius: 999px; }
        .icon-btn { width: 40px; height: 40px; border-radius: 50%; border: 1.5px solid var(--line); background: var(--paper); display: flex; align-items: center; justify-content: center; color: var(--ink-soft); text-decoration: none; transition: .2s; flex-shrink: 0; }
        .icon-btn:hover { border-color: var(--ink); color: var(--ink); }
        .header-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .admin-chip { font-size: 13px; font-weight: 500; color: var(--ink-soft); }

        /* Hero band */
        .portal-hero { background: radial-gradient(circle at 85% 0%, rgba(201,154,59,.2), transparent 45%), linear-gradient(135deg, var(--ink) 0%, var(--ink-2) 100%); padding: 40px 6% 64px; }
        .portal-hero-inner { max-width: 1080px; margin: 0 auto; }
        .portal-hero .eyebrow { color: var(--gold-light); }
        .portal-hero h1 { color: #fff; font-size: 2rem; margin: 10px 0 8px; }
        .portal-hero p { color: rgba(255,255,255,.75); margin: 0; }
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: var(--gold-light); text-decoration: none; font-size: 13.5px; font-weight: 500; margin-bottom: 14px; }
        .back-link:hover { color: #fff; }
        .stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 16px; margin-top: 30px; }
        .stat-tile { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.14); border-radius: 14px; padding: 16px 18px; }
        .stat-tile strong { display: block; font-family: 'Fraunces', serif; font-size: 1.5rem; color: #fff; }
        .stat-tile span { font-family: 'IBM Plex Mono', monospace; font-size: 10.5px; letter-spacing: .08em; text-transform: uppercase; color: rgba(255,255,255,.55); }
        .stat-tile.attention { border-color: var(--gold); }
        .stat-tile.attention strong { color: var(--gold-light); }

        /* Main + panels */
        .portal-main { max-width: 1080px; margin: -36px auto 60px; padding: 0 6%; }
        .panel { background: #fff; border: 1px solid var(--line); border-radius: 20px; padding: 30px; box-shadow: var(--shadow); }
        .panel + .panel { margin-top: 24px; }
        .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
        .panel-head .eyebrow { color: var(--gold); }
        .panel-head h2 { font-size: 1.35rem; margin-top: 4px; }
        .search { position: relative; }
        .search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--ink-soft); font-size: 13px; }
        .search input { font: inherit; font-size: 14px; padding: 10px 16px 10px 36px; border: 1.5px solid var(--line); border-radius: 999px; background: var(--paper); color: var(--ink); width: 260px; max-width: 100%; }
        .search input:focus { outline: none; border-color: var(--gold); background: #fff; }

        /* Tables */
        .table-wrap { overflow-x: auto; margin: 0 -8px; padding: 0 8px; }
        table { width: 100%; border-collapse: collapse; min-width: 560px; }
        th { text-align: left; padding: 12px 14px; font-family: 'IBM Plex Mono', monospace; font-size: 10.5px; letter-spacing: .1em; text-transform: uppercase; color: var(--ink-soft); font-weight: 600; border-bottom: 1.5px solid var(--line); white-space: nowrap; }
        td { padding: 16px 14px; border-bottom: 1px solid var(--line); font-size: 14.5px; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background .15s; }
        tbody tr:hover { background: var(--paper); }
        tr.clickable { cursor: pointer; }
        td small, .muted { color: var(--ink-soft); font-size: 12.5px; }
        .person { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: var(--paper-2); color: var(--ink); border: 1px solid var(--gold); display: flex; align-items: center; justify-content: center; font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 13px; flex-shrink: 0; }
        .person strong { font-weight: 600; }
        .mono { font-family: 'IBM Plex Mono', monospace; font-size: 13px; white-space: nowrap; }
        .pill-group { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .action-group { display: flex; gap: 8px; align-items: center; flex-wrap: nowrap; }

        /* Status badges (same as student portal) */
        .badge { font-family: 'IBM Plex Mono', monospace; padding: 5px 12px; border-radius: 999px; font-size: 10.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; display: inline-block; }
        .badge-draft { background: #eef1f6; color: #475569; }
        .badge-pending { background: #faf1dd; color: #93701e; border: 1px solid var(--gold); }
        .badge-registered { background: var(--success-bg); color: var(--success); border: 1px solid var(--success); }
        .badge-rejected { background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger); }
        .count-pill { font-family: 'IBM Plex Mono', monospace; background: var(--paper-2); border: 1px solid var(--line); color: var(--ink); padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; white-space: nowrap; }

        /* Empty state */
        .empty-state { text-align: center; padding: 56px 10px; }
        .empty-state i { font-size: 44px; color: var(--line); margin-bottom: 14px; display: block; }
        .empty-state p { color: var(--ink-soft); margin: 0; }

        /* Profile card (student details) */
        .profile { display: flex; align-items: center; gap: 22px; flex-wrap: wrap; }
        .profile .avatar { width: 68px; height: 68px; font-size: 22px; }
        .profile h2 { font-size: 1.5rem; }
        .profile-meta { display: flex; flex-wrap: wrap; gap: 8px 22px; margin-top: 8px; color: var(--ink-soft); font-size: 14px; }
        .profile-meta i { color: var(--gold); margin-right: 6px; }

        /* Subject cards */
        .subject-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
        .subject-card { background: #fff; border: 1px solid var(--line); border-top: 3px solid var(--gold); border-radius: 18px; padding: 26px 24px; box-shadow: var(--shadow); display: flex; flex-direction: column; gap: 4px; transition: transform .2s; }
        .subject-card:hover { transform: translateY(-3px); }
        .subject-card .ico { width: 44px; height: 44px; border-radius: 12px; background: var(--paper-2); color: var(--gold); display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 10px; }
        .subject-card h3 { font-size: 1.15rem; }
        .subject-card .num { font-family: 'Fraunces', serif; font-size: 2.4rem; font-weight: 700; margin-top: 10px; line-height: 1; }
        .subject-card .lbl { font-family: 'IBM Plex Mono', monospace; font-size: 10.5px; letter-spacing: .08em; text-transform: uppercase; color: var(--ink-soft); margin-bottom: 18px; }
        .subject-card .btn { align-self: flex-start; margin-top: auto; }


        /* Slide-over panel (shared) */
        .drawer-backdrop { position: fixed; inset: 0; z-index: 1500; background: rgba(16,37,78,.35); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); opacity: 0; visibility: hidden; transition: opacity .25s ease, visibility .25s; }
        .drawer-backdrop.open { opacity: 1; visibility: visible; }
        .drawer { position: fixed; top: 0; right: 0; bottom: 0; z-index: 1501; width: 440px; max-width: 100%; background: var(--paper); box-shadow: -30px 0 60px -20px rgba(10,20,45,.45); transform: translateX(105%); transition: transform .3s cubic-bezier(.22,.8,.3,1); display: flex; flex-direction: column; }
        .drawer.open { transform: none; }
        .drawer-head { background: linear-gradient(135deg, var(--ink), var(--ink-2)); color: #fff; padding: 14px 64px 14px 22px; position: relative; display: flex; align-items: center; gap: 12px; min-height: 68px; }
        .drawer-close { position: absolute; top: 50%; transform: translateY(-50%); right: 16px; width: 34px; height: 34px; border-radius: 50%; border: 1.5px solid rgba(255,255,255,.35); background: rgba(255,255,255,.08); color: #fff; cursor: pointer; font-size: 16px; }
        .drawer-close:hover { background: rgba(255,255,255,.18); border-color: #fff; }
        .drawer-body { padding: 20px 22px 24px; overflow-y: auto; flex: 1; }
        .drawer-foot { padding: 16px 28px; border-top: 1px solid var(--line); background: #fff; }
        .drawer-foot .btn { width: 100%; justify-content: center; }
        .no-scroll { overflow: hidden; }
        @media (prefers-reduced-motion: reduce) { .drawer, .drawer-backdrop { transition: none; } }
        .drawer.wide { width: 520px; }
        .drawer-head .eyebrow { font-size: 10px; display: block; margin-bottom: 2px; }
        .drawer-head .who { min-width: 0; }
        .drawer-head h2 { color: #fff; font-size: 1.1rem; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .drawer-body form { display: block; }
        .drawer-foot.split { display: flex; gap: 10px; }
        .drawer-foot.split .btn { flex: 1; justify-content: center; padding: 12px 18px; }

        /* Form controls */
        .f-row { margin-bottom: 16px; }
        .f-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .f-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .f-label small { font-weight: 400; color: var(--ink-soft); }
        .f-input { width: 100%; font: inherit; font-size: 14.5px; padding: 11px 14px; border: 1.5px solid var(--line); border-radius: 12px; background: #fff; color: var(--ink); transition: border-color .2s; }
        .f-input:focus { outline: none; border-color: var(--gold); }
        textarea.f-input { resize: vertical; min-height: 92px; line-height: 1.5; }
        .f-hint { font-size: 12px; color: var(--ink-soft); margin-top: 5px; }
        .icon-grid { display: grid; grid-template-columns: repeat(8, 1fr); gap: 6px; }
        .icon-grid label, .swatches label { cursor: pointer; position: relative; }
        .icon-grid input, .swatches input { position: absolute; opacity: 0; inset: 0; }
        .icon-grid span { display: flex; align-items: center; justify-content: center; height: 38px; border-radius: 10px; border: 1.5px solid var(--line); background: #fff; color: var(--ink-soft); font-size: 15px; transition: .15s; }
        .icon-grid input:checked + span { border-color: var(--gold); background: var(--paper-2); color: var(--ink); }
        .icon-grid input:focus-visible + span, .swatches input:focus-visible + span { outline: 2px solid var(--gold); outline-offset: 2px; }
        .swatches { display: flex; gap: 10px; flex-wrap: wrap; }
        .swatches span { display: block; width: 30px; height: 30px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 1.5px var(--line); transition: .15s; }
        .swatches input:checked + span { box-shadow: 0 0 0 2.5px var(--ink); }
        .switch-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; background: #fff; border: 1.5px solid var(--line); border-radius: 12px; padding: 12px 14px; }
        .switch-row strong { display: block; font-size: 14px; }
        .switch-row small { color: var(--ink-soft); font-size: 12.5px; }
        .switch { position: relative; width: 46px; height: 26px; flex-shrink: 0; }
        .switch input { position: absolute; opacity: 0; inset: 0; cursor: pointer; z-index: 1; }
        .switch i { position: absolute; inset: 0; border-radius: 999px; background: var(--line); transition: .2s; }
        .switch i::after { content: ""; position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; border-radius: 50%; background: #fff; transition: .2s; box-shadow: 0 1px 3px rgba(0,0,0,.25); }
        .switch input:checked + i { background: var(--success); }
        .switch input:checked + i::after { transform: translateX(20px); }
        .switch input:focus-visible + i { outline: 2px solid var(--gold); outline-offset: 2px; }
        .preview { display: flex; gap: 14px; align-items: center; padding: 14px; border-radius: 14px; background: #fff; border: 1px solid var(--line); border-left: 4px solid var(--pv, #2563eb); margin-bottom: 20px; }
        .preview .ico { width: 44px; height: 44px; border-radius: 12px; background: var(--pv, #2563eb); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .preview .t { font-family: 'IBM Plex Mono', monospace; font-size: 10.5px; letter-spacing: .1em; color: var(--pv, #2563eb); text-transform: uppercase; font-weight: 600; }
        .preview h4 { font-family: 'Fraunces', serif; font-size: 1.05rem; margin: 2px 0; }
        .preview .f { font-family: 'IBM Plex Mono', monospace; font-size: 11.5px; color: var(--ink-soft); }
        .danger-zone { margin-top: 26px; padding-top: 20px; border-top: 1px dashed var(--line); }
        .danger-zone .eyebrow { color: var(--danger); font-size: 10.5px; }
        .danger-zone .row { display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap; }
        .danger-zone p { font-size: 12.5px; color: var(--ink-soft); margin: 8px 0 0; }
        .status-pill { font-family: 'IBM Plex Mono', monospace; font-size: 10px; letter-spacing: .06em; text-transform: uppercase; font-weight: 600; padding: 3px 10px; border-radius: 999px; }
        .status-pill.on { background: var(--success-bg); color: var(--success); border: 1px solid var(--success); }
        .status-pill.off { background: #eef1f6; color: #475569; border: 1px solid #cbd5e1; }
        @media (max-width: 560px) { .f-row2 { grid-template-columns: 1fr; } .icon-grid { grid-template-columns: repeat(6, 1fr); } }

        /* Reject confirm modal */
        .modal { display: none; position: fixed; inset: 0; z-index: 2000; background: rgba(10,20,45,.65); align-items: center; justify-content: center; padding: 24px; }
        .modal.open { display: flex; }
        .confirm-card { width: 100%; max-width: 420px; background: var(--paper); border-radius: 18px; box-shadow: 0 40px 80px -20px rgba(0,0,0,.5); overflow: hidden; }
        .confirm-head { background: linear-gradient(135deg, var(--ink), var(--ink-2)); color: #fff; padding: 24px 28px; }
        .confirm-head .eyebrow { color: var(--gold-light); }
        .confirm-head h3 { font-size: 1.25rem; margin: 8px 0 0; }
        .confirm-body { padding: 22px 28px 28px; }
        .confirm-body p { color: var(--ink-soft); line-height: 1.6; margin: 0 0 22px; }
        .confirm-actions { display: flex; gap: 10px; }
        .confirm-actions .btn { flex: 1; justify-content: center; padding: 12px 18px; }

        /* Flash toast styles come from flash */

        @media (max-width: 820px) {
            .portal-header { flex-wrap: wrap; padding: 12px 5%; }
            .admin-nav { order: 3; width: 100%; }
            .admin-chip { display: none; }
        }
        @media (max-width: 720px) {
            .portal-hero { padding: 32px 5% 56px; }
            .portal-hero h1 { font-size: 1.6rem; }
            .stat-row { grid-template-columns: repeat(2, 1fr); }
            .portal-main { padding: 0 5%; margin-top: -28px; }
            .panel { padding: 22px 18px; }
            .search, .search input { width: 100%; }
        }
    </style>
</head>
<body>

<?php flash_render(); ?>

<header class="portal-header">
    <a class="brand" href="admin">
        <span class="brand-mark">UT</span>
        <span class="brand-word">UNI·TUIT</span>
        <span class="brand-tag">Admin</span>
    </a>
    <nav class="admin-nav" aria-label="Admin navigation">
        <?php foreach ($nav as $key => [$href, $icon, $label]): ?>
            <a href="<?php echo $href; ?>" class="<?php echo $key === $active ? 'active' : ''; ?>"<?php echo $key === $active ? ' aria-current="page"' : ''; ?>>
                <i class="fas <?php echo $icon; ?>"></i> <?php echo $label; ?>
                <?php if ($key === 'pending' && $pending_count > 0): ?><span class="nav-badge"><?php echo $pending_count; ?></span><?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="header-right">
        <span class="admin-chip"><?php echo $admin_name; ?></span>
        <a class="icon-btn" href="logout" title="Log out" aria-label="Log out"><i class="fas fa-right-from-bracket"></i></a>
    </div>
</header>

<section class="portal-hero">
    <div class="portal-hero-inner">
        <?php if ($back): ?>
            <a class="back-link" href="<?php echo admin_h($back[0]); ?>"><i class="fas fa-arrow-left"></i> <?php echo admin_h($back[1]); ?></a><br>
        <?php endif; ?>
        <span class="eyebrow"><?php echo admin_h($eyebrow); ?></span>
        <h1><?php echo admin_h($heading); ?></h1>
        <?php if ($sub !== ''): ?><p><?php echo admin_h($sub); ?></p><?php endif; ?>

        <?php if ($show_stats): ?>
        <div class="stat-row">
            <div class="stat-tile"><strong><?php echo (int)$stat['students']; ?></strong><span>Students</span></div>
            <div class="stat-tile<?php echo $pending_count > 0 ? ' attention' : ''; ?>"><strong><?php echo $pending_count; ?></strong><span>Pending</span></div>
            <div class="stat-tile"><strong><?php echo (int)$stat['registered']; ?></strong><span>Registered</span></div>
            <div class="stat-tile"><strong><?php echo number_format((float)$stat['revenue']); ?></strong><span>Confirmed Tsh</span></div>
        </div>
        <?php endif; ?>
    </div>
</section>

<main class="portal-main">
<?php
}

function admin_page_end() {
    ?>
</main>
</body>
</html>
<?php
}

// Shared "Reject this registration?" dialog. Buttons call: return confirmReject(this, 'Module', 'Student name (optional)')
function admin_reject_modal() {
    ?>
<div id="rejectConfirm" class="modal" role="dialog" aria-modal="true" aria-labelledby="rejectTitle">
    <div class="confirm-card">
        <div class="confirm-head">
            <span class="eyebrow">Confirm action</span>
            <h3 id="rejectTitle">Reject this registration?</h3>
        </div>
        <div class="confirm-body">
            <p id="rejectConfirmText"></p>
            <div class="confirm-actions">
                <button type="button" class="btn btn-outline" onclick="closeRejectConfirm()">Cancel</button>
                <a id="rejectConfirmLink" href="#" class="btn btn-reject">Yes, Reject</a>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmReject(link, moduleName, studentName) {
        document.getElementById('rejectConfirmText').textContent = studentName
            ? 'This will mark "' + moduleName + '" as rejected for ' + studentName + '. The student will see this on their dashboard.'
            : 'This will mark "' + moduleName + '" as rejected. The student will see this on their dashboard.';
        document.getElementById('rejectConfirmLink').setAttribute('href', link.getAttribute('href'));
        document.getElementById('rejectConfirm').classList.add('open');
        return false;
    }
    function closeRejectConfirm() { document.getElementById('rejectConfirm').classList.remove('open'); }
    document.getElementById('rejectConfirm').addEventListener('click', function (e) { if (e.target === this) closeRejectConfirm(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeRejectConfirm(); });
</script>
<?php
}
