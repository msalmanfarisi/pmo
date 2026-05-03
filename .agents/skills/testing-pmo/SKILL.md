---
name: testing-pmo
description: Test the PMO (Project Management Office) Laravel application end-to-end. Use when verifying UI features like notifications, profile, Gantt chart, Kanban, and other project management functionality.
---

# Testing PMO Application

## Prerequisites

- PHP 8.3 available at `/usr/local/php83/bin`
- SQLite database (pre-seeded)
- No external services required for basic testing

## Setup

```bash
export PATH="/usr/local/php83/bin:$PATH"
cd /home/ubuntu/repos/pmo
php artisan serve --host=0.0.0.0 --port=8000 &
```

## Devin Secrets Needed

None — the app uses a local SQLite database with seeded test data.

## Test Credentials

- **Email:** `admin@pmo.local`
- **Password:** `Admin@PMO2024!`
- **CAPTCHA:** Must read the 8-character alphanumeric code from the login page and type it exactly (case-sensitive)

## Login Flow

1. Navigate to `http://localhost:8000/login`
2. Enter email and password
3. Read the CAPTCHA code displayed on the page (8 chars, mixed case alphanumeric)
4. Type the CAPTCHA exactly as shown
5. Click "Sign in"

## Key Test Areas

### Notification Bell (top bar)
- Bell icon in top-right corner
- Click to open dropdown with "Notifications" header
- "Mark all as unread" / "Mark all as read" toggle
- Red badge appears when unread notifications exist
- Note: If testing toggle, you may need to seed notifications first via `php artisan tinker`

### User Profile Dropdown (top bar)
- Click "System Administrator" text with SA avatar
- Shows: Update Profile, Change Password, Sign Out (red text)
- No standalone logout icon should exist outside the dropdown
- Update Profile → `/profile` (name + email pre-filled)
- Change Password → `/profile/password` (3 password fields)

### Gantt Chart
- Navigate to `/projects/{id}/gantt`
- Three view modes: Daily, Weekly, Monthly
- Monthly is the default active view (blue button)
- **Daily view:** Individual day columns; Saturday/Sunday columns have `bg-red-50` background and `text-red-500` text color
- **Weekly view:** Shows week ranges like "6-12", "13-19" grouped by month
- **Monthly view:** Shows month columns like "Jan 2025", "Feb 2025"
- Weekend verification can be done programmatically via browser console to check CSS classes

### Kanban Board
- Navigate to `/projects/{id}/kanban`
- 5 columns: To Do, In Progress, Review, Testing, Done
- Drag-and-drop via SortableJS

## CSP Notes

- The app uses strict Content Security Policy with nonce-based script loading
- Alpine.js v3 requires `'unsafe-eval'` in CSP script-src — this is expected
- Inline onclick handlers are NOT used; all event listeners are attached via `addEventListener`

## Common Issues

- **Session expiration:** If redirected to login, re-authenticate with CAPTCHA
- **Alpine.js dropdowns not working:** Check browser console for CSP errors. The `'unsafe-eval'` directive must be present in script-src
- **Gantt view buttons not responding:** Ensure JavaScript event listeners are loaded (check for CSP blocking)
