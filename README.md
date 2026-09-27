# Modern Personal Portfolio Website for Web Developer

A lightweight, fast, and professional personal portfolio website built with PHP 8+, MySQL, and vanilla JavaScript. Features a complete admin panel for dynamic content management.

## Features

### Frontend
- **Hero Section** - Dynamic content with profile image, social links, and CTAs
- **About Section** - Biography, statistics, and experience badge
- **Skills Section** - Categorized skills with progress bars and icons
- **Services Section** - Service cards with features list
- **Projects/Portfolio** - Filterable projects with gallery and detail pages
- **Experience Timeline** - Professional work history
- **Education** - Academic background
- **Testimonials** - Client feedback with ratings
- **Contact Form** - AJAX form with database storage
- **Blog Module** - Optional blog with categories and tags
- **Responsive Design** - Mobile-first, works on all devices
- **SEO Optimized** - Dynamic meta tags, Open Graph, structured data
- **Performance** - Optimized images, lazy loading, minified assets

### Admin Panel
- **Dashboard** - Statistics overview with recent activity
- **Website Settings** - General, hero, about, theme, SEO settings
- **Homepage Management** - Hero content, CTAs, profile image
- **About Management** - Biography, statistics, profile image
- **Skills CRUD** - Add/edit/delete skills with categories and progress
- **Services CRUD** - Full service management with features
- **Projects CRUD** - Complete project management with gallery
- **Project Categories** - Category management for projects
- **Experience CRUD** - Work history with achievements and technologies
- **Education CRUD** - Academic background
- **Testimonials CRUD** - Client feedback with ratings
- **Messages** - Contact form submissions with read/archive
- **Blog Management** - Posts, categories, tags, SEO
- **Social Links** - Manage social media links
- **Secure Authentication** - Password hashing, CSRF protection, session security

### Security
- Password hashing with bcrypt
- Prepared statements (PDO)
- CSRF token protection
- XSS prevention with output escaping
- SQL injection prevention
- Secure file upload validation
- Login attempt limiting with lockout
- Secure session configuration

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Apache/Nginx with mod_rewrite
- GD Library (for image processing)

## Installation

1. **Clone/Extract** the project to your web server directory:
   ```
   C:\xampp\htdocs\Tamim\
   ```

2. **Create Database** and import schema:
   ```sql
   -- Run database.sql in MySQL/MariaDB
   ```

3. **Configure Database** in `config/database.php`:
   ```php
   return [
       'host' => 'localhost',
       'dbname' => 'portfolio_db',
       'username' => 'root',
       'password' => '',
       'charset' => 'utf8mb4',
   ];
   ```

4. **Configure Application** in `config/app.php`:
   - Set `APP_URL` to your site URL
   - Adjust other settings as needed

5. **Set Permissions** for uploads directory:
   ```bash
   # Windows: Ensure write permissions on assets/uploads/
   ```

6. **Access the Website**:
   - Frontend: `http://localhost/Tamim/`
   - Admin: `http://localhost/Tamim/admin/login.php`

7. **Default Admin Login**:
   - Email: `admin@example.com`
   - Password: `admin123`
   - **Change password immediately after first login!**

## Project Structure

```
Tamim/
├── admin/                    # Admin panel
│   ├── login.php            # Admin login
│   ├── logout.php           # Admin logout
│   ├── dashboard.php        # Dashboard
│   ├── profile.php          # Admin profile
│   ├── change-password.php  # Password change
│   ├── settings/            # Website settings
│   │   ├── index.php        # General settings
│   │   └── social-links.php # Social links
│   ├── homepage/            # Homepage settings
│   ├── about/               # About section
│   ├── skills/              # Skills management
│   ├── services/            # Services management
│   ├── projects/            # Projects management
│   ├── project_categories/  # Project categories
│   ├── experience/          # Experience management
│   ├── education/           # Education management
│   ├── testimonials/        # Testimonials management
│   ├── messages/            # Contact messages
│   └── blog/                # Blog management
├── config/                   # Configuration
│   ├── database.php         # Database config
│   └── app.php              # Application config
├── includes/                 # Shared includes
│   ├── bootstrap.php        # Core bootstrap
│   ├── functions.php        # Helper functions
│   ├── header.php           # Frontend header
│   ├── footer.php           # Frontend footer
│   ├── admin_header.php     # Admin header
│   └── admin_footer.php     # Admin footer
├── assets/                   # Static assets
│   ├── css/
│   │   ├── style.css        # Frontend styles
│   │   └── admin.css        # Admin styles
│   ├── js/
│   │   ├── main.js          # Frontend JS
│   │   └── admin.js         # Admin JS
│   ├── images/              # Static images
│   └── uploads/             # Uploaded files (auto-created)
├── index.php                 # Homepage
├── project.php               # Project details
├── blog.php                  # Blog listing
├── blog-details.php          # Blog post details
├── contact.php               # Contact form handler
├── 404.php                   # 404 page
├── database.sql              # Database schema
├── .htaccess                 # URL rewriting
└── README.md                 # This file
```

## Customization

### Colors & Theme
Edit in Admin Panel → Settings → Theme:
- Primary Color
- Secondary Color
- Background Color
- Text Color

### Content
All content is managed through the admin panel:
- Site name, tagline, developer info
- Hero section content
- About section
- Skills, services, projects
- Experience, education
- Testimonials
- Contact information
- Social links
- SEO settings

### Adding Pages
1. Create new PHP file in root
2. Include `includes/bootstrap.php`
3. Set page variables (`$pageTitle`, `$pageDescription`, etc.)
4. Include `includes/header.php` and `includes/footer.php`
5. Add route to `.htaccess` if needed

## Deployment

1. **Upload files** to your server
2. **Import database.sql** to production database
3. **Update config/database.php** with production credentials
4. **Update config/app.php** with production URL
5. **Set proper file permissions** for uploads directory
6. **Enable SSL** and update .htaccess for HTTPS
7. **Configure email** for contact form notifications (optional)

## Performance Tips

- Enable OPcache in PHP
- Use a CDN for static assets
- Enable gzip/deflate compression
- Set proper cache headers
- Optimize database indexes
- Use WebP images (auto-converted on upload)

## Security Checklist

- [ ] Change default admin password
- [ ] Use HTTPS in production
- [ ] Set secure session cookies
- [ ] Restrict admin access by IP (optional)
- [ ] Regular database backups
- [ ] Monitor error logs
- [ ] Keep PHP and extensions updated

## License

This project is open source and available under the MIT License.

## Support

For issues or questions, please check the documentation or create an issue in the repository.

---

**Built with ❤️ using PHP & MySQL**