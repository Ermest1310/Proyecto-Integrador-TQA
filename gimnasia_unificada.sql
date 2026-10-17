-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 22, 2026 at 09:40 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gimnasia_unificada`
--

-- --------------------------------------------------------

--
-- Table structure for table `anillas`
--

CREATE TABLE `anillas` (
  `ID_Anillas` int NOT NULL,
  `ID_Aparato` int DEFAULT NULL,
  `NombreElemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dificultad` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `aparatos`
--

CREATE TABLE `aparatos` (
  `ID_Aparato` int NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci,
  `UltimoMantenimiento` date DEFAULT NULL,
  `ProximoMantenimiento` date DEFAULT NULL,
  `EstadoMantenimiento` enum('Operativo','Mantenimiento','Fuera de Servicio') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Operativo',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `aparatos`
--

INSERT INTO `aparatos` (`ID_Aparato`, `Nombre`, `Descripcion`, `UltimoMantenimiento`, `ProximoMantenimiento`, `EstadoMantenimiento`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Suelo', NULL, NULL, NULL, 'Operativo', 1, NULL, NULL),
(2, 'Caballo con Arzones', NULL, NULL, NULL, 'Operativo', 1, NULL, NULL),
(3, 'Anillas', NULL, NULL, NULL, 'Operativo', 1, NULL, NULL),
(4, 'Salto', NULL, NULL, NULL, 'Operativo', 1, NULL, NULL),
(5, 'Barras Paralelas', NULL, NULL, NULL, 'Operativo', 1, NULL, NULL),
(6, 'Barra Fija', NULL, NULL, NULL, 'Operativo', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `area_medica`
--

CREATE TABLE `area_medica` (
  `ID_AreaMedica` int NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Especialista` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TipoConsulta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci,
  `ID_Usuario` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barras_paralelas`
--

CREATE TABLE `barras_paralelas` (
  `ID_Paralelas` int NOT NULL,
  `ID_Aparato` int DEFAULT NULL,
  `NombreElemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dificultad` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `caballo_arzones`
--

CREATE TABLE `caballo_arzones` (
  `ID_Caballo` int NOT NULL,
  `ID_Aparato` int DEFAULT NULL,
  `NombreElemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dificultad` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competencias`
--

CREATE TABLE `competencias` (
  `ID_Competencia` int NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Fecha` date NOT NULL,
  `Lugar` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Ubicacion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competencia_gimnastas`
--

CREATE TABLE `competencia_gimnastas` (
  `ID_Competencia` int NOT NULL,
  `ID_Gimnasta` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deducciones_ejecucion`
--

CREATE TABLE `deducciones_ejecucion` (
  `ID_Deduccion` int NOT NULL,
  `ID_Aparato` int DEFAULT NULL,
  `Tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Valor_Deduccion` decimal(3,2) NOT NULL,
  `Criterios` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `descuentos_juez`
--

CREATE TABLE `descuentos_juez` (
  `ID_Descuento` int NOT NULL,
  `ID_EvaluacionJuez` int NOT NULL,
  `ID_Elemento` int DEFAULT NULL,
  `nombre_elemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_error` enum('caida','paso','perdida_equilibrio','separacion_linea','falta_tecnica','otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion_error` text COLLATE utf8mb4_unicode_ci,
  `descuento_ia_propuesto` decimal(3,2) DEFAULT '0.00',
  `descuento_final` decimal(3,2) DEFAULT '0.00',
  `angulo_detectado` decimal(5,2) DEFAULT NULL,
  `coordenadas_json` text COLLATE utf8mb4_unicode_ci,
  `justificacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ejercicios`
--

CREATE TABLE `ejercicios` (
  `ID_Ejercicio` int NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Dificultad` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ID_Aparato` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `elementos_detallados`
--

CREATE TABLE `elementos_detallados` (
  `ID_Elemento` int NOT NULL,
  `ID_Grupo` int NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Nombre_Codigo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Valor_Dificultad` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Valor_Numerico` decimal(3,2) NOT NULL,
  `Descripcion_Tecnica` text COLLATE utf8mb4_unicode_ci,
  `Puntos_Clave` text COLLATE utf8mb4_unicode_ci,
  `Errores_Comunes` text COLLATE utf8mb4_unicode_ci,
  `Restricciones` text COLLATE utf8mb4_unicode_ci,
  `Imagen_Referencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Video_Referencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `elementos_rutina`
--

CREATE TABLE `elementos_rutina` (
  `ID_Rutina` int NOT NULL,
  `ID_Elemento` int NOT NULL,
  `Orden` int NOT NULL,
  `Observaciones` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entrenadores`
--

CREATE TABLE `entrenadores` (
  `ID_Entrenador` int NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Experiencia` int DEFAULT NULL,
  `Especialidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Contacto` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ID_Usuario` int DEFAULT NULL,
  `ID_Rol` int DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entrenamientos`
--

CREATE TABLE `entrenamientos` (
  `ID_Entrenamiento` int NOT NULL,
  `ID_Gimnasta` int NOT NULL,
  `ID_Aparato` int DEFAULT NULL,
  `Fecha` date NOT NULL,
  `Duracion` int DEFAULT NULL,
  `Observaciones` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluaciones`
--

CREATE TABLE `evaluaciones` (
  `ID_Evaluacion` int NOT NULL,
  `ID_Gimnasta` int DEFAULT NULL,
  `ID_Aparato` int DEFAULT NULL,
  `Fecha` date NOT NULL,
  `Puntuacion` decimal(4,2) DEFAULT NULL,
  `Comentarios` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluaciones_juez`
--

CREATE TABLE `evaluaciones_juez` (
  `ID_EvaluacionJuez` int NOT NULL,
  `ID_Gimnasta` int NOT NULL,
  `ID_Competencia` int NOT NULL,
  `ID_Aparato` int NOT NULL,
  `ID_Usuario` int NOT NULL,
  `video_ruta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nota_final` decimal(4,2) DEFAULT NULL,
  `nota_dificultad` decimal(4,2) DEFAULT NULL,
  `nota_ejecucion` decimal(4,2) DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('pendiente','en_revision','confirmado') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_evaluacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gimnastas`
--

CREATE TABLE `gimnastas` (
  `ID_Gimnasta` int NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FechaNacimiento` date NOT NULL,
  `Nacionalidad` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Peso` decimal(5,2) DEFAULT NULL,
  `Altura` decimal(5,2) DEFAULT NULL,
  `Nivel` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ID_Usuario` int DEFAULT NULL,
  `ID_Entrenador` int DEFAULT NULL,
  `ID_Rol` int DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grupos_elementos`
--

CREATE TABLE `grupos_elementos` (
  `ID_Grupo` int NOT NULL,
  `ID_Aparato` int NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci,
  `Orden` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesiones`
--

CREATE TABLE `lesiones` (
  `ID_Lesion` int NOT NULL,
  `ID_Gimnasta` int NOT NULL,
  `ID_Medico` int DEFAULT NULL,
  `Tipo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ParteCuerpo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ParteEspecifica` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Fecha` date NOT NULL,
  `FechaDiagnostico` date DEFAULT NULL,
  `FechaRecuperacion` date DEFAULT NULL,
  `Estado` enum('Lesionado','En Rehabilitación','Recuperada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Lesionado',
  `Gravedad` enum('Leve','Moderada','Grave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Leve',
  `Tratamiento` text COLLATE utf8mb4_unicode_ci,
  `TiempoRecuperacion` int DEFAULT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci,
  `Observaciones` text COLLATE utf8mb4_unicode_ci,
  `Recuperado` tinyint(1) DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postulaciones`
--

CREATE TABLE `postulaciones` (
  `ID_Postulacion` int NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Nombre` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Apellido` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FechaNacimiento` date NOT NULL,
  `Nacionalidad` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CedulaIdentidad` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Peso` decimal(5,2) NOT NULL,
  `Altura` decimal(4,2) NOT NULL,
  `IMC` decimal(4,2) NOT NULL,
  `Edad` int NOT NULL,
  `Nivel` enum('Principiante','Intermedio','Avanzado','Elite') COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `recomendaciones_sistema` text COLLATE utf8mb4_unicode_ci,
  `comentarios` text COLLATE utf8mb4_unicode_ci,
  `fecha_postulacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_revision` timestamp NULL DEFAULT NULL,
  `revisado_por` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progreso_elementos`
--

CREATE TABLE `progreso_elementos` (
  `ID_Progreso` int NOT NULL,
  `ID_Gimnasta` int NOT NULL,
  `ID_Elemento` int NOT NULL,
  `Estado` enum('No iniciado','En desarrollo','Competición') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No iniciado',
  `Nivel_Dominio` decimal(3,2) DEFAULT '0.00',
  `Comentarios` text COLLATE utf8mb4_unicode_ci,
  `Fecha_Actualizado` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requisitos_composicion`
--

CREATE TABLE `requisitos_composicion` (
  `ID_Requisito` int NOT NULL,
  `ID_Aparato` int NOT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Puntos_Otorgados` decimal(3,2) NOT NULL DEFAULT '0.50',
  `Condiciones` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `ID_Rol` int NOT NULL,
  `NombreRol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`ID_Rol`, `NombreRol`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Entrenador', 1, NULL, NULL),
(2, 'Gimnasta', 1, NULL, NULL),
(3, 'Administrador', 1, NULL, NULL),
(4, 'Médico', 1, NULL, NULL),
(5, 'Postulación', 1, NULL, NULL),
(6, 'Juez', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rutinas`
--

CREATE TABLE `rutinas` (
  `ID_Rutina` int NOT NULL,
  `ID_Gimnasta` int DEFAULT NULL,
  `ID_Aparato` int NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Fecha_Creacion` date NOT NULL,
  `Dificultad_Total` decimal(4,2) DEFAULT '0.00',
  `Descripcion` text COLLATE utf8mb4_unicode_ci,
  `Estado` enum('Propuesta','En práctica','Activa','Archivada') COLLATE utf8mb4_unicode_ci DEFAULT 'Propuesta',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salto`
--

CREATE TABLE `salto` (
  `ID_Salto` int NOT NULL,
  `ID_Aparato` int DEFAULT NULL,
  `NombreElemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dificultad` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sesiones_entrenamiento`
--

CREATE TABLE `sesiones_entrenamiento` (
  `ID_Sesion` int NOT NULL,
  `ID_Gimnasta` int NOT NULL,
  `ID_Entrenador` int NOT NULL,
  `ID_Aparato` int DEFAULT NULL,
  `Fecha` date NOT NULL,
  `Hora_Inicio` time NOT NULL,
  `Hora_Fin` time NOT NULL,
  `Duracion_Minutos` int NOT NULL,
  `Tipo_Asignacion` enum('Automatica','Manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Automatica',
  `Estado` enum('Programada','En_Curso','Completada','Cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Programada',
  `Observaciones_Entrenador` text COLLATE utf8mb4_unicode_ci,
  `Notas_Gimnasta` text COLLATE utf8mb4_unicode_ci,
  `Asistio_Gimnasta` tinyint(1) DEFAULT '0',
  `Hora_Llegada_Gimnasta` timestamp NULL DEFAULT NULL,
  `Hora_Salida_Gimnasta` timestamp NULL DEFAULT NULL,
  `Calificacion_Sesion` int DEFAULT NULL,
  `Comentarios_Finales` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `solicitudes_nivel`
--

CREATE TABLE `solicitudes_nivel` (
  `ID_Solicitud` int NOT NULL,
  `ID_Gimnasta` int NOT NULL,
  `nivel_actual` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel_solicitado` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('evaluacion','recomendacion') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'evaluacion',
  `ID_Entrenador` int DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `comentarios_gimnasta` text COLLATE utf8mb4_unicode_ci,
  `comentarios_entrenador` text COLLATE utf8mb4_unicode_ci,
  `comentarios_admin` text COLLATE utf8mb4_unicode_ci,
  `fecha_solicitud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_respuesta` timestamp NULL DEFAULT NULL,
  `aprobado_por` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suelo`
--

CREATE TABLE `suelo` (
  `ID_Suelo` int NOT NULL,
  `ID_Aparato` int DEFAULT NULL,
  `NombreElemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dificultad` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_Usuario` int NOT NULL,
  `NombreUsuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Contrasena` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ID_Rol` int NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CedulaIdentidad` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FotoPerfil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Apellido` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FechaNacimiento` date DEFAULT NULL,
  `Nacionalidad` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Peso` decimal(5,2) DEFAULT NULL,
  `Altura` decimal(5,2) DEFAULT NULL,
  `Nivel` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Contacto` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `videos_evaluacion`
--

CREATE TABLE `videos_evaluacion` (
  `ID_Video` int NOT NULL,
  `ID_EvaluacionJuez` int NOT NULL,
  `nombre_original` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruta_archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tamano_bytes` bigint DEFAULT NULL,
  `duracion_segundos` int DEFAULT NULL,
  `analizado_mediapipe` tinyint(1) DEFAULT '0',
  `resultado_analisis_json` text COLLATE utf8mb4_unicode_ci,
  `fecha_subido` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anillas`
--
ALTER TABLE `anillas`
  ADD PRIMARY KEY (`ID_Anillas`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `aparatos`
--
ALTER TABLE `aparatos`
  ADD PRIMARY KEY (`ID_Aparato`);

--
-- Indexes for table `area_medica`
--
ALTER TABLE `area_medica`
  ADD PRIMARY KEY (`ID_AreaMedica`),
  ADD KEY `ID_Usuario` (`ID_Usuario`);

--
-- Indexes for table `barras_paralelas`
--
ALTER TABLE `barras_paralelas`
  ADD PRIMARY KEY (`ID_Paralelas`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `caballo_arzones`
--
ALTER TABLE `caballo_arzones`
  ADD PRIMARY KEY (`ID_Caballo`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `competencias`
--
ALTER TABLE `competencias`
  ADD PRIMARY KEY (`ID_Competencia`);

--
-- Indexes for table `competencia_gimnastas`
--
ALTER TABLE `competencia_gimnastas`
  ADD PRIMARY KEY (`ID_Competencia`,`ID_Gimnasta`),
  ADD KEY `ID_Gimnasta` (`ID_Gimnasta`);

--
-- Indexes for table `deducciones_ejecucion`
--
ALTER TABLE `deducciones_ejecucion`
  ADD PRIMARY KEY (`ID_Deduccion`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `descuentos_juez`
--
ALTER TABLE `descuentos_juez`
  ADD PRIMARY KEY (`ID_Descuento`),
  ADD KEY `ID_EvaluacionJuez` (`ID_EvaluacionJuez`),
  ADD KEY `ID_Elemento` (`ID_Elemento`);

--
-- Indexes for table `ejercicios`
--
ALTER TABLE `ejercicios`
  ADD PRIMARY KEY (`ID_Ejercicio`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `elementos_detallados`
--
ALTER TABLE `elementos_detallados`
  ADD PRIMARY KEY (`ID_Elemento`),
  ADD KEY `ID_Grupo` (`ID_Grupo`),
  ADD KEY `idx_elementos_valor` (`Valor_Dificultad`,`Valor_Numerico`);

--
-- Indexes for table `elementos_rutina`
--
ALTER TABLE `elementos_rutina`
  ADD PRIMARY KEY (`ID_Rutina`,`ID_Elemento`,`Orden`),
  ADD KEY `ID_Elemento` (`ID_Elemento`);

--
-- Indexes for table `entrenadores`
--
ALTER TABLE `entrenadores`
  ADD PRIMARY KEY (`ID_Entrenador`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Rol` (`ID_Rol`);

--
-- Indexes for table `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD PRIMARY KEY (`ID_Entrenamiento`),
  ADD KEY `ID_Gimnasta` (`ID_Gimnasta`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD PRIMARY KEY (`ID_Evaluacion`),
  ADD KEY `ID_Gimnasta` (`ID_Gimnasta`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `evaluaciones_juez`
--
ALTER TABLE `evaluaciones_juez`
  ADD PRIMARY KEY (`ID_EvaluacionJuez`);

--
-- Indexes for table `gimnastas`
--
ALTER TABLE `gimnastas`
  ADD PRIMARY KEY (`ID_Gimnasta`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Entrenador` (`ID_Entrenador`),
  ADD KEY `ID_Rol` (`ID_Rol`);

--
-- Indexes for table `grupos_elementos`
--
ALTER TABLE `grupos_elementos`
  ADD PRIMARY KEY (`ID_Grupo`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `lesiones`
--
ALTER TABLE `lesiones`
  ADD PRIMARY KEY (`ID_Lesion`),
  ADD KEY `ID_Gimnasta` (`ID_Gimnasta`),
  ADD KEY `ID_Medico` (`ID_Medico`);

--
-- Indexes for table `postulaciones`
--
ALTER TABLE `postulaciones`
  ADD PRIMARY KEY (`ID_Postulacion`),
  ADD UNIQUE KEY `postulaciones_email_unique` (`Email`),
  ADD KEY `revisado_por` (`revisado_por`),
  ADD KEY `postulaciones_nacionalidad_cedulaidentidad_index` (`Nacionalidad`,`CedulaIdentidad`);

--
-- Indexes for table `progreso_elementos`
--
ALTER TABLE `progreso_elementos`
  ADD PRIMARY KEY (`ID_Progreso`),
  ADD UNIQUE KEY `ID_Gimnasta_ID_Elemento` (`ID_Gimnasta`,`ID_Elemento`),
  ADD KEY `ID_Elemento` (`ID_Elemento`),
  ADD KEY `idx_progreso_nivel` (`ID_Gimnasta`,`Nivel_Dominio`);

--
-- Indexes for table `requisitos_composicion`
--
ALTER TABLE `requisitos_composicion`
  ADD PRIMARY KEY (`ID_Requisito`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`ID_Rol`),
  ADD UNIQUE KEY `NombreRol` (`NombreRol`);

--
-- Indexes for table `rutinas`
--
ALTER TABLE `rutinas`
  ADD PRIMARY KEY (`ID_Rutina`),
  ADD KEY `ID_Gimnasta` (`ID_Gimnasta`),
  ADD KEY `ID_Aparato` (`ID_Aparato`),
  ADD KEY `idx_rutinas_estado` (`ID_Gimnasta`,`Estado`);

--
-- Indexes for table `salto`
--
ALTER TABLE `salto`
  ADD PRIMARY KEY (`ID_Salto`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `sesiones_entrenamiento`
--
ALTER TABLE `sesiones_entrenamiento`
  ADD PRIMARY KEY (`ID_Sesion`),
  ADD KEY `ID_Gimnasta` (`ID_Gimnasta`),
  ADD KEY `ID_Entrenador` (`ID_Entrenador`),
  ADD KEY `ID_Aparato` (`ID_Aparato`),
  ADD KEY `sesiones_fecha_hora` (`Fecha`,`Hora_Inicio`);

--
-- Indexes for table `solicitudes_nivel`
--
ALTER TABLE `solicitudes_nivel`
  ADD PRIMARY KEY (`ID_Solicitud`),
  ADD KEY `ID_Gimnasta` (`ID_Gimnasta`),
  ADD KEY `ID_Entrenador` (`ID_Entrenador`),
  ADD KEY `aprobado_por` (`aprobado_por`);

--
-- Indexes for table `suelo`
--
ALTER TABLE `suelo`
  ADD PRIMARY KEY (`ID_Suelo`),
  ADD KEY `ID_Aparato` (`ID_Aparato`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD UNIQUE KEY `NombreUsuario` (`NombreUsuario`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `ID_Rol` (`ID_Rol`);

--
-- Indexes for table `videos_evaluacion`
--
ALTER TABLE `videos_evaluacion`
  ADD PRIMARY KEY (`ID_Video`),
  ADD KEY `ID_EvaluacionJuez` (`ID_EvaluacionJuez`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anillas`
--
ALTER TABLE `anillas`
  MODIFY `ID_Anillas` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aparatos`
--
ALTER TABLE `aparatos`
  MODIFY `ID_Aparato` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `area_medica`
--
ALTER TABLE `area_medica`
  MODIFY `ID_AreaMedica` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barras_paralelas`
--
ALTER TABLE `barras_paralelas`
  MODIFY `ID_Paralelas` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `caballo_arzones`
--
ALTER TABLE `caballo_arzones`
  MODIFY `ID_Caballo` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competencias`
--
ALTER TABLE `competencias`
  MODIFY `ID_Competencia` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deducciones_ejecucion`
--
ALTER TABLE `deducciones_ejecucion`
  MODIFY `ID_Deduccion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `descuentos_juez`
--
ALTER TABLE `descuentos_juez`
  MODIFY `ID_Descuento` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ejercicios`
--
ALTER TABLE `ejercicios`
  MODIFY `ID_Ejercicio` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `elementos_detallados`
--
ALTER TABLE `elementos_detallados`
  MODIFY `ID_Elemento` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entrenadores`
--
ALTER TABLE `entrenadores`
  MODIFY `ID_Entrenador` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entrenamientos`
--
ALTER TABLE `entrenamientos`
  MODIFY `ID_Entrenamiento` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluaciones`
--
ALTER TABLE `evaluaciones`
  MODIFY `ID_Evaluacion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluaciones_juez`
--
ALTER TABLE `evaluaciones_juez`
  MODIFY `ID_EvaluacionJuez` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gimnastas`
--
ALTER TABLE `gimnastas`
  MODIFY `ID_Gimnasta` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grupos_elementos`
--
ALTER TABLE `grupos_elementos`
  MODIFY `ID_Grupo` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesiones`
--
ALTER TABLE `lesiones`
  MODIFY `ID_Lesion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `postulaciones`
--
ALTER TABLE `postulaciones`
  MODIFY `ID_Postulacion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `progreso_elementos`
--
ALTER TABLE `progreso_elementos`
  MODIFY `ID_Progreso` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisitos_composicion`
--
ALTER TABLE `requisitos_composicion`
  MODIFY `ID_Requisito` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `ID_Rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rutinas`
--
ALTER TABLE `rutinas`
  MODIFY `ID_Rutina` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salto`
--
ALTER TABLE `salto`
  MODIFY `ID_Salto` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sesiones_entrenamiento`
--
ALTER TABLE `sesiones_entrenamiento`
  MODIFY `ID_Sesion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `solicitudes_nivel`
--
ALTER TABLE `solicitudes_nivel`
  MODIFY `ID_Solicitud` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suelo`
--
ALTER TABLE `suelo`
  MODIFY `ID_Suelo` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_Usuario` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `videos_evaluacion`
--
ALTER TABLE `videos_evaluacion`
  MODIFY `ID_Video` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anillas`
--
ALTER TABLE `anillas`
  ADD CONSTRAINT `anillas_ibfk_1` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `area_medica`
--
ALTER TABLE `area_medica`
  ADD CONSTRAINT `area_medica_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE SET NULL;

--
-- Constraints for table `barras_paralelas`
--
ALTER TABLE `barras_paralelas`
  ADD CONSTRAINT `barras_paralelas_ibfk_1` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `caballo_arzones`
--
ALTER TABLE `caballo_arzones`
  ADD CONSTRAINT `caballo_arzones_ibfk_1` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `competencia_gimnastas`
--
ALTER TABLE `competencia_gimnastas`
  ADD CONSTRAINT `competencia_gimnastas_ibfk_1` FOREIGN KEY (`ID_Competencia`) REFERENCES `competencias` (`ID_Competencia`) ON DELETE CASCADE,
  ADD CONSTRAINT `competencia_gimnastas_ibfk_2` FOREIGN KEY (`ID_Gimnasta`) REFERENCES `gimnastas` (`ID_Gimnasta`) ON DELETE CASCADE;

--
-- Constraints for table `deducciones_ejecucion`
--
ALTER TABLE `deducciones_ejecucion`
  ADD CONSTRAINT `deducciones_ejecucion_ibfk_1` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE SET NULL;

--
-- Constraints for table `descuentos_juez`
--
ALTER TABLE `descuentos_juez`
  ADD CONSTRAINT `desc_juez_elemento` FOREIGN KEY (`ID_Elemento`) REFERENCES `elementos_detallados` (`ID_Elemento`) ON DELETE SET NULL,
  ADD CONSTRAINT `desc_juez_evaluacion` FOREIGN KEY (`ID_EvaluacionJuez`) REFERENCES `evaluaciones_juez` (`ID_EvaluacionJuez`) ON DELETE CASCADE;

--
-- Constraints for table `ejercicios`
--
ALTER TABLE `ejercicios`
  ADD CONSTRAINT `ejercicios_ibfk_1` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `elementos_detallados`
--
ALTER TABLE `elementos_detallados`
  ADD CONSTRAINT `elementos_detallados_ibfk_1` FOREIGN KEY (`ID_Grupo`) REFERENCES `grupos_elementos` (`ID_Grupo`) ON DELETE CASCADE;

--
-- Constraints for table `elementos_rutina`
--
ALTER TABLE `elementos_rutina`
  ADD CONSTRAINT `elementos_rutina_ibfk_1` FOREIGN KEY (`ID_Rutina`) REFERENCES `rutinas` (`ID_Rutina`) ON DELETE CASCADE,
  ADD CONSTRAINT `elementos_rutina_ibfk_2` FOREIGN KEY (`ID_Elemento`) REFERENCES `elementos_detallados` (`ID_Elemento`) ON DELETE CASCADE;

--
-- Constraints for table `entrenadores`
--
ALTER TABLE `entrenadores`
  ADD CONSTRAINT `entrenadores_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `entrenadores_ibfk_2` FOREIGN KEY (`ID_Rol`) REFERENCES `roles` (`ID_Rol`) ON DELETE SET NULL;

--
-- Constraints for table `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD CONSTRAINT `entrenamientos_ibfk_1` FOREIGN KEY (`ID_Gimnasta`) REFERENCES `gimnastas` (`ID_Gimnasta`) ON DELETE CASCADE,
  ADD CONSTRAINT `entrenamientos_ibfk_2` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD CONSTRAINT `evaluaciones_ibfk_1` FOREIGN KEY (`ID_Gimnasta`) REFERENCES `gimnastas` (`ID_Gimnasta`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluaciones_ibfk_2` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `gimnastas`
--
ALTER TABLE `gimnastas`
  ADD CONSTRAINT `gimnastas_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `gimnastas_ibfk_2` FOREIGN KEY (`ID_Entrenador`) REFERENCES `entrenadores` (`ID_Entrenador`) ON DELETE SET NULL,
  ADD CONSTRAINT `gimnastas_ibfk_3` FOREIGN KEY (`ID_Rol`) REFERENCES `roles` (`ID_Rol`) ON DELETE SET NULL;

--
-- Constraints for table `grupos_elementos`
--
ALTER TABLE `grupos_elementos`
  ADD CONSTRAINT `grupos_elementos_ibfk_1` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `lesiones`
--
ALTER TABLE `lesiones`
  ADD CONSTRAINT `lesiones_ibfk_1` FOREIGN KEY (`ID_Gimnasta`) REFERENCES `gimnastas` (`ID_Gimnasta`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesiones_ibfk_2` FOREIGN KEY (`ID_Medico`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE SET NULL;

--
-- Constraints for table `postulaciones`
--
ALTER TABLE `postulaciones`
  ADD CONSTRAINT `postulaciones_ibfk_1` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE SET NULL;

--
-- Constraints for table `progreso_elementos`
--
ALTER TABLE `progreso_elementos`
  ADD CONSTRAINT `progreso_elementos_ibfk_1` FOREIGN KEY (`ID_Gimnasta`) REFERENCES `gimnastas` (`ID_Gimnasta`) ON DELETE CASCADE,
  ADD CONSTRAINT `progreso_elementos_ibfk_2` FOREIGN KEY (`ID_Elemento`) REFERENCES `elementos_detallados` (`ID_Elemento`) ON DELETE CASCADE;

--
-- Constraints for table `requisitos_composicion`
--
ALTER TABLE `requisitos_composicion`
  ADD CONSTRAINT `requisitos_composicion_ibfk_1` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `rutinas`
--
ALTER TABLE `rutinas`
  ADD CONSTRAINT `rutinas_ibfk_1` FOREIGN KEY (`ID_Gimnasta`) REFERENCES `gimnastas` (`ID_Gimnasta`) ON DELETE SET NULL,
  ADD CONSTRAINT `rutinas_ibfk_2` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `salto`
--
ALTER TABLE `salto`
  ADD CONSTRAINT `salto_ibfk_1` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `sesiones_entrenamiento`
--
ALTER TABLE `sesiones_entrenamiento`
  ADD CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`ID_Gimnasta`) REFERENCES `gimnastas` (`ID_Gimnasta`) ON DELETE CASCADE,
  ADD CONSTRAINT `sesiones_ibfk_2` FOREIGN KEY (`ID_Entrenador`) REFERENCES `entrenadores` (`ID_Entrenador`) ON DELETE CASCADE,
  ADD CONSTRAINT `sesiones_ibfk_3` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `solicitudes_nivel`
--
ALTER TABLE `solicitudes_nivel`
  ADD CONSTRAINT `solicitudes_nivel_ibfk_1` FOREIGN KEY (`ID_Gimnasta`) REFERENCES `gimnastas` (`ID_Gimnasta`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitudes_nivel_ibfk_2` FOREIGN KEY (`ID_Entrenador`) REFERENCES `entrenadores` (`ID_Entrenador`) ON DELETE SET NULL,
  ADD CONSTRAINT `solicitudes_nivel_ibfk_3` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE SET NULL;

--
-- Constraints for table `suelo`
--
ALTER TABLE `suelo`
  ADD CONSTRAINT `suelo_ibfk_1` FOREIGN KEY (`ID_Aparato`) REFERENCES `aparatos` (`ID_Aparato`) ON DELETE CASCADE;

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`ID_Rol`) REFERENCES `roles` (`ID_Rol`) ON DELETE CASCADE;

--
-- Constraints for table `videos_evaluacion`
--
ALTER TABLE `videos_evaluacion`
  ADD CONSTRAINT `vid_juez_evaluacion` FOREIGN KEY (`ID_EvaluacionJuez`) REFERENCES `evaluaciones_juez` (`ID_EvaluacionJuez`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
