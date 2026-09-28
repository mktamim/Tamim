// Main JavaScript - Portfolio Website
document.addEventListener('DOMContentLoaded', function() {
    // Navbar scroll effect
    const navbar = document.getElementById('mainNavbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                const offset = 80;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Active nav link on scroll
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    
    function updateActiveNav() {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionHeight = section.offsetHeight;
            if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    }
    
    window.addEventListener('scroll', updateActiveNav);
    updateActiveNav();

    // Scroll reveal animation
    const revealElements = document.querySelectorAll('.fade-in');
    
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    revealElements.forEach(el => revealObserver.observe(el));

    // Add fade-in class to sections
    const animatedSections = document.querySelectorAll('.section > .container > .row > [class*="col-"]');
    animatedSections.forEach((el, index) => {
        el.classList.add('fade-in');
        el.style.transitionDelay = (index * 0.1) + 's';
    });

    // Skill progress bar animation
    const skillBars = document.querySelectorAll('.skill-progress .progress-bar');
    
    const skillObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.style.width || bar.getAttribute('aria-valuenow') + '%';
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 200);
                skillObserver.unobserve(bar);
            }
        });
    }, { threshold: 0.5 });
    
    skillBars.forEach(bar => skillObserver.observe(bar));

    // Project filter
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            projectCards.forEach(card => {
                if (filter === 'all' || card.dataset.category === filter) {
                    card.style.display = 'block';
                    setTimeout(() => card.style.opacity = '1', 10);
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });
        });
    });

    // Back to top button
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });
    
    backToTop.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Contact form handling
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Clear previous errors
            this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            this.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            
            fetch('/contact', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    contactForm.reset();
                } else {
                    showAlert('danger', data.message);
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = contactForm.querySelector('[name="' + field + '"]');
                            if (input) {
                                input.classList.add('is-invalid');
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                feedback.textContent = data.errors[field];
                                input.parentNode.appendChild(feedback);
                            }
                        });
                    }
                }
            })
            .catch(error => {
                showAlert('danger', 'An error occurred. Please try again.');
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Show alert function
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alert);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        }, 5000);
    }
    
    function createAlertContainer() {
        const container = document.createElement('div');
        container.id = 'alertContainer';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1050';
        container.style.maxWidth = '400px';
        document.body.appendChild(container);
        return container;
    }

    // Counter animation for stats
    const statNumbers = document.querySelectorAll('.stat-item .number, .stat-number, .about-experience-badge .number');
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.textContent.replace(/\D/g, ''));
                const suffix = el.textContent.replace(/[\d]/g, '');
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        el.textContent = target + suffix;
                        clearInterval(timer);
                    } else {
                        el.textContent = Math.floor(current) + suffix;
                    }
                }, 30);
                counterObserver.unobserve(el);
            }
        });
    }, { threshold: 0.5 });
    
    statNumbers.forEach(el => counterObserver.observe(el));

    // Parallax effect for hero shapes
    const heroShapes = document.querySelectorAll('.hero-shape');
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        heroShapes.forEach((shape, index) => {
            const speed = (index + 1) * 0.3;
            shape.style.transform = 'translateY(' + (scrolled * speed) + 'px)';
        });
    });

    // Lazy loading images
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => imageObserver.observe(img));

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Mobile menu close on link click
    const navbarCollapse = document.getElementById('navbarNav');
    const navLinksMobile = document.querySelectorAll('.nav-link');
    navLinksMobile.forEach(link => {
        link.addEventListener('click', () => {
            if (navbarCollapse.classList.contains('show')) {
                new bootstrap.Collapse(navbarCollapse).hide();
            }
        });
    });
});

// Code Rain / Matrix Effect for Hero
function initCodeRain() {
    const container = document.getElementById('codeRain');
    if (!container) return;

    const codeSnippets = [
        'const api = fetch("/api/users")',
        'await db.query("SELECT * FROM users")',
        'npm install && npm run build',
        'git commit -m "feat: add auth"',
        'docker-compose up -d',
        'function handleAuth(token) {}',
        'SELECT * FROM projects WHERE id=?',
        'const user = await User.find(id)',
        'router.get("/api/users", handler)',
        'redis.set("cache:key", value)',
        'await queue.process(jobs)',
        'middleware.validate(schema)',
        'export default function App() {}',
        'interface User { id: string }',
        'const config = { env: "prod" }',
        'try { await deploy() } catch {}',
        'logger.info("Server started")',
        'const data = JSON.parse(body)',
        'app.listen(PORT, () => {})',
        'import { useState } from "react"',
    ];

    const columnsCount = 12;
    const containerWidth = window.innerWidth;
    const columnWidth = 20;

    for (let i = 0; i < columnsCount; i++) {
        const column = document.createElement('div');
        column.className = 'code-rain-column';
        
        // Random position
        const left = Math.random() * (containerWidth - columnWidth);
        column.style.left = left + 'px';
        
        // Random duration between 10-20 seconds
        const duration = 10 + Math.random() * 15;
        column.style.animationDuration = duration + 's';
        
        // Random delay
        column.style.animationDelay = Math.random() * 5 + 's';
        
        // Random font size
        const fontSize = 12 + Math.random() * 4;
        column.style.fontSize = fontSize + 'px';
        
        // Generate random code text
        let text = '';
        const lines = 15 + Math.floor(Math.random() * 10);
        for (let j = 0; j < lines; j++) {
            const snippet = codeSnippets[Math.floor(Math.random() * codeSnippets.length)];
            text += snippet + '\n';
        }
        column.textContent = text;
        
        // Random color variation
        const hue = 210 + Math.random() * 30; // blue-cyan range
        column.style.color = `hsl(${hue}, 80%, 55%)`;
        column.style.textShadow = `0 0 10px hsl(${hue}, 80%, 55%)`;
        
        container.appendChild(column);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // ... existing code ...
    
    // Initialize code rain
    initCodeRain();
});