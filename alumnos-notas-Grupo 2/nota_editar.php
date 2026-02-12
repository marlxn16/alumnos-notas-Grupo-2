<?php
declare(strict_types=1);
require __DIR__ . "/config/db.php";
require __DIR__ . "/helpers/functions.php";

$id = (int)($_GET["id"] ?? 0);
$stmt = $pdo->prepare("
  SELECT n.*, a.nombre, a.apellido
  FROM nota n JOIN alumno a ON a.id = n.alumno_id
  WHERE n.id = ?
");
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { http_response_code(404); exit("Nota no encontrada"); }

$errores = [];
$nota = (string)$row["nota"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nota = trim((string)($_POST["nota"] ?? ""));
  if ($nota === "" || !is_numeric($nota)) $errores[] = "La nota debe ser numérica.";
  else {
    $val = (float)$nota;
    if ($val < 0 || $val > 10) $errores[] = "La nota debe estar entre 0 y 10.";
  }

  if (!$errores) {
    $upd = $pdo->prepare("UPDATE nota SET nota = ? WHERE id = ?");
    $upd->execute([(float)$nota, $id]);
    header("Location: alumno_detalle.php?id=" . (int)$row["alumno_id"]);
    exit;
  }
}

require __DIR__ . "/partials/header.php";
?>

<div class="card p-4">
  <h4>Editar nota</h4>
  <p class="mb-3"><b>Alumno:</b> <?= e($row["nombre"] . " " . $row["apellido"]) ?></p>

  <?php if ($errores): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Nota</label>
      <input class="form-control" name="nota" value="<?= e($nota) ?>" required>
    </div>
    <div class="col-12 d-flex gap-2">
      <button class="btn btn-warning">Actualizar</button>
      <a class="btn btn-secondary" href="alumno_detalle.php?id=<?= (int)$row["alumno_id"] ?>">Cancelar</a>
    </div>
  </form>
</div>

<?php require __DIR__ . "/partials/footer.php"; ?>