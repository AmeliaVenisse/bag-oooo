<?php
require 'config.php';
require_login();
$type = $_GET['type'] ?? '';
$columns = ['valid_id'=>'valid_id_path', 'valid_id_back'=>'valid_id_back_path', 'birth_certificate'=>'birth_certificate_path', 'death_certificate'=>'death_certificate_path'];
if (!isset($columns[$type])) {
    http_response_code(400);
    exit('Invalid document type.');
}
$requesterId = (int)($_GET['user'] ?? 0);
if ($requesterId < 1 || (current_user()['role'] === 'requester' && current_user()['id'] !== $requesterId)) {
    http_response_code(403);
    exit('You do not have permission to view this document.');
}
if (!in_array(current_user()['role'], ['requester','admin','secretary','gso'], true)) {
    http_response_code(403);
    exit('You do not have permission to view this document.');
}
$column = $columns[$type];
$stmt = $conn->prepare("SELECT `$column` document_path FROM users WHERE id=? LIMIT 1");
$stmt->bind_param('i', $requesterId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$relativePath = $row['document_path'] ?? '';
if (!$relativePath || strpos($relativePath, 'requester_documents/') !== 0 || basename($relativePath) !== substr($relativePath, strlen('requester_documents/'))) {
    http_response_code(404);
    exit('Document not found.');
}
$path = __DIR__ . '/private_uploads/' . $relativePath;
if (!is_file($path)) {
    http_response_code(404);
    exit('Document is unavailable.');
}
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($path);
if (!in_array($mime, ['image/jpeg','image/png','application/pdf'], true)) {
    http_response_code(415);
    exit('Unsupported document.');
}
header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="requester-document.' . ($mime === 'application/pdf' ? 'pdf' : ($mime === 'image/png' ? 'png' : 'jpg')) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, max-age=0');
readfile($path);
exit;
