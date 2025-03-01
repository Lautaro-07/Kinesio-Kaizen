-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-03-2025 a las 19:35:40
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `turnos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `id` int(11) NOT NULL,
  `sede` varchar(50) DEFAULT NULL,
  `servicio` varchar(50) DEFAULT NULL,
  `profesional` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `gmail` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `obra_social` varchar(100) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `numero_sesion` int(11) DEFAULT NULL,
  `asistio` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`id`, `sede`, `servicio`, `profesional`, `fecha`, `hora`, `nombre`, `gmail`, `telefono`, `obra_social`, `comentarios`, `diagnostico`, `numero_sesion`, `asistio`) VALUES
(34, 'Kaizen Rodríguez', 'Rehabilitación', 'Gaston Olgiati', '2025-01-23', '10:00:00', 'Carlos ', 'oligiatielizondo@gmail.com', '02914125043', 'PAMI', 'Asistio a su 5ta sesion / \r\nNo fue a su sexta', NULL, NULL, 0),
(40, 'Kaizen Rodríguez', 'Rehabilitación', 'Gaston Olgiati', '2025-01-30', '13:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '2914125043', 'IOMA', 'El paciente termino con sus sesiones\r\n', NULL, NULL, 1),
(41, 'Kaizen Darregueira', 'Rehabilitación', 'Gaston Olgiati', '2025-01-30', '18:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IoMa', 'el paciente tuvo su novena ', NULL, NULL, 0),
(42, 'Kaizen Rodríguez', 'Método Busquet', 'Marcos Luis', '2025-01-09', '16:00:00', 'Carlos ', 'shopii.versee@gmail.com', '02914125043', 'PAMI', '', NULL, NULL, 0),
(44, 'Kaizen Rodríguez', 'Rehabilitación', 'Franco Schroh', '2025-02-05', '10:30:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '2914125043', 'IOMA', NULL, NULL, NULL, 0),
(46, 'Kaizen Rodríguez', 'Rehabilitación', 'Franco Schroh', '2025-02-17', '10:30:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '2914125043', 'IOMA', NULL, NULL, NULL, 0),
(48, 'Kaizen Rodríguez', 'Rehabilitación', 'Gaston Olgiati', '2025-02-11', '16:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '2914125043', 'IOMA', NULL, NULL, NULL, 0),
(49, 'Kaizen Rodríguez', 'Rehabilitación', 'Franco Schroh', '2025-02-05', '08:30:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '2914630504', 'IOMA', NULL, NULL, 1, 0),
(51, 'Kaizen Rodríguez', 'Kinefilaxia', 'Leonel Scolari', '2025-02-11', '20:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '2914125043', 'IOMA', NULL, '\r\n', 1, 0),
(52, 'Kaizen Rodríguez', 'Rehabilitación', 'Micaela Pérez', '2025-02-17', '11:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(53, 'Kaizen Rodríguez', 'Rehabilitación', 'Gaston Coto', '2025-02-20', '11:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 1),
(54, 'Kaizen Rodríguez', 'Evaluacion kinesica', 'Sebastián Mazzeo', '2025-02-21', '15:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'PAMI', NULL, NULL, NULL, 0),
(55, 'Kaizen Rodríguez', 'Evaluacion kinesica', 'Franco Schroh', '2025-02-17', '18:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'Pami', NULL, NULL, NULL, 0),
(56, 'Kaizen Rodríguez', 'Rehabilitación', 'Franco Schroh', '2025-02-26', '18:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(57, 'Kaizen Rodríguez', 'Rehabilitación', 'Gaston Coto', '2025-02-27', '11:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(58, 'Kaizen Rodríguez', 'Método Busquet', 'Marcos Luis', '2025-02-28', '14:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', 'El paciente no pago su ultima sesion\r\n', NULL, 1, 0),
(59, 'Kaizen Rodríguez', 'Método Busquet', 'Marcos Luis', '2025-02-28', '13:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(60, 'Kaizen Rodríguez', 'Rehabilitación', 'Gaston Coto', '2025-02-25', '11:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 1, 1),
(61, 'Kaizen Rodríguez', 'Rehabilitación', 'Gaston Coto', '2025-02-25', '11:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(62, 'Kaizen Rodríguez', 'Rehabilitación', 'Gaston Coto', '2025-02-25', '11:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(63, 'Kaizen Rodríguez', 'Rehabilitación', 'Gaston Coto', '2025-02-25', '11:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(64, 'Kaizen Rodríguez', 'Kinefilaxia', 'Leonel Scolari', '2025-02-25', '12:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(65, 'Kaizen Rodríguez', 'Disfunción ATM', 'Franco Schroh', '2025-02-28', '18:00:00', 'Fabian', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 1, 0),
(66, 'Kaizen Rodríguez', 'Disfunción ATM', 'Franco Schroh', '2025-02-28', '19:00:00', 'Fabian', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 3, 0),
(67, 'Kaizen Rodríguez', 'Kinefilaxia', 'Leonel Scolari', '2025-02-24', '08:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 2, 0),
(68, 'Kaizen Darregueira', 'Rehabilitación', 'Gaston Olgiati', '2025-02-25', '17:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 1, 0),
(69, 'Kaizen Darregueira', 'Rehabilitación', 'Gaston Olgiati', '2025-02-27', '17:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 1, 1),
(70, 'Kaizen Rodríguez', 'Kinefilaxia', 'Leonel Scolari', '2025-02-28', '09:00:00', 'Marcos Luis', 'MarcosLuis@gmail.com', '2914123543', 'PAMI', NULL, NULL, 1, 0),
(71, 'Kaizen Rodríguez', 'Rehabilitación', 'Sebastián Mazzeo', '2025-02-28', '14:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 1, 0),
(72, 'Kaizen Rodríguez', 'Rehabilitación', 'Sebastián Mazzeo', '2025-02-28', '15:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 2, 0),
(73, 'Kaizen Rodríguez', 'Rehabilitación', 'Franco Schroh', '2025-02-27', '16:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 1, 0),
(74, 'Kaizen Rodríguez', 'Rehabilitación', 'Francisco Gomez', '2025-02-25', '09:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 5, 0),
(75, 'Kaizen Rodríguez', 'Método Busquet', 'Marcos Luis', '2025-02-26', '14:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '2914125043', 'IOMA', NULL, NULL, NULL, 0),
(76, 'Kaizen Rodríguez', 'Método Busquet', 'Marcos Luis', '2025-02-26', '13:00:00', 'Lautaro Elizondo', 'oligiatielizondo@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(77, 'Kaizen Rodríguez', 'Método Busquet', 'Marcos Luis', '2025-02-26', '13:00:00', 'Lautaro Elizondo', 'oligiatielizondo@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(78, 'Kaizen Rodríguez', 'Método Busquet', 'Marcos Luis', '2025-02-26', '13:00:00', 'Lautaro Elizondo', 'oligiatielizondo@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(79, 'Kaizen Rodríguez', 'Rehabilitación', 'Micaela Pérez', '2025-02-28', '12:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, NULL, 0),
(80, 'Kaizen Rodríguez', 'Rehabilitación', 'Francisco Gomez', '2025-02-27', '08:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 5, 0),
(81, 'Kaizen Rodríguez', 'Rehabilitación', 'Francisco Gomez', '2025-02-27', '08:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02914125043', 'IOMA', NULL, NULL, 5, 0),
(82, 'Kaizen Rodríguez', 'Rehabilitación', 'Francisco Gomez', '2025-02-27', '08:00:00', 'Javier', 'shopii.versee@gmail.com', '02915323693', 'IOMA', NULL, NULL, 0, 0),
(83, 'Kaizen Rodríguez', 'Disfunción ATM', 'Franco Schroh', '2025-02-28', '17:00:00', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '02915323692', 'PAMI', NULL, NULL, 1, 0),
(84, 'Kaizen Rodríguez', 'Rehabilitación', 'Franco Schroh', '2025-02-27', '17:00:00', 'Emanuel', 'shopii.versee@gmail.com', '02915323694', 'IOMA', NULL, NULL, 2, 0),
(85, 'Kaizen Rodríguez', 'Rehabilitación', 'Franco Schroh', '2025-02-27', '17:00:00', 'Martin', 'shopii.versee@gmail.com', '02915323696', 'IOMA', NULL, NULL, 1, 0),
(86, 'Kaizen Rodríguez', 'Rehabilitación', 'Franco Schroh', '2025-02-27', '17:00:00', 'Joaquin', 'shopii.versee@gmail.com', '02915323699', 'PAMI', NULL, NULL, 1, 0),
(87, 'Kaizen Rodríguez', 'Rehabilitación', 'Franco Schroh', '2025-02-27', '17:00:00', 'Lauta Elizondo', 'shopii.versee@gmail.com', '02915323692', 'IOMA', NULL, NULL, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `pass`, `name`) VALUES
(1, 'gastonolgiati@gmail.com', 'gastonolgiati', 'Gaston Olgiati'),
(2, 'francoschroh@gmail.com', 'francoschroh', 'Franco Schroh'),
(3, 'franciscogomez@gmail.com', 'franciscogomez', 'Francisco Gómez'),
(4, 'gastoncoto@gmail.com', 'gastoncoto', 'Gaston Coto'),
(5, 'sebastianmazzeo@gmail.com', 'sebastianmazzeo', 'Sebastian Mazzeo'),
(6, 'micaelaperez@gmail.com', 'micaelaperez', 'Micaela Pérez'),
(7, 'marcosluis@gmail.com', 'marcosluis', 'Marcos Luis'),
(8, 'leonelscolari@gmail.com', 'leonelscolari', 'Leonel Scolari');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
