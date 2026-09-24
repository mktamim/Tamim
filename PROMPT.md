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
- [ ] Frontend stylesheet and JavaScript completed
- [ ] Frontend route/title/SEO edge cases fixed
- [ ] Admin validation, upload cleanup, and security review completed
- [ ] Installer and end-to-end smoke tests completed
- [ ] PHP lint and final Git review completed
- [ ] Latest commit pushed to GitHub

## Current Status
The project is implemented in progress. Finish the remaining frontend assets, harden admin behavior, test the installer against the local MariaDB service, run PHP lint, review the Git diff, commit the completed work, and push it to `https://github.com/mktamim/Tamim.git`.
