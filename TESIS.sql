-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 22-01-2014 a las 20:28:03
-- Versión del servidor: 5.5.35-0ubuntu0.13.10.1
-- Versión de PHP: 5.5.3-1ubuntu2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `TESIS`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `DOCUMENTOS`
--

CREATE TABLE IF NOT EXISTS `DOCUMENTOS` (
  `ID_TESIS` int(11) NOT NULL,
  `COMPLETO` varchar(200) NOT NULL,
  `INTRO` varchar(200) NOT NULL,
  `CAP1` varchar(200) NOT NULL,
  `CAP2` varchar(200) NOT NULL,
  `CAP3` varchar(200) NOT NULL,
  `CAP4` varchar(200) NOT NULL,
  `CONCLUSION` varchar(200) NOT NULL,
  `COMPLETO_ST` int(11) NOT NULL DEFAULT '0',
  `INTRO_ST` int(11) NOT NULL DEFAULT '0',
  `CAP1_ST` int(11) NOT NULL DEFAULT '0',
  `CAP2_ST` int(11) NOT NULL DEFAULT '0',
  `CAP3_ST` int(11) NOT NULL DEFAULT '0',
  `CAP4_ST` int(11) NOT NULL DEFAULT '0',
  `CONCLUSION_ST` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ESPECIALIDAD`
--

CREATE TABLE IF NOT EXISTS `ESPECIALIDAD` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(50) NOT NULL,
  `USER_CRE` varchar(20) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `ESPECIALIDAD`
--

INSERT INTO `ESPECIALIDAD` (`ID`, `NOMBRE`, `USER_CRE`) VALUES
(3, 'TELECOMUNICACIONES', 'ADMIN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `EVENTO_AUDIT`
--

CREATE TABLE IF NOT EXISTS `EVENTO_AUDIT` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `EVNT_ACCION` varchar(45) DEFAULT NULL,
  `EVNT_USER` varchar(45) DEFAULT NULL,
  `EVNT_MODULO` varchar(45) DEFAULT NULL,
  `EVNT_ID_ELEMENT` int(11) DEFAULT NULL,
  `EVNT_DESCRI` varchar(300) DEFAULT NULL,
  `EVNT_FECHA` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA AUDITAR LAS ACCIONES DE LOS USUARIOS' AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `EVENTO_AUDIT`
--

INSERT INTO `EVENTO_AUDIT` (`ID`, `EVNT_ACCION`, `EVNT_USER`, `EVNT_MODULO`, `EVNT_ID_ELEMENT`, `EVNT_DESCRI`, `EVNT_FECHA`) VALUES
(2, 'AGREGAR', 'jardila', 'TUTORES', 15479623, 'EL Usuario Agrego un Tutor', '2014-01-15 10:54:38'),
(3, 'EDITAR', 'jardila', 'TUTORES', 15479623, 'EL Usuario Edito un Tutor', '2014-01-15 10:55:30'),
(4, 'EDITAR', 'jardila', 'ESPECIALIDADES', 3, 'EL Usuario Edito una especialidad', '2014-01-15 10:56:17'),
(5, 'EDITAR', 'jardila', 'ESPECIALIDADES', 1, 'EL Usuario Edito una especialidad', '2014-01-15 10:56:28'),
(6, 'ELIMINAR', 'jardila', 'ESPECIALIDADES', 1, 'EL Usuario Elimino una especialidad', '2014-01-15 10:56:37'),
(8, 'EDITAR', 'unormal', 'TESIS', 1, 'EL Usuario Actualizo una tesis', '2014-01-15 14:29:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TESIS`
--

CREATE TABLE IF NOT EXISTS `TESIS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TITULO` varchar(300) NOT NULL,
  `AUTORES` varchar(150) NOT NULL,
  `ANO` int(11) NOT NULL,
  `ESPECIALIDAD` int(11) NOT NULL,
  `TUTOR_T` int(11) NOT NULL,
  `TUTOR_M` int(11) NOT NULL,
  `RESUMEN` text NOT NULL,
  `USUARIO_CREACION` int(11) NOT NULL,
  `TIPO_DOCUMENTO` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `TESIS`
--

INSERT INTO `TESIS` (`ID`, `TITULO`, `AUTORES`, `ANO`, `ESPECIALIDAD`, `TUTOR_T`, `TUTOR_M`, `RESUMEN`, `USUARIO_CREACION`, `TIPO_DOCUMENTO`) VALUES
(1, 'DESARROLLO DE UN SISTEMA DE INFORMACION PARA EL REGISTRO Y CONSULTAS DE TESIS DE GRADO DE LA UNEFA', 'Jolver Ardila', 2009, 3, 5, 7, 'La finalidad de este trabajo de investigaciÃ³n es la elaboraciÃ³n de una propuesta para el Desarrollo de un Sistema Automatizado para el Registro y Consultas de Tesis de Grado del Instituto Universitario de TecnologÃ­a Juan Pablo PÃ©rez Alfonzo; por cuanto se ha detectado a travÃ©s de los estudios realizados a la instituciÃ³n que existe el problema de que los estudiantes tienen acceso a las tesis de grado de manera manual, lo cual trae consecuencias para los estudiantes como el tiempo de espera, cantidad de informaciÃ³n limitada y mucho espacio fÃ­sico ocupado dentro de la biblioteca. El diseÃ±o de investigaciÃ³n seleccionado fue el no experimental longitudinal por cuanto consiste en una investigaciÃ³n donde no se tiene un control directo sobre las variables debido a que estas ya ocurrieron y asÃ­ mismo se seguirÃ¡ como orientaciÃ³n bÃ¡sica la estructuraciÃ³n de un Cuadro TÃ©cnico MetodolÃ³gico en el cual se sintetiza la investigaciÃ³n Aplicada ya que sustenta las necesidades relativas al bienestar de la sociedad. Las tÃ©cnicas e instrumentos utilizados para la elaboraciÃ³n del mismo fueron la observaciÃ³n directa. Para la propuesta del sistema se pudo desarrollar un instrumento que controlara el registro y las consultas de las tesis de grado en la biblioteca. Entre los beneficios que brinda este proyecto esta la reducciÃ³n notable en el tiempo de consulta, mayor exactitud en las consultas y menos espacio fÃ­sico ocupado, por lo que se recomienda  la aplicaciÃ³n de la propuesta para lograr asÃ­ los objetivos planteados.', 0, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TUTORES`
--

CREATE TABLE IF NOT EXISTS `TUTORES` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CEDULA` int(11) NOT NULL,
  `NOMBRES` varchar(100) NOT NULL,
  `TIPO_TUTOR` int(11) NOT NULL,
  `USER_CRE` varchar(20) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `TUTORES`
--

INSERT INTO `TUTORES` (`ID`, `CEDULA`, `NOMBRES`, `TIPO_TUTOR`, `USER_CRE`) VALUES
(5, 123456, 'Enrique Gonzalez', 1, 'ADMIN'),
(7, 18006922, 'Jolver Ardila', 2, 'ADMIN'),
(8, 15479623, 'Luis Gutierrez', 2, 'ADMIN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `USUARIOS`
--

CREATE TABLE IF NOT EXISTS `USUARIOS` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `state` smallint(6) DEFAULT NULL,
  `priv` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `USUARIOS`
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
