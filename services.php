<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Other Services | UNI-TUIT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #10254e; --ink-2: #16327a; --ink-soft: #52618a;
            --paper: #faf8f3; --paper-2: #f2ede0; --line: #e4dfd3;
            --gold: #c99a3b; --gold-light: #e6c878;
            --shadow: 0 20px 45px -20px rgba(16, 37, 78, 0.35);
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--paper); color: var(--ink); margin: 0; -webkit-font-smoothing: antialiased; }
        h1, h2, h3 { font-family: 'Fraunces', serif; margin: 0; }
        a { color: inherit; }
        :focus-visible { outline: 2px solid var(--gold); outline-offset: 3px; }
        .eyebrow { font-family: 'IBM Plex Mono', monospace; font-size: 11.5px; letter-spacing: .16em; text-transform: uppercase; font-weight: 600; }

        .btn { border: none; cursor: pointer; border-radius: 999px; font-weight: 600; font-size: 14.5px; padding: 11px 24px; transition: transform .15s ease, box-shadow .15s ease, background .15s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-gold { background: var(--gold); color: var(--ink); }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-1px); box-shadow: 0 8px 18px -6px rgba(201,154,59,.6); }
        .btn-outline { background: transparent; color: var(--ink); border: 1.5px solid var(--line); }
        .btn-outline:hover { border-color: var(--ink); }
        .btn-ghost-light { background: rgba(255,255,255,.08); color: #fff; border: 1.5px solid rgba(255,255,255,.35); }
        .btn-ghost-light:hover { border-color: #fff; background: rgba(255,255,255,.16); }

        /* Header */
        .portal-header { background: var(--paper); padding: 16px 6%; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 100; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand-mark { width: 36px; height: 36px; border-radius: 50%; background: var(--ink); color: var(--gold-light); display: flex; align-items: center; justify-content: center; font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 13px; border: 1px solid var(--gold); flex-shrink: 0; }
        .brand-word { font-family: 'Fraunces', serif; font-size: 19px; font-weight: 700; }

        /* Hero */
        .hero { background: radial-gradient(circle at 85% 0%, rgba(201,154,59,.2), transparent 45%), linear-gradient(135deg, var(--ink) 0%, var(--ink-2) 100%); padding: 56px 6% 96px; }
        .hero-inner { max-width: 1080px; margin: 0 auto; }
        .hero .eyebrow { color: var(--gold-light); }
        .hero h1 { color: #fff; font-size: clamp(1.9rem, 4vw, 2.6rem); margin: 12px 0 12px; }
        .hero p { color: rgba(255,255,255,.75); margin: 0; max-width: 560px; line-height: 1.6; }

        /* Service cards */
        .services { max-width: 1080px; margin: -56px auto 0; padding: 0 6%; display: grid; gap: 28px; }
        .service { background: #fff; border: 1px solid var(--line); border-radius: 22px; box-shadow: var(--shadow); overflow: hidden; display: grid; grid-template-columns: 1fr 1fr; min-height: 380px; }
        .service.flip .service-img { order: 2; }
        .service-img { background-size: cover; background-position: center; min-height: 260px; }
        .service-img.contain { background-size: contain; background-repeat: no-repeat; background-color: #fff; background-origin: content-box; padding: 24px; }
        .service-body { padding: 40px 42px; display: flex; flex-direction: column; justify-content: center; }
        .service-body .eyebrow { color: var(--gold); }
        .service-body h2 { font-size: 1.6rem; margin: 8px 0 12px; line-height: 1.2; }
        .service-body > p { color: var(--ink-soft); line-height: 1.65; margin: 0 0 20px; }
        .feature-list { list-style: none; margin: 0; padding: 0; display: grid; gap: 11px; }
        .feature-list li { display: flex; align-items: flex-start; gap: 12px; font-size: 14.5px; }
        .feature-list i { color: var(--gold); margin-top: 3px; }

        /* CTA */
        .cta-wrap { max-width: 1080px; margin: 44px auto 70px; padding: 0 6%; }
        .cta { background: linear-gradient(135deg, var(--ink) 0%, var(--ink-2) 100%); border-radius: 22px; padding: 44px 40px; text-align: center; color: #fff; box-shadow: var(--shadow); }
        .cta .eyebrow { color: var(--gold-light); }
        .cta h3 { font-size: 1.7rem; margin: 10px 0 8px; }
        .cta p { color: rgba(255,255,255,.75); margin: 0 0 26px; }
        .cta-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        footer { text-align: center; padding: 26px 6% 34px; color: var(--ink-soft); font-size: 13.5px; border-top: 1px solid var(--line); }

        @media (max-width: 820px) {
            .service, .service.flip { grid-template-columns: 1fr; min-height: 0; }
            .service.flip .service-img { order: 0; }
            .service-img { min-height: 220px; }
            .service-body { padding: 28px 24px 32px; }
            .hero { padding: 40px 5% 88px; }
            .services, .cta-wrap { padding: 0 5%; }
            .cta { padding: 34px 22px; }
        }
    </style>
</head>
<body>

<header class="portal-header">
    <a class="brand" href="/">
        <span class="brand-mark">UT</span>
        <span class="brand-word">UNI·TUIT</span>
    </a>
    <a href="/" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to home</a>
</header>

<section class="hero">
    <div class="hero-inner">
        <span class="eyebrow">Beyond Tuition</span>
        <h1>Our Expert Services</h1>
        <p>Empowering your business and personal growth with cutting-edge technology solutions and professional training.</p>
    </div>
</section>

<main class="services">
    <!-- Service 1 -->
    <article class="service">
        <div class="service-img" style="background-image: url('t_shooter.jpg');" role="img" aria-label="Technician repairing a laptop"></div>
        <div class="service-body">
            <span class="eyebrow">Hardware</span>
            <h2>Computer Maintenance and Repair</h2>
            <p>Is your hardware slowing you down? Our certified technicians provide top-tier diagnostic and repair services to keep your productivity at its peak.</p>
            <ul class="feature-list">
                <li><i class="fas fa-check-circle"></i> Advanced hardware troubleshooting</li>
                <li><i class="fas fa-check-circle"></i> Black &amp; blue screen problems</li>
                <li><i class="fas fa-check-circle"></i> OS installation</li>
                <li><i class="fas fa-check-circle"></i> Applications &amp; other software problems</li>
            </ul>
        </div>
    </article>

    <!-- Service 2 -->
    <article class="service flip">
        <div class="service-img contain" style="background-image: url('office.jpg');" role="img" aria-label="Microsoft Office applications"></div>
        <div class="service-body">
            <span class="eyebrow">Training</span>
            <h2>Microsoft Office Training</h2>
            <p>Become a workplace pro. We offer hands-on training for Microsoft tools that are essential for every student and professional in the modern world.</p>
            <ul class="feature-list">
                <li><i class="fas fa-check-circle"></i> Advanced Excel (data analysis &amp; macros)</li>
                <li><i class="fas fa-check-circle"></i> Professional Word &amp; report writing</li>
                <li><i class="fas fa-check-circle"></i> Impactful PowerPoint presentations</li>
                <li><i class="fas fa-check-circle"></i> Access for best database management</li>
            </ul>
        </div>
    </article>

    <!-- Service 3 -->
    <article class="service">
        <div class="service-img" style="background-image: url('website.jpg');" role="img" aria-label="Code on a monitor"></div>
        <div class="service-body">
            <span class="eyebrow">Software</span>
            <h2>Website and System Creation</h2>
            <p>Take your business or idea online. We design and develop custom websites and management systems tailored to your specific needs.</p>
            <ul class="feature-list">
                <li><i class="fas fa-check-circle"></i> Custom school &amp; hospital systems</li>
                <li><i class="fas fa-check-circle"></i> Professional e-commerce websites</li>
                <li><i class="fas fa-check-circle"></i> Mobile-responsive design (UI/UX)</li>
                <li><i class="fas fa-check-circle"></i> Full deployment &amp; maintenance</li>
            </ul>
        </div>
    </article>
</main>

<section class="cta-wrap">
    <div class="cta">
        <span class="eyebrow">Get in touch</span>
        <h3>Need a custom solution?</h3>
        <p>Our team is ready to help you navigate your technical challenges.</p>
        <div class="cta-actions">
            <a class="btn btn-gold" href="mailto:fransiscofrednand24@gmail.com"><i class="fas fa-envelope"></i> Email us</a>
            <a class="btn btn-ghost-light" href="https://wa.me/255760987261"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        </div>
    </div>
</section>

<footer>UNI-TUIT Platform &copy; 2026 &mdash; Quality University Tuition</footer>

</body>
</html>
