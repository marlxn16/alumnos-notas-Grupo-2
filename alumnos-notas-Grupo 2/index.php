<?php
declare(strict_types=1);
require __DIR__ . "/config/db.php";
require __DIR__ . "/helpers/functions.php";

$sql = "
SELECT
  a.id, a.nombre, a.apellido, a.correo,
  (SELECT COUNT(*) FROM nota n WHERE n.alumno_id = a.id) AS total_notas,
  (SELECT AVG(n.nota) FROM nota n WHERE n.alumno_id = a.id) AS promedio
FROM alumno a
ORDER BY a.id DESC
";
$alumnos = $pdo->query($sql)->fetchAll();

require __DIR__ . "/partials/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Listado de alumnos</h3>
  <a class="btn btn-success" href="alumno_crear.php">+ Registrar alumno</a>
</div>

<div class="card p-3 mb-3">
  <div class="d-flex gap-2 flex-wrap">
    <a class="btn btn-outline-primary" href="reportes/reporte_excel.php">Descargar CSV </a>
    <a class="btn btn-outline-danger" href="reportes/reporte_pdf.php">Descargar PDF </a>
  </div>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Alumno</th>
          <th>Correo</th>
          <th class="text-center">Notas</th>
          <th class="text-center">Promedio</th>
          <th class="text-center">Resultado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$alumnos): ?>
        <tr><td colspan="7" class="text-center py-4">No hay alumnos registrados.</td></tr>
      <?php else: ?>
        <?php foreach ($alumnos as $a):
          $prom = $a["promedio"] !== null ? (float)$a["promedio"] : 0.0;
          $promFmt = $a["promedio"] !== null ? number_format($prom, 2) : "—";
          $res = $a["promedio"] !== null ? resultadoCualitativo($prom) : "—";
        ?>
          <tr>
            <td><?= (int)$a["id"] ?></td>
            <td><?= e($a["nombre"] . " " . $a["apellido"]) ?></td>
            <td><?= e($a["correo"]) ?></td>
            <td class="text-center">
              <span class="badge bg-secondary"><?= (int)$a["total_notas"] ?></span>
            </td>
            <td class="text-center fw-semibold"><?= $promFmt ?></td>
            <td class="text-center">
              <?php if ($res === "Sobresaliente"): ?>
                <span class="badge bg-success"><?= e($res) ?></span>
              <?php elseif ($res === "Notable"): ?>
                <span class="badge bg-primary"><?= e($res) ?></span>
              <?php elseif ($res === "Bien"): ?>
                <span class="badge bg-warning text-dark"><?= e($res) ?></span>
              <?php elseif ($res === "Suspenso"): ?>
                <span class="badge bg-danger"><?= e($res) ?></span>
              <?php else: ?>
                <span class="badge bg-light text-dark">—</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="alumno_detalle.php?id=<?= (int)$a["id"] ?>">Ver</a>
              <a class="btn btn-sm btn-outline-success" href="nota_crear.php?alumno_id=<?= (int)$a["id"] ?>">+ Nota</a>
              <a class="btn btn-sm btn-outline-warning" href="alumno_editar.php?id=<?= (int)$a["id"] ?>">Editar</a>
              <a class="btn btn-sm btn-outline-danger" href="alumno_eliminar.php?id=<?= (int)$a["id"] ?>">Eliminar</a>
              <a class="btn btn-sm btn-outline-primary" href="reportes/reporte_excel.php?alumno_id=<?= (int)$a["id"] ?>">CSV</a>
              <a class="btn btn-sm btn-outline-danger" href="reportes/reporte_pdf.php?alumno_id=<?= (int)$a["id"] ?>">PDF</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . "/partials/footer.php"; ?>

