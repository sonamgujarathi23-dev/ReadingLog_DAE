<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: books.php');
  exit;
}

$title       = trim($_POST['title'] ?? '');
$author      = trim($_POST['author'] ?? '');
$total_pages = (int)($_POST['total_pages'] ?? 0);
$pages_read  = (int)($_POST['pages_read'] ?? 0);

if ($title === '' || $total_pages < 1) {
  die('Title and total pages are required. <a href="books.php">Go back</a>');
}

// Pages read cannot be negative or more than the total
$pages_read = max(0, min($pages_read, $total_pages));

// Work out the status automatically
if ($pages_read >= $total_pages) {
  $status = 'finished';
  $finished_on = date('Y-m-d');
} elseif ($pages_read > 0) {
  $status = 'reading';
  $finished_on = null;
} else {
  $status = 'to_read';
  $finished_on = null;
}

$stmt = $pdo->prepare(
  'INSERT INTO books (title, author, total_pages, pages_read, status, finished_on)
   VALUES (?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$title, $author, $total_pages, $pages_read, $status, $finished_on]);

header('Location: books.php');
exit;
