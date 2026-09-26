<?php
include 'db.php';

// ACCESS CONTROL
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login");
    exit();
}

include 'admin_ui.php';
include 'modules_lib.php';

$modules = modules_fetch($conn);

// Registration counts per module name (confirmed vs. all)
$counts = [];
$cq = mysqli_query($conn, "SELECT module_name, COUNT(*) AS total, SUM(status = 'Registered') AS confirmed FROM registrations GROUP BY module_name");
while ($c = mysqli_fetch_assoc($cq)) { $counts[$c['module_name']] = $c; }

// Values to refill the form with after a validation error
$old = $_SESSION['module_old'] ?? null;
unset($_SESSION['module_old']);

$js = [];
foreach ($modules as $m) {
    $c = $counts[$m['name']] ?? ['total' => 0, 'confirmed' => 0];
    $js[(int)$m['id']] = ['id' => (int)$m['id'], 'name' => $m['name'], 'tag' => $m['tag'], 'description' => $m['description'],
        'topics' => $m['topics'], 'instructor' => $m['instructor'], 'duration' => $m['duration'], 'fee' => (int)$m['fee'],
        'icon' => $m['icon'], 'color' => $m['color'], 'is_active' => (int)$m['is_active'], 'regs' => (int)$c['total']];
}

admin_page_start('Subjects', 'subjects', [
    'heading' => 'Subjects',
    'sub' => 'Create and manage the modules students can register for, and track how many are confirmed in each.',
]);
?>
    <div class="panel" style="margin-bottom:24px; padding:22px 26px;">
        <div class="panel-head" style="margin:0;">
            <div>
                <span class="eyebrow">Catalogue</span>
                <h2><?php echo count($modules); ?> <?php echo count($modules) === 1 ? 'module' : 'modules'; ?></h2>
            </div>
            <button type="button" class="btn btn-gold" onclick="openModule(null)"><i class="fas fa-plus"></i> New module</button>
        </div>
    </div>

    <?php if (!$modules): ?>
        <div class="panel"><div class="empty-state"><i class="fas fa-book-open"></i><p>No modules yet. Create the first one.</p></div></div>
    <?php else: ?>
    <div class="subject-grid">
        <?php foreach ($modules as $m):
            $c = $counts[$m['name']] ?? ['total' => 0, 'confirmed' => 0];
            $on = (int)$m['is_active'] === 1;
        ?>
            <div class="subject-card" style="border-top-color:<?php echo admin_h($m['color']); ?>;<?php echo $on ? '' : ' opacity:.85;'; ?>">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                    <div class="ico" style="background:<?php echo admin_h($m['color']); ?>; color:#fff;"><i class="fas <?php echo admin_h($m['icon']); ?>"></i></div>
                    <span class="status-pill <?php echo $on ? 'on' : 'off'; ?>"><?php echo $on ? 'Visible' : 'Hidden'; ?></span>
                </div>
                <h3><?php echo admin_h($m['name']); ?></h3>
                <div class="muted mono" style="white-space:normal;"><?php echo admin_h($m['tag']); ?> &middot; <?php echo number_format((int)$m['fee']); ?> Tsh<?php echo $m['instructor'] !== '' ? ' &middot; ' . admin_h($m['instructor']) : ''; ?></div>
                <div class="num"><?php echo (int)$c['confirmed']; ?></div>
                <div class="lbl">Confirmed students</div>
                <div class="action-group" style="margin-top:auto;">
                    <button type="button" class="btn btn-outline" onclick="openModule(<?php echo (int)$m['id']; ?>)"><i class="fas fa-pen"></i> Edit</button>
                    <a href="admin_subject_details?name=<?php echo urlencode($m['name']); ?>" class="btn btn-gold">Class list <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

<div class="drawer-backdrop" id="drawerBackdrop" onclick="closeModule()"></div>
<aside class="drawer wide" id="drawer" role="dialog" aria-modal="true" aria-labelledby="drawerTitle" aria-hidden="true">
    <div class="drawer-head">
        <div class="who"><span class="eyebrow" id="drawerEyebrow">New module</span><h2 id="drawerTitle">Create a module</h2></div>
        <button type="button" class="drawer-close" onclick="closeModule()" aria-label="Close"><i class="fas fa-xmark"></i></button>
    </div>

    <div class="drawer-body">
        <div class="preview" id="pv">
            <div class="ico"><i class="fas fa-book-open" id="pvIcon"></i></div>
            <div><div class="t" id="pvTag">MOD-NEW</div><h4 id="pvName">Module name</h4><div class="f" id="pvFee">5,000 Tsh</div></div>
        </div>

        <form method="POST" action="admin_module_save" id="moduleForm" autocomplete="off">
            <input type="hidden" name="csrf" value="<?php echo admin_h(admin_csrf()); ?>">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" id="f_id" value="0">

            <div class="f-row">
                <label class="f-label" for="f_name">Module name</label>
                <input class="f-input" id="f_name" name="name" maxlength="100" required placeholder="e.g. Cyber Security Fundamentals">
            </div>

            <div class="f-row">
                <label class="f-label" for="f_description">Description</label>
                <textarea class="f-input" id="f_description" name="description" maxlength="2000" required placeholder="What is this module about, and why should a student take it?"></textarea>
            </div>

            <div class="f-row">
                <label class="f-label" for="f_topics">What students will learn <small>(one topic per line, up to 12)</small></label>
                <textarea class="f-input" id="f_topics" name="topics" placeholder="Network attacks and defences&#10;Cryptography basics&#10;Secure coding practices"></textarea>
            </div>

            <div class="f-row f-row2">
                <div>
                    <label class="f-label" for="f_instructor">Instructor <small>(optional)</small></label>
                    <input class="f-input" id="f_instructor" name="instructor" maxlength="100" placeholder="e.g. Dr. A. Mushi">
                </div>
                <div>
                    <label class="f-label" for="f_duration">Duration <small>(optional)</small></label>
                    <input class="f-input" id="f_duration" name="duration" maxlength="50" placeholder="e.g. 12 weeks">
                </div>
            </div>

            <div class="f-row f-row2">
                <div>
                    <label class="f-label" for="f_fee">Registration fee (Tsh)</label>
                    <input class="f-input" id="f_fee" name="fee" type="number" min="0" max="10000000" step="1" required value="5000" inputmode="numeric">
                </div>
                <div>
                    <label class="f-label" for="f_tag">Module code <small>(auto if blank)</small></label>
                    <input class="f-input" id="f_tag" name="tag" maxlength="20" placeholder="MOD-07">
                </div>
            </div>

            <div class="f-row">
                <span class="f-label">Icon</span>
                <div class="icon-grid">
                    <?php foreach (module_icons() as $cls => $label): ?>
                        <label title="<?php echo admin_h($label); ?>"><input type="radio" name="icon" value="<?php echo $cls; ?>" aria-label="<?php echo admin_h($label); ?>"><span><i class="fas <?php echo $cls; ?>"></i></span></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="f-row">
                <span class="f-label">Colour</span>
                <div class="swatches">
                    <?php foreach (module_colors() as $col): ?>
                        <label><input type="radio" name="color" value="<?php echo $col; ?>" aria-label="Colour <?php echo $col; ?>"><span style="background:<?php echo $col; ?>"></span></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="f-row">
                <div class="switch-row">
                    <div><strong>Visible to students</strong><small>Turn off to keep it as a draft. Students won't see it.</small></div>
                    <label class="switch"><input type="checkbox" name="is_active" id="f_active" value="1"><i></i></label>
                </div>
            </div>
        </form>

        <div class="danger-zone" id="dangerZone" style="display:none;">
            <span class="eyebrow">Manage</span>
            <div class="row">
                <form method="POST" action="admin_module_save">
                    <input type="hidden" name="csrf" value="<?php echo admin_h(admin_csrf()); ?>">
                    <input type="hidden" name="action" value="toggle"><input type="hidden" name="id" id="t_id">
                    <button class="btn btn-outline" id="toggleBtn" type="submit"></button>
                </form>
                <form method="POST" action="admin_module_save" onsubmit="return confirm('Delete this module permanently?');">
                    <input type="hidden" name="csrf" value="<?php echo admin_h(admin_csrf()); ?>">
                    <input type="hidden" name="action" value="delete"><input type="hidden" name="id" id="d_id">
                    <button class="btn btn-reject" id="deleteBtn" type="submit"><i class="fas fa-trash"></i> Delete</button>
                </form>
            </div>
            <p id="dangerNote"></p>
        </div>
    </div>

    <div class="drawer-foot split">
        <button type="button" class="btn btn-outline" onclick="closeModule()">Cancel</button>
        <button type="submit" form="moduleForm" class="btn btn-gold" id="saveBtn"><i class="fas fa-check"></i> Save module</button>
    </div>
</aside>

<script>
    var MODULES = <?php echo json_encode($js, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var OLD = <?php echo json_encode($old, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var DEFAULTS = { id: 0, name: '', tag: '', description: '', topics: '', instructor: '', duration: '', fee: 5000, icon: 'fa-book-open', color: '#2563eb', is_active: 1, regs: 0 };
    var lastFocus = null;
    var $ = function (id) { return document.getElementById(id); };

    function fmt(n) { return Number(n || 0).toLocaleString('en-US'); }

    function preview() {
        var color = (document.querySelector('input[name=color]:checked') || {}).value || '#2563eb';
        var icon = (document.querySelector('input[name=icon]:checked') || {}).value || 'fa-book-open';
        $('pv').style.setProperty('--pv', color);
        $('pvIcon').className = 'fas ' + icon;
        $('pvName').textContent = $('f_name').value.trim() || 'Module name';
        $('pvTag').textContent = $('f_tag').value.trim() || ($('f_id').value > 0 ? '' : 'MOD-NEW');
        $('pvFee').textContent = fmt($('f_fee').value) + ' Tsh';
    }

    function fill(m, isEdit) {
        $('f_id').value = m.id || 0;
        ['name', 'tag', 'description', 'topics', 'instructor', 'duration', 'fee'].forEach(function (k) { $('f_' + k).value = m[k] == null ? '' : m[k]; });
        var ic = document.querySelector('input[name=icon][value="' + m.icon + '"]') || document.querySelector('input[name=icon]');
        ic.checked = true;
        var co = document.querySelector('input[name=color][value="' + m.color + '"]') || document.querySelector('input[name=color]');
        co.checked = true;
        $('f_active').checked = !!Number(m.is_active);
        $('drawerEyebrow').textContent = isEdit ? 'Edit module' : 'New module';
        $('drawerTitle').textContent = isEdit ? m.name : 'Create a module';
        $('saveBtn').innerHTML = '<i class="fas fa-check"></i> ' + (isEdit ? 'Save changes' : 'Create module');
        var dz = $('dangerZone');
        dz.style.display = isEdit ? '' : 'none';
        if (isEdit) {
            var live = Number(m.is_active) === 1;
            $('t_id').value = m.id; $('d_id').value = m.id;
            $('toggleBtn').innerHTML = live ? '<i class="fas fa-eye-slash"></i> Hide from students' : '<i class="fas fa-eye"></i> Show to students';
            var canDelete = Number(m.regs) === 0;
            $('deleteBtn').disabled = !canDelete; $('deleteBtn').style.opacity = canDelete ? '' : '.5';
            $('dangerNote').textContent = canDelete ? 'Nobody has registered yet, so this module can be deleted.'
                : m.regs + ' registration' + (m.regs === 1 ? ' exists' : 's exist') + ', so it can only be hidden, not deleted.';
        }
        preview();
    }

    function openModule(id, prefill) {
        lastFocus = document.activeElement;
        if (prefill) { if (prefill.id > 0 && MODULES[prefill.id]) prefill.regs = MODULES[prefill.id].regs; fill(prefill, prefill.id > 0); if (prefill.id > 0 && MODULES[prefill.id]) { $('drawerTitle').textContent = MODULES[prefill.id].name; } }
        else if (id) { fill(MODULES[id], true); }
        else { fill(DEFAULTS, false); }
        $('drawerBackdrop').classList.add('open');
        var d = $('drawer'); d.classList.add('open'); d.setAttribute('aria-hidden', 'false');
        document.body.classList.add('no-scroll');
        setTimeout(function () { $('f_name').focus(); }, 320);
    }

    function closeModule() {
        $('drawerBackdrop').classList.remove('open');
        var d = $('drawer'); d.classList.remove('open'); d.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('no-scroll');
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }

    ['f_name', 'f_tag', 'f_fee'].forEach(function (id) { $(id).addEventListener('input', preview); });
    document.querySelectorAll('input[name=icon], input[name=color]').forEach(function (el) { el.addEventListener('change', preview); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModule(); });

    // Guard against double-submits without dropping the button's data (see README: defer the disable)
    $('moduleForm').addEventListener('submit', function () {
        var b = $('saveBtn'); setTimeout(function () { b.disabled = true; b.style.opacity = '.75'; }, 0);
    });

    if (OLD) { openModule(null, OLD); }
</script>
<?php admin_page_end();
