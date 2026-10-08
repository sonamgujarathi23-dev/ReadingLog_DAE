<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: kids.php');
  exit;
}

$kid_id = (int)($_POST['kids_book_id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM kids_books WHERE id = ?');
$stmt->execute([$kid_id]);
$kid = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$kid) {
  die('Book not found. <a href="kids.php">Go back</a>');
}

// Do not add the same title twice
$check = $pdo->prepare('SELECT COUNT(*) FROM books WHERE title = ?');
$check->execute([$kid['title']]);

if ((int)$check->fetchColumn() === 0) {
  $ins = $pdo->prepare(
    "INSERT INTO books (title, author, total_pages, pages_read, status) VALUES (?, ?, ?, 0, 'to_read')"
  );
  $ins->execute([$kid['title'], $kid['author'], $kid['pages']]);
}

header('Location: books.php');
exit;
