-- Database Schema for Personal Portfolio Website
-- Run this in MySQL/MariaDB

CREATE DATABASE IF NOT EXISTS `portfolio_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio_db`;

-- Table: admins
CREATE TABLE `admins` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `last_login` DATETIME DEFAULT NULL,
    `login_attempts` INT UNSIGNED DEFAULT 0,
    `locked_until` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: settings
CREATE TABLE `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` LONGTEXT DEFAULT NULL,
    `setting_type` ENUM('text', 'textarea', 'image', 'color', 'json') DEFAULT 'text',
    `group_name` VARCHAR(50) DEFAULT 'general',
    `label` VARCHAR(100) DEFAULT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_public` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_group` (`group_name`),
    INDEX `idx_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: social_links
CREATE TABLE `social_links` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `platform` VARCHAR(50) NOT NULL,
    `icon_class` VARCHAR(50) DEFAULT NULL,
    `url` VARCHAR(255) NOT NULL,
    `label` VARCHAR(100) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: skills
CREATE TABLE `skills` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `icon_class` VARCHAR(100) DEFAULT NULL,
    `icon_type` ENUM('fontawesome', 'custom', 'image') DEFAULT 'fontawesome',
    `icon_image` VARCHAR(255) DEFAULT NULL,
    `percentage` TINYINT UNSIGNED DEFAULT 0,
    `description` TEXT DEFAULT NULL,
    `category` VARCHAR(50) DEFAULT 'technical',
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: services
CREATE TABLE `services` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(150) NOT NULL UNIQUE,
    `short_description` TEXT DEFAULT NULL,
    `full_description` LONGTEXT DEFAULT NULL,
    `icon_class` VARCHAR(100) DEFAULT NULL,
    `icon_type` ENUM('fontawesome', 'custom', 'image') DEFAULT 'fontawesome',
    `icon_image` VARCHAR(255) DEFAULT NULL,
    `features` JSON DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: project_categories
CREATE TABLE `project_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: projects
CREATE TABLE `projects` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `title` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) NOT NULL UNIQUE,
    `short_description` TEXT DEFAULT NULL,
    `full_description` LONGTEXT DEFAULT NULL,
    `cover_image` VARCHAR(255) DEFAULT NULL,
    `gallery_images` JSON DEFAULT NULL,
    `technologies` JSON DEFAULT NULL,
    `features` JSON DEFAULT NULL,
    `challenges` TEXT DEFAULT NULL,
    `solution` TEXT DEFAULT NULL,
    `live_url` VARCHAR(255) DEFAULT NULL,
    `github_url` VARCHAR(255) DEFAULT NULL,
    `client_name` VARCHAR(100) DEFAULT NULL,
    `project_date` DATE DEFAULT NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `views` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`category_id`) REFERENCES `project_categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_category` (`category_id`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: experiences
CREATE TABLE `experiences` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_name` VARCHAR(150) NOT NULL,
    `position` VARCHAR(150) NOT NULL,
    `location` VARCHAR(150) DEFAULT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE DEFAULT NULL,
    `is_current` TINYINT(1) DEFAULT 0,
    `description` TEXT DEFAULT NULL,
    `achievements` JSON DEFAULT NULL,
    `technologies` JSON DEFAULT NULL,
    `company_logo` VARCHAR(255) DEFAULT NULL,
    `company_url` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: educations
CREATE TABLE `educations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `degree` VARCHAR(150) NOT NULL,
    `institution` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(150) DEFAULT NULL,
    `location` VARCHAR(150) DEFAULT NULL,
    `start_year` YEAR NOT NULL,
    `end_year` YEAR DEFAULT NULL,
    `is_current` TINYINT(1) DEFAULT 0,
    `description` TEXT DEFAULT NULL,
    `grade` VARCHAR(50) DEFAULT NULL,
    `institution_logo` VARCHAR(255) DEFAULT NULL,
    `institution_url` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_years` (`start_year`, `end_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: testimonials
CREATE TABLE `testimonials` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_name` VARCHAR(100) NOT NULL,
    `client_designation` VARCHAR(100) DEFAULT NULL,
    `client_company` VARCHAR(100) DEFAULT NULL,
    `client_image` VARCHAR(255) DEFAULT NULL,
    `review` TEXT NOT NULL,
    `rating` TINYINT UNSIGNED DEFAULT 5,
    `project_name` VARCHAR(150) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: messages
CREATE TABLE `messages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `subject` VARCHAR(200) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `is_archived` TINYINT(1) DEFAULT 0,
    `replied_at` DATETIME DEFAULT NULL,
    `reply_message` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_read` (`is_read`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: blog_categories
CREATE TABLE `blog_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: blog_posts
CREATE TABLE `blog_posts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `title` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) NOT NULL UNIQUE,
    `excerpt` TEXT DEFAULT NULL,
    `content` LONGTEXT DEFAULT NULL,
    `featured_image` VARCHAR(255) DEFAULT NULL,
    `tags` JSON DEFAULT NULL,
    `author_id` INT UNSIGNED DEFAULT NULL,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `is_featured` TINYINT(1) DEFAULT 0,
    `meta_title` VARCHAR(200) DEFAULT NULL,
    `meta_description` VARCHAR(300) DEFAULT NULL,
    `meta_keywords` VARCHAR(500) DEFAULT NULL,
    `published_at` DATETIME DEFAULT NULL,
    `views` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`category_id`) REFERENCES `blog_categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`author_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL,
    INDEX `idx_category` (`category_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_slug` (`slug`),
    INDEX `idx_published` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin (password: admin123 - change after first login)
INSERT INTO `admins` (`username`, `email`, `password`, `full_name`) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User');

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `group_name`, `label`, `description`, `sort_order`) VALUES
('site_name', 'My Portfolio', 'text', 'general', 'Site Name', 'Website name shown in title and header', 1),
('site_tagline', 'Professional Web Developer', 'text', 'general', 'Site Tagline', 'Short tagline for the website', 2),
('developer_name', 'John Doe', 'text', 'general', 'Developer Name', 'Your full name', 3),
('developer_title', 'Full Stack Web Developer', 'text', 'general', 'Professional Title', 'Your professional title', 4),
('developer_email', 'john@example.com', 'text', 'general', 'Email', 'Contact email', 5),
('developer_phone', '+1234567890', 'text', 'general', 'Phone', 'Contact phone', 6),
('developer_whatsapp', '+1234567890', 'text', 'general', 'WhatsApp', 'WhatsApp number with country code', 7),
('developer_location', 'New York, USA', 'text', 'general', 'Location', 'Your location', 8),
('profile_image', '', 'image', 'general', 'Profile Image', 'Your professional photo', 9),
('hero_subtitle', 'I build fast, scalable and user-friendly websites and web applications for businesses and individuals.', 'textarea', 'hero', 'Hero Subtitle', 'Short description in hero section', 1),
('hero_cta_text', 'View My Work', 'text', 'hero', 'CTA Button Text', 'Primary CTA button text', 2),
('hero_cta_link', '#projects', 'text', 'hero', 'CTA Button Link', 'Primary CTA button link', 3),
('hero_cta2_text', 'Hire Me', 'text', 'hero', 'Secondary CTA Text', 'Secondary CTA button text', 4),
('hero_cta2_link', '#contact', 'text', 'hero', 'Secondary CTA Link', 'Secondary CTA button link', 5),
('about_short_intro', 'Passionate web developer with 5+ years of experience building modern web applications.', 'textarea', 'about', 'Short Introduction', 'Brief intro for about section', 1),
('about_detailed_bio', 'I am a dedicated full-stack web developer with expertise in PHP, Laravel, JavaScript, and modern web technologies. I love creating clean, efficient, and scalable solutions that solve real-world problems.', 'textarea', 'about', 'Detailed Biography', 'Detailed bio for about section', 2),
('stat_experience', '5+', 'text', 'about', 'Years Experience', 'Years of experience', 3),
('stat_projects', '100+', 'text', 'about', 'Projects Completed', 'Number of projects', 4),
('stat_clients', '50+', 'text', 'about', 'Happy Clients', 'Number of happy clients', 5),
('primary_color', '#2563eb', 'color', 'theme', 'Primary Color', 'Main brand color', 1),
('secondary_color', '#0ea5e9', 'color', 'theme', 'Secondary Color', 'Secondary brand color', 2),
('background_color', '#ffffff', 'color', 'theme', 'Background Color', 'Main background color', 3),
('text_color', '#1f2937', 'color', 'theme', 'Text Color', 'Main text color', 4),
('seo_title', 'John Doe | Full Stack Web Developer', 'text', 'seo', 'SEO Title', 'Default SEO title', 1),
('seo_description', 'Professional Full Stack Web Developer specializing in PHP, Laravel, JavaScript, and modern web technologies.', 'textarea', 'seo', 'SEO Description', 'Default SEO meta description', 2),
('seo_keywords', 'web developer, PHP developer, Laravel developer, full stack developer, freelance developer', 'text', 'seo', 'SEO Keywords', 'Default SEO keywords', 3),
('favicon', '', 'image', 'general', 'Favicon', 'Website favicon', 10),
('logo', '', 'image', 'general', 'Logo', 'Website logo', 11);

-- Insert default social links
INSERT INTO `social_links` (`platform`, `icon_class`, `url`, `label`, `sort_order`) VALUES
('facebook', 'fab fa-facebook-f', 'https://facebook.com', 'Facebook', 1),
('linkedin', 'fab fa-linkedin-in', 'https://linkedin.com', 'LinkedIn', 2),
('github', 'fab fa-github', 'https://github.com', 'GitHub', 3),
('whatsapp', 'fab fa-whatsapp', 'https://wa.me/1234567890', 'WhatsApp', 4),
('email', 'fas fa-envelope', 'mailto:john@example.com', 'Email', 5);

-- Insert default project categories
INSERT INTO `project_categories` (`name`, `slug`, `description`, `sort_order`) VALUES
('PHP', 'php', 'Core PHP projects', 1),
('Laravel', 'laravel', 'Laravel framework projects', 2),
('WordPress', 'wordpress', 'WordPress development', 3),
('E-commerce', 'ecommerce', 'E-commerce websites', 4),
('Business Website', 'business', 'Business and corporate websites', 5),
('Custom Web App', 'custom-web-app', 'Custom web applications', 6);

-- Insert default skills
INSERT INTO `skills` (`name`, `icon_class`, `percentage`, `description`, `category`, `sort_order`) VALUES
('PHP', 'fab fa-php', 95, 'Server-side scripting language', 'backend', 1),
('Laravel', 'fab fa-laravel', 90, 'PHP web application framework', 'backend', 2),
('MySQL', 'fas fa-database', 85, 'Relational database management', 'backend', 3),
('JavaScript', 'fab fa-js-square', 90, 'Client-side and server-side programming', 'frontend', 4),
('HTML5', 'fab fa-html5', 95, 'Markup language for web structure', 'frontend', 5),
('CSS3', 'fab fa-css3-alt', 90, 'Styling and layout', 'frontend', 6),
('Bootstrap', 'fab fa-bootstrap', 85, 'CSS framework for responsive design', 'frontend', 7),
('REST API', 'fas fa-code', 88, 'API design and development', 'backend', 8),
('Git', 'fab fa-git-alt', 90, 'Version control system', 'tools', 9),
('WordPress', 'fab fa-wordpress', 80, 'CMS development and customization', 'cms', 10);

-- Insert default services
INSERT INTO `services` (`title`, `slug`, `short_description`, `full_description`, `icon_class`, `sort_order`) VALUES
('Web Development', 'web-development', 'Custom PHP and modern web technology for professional websites.', 'I build custom websites using PHP, Laravel, and modern web technologies. From simple landing pages to complex web applications, I deliver clean, maintainable, and scalable code.', 'fas fa-code', 1),
('E-commerce Development', 'ecommerce-development', 'Customized online stores for businesses.', 'Complete e-commerce solutions including product management, payment integration, order processing, and admin dashboards. Built with security and performance in mind.', 'fas fa-shopping-cart', 2),
('PHP Development', 'php-development', 'Custom PHP-based web application development.', 'Robust PHP applications using modern practices. MVC architecture, Composer dependency management, PSR standards, and comprehensive testing.', 'fab fa-php', 3),
('Website Maintenance', 'website-maintenance', 'Existing website maintenance and bug fixing.', 'Ongoing maintenance, security updates, performance optimization, bug fixes, and feature enhancements for existing PHP and WordPress websites.', 'fas fa-tools', 4),
('API Integration', 'api-integration', 'Third-party API integration and custom API development.', 'Seamless integration with payment gateways, social media, CRM systems, and other third-party services. Custom RESTful API development with documentation.', 'fas fa-plug', 5),
('Website Speed Optimization', 'speed-optimization', 'Website performance and loading speed optimization.', 'Core Web Vitals optimization, caching strategies, database optimization, image compression, CDN setup, and code minification for lightning-fast websites.', 'fas fa-tachometer-alt', 6);

-- Insert sample experiences
INSERT INTO `experiences` (`company_name`, `position`, `location`, `start_date`, `end_date`, `is_current`, `description`, `sort_order`) VALUES
('Tech Solutions Inc.', 'Senior Full Stack Developer', 'New York, USA', '2022-01-15', NULL, 1, 'Leading development of enterprise web applications using PHP/Laravel and Vue.js. Mentoring junior developers and implementing CI/CD pipelines.', 1),
('Digital Agency', 'Full Stack Developer', 'Remote', '2019-06-01', '2022-01-10', 0, 'Developed 50+ client projects including e-commerce platforms, CMS systems, and custom web applications. Worked with PHP, Laravel, WordPress, and JavaScript frameworks.', 2),
('StartupXYZ', 'Junior Web Developer', 'San Francisco, USA', '2017-03-01', '2019-05-30', 0, 'Built and maintained company websites and internal tools. Learned PHP, MySQL, JavaScript, and modern development practices.', 3);

-- Insert sample educations
INSERT INTO `educations` (`degree`, `institution`, `subject`, `location`, `start_year`, `end_year`, `description`, `sort_order`) VALUES
('Bachelor of Science in Computer Science', 'University of Technology', 'Computer Science', 'New York, USA', 2013, 2017, 'Focused on software engineering, algorithms, and web technologies. Graduated with honors.', 1),
('Web Development Certification', 'Code Academy', 'Full Stack Web Development', 'Online', 2017, 2017, 'Intensive bootcamp covering HTML, CSS, JavaScript, PHP, MySQL, and modern frameworks.', 2);

-- Insert sample testimonials
INSERT INTO `testimonials` (`client_name`, `client_designation`, `client_company`, `review`, `rating`, `project_name`, `is_featured`, `sort_order`) VALUES
('Sarah Johnson', 'CEO', 'TechStart Inc.', 'Exceptional work! Delivered our e-commerce platform on time and within budget. The code quality is outstanding and the communication was excellent throughout the project.', 5, 'E-commerce Platform', 1, 1),
('Michael Chen', 'CTO', 'Innovate Labs', 'Professional, reliable, and highly skilled. He transformed our legacy PHP application into a modern Laravel system with improved performance and maintainability.', 5, 'Legacy Migration', 1, 2),
('Emily Davis', 'Marketing Director', 'Creative Agency', 'Great attention to detail and deep understanding of web technologies. Our website loads incredibly fast now and the user experience is fantastic.', 4, 'Website Redesign', 0, 3);