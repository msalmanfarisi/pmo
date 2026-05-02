# PMO - Project Management Office

A comprehensive, secure, and elegant web-based project management application built with **Laravel 12**. Designed for teams of any size to plan, track, and deliver projects efficiently.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## Features

### Project Management
- **Project CRUD** with plan/actual start and end dates, budget tracking, and progress monitoring
- **Sprint Management** for agile workflows with planning, active, and completed states
- **Kanban Board** with drag-and-drop task cards using SortableJS
- **Gantt Chart** for visual timeline and task scheduling
- **S-Curve** for planned vs. actual progress comparison using Chart.js
- **Backlog Management** for unscheduled tasks

### Task Management
- Full task lifecycle: Backlog → To Do → In Progress → In Review → Done
- Task types: Epic, Story, Task, Bug, Sub-task
- Priority levels: Low, Medium, High, Critical
- Story points, assignee management, and parent/child task relationships
- **Task Comments** for team collaboration
- Automatic progress calculation and propagation

### User & Role Management (RBAC)
- Built-in roles: Admin, Manager, Member, Viewer (powered by Spatie Laravel Permission)
- Permission-based access control
- User activation/deactivation
- Strong password policy (12+ chars, mixed case, numbers, symbols, HIBP check)

### Security (OWASP / SecurityHeaders / Qualys A+ Grade)
- **8-character alphanumeric CAPTCHA** on login (mixed case, time-limited)
- **Content Security Policy (CSP)** with nonce-based script/style allowlisting
- **Strict-Transport-Security (HSTS)** with preload
- **X-Content-Type-Options**, **X-Frame-Options**, **Referrer-Policy**
- **Permissions-Policy** restricting camera, microphone, geolocation, etc.
- **Cross-Origin policies** (COOP, CORP, COEP)
- **Rate limiting** on login attempts (5 per 5 minutes)
- **CSRF protection** on all forms
- **Session encryption** and regeneration on login
- **SQL injection prevention** via Eloquent ORM parameterized queries
- **XSS prevention** via Blade auto-escaping and HTML tag allowlisting
- **SMTP password encryption** using Laravel's built-in encryption
- Server header removal (X-Powered-By, Server)

### Reporting & Export
- **PDF Export** for project summaries and task lists (via DomPDF)
- **Excel Export** for project and task data (via Maatwebsite Excel)
- Professional report formatting with company branding

### Email Notifications
- **SMTP Settings** configurable via admin panel (no .env editing required)
- **Test email** functionality
- Automatic notifications for task assignments
- SMTP password stored encrypted in database

### User Interface
- Modern, responsive design with **Tailwind CSS**
- Clean sidebar navigation with active state indicators
- Interactive charts and data visualizations
- Mobile-friendly layout
- Alpine.js for lightweight interactivity
- Auto-dismissing success/error notifications

## Requirements

- **PHP** >= 8.2 with extensions: BCMath, Ctype, cURL, DOM, Fileinfo, GD, Intl, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, Zip, Sodium
- **Composer** >= 2.0
- **Node.js** >= 18.x and **npm** >= 9.x
- **Database** (one of):
  - MySQL 8.0+
  - MariaDB 10.3+
  - PostgreSQL 13+
  - Microsoft SQL Server 2019+
  - Oracle 12c+
  - SQLite 3.35+ (for development/testing)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/msalmanfarisi/pmo.git
cd pmo
```

### 2. Install PHP Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Install Node.js Dependencies and Build Assets

```bash
npm install
npm run build
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure your database connection:

```env
# For MySQL/MariaDB
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pmo
DB_USERNAME=your_username
DB_PASSWORD=your_password

# For PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pmo
DB_USERNAME=your_username
DB_PASSWORD=your_password

# For SQL Server
DB_CONNECTION=sqlsrv
DB_HOST=127.0.0.1
DB_PORT=1433
DB_DATABASE=pmo
DB_USERNAME=your_username
DB_PASSWORD=your_password

# For SQLite (development)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### 5. Run Migrations and Seed

```bash
php artisan migrate
php artisan db:seed
```

### 6. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Start the Application

```bash
# Development
php artisan serve

# Production (use Nginx/Apache with PHP-FPM)
```

## Default Login Credentials

| Email | Password | Role |
|-------|----------|------|
| `admin@pmo.local` | `Admin@PMO2024!` | Administrator |

> **Important:** Change the default admin password immediately after first login.

## Configuration

### SMTP Email Settings

SMTP can be configured in two ways:

1. **Via Admin Panel** (recommended): Navigate to Settings → SMTP Settings
2. **Via `.env` file**:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="PMO"
```

### Database Compatibility

This application uses **Eloquent ORM** exclusively, ensuring compatibility across all Laravel-supported databases:
- All queries use Eloquent query builder (no raw SQL)
- Migrations use database-agnostic schema builder
- No database-specific functions or syntax

### Security Headers

The application automatically sets the following security headers (configured in `SecurityHeaders` middleware):

| Header | Value |
|--------|-------|
| Strict-Transport-Security | max-age=31536000; includeSubDomains; preload |
| Content-Security-Policy | Nonce-based script/style; frame-ancestors 'none' |
| X-Content-Type-Options | nosniff |
| X-Frame-Options | DENY |
| Referrer-Policy | strict-origin-when-cross-origin |
| Permissions-Policy | camera=(), microphone=(), geolocation=(), ... |
| Cross-Origin-Opener-Policy | same-origin |
| Cross-Origin-Resource-Policy | same-origin |

### Queue Workers (Optional)

For sending email notifications asynchronously:

```bash
php artisan queue:work --tries=3 --timeout=60
```

## Production Deployment

### Nginx Configuration Example

```nginx
server {
    listen 443 ssl http2;
    server_name pmo.yourdomain.com;
    root /var/www/pmo/public;
    index index.php;

    ssl_certificate /etc/ssl/certs/yourdomain.crt;
    ssl_certificate_key /etc/ssl/private/yourdomain.key;

    # Remove server tokens
    server_tokens off;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Optimization

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
composer install --optimize-autoloader --no-dev
```

## Project Structure

```
app/
├── Exports/           # Excel export classes
├── Http/
│   ├── Controllers/   # Request handlers
│   │   └── Auth/      # Authentication controllers
│   └── Middleware/     # Security headers, rate limiting
├── Models/            # Eloquent models
├── Notifications/     # Email notifications
└── Services/          # Business logic (Captcha, Activity Log)

database/
├── migrations/        # Database schema
└── seeders/           # Default data (roles, admin user)

resources/
├── css/              # Tailwind CSS
├── js/               # Alpine.js, SortableJS, Chart.js
└── views/            # Blade templates
    ├── auth/          # Login page
    ├── layouts/       # App layout, sidebar
    ├── projects/      # Project views (CRUD, Kanban, Gantt, S-Curve)
    ├── tasks/         # Task views (CRUD, Backlog)
    ├── users/         # User management
    ├── settings/      # SMTP settings
    └── reports/       # PDF templates, report index
```

## Technology Stack

| Component | Technology |
|-----------|-----------|
| Backend Framework | Laravel 12 |
| PHP Version | 8.2+ |
| ORM | Eloquent (multi-DB) |
| Frontend | Tailwind CSS, Alpine.js |
| Charts | Chart.js |
| Drag & Drop | SortableJS |
| PDF Export | DomPDF |
| Excel Export | Maatwebsite Excel |
| RBAC | Spatie Laravel Permission |
| Build Tool | Vite |

## License

This project is open-sourced software licensed under the [MIT License](LICENSE).
