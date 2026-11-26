<?php
require 'config.php';
if (logged_in()) go('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare('SELECT id,full_name,email,password_hash,role FROM users WHERE email=? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$user['id'],'name'=>$user['full_name'],'email'=>$user['email'],'role'=>$user['role']];
        go('dashboard.php');
    } else $error = 'Incorrect email or password.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Vehicle Reservation | Inabanga</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-page"><main class="login-wrap"><section class="login-art"><div class="brand"><span class="brand-icon">⌖</span><span>INABANGA <small>VEHICLE SERVICES</small></span></div><div class="hero-copy"><span class="eyebrow">MUNICIPAL SERVICES</span><h1>Your trip.<br><em>One simple</em><br>reservation.</h1><p>Request, approve, schedule, and track municipal vehicles in one place.</p></div><div class="car-illustration">🚙</div><div class="art-footer">Office of the Municipal Mayor · General Services Office</div></section>
<section class="login-panel"><div class="mobile-brand">⌖ <b>Vehicle Reservation</b></div><div class="panel-top"><span class="eyebrow">WELCOME BACK</span><h2>Sign in to your account</h2><p>Access your municipal reservation workspace.</p></div>
<?php if ($error): ?><div class="alert error"><?=h($error)?></div><?php endif; ?>
<form method="post" class="stack"><label>Email address<input type="email" name="email" placeholder="name@example.com" required autocomplete="username"></label><label>Password<input type="password" name="password" placeholder="Enter your password" required autocomplete="current-password"></label><button class="btn primary full" type="submit">Sign in <span>→</span></button></form>
<p class="tiny muted">New requester? <a class="text-link" href="register.php">Create an account</a> · <a class="text-link" href="home.php">Back to home</a></p>
<div class="login-note"><b>Demo access</b><p>admin@inabanga.gov.ph · secretary@inabanga.gov.ph<br>gso@inabanga.gov.ph · requester@example.com<br>Password for all: <b>password</b></p></div><p class="tiny muted">For authorized municipal use only.</p></section></main></body></html>
