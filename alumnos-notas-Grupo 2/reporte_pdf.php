<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Traer alumnos con promedio y cualitativo
$alumnos = $pdo->query("
  SELECT 
    a.id,
    a.nombre,
    a.apellido,
    a.correo,
    COALESCE(AVG(n.nota), 0) AS promedio
  FROM alumno a
  LEFT JOIN nota n ON n.alumno_id = a.id
  GROUP BY a.id, a.nombre, a.apellido, a.correo
  ORDER BY a.apellido, a.nombre
")->fetchAll();

function cualitativo($p) {
  if ($p < 5) return "Suspenso";
  if ($p < 7) return "Bien";
  if ($p < 9) return "Notable";
  return "Sobresaliente";
}

// HTML del reporte
$html = '
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
  h2 { margin: 0 0 10px; }
  .small { color: #555; margin-bottom: 12px; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #333; padding: 6px; }
  th { background: #eee; }
</style>
</head>
<body>
  <h2>Reporte de Notas</h2>
  <div class="small">Generado: '.date("Y-m-d H:i").'</div>

  <table>
    <thead>
      <tr>
        <th>Alumno</th>
        <th>Correo</th>
        <th>Promedio</th>
        <th>Resultado</th>
      </tr>
    </thead>
    <tbody>';

foreach ($alumnos as $a) {
  $prom = (float)$a["promedio"];
  $html .= "
    <tr>
      <td>".h($a["apellido"]." ".$a["nombre"])."</td>
      <td>".h($a["correo"])."</td>
      <td>".number_format($prom, 2)."</td>
      <td>".cualitativo($prom)."</td>
    </tr>";
}

$html .= '
    </tbody>
  </table>
</body>
</html>';

$options = new Options();
$options->set("isRemoteEnabled", true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="reporte_notas.pdf"');

echo $dompdf->output();
