<?php include 'db.php'; include 'flash.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UNI-TUIT | Quality Tuition for University Success</title>
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

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background: var(--paper);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, .brand-word { font-family: 'Fraunces', serif; }

        .eyebrow {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 600;
        }

        a { color: inherit; }

        img { max-width: 100%; display: block; }

        button { font-family: inherit; }

        :focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: 3px;
        }

        /* ---------- Header ---------- */
        .site-header {
            background: var(--paper);
            padding: 16px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1200;
            border-bottom: 1px solid var(--line);
        }

        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }

        .brand-mark {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: var(--ink);
            color: var(--gold-light);
            display: flex; align-items: center; justify-content: center;
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid var(--gold);
            flex-shrink: 0;
        }

        .brand-word { font-size: 20px; font-weight: 700; color: var(--ink); letter-spacing: 0.01em; }
        .brand-word em { color: var(--gold); font-style: normal; }

        .nav-desktop { display: flex; align-items: center; gap: 28px; }
        .nav-desktop a { text-decoration: none; font-size: 14.5px; font-weight: 500; color: var(--ink-soft); transition: color 0.2s; }
        .nav-desktop a:hover { color: var(--ink); }

        .nav-actions { display: flex; align-items: center; gap: 12px; }

        .btn { border: none; cursor: pointer; border-radius: 999px; font-weight: 600; font-size: 14.5px; padding: 10px 22px; transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease; }
        .btn-ghost { background: transparent; color: var(--ink); border: 1.5px solid var(--line); }
        .btn-ghost:hover { border-color: var(--ink); }
        .btn-gold { background: var(--gold); color: var(--ink); }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-1px); box-shadow: 0 8px 18px -6px rgba(201, 154, 59, 0.6); }
        .btn-block { width: 100%; padding: 13px 22px; font-size: 15px; }

        .hamburger {
            display: none;
            width: 42px; height: 42px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--paper);
            cursor: pointer;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .hamburger span { width: 20px; height: 2px; background: var(--ink); transition: 0.25s; border-radius: 2px; }
        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* Mobile nav panel */
        .mobile-nav {
            position: fixed; inset: 0; z-index: 1300;
            display: none;
        }
        .mobile-nav.open { display: block; }
        .mobile-nav-backdrop { position: absolute; inset: 0; background: rgba(16, 37, 78, 0.5); }
        .mobile-nav-panel {
            position: absolute; top: 0; right: 0; height: 100%; width: min(320px, 85vw);
            background: var(--paper); box-shadow: -20px 0 40px rgba(0,0,0,0.2);
            padding: 24px; display: flex; flex-direction: column; gap: 6px;
            transform: translateX(100%); transition: transform 0.25s ease;
        }
        .mobile-nav.open .mobile-nav-panel { transform: translateX(0); }
        .mobile-nav-panel a { padding: 14px 6px; text-decoration: none; font-weight: 600; color: var(--ink); border-bottom: 1px solid var(--line); }
        .mobile-nav-actions { margin-top: 16px; display: flex; flex-direction: column; gap: 10px; }
        .mobile-nav-close { align-self: flex-end; background: none; border: none; font-size: 22px; cursor: pointer; color: var(--ink-soft); }

        /* ---------- Hero ---------- */
        .hero {
            background: radial-gradient(circle at 15% 20%, rgba(201,154,59,0.18), transparent 45%), linear-gradient(135deg, var(--ink) 0%, var(--ink-2) 100%);
            padding: 72px 6% 90px;
            overflow: hidden;
        }
        .hero-container { max-width: 1180px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 48px; }
        .hero-text { flex: 1.1; }
        .hero .eyebrow { color: var(--gold-light); display: inline-flex; align-items: center; gap: 8px; margin-bottom: 18px; }
        .hero .eyebrow::before { content: ''; width: 6px; height: 6px; background: var(--gold); border-radius: 50%; display: inline-block; }

        .hero-title { font-size: 3rem; color: #fff; font-weight: 700; line-height: 1.12; margin: 0 0 20px; }
        .hero-title span { color: var(--gold-light); font-style: italic; }
        .hero-subtitle { font-size: 1.08rem; color: rgba(255,255,255,0.78); max-width: 480px; margin: 0 0 34px; line-height: 1.6; }
        .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; }
        .hero-btn-outline { padding: 13px 26px; border: 1.5px solid rgba(255,255,255,0.4); color: #fff; text-decoration: none; font-weight: 600; border-radius: 999px; transition: 0.2s; font-size: 14.5px; }
        .hero-btn-outline:hover { border-color: #fff; background: rgba(255,255,255,0.08); }

        .hero-stats { display: flex; gap: 28px; margin-top: 42px; }
        .hero-stat strong { display: block; font-family: 'Fraunces', serif; font-size: 1.6rem; color: #fff; }
        .hero-stat span { font-size: 12.5px; color: rgba(255,255,255,0.6); }

        .hero-visual { flex: 1; display: flex; justify-content: center; position: relative; }
        .hero-visual-frame { position: relative; }
        .hero-visual img { width: 100%; max-width: 420px; filter: drop-shadow(0 25px 40px rgba(0,0,0,0.45)); animation: floating 5s ease-in-out infinite; border-radius: 18px; }
        .image-glow { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 320px; height: 320px; background: var(--gold); filter: blur(140px); opacity: 0.28; z-index: -1; }

        @keyframes floating { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-16px); } }
        @media (prefers-reduced-motion: reduce) { .hero-visual img { animation: none; } }

        /* ---------- Sections ---------- */
        .section { padding: 80px 6%; max-width: 1180px; margin: 0 auto; }
        .section-head { text-align: center; margin-bottom: 44px; }
        .section-head .eyebrow { color: var(--gold); display: block; margin-bottom: 10px; }
        .section-head h2 { font-size: 2.1rem; margin: 0; color: var(--ink); }
        .section-head p { color: var(--ink-soft); max-width: 520px; margin: 12px auto 0; }

        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; }
        .about-card { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 28px; box-shadow: var(--shadow); }
        .about-card h3 { margin-top: 0; font-size: 1.1rem; }
        .about-card p { color: var(--ink-soft); line-height: 1.6; margin-bottom: 0; }

        .module-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 22px; }
        .card {
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden;
            cursor: pointer; text-align: left; transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
        .card img { width: 100%; height: 150px; object-fit: cover; }
        .card-body { padding: 16px 18px; }
        .card-tag { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: var(--gold); letter-spacing: 0.08em; }
        .card h3 { margin: 6px 0 0; font-size: 1.02rem; color: var(--ink); }

        .services-band { background: var(--ink); color: white; text-align: center; padding: 70px 6%; }
        .services-band h2 { font-size: 2.2rem; margin-bottom: 12px; }
        .services-band p { opacity: 0.85; margin-bottom: 30px; }
        .services-band .btn-gold { text-decoration: none; display: inline-flex; align-items: center; gap: 10px; }

        footer { background: #0c1c3e; color: rgba(255,255,255,0.75); padding: 44px 6% 30px; text-align: center; }
        footer p { margin: 4px 0; font-size: 14px; }
        footer .foot-brand { font-family: 'Fraunces', serif; color: #fff; font-size: 1.2rem; margin-bottom: 10px; }

        /* ---------- Auth Modal (Admission Card) ---------- */
        .modal {
            display: none; position: fixed; inset: 0; z-index: 2000;
            background: rgba(10, 20, 45, 0.65);
            align-items: center; justify-content: center;
            padding: 24px;
        }
        .modal.open { display: flex; }

        .admission-card {
            width: 100%; max-width: 760px; max-height: 92vh; overflow-y: auto;
            background: var(--paper); border-radius: 20px; box-shadow: 0 40px 80px -20px rgba(0,0,0,0.5);
            display: flex; position: relative;
        }

        .admission-side {
            flex: 0 0 260px; background: linear-gradient(160deg, var(--ink), var(--ink-2));
            color: #fff; padding: 36px 30px; display: flex; flex-direction: column; justify-content: space-between;
            position: relative;
        }
        .admission-side::after {
            content: ''; position: absolute; top: 0; right: -1px; bottom: 0; width: 1px;
            background-image: radial-gradient(circle, var(--paper) 2.5px, transparent 2.6px);
            background-size: 100% 18px; background-repeat: repeat-y;
        }
        .seal { width: 54px; height: 54px; border-radius: 50%; border: 1.5px solid var(--gold); display: flex; align-items: center; justify-content: center; font-family: 'IBM Plex Mono', monospace; font-weight: 600; color: var(--gold-light); margin-bottom: 22px; }
        .admission-side h3 { font-family: 'Fraunces', serif; font-size: 1.4rem; margin: 0 0 10px; line-height: 1.25; }
        .admission-side p { font-size: 13.5px; color: rgba(255,255,255,0.72); line-height: 1.6; }
        .admission-code { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: rgba(255,255,255,0.45); letter-spacing: 0.08em; }

        .admission-main { flex: 1; padding: 32px 34px; position: relative; }
        .modal-close { position: absolute; top: 18px; right: 20px; background: none; border: none; font-size: 20px; cursor: pointer; color: var(--ink-soft); line-height: 1; }

        .auth-tabs { display: flex; gap: 4px; background: var(--paper-2); border-radius: 999px; padding: 4px; margin-bottom: 26px; width: fit-content; }
        .auth-tab { border: none; background: transparent; padding: 9px 20px; border-radius: 999px; font-weight: 600; font-size: 13.5px; cursor: pointer; color: var(--ink-soft); }
        .auth-tab.active { background: var(--ink); color: #fff; }

        .auth-panel { display: none; }
        .auth-panel.active { display: block; }
        .auth-panel h2 { font-family: 'Fraunces', serif; font-size: 1.5rem; margin: 0 0 6px; }
        .auth-panel > p.hint { color: var(--ink-soft); font-size: 13.5px; margin: 0 0 22px; }

        .field { margin-bottom: 18px; }
        .field label { display: block; font-family: 'IBM Plex Mono', monospace; font-size: 10.5px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--ink-soft); margin-bottom: 8px; }
        .field input {
            width: 100%; border: none; border-bottom: 1.5px solid var(--line); background: transparent;
            padding: 6px 2px 10px; font-size: 15px; font-family: 'Inter', sans-serif; color: var(--ink);
        }
        .field input:focus { border-bottom-color: var(--gold); outline: none; }
        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }

        .auth-switch { text-align: center; margin-top: 18px; font-size: 13.5px; color: var(--ink-soft); }
        .auth-switch button { background: none; border: none; color: var(--gold); font-weight: 700; cursor: pointer; padding: 0; font-size: 13.5px; }

        /* ---------- Mobile ---------- */
        @media (max-width: 980px) {
            .about-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 860px) {
            .nav-desktop { display: none; }
            .hamburger { display: flex; }
            .hero-container { flex-direction: column-reverse; text-align: center; }
            .hero-text { text-align: center; }
            .hero-subtitle { margin: 0 auto 30px; }
            .hero-actions, .hero-stats { justify-content: center; }
            .hero-title { font-size: 2.2rem; }
            .hero-visual img { max-width: 260px; }
        }

        @media (max-width: 720px) {
            .admission-card { flex-direction: column; max-width: 440px; }
            .admission-side { flex: none; padding: 24px 26px; }
            .admission-side::after { top: auto; bottom: -1px; left: 0; right: 0; width: auto; height: 1px; background-image: radial-gradient(circle, var(--paper) 2.5px, transparent 2.6px); background-size: 18px 100%; background-repeat: repeat-x; }
            .admission-main { padding: 28px 22px 30px; }
            .field-row { grid-template-columns: 1fr; gap: 0; }
        }

        @media (max-width: 480px) {
            .site-header { padding: 14px 5%; }
            .hero { padding: 56px 6% 70px; }
            .hero-title { font-size: 1.85rem; }
            .section { padding: 60px 5%; }
            .services-band h2 { font-size: 1.7rem; }
        }
    </style>
</head>
<body>

<?php flash_render(); ?>

<header class="site-header">
    <a class="brand" href="index.php">
        <span class="brand-mark">UT</span>
        <span class="brand-word">UNI<em>·</em>TUIT</span>
    </a>
    <nav class="nav-desktop">
        <a href="index.php">Home</a>
        <a href="#modules">Modules</a>
        <a href="#about">About</a>
        <a href="services.php">Other Services</a>
        <a href="#contact">Contact</a>
        <div class="nav-actions">
            <a href="admin_login.php" class="btn btn-ghost"><i class="fas fa-user-shield"></i> Admin</a>
            <button class="btn btn-ghost" onclick="openModal('login')">Log in</button>
            <button class="btn btn-gold" onclick="openModal('register')">Get Started</button>
        </div>
    </nav>
    <button class="hamburger" id="hamburgerBtn" aria-label="Open menu" aria-expanded="false" onclick="toggleMenu()">
        <span></span><span></span><span></span>
    </button>
</header>

<div class="mobile-nav" id="mobileNav">
    <div class="mobile-nav-backdrop" onclick="toggleMenu()"></div>
    <div class="mobile-nav-panel">
        <button class="mobile-nav-close" aria-label="Close menu" onclick="toggleMenu()">&times;</button>
        <a href="index.php">Home</a>
        <a href="#modules" onclick="toggleMenu()">Modules</a>
        <a href="#about" onclick="toggleMenu()">About</a>
        <a href="services.php">Other Services</a>
        <a href="#contact" onclick="toggleMenu()">Contact</a>
        <div class="mobile-nav-actions">
            <a href="admin_login.php" class="btn btn-ghost btn-block"><i class="fas fa-user-shield"></i> Admin Login</a>
            <button class="btn btn-ghost btn-block" onclick="toggleMenu(); openModal('login')">Log in</button>
            <button class="btn btn-gold btn-block" onclick="toggleMenu(); openModal('register')">Get Started</button>
        </div>
    </div>
</div>

<section class="hero">
    <div class="hero-container">
        <div class="hero-text">
            <span class="eyebrow">Enrolment open for this semester</span>
            <h1 class="hero-title">Quality tuition for <span>university success</span></h1>
            <p class="hero-subtitle">Master your modules with the best tutors in the country. Book a seat, track your progress, and walk into every exam prepared.</p>
            <div class="hero-actions">
                <button class="btn btn-gold" onclick="openModal('register')">Get Started <i class="fas fa-arrow-right"></i></button>
                <a href="#modules" class="hero-btn-outline">Explore Modules</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><strong>500+</strong><span>Students Tutored</span></div>
                <div class="hero-stat"><strong>12</strong><span>Modules Offered</span></div>
                <div class="hero-stat"><strong>4.8/5</strong><span>Average Rating</span></div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-visual-frame">
                <img src="lecturer.png" alt="UNI-TUIT tutor">
                <div class="image-glow"></div>
            </div>
        </div>
    </div>
</section>

<section id="about" class="section">
    <div class="about-grid">
        <div>
            <span class="eyebrow" style="color:var(--gold);">About UNI-TUIT</span>
            <h2 style="font-size:2rem; margin:10px 0 16px;">Built by tutors who remember the pressure</h2>
            <p style="color:var(--ink-soft); line-height:1.7;">UNI-TUIT connects university students with experienced tutors for the modules that make or break a semester. From database systems to calculus, every session is built around your syllabus, not a generic script.</p>
        </div>
        <div class="about-card">
            <h3><i class="fas fa-check-circle" style="color:var(--gold);"></i>&nbsp; What you get</h3>
            <p>Live tutoring sessions, past paper walkthroughs, and a dashboard to track every module you register for &mdash; all from one account.</p>
        </div>
    </div>
</section>

<section id="modules" class="section">
    <div class="section-head">
        <span class="eyebrow">Course Catalogue</span>
        <h2>Popular Modules</h2>
        <p>Register or log in to unlock full access, book sessions, and view materials for each module.</p>
    </div>
    <div class="module-list">
        <?php
        $mods = ["Database System", "Function of Single Varriable"];
        $img = ["db.jpg", "os.jpg", "network.jpg", "web.jpg", "function.jpg"];
        foreach ($mods as $key => $m) {
            $i = $img[$key];
            $tag = "MOD-" . str_pad($key + 1, 2, "0", STR_PAD_LEFT);
            echo "<div class='card' onclick=\"openModal('login')\">
                    <img src='$i' alt='$m'>
                    <div class='card-body'>
                        <span class='card-tag'>$tag</span>
                        <h3>$m</h3>
                    </div>
                  </div>";
        }
        ?>
    </div>
</section>

<section class="services-band">
    <h2>Beyond Tuition&hellip;</h2>
    <p>Discover professional tech solutions provided by our parent company.</p>
    <a href="services.php" class="btn btn-gold">Explore Our Other Services <i class="fas fa-arrow-right"></i></a>
</section>

<footer id="contact">
    <div class="foot-brand">UNI-TUIT</div>
    <p>Email: fransiscofrednand24@gmail.com &nbsp;|&nbsp; WhatsApp: +255 760 987 261</p>
    <p>&copy; 2026 UNI-TUIT System &nbsp;|&nbsp; <a href="admin_login.php" style="color:inherit;">Admin Login</a></p>
</footer>

<!-- AUTH MODAL -->
<div id="authModal" class="modal">
    <div class="admission-card">
        <div class="admission-side">
            <div>
                <div class="seal">UT</div>
                <h3>Your Admission Pass to Better Grades</h3>
                <p>One account unlocks every module, tutor session, and progress report on UNI-TUIT.</p>
            </div>
            <span class="admission-code">REF// UT-ADMIT-2026</span>
        </div>
        <div class="admission-main">
            <button class="modal-close" aria-label="Close" onclick="closeModal()">&times;</button>

            <div class="auth-tabs" role="tablist">
                <button class="auth-tab" id="tabBtn-login" onclick="switchTab('login')">Log In</button>
                <button class="auth-tab" id="tabBtn-register" onclick="switchTab('register')">Register</button>
            </div>

            <div class="auth-panel" id="panel-login">
                <h2>Welcome back</h2>
                <p class="hint">Log in to continue your modules.</p>
                <form action="auth.php" method="POST" id="loginForm">
                    <div class="field">
                        <label for="login-email">Email address</label>
                        <input id="login-email" type="email" name="email" placeholder="you@example.com" required>
                    </div>
                    <div class="field">
                        <label for="login-password">Password</label>
                        <input id="login-password" type="password" name="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-gold btn-block" id="loginBtn">Enter Portal</button>
                </form>
                <p class="auth-switch">New here? <button type="button" onclick="switchTab('register')">Create an account</button></p>
            </div>

            <div class="auth-panel" id="panel-register">
                <h2>Create your account</h2>
                <p class="hint">Register once, access every module.</p>
                <form action="auth.php" method="POST" id="registerForm">
                    <div class="field">
                        <label for="reg-username">Username</label>
                        <input id="reg-username" type="text" name="username" placeholder="Jane Doe" required>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="reg-phone">Phone number</label>
                            <input id="reg-phone" type="text" name="phone" placeholder="07XXXXXXXX" required>
                        </div>
                        <div class="field">
                            <label for="reg-email">Email address</label>
                            <input id="reg-email" type="email" name="email" placeholder="you@example.com" required>
                        </div>
                    </div>
                    <div class="field">
                        <label for="reg-program">Program name</label>
                        <input id="reg-program" type="text" name="program" placeholder="e.g. BSc Computer Science" required>
                    </div>
                    <div class="field">
                        <label for="reg-password">Password</label>
                        <input id="reg-password" type="password" name="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-gold btn-block" id="registerBtn">Create Account</button>
                </form>
                <p class="auth-switch">Already registered? <button type="button" onclick="switchTab('login')">Log in</button></p>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleMenu() {
        var m = document.getElementById('mobileNav');
        var btn = document.getElementById('hamburgerBtn');
        var isOpen = m.classList.toggle('open');
        btn.classList.toggle('open', isOpen);
        btn.setAttribute('aria-expanded', isOpen);
    }

    function switchTab(tab) {
        ['login', 'register'].forEach(function (t) {
            document.getElementById('panel-' + t).classList.toggle('active', t === tab);
            document.getElementById('tabBtn-' + t).classList.toggle('active', t === tab);
        });
    }

    function openModal(tab) {
        switchTab(tab || 'login');
        document.getElementById('authModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('authModal').classList.remove('open');
        document.body.style.overflow = '';
    }

    document.getElementById('authModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal();
            document.getElementById('mobileNav').classList.remove('open');
        }
    });

    function withLoadingState(formId, btnId, label) {
        var form = document.getElementById(formId);
        form.addEventListener('submit', function () {
            var btn = document.getElementById(btnId);
            // Defer: a button disabled during 'submit' is dropped from the form data (login/register never reach auth.php)
            setTimeout(function () {
                btn.disabled = true;
                btn.style.opacity = '0.75';
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + label;
            }, 0);
        });
    }
    withLoadingState('loginForm', 'loginBtn', 'Logging in&hellip;');
    withLoadingState('registerForm', 'registerBtn', 'Creating account&hellip;');

    // Reopen the auth modal on the right tab after a redirect back from auth.php
    var __authTab = new URLSearchParams(location.search).get('auth');
    if (__authTab === 'login' || __authTab === 'register') {
        openModal(__authTab);
        history.replaceState(null, '', location.pathname + location.hash);
    }
</script>
</body>
</html>
