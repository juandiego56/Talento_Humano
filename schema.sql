-- ============================================================
-- Sistema de Gestión de Talento Humano (SGTH)
-- Base de datos: sgth_talento_humano
-- Módulos: Administración de Personal · Nómina · Bienestar Laboral
-- Motor: MySQL / MariaDB (XAMPP)
-- ============================================================

CREATE DATABASE IF NOT EXISTS sgth_talento_humano
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sgth_talento_humano;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Catálogos organizacionales
-- ------------------------------------------------------------
DROP TABLE IF EXISTS areas;
CREATE TABLE areas (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(120) NOT NULL,
  descripcion   VARCHAR(255) NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS cargos;
CREATE TABLE cargos (
  id                      INT AUTO_INCREMENT PRIMARY KEY,
  nombre                  VARCHAR(120) NOT NULL,
  area_id                 INT NULL,
  salario_base_sugerido   DECIMAL(12,2) NULL,
  created_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- MÓDULO 1: Administración de Personal
-- ------------------------------------------------------------
DROP TABLE IF EXISTS empleados;
CREATE TABLE empleados (
  id                          INT AUTO_INCREMENT PRIMARY KEY,
  tipo_documento              ENUM('CC','CE','TI','PA') NOT NULL DEFAULT 'CC',
  numero_documento            VARCHAR(30) NOT NULL UNIQUE,
  nombres                     VARCHAR(100) NOT NULL,
  apellidos                   VARCHAR(100) NOT NULL,
  fecha_nacimiento            DATE NULL,
  genero                      ENUM('M','F','Otro') NULL,
  estado_civil                VARCHAR(30) NULL,
  direccion                   VARCHAR(200) NULL,
  telefono                    VARCHAR(30) NULL,
  email                       VARCHAR(120) NULL,
  cargo_id                    INT NULL,
  area_id                     INT NULL,
  fecha_ingreso               DATE NOT NULL,
  tipo_contrato               ENUM('termino_fijo','termino_indefinido','obra_labor','prestacion_servicios') NOT NULL DEFAULT 'termino_fijo',
  salario_base                DECIMAL(12,2) NOT NULL DEFAULT 0,
  eps                         VARCHAR(100) NULL,
  fondo_pension               VARCHAR(100) NULL,
  arl                         VARCHAR(100) NULL,
  tipo_sangre                 VARCHAR(5) NULL,
  contacto_emergencia_nombre  VARCHAR(120) NULL,
  contacto_emergencia_telefono VARCHAR(30) NULL,
  estado                      ENUM('activo','inactivo','retirado') NOT NULL DEFAULT 'activo',
  fecha_retiro                DATE NULL,
  foto_path                   VARCHAR(255) NULL,
  created_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (cargo_id) REFERENCES cargos(id) ON DELETE SET NULL,
  FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Hoja de vida: formación académica
DROP TABLE IF EXISTS empleado_educacion;
CREATE TABLE empleado_educacion (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  empleado_id       INT NOT NULL,
  nivel_educativo   ENUM('bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado') NOT NULL,
  institucion       VARCHAR(150) NOT NULL,
  titulo_obtenido   VARCHAR(150) NULL,
  anio_graduacion   YEAR NULL,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Hoja de vida: experiencia laboral
DROP TABLE IF EXISTS empleado_experiencia;
CREATE TABLE empleado_experiencia (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  empleado_id   INT NOT NULL,
  empresa       VARCHAR(150) NOT NULL,
  cargo         VARCHAR(120) NOT NULL,
  fecha_inicio  DATE NULL,
  fecha_fin     DATE NULL,
  funciones     TEXT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Catálogo maestro de documentos para la lista de chequeo
DROP TABLE IF EXISTS documentos_requeridos;
CREATE TABLE documentos_requeridos (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(150) NOT NULL,
  codigo        VARCHAR(30) NULL,
  descripcion   VARCHAR(255) NULL,
  obligatorio   TINYINT(1) NOT NULL DEFAULT 1,
  activo        TINYINT(1) NOT NULL DEFAULT 1,
  orden         INT NOT NULL DEFAULT 0,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Lista de chequeo por empleado
DROP TABLE IF EXISTS empleado_documentos;
CREATE TABLE empleado_documentos (
  id                        INT AUTO_INCREMENT PRIMARY KEY,
  empleado_id               INT NOT NULL,
  documento_id              INT NOT NULL,
  entregado                 TINYINT(1) NOT NULL DEFAULT 0,
  fecha_entrega             DATE NULL,
  observaciones             VARCHAR(255) NULL,
  archivo_path              VARCHAR(255) NULL,
  archivo_nombre_original   VARCHAR(255) NULL,
  archivo_fecha_subida      TIMESTAMP NULL,
  UNIQUE KEY uq_empleado_documento (empleado_id, documento_id),
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
  FOREIGN KEY (documento_id) REFERENCES documentos_requeridos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE empleado_checklist_firma (
  empleado_id        INT PRIMARY KEY,
  reviso_en_archivo  VARCHAR(150) NULL,
  firma_reviso       VARCHAR(150) NULL,
  fecha_revision     DATE NULL,
  updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- MÓDULO 2: Nómina
-- ------------------------------------------------------------
DROP TABLE IF EXISTS conceptos_nomina;
CREATE TABLE conceptos_nomina (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(120) NOT NULL,
  tipo          ENUM('devengado','deduccion') NOT NULL,
  es_porcentaje TINYINT(1) NOT NULL DEFAULT 0,
  porcentaje    DECIMAL(5,2) NULL,
  valor_fijo    DECIMAL(12,2) NULL,
  automatico    TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Se aplica automáticamente al generar nómina',
  activo        TINYINT(1) NOT NULL DEFAULT 1,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS nominas;
CREATE TABLE nominas (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  periodo             VARCHAR(7) NOT NULL UNIQUE COMMENT 'Formato YYYY-MM',
  fecha_generacion    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_pago          DATE NULL,
  estado              ENUM('borrador','pagada','anulada') NOT NULL DEFAULT 'borrador',
  generado_por        INT NULL,
  total_devengado     DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_deducciones   DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_neto          DECIMAL(14,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

DROP TABLE IF EXISTS nomina_detalle;
CREATE TABLE nomina_detalle (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  nomina_id           INT NOT NULL,
  empleado_id         INT NOT NULL,
  dias_trabajados     INT NOT NULL DEFAULT 30,
  salario_base        DECIMAL(12,2) NOT NULL DEFAULT 0,
  total_devengado     DECIMAL(12,2) NOT NULL DEFAULT 0,
  total_deducciones   DECIMAL(12,2) NOT NULL DEFAULT 0,
  neto_pagar          DECIMAL(12,2) NOT NULL DEFAULT 0,
  UNIQUE KEY uq_nomina_empleado (nomina_id, empleado_id),
  FOREIGN KEY (nomina_id) REFERENCES nominas(id) ON DELETE CASCADE,
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS nomina_detalle_conceptos;
CREATE TABLE nomina_detalle_conceptos (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  nomina_detalle_id   INT NOT NULL,
  concepto_id         INT NULL,
  nombre_concepto     VARCHAR(120) NOT NULL,
  tipo                ENUM('devengado','deduccion') NOT NULL,
  valor               DECIMAL(12,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (nomina_detalle_id) REFERENCES nomina_detalle(id) ON DELETE CASCADE,
  FOREIGN KEY (concepto_id) REFERENCES conceptos_nomina(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- MÓDULO 3: Bienestar Laboral
-- ------------------------------------------------------------
DROP TABLE IF EXISTS actividades_bienestar;
CREATE TABLE actividades_bienestar (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(150) NOT NULL,
  descripcion   TEXT NULL,
  tipo          ENUM('capacitacion','recreacion','salud','integracion','deportivo') NOT NULL DEFAULT 'integracion',
  fecha_inicio  DATE NOT NULL,
  fecha_fin     DATE NULL,
  hora_inicio   TIME NULL,
  lugar         VARCHAR(150) NULL,
  responsable   VARCHAR(120) NULL,
  cupo_maximo   INT NULL,
  estado        ENUM('programada','en_curso','finalizada','cancelada') NOT NULL DEFAULT 'programada',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS bienestar_inscripciones;
CREATE TABLE bienestar_inscripciones (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  actividad_id        INT NOT NULL,
  empleado_id         INT NOT NULL,
  fecha_inscripcion   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  asistio             TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY uq_actividad_empleado (actividad_id, empleado_id),
  FOREIGN KEY (actividad_id) REFERENCES actividades_bienestar(id) ON DELETE CASCADE,
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Usuarios del sistema
-- ------------------------------------------------------------
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  nombre          VARCHAR(120) NOT NULL,
  email           VARCHAR(150) NOT NULL UNIQUE,
  password_hash   VARCHAR(255) NOT NULL,
  rol_id          TINYINT NOT NULL COMMENT '1=Admin, 2=Gestor TH, 3=Empleado',
  empleado_id     INT NULL,
  activo          TINYINT(1) NOT NULL DEFAULT 1,
  foto_path       VARCHAR(255) NULL,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Vista: completitud de la lista de chequeo por empleado
-- ------------------------------------------------------------
CREATE TABLE entrevistas_personal (
  id                          INT AUTO_INCREMENT PRIMARY KEY,
  empleado_id                 INT NOT NULL,
  nombre_entrevistador        VARCHAR(150) NULL,
  cargo_entrevistador         VARCHAR(150) NULL,
  area_entrevistador          VARCHAR(150) NULL,
  fecha_entrevista            DATE NULL,
  modalidad                   ENUM('presencial','virtual') NULL,
  nombre_entrevistado         VARCHAR(150) NULL,
  profesion                   VARCHAR(150) NULL,
  cargo_aspira                VARCHAR(150) NULL,
  posgrados_estado            ENUM('no_aplica','en_curso','culminado') NULL,
  posgrados_detalle           VARCHAR(255) NULL,
  doc_tipo                    ENUM('TI','CC','CE') NULL,
  doc_numero                  VARCHAR(30) NULL,
  doc_lugar_expedicion        VARCHAR(100) NULL,
  informacion_personal        TEXT NULL,
  formacion_academica         TEXT NULL,
  experiencia_laboral         TEXT NULL,
  resultado_prueba_psicotecnica TEXT NULL,
  resultado_prueba_tecnica    TEXT NULL,
  disponibilidad_tiempo       ENUM('si','no') NULL,
  acepta_condiciones          ENUM('si','no') NULL,
  conclusiones                TEXT NULL,
  decision                    ENUM('vincular','descartar') NULL,
  firma_entrevistador         VARCHAR(150) NULL,
  created_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_empleado_entrevista (empleado_id),
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE entrevistas_docente (
  id                          INT AUTO_INCREMENT PRIMARY KEY,
  empleado_id                 INT NOT NULL,
  nombre_entrevistador        VARCHAR(150) NULL,
  cargo_entrevistador         VARCHAR(150) NULL,
  programa_academico          VARCHAR(150) NULL,
  fecha_entrevista            DATE NULL,
  modalidad                   ENUM('presencial','virtual') NULL,
  nombre_entrevistado         VARCHAR(150) NULL,
  profesion                   VARCHAR(150) NULL,
  posgrados_estado            ENUM('en_curso','culminado') NULL,
  posgrados_detalle           VARCHAR(255) NULL,
  doc_tipo                    ENUM('CC','CE') NULL,
  doc_numero                  VARCHAR(30) NULL,
  doc_lugar_expedicion        VARCHAR(100) NULL,
  tipo_vinculacion_docente    ENUM('tiempo_completo','medio_tiempo','hora_catedra') NULL,
  competencias_personales     TEXT NULL,
  competencias_academicas     TEXT NULL,
  competencias_profesionales  TEXT NULL,
  concepto_general            TEXT NULL,
  disponibilidad_tiempo       ENUM('si','no') NULL,
  acepta_condiciones          ENUM('si','no') NULL,
  conclusiones                TEXT NULL,
  firma_entrevistador         VARCHAR(150) NULL,
  created_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_empleado_entrevista_docente (empleado_id),
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE OR REPLACE VIEW v_checklist_completitud AS
SELECT
  e.id AS empleado_id,
  COUNT(dr.id) AS total_documentos,
  SUM(CASE WHEN ed.entregado = 1 THEN 1 ELSE 0 END) AS entregados,
  ROUND(SUM(CASE WHEN ed.entregado = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(dr.id),0) * 100, 1) AS pct_completitud
FROM empleados e
CROSS JOIN documentos_requeridos dr
LEFT JOIN empleado_documentos ed ON ed.empleado_id = e.id AND ed.documento_id = dr.id
WHERE dr.activo = 1
GROUP BY e.id;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DATOS SEMILLA
-- ============================================================

-- Áreas
INSERT INTO areas (nombre, descripcion) VALUES
('Talento Humano', 'Gestión del personal y bienestar'),
('Administrativa y Financiera', 'Contabilidad, tesorería y compras'),
('Académica', 'Docencia y coordinación de programas'),
('Sistemas e Infraestructura', 'Soporte técnico e infraestructura tecnológica'),
('Bienestar Institucional', 'Programas de bienestar y calidad de vida laboral');

-- Cargos
INSERT INTO cargos (nombre, area_id, salario_base_sugerido) VALUES
('Auxiliar de Talento Humano', 1, 1600000),
('Coordinador de Talento Humano', 1, 3200000),
('Analista de Nómina', 1, 2200000),
('Contador', 2, 3500000),
('Auxiliar Contable', 2, 1800000),
('Docente Tiempo Completo', 3, 4200000),
('Docente Cátedra', 3, 1500000),
('Coordinador Académico', 3, 3600000),
('Ingeniero de Soporte', 4, 2500000),
('Coordinador de Bienestar', 5, 2800000);

-- Documentos requeridos (lista de chequeo maestra)
-- Catálogo oficial: FO-TH-027 "Lista de Chequeo Vinculaciones Laborales" V9
INSERT INTO documentos_requeridos (codigo, nombre, descripcion, obligatorio, activo, orden) VALUES
('FO-TH-18',             'Formato Único Hoja de Vida',                                       NULL, 1, 1, 1),
('FO-TH-019',            'Formato Resumen Hoja de Vida',                                      NULL, 1, 1, 2),
('FO-TH-013',            'Formato ficha sociodemográfica',                                    NULL, 1, 1, 3),
(NULL,                   'Copia de documento de identidad al 150%',                           NULL, 1, 1, 4),
(NULL,                   'Copia de Libreta Militar',                                          'Si aplica', 0, 1, 5),
(NULL,                   'Copia de Tarjeta Profesional',                                      'Si aplica', 0, 1, 6),
(NULL,                   'Certificado de vigencia y antecedentes disciplinarios profesionales', 'Si aplica', 0, 1, 7),
(NULL,                   'Certificados Académicos de estudios formales y educación continuada', 'Bachiller, Técnico, Tecnólogo, Pregrados, Posgrados, Cursos, Seminarios, Diplomados, etc.', 1, 1, 8),
(NULL,                   'Apostillado y Convalidación ante el MEN',                           'Estudios de pregrado y posgrado realizados fuera del país (si aplica)', 0, 1, 9),
(NULL,                   'Certificados Laborales',                                            NULL, 1, 1, 10),
(NULL,                   'Certificado de afiliación a EPS (Salud)',                           NULL, 1, 1, 11),
(NULL,                   'Certificado de afiliación a AFP (Pensión)',                         NULL, 1, 1, 12),
(NULL,                   'Certificado de Procuraduría',                                       NULL, 1, 1, 13),
(NULL,                   'Certificado de Contraloría',                                        NULL, 1, 1, 14),
(NULL,                   'Certificado Judicial (Policía Nacional)',                           NULL, 1, 1, 15),
(NULL,                   'Certificado Sistema Registro Nacional de Medidas Correctivas RNMC', 'Policía Nacional', 1, 1, 16),
(NULL,                   'Certificado de Inhabilidades por delitos sexuales contra menores',  'Ley 1918 de 2018 — Policía Nacional', 1, 1, 17),
(NULL,                   'Certificación Cuenta Bancaria',                                     NULL, 1, 1, 18),
('FO-TH-006',            'Formato de Requerimiento de Personal',                              NULL, 1, 1, 19),
('FO-TH-009 / FO-TH-031','Formato de Entrevista de Personal (Administrativo o Docente)',      NULL, 1, 1, 20),
(NULL,                   'Prueba Psicotécnica',                                               NULL, 1, 1, 21),
(NULL,                   'Examen Médico Ocupacional',                                         NULL, 1, 1, 22),
(NULL,                   'Contrato Laboral',                                                  NULL, 1, 1, 23);

-- Conceptos de nómina
INSERT INTO conceptos_nomina (nombre, tipo, es_porcentaje, porcentaje, valor_fijo, automatico) VALUES
('Salario básico', 'devengado', 0, NULL, NULL, 1),
('Auxilio de transporte', 'devengado', 0, NULL, 200000, 1),
('Horas extra', 'devengado', 0, NULL, NULL, 0),
('Bonificación', 'devengado', 0, NULL, NULL, 0),
('Comisiones', 'devengado', 0, NULL, NULL, 0),
('Salud (empleado)', 'deduccion', 1, 4.00, NULL, 1),
('Pensión (empleado)', 'deduccion', 1, 4.00, NULL, 1),
('Fondo de solidaridad pensional', 'deduccion', 1, 1.00, NULL, 0),
('Libranza / préstamo', 'deduccion', 0, NULL, NULL, 0),
('Otros descuentos', 'deduccion', 0, NULL, NULL, 0);

-- Empleados de ejemplo
INSERT INTO empleados
  (tipo_documento, numero_documento, nombres, apellidos, fecha_nacimiento, genero, estado_civil,
   direccion, telefono, email, cargo_id, area_id, fecha_ingreso, tipo_contrato, salario_base,
   eps, fondo_pension, arl, tipo_sangre, contacto_emergencia_nombre, contacto_emergencia_telefono, estado)
VALUES
('CC','1002003001','María Fernanda','Gómez Rojas','1990-04-12','F','Soltera','Cra 10 # 20-30, Popayán','3201234567','maria.gomez@empresa.co',2,1,'2022-02-01','termino_indefinido',3200000,'Nueva EPS','Colpensiones','Sura','O+','Luz Gómez','3009876543','activo'),
('CC','1002003002','Carlos Andrés','Muñoz Paz','1988-09-23','M','Casado','Calle 5 # 8-15, Popayán','3112345678','carlos.munoz@empresa.co',6,3,'2021-06-15','termino_fijo',4200000,'Sanitas','Protección','Positiva','A+','Ana Paz','3013456789','activo'),
('CC','1002003003','Laura Ximena','Ibarra Solís','1995-01-30','F','Soltera','Av. Panamericana # 3-45, Popayán','3157894561','laura.ibarra@empresa.co',3,1,'2023-03-10','termino_indefinido',2200000,'Sura EPS','Porvenir','Sura','B+','Jorge Ibarra','3167891234','activo'),
('CC','1002003004','Julián David','Zúñiga Ortega','1992-11-05','M','Unión libre','Cra 15 # 10-20, Popayán','3201112233','julian.zuniga@empresa.co',9,4,'2020-08-01','termino_indefinido',2500000,'Nueva EPS','Colpensiones','Sura','O-','Patricia Ortega','3021113344','activo'),
('CC','1002003005','Diana Marcela','Chávez León','1985-07-19','F','Casada','Calle 20 # 5-10, Popayán','3134445566','diana.chavez@empresa.co',10,5,'2019-05-20','termino_indefinido',2800000,'Coomeva','Protección','Positiva','A-','Pedro León','3045556677','activo'),
('CC','1002003006','Andrés Felipe','Rengifo Vidal','1998-02-14','M','Soltero','Cra 8 # 22-11, Popayán','3187778899','andres.rengifo@empresa.co',7,3,'2024-01-15','prestacion_servicios',1500000,'Sanitas','Colpensiones','Sura','B-','Marta Vidal','3059990011','activo');

-- Educación (ejemplos)
INSERT INTO empleado_educacion (empleado_id, nivel_educativo, institucion, titulo_obtenido, anio_graduacion) VALUES
(1,'pregrado','Universidad del Cauca','Psicología',2013),
(1,'especializacion','Fundación Universidad de Popayán','Gestión del Talento Humano',2018),
(2,'pregrado','Universidad del Cauca','Ingeniería Industrial',2011),
(2,'maestria','Universidad Nacional','Maestría en Administración',2017),
(3,'pregrado','Fundación Universidad de Popayán','Contaduría Pública',2019),
(4,'tecnologo','SENA','Tecnólogo en Sistemas',2014),
(4,'pregrado','Universidad del Cauca','Ingeniería de Sistemas',2019),
(5,'pregrado','Universidad del Cauca','Trabajo Social',2009),
(6,'pregrado','Universidad del Cauca','Licenciatura en Matemáticas',2022);

-- Experiencia laboral (ejemplos)
INSERT INTO empleado_experiencia (empleado_id, empresa, cargo, fecha_inicio, fecha_fin, funciones) VALUES
(1,'Comfacauca','Analista de Selección','2015-01-10','2022-01-15','Selección de personal, inducción y capacitación.'),
(2,'Universidad del Valle','Docente Catedrático','2012-02-01','2021-05-30','Docencia en Ingeniería Industrial y Producción.'),
(3,'Cooperativa Financiera del Cauca','Auxiliar Contable','2019-06-01','2023-02-28','Registro contable y conciliaciones bancarias.'),
(4,'SoporteTIC S.A.S.','Técnico de Soporte','2014-03-01','2020-07-30','Soporte técnico a usuarios y mantenimiento de equipos.');

-- Lista de chequeo: marcar algunos documentos como entregados
INSERT INTO empleado_documentos (empleado_id, documento_id, entregado, fecha_entrega)
SELECT e.id, d.id, 1, e.fecha_ingreso
FROM empleados e JOIN documentos_requeridos d ON d.id IN (1,2,3,8,9,10,11)
WHERE e.id IN (1,2,4,5);

INSERT INTO empleado_documentos (empleado_id, documento_id, entregado, fecha_entrega)
SELECT e.id, d.id, 1, e.fecha_ingreso
FROM empleados e JOIN documentos_requeridos d ON d.id IN (1,2,3)
WHERE e.id IN (3,6);

-- Actividades de bienestar (ejemplos)
INSERT INTO actividades_bienestar (nombre, descripcion, tipo, fecha_inicio, fecha_fin, hora_inicio, lugar, responsable, cupo_maximo, estado) VALUES
('Jornada de Salud Ocupacional', 'Exámenes visuales y de tamizaje para todos los empleados', 'salud', '2026-07-15', '2026-07-15', '08:00:00', 'Auditorio Principal', 'Diana Chávez León', 60, 'programada'),
('Torneo de Fútbol Interno', 'Campeonato deportivo entre áreas de la institución', 'deportivo', '2026-07-25', '2026-08-15', '15:00:00', 'Cancha institucional', 'Diana Chávez León', 40, 'programada'),
('Capacitación en Manejo del Estrés Laboral', 'Taller vivencial sobre bienestar emocional', 'capacitacion', '2026-06-10', '2026-06-10', '09:00:00', 'Sala de juntas', 'Diana Chávez León', 30, 'finalizada'),
('Celebración Día de la Familia', 'Actividad de integración con familias de los empleados', 'integracion', '2026-08-30', '2026-08-30', '10:00:00', 'Zona campestre El Recreo', 'Diana Chávez León', 100, 'programada');

INSERT INTO bienestar_inscripciones (actividad_id, empleado_id, asistio) VALUES
(3,1,1),(3,2,1),(3,3,0),(3,5,1),
(1,1,0),(1,2,0),(1,3,0),(1,4,0),(1,5,0),(1,6,0),
(2,2,0),(2,4,0),(2,6,0);

-- Nómina de ejemplo (periodo ya pagado) — se calcula sobre salario_base de cada empleado
INSERT INTO nominas (periodo, estado, total_devengado, total_deducciones, total_neto, fecha_pago)
VALUES ('2026-06','pagada', 16780000, 1342400, 15437600, '2026-06-30');

-- Detalle de esa nómina para los 6 empleados
INSERT INTO nomina_detalle (nomina_id, empleado_id, dias_trabajados, salario_base, total_devengado, total_deducciones, neto_pagar) VALUES
(1,1,30,3200000,3400000,256000,3144000),
(1,2,30,4200000,4400000,336000,4064000),
(1,3,30,2200000,2400000,176000,2224000),
(1,4,30,2500000,2700000,200000,2500000),
(1,5,30,2800000,3000000,224000,2776000),
(1,6,30,1500000,1500000,120000,1380000);

-- Conceptos aplicados por cada detalle (snapshot)
INSERT INTO nomina_detalle_conceptos (nomina_detalle_id, concepto_id, nombre_concepto, tipo, valor) VALUES
(1,1,'Salario básico','devengado',3200000),(1,2,'Auxilio de transporte','devengado',200000),
(1,6,'Salud (empleado)','deduccion',128000),(1,7,'Pensión (empleado)','deduccion',128000),
(2,1,'Salario básico','devengado',4200000),(2,2,'Auxilio de transporte','devengado',200000),
(2,6,'Salud (empleado)','deduccion',168000),(2,7,'Pensión (empleado)','deduccion',168000),
(3,1,'Salario básico','devengado',2200000),(3,2,'Auxilio de transporte','devengado',200000),
(3,6,'Salud (empleado)','deduccion',88000),(3,7,'Pensión (empleado)','deduccion',88000),
(4,1,'Salario básico','devengado',2500000),(4,2,'Auxilio de transporte','devengado',200000),
(4,6,'Salud (empleado)','deduccion',100000),(4,7,'Pensión (empleado)','deduccion',100000),
(5,1,'Salario básico','devengado',2800000),(5,2,'Auxilio de transporte','devengado',200000),
(5,6,'Salud (empleado)','deduccion',112000),(5,7,'Pensión (empleado)','deduccion',112000),
(6,1,'Salario básico','devengado',1500000),
(6,6,'Salud (empleado)','deduccion',60000),(6,7,'Pensión (empleado)','deduccion',60000);

-- Usuarios del sistema (contraseña para TODOS: "talento2026")
-- Hash generado con password_hash('talento2026', PASSWORD_DEFAULT)
INSERT INTO usuarios (nombre, email, password_hash, rol_id, empleado_id, activo) VALUES
('Administrador SGTH', 'admin@empresa.co', '$2b$10$yMqMIu9fIuT1Chb/KRU0t.G5zKScUNne3T9CYMPTElXeyXPl46hV.', 1, NULL, 1),
('María Fernanda Gómez', 'maria.gomez@empresa.co', '$2b$10$yMqMIu9fIuT1Chb/KRU0t.G5zKScUNne3T9CYMPTElXeyXPl46hV.', 2, 1, 1),
('Carlos Andrés Muñoz', 'carlos.munoz@empresa.co', '$2b$10$yMqMIu9fIuT1Chb/KRU0t.G5zKScUNne3T9CYMPTElXeyXPl46hV.', 3, 2, 1);
