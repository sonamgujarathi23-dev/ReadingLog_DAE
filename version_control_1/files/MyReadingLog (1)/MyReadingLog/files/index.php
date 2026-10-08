<?php
require 'db.php';

$goal  = 20; // daily goal in minutes (change here)
$today = date('Y-m-d');

// Today's minutes
$stmt = $pdo->prepare('SELECT COALESCE(SUM(minutes_read),0) FROM reading_sessions WHERE session_date = ?');
$stmt->execute([$today]);
$todayMin = (int)$stmt->fetchColumn();

// Reading streak (consecutive days with a session, ending today or yesterday)
$dates = $pdo->query('SELECT DISTINCT session_date FROM reading_sessions WHERE minutes_read > 0')->fetchAll(PDO::FETCH_COLUMN);
$set = array_flip($dates);
$day = new DateTime($today);
if (!isset($set[$day->format('Y-m-d')])) { $day->modify('-1 day'); }
$streak = 0;
while (isset($set[$day->format('Y-m-d')])) { $streak++; $day->modify('-1 day'); }

// Books
$totalBooks    = (int)$pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
$finishedBooks = (int)$pdo->query("SELECT COUNT(*) FROM books WHERE status = 'finished'")->fetchColumn();

// Total reading time
$row = $pdo->query('SELECT COALESCE(SUM(minutes_read),0) AS m, COUNT(*) AS c FROM reading_sessions')->fetch(PDO::FETCH_ASSOC);
$totalHours = round($row['m'] / 60, 1);
$sessionCount = (int)$row['c'];

// Last 7 days
$start = date('Y-m-d', strtotime('-6 days'));
$q = $pdo->prepare('SELECT session_date, SUM(minutes_read) FROM reading_sessions WHERE session_date >= ? GROUP BY session_date');
$q->execute([$start]);
$perDay = $q->fetchAll(PDO::FETCH_KEY_PAIR);
$week = [];
for ($i = 6; $i >= 0; $i--) {
  $d = date('Y-m-d', strtotime("-$i days"));
  $week[] = ['label' => date('D', strtotime($d)), 'min' => (int)($perDay[$d] ?? 0)];
}
$maxMin = max(40, max(array_column($week, 'min')));

// Currently reading
$reading = $pdo->query("SELECT * FROM books WHERE status = 'reading' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Progress · My Reading Log</title><link rel="stylesheet" href="style.css"></head><body><div class="wrap">
<h1>📚 My Reading Log</h1>
<nav><a href="index.php" class="on">📊 Progress</a><a href="log.php">⏱ Log reading</a><a href="books.php">📚 My books</a><a href="kids.php">🧒 Kids' books</a></nav>

<div class="grid">
<div class="card"><div class="mu">Today</div><div class="big"><?= $todayMin ?> min</div><progress value="<?= min($todayMin, $goal) ?>" max="<?= $goal ?>"></progress><div class="mu">Daily goal: <?= $goal ?> min</div></div>
<div class="card"><div class="mu">Reading streak</div><div class="big"><?= $streak ?> 🔥</div><div class="mu">days in a row</div></div>
<div class="card"><div class="mu">Books finished</div><div class="big"><?= $finishedBooks ?></div><div class="mu">of <?= $totalBooks ?> in library</div></div>
<div class="card"><div class="mu">Total reading time</div><div class="big"><?= $totalHours ?> h</div><div class="mu"><?= $sessionCount ?> sessions</div></div>
</div>

<h2>Last 7 days (minutes)</h2>
<div class="card"><div class="bars">
<?php foreach ($week as $w): ?>
  <div><span style="height:<?= max(2, (int)round($w['min'] / $maxMin * 120)) ?>px"></span><?= $w['min'] ?><br><?= $w['label'] ?></div>
<?php endforeach; ?>
</div></div>

<h2>Currently reading</h2>
<?php if (count($reading) === 0): ?>
  <div class="card mu">No books in progress. Start one on <a href="books.php">My books</a>.</div>
<?php endif; ?>
<?php foreach ($reading as $i => $b): ?>
  <?php if ($i > 0): ?><br><?php endif; ?>
  <div class="card"><b><?= htmlspecialchars($b['title']) ?></b> <span class="mu">· <?= htmlspecialchars($b['author'] ?? '') ?></span>
    <progress value="<?= (int)$b['pages_read'] ?>" max="<?= (int)$b['total_pages'] ?>"></progress>
    <div class="mu"><?= (int)$b['pages_read'] ?> of <?= (int)$b['total_pages'] ?> pages read · <b><?= (int)$b['pages_left'] ?> pages left</b></div>
  </div>
<?php endforeach; ?>
</div></body></html>
