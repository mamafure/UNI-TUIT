<?php
include 'db.php';

// ACCESS CONTROL: Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login");
    exit();
}

include 'admin_ui.php';

// Unique students who have at least one registration
$sql = "SELECT users.id, users.username, users.email, users.phone, users.program,
        COUNT(registrations.id) AS total_mods,
        SUM(registrations.status = 'Pending') AS pending_mods
        FROM users
        JOIN registrations ON users.id = registrations.user_id
        GROUP BY users.id
        ORDER BY users.username ASC";
$result = mysqli_query($conn, $sql);
$students = [];
while ($row = mysqli_fetch_assoc($result)) { $students[] = $row; }

// Data for the slide-over panel (all students are small enough to embed)
$drawer = [];
foreach ($students as $s) {
    $drawer[(int)$s['id']] = ['id' => (int)$s['id'], 'name' => $s['username'], 'email' => $s['email'],
        'phone' => $s['phone'], 'program' => $s['program'], 'regs' => []];
}
$rq = mysqli_query($conn, "SELECT id, user_id, module_name, fee, status FROM registrations ORDER BY id ASC");
while ($r = mysqli_fetch_assoc($rq)) {
    if (isset($drawer[(int)$r['user_id']])) {
        $drawer[(int)$r['user_id']]['regs'][] = ['id' => (int)$r['id'], 'module' => $r['module_name'],
            'fee' => (float)$r['fee'], 'status' => $r['status']];
    }
}

admin_page_start('Student Registrations', 'students', [
    'heading' => 'Student Registrations',
    'sub' => 'Every student who has applied for modules, with their current activity.',
]);
?>
    <div class="panel">
        <div class="panel-head">
            <div>
                <span class="eyebrow">Directory</span>
                <h2>All Students</h2>
            </div>
            <?php if ($students): ?>
            <label class="search"><i class="fas fa-magnifying-glass"></i>
                <input type="search" id="studentSearch" placeholder="Search name, email or program" aria-label="Search students">
            </label>
            <?php endif; ?>
        </div>

        <?php if (!$students): ?>
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <p>No students have selected modules yet.</p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table id="studentTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Contact</th>
                        <th>Program</th>
                        <th>Modules</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($students as $s):
                    $uid = (int)$s['id'];
                    $initial = strtoupper(mb_substr($s['username'], 0, 1));
                    $pend = (int)$s['pending_mods'];
                    $mods = (int)$s['total_mods'];
                ?>
                    <tr class="clickable" tabindex="0" onclick="openStudent(<?php echo $uid; ?>)" onkeydown="if(event.key==='Enter')openStudent(<?php echo $uid; ?>)">
                        <td><div class="person"><span class="avatar"><?php echo admin_h($initial); ?></span><strong><?php echo admin_h($s['username']); ?></strong></div></td>
                        <td><?php echo admin_h($s['email']); ?><br><small><?php echo admin_h($s['phone']); ?></small></td>
                        <td><?php echo admin_h($s['program']); ?></td>
                        <td><div class="pill-group">
                            <span class="count-pill"><?php echo $mods; ?> <?php echo $mods === 1 ? 'module' : 'modules'; ?></span>
                            <?php if ($pend > 0): ?><span class="badge badge-pending"><?php echo $pend; ?> pending</span><?php endif; ?>
                        </div></td>
                        <td><button type="button" class="btn btn-outline" onclick="event.stopPropagation(); openStudent(<?php echo $uid; ?>)">View <i class="fas fa-arrow-right"></i></button></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

<style>
    .drawer-head .eyebrow { color: var(--gold-light); }
    .drawer-head .avatar { width: 38px; height: 38px; font-size: 14px; background: var(--paper-2); color: var(--ink); }
    .drawer-head .who { min-width: 0; }
    .drawer-head h2 { color: #fff; font-size: 1.1rem; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .drawer-head .eyebrow { font-size: 10px; display: block; margin-bottom: 2px; }
    .info-list { display: grid; gap: 12px; margin: 0 0 22px; padding: 0; list-style: none; }
    .info-list li { display: flex; gap: 14px; align-items: flex-start; font-size: 14.5px; word-break: break-word; }
    .info-list i { width: 32px; height: 32px; border-radius: 10px; background: var(--paper-2); color: var(--gold); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
    .info-list small { display: block; font-family: 'IBM Plex Mono', monospace; font-size: 10px; letter-spacing: .1em; text-transform: uppercase; color: var(--ink-soft); margin-bottom: 2px; }
    .drawer h3.sec { font-size: 1.05rem; margin-bottom: 12px; display: flex; align-items: baseline; justify-content: space-between; }
    .drawer h3.sec span { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: var(--ink-soft); font-weight: 500; }
    .mod { background: #fff; border: 1px solid var(--line); border-left: 3px solid var(--line); border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; }
    .mod.s-pending { border-left-color: var(--gold); } .mod.s-registered { border-left-color: var(--success); } .mod.s-rejected { border-left-color: var(--danger); } .mod.s-draft { border-left-color: #94a3b8; }
    .mod-top { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
    .mod-top strong { font-size: 15px; }
    .mod-fee { font-family: 'IBM Plex Mono', monospace; font-size: 11.5px; color: var(--ink-soft); margin-top: 3px; }
    .mod .action-group { margin-top: 12px; }
    .mod .btn { padding: 8px 15px; font-size: 13px; }
</style>

<div class="drawer-backdrop" id="drawerBackdrop" onclick="closeStudent()"></div>
<aside class="drawer" id="drawer" role="dialog" aria-modal="true" aria-labelledby="drawerName" aria-hidden="true">
    <div class="drawer-head">
        <div class="avatar" id="drawerAvatar"></div>
        <div class="who"><span class="eyebrow">Student profile</span><h2 id="drawerName"></h2></div>
        <button type="button" class="drawer-close" id="drawerClose" onclick="closeStudent()" aria-label="Close"><i class="fas fa-xmark"></i></button>
    </div>
    <div class="drawer-body">
        <ul class="info-list" id="drawerInfo"></ul>
        <h3 class="sec">Registration list <span id="drawerCount"></span></h3>
        <div id="drawerMods"></div>
    </div>
    <div class="drawer-foot"><a class="btn btn-outline" id="drawerFull" href="#"><i class="fas fa-up-right-from-square"></i> Open full page</a></div>
</aside>

<?php admin_reject_modal(); ?>

<script>
    var STUDENTS = <?php echo json_encode($drawer, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var lastFocus = null;

    function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }
    function fmt(n) { return Number(n).toLocaleString('en-US'); }

    function openStudent(id) {
        var s = STUDENTS[id]; if (!s) return;
        lastFocus = document.activeElement;
        document.getElementById('drawerAvatar').textContent = (s.name || '?').charAt(0).toUpperCase();
        document.getElementById('drawerName').textContent = s.name;
        document.getElementById('drawerInfo').innerHTML =
            '<li><i class="fas fa-envelope"></i><div><small>Email</small>' + esc(s.email) + '</div></li>' +
            '<li><i class="fas fa-phone"></i><div><small>Phone</small>' + esc(s.phone) + '</div></li>' +
            '<li><i class="fas fa-graduation-cap"></i><div><small>Program</small>' + esc(s.program) + '</div></li>';
        document.getElementById('drawerCount').textContent = s.regs.length + (s.regs.length === 1 ? ' module' : ' modules');
        var html = '';
        s.regs.forEach(function (r) {
            var st = r.status.toLowerCase(), base = 'approve?id=' + r.id + '&user_id=' + s.id + '&return=students';
            var actions = '';
            if (r.status === 'Pending') {
                actions = '<div class="action-group">' +
                    '<a class="btn btn-approve" href="' + base + '&action=approve"><i class="fas fa-check"></i> Approve</a>' +
                    '<a class="btn btn-reject" href="' + base + '&action=reject" data-m="' + esc(r.module).replace(/"/g, '&quot;') + '" data-s="' + esc(s.name).replace(/"/g, '&quot;') + '" onclick="return confirmReject(this, this.dataset.m, this.dataset.s)"><i class="fas fa-xmark"></i> Reject</a></div>';
            } else if (r.status === 'Draft') {
                actions = '<div class="mod-fee" style="margin-top:10px"><i class="fas fa-clock"></i> Not submitted by student yet</div>';
            }
            html += '<div class="mod s-' + st + '"><div class="mod-top"><strong>' + esc(r.module) + '</strong><span class="badge badge-' + st + '">' + esc(r.status) + '</span></div>' +
                    '<div class="mod-fee">' + fmt(r.fee) + ' Tsh</div>' + actions + '</div>';
        });
        document.getElementById('drawerMods').innerHTML = html || '<div class="empty-state" style="padding:24px 0"><p>No modules selected yet.</p></div>';
        document.getElementById('drawerFull').href = 'admin_student_details?user_id=' + s.id;
        document.getElementById('drawerBackdrop').classList.add('open');
        var d = document.getElementById('drawer'); d.classList.add('open'); d.setAttribute('aria-hidden', 'false');
        document.body.classList.add('no-scroll');
        document.getElementById('drawerClose').focus();
    }

    function closeStudent() {
        document.getElementById('drawerBackdrop').classList.remove('open');
        var d = document.getElementById('drawer'); d.classList.remove('open'); d.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('no-scroll');
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('rejectConfirm').classList.contains('open')) closeStudent();
    });

    // Reopen after Approve/Reject so the admin stays in context
    var openId = new URLSearchParams(location.search).get('open');
    if (openId && STUDENTS[openId]) { openStudent(openId); history.replaceState(null, '', 'admin'); }

    var box = document.getElementById('studentSearch');
    if (box) box.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#studentTable tbody tr').forEach(function (tr) {
            tr.style.display = tr.textContent.toLowerCase().indexOf(q) === -1 ? 'none' : '';
        });
    });
</script>
<?php admin_page_end();
