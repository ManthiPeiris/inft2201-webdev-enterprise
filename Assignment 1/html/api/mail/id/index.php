<?php
require '../../../vendor/autoload.php';

use Application\Mail;
use Application\Page;

$dsn = "pgsql:host=" . getenv('DB_PROD_HOST') . ";dbname=" . getenv('DB_PROD_NAME');

try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed";
    exit;
}

$mail = new Mail($pdo);
$page = new Page();

// GET ID FROM URL
$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/'));
$id = end($parts);

if (!is_numeric($id)) {
    $page->badRequest();
    exit;
}

$id = (int)$id;

// GET ONE
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $item = $mail->getMailById($id);

    if (!$item) {
        $page->notFound();
        exit;
    }

    $page->item($item);
    exit;
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['subject'], $data['body'])) {
        $page->badRequest();
        exit;
    }

    if (!$mail->updateMail($id, $data['subject'], $data['body'])) {
        $page->notFound();
        exit;
    }

    $page->item(["updated" => true]);
    exit;
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    if (!$mail->deleteMail($id)) {
        $page->notFound();
        exit;
    }

    $page->item(["deleted" => true]);
    exit;
}

$page->badRequest();
