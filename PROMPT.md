# Tamim Portfolio — Implementation Prompt

## Project
Build a modern, dynamic personal portfolio website for a professional web developer.

## Technology
- PHP 8+
- MySQL or MariaDB
- HTML5
- CSS3
- Vanilla JavaScript
- No Laravel or heavy frontend framework
- Lightweight, mobile-first, secure, and easy to maintain

## Design Direction
- Minimal, modern, professional, and readable
- Mobile-first responsive layout
- Restrained animations and transitions
- Strong typography, generous spacing, and clear visual hierarchy
- Accessible navigation, forms, focus states, and semantic HTML

## Public Website
Create these routes and sections:

- `/` — Home
- `/about` — About
- `/skills` — Skills
- `/services` — Services
- `/projects` — Projects
- `/project/{slug}` — Project details
- `/experience` — Experience
- `/education` — Education
- `/testimonials` — Testimonials
- `/blog` — Journal
- `/blog/{slug}` — Article
- `/contact` — Contact form

Home should include Hero, About preview, Services, Featured Projects, Skills, Testimonials, and Contact CTA.

All public content must be loaded from the database where applicable. Project and article detail pages must use clean slugs.

## Admin Panel
Create one secure admin area with:

- `/admin/login`
- `/admin/dashboard`
- `/admin/settings`
- `/admin/skills`
- `/admin/services`
- `/admin/projects`
- `/admin/experience`
- `/admin/education`
- `/admin/testimonials`
- `/admin/messages`
- `/admin/blog-posts`

Admin features:
- Secure login with password hashing and session regeneration
- CSRF protection for all mutating forms
- Login attempt throttling
- Dashboard statistics and recent activity
- Site settings CRUD
- CRUD for skills, services, projects, experience, education, testimonials, messages, and blog posts
- Validated image uploads for projects, testimonials, and blog posts
- Draft/published blog post status
- Message read/unread management

## Security
- Prepared PDO statements
- Output escaping
- CSRF tokens
- Secure session cookies
- Password hashing with `password_hash()` and `password_verify()`
- Input validation and length limits
- Safe redirect handling
- Upload MIME, size, and dimension validation
- Unique upload filenames
- No secrets committed to Git

## SEO and Performance
- Dynamic title and meta description
- Open Graph tags
- Canonical URL
- Clean URLs through `.htaccess`
- Responsive images and lazy loading where appropriate
- Optimized CSS and lightweight JavaScript
- Browser caching headers where appropriate

## Database
Use tables for settings, skills, services, projects, experience, education, testimonials, messages, blog posts, and admins.

Provide:
- `database/schema.sql`
- `database/seed.sql`
- `database/install.php`

Default local database name: `tamim_portfolio`.

## Project Structure
```text
admin/
  assets/
  includes/
  pages/
assets/
  css/
  images/
  js/
  uploads/
config/
database/
includes/
pages/
index.php
.htaccess
```

## Phase Checklist
- [x] Project folders and Git repository initialized
- [x] PHP/MySQL environment checked
- [x] Database schema, seed data, and installer created
- [x] Frontend routes and dynamic page templates created
- [x] Admin authentication, dashboard, settings, and CRUD structure created
- [x] Frontend stylesheet and JavaScript completed
- [x] Frontend route/title/SEO edge cases fixed
- [x] Admin validation, upload cleanup, and security review completed
- [x] Installer and end-to-end smoke tests completed
- [x] PHP lint and final Git review completed
- [x] Latest commit pushed to GitHub

## Current Status
The project is fully implemented and tested. All checklist items are complete. The work has been committed and pushed to `https://github.com/mktamim/Tamim.git`.

### Verification performed
- PHP lint: 0 errors across all 33 PHP files
- MariaDB 10.4.32: full install verified (database created, schema + seed applied, 10 tables, 8 skills, 3 projects, 18 settings, admin account created)
- Admin login flow tested (password hashing, session regeneration, login throttling)
- All public routes render with dynamic DB content
- Admin CRUD tested for skills, services, projects, experience, education, testimonials, messages, blog posts, and settings
- Image upload validation (MIME, size, dimensions, unique names, cleanup on failure)
- CSRF protection on all mutating forms
- SEO: dynamic titles, meta descriptions, Open Graph tags, canonical URLs

### How to run
1. Copy `.env.example` to `.env` and set your database credentials
2. Visit `http://localhost/tamim_portfolio/database/install.php` to install
3. Admin login at `/admin/login` (use the credentials from your `.env`)
4. Public site at `/`
