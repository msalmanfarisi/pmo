---
name: testing-pmo
description: Test the PMO (Project Management Office) Laravel application end-to-end. Use when verifying UI, CRUD, or feature changes.
---

# Testing the PMO Application

## Prerequisites

- PHP 8.3+ with extensions: sqlite3, mbstring, xml, zip, gd, intl, bcmath, curl, sodium
- Composer dependencies installed (`composer install`)
- Node.js + npm for Vite asset build (`npm install && npm run build`)
- If PHP 8.3 is not available via PPA, it may need to be compiled from source at `/usr/local/php83/bin/php`. Add to PATH: `export PATH="/usr/local/php83/bin:$PATH"`

## Environment Setup

1. Copy `.env.example` to `.env` if not present
2. Configure SQLite for testing:
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database/database.sqlite
   ```
3. Create SQLite file: `touch database/database.sqlite`
4. Generate app key: `php artisan key:generate`
5. Run migrations and seed: `php artisan migrate:fresh --seed`
6. Start dev server: `php artisan serve --host=0.0.0.0 --port=8000`

## Default Credentials

- Email: `admin@pmo.local`
- Password: `Admin@PMO2024!`
- The login page has an 8-character alphanumeric CAPTCHA that must be entered exactly as displayed (case-sensitive)

## Test Flow (Primary Features)

Test in this order for a complete lifecycle:

1. **Login with CAPTCHA** — Navigate to `/login`. Test with wrong CAPTCHA first (expect error), then correct CAPTCHA (expect redirect to `/dashboard`)
2. **Dashboard** — Verify stats cards (Total Projects, Active Projects, My Open Tasks, Overdue Tasks) and user initials badge
3. **Create Project** — Go to `/projects`, click "New Project". Fill name, code, description, status, priority, plan start/end dates
4. **Create Sprint** — On project detail page, use "+ Add Sprint" form. Set name, dates, status
5. **Create Task** — Click "+ Add Task". Set title, type, status, priority, assignee, sprint, plan dates
6. **Add Comment** — Click task title to open detail, post a comment in the comments section
7. **Kanban Board** — Click "Kanban" button on project page. Verify 5 columns: Backlog, To Do, In Progress, In Review, Done
8. **Gantt Chart** — Click "Gantt" button. Verify monthly timeline columns and task bars
9. **S-Curve** — Click "S-Curve" button. Verify chart canvas renders with stat cards
10. **Reports/PDF Export** — Go to `/reports`, click PDF button for a project. Verify file downloads
11. **User Management** — Go to `/users`. Verify user table with name, email, role, status columns
12. **SMTP Settings** — Go to `/settings/smtp`. Verify form fields (mailer, host, port, encryption, etc.)
13. **Security Headers** — Use `curl -s -D - -o /dev/null http://localhost:8000/login` and verify CSP, HSTS, X-Frame-Options, X-Content-Type-Options headers

## Notification Bell (top bar)

- Bell icon in top-right corner
- Click to open dropdown with "Notifications" header
- "Mark all as unread" / "Mark all as read" toggle
- Red badge appears when unread notifications exist
- Note: If testing toggle, you may need to seed notifications first via `php artisan tinker`

## User Profile Dropdown (top bar)

- Click "System Administrator" text with SA avatar
- Shows: Update Profile, Change Password, Sign Out (red text)
- No standalone logout icon should exist outside the dropdown
- Update Profile → `/profile` (name + email pre-filled)
- Change Password → `/profile/password` (3 password fields)

## Gantt Chart Views

- Navigate to `/projects/{id}/gantt`
- Three view modes: Daily, Weekly, Monthly
- Monthly is the default active view (blue button)
- **Daily view:** Individual day columns; Saturday/Sunday columns have `bg-red-50` background and `text-red-500` text color
- **Weekly view:** Shows week ranges like "6-12", "13-19" grouped by month
- **Monthly view:** Shows month columns like "Jan 2025", "Feb 2025"
- Weekend verification can be done programmatically via browser console to check CSS classes

## CSP Notes

- The app uses strict Content Security Policy with nonce-based script loading
- Alpine.js v3 requires `'unsafe-eval'` in CSP script-src — this is expected
- Inline onclick handlers are NOT used; all event listeners are attached via `addEventListener`

## Tips

- CAPTCHA is case-sensitive and expires after 5 minutes. Read the displayed code carefully before typing
- The CAPTCHA refresh button (circular arrow icon) generates a new code if the current one is hard to read
- For PDF export testing, the file naming convention is `project-{CODE}-report.pdf`
- Security headers are set by the `SecurityHeaders` middleware in `app/Http/Middleware/SecurityHeaders.php`
- `X-Powered-By` header may be exposed by PHP runtime — this requires `expose_php = Off` in `php.ini`, not an app-level fix
- Kanban drag-and-drop uses SortableJS — testing drag events via browser automation may be unreliable; visual column verification is sufficient
- The app uses Spatie Permission for RBAC. Roles: admin, manager, member, viewer

## Common Issues

- **Session expiration:** If redirected to login, re-authenticate with CAPTCHA
- **Alpine.js dropdowns not working:** Check browser console for CSP errors. The `'unsafe-eval'` directive must be present in script-src
- **Gantt view buttons not responding:** Ensure JavaScript event listeners are loaded (check for CSP blocking)

## Devin Secrets Needed

No secrets required for local testing with SQLite. The default admin credentials are seeded by the application.
