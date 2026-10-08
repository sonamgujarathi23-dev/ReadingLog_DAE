<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id > 0) {
    // reading_sessions rows for this book are deleted automatically (ON DELETE CASCADE)
    $stmt = $pdo->prepare('DELETE FROM books WHERE id = ?');
    $stmt->execute([$id]);
  }
}

header('Location: books.php');
exit;
