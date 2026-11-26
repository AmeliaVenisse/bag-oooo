<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set('Asia/Manila'); // keep PHP dates consistent with the LGU's local time
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'municipal_vehicle_reservation';

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conn->set_charset('utf8mb4');
    $conn->query("SET time_zone = '+08:00'"); // match PHP timezone so created_at lines up
} catch (Throwable $e) {
    http_response_code(500);
    exit('Database connection failed. Check config.php and make sure database.sql was imported.');
}
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
function h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function logged_in() { return !empty($_SESSION['user']); }
function current_user() { return $_SESSION['user'] ?? null; }
function require_login() {
    if (!logged_in()) { header('Location: index.php'); exit; }
}
function require_role($roles) {
    require_login();
    $roles = (array)$roles;
    if (!in_array(current_user()['role'], $roles, true)) {
        http_response_code(403);
        exit('You do not have permission to access this page.');
    }
}
function log_activity($conn, $reservationId, $action, $details='') {
    $userId = current_user()['id'] ?? null;
    $stmt = $conn->prepare('INSERT INTO activity_log (user_id,reservation_id,action,details) VALUES (?,?,?,?)');
    $stmt->bind_param('iiss', $userId, $reservationId, $action, $details);
    $stmt->execute();
}
function go($url) { header('Location: '.$url); exit; }
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="'.h(csrf_token()).'">'; }
function check_csrf() {
    $t = $_POST['csrf'] ?? '';
    if (!is_string($t) || !hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(400);
        exit('Invalid or expired form. Go back, refresh the page and try again.');
    }
}
function store_requester_document($file, $required=false) {
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) throw new Exception('Upload a clear JPG, PNG, or PDF document.');
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('The document upload failed. Please try again.');
    if ((int)$file['size'] < 1 || (int)$file['size'] > 5 * 1024 * 1024) throw new Exception('Each document must be smaller than 5 MB.');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $extensions = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'application/pdf'=>'pdf'];
    if (!isset($extensions[$mime])) throw new Exception('Upload the document as a JPG, PNG, or PDF file.');
    $directory = __DIR__ . '/private_uploads/requester_documents';
    if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) throw new Exception('Private document storage is unavailable. Please contact the administrator.');
    $filename = bin2hex(random_bytes(24)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) throw new Exception('The document could not be saved. Please try again.');
    return 'requester_documents/' . $filename;
}
function requester_id_types() {
    return [
        'philid' => 'PhilID / ePhilID (PSA)',
        'passport' => 'Passport (DFA)',
        'drivers_license' => 'Driver’s License (LTO)',
        'student_permit' => 'Student Driver’s Permit (LTO)',
        'umid' => 'UMID (SSS / GSIS)',
        'sss_id' => 'SSS ID',
        'gsis_id' => 'GSIS e-Card',
        'prc_id' => 'PRC ID',
        'voters_id' => 'Voter’s ID / Certification (COMELEC)',
        'barangay_id' => 'Barangay ID (LGU)',
        'postal_id' => 'Postal ID (PHLPost)',
        'philhealth_id' => 'PhilHealth ID',
        'tin_id' => 'TIN ID (BIR)',
        'pagibig_id' => 'Pag-IBIG ID (HDMF)',
        'senior_citizen_id' => 'Senior Citizen ID (OSCA / LGU)',
        'pwd_id' => 'PWD ID (LGU)',
        'solo_parent_id' => 'Solo Parent ID (LGU / DSWD)',
        'ofw_owwa_id' => 'OFW ID / OWWA E-Card',
        'seafarers_id' => 'Seafarer’s ID (MARINA)',
        'nbi_clearance' => 'NBI Clearance',
        'ibp_id' => 'IBP ID',
        'employee_id' => 'Employee / Office ID',
        'school_id' => 'School ID',
        'four_ps_id' => '4Ps ID (DSWD)',
        'pvao_id' => 'PVAO ID',
        'afp_beneficiary_id' => 'AFP Beneficiary ID',
        'afpslai_id' => 'AFPSLAI ID',
        'firearms_license' => 'Firearms License (PNP-FEO)',
        'pnp_permit' => 'PNP Permit',
        'airman_license' => 'Airman License (CAAP)',
        'acr_i_card' => 'ACR I-Card (BI)',
        'other_government_id' => 'Other Valid Government ID',
    ];
}
function requester_id_type_label($type) {
    $types = requester_id_types();
    $legacyTypes = ['Government ID'=>'Government ID', 'Barangay ID'=>'Barangay ID', 'Other valid ID'=>'Other valid ID'];
    return $types[$type] ?? $legacyTypes[$type] ?? 'Other valid government-issued ID';
}
?>
