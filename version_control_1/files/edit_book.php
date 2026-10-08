<?php
require 'db.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Load the book being edited
$stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
$stmt->execute([$id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$book) {
  die('Book not found. <a href="books.php">Go back</a>');
}

$error = '';

// UPDATE: runs when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title       = trim($_POST['title'] ?? '');
  $author      = trim($_POST['author'] ?? '');
  $total_pages = (int)($_POST['total_pages'] ?? 0);
  $pages_read  = (int)($_POST['pages_read'] ?? 0);

  if ($title === '' || $total_pages < 1) {
    $error = 'Title and total pages are required.';
  } else {
    $pages_read = max(0, min($pages_read, $total_pages));

    if ($pages_read >= $total_pages) {
      $status = 'finished';
      // keep the original finished date if there was one
      $finished_on = $book['finished_on'] ?: date('Y-m-d');
    } elseif ($pages_read > 0) {
      $status = 'reading';
      $finished_on = null;
    } else {
      $status = 'to_read';
      $finished_on = null;
    }

    $upd = $pdo->prepare(
      'UPDATE books
       SET title = ?, author = ?, total_pages = ?, pages_read = ?, status = ?, finished_on = ?
       WHERE id = ?'
    );
    $upd->execute([$title, $author, $total_pages, $pages_read, $status, $finished_on, $id]);

    header('Location: books.php');
    exit;
  }

  // keep what the user typed if there was an error
  $book['title'] = $title; $book['author'] = $author;
  $book['total_pages'] = $total_pages; $book['pages_read'] = $pages_read;
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Edit book · My Reading Log</title><link rel="stylesheet" href="style.css"></head><body><div class="wrap">
<h1>📚 My Reading Log</h1>
<nav><a href="index.html">📊 Progress</a><a href="log.html">⏱ Log reading</a><a href="books.php" class="on">📚 My books</a><a href="kids.html">🧒 Kids' books</a></nav>

<div class="card"><b>Edit book</b>
<?php if ($error): ?><p style="color:#d64545"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form action="edit_book.php" method="post">
<input type="hidden" name="id" value="<?= (int)$book['id'] ?>">
<label for="title">Title</label><input id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
<label for="author">Author</label><input id="author" name="author" value="<?= htmlspecialchars($book['author'] ?? '') ?>">
<label for="total_pages">Total pages</label><input id="total_pages" name="total_pages" type="number" min="1" value="<?= (int)$book['total_pages'] ?>" required>
<label for="pages_read">Pages read</label><input id="pages_read" name="pages_read" type="number" min="0" value="<?= (int)$book['pages_read'] ?>">
<button type="submit">Save changes</button>
<a href="books.php" style="margin-left:12px">Cancel</a>
</form></div>
</div></body></html>
