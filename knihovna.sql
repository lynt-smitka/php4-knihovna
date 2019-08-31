/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportování struktury pro tabulka knihovna.ctenari
CREATE TABLE IF NOT EXISTS `ctenari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(50) CHARACTER SET cp1250 COLLATE cp1250_czech_cs DEFAULT NULL,
  `adresa` varchar(50) CHARACTER SET cp1250 COLLATE cp1250_czech_cs DEFAULT NULL,
  `telefon` varchar(20) CHARACTER SET cp1250 COLLATE cp1250_czech_cs DEFAULT NULL,
  `email` varchar(40) CHARACTER SET cp1250 COLLATE cp1250_czech_cs DEFAULT NULL,
  `clenstvi_od` date DEFAULT NULL,
  `placeno` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka knihovna.knihy
CREATE TABLE IF NOT EXISTS `knihy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) DEFAULT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `prekladatel` varchar(50) DEFAULT NULL,
  `isbn` varchar(15) DEFAULT NULL,
  `vydavatel` varchar(50) DEFAULT NULL,
  `rok_vydani` year(4) DEFAULT NULL,
  `anotace` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16376 DEFAULT CHARSET=cp1250;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka knihovna.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jmeno` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka knihovna.vypujcky
CREATE TABLE IF NOT EXISTS `vypujcky` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ctenar` int(11) NOT NULL,
  `id_vytisk` int(11) NOT NULL,
  `vypujceno` date DEFAULT NULL,
  `doba` int(11) DEFAULT 31,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

-- Export dat nebyl vybrán.

-- Exportování struktury pro tabulka knihovna.vytisky
CREATE TABLE IF NOT EXISTS `vytisky` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_knihy` int(11) NOT NULL,
  `ident` varchar(10) CHARACTER SET cp1250 COLLATE cp1250_czech_cs DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6455 DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

-- Export dat nebyl vybrán.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
