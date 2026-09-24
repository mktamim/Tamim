<?php
$sent = isset($_GET['sent']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!tamim_verify_csrf()) {
            throw new RuntimeException('The form could not be verified. Please try again.');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        $website = trim((string) ($_POST['website'] ?? ''));

        if ($website !== '') {
            tamim_redirect('/contact?sent=1');
        }
        if (mb_strlen($name) < 2 || mb_strlen($name) > 120) {
            throw new RuntimeException('Please enter your name.');
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || mb_strlen($email) > 180) {
            throw new RuntimeException('Please enter a valid email address.');
        }
        if ($subject !== '' && mb_strlen($subject) > 180) {
            throw new RuntimeException('The subject is too long.');
        }
        if (mb_strlen($message) < 10 || mb_strlen($message) > 5000) {
            throw new RuntimeException('Please write a message between 10 and 5000 characters.');
        }

        $insert = $pdo->prepare('INSERT INTO messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)');
        $insert->execute([':name' => $name, ':email' => $email, ':subject' => $subject, ':message' => $message]);
        tamim_redirect('/contact?sent=1');
    } catch (Throwable $error) {
        tamim_flash('error', $error->getMessage());
    }
}
?>
<section class="contact-hero section-soft"><div class="container contact-hero-grid"><div><p class="eyebrow">Contact</p><h1>Let us talk about your <em>next idea</em>.</h1><p class="page-hero-lead">Tell me what you are building, what feels stuck and what success should look like.</p><div class="contact-details"><div><span>Email</span><a href="mailto:<?php echo tamim_e(tamim_setting('hero_email', 'hello@tamim.dev')); ?>"><?php echo tamim_e(tamim_setting('hero_email', 'hello@tamim.dev')); ?></a></div><div><span>Location</span><strong><?php echo tamim_e(tamim_setting('hero_location', 'Bangladesh')); ?></strong></div><div><span>Availability</span><strong>Selected projects</strong></div></div></div><div class="contact-illustration" aria-hidden="true"><div class="mail-window"><div class="window-bar"><span></span><span></span><span></span><strong>new message</strong></div><div class="mail-lines"><i></i><i></i><i></i><i class="short"></i></div><div class="mail-stamp">→</div></div><div class="contact-orbit orbit-one"></div><div class="contact-orbit orbit-two"></div></div></div></section>
<section class="section"><div class="container contact-layout"><div class="contact-prompt"><p class="eyebrow">A helpful brief</p><h2>A few details are all I need to <em>get oriented</em>.</h2><p class="muted">Share the goal, the audience and the constraints you already know. I will reply with the next useful question, not a generic sales pitch.</p><ul class="prompt-list"><li><span>01</span><div><strong>What are you making?</strong><p>A product, website, campaign or something still taking shape.</p></div></li><li><span>02</span><div><strong>Who is it for?</strong><p>The people whose needs should shape every decision.</p></div></li><li><span>03</span><div><strong>What does success look like?</strong><p>A launch, a clearer workflow, more signups or a faster site.</p></div></li></ul></div><form class="contact-form" method="post" action="/contact"><?php echo tamim_csrf_field(); ?><?php include __DIR__ . '/includes/flashes.php'; ?><div class="form-grid"><label><span>Name</span><input type="text" name="name" value="<?php echo tamim_e($_POST['name'] ?? ''); ?>" required maxlength="120" autocomplete="name"></label><label><span>Email</span><input type="email" name="email" value="<?php echo tamim_e($_POST['email'] ?? ''); ?>" required maxlength="180" autocomplete="email"></label></div><label><span>Subject <small>Optional</small></span><input type="text" name="subject" value="<?php echo tamim_e($_POST['subject'] ?? ''); ?>" maxlength="180"></label><label><span>Message</span><textarea name="message" rows="7" required maxlength="5000" placeholder="Tell me about the project..."><?php echo tamim_e($_POST['message'] ?? ''); ?></textarea></label><label class="honeypot" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label><button class="button button-primary" type="submit">Send message <span>↗</span></button><p class="form-note">I usually reply within two business days.</p></form></div></section>
<?php if ($sent): ?><div class="container"><div class="notice notice-success sent-notice" role="status"><strong>Message sent.</strong> Thanks for reaching out. I will get back to you soon.</div></div><?php endif; ?>
