<?php
require 'config.php';

$user = current_user();
$role = $user['role'] ?? null;
$roleLabels = ['admin' => 'Administrator', 'secretary' => 'Secretary', 'gso' => 'General Services Office', 'requester' => 'Requester'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Municipal vehicle reservation | LGU Inabanga</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,500..800&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --bay:#0F3A5F; --bay-deep:#0A2740; --sign:#FFC629; --sign-dark:#E5AC10;
  --ink:#16202B; --muted:#55626F; --line:#C9D3DC; --page:#EEF2F5; --paper:#FFFFFF;
  --head:'Archivo','Arial Narrow','Helvetica Neue',Arial,sans-serif;
  --body:'IBM Plex Sans','Segoe UI',Roboto,Arial,sans-serif;
}
*{box-sizing:border-box}
body{margin:0;background:var(--page);color:var(--ink);font:400 16px/1.55 var(--body)}
a{color:inherit}
:focus-visible{outline:3px solid var(--sign);outline-offset:2px}
.wrap{width:min(1120px,100% - 40px);margin-inline:auto}

.hero{position:relative;isolation:isolate;background:transparent;color:#fff;padding-bottom:72px}
.hero:before{content:"";position:absolute;inset:-8px;z-index:-1;background:linear-gradient(rgba(10,39,64,.42),rgba(15,58,95,.34)),url('assets/vehicle-background.png') center/cover no-repeat;filter:blur(3px);transform:scale(1.02)}
.bar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:20px 0}
.brand{display:flex;align-items:center;gap:12px;text-decoration:none}
.brand-mark{width:38px;height:38px;border-radius:8px;background:var(--sign);color:var(--bay-deep);display:grid;place-items:center;font:700 20px/1 var(--head)}
.brand b{display:block;font:700 18px/1.1 var(--head);font-stretch:80%;letter-spacing:.02em}
.brand span span{display:block;font-size:13px;color:#B9CADA}
.nav{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.nav a{text-decoration:none;font-weight:500;padding:9px 14px;border-radius:6px;display:inline-flex;align-items:center;gap:8px}
.nav a.link:hover{background:rgba(255,255,255,.1)}
.nav a.pill{background:var(--sign);color:var(--bay-deep);font-weight:600}
.nav a.pill:hover{background:var(--sign-dark)}
.nav a.auth-link{border:1px solid #ffffff70;background:#ffffff10;color:#fff}
.nav a.auth-link:hover{background:#ffffff20}

.hero-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,460px);gap:56px;align-items:center;padding-top:36px}
h1{font:700 clamp(40px,6vw,68px)/.98 var(--head);font-stretch:75%;letter-spacing:-.01em;margin:8px 0 20px}
.lede{font-size:18px;color:#D5E1EC;max-width:46ch;margin:0 0 28px}
.facts{list-style:none;margin:0;padding:0;display:grid;gap:14px;max-width:46ch}
.facts li{padding-left:18px;border-left:3px solid var(--sign);color:#E4ECF3}
.facts b{color:#fff;font-weight:600}

/* trip-ticket card holding the two entry points */
.ticket{position:relative;background:#F2F6FB;color:var(--ink);border-radius:6px;box-shadow:0 18px 40px rgba(5,20,35,.35)}
.ticket-head{padding:22px 26px 18px}
.ticket-head h2{margin:0 0 4px;font:700 26px/1 var(--head);font-stretch:80%}
.ticket-head p{margin:0;color:var(--muted);font-size:14px}
.choice{position:relative;padding:22px 26px 26px;border-top:2px dashed var(--line)}
.choice::before,.choice::after{content:"";position:absolute;top:-11px;width:20px;height:20px;border-radius:50%;background:var(--bay)}
.choice::before{left:-10px}.choice::after{right:-10px}
.choice h3{margin:0 0 6px;font:700 20px/1.1 var(--head);font-stretch:85%}
.choice p{margin:0 0 16px;color:var(--muted);font-size:15px}
.btn{display:block;width:100%;border:2px solid transparent;border-radius:6px;padding:13px 18px;font:600 16px/1.2 var(--body);text-decoration:none;text-align:center}
.btn.primary{background:var(--sign);color:var(--bay-deep);border-color:var(--sign)}
.btn.primary:hover{background:var(--sign-dark);border-color:var(--sign-dark)}
.btn.ghost{background:transparent;color:var(--bay);border-color:var(--bay)}
.btn.ghost:hover{background:rgba(15,58,95,.07)}

section.block{padding:64px 0 0}
h2.title{font:700 clamp(28px,3.6vw,40px)/1.05 var(--head);font-stretch:80%;margin:0 0 8px}
.sub{color:var(--muted);margin:0 0 28px;max-width:60ch}
.steps{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(5,1fr);border-top:3px solid var(--bay);counter-reset:s}
.steps li{counter-increment:s;padding:18px 16px 0 0;position:relative}
.steps li::before{content:counter(s);display:grid;place-items:center;width:30px;height:30px;margin-top:-34px;margin-bottom:12px;border-radius:50%;background:var(--bay);color:#fff;font:700 15px/1 var(--head)}
.steps b{display:block;font-weight:600}
.steps span{color:var(--muted);font-size:14px}
footer{margin-top:72px;padding:26px 0 40px;border-top:1.5px solid var(--line);color:var(--muted);font-size:14px}
footer .wrap{display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap}

@media (max-width:900px){
  .hero-grid{grid-template-columns:1fr;gap:36px}
  .steps{grid-template-columns:1fr;border-top:0;border-left:3px solid var(--bay)}
  .steps li{padding:0 0 22px 28px}
  .steps li::before{position:absolute;left:-16px;top:0;margin:0}
}
@media (max-width:520px){
  .ticket-head,.choice{padding-inline:18px}
}
</style>
</head>
<body>

<header class="hero">
  <div class="wrap">
    <div class="bar">
      <a class="brand" href="home.php" aria-label="Inabanga Vehicle Services home">
        <span class="brand-mark" aria-hidden="true">I</span>
        <span><b>Inabanga vehicle services</b><span>Municipal General Services Office</span></span>
      </a>
      <nav class="nav" aria-label="Account">
        <?php if (logged_in()): ?>
          <a class="link" href="dashboard.php">Dashboard</a>
          <a class="pill" href="logout.php">Sign out</a>
        <?php else: ?>
          <a class="auth-link" href="home.php">Home</a>
          <a class="auth-link" href="index.php">Requester login</a>
          <a class="auth-link" href="staff-login.php">Staff login</a>
          <a class="pill" href="register.php">Create account</a>
        <?php endif; ?>
      </nav>
    </div>

    <div class="hero-grid">
      <div>
        <h1>Book a municipal vehicle</h1>
        <p class="lede">Create an account to request a vehicle, or sign in to follow your requests.</p>
        <ul class="facts">
          <li><b>Need an ambulance?</b> Mark your request urgent. Urgent requests are listed first for review.</li>
          <li><b>Every request is tracked.</b> You'll see each decision, the assigned vehicle and driver, and the return.</li>
        </ul>
      </div>

      <div class="ticket">
        <?php if (logged_in()): ?>
          <div class="ticket-head">
            <h2>You're signed in</h2>
            <p><?=h($user['name'])?>, <?=h($roleLabels[$role] ?? $role)?></p>
          </div>
          <div class="choice">
            <h3>Continue where you left off</h3>
            <p><?=$role === 'requester' ? 'Open your dashboard or start a new vehicle request.' : 'Open your dashboard to review and manage reservations.'?></p>
            <a class="btn primary" href="dashboard.php">Go to dashboard</a>
          </div>
        <?php else: ?>
          <div class="ticket-head">
            <h2>Get started</h2>
            <p>Choose how you want to continue.</p>
          </div>
          <div class="choice">
            <h3>New requester</h3>
            <p>Create an account to request a municipal vehicle or ambulance.</p>
            <a class="btn primary" href="register.php">Create account</a>
          </div>
          <div class="choice">
            <h3>Returning requester</h3>
            <p>Sign in to follow your vehicle requests and reservation updates.</p>
            <a class="btn ghost" href="index.php">Requester login</a>
          </div>
          <div class="choice">
            <h3>Municipal staff</h3>
            <p>Authorized staff can sign in to manage vehicle reservations.</p>
            <a class="btn ghost" href="staff-login.php">Staff login</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<main class="wrap">
  <section class="block" aria-labelledby="flow">
    <h2 class="title" id="flow">What happens after you send a request</h2>
    <p class="sub">Every request goes through the same review before a vehicle is released.</p>
    <ol class="steps">
      <li><b>Administrative review</b><span>The administrator checks your request and forwards it.</span></li>
      <li><b>Secretary decision</b><span>The secretary approves or declines it.</span></li>
      <li><b>Vehicle and driver</b><span>The GSO assigns a vehicle and driver for your dates.</span></li>
      <li><b>Dispatch</b><span>Odometer and fuel are recorded when the vehicle leaves.</span></li>
      <li><b>Return</b><span>Mileage, fuel and condition are recorded to close the trip.</span></li>
    </ol>
  </section>
</main>

<footer>
  <div class="wrap">
    <span>© <?=date('Y')?> LGU Inabanga, Bohol</span>
    <span>Questions? Visit the Municipal General Services Office.</span>
  </div>
</footer>

</body>
</html>
