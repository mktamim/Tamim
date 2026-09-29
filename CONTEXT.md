# Portfolio Website - Project Context

**Live URL:** https://traveleyebangla.com  
**Repo:** C:\xampp\htdocs\Tamim  
**Hosting:** cPanel (traveleyeba_db)

---

## Tech Stack
- **PHP 8.1+** (vanilla, no framework)
- **MySQL/MariaDB** (PDO)
- **Bootstrap 5.3** + Custom CSS (assets/css/style.css)
- **Vanilla JS ES6+** (assets/js/main.js)
- **cPanel** hosting with PHP 8.1 handler

---

## Directory Structure
```
/public_html/
├── index.php              # Homepage (all sections)
├── blog.php               # Blog listing
├── blog-details.php       # Single post view
├── project.php            # Project detail
├── .htaccess              # Rewrite rules, security, HTTPS
├── .env                   # DB credentials, APP_URL, APP_DEBUG
├── admin/                 # Admin panel
│   ├── index.php          # Dashboard
│   ├── login.php          # Login
│   ├── logout.php         # Logout
│   ├── blog/              # Blog CRUD
│   ├── skills/            # Skills CRUD
│   ├── services/          # Services CRUD
│   ├── projects/          # Projects CRUD
│   ├── experience/        # Experience CRUD
│   ├── education/         # Education CRUD
│   ├── testimonials/      # Testimonials CRUD
│   ├── messages/          # Contact messages
│   ├── settings/          # Site settings (general, hero, about, theme, seo)
│   └── homepage/          # Homepage sections settings
├── includes/
│   ├── bootstrap.php      # App entry: config, DB, auto-fix site_url
│   ├── functions.php      # ALL helper functions (see below)
│   ├── header.php         # Navbar, CSS, meta, CSP
│   ├── footer.php         # Footer, JS
│   ├── live-chat-widget.php # Chat widget (cookie/session persistence)
│   └── admin_header.php   # Admin layout
├── config/
│   ├── app.php            # Main config (loads .env)
│   └── database.php       # DB credentials from .env
├── assets/
│   ├── css/style.css      # All styles (responsive, animations)
│   └── js/main.js         # Theme toggle, smooth scroll, etc.
└── assets/uploads/
    ├── settings/          # profile_image, favicon, logo
    ├── blog/              # Featured images
    ├── skills/            # Skill icons
    ├── projects/          # Project galleries
    └── testimonials/      # Client images
```

---

## Database (traveleyeba_db)

### Key Tables
| Table | Purpose |
|-------|---------|
| `settings` | Site config (site_url, profile_image, hero_*, developer_*, theme colors, etc.) |
| `blog_posts` | Blog articles |
| `blog_categories` | Blog categories |
| `skills` | Skills (name, category, percentage, icon) |
| `services` | Services offered |
| `projects` | Portfolio projects |
| `testimonials` | Client feedback |
| `admins` | Admin users |
| `live_chats` | Chat sessions |
| `live_chat_messages` | Chat messages |

### Settings Keys (group_name)
- **general:** site_url, site_name, developer_name, developer_title, developer_email, profile_image, favicon, logo
- **hero:** hero_badge, hero_subtitle, hero_cta_text, hero_cta_link, hero_cta2_text, hero_cta2_link
- **about:** about_short_intro, about_detailed_bio, stat_*
- **theme:** primary_color, secondary_color, background_color, text_color, hero_background_color
- **seo:** seo_title, seo_description, seo_keywords

---

## Key Functions (includes/functions.php)

### Database
- `db(): PDO` - singleton connection
- `db_one(string $sql, array $params): array|false`
- `db_all(string $sql, array $params): array`
- `db_execute(string $sql, array $params): bool`

### Settings & Content
- `setting(string $key, $default = null)` - get setting value (caches)
- `skills(string $category = null): array` - active skills, optional category filter
- `services(bool $featuredOnly = false): array`
- `projects(array $options = []): array`
- `testimonials(): array`
- `blog_categories(): array`
- `social_links(): array`

### Utilities
- `upload_image(array $file, string $subdir): array` - handles JPG/PNG/WebP, GD fallback
- `delete_file(string $path): bool`
- `redirect(string $url, string $message = '', string $type = 'success')` - with session_write_close()
- `csrf_field(): string`, `csrf_verify(string $token): bool`
- `format_date(string $date, string $format = 'M d, Y'): string`
- `slugify(string $text): string`
- `e(mixed $value): string` - HTML escape

### Pagination
- `paginate(int $total, int $perPage, int $currentPage, string $baseUrl): array`
- `render_pagination(array $pagination): string`

---

## URL Structure (via .htaccess)

| Pattern | Target |
|---------|--------|
| `/` | index.php |
| `/blog/` | blog.php (listing) |
| `/blog/post-slug/` | blog.php?slug=post-slug |
| `/blog/category/cat-slug/` | blog.php?category=cat-slug |
| `/project/project-slug/` | project.php?slug=project-slug |
| `/admin/login` | admin/login.php |
| `/admin/blog/` | admin/blog/index.php |
| `/admin/skills/` | admin/skills/index.php |
| `/admin/settings/` | admin/settings/index.php |
| `/api/live-chat/init` | api/live-chat/init.php |
| `/api/live-chat/send` | api/live-chat/send.php |
| `/api/live-chat/messages` | api/live-chat/messages.php |

---

## Admin Panel
- **URL:** `/admin/login.php`
- **Default:** admin@example.com / `password` (change immediately)
- **Sections:** Dashboard, Blog, Skills, Services, Projects, Experience, Education, Testimonials, Messages, Settings, Homepage
- **Auth:** session-based, CSRF protected

---

## Important Fixes & Features

### 1. Auto-fix site_url (bootstrap.php)
```php
// On every page load in production: if site_url is localhost/empty → upsert to current domain
```

### 2. Image Upload Robust (functions.php)
- GD extension optional: falls back to original JPG/PNG if WebP conversion fails
- Filename sanitization: `uniqid('', true) . '_' . time() . '.' . $ext`
- Detailed error_log for debugging

### 3. Mobile Hero Padding (index.php inline JS)
```javascript
// Dynamic padding = navbar.offsetHeight + 20px
// Updates on resize, navbar collapse/expand
```

### 4. Live Chat Session Persistence
- `redirect()` calls `session_write_close()` before header()
- API endpoints set `SameSite=Lax`, `Secure`, `HttpOnly` cookies
- Widget uses `credentials: 'include'` on all fetch calls

### 5. Skills Section (index.php)
- 3 categories: Frontend, Backend, Networking
- 3 per row on desktop (`col-4`), 2 on tablet (`col-sm-6`), 1 on mobile
- Centered headers, matching Services card style (hover animation, icon color change)

---

## Deployment Checklist

### cPanel File Manager Uploads Required:
- `public_html/.htaccess`
- `public_html/index.php`
- `public_html/blog.php`
- `public_html/includes/functions.php`
- `public_html/includes/bootstrap.php`
- `public_html/includes/header.php`
- `public_html/admin/settings/index.php`
- `public_html/admin/blog/create.php`, `edit.php`
- `public_html/assets/css/style.css`
- `public_html/assets/js/main.js`

### phpMyAdmin:
- `settings` table: ensure `site_url = 'https://traveleyebangla.com'`
- `blog_posts`: status = 'published', published_at set

### cPanel Settings:
- PHP Version: 8.1+ (Select PHP Version)
- Extensions: `gd` (enable for WebP), `pdo_mysql`, `mbstring`, `openssl`
- File Manager: `assets/uploads/` permissions 755

---

## Environment (.env)
```ini
APP_NAME="Tamim Portfolio"
APP_URL=https://traveleyebangla.com
APP_DEBUG=false
APP_TIMEZONE=Asia/Dhaka

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=traveleyeba_db
DB_USERNAME=traveleyeba_traveleyeba
DB_PASSWORD=Trav3l3y32024Secure
DB_CHARSET=utf8mb4

SESSION_LIFETIME=7200
SESSION_SECURE=true
SESSION_HTTPONLY=true
SESSION_SAMESITE=Lax
```

---

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Blank page on /blog/ | Check Error Log; usually missing .htaccess upload or DB error |
| Images not showing | Check `site_url` in settings; verify file exists in uploads/ |
| Upload fails | Enable `gd` extension; check uploads/ permissions 755 |
| Live chat not working | Check cookies (SameSite/Lax), session_write_close in redirect() |
| Mobile hero overlap | Ensure dynamic padding JS loads; check navbar height |
| Admin login fails | Clear browser cookies; check session.save_path writable |

---

## Future Work / TODO
- [ ] Add sitemap.xml generation
- [ ] Implement blog search
- [ ] Add project filtering by category
- [ ] Dark mode toggle persistence
- [ ] Email notifications for contact form
- [ ] Analytics integration
- [ ] Backup script for DB + uploads

---

## Quick Commands for AI Agents

```bash
# Test DB connection
php -r "require 'includes/bootstrap.php'; var_dump(db()->query('SELECT 1')->fetch());"

# Check published posts
php test_blog.php

# Check settings
php test_settings.php

# Syntax check
php -l index.php
php -l blog.php
php -l includes/functions.php
```

---

*Last updated: 2026-09-29*  
*Keep this file updated with every significant change.*