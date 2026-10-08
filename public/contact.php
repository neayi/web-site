<?php
/**
 * Réception du formulaire de contact de neayi.com.
 *
 * 1. Valide les champs et filtre le spam (champ piège + limite de fréquence par IP).
 * 2. Envoie le contact à HubSpot (API Forms v3) avec le cookie hubspotutk.
 * 3. Envoie une notification par email à l'équipe, par le SMTP de Brevo (PHPMailer, dans lib/).
 *
 * La configuration (identifiants HubSpot, adresses email) est lue dans un fichier
 * placé hors du dossier publié : voir deploy/neayi-contact-config.example.php.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex');
header('Cache-Control: no-store');

function reply(int $status, array $body): never
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}

function field(string $name, int $maxLength): string
{
    $value = trim((string) ($_POST[$name] ?? ''));
    $value = str_replace("\0", '', $value);
    return mb_substr($value, 0, $maxLength);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    reply(405, ['ok' => false, 'error' => 'method']);
}

$configFile = dirname(__DIR__) . '/neayi-contact-config.php';
if (!is_file($configFile)) {
    error_log('contact.php: fichier de configuration absent : ' . $configFile);
    reply(500, ['ok' => false, 'error' => 'config']);
}
$config = require $configFile;

// Champ piège : rempli uniquement par les robots. On répond « ok » sans rien faire.
if (field('website', 200) !== '') {
    reply(200, ['ok' => true]);
}

// Limite de fréquence : 5 envois par IP et par tranche de 10 minutes.
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateFile = sys_get_temp_dir() . '/neayi-contact-' . hash('sha256', $ip);
$now = time();
$hits = is_file($rateFile) ? (json_decode((string) file_get_contents($rateFile), true) ?: []) : [];
$hits = array_values(array_filter($hits, fn ($t) => is_int($t) && $t > $now - 600));
if (count($hits) >= 5) {
    reply(429, ['ok' => false, 'error' => 'rate']);
}
$hits[] = $now;
file_put_contents($rateFile, json_encode($hits), LOCK_EX);

$data = [
    'firstname' => field('firstname', 100),
    'lastname'  => field('lastname', 100),
    'email'     => field('email', 200),
    'company'   => field('company', 200),
    'subject'   => field('subject', 40),
    'message'   => field('message', 5000),
    'lang'      => field('lang', 5) === 'en' ? 'en' : 'fr',
    'page'      => field('page', 500),
];

$subjects = [
    'partnership' => 'Partenariat',
    'services'    => 'Accompagnement',
    'itinera'     => 'Itinera',
    'press'       => 'Presse',
    'other'       => 'Autre',
];
if (!isset($subjects[$data['subject']])) {
    $data['subject'] = 'other';
}

$errors = [];
foreach (['firstname', 'lastname', 'message'] as $required) {
    if ($data[$required] === '') {
        $errors[] = $required;
    }
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'email';
}
if ($errors) {
    reply(422, ['ok' => false, 'error' => 'invalid', 'fields' => $errors]);
}

$subjectLabel = $subjects[$data['subject']];
$pageUri = str_starts_with($data['page'], 'https://neayi.com') ? $data['page'] : 'https://neayi.com/';

// --- HubSpot ---------------------------------------------------------------
// Le sujet et la langue sont ajoutés en tête du message pour ne dépendre que des
// champs standard du formulaire HubSpot (firstname, lastname, email, company, message).
$hubspotOk = false;
if (!empty($config['hubspot_portal_id']) && !empty($config['hubspot_form_guid'])) {
    $context = [
        'pageUri'   => $pageUri,
        'pageName'  => 'neayi.com · contact',
        'ipAddress' => $ip,
    ];
    if (!empty($_COOKIE['hubspotutk']) && preg_match('/^[a-f0-9]{32}$/', $_COOKIE['hubspotutk'])) {
        $context['hutk'] = $_COOKIE['hubspotutk'];
    }
    $payload = [
        'fields' => [
            ['name' => 'firstname', 'value' => $data['firstname']],
            ['name' => 'lastname', 'value' => $data['lastname']],
            ['name' => 'email', 'value' => $data['email']],
            ['name' => 'company', 'value' => $data['company']],
            ['name' => 'message', 'value' => "[{$subjectLabel} · {$data['lang']}]\n\n{$data['message']}"],
        ],
        'context' => $context,
    ];
    $url = sprintf(
        'https://api.hsforms.com/submissions/v3/integration/submit/%s/%s',
        rawurlencode((string) $config['hubspot_portal_id']),
        rawurlencode((string) $config['hubspot_form_guid'])
    );
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $hubspotOk = $status >= 200 && $status < 300;
    if (!$hubspotOk) {
        error_log("contact.php: HubSpot a répondu $status : " . (is_string($response) ? $response : 'pas de réponse'));
    }
}

// --- Email (SMTP Brevo, via PHPMailer) ---------------------------------------
require __DIR__ . '/lib/phpmailer/Exception.php';
require __DIR__ . '/lib/phpmailer/PHPMailer.php';
require __DIR__ . '/lib/phpmailer/SMTP.php';

$mailOk = false;
if (!empty($config['mail_to']) && !empty($config['smtp_host'])) {
    $name = "{$data['firstname']} {$data['lastname']}";
    $body = "Nouveau message depuis neayi.com\n\n"
        . "Nom : $name\n"
        . "Email : {$data['email']}\n"
        . 'Structure : ' . ($data['company'] ?: '-') . "\n"
        . "Sujet : $subjectLabel\n"
        . "Langue : {$data['lang']}\n"
        . "Page : $pageUri\n"
        . 'HubSpot : ' . ($hubspotOk ? 'enregistré' : 'ÉCHEC, à saisir à la main') . "\n\n"
        . $data['message'] . "\n";

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->Port = (int) ($config['smtp_port'] ?? 587);
        $mail->SMTPSecure = $mail->Port === 465
            ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_password'];
        $mail->Timeout = 15;
        $mail->CharSet = PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;

        $mail->setFrom($config['mail_from'], $config['mail_from_name'] ?? 'neayi.com');
        foreach (array_filter(array_map('trim', explode(',', $config['mail_to']))) as $to) {
            $mail->addAddress($to);
        }
        $mail->addReplyTo($data['email'], $name);
        $mail->Subject = "[neayi.com] $subjectLabel · $name";
        $mail->Body = $body;

        $mail->send();
        $mailOk = true;
    } catch (PHPMailer\PHPMailer\Exception $e) {
        error_log('contact.php: échec de l\'envoi SMTP : ' . $mail->ErrorInfo);
    }
}

if ($hubspotOk || $mailOk) {
    reply(200, ['ok' => true]);
}
reply(502, ['ok' => false, 'error' => 'delivery']);
