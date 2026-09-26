<?php
// Module catalogue stored in the `modules` table (managed by admins on the Subjects page).
// Creates the table and seeds the original modules automatically the first time it is needed,
// so existing installs upgrade without a manual migration.

function module_icons() {
    return [
        'fa-database' => 'Database', 'fa-microchip' => 'Hardware / OS', 'fa-network-wired' => 'Networking',
        'fa-code' => 'Programming', 'fa-square-root-variable' => 'Mathematics', 'fa-shield-alt' => 'Security',
        'fa-laptop-code' => 'Development', 'fa-chart-line' => 'Statistics', 'fa-brain' => 'AI / ML',
        'fa-cloud' => 'Cloud', 'fa-mobile-alt' => 'Mobile', 'fa-book-open' => 'General',
        'fa-flask' => 'Science', 'fa-language' => 'Languages', 'fa-scale-balanced' => 'Law / Business',
        'fa-palette' => 'Design',
    ];
}

function module_colors() {
    return ['#2563eb', '#dc2626', '#059669', '#7c3aed', '#ea580c', '#0891b2', '#c99a3b', '#be185d'];
}

function modules_ensure($conn) {
    static $done = false;
    if ($done) return;
    $done = true;

    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS modules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL UNIQUE,
        tag VARCHAR(20) NOT NULL DEFAULT '',
        description TEXT NOT NULL,
        topics TEXT NOT NULL,
        instructor VARCHAR(100) NOT NULL DEFAULT '',
        duration VARCHAR(50) NOT NULL DEFAULT '',
        fee INT NOT NULL DEFAULT 5000,
        icon VARCHAR(40) NOT NULL DEFAULT 'fa-book-open',
        color VARCHAR(9) NOT NULL DEFAULT '#2563eb',
        image VARCHAR(100) NOT NULL DEFAULT '',
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $r = mysqli_query($conn, "SELECT COUNT(*) FROM modules");
    if ($r && (int)mysqli_fetch_row($r)[0] === 0) {
        // The modules the app shipped with. Only the two that students could already see start visible.
        $seed = [
            ['Database System', 'MOD-01', "Master the art of data management. In today's world, data is the new oil, and knowing how to store, retrieve, and secure it is a vital skill for any IT professional.", "Entity Relationship Diagrams (ERD)\nAdvanced SQL Queries\nDatabase Normalization (1NF, 2NF, 3NF)\nTransaction Management & Concurrency Control", 'fa-database', '#2563eb', 'db.jpg', 1],
            ['Function of Single Varriable', 'MOD-02', "The mathematics of change. Calculus is essential for engineering, physics, and computer science. We make complex derivatives and integrals easy to understand.", "Limits and Continuity\nRules of Differentiation\nApplications of Integrals\nInfinite Sequences and Series", 'fa-square-root-variable', '#ea580c', 'function.jpg', 1],
            ['OS', 'MOD-03', "Go behind the scenes of computing. Learn how Operating Systems manage hardware resources and provide a platform for application software to run smoothly.", "Process Scheduling & Threads\nMemory Management & Virtual Memory\nDeadlock Detection and Prevention\nFile Systems and Disk Management", 'fa-microchip', '#dc2626', 'os.jpg', 0],
            ['Networking', 'MOD-04', "The world is connected! Understand the protocols and technologies that allow computers to communicate across the globe, from local cables to the vast internet.", "OSI and TCP/IP Models\nIP Addressing and Subnetting\nRouting and Switching Protocols\nNetwork Security and Firewalls", 'fa-network-wired', '#059669', 'network.jpg', 0],
            ['Web Programming', 'MOD-05', "Build the modern web. From beautiful user interfaces to powerful server-side logic, this module prepares you to create full-stack web applications.", "Responsive Design with HTML5 & CSS3\nJavaScript & DOM Manipulation\nPHP Backend & MySQL Integration\nWeb Security Best Practices", 'fa-code', '#7c3aed', 'web.jpg', 0],
            ['Calculus', 'MOD-06', "An introduction to differentiation and integration and how they model change in the real world.", "Limits and Continuity\nDifferentiation\nIntegration\nSeries", 'fa-square-root-variable', '#ea580c', '', 0],
        ];
        $st = mysqli_prepare($conn, "INSERT IGNORE INTO modules (name, tag, description, topics, icon, color, image, is_active, fee) VALUES (?,?,?,?,?,?,?,?,5000)");
        foreach ($seed as $s) {
            mysqli_stmt_bind_param($st, 'sssssssi', $s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $s[6], $s[7]);
            mysqli_stmt_execute($st);
        }
        mysqli_stmt_close($st);
    }
}

// All modules (or only those visible to students), oldest first.
function modules_fetch($conn, $active_only = false) {
    modules_ensure($conn);
    $res = mysqli_query($conn, "SELECT * FROM modules " . ($active_only ? "WHERE is_active = 1 " : "") . "ORDER BY id ASC");
    $rows = [];
    while ($res && ($r = mysqli_fetch_assoc($res))) { $rows[] = $r; }
    return $rows;
}

function module_find($conn, $name, $active_only = false) {
    modules_ensure($conn);
    $sql = "SELECT * FROM modules WHERE name = ?" . ($active_only ? " AND is_active = 1" : "");
    $st = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($st, 's', $name);
    mysqli_stmt_execute($st);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
    mysqli_stmt_close($st);
    return $row ?: null;
}

function module_topics($row) {
    return array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$row['topics'])), 'strlen'));
}
