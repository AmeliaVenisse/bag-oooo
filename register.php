<?php
require 'config.php';
if (logged_in()) go('dashboard.php');

$error = '';
$name = '';
$email = '';
$address = '';
$contactNumber = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name     = trim($_POST['full_name'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name === '' || mb_strlen($name) > 120) {
        $error = 'Please enter your full name (up to 120 characters).';
    } elseif ($address === '' || mb_strlen($address) > 255) {
        $error = 'Please enter your address (up to 255 characters).';
    } elseif (!preg_match('/^[0-9+()\-\s]{7,30}$/', $contactNumber)) {
        $error = 'Please enter a valid contact number.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 160) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // Self-registration always creates a requester. Other roles are never assignable here.
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'requester';
            $stmt = $conn->prepare('INSERT INTO users (full_name,email,password_hash,role,address,contact_number) VALUES (?,?,?,?,?,?)');
            $stmt->bind_param('ssssss', $name, $email, $hash, $role, $address, $contactNumber);
            $stmt->execute();
            $newId = (int)$conn->insert_id;

            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => $newId, 'name' => $name, 'email' => $email, 'role' => $role];
            log_activity($conn, null, 'Account registered', $email);

            // Straight to the vehicle request form.
            go('reservations.php?new=1');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $error = 'An account with that email already exists. Try signing in instead.';
            } else {
                error_log($e->getMessage());
                $error = 'Unable to create your account right now. Please try again.';
            }
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Create account | Vehicle Reservation</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-page"><main class="login-wrap"><section class="login-art"><div class="brand"><span class="brand-icon">⌖</span><span>INABANGA <small>VEHICLE SERVICES</small></span></div><div class="hero-copy"><span class="eyebrow">MUNICIPAL SERVICES</span><h1>Need a ride?<br><em>Register and</em><br>request one.</h1><p>Create a requester account, then submit your vehicle reservation in minutes.</p></div><div class="car-illustration">🚙</div><div class="art-footer">Office of the Municipal Mayor · General Services Office</div></section>
<section class="login-panel"><div class="mobile-brand">⌖ <b>Vehicle Reservation</b></div><div class="panel-top"><span class="eyebrow">GET STARTED</span><h2>Create your account</h2><p>Register as a requester to book a municipal vehicle.</p></div>
<?php if ($error): ?><div class="alert error"><?=h($error)?></div><?php endif; ?>
<form method="post" class="stack"><?=csrf_field()?>
<label>Full name<input type="text" name="full_name" value="<?=h($name)?>" placeholder="Juan Dela Cruz" required maxlength="120" autocomplete="name"></label>
<label>Address<input type="text" name="address" value="<?=h($address)?>" placeholder="Inabanga, Bohol" required maxlength="255" autocomplete="street-address"></label>
<label>Contact number<input type="tel" name="contact_number" value="<?=h($contactNumber)?>" placeholder="09XXXXXXXXX" required maxlength="30" autocomplete="tel"></label>
<label>Email address<input type="email" name="email" value="<?=h($email)?>" placeholder="name@example.com" required maxlength="160" autocomplete="username"></label>
<label>Password<input type="password" name="password" placeholder="At least 8 characters" required minlength="8" autocomplete="new-password"></label>
<label>Confirm password<input type="password" name="confirm_password" placeholder="Re-enter your password" required minlength="8" autocomplete="new-password"></label>
<button class="btn primary full" type="submit">Create account &amp; request a vehicle <span>→</span></button></form>
<div class="login-note"><p>Already have an account? <a class="text-link" href="index.php">Sign in</a> · <a class="text-link" href="home.php">Back to home</a></p></div><p class="tiny muted">For authorized municipal use only.</p></section></main></body></html>
