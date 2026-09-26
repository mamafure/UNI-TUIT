<?php
include 'db.php';
include 'flash.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$m_name = $_GET['name'];
$u_id = $_SESSION['user_id'];

// 1. DATA ARRAY: Information for each specific subject
$subject_data = [
    "Database System" => [
        "intro" => "Master the art of data management. In today's world, data is the new oil, and knowing how to store, retrieve, and secure it is a vital skill for any IT professional.",
        "topics" => ["Entity Relationship Diagrams (ERD)", "Advanced SQL Queries", "Database Normalization (1NF, 2NF, 3NF)", "Transaction Management & Concurrency Control"],
        "icon" => "fa-database",
        "color" => "#2563eb",
       "watermark" => "db.jpg" // Database icon

    ],
    "OS" => [
        "intro" => "Go behind the scenes of computing. Learn how Operating Systems manage hardware resources and provide a platform for application software to run smoothly.",
        "topics" => ["Process Scheduling & Threads", "Memory Management & Virtual Memory", "Deadlock Detection and Prevention", "File Systems and Disk Management"],
        "icon" => "fa-microchip",
        "color" => "#dc2626",
        "watermark" => "os.jpg"
    ],
    "Networking" => [
        "intro" => "The world is connected! Understand the protocols and technologies that allow computers to communicate across the globe, from local cables to the vast internet.",
        "topics" => ["OSI and TCP/IP Models", "IP Addressing and Subnetting", "Routing and Switching Protocols", "Network Security and Firewalls"],
        "icon" => "fa-network-wired",
        "color" => "#059669",
        "watermark" => "network.jpg"
    ],
    "Web Programming" => [
        "intro" => "Build the modern web. From beautiful user interfaces to powerful server-side logic, this module prepares you to create full-stack web applications.",
        "topics" => ["Responsive Design with HTML5 & CSS3", "JavaScript & DOM Manipulation", "PHP Backend & MySQL Integration", "Web Security Best Practices"],
        "icon" => "fa-code",
        "color" => "#7c3aed",
        "watermark" => "web.jpg"
    ],
    "Function of Single Varriable" => [
        "intro" => "The mathematics of change. Calculus is essential for engineering, physics, and computer science. We make complex derivatives and integrals easy to understand.",
        "topics" => ["Limits and Continuity", "Rules of Differentiation", "Applications of Integrals", "Infinite Sequences and Series"],
        "icon" => "fa-square-root-variable",
        "color" => "#ea580c",
        "watermark" => "function.jpg"
    ]
];

// Fallback if the subject name doesn't exist in our array
if (!array_key_exists($m_name, $subject_data)) {
    die("Subject not found.");
}

$info = $subject_data[$m_name];

// 2. HANDLE SUBMISSION
if (isset($_POST['add'])) {
    // Check if user already registered for this specific module
    $check = mysqli_query($conn, "SELECT * FROM registrations WHERE user_id='$u_id' AND module_name='$m_name'");
    if (mysqli_num_rows($check) > 0) {
        flash_set('error', 'You have already added this subject to your list.');
    } else {
        mysqli_query($conn, "INSERT INTO registrations (user_id, module_name,fee,status) VALUES ('$u_id', '$m_name',5000,'Draft')");
        flash_set('success', $m_name . ' has been added to your tuition list.');
    }
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($m_name, ENT_QUOTES, 'UTF-8'); ?> | UNI-TUIT Details</title>
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
            --radius: 16px;
            --shadow: 0 20px 45px -20px rgba(16, 37, 78, 0.35);
            --subject: <?php echo $info['color']; ?>;
        }

        * { box-sizing: border-box; }

        body { font-family: 'Inter', sans-serif; background: var(--paper); color: var(--ink); margin: 0; -webkit-font-smoothing: antialiased; position: relative; }
        h1, h2, h3 { font-family: 'Fraunces', serif; margin: 0; }
        a { color: inherit; }
        button { font-family: inherit; }
        :focus-visible { outline: 2px solid var(--gold); outline-offset: 3px; }

        .eyebrow { font-family: 'IBM Plex Mono', monospace; font-size: 11.5px; letter-spacing: 0.16em; text-transform: uppercase; font-weight: 600; }

        .btn { border: none; cursor: pointer; border-radius: 999px; font-weight: 600; font-size: 14.5px; padding: 11px 24px; transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-gold { background: var(--gold); color: var(--ink); }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-1px); box-shadow: 0 8px 18px -6px rgba(201, 154, 59, 0.6); }
        .btn-outline { background: transparent; color: var(--ink); border: 1.5px solid var(--line); }
        .btn-outline:hover { border-color: var(--ink); }
        .btn-block { width: 100%; justify-content: center; padding: 17px 24px; font-size: 16px; }

        /* Watermark */
        body::before {
            content: "";
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 500px; height: 500px;
            background-image: url('<?php echo htmlspecialchars($info['watermark'], ENT_QUOTES, 'UTF-8'); ?>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.035;
            z-index: 0;
            pointer-events: none;
            filter: grayscale(100%);
        }

        /* ---------- Portal header ---------- */
        .portal-header { background: var(--paper); padding: 16px 6%; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--line); position: relative; z-index: 2; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand-mark { width: 36px; height: 36px; border-radius: 50%; background: var(--ink); color: var(--gold-light); display: flex; align-items: center; justify-content: center; font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 13px; border: 1px solid var(--gold); flex-shrink: 0; }
        .brand-word { font-family: 'Fraunces', serif; font-size: 19px; font-weight: 700; color: var(--ink); }

        /* ---------- Hero (slim title strip) ---------- */
        .subject-hero { background: radial-gradient(circle at 85% 0%, rgba(201,154,59,0.2), transparent 45%), linear-gradient(135deg, var(--ink) 0%, var(--ink-2) 100%); padding: 30px 6% 58px; position: relative; z-index: 1; }
        .subject-hero-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; gap: 20px; }
        .subject-badge { width: 58px; height: 58px; flex-shrink: 0; border-radius: 50%; background: color-mix(in srgb, var(--subject) 22%, transparent); border: 1.5px solid var(--subject); display: flex; align-items: center; justify-content: center; }
        .subject-badge i { font-size: 22px; color: var(--subject); }
        .subject-hero .eyebrow { color: var(--gold-light); display: block; margin-bottom: 4px; }
        .subject-hero h1 { color: #fff; font-size: 1.7rem; }

        /* ---------- Content grid ---------- */
        .detail-grid { max-width: 1100px; margin: -30px auto 60px; padding: 0 6%; position: relative; z-index: 2; display: grid; grid-template-columns: 1.7fr 1fr; gap: 24px; align-items: start; }
        .content-card, .sidebar-card { background: #fff; border: 1px solid var(--line); border-radius: 20px; box-shadow: var(--shadow); padding: 32px; }
        .sidebar-card { position: sticky; top: 24px; }

        .intro-text { font-size: 15.5px; line-height: 1.7; color: var(--ink-soft); margin: 10px 0 26px; border-left: 3px solid var(--subject); padding-left: 18px; }

        .learning-box { background: var(--paper-2); padding: 22px; border-radius: 14px; }
        .learning-box h3 { font-size: 1.02rem; margin-bottom: 12px; }
        .learning-box ul { list-style: none; padding: 0; margin: 0; }
        .learning-box li { padding: 10px 0; border-bottom: 1px solid var(--line); display: flex; align-items: flex-start; gap: 12px; font-size: 14px; }
        .learning-box li:last-child { border-bottom: none; }
        .learning-box li i { color: var(--subject); margin-top: 2px; }

        .sidebar-card .eyebrow { color: var(--ink-soft); }
        .fee-section { text-align: center; padding: 20px; border: 1.5px dashed var(--subject); border-radius: 14px; margin: 12px 0 20px; }
        .fee-amount { font-family: 'Fraunces', serif; font-size: 1.7rem; font-weight: 700; color: var(--subject); margin: 6px 0; }
        .fee-note { font-size: 12px; color: var(--ink-soft); margin: 0; }

        footer { text-align: center; padding: 26px; color: var(--ink-soft); font-size: 13.5px; position: relative; z-index: 1; }

        @media (max-width: 900px) {
            .detail-grid { grid-template-columns: 1fr; }
            .sidebar-card { position: static; }
        }

        @media (max-width: 480px) {
            .portal-header { padding: 14px 5%; }
            .subject-hero { padding: 26px 5% 48px; }
            .subject-hero-inner { gap: 14px; }
            .subject-badge { width: 48px; height: 48px; }
            .subject-badge i { font-size: 18px; }
            .subject-hero h1 { font-size: 1.35rem; }
            .detail-grid { padding: 0 5%; margin-top: -24px; }
            .content-card, .sidebar-card { padding: 24px 20px; }
        }
    </style>
</head>
<body>

<header class="portal-header">
    <a class="brand" href="home.php">
        <span class="brand-mark">UT</span>
        <span class="brand-word">UNI&middot;TUIT</span>
    </a>
    <a href="home.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to My List</a>
</header>

<section class="subject-hero">
    <div class="subject-hero-inner">
        <div class="subject-badge"><i class="fas <?php echo $info['icon']; ?>"></i></div>
        <div>
            <span class="eyebrow">Module Profile</span>
            <h1><?php echo htmlspecialchars($m_name, ENT_QUOTES, 'UTF-8'); ?></h1>
        </div>
    </div>
</section>

<div class="detail-grid">
    <div class="content-card">
        <span class="eyebrow" style="color:var(--subject);">General Overview</span>
        <p class="intro-text"><?php echo htmlspecialchars($info['intro'], ENT_QUOTES, 'UTF-8'); ?></p>

        <div class="learning-box">
            <h3>What you will learn</h3>
            <ul>
                <?php foreach($info['topics'] as $topic): ?>
                    <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($topic, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="sidebar-card">
        <span class="eyebrow">Tuition Registration Fee</span>
        <div class="fee-section">
            <div class="fee-amount">5,000 Tsh</div>
            <p class="fee-note">Payable via M-Pesa / TigoPesa after confirmation</p>
        </div>

        <form method="POST" id="addSubjectForm">
            <button type="submit" name="add" class="btn btn-gold btn-block" id="addSubjectBtn">Select This Subject <i class="fas fa-plus-circle"></i></button>
        </form>
    </div>
</div>

<footer>UNI-TUIT Platform &copy; 2026 &mdash; Quality University Tuition</footer>

<script>
    document.getElementById('addSubjectForm').addEventListener('submit', function () {
        var btn = document.getElementById('addSubjectBtn');
        btn.disabled = true;
        btn.style.opacity = '0.75';
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding&hellip;';
    });
</script>

</body>
</html>
