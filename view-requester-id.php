<?php
require 'config.php';
require_role(['admin', 'secretary', 'gso']);

$reservationId = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT r.requester_id,u.valid_id_path FROM reservations r JOIN users u ON u.id=r.requester_id WHERE r.id=? LIMIT 1');
$stmt->bind_param('i', $reservationId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row || (current_user()['role'] === 'requester' && (int)$row['requester_id'] !== (int)current_user()['id'])) {
    http_response_code(403);
    exit('You do not have permission to view this document.');
}
$relativePath = $row['valid_id_path'] ?: '';
if (!$relativePath) {
    $legacy = $conn->prepare('SELECT requester_id_document FROM reservations WHERE id=?');
    $legacy->bind_param('i', $reservationId);
    $legacy->execute();
    $legacyRow = $legacy->get_result()->fetch_assoc();
    $legacyName = basename($legacyRow['requester_id_document'] ?? '');
    $legacyPath = __DIR__ . '/private_uploads/requester_ids/' . $legacyName;
} else {
    $legacyPath = __DIR__ . '/private_uploads/' . $relativePath;
}
if (!$relativePath && empty($legacyName)) {
    http_response_code(404);
    exit('Requester ID not found.');
}
$path = $legacyPath;
if (!is_file($path)) {
    http_response_code(404);
    exit('Requester ID file is unavailable.');
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($path);
if (!in_array($mime, ['image/jpeg', 'image/png', 'application/pdf'], true)) {
    http_response_code(415);
    exit('Unsupported ID file.');
}

header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="requester-id.' . ($mime === 'application/pdf' ? 'pdf' : ($mime === 'image/png' ? 'png' : 'jpg')) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, max-age=0');
readfile($path);
exit;
