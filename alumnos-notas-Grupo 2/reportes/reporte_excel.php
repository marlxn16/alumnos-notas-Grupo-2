<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/functions.php';

$alumno_id = isset($_GET['alumno_id']) ? (int)$_GET['alumno_id'] : 0;

$sql = "SELECT a.id, a.nombre, a.apellido, a.correo, n.nota AS valor
        FROM alumno a
        LEFT JOIN nota n ON n.alumno_id = a.id";
$params = [];
if ($alumno_id > 0) {
  $sql .= " WHERE a.id = ?";
  $params[] = $alumno_id;
}
$sql .= " ORDER BY a.apellido, a.nombre, a.id";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Agrupar por alumno
$alumnos = [];
while ($row = $stmt->fetch()) {
  $id = (int)$row['id'];
  if (!isset($alumnos[$id])) {
    $alumnos[$id] = [
      'id' => $id,
      'nombre' => (string)$row['nombre'],
      'apellido' => (string)$row['apellido'],
      'correo' => (string)$row['correo'],
      'notas' => []
    ];
  }
  if ($row['valor'] !== null) {
    $alumnos[$id]['notas'][] = (float)$row['valor'];
  }
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="reporte_alumnos_notas.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');

fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($out, ["ID","Alumno","Correo","Notas","Promedio","Resultado"], ";");

foreach ($alumnos as $a) {
  $notas = $a["notas"];
  if (count($notas) > 0) {
    $suma = 0.0; foreach ($notas as $v) $suma += $v;
    $prom = $suma / count($notas);
    $resu = cualitativo($prom);
    $notas_str = implode(" | ", array_map(fn($x)=>number_format($x,2,".",""), $notas));
    $prom_str = number_format($prom, 2, ".", "");
  } else {
    $notas_str = "Sin notas";
    $prom_str = "";
    $resu = "";
  }

  fputcsv($out, [
    $a["id"],
    $a["apellido"].", ".$a["nombre"],
    $a["correo"],
    $notas_str,
    $prom_str,
    $resu
  ], ";");
}

fclose($out);
exit;
