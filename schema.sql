-- UNI-TUIT database schema (structure only: no data, no default accounts).
-- Import into an empty database, e.g.:
--   mysql -u <user> -p <database> < schema.sql
-- or paste into phpMyAdmin > Import. See README.md for creating the first admin.

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL,
    phone      VARCHAR(20)  NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,              -- bcrypt hash from password_hash()
    program    VARCHAR(150) NOT NULL,
    role       VARCHAR(20)  NOT NULL DEFAULT 'student',   -- 'student' | 'admin'
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS registrations (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    module_name VARCHAR(150) NOT NULL,
    fee         INT NOT NULL DEFAULT 5000,
    status      VARCHAR(20) NOT NULL DEFAULT 'Draft',      -- Draft | Pending | Registered | Rejected
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    KEY user_id (user_id),
    CONSTRAINT registrations_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Only used by the legacy dashboard.php page.
CREATE TABLE IF NOT EXISTS student_modules (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    module_name VARCHAR(150) NOT NULL,
    CONSTRAINT student_modules_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
