<?php
declare(strict_types=1);

date_default_timezone_set('Europe/London');

$BASE = 'https://sandwichsecurity.co.uk';

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/contact.html');
}

function clean_field(string $value, int $maxLen = 500): string {
    $value = trim($value);
    $value = str_replace(["\r", "\n"], ' ', $value);
    $value = strip_tags($value);
    return mb_substr($value, 0, $maxLen);
}

function post_value(string $key): string {
    return isset($_POST[$key]) ? (string) $_POST[$key] : '';
}

// Honeypot: silent success on bots
if (trim(post_value('_honey')) !== '') {
    redirect('/thanks.html');
}

$name         = clean_field(post_value('name'), 120);
$address      = clean_field(post_value('address'), 200);
$postcode     = strtoupper(clean_field(post_value('postcode'), 10));
$phone        = clean_field(post_value('phone'), 40);
$email        = filter_var(trim(post_value('email')), FILTER_VALIDATE_EMAIL);
$location     = clean_field(post_value('location'), 80);
$service      = clean_field(post_value('service'), 80);
$hasWhatsApp  = clean_field(post_value('has_whatsapp'), 5);
$message      = trim(post_value('message'));

$postcodeOk = (bool) preg_match(
    '/^([A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2})$/',
    $postcode
);

if (
    $name === '' ||
    $address === '' ||
    !$postcodeOk ||
    $phone === '' ||
    $email === false ||
    $location === '' ||
    $service === '' ||
    ($hasWhatsApp !== 'Yes' && $hasWhatsApp !== 'No') ||
    mb_strlen($message) < 10
) {
    redirect('/contact.html?status=invalid');
}

$message = mb_substr(strip_tags($message), 0, 4000);

$secrets = @include __DIR__ . '/config/secrets.php';
$apiKey = is_array($secrets) && !empty($secrets['resend_api_key'])
    ? $secrets['resend_api_key']
    : (getenv('RESEND_API_KEY') ?: '');

if ($apiKey === '') {
    error_log('[contact-submit] missing RESEND_API_KEY');
    redirect('/contact.html?status=error');
}

$submittedAt = date('Y-m-d H:i:s') . ' (Europe/London)';

$subject = 'New CCTV/doorbell enquiry, sandwichsecurity.co.uk';

$escName     = htmlspecialchars($name,     ENT_QUOTES | ENT_HTML5, 'UTF-8');
$escAddress  = htmlspecialchars($address,  ENT_QUOTES | ENT_HTML5, 'UTF-8');
$escPostcode = htmlspecialchars($postcode, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$escPhone    = htmlspecialchars($phone,    ENT_QUOTES | ENT_HTML5, 'UTF-8');
$escEmail    = htmlspecialchars($email,    ENT_QUOTES | ENT_HTML5, 'UTF-8');
$escLocation = htmlspecialchars($location, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$escService  = htmlspecialchars($service,  ENT_QUOTES | ENT_HTML5, 'UTF-8');
$escWA       = htmlspecialchars($hasWhatsApp, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$escMsg      = nl2br(htmlspecialchars($message, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$escTime     = htmlspecialchars($submittedAt, ENT_QUOTES | ENT_HTML5, 'UTF-8');

$html = <<<HTML
<!doctype html>
<html>
<body style="margin:0;padding:24px;background:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:#0b1220;">
  <div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:28px;">
    <div style="font-size:11px;text-transform:uppercase;color:#4b5768;letter-spacing:0.05em;margin-bottom:6px;">New enquiry</div>
    <h1 style="margin:0 0 16px;font-size:22px;color:#0a0f1a;">CCTV/doorbell enquiry from {$escName}</h1>
    <table style="width:100%;border-collapse:collapse;font-size:15px;">
      <tr><td style="padding:8px 0;border-bottom:1px solid #eee;width:140px;color:#4b5768;">Name</td><td style="padding:8px 0;border-bottom:1px solid #eee;">{$escName}</td></tr>
      <tr><td style="padding:8px 0;border-bottom:1px solid #eee;color:#4b5768;">Address</td><td style="padding:8px 0;border-bottom:1px solid #eee;">{$escAddress}</td></tr>
      <tr><td style="padding:8px 0;border-bottom:1px solid #eee;color:#4b5768;">Postcode</td><td style="padding:8px 0;border-bottom:1px solid #eee;">{$escPostcode}</td></tr>
      <tr><td style="padding:8px 0;border-bottom:1px solid #eee;color:#4b5768;">Area</td><td style="padding:8px 0;border-bottom:1px solid #eee;">{$escLocation}</td></tr>
      <tr><td style="padding:8px 0;border-bottom:1px solid #eee;color:#4b5768;">Service</td><td style="padding:8px 0;border-bottom:1px solid #eee;">{$escService}</td></tr>
      <tr><td style="padding:8px 0;border-bottom:1px solid #eee;color:#4b5768;">Phone</td><td style="padding:8px 0;border-bottom:1px solid #eee;"><a href="tel:{$escPhone}" style="color:#1741a6;">{$escPhone}</a></td></tr>
      <tr><td style="padding:8px 0;border-bottom:1px solid #eee;color:#4b5768;">Email</td><td style="padding:8px 0;border-bottom:1px solid #eee;"><a href="mailto:{$escEmail}" style="color:#1741a6;">{$escEmail}</a></td></tr>
      <tr><td style="padding:8px 0;border-bottom:1px solid #eee;color:#4b5768;">Has WhatsApp?</td><td style="padding:8px 0;border-bottom:1px solid #eee;font-weight:600;">{$escWA}</td></tr>
      <tr><td style="padding:8px 0;border-bottom:1px solid #eee;color:#4b5768;">Submitted</td><td style="padding:8px 0;border-bottom:1px solid #eee;">{$escTime}</td></tr>
    </table>
    <h2 style="margin:22px 0 8px;font-size:15px;color:#4b5768;text-transform:uppercase;letter-spacing:0.04em;">Job details</h2>
    <div style="line-height:1.6;background:#f4f6f9;border-radius:8px;padding:14px 16px;">{$escMsg}</div>
    <hr style="margin:22px 0;border:0;border-top:1px solid #eee;">
    <p style="font-size:12px;color:#94a3b8;margin:0;">Reply directly to this email to respond to {$escName}.</p>
  </div>
</body>
</html>
HTML;

$plain = "New CCTV/doorbell enquiry, sandwichsecurity.co.uk\n\n"
       . "Name:     {$name}\n"
       . "Address:  {$address}\n"
       . "Postcode: {$postcode}\n"
       . "Area:     {$location}\n"
       . "Service:  {$service}\n"
       . "Phone:    {$phone}\n"
       . "Email:    {$email}\n"
       . "WhatsApp: {$hasWhatsApp}\n"
       . "Time:     {$submittedAt}\n\n"
       . "Job details:\n{$message}\n";

// Resend sandbox: emails only go to the account owner until the sending
// domain is verified. Once sandwichsecurity.co.uk is a verified sender, switch
// 'from' to 'Sandwich Security <hello@sandwichsecurity.co.uk>' and 'to' to
// hello@sandwichsecurity.co.uk (which forwards to the working inbox).
$payload = [
    'from'     => 'Sandwich Security <onboarding@resend.dev>',
    'to'       => ['bluebucketuk@gmail.com'],
    'reply_to' => $email,
    'subject'  => $subject,
    'html'     => $html,
    'text'     => $plain,
];

$ch = curl_init('https://api.resend.com/emails');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

if ($response === false || $status < 200 || $status >= 300) {
    $logMsg = sprintf(
        '[contact-submit] Resend error status=%d curl_err=%s body=%s',
        $status,
        $error,
        substr((string) $response, 0, 500)
    );
    error_log($logMsg);
    redirect('/contact.html?status=error');
}

redirect('/thanks.html');
