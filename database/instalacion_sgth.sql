-- SGTH · Sistema de Gestión de Talento Humano — Instalación completa de la base de datos
-- Crea la base, todas las tablas y la vista, y carga solo los catálogos y el usuario administrador.
-- Usuario inicial: admin@empresa.co   Contraseña inicial: talento2026  (el sistema pide cambiarla al primer ingreso)
SET NAMES utf8mb4; SET FOREIGN_KEY_CHECKS=0;
CREATE DATABASE IF NOT EXISTS sgth_talento_humano CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sgth_talento_humano;
/*M!999999\- enable the sandbox mode */ 

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `actividades_bienestar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `actividades_bienestar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('capacitacion','recreacion','salud','integracion','deportivo') NOT NULL DEFAULT 'integracion',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `responsable` varchar(120) DEFAULT NULL,
  `cupo_maximo` int(11) DEFAULT NULL,
  `estado` enum('programada','en_curso','finalizada','cancelada') NOT NULL DEFAULT 'programada',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `qr_token` varchar(64) DEFAULT NULL COMMENT 'Token Ãºnico para el enlace/QR de auto-registro de asistencia',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_actividades_qr_token` (`qr_token`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `arl_niveles_riesgo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `arl_niveles_riesgo` (
  `nivel` tinyint(4) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `tasa` decimal(6,5) NOT NULL COMMENT 'Tasa como fracciÃ³n, ej. 0.00522 = 0.522%',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bienestar_inscripciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bienestar_inscripciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actividad_id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `fecha_inscripcion` timestamp NULL DEFAULT current_timestamp(),
  `asistio` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_actividad_empleado` (`actividad_id`,`empleado_id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `bienestar_inscripciones_ibfk_1` FOREIGN KEY (`actividad_id`) REFERENCES `actividades_bienestar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bienestar_inscripciones_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cargos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cargos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `area_id` int(11) DEFAULT NULL,
  `salario_base_sugerido` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `area_id` (`area_id`),
  CONSTRAINT `cargos_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `conceptos_nomina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `conceptos_nomina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `tipo` enum('devengado','deduccion') NOT NULL,
  `es_porcentaje` tinyint(1) NOT NULL DEFAULT 0,
  `porcentaje` decimal(5,2) DEFAULT NULL,
  `valor_fijo` decimal(12,2) DEFAULT NULL,
  `automatico` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Se aplica automáticamente al generar nómina',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documentos_requeridos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos_requeridos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `codigo` varchar(30) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `obligatorio` tinyint(1) NOT NULL DEFAULT 1,
  `visible_empleado` tinyint(1) NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `empleado_checklist_firma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado_checklist_firma` (
  `empleado_id` int(11) NOT NULL,
  `reviso_en_archivo` varchar(150) DEFAULT NULL,
  `firma_reviso` varchar(150) DEFAULT NULL,
  `fecha_revision` date DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`empleado_id`),
  CONSTRAINT `empleado_checklist_firma_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `empleado_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empleado_id` int(11) NOT NULL,
  `documento_id` int(11) NOT NULL,
  `entregado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_entrega` date DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `archivo_path` varchar(255) DEFAULT NULL,
  `archivo_nombre_original` varchar(255) DEFAULT NULL,
  `archivo_fecha_subida` timestamp NULL DEFAULT NULL,
  `peso_kb` int(11) DEFAULT NULL,
  `estado_verificacion` enum('pendiente','aprobado','devuelto') NOT NULL DEFAULT 'pendiente',
  `motivo_devolucion` varchar(255) DEFAULT NULL,
  `verificado_por` varchar(150) DEFAULT NULL,
  `verificado_en` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_empleado_documento` (`empleado_id`,`documento_id`),
  KEY `documento_id` (`documento_id`),
  CONSTRAINT `empleado_documentos_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `empleado_documentos_ibfk_2` FOREIGN KEY (`documento_id`) REFERENCES `documentos_requeridos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=317 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `empleado_educacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado_educacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empleado_id` int(11) NOT NULL,
  `nivel_educativo` enum('bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado') NOT NULL,
  `institucion` varchar(150) NOT NULL,
  `titulo_obtenido` varchar(150) DEFAULT NULL,
  `anio_graduacion` year(4) DEFAULT NULL,
  `fecha_expedicion` date DEFAULT NULL,
  `en_curso` tinyint(1) NOT NULL DEFAULT 0,
  `archivo_titulo_path` varchar(255) DEFAULT NULL,
  `archivo_titulo_nombre_original` varchar(255) DEFAULT NULL,
  `archivo_titulo_peso_kb` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `empleado_educacion_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `empleado_experiencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado_experiencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empleado_id` int(11) NOT NULL,
  `empresa` varchar(150) NOT NULL,
  `cargo` varchar(120) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `funciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `empleado_experiencia_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `empleado_formacion_complementaria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado_formacion_complementaria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empleado_id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `institucion` varchar(150) NOT NULL,
  `fecha` date NOT NULL,
  `horas` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `empleado_formacion_complementaria_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `empleado_hojavida_notas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado_hojavida_notas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empleado_id` int(11) NOT NULL,
  `seccion` varchar(80) NOT NULL COMMENT 'nombre de la secciÃ³n/campo marcado',
  `motivo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `empleado_hojavida_notas_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_documento` enum('CC','CE','TI','PA') NOT NULL DEFAULT 'CC',
  `numero_documento` varchar(30) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('M','F','Otro') DEFAULT NULL,
  `estado_civil` varchar(30) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `cargo_id` int(11) DEFAULT NULL,
  `area_id` int(11) DEFAULT NULL,
  `programa_id` int(11) DEFAULT NULL,
  `fecha_ingreso` date NOT NULL,
  `tipo_contrato` enum('termino_fijo','termino_indefinido','obra_labor','prestacion_servicios') NOT NULL DEFAULT 'termino_fijo',
  `salario_base` decimal(12,2) NOT NULL DEFAULT 0.00,
  `eps` varchar(100) DEFAULT NULL,
  `fondo_pension` varchar(100) DEFAULT NULL,
  `arl` varchar(100) DEFAULT NULL,
  `tipo_sangre` varchar(5) DEFAULT NULL,
  `contacto_emergencia_nombre` varchar(120) DEFAULT NULL,
  `contacto_emergencia_telefono` varchar(30) DEFAULT NULL,
  `estado` enum('activo','inactivo','retirado') NOT NULL DEFAULT 'activo',
  `fecha_retiro` date DEFAULT NULL,
  `foto_path` varchar(255) DEFAULT NULL,
  `banco` varchar(100) DEFAULT NULL,
  `tipo_cuenta` enum('ahorros','corriente') DEFAULT NULL,
  `numero_cuenta` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nivel_educativo` enum('bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado') DEFAULT NULL,
  `tipo_vinculacion_docente` enum('tiempo_completo','medio_tiempo','hora_catedra') DEFAULT NULL,
  `arl_nivel_riesgo` tinyint(4) NOT NULL DEFAULT 1,
  `hojavida_estado` enum('borrador','enviada','aprobada','devuelta') NOT NULL DEFAULT 'borrador',
  `hojavida_version` int(11) NOT NULL DEFAULT 1,
  `hojavida_fecha` date DEFAULT NULL,
  `hojavida_observaciones` text DEFAULT NULL,
  `hojavida_enviada_en` datetime DEFAULT NULL,
  `hojavida_revisada_por` varchar(150) DEFAULT NULL,
  `hojavida_revisada_en` datetime DEFAULT NULL,
  `hojavida_respaldo` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_documento` (`numero_documento`),
  KEY `cargo_id` (`cargo_id`),
  KEY `area_id` (`area_id`),
  KEY `fk_empleados_programa` (`programa_id`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`cargo_id`) REFERENCES `cargos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `empleados_ibfk_2` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_empleados_programa` FOREIGN KEY (`programa_id`) REFERENCES `programas_academicos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entrevistas_docente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `entrevistas_docente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empleado_id` int(11) NOT NULL,
  `nombre_entrevistador` varchar(150) DEFAULT NULL,
  `cargo_entrevistador` varchar(150) DEFAULT NULL,
  `programa_academico` varchar(150) DEFAULT NULL,
  `fecha_entrevista` date DEFAULT NULL,
  `modalidad` enum('presencial','virtual') DEFAULT NULL,
  `nombre_entrevistado` varchar(150) DEFAULT NULL,
  `profesion` varchar(150) DEFAULT NULL,
  `posgrados_estado` enum('en_curso','culminado') DEFAULT NULL,
  `posgrados_detalle` varchar(255) DEFAULT NULL,
  `doc_tipo` enum('CC','CE') DEFAULT NULL,
  `doc_numero` varchar(30) DEFAULT NULL,
  `doc_lugar_expedicion` varchar(100) DEFAULT NULL,
  `tipo_vinculacion_docente` enum('tiempo_completo','medio_tiempo','hora_catedra') DEFAULT NULL,
  `competencias_personales` text DEFAULT NULL,
  `competencias_academicas` text DEFAULT NULL,
  `competencias_profesionales` text DEFAULT NULL,
  `concepto_general` text DEFAULT NULL,
  `disponibilidad_tiempo` enum('si','no') DEFAULT NULL,
  `acepta_condiciones` enum('si','no') DEFAULT NULL,
  `conclusiones` text DEFAULT NULL,
  `firma_entrevistador` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_empleado_entrevista_docente` (`empleado_id`),
  CONSTRAINT `entrevistas_docente_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `entrevistas_personal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `entrevistas_personal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empleado_id` int(11) NOT NULL,
  `nombre_entrevistador` varchar(150) DEFAULT NULL,
  `cargo_entrevistador` varchar(150) DEFAULT NULL,
  `area_entrevistador` varchar(150) DEFAULT NULL,
  `fecha_entrevista` date DEFAULT NULL,
  `modalidad` enum('presencial','virtual') DEFAULT NULL,
  `nombre_entrevistado` varchar(150) DEFAULT NULL,
  `profesion` varchar(150) DEFAULT NULL,
  `cargo_aspira` varchar(150) DEFAULT NULL,
  `posgrados_estado` enum('no_aplica','en_curso','culminado') DEFAULT NULL,
  `posgrados_detalle` varchar(255) DEFAULT NULL,
  `doc_tipo` enum('TI','CC','CE') DEFAULT NULL,
  `doc_numero` varchar(30) DEFAULT NULL,
  `doc_lugar_expedicion` varchar(100) DEFAULT NULL,
  `informacion_personal` text DEFAULT NULL,
  `formacion_academica` text DEFAULT NULL,
  `experiencia_laboral` text DEFAULT NULL,
  `resultado_prueba_psicotecnica` text DEFAULT NULL,
  `resultado_prueba_tecnica` text DEFAULT NULL,
  `disponibilidad_tiempo` enum('si','no') DEFAULT NULL,
  `acepta_condiciones` enum('si','no') DEFAULT NULL,
  `conclusiones` text DEFAULT NULL,
  `decision` enum('vincular','descartar') DEFAULT NULL,
  `firma_entrevistador` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_empleado_entrevista` (`empleado_id`),
  CONSTRAINT `entrevistas_personal_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `escalafon_salarial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `escalafon_salarial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cargo_id` int(11) NOT NULL,
  `nivel_educativo` enum('bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado') NOT NULL,
  `salario_tiempo_completo` decimal(12,2) NOT NULL,
  `salario_medio_tiempo` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_escalafon_cargo_nivel` (`cargo_id`,`nivel_educativo`),
  CONSTRAINT `escalafon_salarial_ibfk_1` FOREIGN KEY (`cargo_id`) REFERENCES `cargos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nomina_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nomina_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomina_id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `dias_trabajados` int(11) NOT NULL DEFAULT 30,
  `salario_base` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_devengado` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_deducciones` decimal(12,2) NOT NULL DEFAULT 0.00,
  `neto_pagar` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nomina_empleado` (`nomina_id`,`empleado_id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `nomina_detalle_ibfk_1` FOREIGN KEY (`nomina_id`) REFERENCES `nominas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nomina_detalle_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nomina_detalle_conceptos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nomina_detalle_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomina_detalle_id` int(11) NOT NULL,
  `concepto_id` int(11) DEFAULT NULL,
  `nombre_concepto` varchar(120) NOT NULL,
  `tipo` enum('devengado','deduccion') NOT NULL,
  `valor` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `nomina_detalle_id` (`nomina_detalle_id`),
  KEY `concepto_id` (`concepto_id`),
  CONSTRAINT `nomina_detalle_conceptos_ibfk_1` FOREIGN KEY (`nomina_detalle_id`) REFERENCES `nomina_detalle` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nomina_detalle_conceptos_ibfk_2` FOREIGN KEY (`concepto_id`) REFERENCES `conceptos_nomina` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nominas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nominas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `periodo` varchar(7) NOT NULL COMMENT 'Formato YYYY-MM',
  `corte_academico` varchar(10) DEFAULT NULL COMMENT 'Etiqueta de corte acadÃ©mico que agrupa periodos mensuales, ej. 2025-2',
  `fecha_generacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_pago` date DEFAULT NULL,
  `estado` enum('borrador','pagada','anulada') NOT NULL DEFAULT 'borrador',
  `generado_por` int(11) DEFAULT NULL,
  `total_devengado` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_deducciones` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_neto` decimal(14,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `periodo` (`periodo`),
  KEY `idx_nominas_corte` (`corte_academico`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `programas_academicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `programas_academicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `codigo` varchar(30) DEFAULT NULL,
  `facultad` varchar(150) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `solicitudes_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('pendiente','atendida') NOT NULL DEFAULT 'pendiente',
  `fecha_solicitud` timestamp NULL DEFAULT current_timestamp(),
  `atendida_por` int(11) DEFAULT NULL,
  `fecha_atendida` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `atendida_por` (`atendida_por`),
  CONSTRAINT `solicitudes_password_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_password_ibfk_2` FOREIGN KEY (`atendida_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `solicitudes_vinculacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes_vinculacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `programa_id` int(11) NOT NULL,
  `solicitado_por` int(11) NOT NULL,
  `nombres_candidato` varchar(100) NOT NULL,
  `apellidos_candidato` varchar(100) NOT NULL,
  `cargo_sugerido` varchar(150) DEFAULT NULL,
  `nivel_educativo_requerido` enum('bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado') DEFAULT NULL,
  `tipo_vinculacion_docente` enum('tiempo_completo','medio_tiempo','hora_catedra') DEFAULT NULL,
  `justificacion` text NOT NULL,
  `fecha_requerida` date DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `respuesta` text DEFAULT NULL,
  `atendida_por` int(11) DEFAULT NULL,
  `fecha_solicitud` timestamp NULL DEFAULT current_timestamp(),
  `fecha_respuesta` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_solvinc_solicitante` (`solicitado_por`),
  KEY `fk_solvinc_atendida` (`atendida_por`),
  KEY `idx_solvinc_programa` (`programa_id`),
  KEY `idx_solvinc_estado` (`estado`),
  CONSTRAINT `fk_solvinc_atendida` FOREIGN KEY (`atendida_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_solvinc_programa` FOREIGN KEY (`programa_id`) REFERENCES `programas_academicos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_solvinc_solicitante` FOREIGN KEY (`solicitado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol_id` tinyint(4) NOT NULL COMMENT '1=Admin, 2=Gestor TH, 3=Empleado, 4=Director de Programa',
  `programa_id` int(11) DEFAULT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `foto_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `debe_cambiar_password` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `empleado_id` (`empleado_id`),
  KEY `fk_usuarios_programa` (`programa_id`),
  CONSTRAINT `fk_usuarios_programa` FOREIGN KEY (`programa_id`) REFERENCES `programas_academicos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `v_checklist_completitud`;
/*!50001 DROP VIEW IF EXISTS `v_checklist_completitud`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_checklist_completitud` AS SELECT
 1 AS `empleado_id`,
  1 AS `total_documentos`,
  1 AS `entregados`,
  1 AS `pct_completitud` */;
SET character_set_client = @saved_cs_client;
/*!50001 DROP VIEW IF EXISTS `v_checklist_completitud`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013  SQL SECURITY DEFINER */
/*!50001 VIEW `v_checklist_completitud` AS select `e`.`id` AS `empleado_id`,count(`dr`.`id`) AS `total_documentos`,sum(case when `ed`.`entregado` = 1 then 1 else 0 end) AS `entregados`,round(sum(case when `ed`.`entregado` = 1 then 1 else 0 end) / nullif(count(`dr`.`id`),0) * 100,1) AS `pct_completitud` from ((`empleados` `e` join `documentos_requeridos` `dr`) left join `empleado_documentos` `ed` on(`ed`.`empleado_id` = `e`.`id` and `ed`.`documento_id` = `dr`.`id`)) where `dr`.`activo` = 1 group by `e`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


-- ── Catálogos ──────────────────────────────────────────────
/*M!999999\- enable the sandbox mode */ 

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40000 ALTER TABLE `areas` DISABLE KEYS */;
INSERT INTO `areas` (`id`, `nombre`, `descripcion`, `created_at`) VALUES (1,'Talento Humano','Gestión del personal y bienestar','2026-10-07 16:34:07'),
(2,'Administrativa y Financiera','Contabilidad, tesorería y compras','2026-10-07 16:34:07'),
(3,'Académica','Docencia y coordinación de programas','2026-10-07 16:34:07'),
(4,'Sistemas e Infraestructura','Soporte técnico e infraestructura tecnológica','2026-10-07 16:34:07'),
(5,'Bienestar Institucional','Programas de bienestar y calidad de vida laboral','2026-10-07 16:34:07'),
(6,'Talento Humano',NULL,'2026-10-07 16:34:33'),
(7,'IngenierÃ­a',NULL,'2026-10-07 16:34:33');
/*!40000 ALTER TABLE `areas` ENABLE KEYS */;

/*!40000 ALTER TABLE `cargos` DISABLE KEYS */;
INSERT INTO `cargos` (`id`, `nombre`, `area_id`, `salario_base_sugerido`, `created_at`) VALUES (1,'Auxiliar de Talento Humano',1,1600000.00,'2026-10-07 16:34:07'),
(2,'Coordinador de Talento Humano',1,3200000.00,'2026-10-07 16:34:07'),
(3,'Analista de Nómina',1,2200000.00,'2026-10-07 16:34:07'),
(4,'Contador',2,3500000.00,'2026-10-07 16:34:07'),
(5,'Auxiliar Contable',2,1800000.00,'2026-10-07 16:34:07'),
(6,'Docente Tiempo Completo',3,4200000.00,'2026-10-07 16:34:07'),
(7,'Docente Cátedra',3,1500000.00,'2026-10-07 16:34:07'),
(8,'Coordinador Académico',3,3600000.00,'2026-10-07 16:34:07'),
(9,'Ingeniero de Soporte',4,2500000.00,'2026-10-07 16:34:07'),
(10,'Coordinador de Bienestar',5,2800000.00,'2026-10-07 16:34:07'),
(11,'Docente',NULL,NULL,'2026-10-07 16:34:33'),
(12,'Auxiliar administrativo',NULL,NULL,'2026-10-07 16:34:33');
/*!40000 ALTER TABLE `cargos` ENABLE KEYS */;

/*!40000 ALTER TABLE `arl_niveles_riesgo` DISABLE KEYS */;
INSERT INTO `arl_niveles_riesgo` (`nivel`, `descripcion`, `tasa`, `updated_at`) VALUES (1,'Riesgo I â€” MÃ­nimo (labores administrativas, docencia)',0.00522,'2026-10-07 16:34:07'),
(2,'Riesgo II â€” Bajo',0.01044,'2026-10-07 16:34:07'),
(3,'Riesgo III â€” Medio',0.02436,'2026-10-07 16:34:07'),
(4,'Riesgo IV â€” Alto',0.04350,'2026-10-07 16:34:07'),
(5,'Riesgo V â€” MÃ¡ximo',0.06960,'2026-10-07 16:34:07');
/*!40000 ALTER TABLE `arl_niveles_riesgo` ENABLE KEYS */;

/*!40000 ALTER TABLE `conceptos_nomina` DISABLE KEYS */;
INSERT INTO `conceptos_nomina` (`id`, `nombre`, `tipo`, `es_porcentaje`, `porcentaje`, `valor_fijo`, `automatico`, `activo`, `created_at`) VALUES (1,'Salario básico','devengado',0,NULL,NULL,1,1,'2026-10-07 16:34:07'),
(2,'Auxilio de transporte','devengado',0,NULL,200000.00,1,1,'2026-10-07 16:34:07'),
(3,'Horas extra','devengado',0,NULL,NULL,0,1,'2026-10-07 16:34:07'),
(4,'Bonificación','devengado',0,NULL,NULL,0,1,'2026-10-07 16:34:07'),
(5,'Comisiones','devengado',0,NULL,NULL,0,1,'2026-10-07 16:34:07'),
(6,'Salud (empleado)','deduccion',1,4.00,NULL,1,1,'2026-10-07 16:34:07'),
(7,'Pensión (empleado)','deduccion',1,4.00,NULL,1,1,'2026-10-07 16:34:07'),
(8,'Fondo de solidaridad pensional','deduccion',1,1.00,NULL,0,1,'2026-10-07 16:34:07'),
(9,'Libranza / préstamo','deduccion',0,NULL,NULL,0,1,'2026-10-07 16:34:07'),
(10,'Otros descuentos','deduccion',0,NULL,NULL,0,1,'2026-10-07 16:34:07');
/*!40000 ALTER TABLE `conceptos_nomina` ENABLE KEYS */;

/*!40000 ALTER TABLE `documentos_requeridos` DISABLE KEYS */;
INSERT INTO `documentos_requeridos` (`id`, `nombre`, `codigo`, `descripcion`, `obligatorio`, `visible_empleado`, `activo`, `orden`, `created_at`) VALUES (1,'Formato Único Hoja de Vida','FO-TH-18',NULL,1,1,0,1,'2026-10-07 16:34:07'),
(2,'Formato Resumen Hoja de Vida','FO-TH-019',NULL,1,1,0,2,'2026-10-07 16:34:07'),
(3,'Formato ficha sociodemográfica','FO-TH-013',NULL,1,1,0,3,'2026-10-07 16:34:07'),
(4,'Copia de documento de identidad al 150%',NULL,NULL,1,1,0,4,'2026-10-07 16:34:07'),
(5,'Copia de Libreta Militar',NULL,'Si aplica',0,1,0,5,'2026-10-07 16:34:07'),
(6,'Copia de Tarjeta Profesional',NULL,'Si aplica',0,1,0,6,'2026-10-07 16:34:07'),
(7,'Certificado de vigencia y antecedentes disciplinarios profesionales',NULL,'Si aplica',0,1,0,7,'2026-10-07 16:34:07'),
(8,'Certificados Académicos de estudios formales y educación continuada',NULL,'Bachiller, Técnico, Tecnólogo, Pregrados, Posgrados, Cursos, Seminarios, Diplomados, etc.',1,1,0,8,'2026-10-07 16:34:07'),
(9,'Apostillado y Convalidación ante el MEN',NULL,'Estudios de pregrado y posgrado realizados fuera del país (si aplica)',0,1,0,9,'2026-10-07 16:34:07'),
(10,'Certificados Laborales',NULL,NULL,1,1,0,10,'2026-10-07 16:34:07'),
(11,'Certificado de afiliación a EPS (Salud)',NULL,NULL,1,1,0,11,'2026-10-07 16:34:07'),
(12,'Certificado de afiliación a AFP (Pensión)',NULL,NULL,1,1,0,12,'2026-10-07 16:34:07'),
(13,'Certificado de Procuraduría',NULL,NULL,1,1,0,13,'2026-10-07 16:34:07'),
(14,'Certificado de Contraloría',NULL,NULL,1,1,0,14,'2026-10-07 16:34:07'),
(15,'Certificado Judicial (Policía Nacional)',NULL,NULL,1,1,0,15,'2026-10-07 16:34:07'),
(16,'Certificado Sistema Registro Nacional de Medidas Correctivas RNMC',NULL,'Policía Nacional',1,1,0,16,'2026-10-07 16:34:07'),
(17,'Certificado de Inhabilidades por delitos sexuales contra menores',NULL,'Ley 1918 de 2018 — Policía Nacional',1,1,0,17,'2026-10-07 16:34:07'),
(18,'Certificación Cuenta Bancaria',NULL,NULL,1,1,0,18,'2026-10-07 16:34:07'),
(19,'Formato de Requerimiento de Personal','FO-TH-006',NULL,1,0,0,19,'2026-10-07 16:34:07'),
(20,'Formato de Entrevista de Personal (Administrativo o Docente)','FO-TH-009 / FO-TH-031',NULL,1,0,0,20,'2026-10-07 16:34:07'),
(21,'Prueba Psicotécnica',NULL,NULL,1,0,0,21,'2026-10-07 16:34:07'),
(22,'Examen Médico Ocupacional',NULL,NULL,1,0,0,22,'2026-10-07 16:34:07'),
(23,'Contrato Laboral',NULL,NULL,1,0,0,23,'2026-10-07 16:34:07'),
(24,'Formato Ãšnico Hoja de Vida','FO-TH-18',NULL,1,1,1,1,'2026-10-07 16:34:07'),
(25,'Formato Resumen Hoja de Vida','FO-TH-019',NULL,1,1,1,2,'2026-10-07 16:34:07'),
(26,'Formato ficha sociodemogrÃ¡fica','FO-TH-013',NULL,1,1,1,3,'2026-10-07 16:34:07'),
(27,'Copia de documento de identidad al 150%',NULL,NULL,1,1,1,4,'2026-10-07 16:34:07'),
(28,'Copia de Libreta Militar',NULL,'Si aplica',0,1,1,5,'2026-10-07 16:34:07'),
(29,'Copia de Tarjeta Profesional',NULL,'Si aplica',0,1,1,6,'2026-10-07 16:34:07'),
(30,'Certificado de vigencia y antecedentes disciplinarios profesionales',NULL,'Si aplica',0,1,1,7,'2026-10-07 16:34:07'),
(31,'Certificados AcadÃ©micos de estudios formales y educaciÃ³n continuada',NULL,'Bachiller, TÃ©cnico, TecnÃ³logo, Pregrados, Posgrados, Cursos, Seminarios, Diplomados, etc.',1,1,1,8,'2026-10-07 16:34:07'),
(32,'Apostillado y ConvalidaciÃ³n ante el MEN',NULL,'Estudios de pregrado y posgrado realizados fuera del paÃ­s (si aplica)',0,1,1,9,'2026-10-07 16:34:07'),
(33,'Certificados Laborales',NULL,NULL,1,1,1,10,'2026-10-07 16:34:07'),
(34,'Certificado de afiliaciÃ³n a EPS (Salud)',NULL,NULL,1,1,1,11,'2026-10-07 16:34:07'),
(35,'Certificado de afiliaciÃ³n a AFP (PensiÃ³n)',NULL,NULL,1,1,1,12,'2026-10-07 16:34:07'),
(36,'Certificado de ProcuradurÃ­a',NULL,NULL,1,1,1,13,'2026-10-07 16:34:07'),
(37,'Certificado de ContralorÃ­a',NULL,NULL,1,1,1,14,'2026-10-07 16:34:07'),
(38,'Certificado Judicial (PolicÃ­a Nacional)',NULL,NULL,1,1,1,15,'2026-10-07 16:34:07'),
(39,'Certificado Sistema Registro Nacional de Medidas Correctivas RNMC',NULL,'PolicÃ­a Nacional',1,1,1,16,'2026-10-07 16:34:07'),
(40,'Certificado de Inhabilidades por delitos sexuales contra menores',NULL,'Ley 1918 de 2018 â€” PolicÃ­a Nacional',1,1,1,17,'2026-10-07 16:34:07'),
(41,'CertificaciÃ³n Cuenta Bancaria',NULL,NULL,1,1,1,18,'2026-10-07 16:34:07'),
(42,'Formato de Requerimiento de Personal','FO-TH-006',NULL,1,0,1,19,'2026-10-07 16:34:07'),
(43,'Formato de Entrevista de Personal (Administrativo o Docente)','FO-TH-009 / FO-TH-031',NULL,1,0,1,20,'2026-10-07 16:34:07'),
(44,'Prueba PsicotÃ©cnica',NULL,NULL,1,0,1,21,'2026-10-07 16:34:07'),
(45,'Examen MÃ©dico Ocupacional',NULL,NULL,1,0,1,22,'2026-10-07 16:34:07'),
(46,'Contrato Laboral',NULL,NULL,1,0,1,23,'2026-10-07 16:34:07');
/*!40000 ALTER TABLE `documentos_requeridos` ENABLE KEYS */;

/*!40000 ALTER TABLE `escalafon_salarial` DISABLE KEYS */;
/*!40000 ALTER TABLE `escalafon_salarial` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


-- ── Usuario administrador inicial ─────────────────────────
/*M!999999\- enable the sandbox mode */ 

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password_hash`, `rol_id`, `programa_id`, `empleado_id`, `activo`, `foto_path`, `created_at`, `debe_cambiar_password`) VALUES (1,'Administrador SGTH','admin@empresa.co','$2b$10$yMqMIu9fIuT1Chb/KRU0t.G5zKScUNne3T9CYMPTElXeyXPl46hV.',1,NULL,NULL,1,NULL,'2026-10-07 16:34:07',0);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

UPDATE usuarios SET debe_cambiar_password = 1 WHERE email = 'admin@empresa.co';
SET FOREIGN_KEY_CHECKS=1;
