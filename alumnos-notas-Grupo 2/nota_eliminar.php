<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
  $del = $pdo->prepare("DELETE FROM nota WHERE id = ?");
  $del->execute([$id]);
}

header("Location: index.php");
exit;
