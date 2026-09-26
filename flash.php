<?php
// Session-based flash messages: set on one request, rendered once on the next.
// Replaces the old pattern of echo "<script>alert(...); window.location=...</script>"
// with a real HTTP redirect + a styled, dismissible toast instead of a browser dialog.

function flash_set($type, $msg) {
    $_SESSION['flash'] = ['type' => $type === 'error' ? 'error' : 'success', 'msg' => $msg];
}

function flash_render() {
    if (empty($_SESSION['flash'])) return;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);

    $type = $f['type'];
    $msg = htmlspecialchars($f['msg'], ENT_QUOTES, 'UTF-8');
    $icon = $type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check';
    ?>
    <div id="flashToast" class="flash-toast flash-<?php echo $type; ?>" role="status" aria-live="polite">
        <i class="fas <?php echo $icon; ?>"></i>
        <span><?php echo $msg; ?></span>
        <button type="button" class="flash-close" onclick="document.getElementById('flashToast').remove()" aria-label="Dismiss">&times;</button>
    </div>
    <style>
        .flash-toast {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            z-index: 5000; display: flex; align-items: center; gap: 12px;
            padding: 14px 16px 14px 18px; border-radius: 12px;
            font-family: 'Inter', system-ui, sans-serif; font-size: 14.5px; font-weight: 500;
            box-shadow: 0 15px 35px -10px rgba(0,0,0,0.4);
            max-width: min(90vw, 460px);
            animation: flashIn 0.3s ease, flashOut 0.3s ease 5.2s forwards;
        }
        .flash-success { background: #10254e; color: #fff; border: 1px solid #c99a3b; }
        .flash-success i { color: #c99a3b; }
        .flash-error { background: #fdf0ef; color: #7a1f1f; border: 1px solid #d64545; }
        .flash-error i { color: #d64545; }
        .flash-toast span { line-height: 1.4; }
        .flash-close { background: none; border: none; color: inherit; opacity: 0.6; cursor: pointer; font-size: 18px; line-height: 1; margin-left: 4px; padding: 0; }
        .flash-close:hover { opacity: 1; }
        @keyframes flashIn { from { opacity: 0; transform: translate(-50%, -14px); } to { opacity: 1; transform: translate(-50%, 0); } }
        @keyframes flashOut { to { opacity: 0; transform: translate(-50%, -14px); } }
        @media (prefers-reduced-motion: reduce) { .flash-toast { animation: none; } }
    </style>
    <script>
        setTimeout(function () {
            var t = document.getElementById('flashToast');
            if (t) t.remove();
        }, 5600);
    </script>
    <?php
}
