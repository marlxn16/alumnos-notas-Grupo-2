<?php
declare(strict_types=1);
require __DIR__ . "/config/db.php";
require __DIR__ . "/helpers/functions.php";

$alumno_id = (int)($_GET["alumno_id"] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM alumno WHERE id = ?");
$stmt->execute([$alumno_id]);
$alumno = $stmt->fetch();
if (!$alumno) { http_response_code(404); exit("Alumno no encontrado"); }

$errores = [];
$nota = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nota = trim((string)($_POST["nota"] ?? ""));
  if ($nota === "" || !is_numeric($nota)) $errores[] = "La nota debe ser numérica.";
  else {
    $val = (float)$nota;
    if ($val < 0 || $val > 10) $errores[] = "La nota debe estar entre 0 y 10.";
  }

  if (!$errores) {
    $ins = $pdo->prepare("INSERT INTO nota (alumno_id, nota) VALUES (?, ?)");
    $ins->execute([$alumno_id, (float)$nota]);
    header("Location: alumno_detalle.php?id=" . $alumno_id);
    exit;
  }
}

require __DIR__ . "/partials/header.php";
?>

<div class="card p-4">
  <h4>Registrar nota</h4>
  <p class="mb-1"><b>Alumno:</b> <?= e($alumno["nombre"] . " " . $alumno["apellido"]) ?></p>
  <p class="text-muted">Rango permitido: 0 a 10</p>

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
      <input class="form-control" name="nota" value="<?= e($nota) ?>" placeholder="Ej: 8.50" required>
    </div>
    <div class="col-12 d-flex gap-2">
      <button class="btn btn-success">Guardar</button>
      <a class="btn btn-secondary" href="alumno_detalle.php?id=<?= $alumno_id ?>">Cancelar</a>
    </div>
  </form>
</div>

<?php require __DIR__ . "/partials/footer.php"; ?>