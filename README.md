# UNI·TUIT

A university tuition-registration portal. Students create an account, pick the modules they want to take, and submit them for approval. Administrators review every submission and confirm or reject it. Plain PHP and MySQL, with no framework and no build step.

- [How it works](#how-it-works)
- [Requirements](#requirements)
- [Setup on a new host](#setup-on-a-new-host) — Apache · Nginx · shared hosting
- [Updating an existing deployment](#updating-an-existing-deployment)
- [Clean URLs (no `.php`)](#clean-urls-no-php--read-this-if-links-404)
- [Creating the first admin](#creating-the-first-admin)
- [Configuration](#configuration)
- [Project layout](#project-layout)
- [Troubleshooting](#troubleshooting)
- [Security notes](#security-notes)

---

## How it works

```
 Student                                          Admin
 ───────                                          ─────
 Register / log in
 Pick modules ─────────►  Draft
 Press "Send to admin" ──►  Pending ───────────►  Sees it in Pending Approvals
                                                  Approve ──►  Registered
                                                  Reject  ──►  Rejected
 Sees the new status on their dashboard ◄─────────────────────┘
```

**Students** (`/home`) get a dashboard with their modules, status badges, and running total, plus a "Select Your Subjects" page.

**Admins** (`/admin`) get:

| Page | What it does |
|---|---|
| **Students** | Searchable list of every student. **View** opens a slide-over panel with their details and modules, with Approve and Reject buttons. |
| **Pending** | One queue of every submission waiting on a decision, across all students. |
| **Subjects** | Confirmed head-count per module, with the official class list for each. |

Admins and students use the **same login form**. An account whose `role` is `admin` is sent to the admin dashboard automatically.

---

## Requirements

| | Minimum | Notes |
|---|---|---|
| PHP | 8.0 | Developed and tested on 8.2. Needs the `mysqli` extension. |
| MySQL / MariaDB | MySQL 5.7 · MariaDB 10.3 | InnoDB, `utf8mb4`. |
| Web server | Apache 2.4 | With `mod_rewrite` and `AllowOverride All`. Nginx also works — see below. |
| HTTPS | Recommended | Required for anything public. Logins and sessions travel in cleartext without it. |

---

## Setup on a new host

### 1 · Get the code

```bash
git clone https://github.com/mamafure/UNI-TUIT.git
cd UNI-TUIT
```

Put the files in your web root (for example `/var/www/html`). **The app expects to live at the root of a domain or subdomain** (`https://example.com/`), not in a sub-folder — the links are root-relative.

### 2 · Create the database

```sql
CREATE DATABASE uni_tuit_db CHARACTER SET utf8mb4;
CREATE USER 'uni_tuit'@'localhost' IDENTIFIED BY 'choose-a-strong-password';
GRANT ALL PRIVILEGES ON uni_tuit_db.* TO 'uni_tuit'@'localhost';
FLUSH PRIVILEGES;
```

Then import the tables:

```bash
mysql -u uni_tuit -p uni_tuit_db < schema.sql
```

(In phpMyAdmin: select the database → **Import** → choose `schema.sql`.)

`schema.sql` creates structure only — **no data and no default login**.

### 3 · Point the app at the database

`db.php` reads four environment variables and falls back to local-development defaults:

| Variable | Default |
|---|---|
| `DB_HOST` | `localhost` |
| `DB_USER` | `root` |
| `DB_PASS` | *(empty)* |
| `DB_NAME` | `uni_tuit_db` |

Set them the way your host normally does — for Apache, in the virtual host:

```apache
SetEnv DB_HOST localhost
SetEnv DB_USER uni_tuit
SetEnv DB_PASS choose-a-strong-password
SetEnv DB_NAME uni_tuit_db
```

For PHP-FPM, use `env[DB_PASS] = ...` in the pool config. On **shared hosting** where you cannot set environment variables, just edit the four fallback values at the top of `db.php` directly, and never commit that change.

### 4 · Web server

#### Apache (recommended)

The repo ships a `.htaccess` that handles clean URLs and blocks internal files. Apache must allow it:

```bash
sudo a2enmod rewrite
```

```apache
<Directory /var/www/html>
    AllowOverride All
    Require all granted
</Directory>
```

Reload Apache (`sudo systemctl reload apache2`). That's the whole step.

#### Nginx

Nginx ignores `.htaccess`, so you must recreate its rules. Inside your `server { ... }` block:

```nginx
root /var/www/html;
index index.php index.html;

# Never serve internals, dumps, or dotfiles
location ~ /\.                                   { deny all; }
location ~* \.(sql|json|md|yml|yaml|sh|env|log|bak)$ { deny all; }
location ~* ^/(db|flash|admin_ui)\.php$          { deny all; }
location ^~ /docker/                             { deny all; }

# Old-style /page.php  ->  /page
location ~ ^/(.+)\.php$ {
    if ($request_method = GET) { return 301 /$1$is_args$args; }
    # non-GET (form posts) fall through to PHP
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php8.2-fpm.sock;   # adjust to your PHP version
}

# Clean URLs: /admin -> admin.php
location / {
    try_files $uri $uri/ $uri.php$is_args$args;
}

location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php8.2-fpm.sock;
}
```

Test with `sudo nginx -t`, then reload. If you would rather keep it simple, you can skip the redirect block; the app works either way, and only the address-bar cosmetics differ.

#### Shared hosting (cPanel, InfinityFree, etc.)

1. Upload all files to `public_html/` (or `htdocs/`) with the file manager or FTP. Include the `.htaccess` file — it is hidden by default, so enable "show hidden files".
2. Create a MySQL database in the control panel and import `schema.sql` with phpMyAdmin.
3. Edit the four defaults at the top of `db.php` with the host name, user, password and database name your provider gave you.
4. Most shared hosts already allow `.htaccess` and `mod_rewrite`. If pages only open with `.php` on the end, ask your provider to enable them.

### 5 · Create the first admin

See [Creating the first admin](#creating-the-first-admin), then open your site and log in.

---

## Updating an existing deployment

If the code is already running on your server (a clone of this repository in the web root), updating is:

```bash
cd /path/to/UNI-TUIT
git pull origin main
```

Then:

- **Nothing else** for most changes. PHP files take effect immediately.
- If a release changes the database, it will say so in the commit message. Apply the change by hand; existing data is never touched by `git pull`.
- If you use PHP opcache with `validate_timestamps=0`, reload PHP-FPM or Apache after pulling.
- Local edits to `db.php` (shared hosting) will conflict with a pull. Either use environment variables instead, or run `git stash` before pulling and `git stash pop` after.

Your `.htaccess` comes with the repository, so if URLs stop working after an update, first confirm `AllowOverride All` is still set (see [Troubleshooting](#troubleshooting)).

---

## Clean URLs (no `.php`) — read this if links 404

Every page is served **without** the `.php` extension: `/admin`, `/home`, `/admin_pending`. Old-style links such as `/admin.php` still work: they `301`-redirect to the clean address, and form posts are left alone so they never break.

This depends on the rewrite rules in `.htaccess`, which means:

- **Apache needs `mod_rewrite` enabled and `AllowOverride All`** for the web root.
- **Nginx needs the equivalent rules** from the [Nginx section](#nginx).
- If those are missing, `/admin` returns a 404. Either fix the server (preferred), or reach pages with `.php` on the end, which works everywhere but will not be pretty.

The same `.htaccess` also **hides internal files**, so `db.php`, `flash.php`, `admin_ui.php`, `schema.sql`, `*.json`, `*.md`, dotfiles and any `docker/` folder return `403`/`404` instead of being downloadable. If your server ignores `.htaccess`, those files are exposed. Run the check below after setting up:

```bash
for f in db.php flash.php schema.sql README.md .git/config; do
  printf "%-14s %s\n" "$f" "$(curl -s -o /dev/null -w '%{http_code}' https://YOUR-DOMAIN/$f)"
done
# every line should print 403 or 404, never 200
```

---

## Creating the first admin

The schema deliberately ships without a default admin, so there is no known password to change. Create your own.

**1. Generate a password hash** (PHP is enough; run it anywhere):

```bash
php -r "echo password_hash('your-strong-password', PASSWORD_DEFAULT), PHP_EOL;"
```

**2. Insert the admin row**, pasting the hash you just printed:

```sql
INSERT INTO users (username, phone, email, password, program, role)
VALUES ('Site Admin', '0700000000', 'you@example.com', '<PASTE_HASH_HERE>', 'Administration', 'admin');
```

**3. Log in** with that email and password on the normal login form. You'll land on `/admin`.

To promote an existing student instead: `UPDATE users SET role = 'admin' WHERE email = 'person@example.com';`

---

## Configuration

| What | Where |
|---|---|
| Database credentials | Environment variables, or the defaults in `db.php` |
| Available modules | The `$all_system_modules` list in `home.php` and the matching list in `admin_subjects.php` |
| Registration fee per module | `fee` column on each row (defaults to `5000` in `schema.sql`) |
| Colours, fonts | CSS variables at the top of each page's `<style>` block; the admin pages share one set in `admin_ui.php` |
| Phone number rule | `auth.php` — must be 10 digits starting with `06` or `07` |

---

## Project layout

```
index.php               Landing page + login / register modal
auth.php                Handles student registration and login (also routes admins)
admin_login.php         Standalone admin sign-in page (direct link; not shown on the landing page)
admin_auth.php          Handles the admin sign-in form

home.php                Student dashboard
subject_list.php        Browse and pick modules
subject_view.php        Module details + "add" action
process_submission.php  Student sends Draft modules to the admin (Draft → Pending)
logout.php

admin.php               Admin: student list + slide-over profile panel
admin_pending.php       Admin: pending approvals queue
admin_subjects.php      Admin: per-module statistics
admin_subject_details.php   Admin: class list for one module
admin_student_details.php   Admin: full-page student view
approve.php             Approve / reject action (admin only)
admin_ui.php            Shared admin layout, styles and reject dialog
flash.php               One-time toast messages

db.php                  Database connection + session start
schema.sql              Database structure (no data)
.htaccess               Clean URLs and protection of internal files
```

---

## Troubleshooting

**`/admin` (or any page without `.php`) shows "Not Found".**
Apache is not applying `.htaccess`. Enable `mod_rewrite` and set `AllowOverride All` for the web root (see [Apache](#apache-recommended)), then reload. Quick test: `/admin.php` should redirect to `/admin`.

**Every page shows a blank white screen.**
PHP errors are hidden. Check the server's PHP error log. The usual cause is a wrong database password or a missing `mysqli` extension (`php -m | grep mysqli`).

**"Connection failed" message.**
The credentials in the environment variables (or `db.php`) do not match the database. Confirm with `mysql -u USER -p -h HOST DBNAME`.

**I can log in but get sent back to the login page straight away.**
Sessions are not being saved. Make sure PHP's session directory is writable, and that no file has blank lines or a space before `<?php` (this breaks `session_start()`).

**Logged in as an admin but see the student dashboard.**
The account's `role` must be exactly `admin` (lowercase) in the `users` table.

**Registration says the email is already used.**
`email` is unique. Use a different one or log in instead.

**Approve or Reject does nothing.**
You must be logged in as an admin, and the request must be a normal browser click (these are GET links protected by the admin session).

---

## Security notes

Please read these before putting real students' data on a public server.

- **Use HTTPS.** Without it, passwords and session cookies can be read on the network.
- **Never commit real data.** Database exports (`*.sql`, `*.json`) are gitignored on purpose because they contain personal details and password hashes. `schema.sql` is the one exception because it contains structure only.
- **Never commit credentials.** Use environment variables for the database password.
- **Pick a strong admin password**, and do not reuse it elsewhere.
- **Some queries are built by string concatenation** (notably in the student login and registration handlers). Treat the app as a learning project and get an independent security review before relying on it for sensitive data. Converting those to prepared statements is the first thing to do.
- **Approve and Reject are GET links.** They require an admin session, but they are not protected by CSRF tokens.
