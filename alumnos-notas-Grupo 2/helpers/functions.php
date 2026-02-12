<?php
declare(strict_types=1);

function e(string $text): string {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}


function h(string $text): string {
  return e($text);
}

function resultadoCualitativo(float $promedio): string {
  if ($promedio < 5.0) return "Suspenso";
  if ($promedio < 7.0) return "Bien";
  if ($promedio < 9.0) return "Notable";
  return "Sobresaliente";
}


function cualitativo(float $promedio): string {
  return resultadoCualitativo($promedio);
}

function pdf_to_latin1(string $s): string {
  if (function_exists('iconv')) {
    $conv = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $s);
    if ($conv !== false) return $conv;
  }
  
  return preg_replace('/[^\x20-\x7E]/', '?', $s) ?? $s;
}

function pdf_escape(string $s): string {
  $s = pdf_to_latin1($s);
  $s = str_replace('\\', '\\\\', $s);
  $s = str_replace('(', '\\(', $s);
  $s = str_replace(')', '\\)', $s);
  return $s;
}


function pdf_simple(string $title, array $lines): string {
  $y = 800;
  $leading = 14;

  $content = "BT\n/F1 14 Tf\n72 {$y} Td\n(" . pdf_escape($title) . ") Tj\n";
  $y -= 24;
  $content .= "/F1 11 Tf\n72 {$y} Td\n";

  $first = true;
  foreach ($lines as $line) {
    if ($first) {
      $content .= "(" . pdf_escape((string)$line) . ") Tj\n";
      $first = false;
    } else {
      $content .= "0 -{$leading} Td\n(" . pdf_escape((string)$line) . ") Tj\n";
    }
  }
  $content .= "ET\n";

  $objects = [];

  $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
  
  $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
  
  $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> >>";
  
  $len = strlen($content);
  $objects[] = "<< /Length {$len} >>\nstream\n{$content}endstream";
  
  $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";

  $pdf = "%PDF-1.3\n";
  $offsets = [0];

  $i = 1;
  foreach ($objects as $obj) {
    $offsets[$i] = strlen($pdf);
    $pdf .= "{$i} 0 obj\n{$obj}\nendobj\n";
    $i++;
  }

  $xrefStart = strlen($pdf);
  $count = count($offsets);

  $pdf .= "xref\n";
  $pdf .= "0 {$count}\n";
  $pdf .= "0000000000 65535 f \n";

  for ($j = 1; $j < $count; $j++) {
    $pdf .= sprintf("%010d 00000 n \n", $offsets[$j]);
  }

  $pdf .= "trailer\n";
  $pdf .= "<< /Size {$count} /Root 1 0 R >>\n";
  $pdf .= "startxref\n";
  $pdf .= "{$xrefStart}\n";
  $pdf .= "%%EOF\n";

  return $pdf;
}