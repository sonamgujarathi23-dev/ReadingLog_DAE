<?php
require 'db.php';
$books = $pdo->query('SELECT * FROM books ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

$labels = ['to_read' => 'To read', 'reading' => 'Reading', 'finished' => '✅ Finished'];
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>My books · My Reading Log</title><link rel="stylesheet" href="style.css">
<style>
  .actions{white-space:nowrap}
  .actions form{display:inline}
  .btn-edit,.btn-del{display:inline-block;border:0;border-radius:8px;padding:6px 12px;font:inherit;font-size:.85rem;cursor:pointer;text-decoration:none;margin:0}
  .btn-edit{background:var(--ac);color:#fff}
  .btn-del{background:#d64545;color:#fff}
</style></head><body><div class="wrap">
<h1>📚 My Reading Log</h1>
<nav><a href="index.php">📊 Progress</a><a href="log.php">⏱ Log reading</a><a href="books.php" class="on">📚 My books</a><a href="kids.php">🧒 Kids' books</a></nav>

<div class="card"><b>Add a book</b>
<form action="add_book.php" method="post">
<label for="title">Title</label><input id="title" name="title" required>
<label for="author">Author</label><input id="author" name="author">
<label for="total_pages">Total pages</label><input id="total_pages" name="total_pages" type="number" min="1" required>
<label for="pages_read">Pages already read</label><input id="pages_read" name="pages_read" type="number" min="0" value="0">
<button type="submit">Add book</button>
</form></div>

<h2>My library</h2>
<div class="scroll"><table>
<tr><th>Title</th><th>Author</th><th>Total pages</th><th>Read</th><th>Left</th><th>Status</th><th>Progress</th><th>Actions</th></tr>
<?php if (count($books) === 0): ?>
<tr><td colspan="8">No books yet. Add your first book above.</td></tr>
<?php endif; ?>
<?php foreach ($books as $b): ?>
<tr>
  <td><?= htmlspecialchars($b['title']) ?></td>
  <td><?= htmlspecialchars($b['author'] ?? '') ?></td>
  <td><?= (int)$b['total_pages'] ?></td>
  <td><?= (int)$b['pages_read'] ?></td>
  <td><?= (int)$b['pages_left'] ?></td>
  <td<?= $b['status'] === 'finished' ? ' class="done"' : '' ?>><?= $labels[$b['status']] ?></td>
  <td><progress value="<?= (int)$b['pages_read'] ?>" max="<?= (int)$b['total_pages'] ?>"></progress></td>
  <td class="actions">
    <a class="btn-edit" href="edit_book.php?id=<?= (int)$b['id'] ?>">✏️ Edit</a>
    <form action="delete_book.php" method="post" onsubmit="return confirm('Delete this book and its reading sessions?');">
      <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
      <button type="submit" class="btn-del">🗑 Delete</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</table></div>
</div></body></html>
