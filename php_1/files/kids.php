<?php
require 'db.php';

$kids = $pdo->query('SELECT * FROM kids_books ORDER BY age_group, id')->fetchAll(PDO::FETCH_ASSOC);

// Titles already in the user's library
$have = $pdo->query('SELECT title FROM books')->fetchAll(PDO::FETCH_COLUMN);
$have = array_map('strtolower', $have);

// Group by age
$groups = [];
foreach ($kids as $k) { $groups[$k['age_group']][] = $k; }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Kids' books · My Reading Log</title><link rel="stylesheet" href="style.css"></head><body><div class="wrap">
<h1>📚 My Reading Log</h1>
<nav><a href="index.php">📊 Progress</a><a href="log.php">⏱ Log reading</a><a href="books.php">📚 My books</a><a href="kids.php" class="on">🧒 Kids' books</a></nav>

<div class="chips">
<?php foreach (array_keys($groups) as $g): ?>
  <a class="chip" href="#age-<?= htmlspecialchars($g) ?>">Ages <?= htmlspecialchars(str_replace('-', '–', $g)) ?></a>
<?php endforeach; ?>
</div>

<?php foreach ($groups as $age => $list): ?>
<h2 id="age-<?= htmlspecialchars($age) ?>">Ages <?= htmlspecialchars(str_replace('-', '–', $age)) ?></h2>
<div class="grid">
<?php foreach ($list as $k): ?>
  <div class="card"><b><?= htmlspecialchars($k['title']) ?></b>
    <div class="mu"><?= htmlspecialchars($k['author'] ?? '') ?> · Ages <?= htmlspecialchars(str_replace('-', '–', $k['age_group'])) ?> · ~<?= (int)$k['pages'] ?> pages</div>
    <?php if (in_array(strtolower($k['title']), $have, true)): ?>
      <div class="done" style="margin-top:12px">✓ In your library</div>
    <?php else: ?>
      <form action="add_kids_book.php" method="post">
        <input type="hidden" name="kids_book_id" value="<?= (int)$k['id'] ?>">
        <button type="submit">+ Add to my library</button>
      </form>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>
<?php endforeach; ?>
</div></body></html>
