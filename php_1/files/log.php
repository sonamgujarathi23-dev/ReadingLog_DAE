<?php
require 'db.php';

// Books for the dropdown (unfinished first)
$books = $pdo->query("SELECT id, title, status FROM books ORDER BY status = 'finished', title")->fetchAll(PDO::FETCH_ASSOC);

// Recent sessions (newest first)
$sessions = $pdo->query(
  'SELECT s.session_date, b.title, s.minutes_read, s.pages_read
   FROM reading_sessions s
   JOIN books b ON b.id = s.book_id
   ORDER BY s.session_date DESC, s.id DESC
   LIMIT 20'
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Log reading · My Reading Log</title><link rel="stylesheet" href="style.css"></head><body><div class="wrap">
<h1>📚 My Reading Log</h1>
<nav><a href="index.php">📊 Progress</a><a href="log.php" class="on">⏱ Log reading</a><a href="books.php">📚 My books</a><a href="kids.php">🧒 Kids' books</a></nav>

<div class="card"><b>Log a reading session</b>
<?php if (count($books) === 0): ?>
  <p class="mu">You have no books yet. <a href="books.php">Add a book first</a>.</p>
<?php else: ?>
<form action="save_session.php" method="post">
<label for="book_id">Book</label>
<select id="book_id" name="book_id">
<?php foreach ($books as $b): ?>
  <option value="<?= (int)$b['id'] ?>"><?= htmlspecialchars($b['title']) ?><?= $b['status'] === 'finished' ? ' (finished)' : '' ?></option>
<?php endforeach; ?>
</select>
<label for="session_date">Date</label><input id="session_date" name="session_date" type="date" value="<?= date('Y-m-d') ?>" required>
<label for="minutes_read">Minutes read</label><input id="minutes_read" name="minutes_read" type="number" min="1" required>
<label for="pages_read">Pages read</label><input id="pages_read" name="pages_read" type="number" min="0" value="0">
<button type="submit">Save session</button>
</form>
<?php endif; ?>
</div>

<h2>Recent sessions</h2>
<div class="scroll"><table>
<tr><th>Date</th><th>Book</th><th>Minutes</th><th>Pages</th></tr>
<?php if (count($sessions) === 0): ?>
<tr><td colspan="4">No sessions yet. Log your first one above.</td></tr>
<?php endif; ?>
<?php foreach ($sessions as $s): ?>
<tr>
  <td><?= htmlspecialchars($s['session_date']) ?></td>
  <td><?= htmlspecialchars($s['title']) ?></td>
  <td><?= (int)$s['minutes_read'] ?></td>
  <td><?= (int)$s['pages_read'] ?></td>
</tr>
<?php endforeach; ?>
</table></div>
</div></body></html>
