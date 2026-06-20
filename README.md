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


No integra con SUNAT ni emite documentos tributarios. Los "certificados" son documentos internos del instituto, no comprobantes de pago.
No requiere firma digital ni certificado X.509.
El código de verificación del certificado es un identificador interno simple, no un mecanismo criptográfico robusto.
Los datos de prueba para la medición pre-test son simulados, documentados como tal en el informe académico.


Requerimientos funcionales

IDRequerimientoRF-01Registro de participantes (nombre, DNI, curso, fecha)RF-02Catálogo de cursos (código, nombre, fechas, docente)RF-03Asociar participante a curso con estado de finalización (aprobado/desaprobado)RF-04Generar certificado en PDF con código único de verificaciónRF-05Validación de datos obligatorios antes de generar el certificadoRF-06Estado del certificado: pendiente / emitido / anuladoRF-07Anular y reemitir certificado con motivo registradoRF-08Búsqueda por participante, curso o código de verificaciónRF-09Envío automático del PDF al correo del participanteRF-10Dashboard con % error, % pendientes y total de certificados emitidos

Stack tecnológico

ComponenteTecnologíaBackend + Panel adminLaravel 12 + Filament 3Base de datosMySQL (vía XAMPP en desarrollo local)Generación de PDFLibrería integrada en el servicio de certificados

Convención de nombres

Todo el código propio (tablas, columnas, clases, variables, métodos) está nombrado en español. Los nombres de métodos y parámetros propios del framework (hasMany, belongsTo, save, $search, $record, $get, $set, etc.) se mantienen en inglés tal cual los define Laravel/Filament, ya que son parte de su funcionamiento interno y no se traducen.

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


 Fase 1  Setup del proyecto y migraciones
 Fase 2  Filament Resources (Cursos, Participantes, Certificados)
 Fase 3  Generación de PDF, código de verificación, estados y reemisión
 Fase 4  Envío de correo, búsqueda y dashboard de indicadores
 Fase 5  Datos de prueba (seeders) para medición pre-test
