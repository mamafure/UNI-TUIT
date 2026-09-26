<?php
include 'db.php';
// Protect the page - only logged in users
if(!isset($_SESSION['user_id'])) { header("Location: /"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Explore Modules | UNI-TUIT</title>
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
        .btn-outline { background: transparent; color: var(--ink); border: 1.5px solid var(--line); }
        .btn-outline:hover { border-color: var(--ink); }

        /* ---------- Portal header ---------- */
        .portal-header { background: var(--paper); padding: 16px 6%; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 100; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand-mark { width: 36px; height: 36px; border-radius: 50%; background: var(--ink); color: var(--gold-light); display: flex; align-items: center; justify-content: center; font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 13px; border: 1px solid var(--gold); flex-shrink: 0; }
        .brand-word { font-family: 'Fraunces', serif; font-size: 19px; font-weight: 700; color: var(--ink); }

        /* ---------- Hero band ---------- */
        .portal-hero { background: radial-gradient(circle at 85% 0%, rgba(201,154,59,0.2), transparent 45%), linear-gradient(135deg, var(--ink) 0%, var(--ink-2) 100%); padding: 44px 6% 56px; }
        .portal-hero-inner { max-width: 1100px; margin: 0 auto; }
        .portal-hero .eyebrow { color: var(--gold-light); }
        .portal-hero h1 { color: #fff; font-size: 2rem; margin: 10px 0 8px; }
        .portal-hero p { color: rgba(255,255,255,0.75); margin: 0; max-width: 480px; }

        /* ---------- Grid ---------- */
        .catalogue { max-width: 1100px; margin: 0 auto; padding: 50px 6% 70px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; }

        .card { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card:hover { transform: translateY(-6px); box-shadow: var(--shadow); }
        .card img { width: 100%; height: 160px; object-fit: cover; }
        .card-body { padding: 22px; }
        .card-tag { font-family: 'IBM Plex Mono', monospace; font-size: 11px; letter-spacing: 0.08em; }
        .card-body h3 { font-size: 1.15rem; margin: 6px 0 10px; }
        .card-fee { color: var(--ink-soft); font-size: 13.5px; margin: 0 0 18px; }
        .card-fee strong { color: var(--ink); font-family: 'IBM Plex Mono', monospace; font-weight: 600; }
        .btn-view { width: 100%; justify-content: center; }

        @media (max-width: 480px) {
            .portal-header { padding: 14px 5%; }
            .portal-hero { padding: 36px 5% 46px; }
            .portal-hero h1 { font-size: 1.6rem; }
            .catalogue { padding: 36px 5% 50px; }
        }
    </style>
</head>
<body>

<header class="portal-header">
    <a class="brand" href="home">
        <span class="brand-mark">UT</span>
        <span class="brand-word">UNI&middot;TUIT</span>
    </a>
    <a href="home" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to My List</a>
</header>

<section class="portal-hero">
    <div class="portal-hero-inner">
        <span class="eyebrow">Course Catalogue</span>
        <h1>Explore Modules</h1>
        <p>Pick a module to see the full syllabus, then add it to your tuition list.</p>
    </div>
</section>

<div class="catalogue">
    <div class="grid">
        <?php
        // Define our modules with images
        $modules = [
            ["name" => "Database System", "img" => "db.jpg", "tag" => "MOD-01", "color" => "#2563eb"],
            ["name" => "Function of Single Varriable", "img" => "function.jpg", "tag" => "MOD-02", "color" => "#ea580c"]
        ];

        foreach ($modules as $m) {
            $name = htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8');
            echo "
            <div class='card'>
                <img src='{$m['img']}' alt='{$name}'>
                <div class='card-body'>
                    <span class='card-tag' style='color:{$m['color']}'>{$m['tag']}</span>
                    <h3>{$name}</h3>
                    <p class='card-fee'>Registration fee <strong>15,000 Tsh</strong></p>
                    <a href='subject_view?name=" . urlencode($m['name']) . "' class='btn btn-gold btn-view'>View Details <i class='fas fa-arrow-right'></i></a>
                </div>
            </div>";
        }
        ?>
    </div>
</div>

</body>
</html>
