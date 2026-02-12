<?php
declare(strict_types=1);
require __DIR__ . "/config/db.php";
require __DIR__ . "/helpers/functions.php";

$id = (int)($_GET["id"] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM alumno WHERE id = ?");
$stmt->execute([$id]);
$alumno = $stmt->fetch();
if (!$alumno) { http_response_code(404); exit("Alumno no encontrado"); }

$notasStmt = $pdo->prepare("SELECT * FROM nota WHERE alumno_id = ? ORDER BY id DESC");
$notasStmt->execute([$id]);
$notas = $notasStmt->fetchAll();

$promStmt = $pdo->prepare("SELECT AVG(nota) AS prom FROM nota WHERE alumno_id = ?");
$promStmt->execute([$id]);
$promRow = $promStmt->fetch();

$prom = $promRow["prom"] !== null ? (float)$promRow["prom"] : null;

require __DIR__ . "/partials/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-0"><?= e($alumno["nombre"] . " " . $alumno["apellido"]) ?></h3>
    <div class="text-muted"><?= e($alumno["correo"]) ?></div>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a class="btn btn-outline-secondary" href="index.php">Volver</a>
    <a class="btn btn-success" href="nota_crear.php?alumno_id=<?= $id ?>">+ Registrar nota</a>
    <a class="btn btn-outline-primary" href="reportes/reporte_excel.php?alumno_id=<?= $id ?>">CSV</a>
    <a class="btn btn-outline-danger" href="reportes/reporte_pdf.php?alumno_id=<?= $id ?>">PDF</a>
  </div>
</div>

<div class="card p-3 mb-3">
  <?php if ($prom === null): ?>
    <p class="mb-0">Promedio: <b>—</b> | Resultado: <b>—</b></p>
  <?php else: ?>
    <p class="mb-0">
      Promedio: <b><?= number_format($prom, 2) ?></b>
      | Resultado: <b><?= e(resultadoCualitativo($prom)) ?></b>
    </p>
  <?php endif; ?>
</div>

<div class="card p-3">
  <h5>Notas registradas</h5>
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Nota</th>
          <th>Fecha</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$notas): ?>
        <tr><td colspan="4" class="text-center py-4">No hay notas registradas.</td></tr>
      <?php else: ?>
        <?php foreach ($notas as $n): ?>
          <tr>
            <td><?= (int)$n["id"] ?></td>
            <td class="fw-semibold"><?= e((string)$n["nota"]) ?></td>
            <td class="text-muted"><?= e((string)$n["created_at"]) ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-warning" href="nota_editar.php?id=<?= (int)$n["id"] ?>">Editar</a>
              <a class="btn btn-sm btn-outline-danger" href="nota_eliminar.php?id=<?= (int)$n["id"] ?>">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . "/partials/footer.php"; ?>