<?php
require 'config.php';
if (logged_in()) go(current_user()['role'] === 'requester' ? 'index.php' : 'dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare('SELECT id,full_name,email,password_hash,role FROM users WHERE email=? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $staffRoles = ['admin', 'secretary', 'gso'];
    if ($user && in_array($user['role'], $staffRoles, true) && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$user['id'],'name'=>$user['full_name'],'email'=>$user['email'],'role'=>$user['role']];
        unset($_SESSION['draft_request']);
        go('dashboard.php');
    }
    $error = 'Staff account not found, or the email and password do not match.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Staff sign in | Vehicle Reservation</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-page"><main class="login-wrap"><section class="login-art"><div class="brand"><span class="brand-icon">⌖</span><span>INABANGA <small>VEHICLE SERVICES</small></span></div><div class="hero-copy"><span class="eyebrow">MUNICIPAL SERVICES</span><h1>Review.<br><em>Approve.</em><br>Keep moving.</h1><p>Manage reservation reviews, approvals, vehicle assignments and trip records.</p></div><div class="car-illustration">🚙</div><div class="art-footer">Office of the Municipal Mayor · General Services Office</div></section>
<section class="login-panel"><div class="mobile-brand">⌖ <b>Vehicle Reservation</b></div><div class="panel-top"><span class="eyebrow">AUTHORIZED STAFF</span><h2>Staff sign in</h2><p>Use the municipal staff account assigned to you.</p></div>
<?php if ($error): ?><div class="alert error"><?=h($error)?></div><?php endif; ?>
<form method="post" class="stack"><label>Staff email address<input type="email" name="email" placeholder="name@inabanga.gov.ph" required autocomplete="username"></label><label>Password<input type="password" name="password" placeholder="Enter your password" required autocomplete="current-password"></label><button class="btn primary full" type="submit">Staff login <span>→</span></button></form>
<a class="btn primary full" href="home.php" style="margin-top:16px">Back to home <span>→</span></a><p class="tiny muted">For authorized municipal use only.</p></section></main></body></html>
