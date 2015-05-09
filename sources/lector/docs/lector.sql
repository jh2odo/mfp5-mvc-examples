-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 09-05-2015 a las 20:22:06
-- Versión del servidor: 5.1.73
-- Versión de PHP: 5.3.2-1ubuntu4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `lector`
--

CREATE DATABASE IF NOT EXISTS `lector`;
USE `lector`;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fuente`
--

DROP TABLE IF EXISTS `fuente`;
CREATE TABLE `fuente` (
  `id_fuente`         INT(4)                  NOT NULL AUTO_INCREMENT,
  `titulo_fuente`     VARCHAR(35)
                      COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url_fuente`        VARCHAR(255)
                      COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fecha_alta_fuente` DATE                    NOT NULL DEFAULT '0000-00-00',
  `estado_fuente`     ENUM('activo', 'suspendido')
                      COLLATE utf8_unicode_ci NOT NULL DEFAULT 'activo',
  `tipo_fuente`       ENUM('rss', 'atom', 'otro')
                      COLLATE utf8_unicode_ci NOT NULL DEFAULT 'rss',
  `id_medio_fuente`   INT(4)                  NOT NULL DEFAULT '0',
  `id_seccion_fuente` INT(4)                  NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_fuente`),
  UNIQUE KEY `url_fuente` (`url_fuente`),
  KEY `id_medio_fuente` (`id_medio_fuente`),
  KEY `id_seccion_fuente` (`id_seccion_fuente`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci
  AUTO_INCREMENT = 172;

--
-- Volcar la base de datos para la tabla `fuente`
--

INSERT INTO `fuente` (`id_fuente`, `titulo_fuente`, `url_fuente`, `fecha_alta_fuente`, `estado_fuente`, `tipo_fuente`, `id_medio_fuente`, `id_seccion_fuente`)
VALUES
  (1, '20Minutos - Ultima Hora', 'http://www.20minutos.es/rss/', '2007-06-17', 'activo', 'rss', 1, 32),
  (2, '20Minutos - Deportes', 'http://www.20minutos.es/rss/deportes/', '2007-06-17', 'activo', 'rss', 1, 1),
  (3, '24HorasLibre - Peru', 'http://www.24horaslibre.com/cuerpo-portada-rss.xml', '2007-06-17', 'suspendido', 'rss', 2, 18),
  (4, 'Abc - Cataluña', 'http://www.abc.es/rss/feeds/abc_Catalunya.xml', '2007-06-17', 'activo', 'rss', 3, 24),
  (5, 'Abc - Tecnologia', 'http://www.abc.es/rss/feeds/abc_Tecnologia.xml', '2007-06-17', 'activo', 'rss', 3, 31),
  (6, 'Abc - Galicia', 'http://www.abc.es/rss/feeds/abc_Galicia.xml', '2007-06-28', 'activo', 'rss', 3, 27),
  (7, 'Abc - Espectaculo', 'http://www.abc.es/rss/feeds/abc_Espectaculos.xml', '2007-06-17', 'activo', 'rss', 3, 8),
  (8, 'Abc - Deportes', 'http://www.abc.es/rss/feeds/abc_Deportes.xml', '2007-06-17', 'activo', 'rss', 3, 1),
  (9, 'Abc - Ultima Hora', 'http://www.abc.es/rss/feeds/abc_Ultima.xml', '2007-06-17', 'activo', 'rss', 3, 32),
  (10, 'Abc - Madrid', 'http://www.abc.es/rss/feeds/abc_Madrid.xml', '2007-06-17', 'activo', 'rss', 3, 28),
  (11, 'Abc - Andalucia', 'http://www.abc.es/rss/feeds/Sevilla_Andalucia.xml', '2007-06-17', 'activo', 'rss', 3, 21),
  (12, 'ABN - Bolivia', 'www.abn.info.ve/rss.php', '2007-06-17', 'suspendido', 'rss', 4, 11),
  (13, 'Adictos 2.0 - Tecnologia', 'http://feeds.feedburner.com/Adictos', '2007-10-19', 'activo', 'rss', 5, 31),
  (14, 'As - Deportes', 'http://www.as.com/rss.html', '2007-06-17', 'activo', 'rss', 7, 1),
  (15, 'AsturiasOpinion - Asturias', 'http://feeds.feedburner.com/Asturiasopinioncom?format=xml', '2007-10-05', 'activo', 'rss', 8, 22),
  (16, 'AtinaChile - Chile', 'http://www.atinachile.cl/rss/node.xml', '2007-06-17', 'activo', 'rss', 9, 12),
  (17, 'BandaAncha.st - Tecnologia', 'http://www.bandaancha.st/backend.xml', '2007-06-17', 'activo', 'rss', 10, 31),
  (18, 'Barrapunto - Tecnologia', 'http://backends.barrapunto.com/barrapunto.rss', '2007-06-17', 'activo', 'rss', 12, 31),
  (19, 'BBCWorld - Economia', 'http://xml.newsisfree.com/feeds/73/6673.xml', '2007-06-19', 'activo', 'rss', 13, 7),
  (20, 'BBCWorld - Ultima Hora', 'http://newsrss.bbc.co.uk/rss/spanish/news/rss.xml', '2007-06-17', 'activo', 'rss', 13, 32),
  (21, 'Bloggea2 - Tecnologia', 'http://bloggea2.com/feed/', '2007-06-30', 'suspendido', 'rss', 14, 31),
  (22, 'CadenaSer - Ultima Hora', 'http://www.cadenaser.com/rss.html', '2007-06-17', 'activo', 'rss', 15, 32),
  (23, 'CanalSur - Andalucia', 'http://www.canalsur.es/rss/informativos/portada?idCanal=713&idActivo=170', '2007-06-17', 'activo', 'rss', 16, 21),
  (24, 'Canarias7 - Canarias', 'http://www.canarias7.es/rsstitulares.cfm', '2007-06-17', 'activo', 'rss', 18, 23),
  (25, 'Clarin - Argentina', 'http://www.clarin.com/diario/hoy/um/sumariorss.xml', '2007-06-17', 'activo', 'rss', 19, 10),
  (26, 'CoberturaDigital - Tecnologia', 'http://www.coberturadigital.com/feed/', '2007-06-27', 'activo', 'rss', 20, 31),
  (27, 'Cre - Ecuador', 'http://www.cre.com.ec/rss.aspx', '2007-06-17', 'suspendido', 'rss', 21, 16),
  (28, 'Cuabasi - Cuba', 'www.cubasi.cu/rss.aspx', '2007-06-17', 'suspendido', 'rss', 22, 15),
  (29, 'DespuesdeGoogle - Tecnologia', 'http://feeds.feedburner.com/Despuesdegoogle', '2007-06-25', 'suspendido', 'rss', 23, 31),
  (30, 'Diario Directo - Ultima Hora', 'http://www.diariodirecto.com/rss.xml', '2007-10-19', 'suspendido', 'rss', 24, 32),
  (31, 'DiarioCordoba - Andalucia', 'http://www.diariocordoba.com/RSS/101.xml', '2007-06-17', 'activo', 'rss', 25, 21),
  (32, 'DiarioRed - Tecnologia', 'http://diariored.com/index.xml', '2007-06-17', 'suspendido', 'rss', 27, 31),
  (33, 'DiarioTi - Tecnologia', 'http://www.diarioti.com/inc/titulares_rss.xml', '2007-06-17', 'activo', 'rss', 28, 31),
  (34, 'El Confidencial - Varios', 'http://www.elconfidencial.com/rss/salud.xml', '2007-10-19', 'activo', 'rss', 30, 33),
  (35, 'El Confidencial - Ultima Hora', 'http://www.elconfidencial.com/rss/portada.xml', '2007-10-19', 'activo', 'rss', 30, 32),
  (36, 'El Confidencial - Tecnologia', 'http://www.elconfidencial.com/rss/tecnologia.xml', '2007-10-19', 'activo', 'rss', 30, 31),
  (37, 'El Economista - Economia', 'http://www.eleconomista.es/rss/rss-economia.php', '2007-10-19', 'activo', 'rss', 31, 7),
  (38, 'El periodico de extremadura - Espec', 'http://www.elperiodicoextremadura.com/RSS/215.xml', '2007-06-25', 'activo', 'rss', 32, 8),
  (39, 'El periodico de extremadura - Depor', 'http://www.elperiodicoextremadura.com/RSS/7.xml', '2007-06-25', 'activo', 'rss', 32, 1),
  (40, 'El periodico de extremadura - Inter', 'http://www.elperiodicoextremadura.com/RSS/4.xml', '2007-06-25', 'activo', 'rss', 32, 9),
  (41, 'El periodico de extremadura - Econo', 'http://www.elperiodicoextremadura.com/RSS/5.xml', '2007-06-25', 'activo', 'rss', 32, 7),
  (42, 'El periodico de extremadura - Extre', 'http://www.elperiodicoextremadura.com/RSS/105.xml', '2007-06-25', 'activo', 'rss', 32, 26),
  (43, 'El periodico de extremadura - Vario', 'http://www.elperiodicoextremadura.com/RSS/217.xml', '2007-06-25', 'activo', 'rss', 32, 33),
  (44, 'El periodico de extremadura - Tecno', 'http://www.elperiodicoextremadura.com/RSS/218.xml', '2007-06-25', 'activo', 'rss', 32, 31),
  (45, 'El periodico de extremadura - Españ', 'http://www.elperiodicoextremadura.com/RSS/3.xml', '2007-06-25', 'activo', 'rss', 32, 20),
  (46, 'ElCamajan - Cuba', 'http://www.elcamajan.com/export.php', '2007-06-17', 'suspendido', 'rss', 33, 15),
  (47, 'ElMundo - Ultima Hora', 'http://rss.elmundo.es/rss/descarga.htm?data2=4', '2007-06-17', 'activo', 'rss', 34, 32),
  (48, 'ElMundo - Internacional', 'http://rss.elmundo.es/rss/descarga.htm?data2=9', '2007-06-17', 'activo', 'rss', 34, 9),
  (49, 'ElMundo - Varios', 'http://rss.elmundo.es/rss/descarga.htm?data2=15', '2007-06-24', 'activo', 'rss', 34, 33),
  (50, 'ElMundo - Deportes', 'http://rss.elmundo.es/rss/descarga.htm?data2=14', '2007-06-17', 'activo', 'rss', 34, 1),
  (51, 'ElMundo - España', 'http://rss.elmundo.es/rss/descarga.htm?data2=8', '2007-06-17', 'activo', 'rss', 34, 20),
  (52, 'ElMundo - Economia', 'http://rss.elmundo.es/rss/descarga.htm?data2=7', '2007-06-24', 'activo', 'rss', 34, 7),
  (53, 'ElPais - Andalucia', 'http://www.elpais.com/rss/feed.html?feedId=1015', '2007-06-26', 'activo', 'rss', 35, 21),
  (54, 'ElPais - Espectaculo', 'http://www.ELPAIS.com/rss/feed.html?feedId=1', '2007-06-17', 'activo', 'rss', 35, 8),
  (55, 'ElPais - España', 'http://www.ELPAIS.com/rss/rss_section.html?anchor=elppornac', '2007-06-17', 'activo', 'rss', 35, 20),
  (56, 'ElPais - Deportes', 'http://www.elpais.com/rss/feed.html?feedId=15', '2007-06-17', 'activo', 'rss', 35, 1),
  (57, 'ElPais - Ultima Hora', 'http://www.elpais.com/rss.html', '2007-06-17', 'activo', 'rss', 35, 32),
  (58, 'ElPais - Internacional', 'http://www.elpais.com/rss/rss_section.html?anchor=elpporint', '2007-06-17', 'activo', 'rss', 35, 9),
  (59, 'ElPeriodico - Ultima Hora', 'http://xml.newsisfree.com/feeds/08/5308.xml', '2007-06-17', 'activo', 'rss', 36, 32),
  (60, 'ElPeriodico - Cataluña', 'http://www.elperiodico.com/rss.asp?id=46', '2007-06-17', 'activo', 'rss', 36, 24),
  (61, 'ElPlural.Com - Ultima Hora', 'http://www.elplural.com/rss/articulos.php', '2007-07-28', 'suspendido', 'rss', 37, 32),
  (63, 'eltiempo.com - Internacional', 'http://www.eltiempo.com/mundo/latinoamerica/rss.xml', '2007-06-26', 'suspendido', 'rss', 38, 9),
  (64, 'eltiempo.com - Colombia', 'http://www.eltiempo.com/colombia/politica/rss.xml', '2007-06-26', 'suspendido', 'rss', 38, 13),
  (65, 'eltiempo.com - Deportes', 'http://www.eltiempo.com/deportes/rss.xml', '2007-06-26', 'activo', 'rss', 38, 1),
  (66, 'ElUniversal - Mexico', 'http://www.eluniversal.com.mx/rss/mexico.xml', '2007-06-17', 'activo', 'rss', 39, 17),
  (67, 'Error500 - Tecnologia', 'http://feeds.feedburner.com/error500?q=node/feed', '2007-07-25', 'activo', 'rss', 40, 31),
  (68, 'Genbeta - Tecnologia', 'http://www.genbeta.com/categoria/actualidad/rss2.xml', '2007-06-17', 'activo', 'rss', 41, 31),
  (69, 'Hoy - Espectaculo', 'http://www.hoy.es/rss/feeds/television.xml', '2007-06-25', 'activo', 'rss', 43, 8),
  (70, 'Hoy - Extremadura', 'http://www.hoy.es/rss/feeds/prov_badajoz.xml', '2007-06-25', 'activo', 'rss', 43, 26),
  (71, 'Hoy - Extremadura', 'http://www.hoy.es/rss/feeds/merida.xml', '2007-06-25', 'activo', 'rss', 43, 26),
  (72, 'Hoy - Economia', 'http://www.hoy.es/rss/feeds/economia.xml', '2007-06-25', 'activo', 'rss', 43, 7),
  (73, 'Hoy - Ultima Hora', 'http://www.hoy.es/rss/feeds/ultima.xml', '2007-06-25', 'activo', 'rss', 43, 32),
  (74, 'Hoy - Internacional', 'http://www.hoy.es/rss/feeds/internacional.xml', '2007-06-25', 'activo', 'rss', 43, 9),
  (75, 'Hoy - Extremadura', 'http://www.hoy.es/rss/feeds/prov_caceres.xml', '2007-06-25', 'activo', 'rss', 43, 26),
  (76, 'Hoy - España', 'http://www.hoy.es/rss/feeds/nacional.xml', '2007-06-25', 'activo', 'rss', 43, 20),
  (77, 'Hoy - Deportes', 'http://www.hoy.es/rss/feeds/deportes.xml', '2007-06-25', 'activo', 'rss', 43, 1),
  (78, 'IBLNews - Ultima Hora', 'http://iblnews.com/rss0.php', '2007-06-17', 'suspendido', 'rss', 44, 32),
  (79, 'iMente - Andalucia', 'http://rss.imente.com/967731315.rss', '2007-07-01', 'suspendido', 'rss', 45, 21),
  (80, 'iMente - Peru', 'http://rss.imente.com/988227412.rss', '2007-07-01', 'suspendido', 'rss', 45, 18),
  (81, 'iMente - Argentina', 'http://rss.imente.com/988226843.rss', '2007-07-01', 'suspendido', 'rss', 45, 10),
  (82, 'iMente - Canarias', 'http://rss.imente.com/967731965.rss', '2007-07-01', 'suspendido', 'rss', 45, 23),
  (83, 'iMente - Navarra', 'http://rss.imente.com/967732871.rss', '2007-07-01', 'suspendido', 'rss', 45, 29),
  (84, 'iMente - Madrid', 'http://rss.imente.com/967732637.rss', '2007-07-01', 'suspendido', 'rss', 45, 28),
  (85, 'iMente - Chile', 'http://rss.imente.com/988228631.rss', '2007-07-01', 'suspendido', 'rss', 45, 12),
  (86, 'iMente - Economia', 'http://rss.imente.com/983805827.rss', '2007-07-01', 'suspendido', 'rss', 45, 7),
  (87, 'iMente - Tecnologia', 'http://rss.imente.com/971250445.rss', '2007-07-01', 'suspendido', 'rss', 45, 31),
  (88, 'iMente - Ultima Hora', 'http://rss.imente.com/979765167.rss', '2007-07-01', 'suspendido', 'rss', 45, 32),
  (89, 'iMente - Deportes', 'http://rss.imente.com/967749572.rss', '2007-07-01', 'suspendido', 'rss', 45, 1),
  (90, 'iMente - Mexico', 'http://rss.imente.com/988227527.rss', '2007-07-01', 'suspendido', 'rss', 45, 17),
  (91, 'IndealDigital - Ultima Hora', 'http://www.ideal.es/almeria/rss/feeds/ultima.xml', '2007-06-25', 'activo', 'rss', 46, 32),
  (92, 'IndealDigital - Economia', 'http://www.ideal.es/almeria/rss/feeds/economia.xml', '2007-06-25', 'activo', 'rss', 46, 7),
  (93, 'IndealDigital - Andalucia', 'http://www.ideal.es/almeria/rss/feeds/andalucia.xml', '2007-06-25', 'activo', 'rss', 46, 21),
  (94, 'INE - Varios', 'http://www.ine.es/rss/rssine.rss', '2007-06-24', 'activo', 'rss', 47, 33),
  (95, 'InterBusca - Navarra', 'http://www.interbusca.com/rss/noticias/regional/navarra.xml', '2007-06-17', 'activo', 'rss', 49, 29),
  (96, 'InterBusca - Cataluña', 'http://www.interbusca.com/rss/noticias/regional/cataluna.xml', '2007-06-17', 'activo', 'rss', 49, 24),
  (97, 'IPSNoticias - Economia', 'http://www.ipsnoticias.net/rss/economia.xml', '2007-06-19', 'activo', 'rss', 50, 7),
  (98, 'IPSNoticias - Venezuela', 'www.ipsnoticias.net/rss/venezuela.xml', '2007-06-17', 'suspendido', 'rss', 50, 19),
  (99, 'Jp-geek - Tecnologia', 'http://feeds.feedburner.com/Jp-geek', '2007-06-30', 'activo', 'rss', 51, 31),
  (100, 'LaNacion - Argentina', 'http://www.lanacion.com.ar/herramientas/rss/index.asp?origen=2', '2007-06-17', 'activo', 'rss', 53, 10),
  (101, 'LaPalmaEnLinea - Canarias', 'http://www.lapalmaenlinea.com/rss2.php', '2007-06-22', 'activo', 'rss', 54, 23),
  (102, 'LaRepublica - Ultima Hora', 'http://larepublica.es/backend.php3?id_rubrique=57', '2007-06-17', 'suspendido', 'rss', 55, 32),
  (103, 'LaVanguardia - Ultima Hora', 'http://www.lavanguardia.es/rss/index.rss', '2007-06-17', 'activo', 'rss', 56, 32),
  (104, 'LibertadDigital - Internacional', 'http://libertaddigital.es/rss/internacional.xml', '2007-06-17', 'activo', 'rss', 58, 9),
  (105, 'LibertadDigital - España', 'http://libertaddigital.es/rss/nacional.xml', '2007-06-17', 'activo', 'rss', 58, 20),
  (106, 'LibertadDigital - Deportes', 'http://www.libertaddigital.com/rss/deportes.xml', '2007-06-17', 'activo', 'rss', 58, 1),
  (107, 'LibertadDigital - Ultima Hora', 'http://libertaddigital.es/rss/portada.xml', '2007-06-17', 'activo', 'rss', 58, 32),
  (108, 'MadridDiario - Varios', 'http://www.madridiario.es/medioambiente/rss.html', '2007-10-19', 'activo', 'rss', 59, 33),
  (109, 'MadridDiario - Madrid', 'http://www.madridiario.es/madrid/rss.html', '2007-10-19', 'activo', 'rss', 59, 28),
  (110, 'MadridDiario - Tecnologia', 'http://www.madridiario.es/ciencia-tecnologia/rss.html', '2007-10-19', 'activo', 'rss', 59, 31),
  (111, 'MadridDiario - Ultima Hora', 'http://www.madridiario.es/rss.html', '2007-10-19', 'activo', 'rss', 59, 32),
  (112, 'MicroZulo - Tecnologia', 'http://feeds.feedburner.com/Microzulo?format=xml', '2007-06-30', 'activo', 'rss', 62, 31),
  (113, 'NewsIsFree - Economia', 'http://xml.newsisfree.com/feeds/04/1604.xml', '2007-06-19', 'activo', 'rss', 64, 7),
  (114, 'NoticiasdeNavarra - Navarra', 'http://www.noticiasdenavarra.com/rss/titulares.xml', '2007-06-17', 'activo', 'rss', 65, 29),
  (115, 'NotiEmail - Ecuador', 'http://ecuador.notiemail.com/rss/rss6.asp', '2007-06-17', 'suspendido', 'rss', 66, 16),
  (116, 'NotiEmail - CostaRica', 'http://costarica.notiemail.com/rss/rss15.asp', '2007-06-17', 'suspendido', 'rss', 66, 14),
  (117, 'NotiEmail - Peru', 'http://peru.notiemail.com/rss/rss10.asp', '2007-06-17', 'suspendido', 'rss', 66, 18),
  (118, 'NotiEmail - Bolivia', 'bolivia.notiemail.com/rss/rss1.asp', '2007-06-17', 'suspendido', 'rss', 66, 11),
  (119, 'NotiEmail - Chile', 'http://chile.notiemail.com/rss/rss3.asp', '2007-06-17', 'suspendido', 'rss', 66, 12),
  (120, 'OtroMadrid.org - Madrid', 'http://www.otromadrid.org/rss/noticias_madrid.php', '2007-06-17', 'activo', 'rss', 67, 28),
  (121, 'Periodismo en red - Argentina', 'http://feeds.feedburner.com/PeriodismoEnRed', '2008-10-01', 'activo', 'rss', 68, 10),
  (122, 'Publico - Economia', 'http://www.publico.es/rss?seccion=Dinero', '2007-10-06', 'activo', 'rss', 70, 7),
  (123, 'Publico - España', 'http://www.publico.es/rss?seccion=Espana', '2007-10-06', 'activo', 'rss', 70, 20),
  (124, 'Publico - Varios', 'http://www.publico.es/rss?seccion=Gente', '2007-10-06', 'activo', 'rss', 70, 33),
  (125, 'Publico - Internacional', 'http://www.publico.es/rss?seccion=Internacional', '2007-10-06', 'activo', 'rss', 70, 9),
  (126, 'R.Universidad Chile - Chile', 'http://www.radio.uchile.cl/rss/UltimasNoticias.aspx', '2007-06-17', 'activo', 'rss', 71, 12),
  (127, 'RiojaDifusion - Rioja', 'http://www.radioharo.com/rss/rss.asp', '2007-06-17', 'suspendido', 'rss', 73, 30),
  (128, 'RNV - Venezuela', 'http://www.rnv.gov.ve/noticias/index.php?act=ShowRSS', '2007-06-17', 'activo', 'rss', 74, 19),
  (129, 'Sentimiento Bursatil - Economia', 'http://www.sentimientobursatil.com/sb.xml', '2007-06-19', 'suspendido', 'rss', 75, 7),
  (130, 'SevillaFC - Deportes', 'http://www.sevillafc.es/rss/noticias.xml', '2007-06-27', 'activo', 'rss', 76, 1),
  (131, 'Sport.es - Baloncesto', 'http://www.sport.es/rss.asp?id=808', '2007-06-26', 'activo', 'rss', 79, 2),
  (132, 'Sport.es - Futbol Primera', 'http://www.sport.es/rss.asp?id=805', '2007-06-26', 'activo', 'rss', 79, 3),
  (133, 'Sport.es - Deportes', 'http://www.sport.es/rss.asp', '2007-06-26', 'activo', 'rss', 79, 1),
  (134, 'Sport.es - Futbol Internacional', 'http://www.sport.es/rss.asp?id=806', '2007-06-26', 'activo', 'rss', 79, 5),
  (135, 'SurAmericaPress - Internacional', 'http://www.suramericapress.com/b2rss2.php', '2008-10-01', 'suspendido', 'rss', 80, 9),
  (136, 'Trabber - Varios', 'http://noticias.trabber.com/index.php/feed/', '2007-08-29', 'activo', 'rss', 81, 33),
  (137, 'W3C - Tecnologia', 'http://www.w3c.es/noticias.rss', '2007-06-17', 'activo', 'rss', 83, 31),
  (138, 'Webadictos - Tecnologia', 'http://feeds.feedburner.com/Webadictos', '2007-10-27', 'activo', 'rss', 84, 31),
  (139, 'WwwhatsNew - Tecnologia', 'http://feeds.feedburner.com/WwwhatsNew.xml', '2007-06-24', 'activo', 'rss', 85, 31),
  (140, 'Abc - Canarias', 'http://www.abc.es/rss/feeds/abc_Canarias.xml', '2009-04-30', 'activo', 'rss', 3, 23),
  (141, 'Abc -  Comunidad Valenciana', 'http://www.abc.es/rss/feeds/abc_Valencia.xml', '2009-04-30', 'activo', 'rss', 3, 36),
  (142, 'abc.es - Castilla y León', 'http://www.abc.es/rss/feeds/abc_CastillaLeon.xml', '2009-04-30', 'activo', 'rss', 3, 25),
  (143, 'Nacional en www.abc.es', 'http://www.abc.es/rss/feeds/abc_Nacional.xml', '2009-04-30', 'activo', 'rss', 3, 20),
  (144, 'TVyRadio en www.abc.es', 'http://www.abc.es/rss/feeds/abc_TvYRadio.xml', '2009-04-30', 'activo', 'rss', 3, 33),
  (145, 'Internacional en www.abc.es', 'http://www.abc.es/rss/feeds/abc_Internacional.xml', '2009-04-30', 'activo', 'rss', 3, 9),
  (146, 'ELPAIS.com - Cataluña', 'http://www.elpais.com/rss/feed.html?feedId=17059', '2009-04-30', 'activo', 'rss', 35, 24),
  (147, 'ELPAIS.com - Comunidad Valenciana', 'http://www.elpais.com/rss/feed.html?feedId=17061', '2009-04-30', 'activo', 'rss', 35, 36),
  (148, 'ELPAIS.com - Madrid', 'http://www.elpais.com/rss/feed.html?feedId=1016', '2009-04-30', 'activo', 'rss', 35, 28),
  (149, 'ELPAIS.com - País Vasco', 'http://www.elpais.com/rss/feed.html?feedId=17062', '2009-04-30', 'activo', 'rss', 35, 37),
  (150, 'ELPAIS.com - Galicia', 'http://www.elpais.com/rss/feed.html?feedId=17063', '2009-04-30', 'activo', 'rss', 35, 27),
  (151, 'ELPAIS.com - Sección Gente y TV', 'http://www.elpais.com/rss/feed.html?feedId=1009', '2009-04-30', 'activo', 'rss', 35, 33),
  (152, 'ELPAIS.com - Sección Economía', 'http://www.elpais.com/rss/feed.html?feedId=1006', '2009-04-30', 'activo', 'rss', 35, 7),
  (153, 'ELPAIS.com - Sección Tecnología', 'http://www.elpais.com/rss/feed.html?feedId=1005', '2009-04-30', 'activo', 'rss', 35, 31),
  (154, 'Castilla y León // elmundo.es', 'http://rss.elmundo.es/rss/descarga.htm?data2=110', '2009-04-30', 'activo', 'rss', 34, 25),
  (155, 'Valencia // elmundo.es', 'http://rss.elmundo.es/rss/descarga.htm?data2=111', '2009-04-30', 'activo', 'rss', 34, 36),
  (156, 'Baleares // elmundo.es', 'http://rss.elmundo.es/rss/descarga.htm?data2=81', '2009-04-30', 'activo', 'rss', 34, 38),
  (157, 'Barcelona // elmundo.es', 'http://rss.elmundo.es/rss/descarga.htm?data2=95', '2009-04-30', 'activo', 'rss', 34, 24),
  (158, 'Madrid24horas // elmundo.es', 'http://rss.elmundo.es/rss/descarga.htm?data2=10', '2009-04-30', 'activo', 'rss', 34, 28),
  (159, 'Portada // Marca // marca.com', 'http://rss.marca.com/rss/descarga.htm?data2=425', '2009-04-30', 'activo', 'rss', 61, 1),
  (160, 'Primera División // Marca // marca.', 'http://rss.marca.com/rss/descarga.htm?data2=394', '2009-04-30', 'activo', 'rss', 61, 3),
  (161, 'Segunda División // Marca // marca.', 'http://rss.marca.com/rss/descarga.htm?data2=391', '2009-04-30', 'activo', 'rss', 61, 4),
  (162, 'Fútbol Internacional // Marca // ma', 'http://rss.marca.com/rss/descarga.htm?data2=393', '2009-04-30', 'activo', 'rss', 61, 5),
  (163, 'Fútbol Selección // Marca // marca.', 'http://rss.marca.com/rss/descarga.htm?data2=423', '2009-04-30',
   'activo', 'rss', 61, 6),
  (164, 'Motor // Marca // marca.comMotor //', 'http://rss.marca.com/rss/descarga.htm?data2=375', '2009-04-30',
   'activo', 'rss', 61, 39),
  (165, 'Baloncesto // Marca // marca.com', 'http://rss.marca.com/rss/descarga.htm?data2=371', '2009-04-30', 'activo',
   'rss', 61, 2),
  (166, 'Tenis // Marca // marca.com', 'http://rss.marca.com/rss/descarga.htm?data2=376', '2009-04-30', 'activo', 'rss',
   61, 40),
  (167, 'RSS de as.com - Futbol', 'http://www.as.com/rss/feed.html?feedId=61', '2009-04-30', 'activo', 'rss', 7, 3),
  (168, 'RSS de as.com - Baloncesto', 'http://www.as.com/rss/feed.html?feedId=62', '2009-04-30', 'activo', 'rss', 7, 2),
  (169, 'RSS de as.com - Motor', 'http://www.as.com/rss/feed.html?feedId=63', '2009-04-30', 'activo', 'rss', 7, 39),
  (170, 'RSS de los40.com - Todos Los Titula', 'http://www.los40.com/rss/feed.html?feedId=14036', '2009-04-30',
   'suspendido', 'rss', 87, 33),
  (171, 'CINCODIAS.com - RSS - Última hora', 'http://www.cincodias.com/rss/', '2009-04-30', 'activo', 'rss', 88, 32);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medio`
--

DROP TABLE IF EXISTS `medio`;
CREATE TABLE `medio` (
  `id_medio`      INT(4)                  NOT NULL AUTO_INCREMENT,
  `titulo_medio`  VARCHAR(35)
                  COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url_medio`     VARCHAR(255)
                  COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `estado_medio`  ENUM('activo', 'suspendido')
                  COLLATE utf8_unicode_ci NOT NULL DEFAULT 'activo',
  `tipo_medio`    ENUM('diario', 'revista', 'blog', 'otro')
                  COLLATE utf8_unicode_ci NOT NULL DEFAULT 'diario',
  `id_pais_medio` INT(3)                  NOT NULL DEFAULT '64'
  COMMENT 'España por defecto',
  PRIMARY KEY (`id_medio`),
  UNIQUE KEY `url_medio` (`url_medio`),
  UNIQUE KEY `titulo_medio` (`titulo_medio`),
  KEY `id_pais_medio` (`id_pais_medio`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci
  AUTO_INCREMENT = 89;

--
-- Volcar la base de datos para la tabla `medio`
--

INSERT INTO `medio` (`id_medio`, `titulo_medio`, `url_medio`, `estado_medio`, `tipo_medio`, `id_pais_medio`) VALUES
  (1, '20Minutos', 'www.20minutos.es', 'activo', 'diario', 64),
  (2, '24HorasLibre', 'www.24horaslibre.com', 'activo', 'diario', 64),
  (3, 'Abc', 'www.abc.es', 'activo', 'diario', 64),
  (4, 'ABN', 'www.abn.info.ve', 'activo', 'diario', 64),
  (5, 'Adictos 2.0', 'adictos2007.blogspot.com/', 'activo', 'diario', 64),
  (6, 'AlAlza.Com', 'www.alalza.com', 'activo', 'diario', 64),
  (7, 'As', 'www.as.com', 'activo', 'diario', 64),
  (8, 'AsturiasOpinion', 'www.asturiasopinion.com', 'activo', 'diario', 64),
  (9, 'AtinaChile', 'www.atinachile.cl', 'activo', 'diario', 64),
  (10, 'BandaAncha.st', 'www.bandaancha.st', 'activo', 'diario', 64),
  (11, 'Baquia', 'www.baquia.com', 'activo', 'diario', 64),
  (12, 'Barrapunto', 'www.barrapunto.com', 'activo', 'diario', 64),
  (13, 'BBCWorld', 'news.bbc.co.uk/hi/spanish/', 'activo', 'diario', 64),
  (14, 'Bloggea2', 'bloggea2.com', 'activo', 'diario', 64),
  (15, 'CadenaSer', 'www.cadenaser.com', 'activo', 'diario', 64),
  (16, 'CanalSur', 'www.canalsur.es', 'activo', 'diario', 64),
  (17, 'CanalTDT', 'www.canaltdt.es', 'activo', 'diario', 64),
  (18, 'Canarias7', 'www.canarias7.es', 'activo', 'diario', 64),
  (19, 'Clarin', 'www.clarin.com', 'activo', 'diario', 64),
  (20, 'CoberturaDigital', 'www.coberturadigital.com', 'activo', 'diario', 64),
  (21, 'Cre', 'www.cre.com.ec', 'activo', 'diario', 64),
  (22, 'Cuabasi', 'www.cubasi.cu', 'activo', 'diario', 64),
  (23, 'DespuesdeGoogle', 'despuesdegoogle.com/', 'activo', 'diario', 64),
  (24, 'Diario Directo', 'www.diariodirecto.com', 'activo', 'diario', 64),
  (25, 'DiarioCordoba', 'www.diariocordoba.com', 'activo', 'diario', 64),
  (26, 'DiarioMetro', 'www.diariometro.es', 'activo', 'diario', 64),
  (27, 'DiarioRed', 'www.diariored.com', 'activo', 'diario', 64),
  (28, 'DiarioTi', 'www.diarioti.com', 'activo', 'diario', 64),
  (29, 'El Blog del Poeta', 'www.elblogdelpoeta.com', 'activo', 'diario', 64),
  (30, 'El Confidencial', 'www.elconfidencial.com', 'activo', 'diario', 64),
  (31, 'El Economista', 'www.eleconomista.es', 'activo', 'diario', 64),
  (32, 'El periodico de extremadura', 'www.elperiodicoextremadura.com', 'activo', 'diario', 64),
  (33, 'ElCamajan', 'www.elcamajan.com', 'activo', 'diario', 64),
  (34, 'ElMundo', 'www.elmundo.es', 'activo', 'diario', 64),
  (35, 'ElPais', 'www.elpais.es', 'activo', 'diario', 64),
  (36, 'ElPeriodico', 'www.elperiodico.com', 'activo', 'diario', 64),
  (37, 'ElPlural.Com', 'www.elplural.com', 'activo', 'diario', 64),
  (38, 'eltiempo.com', 'www.eltiempo.com', 'activo', 'diario', 64),
  (39, 'ElUniversal', 'www.eluniversal.com.mx', 'activo', 'diario', 64),
  (40, 'Error500', 'www.error500.net', 'activo', 'diario', 64),
  (41, 'Genbeta', 'www.genbeta.com', 'activo', 'diario', 64),
  (42, 'Guest In', 'www.guestin.com.ar', 'activo', 'diario', 64),
  (43, 'Hoy', 'www.hoy.es', 'activo', 'diario', 64),
  (44, 'IBLNews', 'www.iblnews.com/', 'activo', 'diario', 64),
  (45, 'iMente', 'www.imente.com', 'activo', 'diario', 64),
  (46, 'IndealDigital', 'www.ideal.es', 'activo', 'diario', 64),
  (47, 'INE', 'www.ine.es', 'activo', 'diario', 64),
  (49, 'InterBusca', 'www.interbusca.com', 'activo', 'diario', 64),
  (50, 'IPSNoticias', 'www.ipsnoticias.net', 'activo', 'diario', 64),
  (51, 'Jp-geek', 'www.jp-geek.com', 'activo', 'diario', 64),
  (52, 'La Voz de Galicia', 'www.lavozdegalicia.es', 'activo', 'diario', 64),
  (53, 'LaNacion', 'www.lanacion.com.ar', 'activo', 'diario', 64),
  (54, 'LaPalmaEnLinea', 'www.lapalmaenlinea.com', 'activo', 'diario', 64),
  (55, 'LaRepublica', 'www.larepublica.es', 'activo', 'diario', 64),
  (56, 'LaVanguardia', 'www.lavanguardia.es', 'activo', 'diario', 64),
  (57, 'LeoNoticias.com', 'www.leonoticias.com/', 'activo', 'diario', 64),
  (58, 'LibertadDigital', 'www.libertaddigital.com', 'activo', 'diario', 64),
  (59, 'MadridDiario', 'www.madridiario.es', 'activo', 'diario', 64),
  (60, 'Maestros del Web', 'www.maestrosdelweb.com', 'activo', 'diario', 64),
  (61, 'Marca', 'www.marca.es', 'activo', 'diario', 64),
  (62, 'MicroZulo', 'www.microzulo.com', 'activo', 'diario', 64),
  (63, 'Nacion', 'www.nacion.com', 'activo', 'diario', 64),
  (64, 'NewsIsFree', 'www.newsisfree.com', 'activo', 'diario', 64),
  (65, 'NoticiasdeNavarra', 'www.noticiasdenavarra.com', 'activo', 'diario', 64),
  (66, 'NotiEmail', 'www.notiemail.com', 'activo', 'diario', 64),
  (67, 'OtroMadrid.org', 'www.otromadrid.org', 'activo', 'diario', 64),
  (68, 'Periodismo en red', 'www.periodismoenred.com.ar', 'activo', 'diario', 64),
  (69, 'Periodista Digital', 'www.periodistadigital.com', 'activo', 'diario', 64),
  (70, 'Publico', 'www.publico.es', 'activo', 'diario', 64),
  (71, 'R.Universidad Chile', 'www.radio.uchile.cl', 'activo', 'diario', 64),
  (72, 'RedCasting', 'www.redcasting.com', 'activo', 'diario', 64),
  (73, 'RiojaDifusion', 'www.radioharo.com', 'activo', 'diario', 64),
  (74, 'RNV', 'www.rnv.gov.ve', 'activo', 'diario', 64),
  (75, 'Sentimiento Bursatil', 'www.sentimientobursatil.com', 'activo', 'diario', 64),
  (76, 'SevillaFC', 'www.sevillafc.es', 'activo', 'diario', 64),
  (77, 'ShellSec.Net', 'www.shellsec.net', 'activo', 'diario', 64),
  (78, 'Soflix', 'www.soflix.com', 'activo', 'diario', 64),
  (79, 'Sport.es', 'www.sport.es', 'activo', 'diario', 64),
  (80, 'SurAmericaPress', 'www.suramericapress.com/', 'activo', 'diario', 64),
  (81, 'Trabber', 'www.trabber.com', 'activo', 'diario', 64),
  (82, 'VnuNet', 'www.vnunet.es', 'activo', 'diario', 64),
  (83, 'W3C - España', 'www.w3c.es', 'activo', 'diario', 64),
  (84, 'Webadictos', 'webadictos.blogsome.com', 'activo', 'diario', 64),
  (85, 'WwwhatsNew', 'wwwhatsnew.com', 'activo', 'diario', 64),
  (87, 'Los40', 'www.los40.com', 'activo', 'otro', 64),
  (88, 'CincoDias', 'www.cincodias.com', 'activo', 'diario', 64);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pais`
--

DROP TABLE IF EXISTS `pais`;
CREATE TABLE `pais` (
  `id_pais`     INT(3)                    NOT NULL AUTO_INCREMENT,
  `codigo_pais` CHAR(2)
                CHARACTER SET latin1
                COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `nombre_pais` VARCHAR(80)
                CHARACTER SET latin1
                COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_pais`),
  UNIQUE KEY `nombre` (`nombre_pais`),
  KEY `codigo_pais` (`codigo_pais`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci
  AUTO_INCREMENT = 240;

--
-- Volcar la base de datos para la tabla `pais`
--

INSERT INTO `pais` (`id_pais`, `codigo_pais`, `nombre_pais`) VALUES
  (1, 'AF', 'AFGHANISTÁN'),
  (2, 'AL', 'ALBANIA'),
  (3, 'DE', 'ALEMANIA'),
  (4, 'AD', 'ANDORRA'),
  (5, 'AO', 'ANGOLA'),
  (6, 'AI', 'ANGUILA'),
  (7, 'AQ', 'ANTÁRTIDA'),
  (8, 'AG', 'ANTIGUA Y BARBUDA'),
  (9, 'AN', 'ANTILLAS HOLANDESAS'),
  (10, 'SA', 'ARABIA SAUDÍ'),
  (11, 'DZ', 'ARGELIA'),
  (12, 'AR', 'ARGENTINA'),
  (13, 'AM', 'ARMENIA'),
  (14, 'AW', 'ARUBA'),
  (15, 'AU', 'AUSTRALIA'),
  (16, 'AT', 'AUSTRIA'),
  (17, 'AZ', 'AZERBAIYÁN'),
  (18, 'BS', 'BAHAMAS'),
  (19, 'BD', 'BANGLADESH'),
  (20, 'BB', 'BARBADOS'),
  (21, 'BZ', 'BELICE'),
  (22, 'BJ', 'BENIN'),
  (23, 'BM', 'BERMUDA'),
  (24, 'BE', 'BÉLGICA'),
  (25, 'BT', 'BHUTÁN'),
  (26, 'BY', 'BIELORRUSIA'),
  (27, 'MM', 'BIRMANIA (MYANMAR)'),
  (28, 'BO', 'BOLIVIA'),
  (29, 'BA', 'BOSNIA-HERCEGOVINA'),
  (30, 'BW', 'BOTSWANA'),
  (31, 'BR', 'BRASIL'),
  (32, 'BN', 'BRUNEI DARUSSALAM'),
  (33, 'BG', 'BULGARIA'),
  (34, 'BF', 'BURKINA FASO'),
  (35, 'BI', 'BURUNDI'),
  (36, 'CV', 'CABO VERDE'),
  (37, 'KH', 'CAMBOYA'),
  (38, 'CM', 'CAMERÚN'),
  (39, 'CA', 'CANADÁ'),
  (40, 'TD', 'CHAD'),
  (41, 'CL', 'CHILE'),
  (42, 'CN', 'CHINA'),
  (43, 'CY', 'CHIPRE'),
  (44, 'VA', 'CIUDAD DEL VATICANO'),
  (45, 'CO', 'COLOMBIA'),
  (46, 'KM', 'COMORES'),
  (47, 'CG', 'CONGO BRAZZAVILLE'),
  (48, 'CD', 'CONGO KINSHASA'),
  (49, 'KR', 'COREA, REPÚBLICA'),
  (50, 'KP', 'COREA, REPÚBLICA POPULAR'),
  (51, 'CI', 'COSTA DE MARFIL'),
  (52, 'CR', 'COSTA RICA'),
  (53, 'HR', 'CROACIA'),
  (54, 'CU', 'CUBA'),
  (55, 'DK', 'DINAMARCA'),
  (56, 'DM', 'DOMINICA'),
  (57, 'EC', 'ECUADOR'),
  (58, 'EG', 'EGIPTO'),
  (59, 'SV', 'EL SALVADOR'),
  (60, 'AE', 'EMIRATOS ÁRABES UNIDOS'),
  (61, 'ER', 'ERITREA'),
  (62, 'SK', 'ESLOVAQUIA'),
  (63, 'SI', 'ESLOVENIA'),
  (64, 'ES', 'ESPAÑA'),
  (65, 'US', 'ESTADOS UNIDOS'),
  (66, 'EE', 'ESTONIA'),
  (67, 'ET', 'ETIOPÍA'),
  (68, 'RU', 'FEDERACIÓN RUSA'),
  (69, 'FJ', 'FIJI'),
  (70, 'PH', 'FILIPINAS'),
  (71, 'FI', 'FINLANDIA'),
  (72, 'FR', 'FRANCIA'),
  (73, 'GA', 'GABÓN'),
  (74, 'GM', 'GAMBIA'),
  (75, 'GE', 'GEORGIA'),
  (76, 'GH', 'GHANA'),
  (77, 'GI', 'GIBRALTAR'),
  (78, 'GD', 'GRANADA'),
  (79, 'GR', 'GRECIA'),
  (80, 'GL', 'GROENLANDIA'),
  (81, 'GP', 'GUADALUPE'),
  (82, 'GU', 'GUAM'),
  (83, 'GT', 'GUATEMALA'),
  (84, 'GY', 'GUAYANA'),
  (85, 'GF', 'GUAYANA FRANCESA'),
  (86, 'GN', 'GUINEA'),
  (87, 'GQ', 'GUINEA ECUATORIAL'),
  (88, 'GW', 'GUINEA-BISSAU'),
  (89, 'HT', 'HAITÍ'),
  (90, 'NL', 'HOLANDA (PAÍSES BAJOS)'),
  (91, 'HN', 'HONDURAS'),
  (92, 'HK', 'HONG KONG'),
  (93, 'HU', 'HUNGRÍA'),
  (94, 'IN', 'INDIA'),
  (95, 'ID', 'INDONESIA'),
  (96, 'IQ', 'IRAK'),
  (97, 'IR', 'IRÁN'),
  (98, 'IE', 'IRLANDA'),
  (99, 'BV', 'ISLA BOUVET'),
  (100, 'CX', 'ISLA CHRISTMAS'),
  (101, 'NF', 'ISLA NORFOLK'),
  (102, 'IS', 'ISLANDIA'),
  (103, 'KY', 'ISLAS CAIMÁN'),
  (104, 'CC', 'ISLAS COCOS (KEELING)'),
  (105, 'CK', 'ISLAS COOK'),
  (106, 'FO', 'ISLAS FEROE'),
  (107, 'GS', 'ISLAS GEORGIA Y SANDWICH DEL SUR'),
  (108, 'HM', 'ISLAS HEARD Y MC DONALD'),
  (109, 'FK', 'ISLAS MALVINAS'),
  (110, 'MP', 'ISLAS MARIANAS DEL NORTE'),
  (111, 'MH', 'ISLAS MARSHAL'),
  (112, 'UM', 'ISLAS MENORES DE LOS EE.UU.'),
  (113, 'SB', 'ISLAS SALOMÓN'),
  (114, 'SJ', 'ISLAS SVALBARD Y JAN MAYEN'),
  (115, 'TC', 'ISLAS TURKS Y CAICOS'),
  (116, 'VG', 'ISLAS VÍRGENES (BRITÁNICAS)'),
  (117, 'VI', 'ISLAS VÍRGENES (EE.UU.)'),
  (118, 'WF', 'ISLAS WALLIS Y FUTUNA'),
  (119, 'IL', 'ISRAEL'),
  (120, 'IT', 'ITALIA'),
  (121, 'JM', 'JAMAICA'),
  (122, 'JP', 'JAPÓN'),
  (123, 'JO', 'JORDANIA'),
  (124, 'KZ', 'KAZAJSTÁN'),
  (125, 'KE', 'KENIA'),
  (126, 'KG', 'KIRGISTÁN'),
  (127, 'KI', 'KIRIBATI'),
  (128, 'KW', 'KUWAIT'),
  (129, 'LA', 'LAOS, REPÚBLICA POPULAR'),
  (130, 'LS', 'LESOTHO'),
  (131, 'LV', 'LETONIA'),
  (132, 'LR', 'LIBERIA'),
  (133, 'LY', 'LIBIA'),
  (134, 'LI', 'LIECHTENSTEIN'),
  (135, 'LT', 'LITUANIA'),
  (136, 'LB', 'LÍBANO'),
  (137, 'LU', 'LUXEMBURGO'),
  (138, 'MO', 'MACAO'),
  (139, 'MK', 'MACEDONIA'),
  (140, 'MG', 'MADAGASCAR'),
  (141, 'MY', 'MALASIA'),
  (142, 'MW', 'MALAWI'),
  (143, 'MV', 'MALDIVAS'),
  (144, 'ML', 'MALÍ'),
  (145, 'MT', 'MALTA'),
  (146, 'MA', 'MARRUECOS'),
  (147, 'MQ', 'MARTINICA'),
  (148, 'MU', 'MAURICIO'),
  (149, 'MR', 'MAURITANIA'),
  (150, 'YT', 'MAYOTTE'),
  (151, 'MX', 'MÉXICO'),
  (152, 'FM', 'MICRONESIA'),
  (153, 'MD', 'MOLDAVIA'),
  (154, 'MN', 'MONGOLIA'),
  (155, 'MS', 'MONTSERRAT'),
  (156, 'MZ', 'MOZAMBIQUE'),
  (157, 'MC', 'MÓNACO'),
  (158, 'NA', 'NAMIBIA'),
  (159, 'NR', 'NAURU'),
  (160, 'NP', 'NEPAL'),
  (161, 'NI', 'NICARAGUA'),
  (162, 'NG', 'NIGERIA'),
  (163, 'NU', 'NIUE'),
  (164, 'NE', 'NÍGER'),
  (165, 'NO', 'NORUEGA'),
  (166, 'NC', 'NUEVA CALEDONIA'),
  (167, 'NZ', 'NUEVA ZELANDA'),
  (168, 'OM', 'OMÁN'),
  (169, 'PK', 'PAKISTÁN'),
  (170, 'PW', 'PALAU'),
  (171, 'PS', 'PALESTINA'),
  (172, 'PA', 'PANAMÁ'),
  (173, 'PG', 'PAPUA NUEVA GUINEA'),
  (174, 'PY', 'PARAGUAY'),
  (175, 'PE', 'PERÚ'),
  (176, 'PN', 'PITCAIRN'),
  (177, 'PF', 'POLINESIA FRANCESA (TAHITÍ)'),
  (178, 'PL', 'POLONIA'),
  (179, 'PT', 'PORTUGAL'),
  (180, 'PR', 'PUERTO RICO'),
  (181, 'QA', 'QATAR'),
  (182, 'GB', 'REINO UNIDO'),
  (183, 'CF', 'REPÚBLICA CENTROAFRICANA'),
  (184, 'CZ', 'REPÚBLICA CHECA'),
  (185, 'DO', 'REPÚBLICA DOMINICANA'),
  (186, 'RE', 'REUNIÓN'),
  (187, 'RW', 'RUANDA'),
  (188, 'RO', 'RUMANÍA'),
  (189, 'KN', 'SAINT KITTS Y NEVIS'),
  (190, 'WS', 'SAMOA'),
  (191, 'AS', 'SAMOA ESTADOUNIDENSE'),
  (192, 'SM', 'SAN MARINO'),
  (193, 'VC', 'SAN VICENTE Y LAS GRANADINAS'),
  (194, 'SH', 'SANTA HELENA'),
  (195, 'LC', 'SANTA LUCÍA'),
  (196, 'ST', 'SANTO TOMÉ Y PRÍNCIPE'),
  (197, 'EH', 'SÁHARA OCCIDENTAL'),
  (198, 'SN', 'SENEGAL'),
  (199, 'SC', 'SEYCHELLES'),
  (200, 'SL', 'SIERRA LEONA'),
  (201, 'XX', 'SIN PAIS'),
  (202, 'SG', 'SINGAPUR'),
  (203, 'SY', 'SIRIA'),
  (204, 'SO', 'SOMALIA'),
  (205, 'LK', 'SRI LANKA'),
  (206, 'PM', 'ST. PIERRE Y MIQUELON'),
  (207, 'ZA', 'SUDÁFRICA'),
  (208, 'SD', 'SUDÁN'),
  (209, 'SE', 'SUECIA'),
  (210, 'CH', 'SUIZA'),
  (211, 'SR', 'SURINAM'),
  (212, 'SZ', 'SWAZILANDIA'),
  (213, 'TH', 'TAILANDIA'),
  (214, 'TW', 'TAIWÁN'),
  (215, 'TJ', 'TAJIKISTÁN'),
  (216, 'TZ', 'TANZANIA'),
  (217, 'IO', 'TERRITORIO BRITÁNICO DEL ÍNDICO'),
  (218, 'TF', 'TERRITORIOS FRANCESES DEL SUR'),
  (219, 'TL', 'TIMOR-LESTE'),
  (220, 'TG', 'TOGO'),
  (221, 'TK', 'TOKELAU'),
  (222, 'TO', 'TONGA'),
  (223, 'TT', 'TRINIDAD Y TOBAGO'),
  (224, 'TM', 'TURKMENISTÁN'),
  (225, 'TR', 'TURQUÍA'),
  (226, 'TV', 'TUVALU'),
  (227, 'TN', 'TÚNEZ'),
  (228, 'UA', 'UCRANIA'),
  (229, 'UG', 'UGANDA'),
  (230, 'UY', 'URUGUAY'),
  (231, 'UZ', 'UZBEKISTÁN'),
  (232, 'VU', 'VANUATU'),
  (233, 'VE', 'VENEZUELA'),
  (234, 'VN', 'VIETNAM'),
  (235, 'YE', 'YEMEN'),
  (236, 'DJ', 'YIBUTI'),
  (237, 'YU', 'YUGOSLAVIA'),
  (238, 'ZM', 'ZAMBIA'),
  (239, 'ZW', 'ZIMBABWE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seccion`
--

DROP TABLE IF EXISTS `seccion`;
CREATE TABLE `seccion` (
  `id_seccion`         INT(4)                  NOT NULL AUTO_INCREMENT,
  `titulo_seccion`     VARCHAR(35)
                       COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `estado_seccion`     ENUM('activo', 'suspendido')
                       COLLATE utf8_unicode_ci NOT NULL DEFAULT 'activo',
  `subseccion_seccion` INT(4)                  NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_seccion`),
  UNIQUE KEY `titulo_seccion` (`titulo_seccion`),
  KEY `subseccion_seccion` (`subseccion_seccion`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci
  AUTO_INCREMENT = 41;

--
-- Volcar la base de datos para la tabla `seccion`
--

INSERT INTO `seccion` (`id_seccion`, `titulo_seccion`, `estado_seccion`, `subseccion_seccion`) VALUES
  (0, 'Base', 'activo', 0),
  (1, 'Deportes', 'activo', 0),
  (2, 'Baloncesto', 'activo', 1),
  (3, 'Futbol Primera', 'activo', 1),
  (4, 'Futbol Segunda', 'activo', 1),
  (5, 'Futbol Inter.', 'activo', 1),
  (6, 'Seleccion', 'activo', 1),
  (7, 'Economia', 'activo', 0),
  (8, 'Espectaculo', 'activo', 0),
  (9, 'Internacional', 'activo', 0),
  (10, 'Argentina', 'activo', 9),
  (11, 'Bolivia', 'activo', 9),
  (12, 'Chile', 'activo', 9),
  (13, 'Colombia', 'activo', 9),
  (14, 'CostaRica', 'activo', 9),
  (15, 'Cuba', 'activo', 9),
  (16, 'Ecuador', 'activo', 9),
  (17, 'Mexico', 'activo', 9),
  (18, 'Peru', 'activo', 9),
  (19, 'Venezuela', 'activo', 9),
  (20, 'España', 'activo', 0),
  (21, 'Andalucia', 'activo', 20),
  (22, 'Asturias', 'activo', 20),
  (23, 'Canarias', 'activo', 20),
  (24, 'Cataluña', 'activo', 20),
  (25, 'Castilla y León', 'activo', 20),
  (26, 'Extremadura', 'activo', 20),
  (27, 'Galicia', 'activo', 20),
  (28, 'Madrid', 'activo', 20),
  (29, 'Navarra', 'activo', 20),
  (30, 'Rioja', 'activo', 20),
  (31, 'Tecnologia', 'activo', 0),
  (32, 'Ultima Hora', 'activo', 0),
  (33, 'Varios', 'activo', 0),
  (35, 'Guatemala', 'activo', 9),
  (36, 'Comunidad Valenciana', 'activo', 20),
  (37, 'País Vasco', 'activo', 20),
  (38, 'Baleares', 'activo', 20),
  (39, 'Motor', 'activo', 1),
  (40, 'Tenis', 'activo', 1);

--
-- Filtros para las tablas descargadas (dump)
--

--
-- Filtros para la tabla `fuente`
--
ALTER TABLE `fuente`
ADD CONSTRAINT `fuente_ibfk_1` FOREIGN KEY (`id_medio_fuente`) REFERENCES `medio` (`id_medio`)
  ON DELETE NO ACTION
  ON UPDATE CASCADE,
ADD CONSTRAINT `fuente_ibfk_2` FOREIGN KEY (`id_seccion_fuente`) REFERENCES `seccion` (`id_seccion`)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;

--
-- Filtros para la tabla `medio`
--
ALTER TABLE `medio`
ADD CONSTRAINT `medio_ibfk_1` FOREIGN KEY (`id_pais_medio`) REFERENCES `pais` (`id_pais`)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;

--
-- Filtros para la tabla `seccion`
--
ALTER TABLE `seccion`
ADD CONSTRAINT `seccion_ibfk_1` FOREIGN KEY (`subseccion_seccion`) REFERENCES `seccion` (`id_seccion`)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;
