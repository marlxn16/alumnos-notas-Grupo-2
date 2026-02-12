<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Notas");

// Encabezados
$sheet->setCellValue("A1", "Alumno");
$sheet->setCellValue("B1", "Correo");
$sheet->setCellValue("C1", "Promedio");
$sheet->setCellValue("D1", "Resultado");

// Datos
$row = 2;
foreach ($alumnos as $a) {
  $prom = (float)$a["promedio"];
  $sheet->setCellValue("A{$row}", $a["apellido"] . " " . $a["nombre"]);
  $sheet->setCellValue("B{$row}", $a["correo"]);
  $sheet->setCellValue("C{$row}", round($prom, 2));
  $sheet->setCellValue("D{$row}", cualitativo($prom));
  $row++;
}

$lastRow = $row - 1;

// Congelar encabezado y poner filtro
$sheet->freezePane("A2");
$sheet->setAutoFilter("A1:D1");

// Estilo del encabezado
$headerStyle = [
  'font' => [
    'bold' => true,
    'color' => ['rgb' => 'FFFFFF'],
  ],
  'fill' => [
    'fillType' => Fill::FILL_SOLID,
    'startColor' => ['rgb' => '4F81BD'],
  ],
  'alignment' => [
    'horizontal' => Alignment::HORIZONTAL_CENTER,
    'vertical' => Alignment::VERTICAL_CENTER,
  ],
];
$sheet->getStyle("A1:D1")->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(20);

// Formato numérico 2 decimales en promedio
if ($lastRow >= 2) {
  $sheet->getStyle("C2:C{$lastRow}")
        ->getNumberFormat()
        ->setFormatCode('0.00');
}

// Bordes tipo tabla
if ($lastRow >= 1) {
  $tableStyle = [
    'borders' => [
      'allBorders' => [
        'borderStyle' => Border::BORDER_THIN,
        'color' => ['rgb' => '999999'],
      ],
    ],
    'alignment' => [
      'vertical' => Alignment::VERTICAL_CENTER,
    ],
  ];
  $sheet->getStyle("A1:D{$lastRow}")->applyFromArray($tableStyle);
}

// Ajuste de columnas
foreach (range("A","D") as $col) {
  $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Nombre con fecha
$filename = "reporte_notas_" . date("Y-m-d") . ".xlsx";

// Por si algo imprimió espacios antes (evita Excel corrupto)
if (ob_get_length()) {
  ob_end_clean();
}

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
