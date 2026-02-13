README

Examen Pratico 

Objetivo
- Desarrollar un CRUD web para administrar alumnos y sus notas, calculando promedio y resultado , y generando reportes en PDF y exportacion para Excel. :contentReference[oaicite:0]{index=0}

Recursos usados
- PHP con PDO para acceso a datos. :contentReference[oaicite:1]{index=1}
- MySQL phpMyAdmin para gestion 
- Bootstrap para formularios y vistas de forma local. :contentReference[oaicite:2]{index=2}
- Dompdf carpeta incluida en el proyecto

Modelo de datos
- Tabla alumno: datos del estudiante (nombre, apellido, correo). :contentReference[oaicite:3]{index=3}
- Tabla nota: calificaciones asociadas a un alumno mediante alumno_id (0 a 10). :contentReference[oaicite:4]{index=4}

Funciones implementadas
- CRUD de alumnos:
  - Crear, editar, eliminar y ver detalle. :contentReference[oaicite:5]{index=5} :contentReference[oaicite:6]{index=6}
- CRUD de notas:
  - Registrar, editar y eliminar notas por alumno, validando rango 0 a 10. :contentReference[oaicite:7]{index=7} :contentReference[oaicite:8]{index=8}
- Listado general:
  - Visualizacion de alumnos con total de notas, promedio y resultado. :contentReference[oaicite:9]{index=9}
  - Promedio automatico con 2 decimales. :contentReference[oaicite:10]{index=10}
  - Resultado cualitativo por rangos:
    - Menor a 5: Suspenso
    - Menor a 7: Bien
    - Menor a 9: Notable
    - Mayor o igual a 9: Sobresaliente :contentReference[oaicite:11]{index=11}
- Eliminacion en cascada logica:
  - Al eliminar un alumno, se eliminan sus notas asociadas. :contentReference[oaicite:12]{index=12}

Reportes
- PDF:
  - Generacion de reporte en PDF con Dompdf (HTML a PDF).
- Excel:
  - Exportacion en formato CSV para compatibilidad directa con Excel  :contentReference[oaicite:13]{index=13}

Estructura del proyecto
- index.php: listado principal y acciones.
- config/db.php: conexion a base de datos.
- helpers/functions.php: utilidades
- alumno_*.php: operaciones de alumnos.
- nota_*.php: operaciones de notas.
- reportes/: generacion de PDF y exportacion CSV.
- assets/: estilos.
- partials/: header y footer.
- dompdf/: libreria para PDF.
