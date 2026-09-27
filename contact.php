<?php
/**
 * Contact Form Handler
 * Portfolio Website
 */

require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
if (!csrf_verify($_POST[config('security.csrf_token_name')] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// Validate required fields
$errors = [];
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name)) $errors['name'] = 'Name is required';
if (empty($email)) $errors['email'] = 'Email is required';
elseif (!valid_email($email)) $errors['email'] = 'Invalid email format';
if (empty($message)) $errors['message'] = 'Message is required';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
    exit;
}

// Save to database
$ip = get_client_ip();
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

try {
    db_execute(
        'INSERT INTO messages (name, email, phone, subject, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [$name, $email, $phone, $subject, $message, $ip, $userAgent]
    );
    
    // TODO: Send email notification to admin
    // $adminEmail = setting('developer_email');
    // $headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
    // $emailBody = "New contact form submission:\n\nName: $name\nEmail: $email\nPhone: $phone\nSubject: $subject\n\nMessage:\n$message";
    // mail($adminEmail, "New Contact: $subject", $emailBody, $headers);
    
    echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent successfully. I\'ll get back to you soon.']);
} catch (Exception $e) {
    error_log('Contact form error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save message. Please try again later.']);
}