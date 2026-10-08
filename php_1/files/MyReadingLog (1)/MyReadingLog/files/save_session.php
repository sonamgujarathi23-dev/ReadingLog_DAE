<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: log.php');
  exit;
}

$book_id      = (int)($_POST['book_id'] ?? 0);
$session_date = $_POST['session_date'] ?? '';
$minutes_read = (int)($_POST['minutes_read'] ?? 0);
$pages_read   = max(0, (int)($_POST['pages_read'] ?? 0));

// Validate the date (YYYY-MM-DD) and minutes
$d = DateTime::createFromFormat('Y-m-d', $session_date);
if (!$d || $d->format('Y-m-d') !== $session_date || $minutes_read < 1 || $book_id < 1) {
  die('Please enter a valid book, date and minutes. <a href="log.php">Go back</a>');
}

try {
  $pdo->beginTransaction();

  // Lock and load the book
  $stmt = $pdo->prepare('SELECT * FROM books WHERE id = ? FOR UPDATE');
  $stmt->execute([$book_id]);
  $book = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$book) {
    $pdo->rollBack();
    die('Book not found. <a href="log.php">Go back</a>');
  }

  // Do not allow more pages than the book has left
  $pages_read = min($pages_read, (int)$book['pages_left']);

  // 1) Save the session
  $ins = $pdo->prepare(
    'INSERT INTO reading_sessions (book_id, session_date, minutes_read, pages_read)
     VALUES (?, ?, ?, ?)'
  );
  $ins->execute([$book_id, $session_date, $minutes_read, $pages_read]);

  // 2) Update the book's progress
  $new_read = (int)$book['pages_read'] + $pages_read;
  if ($new_read >= (int)$book['total_pages']) {
    $status = 'finished';
    $finished_on = $book['finished_on'] ?: $session_date;
  } elseif ($new_read > 0) {
    $status = 'reading';
    $finished_on = null;
  } else {
    $status = 'to_read';
    $finished_on = null;
  }

  $upd = $pdo->prepare('UPDATE books SET pages_read = ?, status = ?, finished_on = ? WHERE id = ?');
  $upd->execute([$new_read, $status, $finished_on, $book_id]);

  $pdo->commit();
} catch (Exception $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  die('Could not save the session: ' . htmlspecialchars($e->getMessage()));
}

header('Location: log.php');
exit;
