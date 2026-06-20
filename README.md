Sistema de Gestión de Emisión de Certificados Instituto Hábitat


Contexto

El Instituto Peruano de Estudios del Hábitat (institución privada, Miraflores, Lima, dedicada a investigación, educación continua, asesoría y consultoría en vivienda y desarrollo urbano) emite certificados a los participantes de sus cursos de especialización de forma manual. Este proceso genera:


Errores en los datos del certificado (nombre, curso, fechas)
Certificados duplicados
Falta de control sobre certificados pendientes de emisión
Demoras en la entrega tras finalizar el curso


Este sistema busca automatizar y estandarizar ese proceso, midiendo el impacto mediante dos indicadores de calidad, en el marco de un diseño de investigación Pre-Experimental (medición pre-test/post-test).

Indicadores de calidad medidos

IndicadorQué mide% de certificados con errorCertificados anulados / total de certificados emitidos% de certificados pendientes por emitirCertificados en estado "pendiente" / total de certificados

Evaluado bajo el modelo ISO/IEC 25010, priorizando adecuación funcional, seguridad, eficiencia de desempeño y fiabilidad.

Alcance y limitaciones 

Este es un prototipo académico, no un sistema en producción. Específicamente:





Requerimientos funcionales

IDRequerimiento
RF-01Registro de participantes (nombre, DNI, curso, fecha)
RF-02Catálogo de cursos (código, nombre, fechas, docente)
RF-03Asociar participante a curso con estado de finalización (aprobado/desaprobado)
RF-04Generar certificado en PDF con código único de verificación
RF-05Validación de datos obligatorios antes de generar el certificado
RF-06Estado del certificado: pendiente / emitido / anulado
RF-07Anular y reemitir certificado con motivo registrado
RF-08Búsqueda por participante, curso o código de verificación
RF-09Envío automático del PDF al correo del participante
RF-10Dashboard con % error, % pendientes y total de certificados emitidos

Stack tecnológico

ComponenteTecnologíaBackend + Panel adminLaravel 12 + Filament 3Base de datosMySQL (vía XAMPP en desarrollo local)Generación de PDFLibrería integrada en el servicio de certificados


Instalación local

Requiere PHP 8.2+, Composer, y MySQL corriendo (vía XAMPP u otro).

bash# Clonar el repositorio
git clone https://github.com/23Alexisr/instituto_habitat.git
cd instituto_habitat

# Instalar dependencias
composer install

# Configurar entorno
cp .env.example .env
php artisan key:generate
# Editar .env con los datos de tu base de datos MySQL (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# Crear la base de datos en phpMyAdmin con el nombre indicado en DB_DATABASE,
# luego correr las migraciones
php artisan migrate

# Crear un usuario administrador para el panel
php artisan make:filament-user

# Levantar el servidor
php artisan serve

Accede al panel en http://127.0.0.1:8000/admin.

Módulos del sistema


Cursos: catálogo de cursos de especialización del instituto.
Participantes: registro de personas inscritas en los cursos.
Inscripciones: relación entre participante y curso, con estado de finalización (aprobado/desaprobado). Un certificado solo puede generarse si el participante tiene una inscripción en estado aprobado.
Certificados: emisión, anulación y reemisión de certificados, con código de verificación y exportación a PDF.
Dashboard: panel con los indicadores de calidad del proceso.


Estado actual del proyecto
 PRODUCCIÓN 


