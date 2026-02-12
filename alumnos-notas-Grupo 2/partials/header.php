<?php declare(strict_types=1); ?>
<?php
  $prefix = (strpos($_SERVER['SCRIPT_NAME'], '/reportes/') !== false) ? '../' : '';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Alumnos y Notas</title>
  <link href="<?= $prefix ?>vendor/bootstrap5/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $prefix ?>assets/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="<?= $prefix ?>index.php">CRUD Alumnos/Notas</a>
  </div>
</nav>
<div class="container my-4">