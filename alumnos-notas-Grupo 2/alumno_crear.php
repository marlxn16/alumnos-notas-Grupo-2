<?php
declare(strict_types=1);
require __DIR__ . "/config/db.php";
require __DIR__ . "/helpers/functions.php";

$errores = [];
$nombre = $apellido = $correo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nombre = trim((string)($_POST["nombre"] ?? ""));
  $apellido = trim((string)($_POST["apellido"] ?? ""));
  $correo = trim((string)($_POST["correo"] ?? ""));

  if ($nombre === "") $errores[] = "El nombre es obligatorio.";
  if ($apellido === "") $errores[] = "El apellido es obligatorio.";
  if ($correo === "" || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "Correo inválido.";

  if (!$errores) {
    $stmt = $pdo->prepare("INSERT INTO alumno (nombre, apellido, correo) VALUES (?, ?, ?)");
    try {
      $stmt->execute([$nombre, $apellido, $correo]);
      header("Location: index.php");
      exit;
    } catch (PDOException $e) {
      $errores[] = "No se pudo guardar. Puede que el correo ya exista.";
    }
  }
}

require __DIR__ . "/partials/header.php";
?>

<div class="card p-4">
  <h4>Registrar alumno</h4>

  <?php if ($errores): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Nombre</label>
      <input class="form-control" name="nombre" value="<?= e($nombre) ?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Apellido</label>
      <input class="form-control" name="apellido" value="<?= e($apellido) ?>" required>
    </div>
    <div class="col-md-12">
      <label class="form-label">Correo</label>
      <input class="form-control" name="correo" value="<?= e($correo) ?>" required>
    </div>
    <div class="col-12 d-flex gap-2">
      <button class="btn btn-success">Guardar</button>
      <a class="btn btn-secondary" href="index.php">Cancelar</a>
    </div>
  </form>
</div>

<?php require __DIR__ . "/partials/footer.php"; ?>