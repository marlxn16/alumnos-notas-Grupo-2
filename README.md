# Examen Practico - CRUD Alumnos y Notas

## Objetivo
- Desarrollar un CRUD web para administrar alumnos y sus notas
- Calcular promedio y resultado cualitativo por alumno
- Generar reportes en PDF y exportacion para Excel (CSV)

## Recursos usados
- PHP con PDO para acceso a datos
- MySQL (phpMyAdmin para gestion)
- Bootstrap para formularios y vistas
- Dompdf incluido en la carpeta dompdf/ (sin Composer)

## Modelo de datos
- alumno: nombre, apellido, correo
- nota: nota (0 a 10) asociada a alumno por alumno_id

## Funciones implementadas
- Alumnos
  - Crear alumno
  - Editar alumno
  - Ver detalle de alumno
  - Eliminar alumno (con sus notas asociadas)

- Notas
  - Registrar nota a un alumno (validacion 0 a 10)
  - Editar nota
  - Eliminar nota

- Listado general
  - Muestra alumnos con total de notas, promedio y resultado
  - Promedio automatico con 2 decimales
  - Resultado cualitativo:
    - menor a 5: Suspenso
    - menor a 7: Bien
    - menor a 9: Notable
    - mayor o igual a 9: Sobresaliente

## Reportes
- PDF
  - Generacion de reporte en PDF con Dompdf (HTML a PDF)
  - Archivo: reportes/reporte_pdf.php

- Excel (CSV)
  - Exportacion en CSV compatible con Excel (sin dependencias)
  - Archivo: reportes/reporte_excel.php

## Estructura del proyecto
- index.php
- config/db.php
- helpers/functions.php
- partials/header.php y partials/footer.php
- alumno_*.php
- nota_*.php
- reportes/
- assets/
- dompdf/

## Ejecucion local
- Iniciar Apache y MySQL (XAMPP)
- Importar la base de datos en MySQL
- Abrir:
  http://localhost/NOMBRE_CARPETA_PROYECTO/index.php
