INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Tamim'),
('site_title', 'Tamim | Web Developer & Creative Problem Solver'),
('site_description', 'Modern, responsive and performant web experiences built with clean code.'),
('hero_title', 'Building digital products with purpose.'),
('hero_subtitle', 'I am Tamim, a web developer focused on creating fast, accessible and memorable experiences for people and businesses.'),
('hero_email', 'hello@tamim.dev'),
('hero_phone', '+880 1XXX-XXXXXX'),
('hero_location', 'Dhaka, Bangladesh'),
('hero_cta_label', 'View my work'),
('hero_cta_url', '/projects'),
('og_image', ''),
('about_title', 'A developer who cares about the details.'),
('about_lead', 'I turn complex problems into simple, elegant and useful digital solutions.'),
('about_description', 'For the past several years, I have helped startups, agencies and growing businesses launch websites and web applications. My work combines thoughtful design, reliable engineering and a strong focus on performance and accessibility.'),
('social_github', 'https://github.com/mktamim'),
('social_linkedin', 'https://www.linkedin.com/'),
('social_twitter', 'https://twitter.com/'),
('copyright', 'Tamim. All rights reserved.');

INSERT INTO skills (name, category, level, sort_order) VALUES
('PHP', 'Backend', 92, 1),
('MySQL', 'Backend', 88, 2),
('JavaScript', 'Frontend', 90, 3),
('HTML & CSS', 'Frontend', 95, 4),
('Laravel', 'Backend', 82, 5),
('Git', 'Workflow', 86, 6),
('Responsive Design', 'Frontend', 94, 7),
('REST APIs', 'Backend', 84, 8);

INSERT INTO services (title, description, icon, sort_order) VALUES
('Custom Web Development', 'Reliable, maintainable and scalable websites built around your actual business needs.', 'code', 1),
('Responsive Frontend', 'Fast and accessible interfaces that feel natural on every screen and device.', 'layout', 2),
('CMS & Admin Panels', 'Practical content tools that make day-to-day publishing simple for your team.', 'dashboard', 3),
('Performance & Care', 'Technical audits, optimization and ongoing support to keep your product healthy.', 'spark', 4);

INSERT INTO projects (title, slug, summary, description, image, technologies, demo_url, source_url, featured, sort_order) VALUES
('Northstar Commerce', 'northstar-commerce', 'A focused commerce experience with a fast product discovery flow and a calm checkout.', 'Northstar needed a storefront that could grow without becoming difficult to manage. I designed a lightweight catalog, a clear product detail experience and an admin-friendly content model. The result is a responsive storefront with measurable performance improvements and a foundation the internal team can extend.', NULL, '["PHP","MySQL","JavaScript","REST API"]', '#', '#', 1, 1),
('Atlas Analytics', 'atlas-analytics', 'A readable dashboard that turns operational data into decisions people can act on.', 'Atlas brings together reporting, filters and export workflows in one focused interface. I worked across the data model, API contracts and frontend states to make the product useful for both new and experienced users.', NULL, '["PHP","MySQL","Vanilla JavaScript","Charts"]', '#', '#', 1, 2),
('Fieldnotes Journal', 'fieldnotes-journal', 'A small publishing platform for long-form stories, drafts and editorial review.', 'Fieldnotes is a content-first publishing tool with a simple editor, predictable URLs and a lightweight review workflow. The project uses a small set of well-defined models so the product stays easy to maintain.', NULL, '["PHP","MariaDB","HTML","CSS"]', '#', '#', 0, 3);

INSERT INTO experience (company, title, location, start_date, end_date, current_job, description, sort_order) VALUES
('Independent', 'Web Developer', 'Remote', '2022-01-01', NULL, 1, 'Partnering with clients to design, build and maintain websites and web applications from discovery through launch.', 1),
('Creative Studio', 'Frontend Developer', 'Dhaka, Bangladesh', '2020-03-01', '2021-12-31', 0, 'Built responsive marketing sites and product interfaces while working closely with designers and backend engineers.', 2),
('Tech Labs', 'Junior Developer', 'Dhaka, Bangladesh', '2018-06-01', '2020-02-28', 0, 'Learned production workflows, debugging and database design while shipping internal tools and client projects.', 3);

INSERT INTO education (institution, degree, field_of_study, graduation_year, description, sort_order) VALUES
('Bangladesh University', 'Bachelor of Science', 'Computer Science and Engineering', 2018, 'Focused on software engineering, databases, algorithms and human-centered technology.', 1),
('Online Learning', 'Continuous Study', 'Web Architecture and Design', NULL, 'Regularly studying modern web platforms, accessibility, performance and product design.', 2);

INSERT INTO testimonials (name, role, message, avatar, sort_order) VALUES
('Nusrat Jahan', 'Product Lead, Northstar', 'Tamim brought structure to a broad idea and delivered a product that our team could confidently use.', NULL, 1),
('Arif Rahman', 'Founder, Atlas', 'Clear communication, careful implementation and a strong eye for the details that make a product feel polished.', NULL, 2),
('Mehedi Hasan', 'Editor, Fieldnotes', 'The publishing workflow is simple without feeling limited. It has made our editorial process much faster.', NULL, 3);

INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, status, published_at) VALUES
('Designing for the first five seconds', 'designing-for-the-first-five-seconds', 'A practical look at clarity, hierarchy and trust in modern web interfaces.', '<p>The first few seconds of a visit set the tone for everything that follows. A clear headline, a useful next step and a calm visual hierarchy help people understand where they are and what they can do.</p><p>Good design is not about adding more decoration. It is about removing uncertainty and making the most important action feel obvious.</p>', NULL, 'published', '2026-08-18 10:00:00'),
('Small performance wins that compound', 'small-performance-wins-that-compound', 'How thoughtful defaults and careful measurement make websites feel faster.', '<p>Performance is a product feature. Smaller images, fewer blocking requests and deliberate loading states create a more respectful experience for everyone.</p><p>The best improvements usually come from measuring first, choosing the smallest useful change and repeating the process.</p>', NULL, 'published', '2026-07-24 10:00:00');
