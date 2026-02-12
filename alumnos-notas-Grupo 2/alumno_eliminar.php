<?php
declare(strict_types=1);
require __DIR__ . "/config/db.php";
require __DIR__ . "/helpers/functions.php";

$id = (int)($_GET["id"] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM alumno WHERE id = ?");
$stmt->execute([$id]);
$alumno = $stmt->fetch();
if (!$alumno) { http_response_code(404); exit("Alumno no encontrado"); }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $del = $pdo->prepare("DELETE FROM alumno WHERE id = ?");
  $del->execute([$id]);
  header("Location: index.php");
  exit;
}

require __DIR__ . "/partials/header.php";
?>

<div class="card p-4">
  <h4 class="text-danger">Eliminar alumno</h4>
  <p>¿Seguro que deseas eliminar a <b><?= e($alumno["nombre"] . " " . $alumno["apellido"]) ?></b>?</p>
  <p class="text-muted mb-3">Se eliminarán también todas sus notas.</p>

  <form method="post" class="d-flex gap-2">
    <button class="btn btn-danger">Sí, eliminar</button>
    <a class="btn btn-secondary" href="index.php">Cancelar</a>
  </form>
</div>

<?php require __DIR__ . "/partials/footer.php"; ?>