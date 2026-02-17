# Deployment Guide — Field Connect

## Prerequisites
- PHP 8.1+
- MySQL 5.7+ / MariaDB
- Composer
- Node.js 18+ (for frontend assets)
- Web server (Nginx/Apache)

---

## Backend Setup

### 1. Install Dependencies
```bash
cd beneficiary_app
composer install
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```
DB_DATABASE=beneficiary_app
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

SANCTUM_STATEFUL_DOMAINS=your-domain.com
SESSION_DOMAIN=.your-domain.com
```

### 3. Database
```bash
php artisan migrate --seed
php artisan storage:link
```

### 4. Permissions (Linux)
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Mobile Web App

The mobile app is at `/public/mobile/`. No build step needed — it's a static PWA.

### Access
```
https://your-domain.com/mobile/
```

### Files
```
public/mobile/
├── index.html       # HTML shell
├── styles.css       # Styles
├── api.js           # API client & state
├── screens.js       # Splash, Login, Dashboard
├── screens2.js      # Projects, Forms, History, Drafts, Detail
├── app.js           # Event binding & logic
└── manifest.json    # PWA manifest
```

---

## Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/beneficiary_app/public;
    index index.php index.html;

    # Mobile app
    location /mobile/ {
        try_files $uri $uri/ /mobile/index.html;
    }

    # Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## API Routes Summary

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/api/v1/login` | POST | No | Login |
| `/api/v1/logout` | POST | Yes | Logout |
| `/api/v1/me` | GET | Yes | Profile |
| `/api/v1/volunteer/dashboard` | GET | Yes | Stats |
| `/api/v1/volunteer/projects` | GET | Yes | Projects |
| `/api/v1/project/{id}/packages` | GET | Yes | Packages |
| `/api/v1/sync-status` | GET | Yes | Sync info |
| `/api/v1/beneficiaries` | POST | Yes | Create |
| `/api/v1/beneficiaries/my-submissions` | GET | Yes | My list |
| `/api/v1/beneficiary/{id}` | GET | Yes | Detail |
| `/api/v1/beneficiary/{id}` | PUT | Yes | Resubmit |
| `/api/v1/beneficiary/{id}/review` | POST | Yes | Review |
| `/api/v1/upload` | POST | Yes | Upload doc |
| `/api/v1/notifications` | GET | Yes | List |
| `/api/v1/notifications/unread-count` | GET | Yes | Count |
| `/api/v1/notifications/{id}/read` | POST | Yes | Mark read |
| `/api/v1/notifications/mark-all-read` | POST | Yes | Mark all |

---

## Test Accounts (from seeder)
| Email | Password | Role |
|-------|----------|------|
| admin@stitch.org | password | super_admin |
| manager@stitch.org | password | manager |
| volunteer@stitch.org | password | volunteer |
