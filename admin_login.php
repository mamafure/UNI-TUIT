<?php include 'db.php'; include 'flash.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | UNI-TUIT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#10254e; --ink-2:#16327a; --ink-soft:#52618a; --paper:#faf8f3; --paper-2:#f2ede0; --line:#e4dfd3; --gold:#c99a3b; --gold-light:#e6c878; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; font-family: 'Inter', sans-serif; color: var(--ink); -webkit-font-smoothing: antialiased;
               background: radial-gradient(circle at 85% 0%, rgba(201,154,59,.2), transparent 45%), linear-gradient(135deg, var(--ink) 0%, var(--ink-2) 100%); }
        :focus-visible { outline: 2px solid var(--gold); outline-offset: 3px; }
        .login-box { width: 100%; max-width: 420px; background: var(--paper); border-radius: 20px; padding: 36px 34px 30px; box-shadow: 0 40px 80px -20px rgba(0,0,0,.5); }
        .brand { display: flex; align-items: center; gap: 10px; justify-content: center; margin-bottom: 22px; }
        .brand-mark { width: 40px; height: 40px; border-radius: 50%; background: var(--ink); color: var(--gold-light); display: flex; align-items: center; justify-content: center; font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 14px; border: 1px solid var(--gold); }
        .brand-word { font-family: 'Fraunces', serif; font-size: 21px; font-weight: 700; }
        .eyebrow { display: block; text-align: center; font-family: 'IBM Plex Mono', monospace; font-size: 11.5px; letter-spacing: .16em; text-transform: uppercase; font-weight: 600; color: var(--gold); }
        h2 { font-family: 'Fraunces', serif; text-align: center; font-size: 1.6rem; margin: 6px 0 24px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        input { width: 100%; font: inherit; font-size: 15px; padding: 13px 16px; border: 1.5px solid var(--line); border-radius: 12px; background: #fff; color: var(--ink); transition: border-color .2s; }
        input:focus { outline: none; border-color: var(--gold); }
        .btn-login { width: 100%; margin-top: 8px; border: none; cursor: pointer; border-radius: 999px; padding: 14px 24px; font: inherit; font-size: 15.5px; font-weight: 600; background: var(--gold); color: var(--ink); transition: transform .15s, box-shadow .15s, background .15s; }
        .btn-login:hover:not(:disabled) { background: var(--gold-light); transform: translateY(-1px); box-shadow: 0 8px 18px -6px rgba(201,154,59,.6); }
        .footer-link { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 22px; color: var(--ink-soft); text-decoration: none; font-size: 13.5px; font-weight: 500; }
        .footer-link:hover { color: var(--ink); }
        .footer-link i { font-size: 12px; }
    </style>
</head>
<body>

<?php flash_render(); ?>

<div class="login-box">
    <div class="brand"><span class="brand-mark">UT</span><span class="brand-word">UNI·TUIT</span></div>
    <span class="eyebrow">Admin Console</span>
    <h2>Sign in to continue</h2>

    <form action="admin_auth" method="POST" id="adminLoginForm">
        <div class="form-group">
            <label for="admin-email">Administrator email</label>
            <input id="admin-email" type="email" name="email" placeholder="email@unituit.com" autocomplete="username" required>
        </div>
        <div class="form-group">
            <label for="admin-password">Password</label>
            <input id="admin-password" type="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
        </div>
        <button type="submit" name="admin_login" class="btn-login" id="adminLoginBtn">Secure Login</button>
    </form>

    <a href="/" class="footer-link"><i class="fas fa-arrow-left"></i> Back to main website</a>
</div>

<script>
    document.getElementById('adminLoginForm').addEventListener('submit', function () {
        var btn = document.getElementById('adminLoginBtn');
        // Defer: a button disabled during 'submit' is dropped from the form data (admin_login would not reach admin_auth)
        setTimeout(function () {
            btn.disabled = true;
            btn.style.opacity = '0.75';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in&hellip;';
        }, 0);
    });
</script>

</body>
</html>
