<?php
require 'config.php';
require_role('requester');
$user = current_user();
$stmt = $conn->prepare('SELECT id,reference_no,purpose,destination,departure_at,status,created_at FROM reservations WHERE requester_id=? ORDER BY created_at DESC');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$rows = $stmt->get_result();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>History | Vehicle Reservation</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="app"><aside class="sidebar"><a class="side-brand" href="dashboard.php"><span class="brand-icon">⌖</span><span>VEHICLE<br><small>RESERVATION</small></span></a><div class="side-label">WORKSPACE</div><nav><a href="dashboard.php">▦ <span>Dashboard</span></a><a href="reservations.php">▤ <span>My Requests</span></a><a class="active" href="history.php">◷ <span>History</span></a></nav><div class="side-bottom"><a href="logout.php">↪ <span>Sign out</span></a></div></aside>
<main class="main"><header class="topbar"><div class="crumb">Workspace <span>/</span> History</div><div class="user-chip"><div class="avatar"><?=h(strtoupper(substr($user['name'],0,1)))?></div><div><b><?=h($user['name'])?></b><small>Requester</small></div></div></header>
<section class="welcome-row"><div><span class="eyebrow">REQUESTER RECORDS</span><h1>Reservation history</h1><p>Review your previous requests and their current statuses.</p></div><a class="btn primary" href="reservations.php?new=1">＋ New reservation</a></section>
<section class="content-card"><div class="table-wrap"><table><thead><tr><th>REFERENCE</th><th>PURPOSE</th><th>DESTINATION</th><th>DEPARTURE</th><th>SUBMITTED</th><th>STATUS</th><th></th></tr></thead><tbody><?php if(!$rows->num_rows):?><tr><td colspan="7" class="empty">No reservation history yet.</td></tr><?php endif;?><?php while($row=$rows->fetch_assoc()):?><tr><td><b class="ref"><?=h($row['reference_no'])?></b></td><td><?=h($row['purpose'])?></td><td><?=h($row['destination'])?></td><td><?=h(date('M d, Y · g:i A',strtotime($row['departure_at'])))?></td><td><?=h(date('M d, Y',strtotime($row['created_at'])))?></td><td><span class="status <?=strtolower(str_replace(' ','-',$row['status']))?>"><?=h($row['status'])?></span></td><td><a class="icon-link" href="reservations.php?id=<?=(int)$row['id']?>" aria-label="View reservation details">↗</a></td></tr><?php endwhile;?></tbody></table></div></section><footer>© <?=date('Y')?> LGU Inabanga, Bohol · Vehicle Reservation & Dispatch Tracking</footer></main></div></body></html>
