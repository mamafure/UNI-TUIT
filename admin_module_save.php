<?php
// Create / update / show-hide / delete a module. Admin only, POST only, CSRF-protected.
include 'db.php';
include 'flash.php';
include 'modules_lib.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: admin_login"); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: admin_subjects"); exit(); }

$token = $_POST['csrf'] ?? '';
if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
    flash_set('error', 'Your session expired. Please try again.');
    header("Location: admin_subjects");
    exit();
}

modules_ensure($conn);
$action = $_POST['action'] ?? 'save';
$id = (int)($_POST['id'] ?? 0);

function back($type, $msg) { flash_set($type, $msg); header("Location: admin_subjects"); exit(); }

// ---- show / hide ----
if ($action === 'toggle' && $id > 0) {
    $st = mysqli_prepare($conn, "UPDATE modules SET is_active = 1 - is_active WHERE id = ?");
    mysqli_stmt_bind_param($st, 'i', $id);
    mysqli_stmt_execute($st);
    back('success', 'Module visibility updated.');
}

// ---- delete (only when nobody has registered for it) ----
if ($action === 'delete' && $id > 0) {
    $st = mysqli_prepare($conn, "SELECT name FROM modules WHERE id = ?");
    mysqli_stmt_bind_param($st, 'i', $id); mysqli_stmt_execute($st);
    $m = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
    if (!$m) back('error', 'That module no longer exists.');

    $st = mysqli_prepare($conn, "SELECT COUNT(*) FROM registrations WHERE module_name = ?");
    mysqli_stmt_bind_param($st, 's', $m['name']); mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $n); mysqli_stmt_fetch($st); mysqli_stmt_close($st);
    if ($n > 0) back('error', "\"{$m['name']}\" has $n registration(s), so it can't be deleted. Hide it from students instead.");

    $st = mysqli_prepare($conn, "DELETE FROM modules WHERE id = ?");
    mysqli_stmt_bind_param($st, 'i', $id); mysqli_stmt_execute($st);
    back('success', "\"{$m['name']}\" was deleted.");
}

// ---- save (create or edit) ----
$in = [
    'id' => $id,
    'name' => trim($_POST['name'] ?? ''),
    'tag' => trim($_POST['tag'] ?? ''),
    'description' => trim($_POST['description'] ?? ''),
    'topics' => trim($_POST['topics'] ?? ''),
    'instructor' => trim($_POST['instructor'] ?? ''),
    'duration' => trim($_POST['duration'] ?? ''),
    'fee' => trim($_POST['fee'] ?? ''),
    'icon' => $_POST['icon'] ?? 'fa-book-open',
    'color' => $_POST['color'] ?? '#2563eb',
    'is_active' => isset($_POST['is_active']) ? 1 : 0,
];

$errors = [];
$len = function ($s) { return mb_strlen($s, 'UTF-8'); };
if ($len($in['name']) < 2 || $len($in['name']) > 100) $errors[] = 'Module name must be 2–100 characters.';
if ($in['description'] === '' || $len($in['description']) > 2000) $errors[] = 'Add a description (up to 2000 characters).';
if ($len($in['topics']) > 2000 || count(array_filter(preg_split('/\R/', $in['topics']), 'strlen')) > 12) $errors[] = 'Topics: at most 12 lines.';
if ($len($in['tag']) > 20) $errors[] = 'Module code is too long (max 20).';
if ($len($in['instructor']) > 100) $errors[] = 'Instructor name is too long.';
if ($len($in['duration']) > 50) $errors[] = 'Duration is too long (max 50).';
if (!ctype_digit($in['fee']) || (int)$in['fee'] > 10000000) $errors[] = 'Fee must be a whole number of Tsh.';
if (!array_key_exists($in['icon'], module_icons())) $in['icon'] = 'fa-book-open';
if (!in_array($in['color'], module_colors(), true)) $in['color'] = '#2563eb';

// name must be unique
if (!$errors) {
    $st = mysqli_prepare($conn, "SELECT id FROM modules WHERE name = ? AND id <> ?");
    mysqli_stmt_bind_param($st, 'si', $in['name'], $id); mysqli_stmt_execute($st);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($st))) $errors[] = "A module named \"{$in['name']}\" already exists.";
    mysqli_stmt_close($st);
}

if ($errors) {
    $_SESSION['module_old'] = $in;   // re-open the form with what they typed
    flash_set('error', implode(' ', $errors));
    header("Location: admin_subjects?fix=" . ($id ?: 'new'));
    exit();
}

$fee = (int)$in['fee'];

if ($id > 0) {
    $st = mysqli_prepare($conn, "SELECT name, tag FROM modules WHERE id = ?");
    mysqli_stmt_bind_param($st, 'i', $id); mysqli_stmt_execute($st);
    $old = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
    if (!$old) back('error', 'That module no longer exists.');
    if ($in['tag'] === '') $in['tag'] = $old['tag'];   // never wipe the code by leaving it blank

    mysqli_begin_transaction($conn);
    $st = mysqli_prepare($conn, "UPDATE modules SET name=?, tag=?, description=?, topics=?, instructor=?, duration=?, fee=?, icon=?, color=?, is_active=? WHERE id=?");
    mysqli_stmt_bind_param($st, 'ssssssissii', $in['name'], $in['tag'], $in['description'], $in['topics'], $in['instructor'], $in['duration'], $fee, $in['icon'], $in['color'], $in['is_active'], $id);
    $ok = mysqli_stmt_execute($st);
    // Registrations reference the module by name: keep them attached if it was renamed
    if ($ok && $old['name'] !== $in['name']) {
        $st2 = mysqli_prepare($conn, "UPDATE registrations SET module_name = ? WHERE module_name = ?");
        mysqli_stmt_bind_param($st2, 'ss', $in['name'], $old['name']);
        $ok = mysqli_stmt_execute($st2);
    }
    $ok ? mysqli_commit($conn) : mysqli_rollback($conn);
    if (!$ok) { error_log('module update failed: ' . mysqli_error($conn)); back('error', 'Something went wrong saving that module.'); }
    back('success', "\"{$in['name']}\" was updated.");
}

// new module: auto-generate a code (MOD-07, MOD-08, ...) when left blank
if ($in['tag'] === '') {
    $r = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(MAX(id), 0) + 1 FROM modules"));
    $in['tag'] = 'MOD-' . str_pad((string)$r[0], 2, '0', STR_PAD_LEFT);
}
$st = mysqli_prepare($conn, "INSERT INTO modules (name, tag, description, topics, instructor, duration, fee, icon, color, is_active) VALUES (?,?,?,?,?,?,?,?,?,?)");
mysqli_stmt_bind_param($st, 'ssssssissi', $in['name'], $in['tag'], $in['description'], $in['topics'], $in['instructor'], $in['duration'], $fee, $in['icon'], $in['color'], $in['is_active']);
if (!mysqli_stmt_execute($st)) { error_log('module insert failed: ' . mysqli_error($conn)); back('error', 'Something went wrong saving that module.'); }
back('success', "\"{$in['name']}\" was created" . ($in['is_active'] ? ' and is now visible to students.' : ' (hidden from students).'));
