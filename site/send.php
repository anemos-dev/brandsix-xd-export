<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$required = ['お名前', 'ふりがな', '電話番号', 'メールアドレス', 'ブランド名', 'モデル名'];
foreach ($required as $field) {
    if (trim((string)($_POST[$field] ?? '')) === '') {
        http_response_code(400);
        exit('必須項目が入力されていません。');
    }
}

$email = trim((string)($_POST['メールアドレス'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('メールアドレスの形式が正しくありません。');
}

$to = 'info.brand.six@gmail.com';
$subject = '【BRANDSiX】無料WEB査定フォームからのお問い合わせ';
$fields = [
    'お名前',
    'ふりがな',
    '電話番号',
    'メールアドレス',
    'ブランド名',
    'モデル名',
    'お問い合わせ種類',
    'お問い合わせ内容詳細',
];

$bodyLines = [];
foreach ($fields as $field) {
    $value = trim((string)($_POST[$field] ?? ''));
    $bodyLines[] = $field . ': ' . ($value !== '' ? $value : '未入力');
}
$body = implode("\n", $bodyLines);

$headers = [
    'From: BRANDSiX <info.brand.six@gmail.com>',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
];

$sent = false;
if (function_exists('mb_send_mail')) {
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');
    $sent = mb_send_mail($to, $subject, $body, implode("\n", $headers));
} else {
    $sent = mail($to, $subject, $body, implode("\n", $headers));
}

if (!$sent) {
    http_response_code(500);
    exit('メール送信に失敗しました。');
}

header('Location: ./thanks.html', true, 303);
exit;
