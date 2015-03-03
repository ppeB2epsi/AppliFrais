-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 03 Mars 2015 à 09:52
-- Version du serveur :  5.6.15-log
-- Version de PHP :  5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `gsb_frais`
--

-- --------------------------------------------------------

--
-- Structure de la table `comptable`
--

CREATE TABLE IF NOT EXISTS `comptable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) DEFAULT NULL,
  `prenom` varchar(30) DEFAULT NULL,
  `login` varchar(30) DEFAULT NULL,
  `mdp` varchar(20) DEFAULT NULL,
  `adresse` varchar(30) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `ville` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `etat`
--

CREATE TABLE IF NOT EXISTS `etat` (
  `id` char(2) NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `etat`
--

INSERT INTO `etat` (`id`, `libelle`) VALUES
('CL', 'Saisie cl?tur'),
('CR', 'Fiche cr??e, saisie en cours'),
('RB', 'Rembours'),
('VA', 'Valid?e et mise en paiement');

-- --------------------------------------------------------

--
-- Structure de la table `fichefrais`
--

CREATE TABLE IF NOT EXISTS `fichefrais` (
  `idVisiteur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `nbJustificatifs` int(11) DEFAULT NULL,
  `montantValide` decimal(10,2) DEFAULT NULL,
  `dateModif` date DEFAULT NULL,
  `idEtat` char(2) DEFAULT 'CR',
  PRIMARY KEY (`idVisiteur`,`mois`),
  KEY `idEtat` (`idEtat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `fichefrais`
--

INSERT INTO `fichefrais` (`idVisiteur`, `mois`, `nbJustificatifs`, `montantValide`, `dateModif`, `idEtat`) VALUES
('a17', '201502', 0, NULL, '2015-03-03', 'CL'),
('a17', '201503', 0, NULL, '2015-03-03', 'CR');

-- --------------------------------------------------------

--
-- Structure de la table `fraisforfait`
--

CREATE TABLE IF NOT EXISTS `fraisforfait` (
  `id` char(3) NOT NULL,
  `libelle` char(20) DEFAULT NULL,
  `montant` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `fraisforfait`
--

INSERT INTO `fraisforfait` (`id`, `libelle`, `montant`) VALUES
('ETP', 'Forfait Etape', '110.00'),
('KM', 'Frais Kilométrique', '0.62'),
('NUI', 'Nuitée Hotel', '80.00'),
('REP', 'Repas Restaurant', '25.00');

-- --------------------------------------------------------

--
-- Structure de la table `lignefraisforfait`
--

CREATE TABLE IF NOT EXISTS `lignefraisforfait` (
  `idVisiteur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `idFraisForfait` char(3) NOT NULL,
  `quantite` int(11) DEFAULT NULL,
  PRIMARY KEY (`idVisiteur`,`mois`,`idFraisForfait`),
  KEY `idFraisForfait` (`idFraisForfait`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `lignefraisforfait`
--

INSERT INTO `lignefraisforfait` (`idVisiteur`, `mois`, `idFraisForfait`, `quantite`) VALUES
('a17', '201502', 'ETP', 0),
('a17', '201502', 'KM', 0),
('a17', '201502', 'NUI', 0),
('a17', '201502', 'REP', 0),
('a17', '201503', 'ETP', 0),
('a17', '201503', 'KM', 0),
('a17', '201503', 'NUI', 0),
('a17', '201503', 'REP', 0);

-- --------------------------------------------------------

--
-- Structure de la table `lignefraishorsforfait`
--

CREATE TABLE IF NOT EXISTS `lignefraishorsforfait` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idVisiteur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `libelle` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idVisiteur` (`idVisiteur`,`mois`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

CREATE TABLE IF NOT EXISTS `vehicule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `prix` float(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `vehicule`
--

INSERT INTO `vehicule` (`id`, `libelle`, `prix`) VALUES
(1, 'Véhicule 4CV Diesel', 0.52),
(2, 'Véhicule 5/6CV Diesel', 0.58),
(3, 'Véhicule 4CV Essence ', 0.62),
(4, 'Véhicule 5/6CV Essence', 0.67);

-- --------------------------------------------------------

--
-- Structure de la table `visiteur`
--

CREATE TABLE IF NOT EXISTS `visiteur` (
  `id` char(4) NOT NULL,
  `nom` char(30) DEFAULT NULL,
  `prenom` char(30) DEFAULT NULL,
  `login` char(20) DEFAULT NULL,
  `mdp` char(200) DEFAULT NULL,
  `adresse` char(30) DEFAULT NULL,
  `cp` char(5) DEFAULT NULL,
  `ville` char(30) DEFAULT NULL,
  `dateEmbauche` date DEFAULT NULL,
  `idVehicule` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idVehicule` (`idVehicule`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `visiteur`
--

INSERT INTO `visiteur` (`id`, `nom`, `prenom`, `login`, `mdp`, `adresse`, `cp`, `ville`, `dateEmbauche`, `idVehicule`) VALUES
('a131', 'Villechalane', 'Louis', 'lvillachane', '$2y$10$VzvM3Ao.HS7PLofYAovvo.fwWHYuaazN.Cbh5F79ERjXdxIBb7lDC', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', NULL),
('a17', 'Andre', 'David', 'dandre', '$2y$10$j1GT7E22xF5Rpoblhu3Auuu0dpPvGWtLpY62Nxzm6XUaD0gAMl7wG', '1 rue Petit', '46200', 'Lalbenque', '1998-11-23', NULL),
('a55', 'Bedos', 'Christian', 'cbedos', '$2y$10$97OMXcjyDdfDqjj4WsR.qu3oB//mp1hGIpPAUUps.o4ACqVypqF4u', '1 rue Peranud', '46250', 'Montcuq', '1995-01-12', NULL),
('a93', 'Tusseau', 'Louis', 'ltusseau', '$2y$10$RGqxSBERHnl3a0nGVkvIZOWJyjRMT.ELQTYXZl1MDap/6gEfhFO3e', '22 rue des Ternes', '46123', 'Gramat', '2000-05-01', NULL),
('b13', 'Bentot', 'Pascal', 'pbentot', '$2y$10$yTnfkCTt0aVFedSjynC6KeCsYwCpBddlOj9SeP5vdL.CBj0iX5wTK', '11 all?e des Cerises', '46512', 'Bessines', '1992-07-09', NULL),
('b16', 'Bioret', 'Luc', 'lbioret', '$2y$10$4/Mpu62yUuQKoOCu/iV4ke3wl8WmLwApeIO0L9fO6L4eAiFg49GSO', '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11', NULL),
('b19', 'Bunisset', 'Francis', 'fbunisset', '$2y$10$wi7DBI4AWb0RYUILOVKon.4s2lFUiSGJLmf49dlijehdlRb5lIHLC', '10 rue des Perles', '93100', 'Montreuil', '1987-10-21', NULL),
('b25', 'Bunisset', 'Denise', 'dbunisset', '$2y$10$or8Z5l3VDhBt/Y2HraGsAuG.MsAFWxQD3UPNtGsA18zrEskdY4sn6', '23 rue Manin', '75019', 'paris', '2010-12-05', NULL),
('b28', 'Cacheux', 'Bernard', 'bcacheux', '$2y$10$9q.WV6Qu8UtUfNaCpQYoh.EkzDhOSUoMwJvHUMXrwckhgIdcu4nDi', '114 rue Blanche', '75017', 'Paris', '2009-11-12', NULL),
('b34', 'Cadic', 'Eric', 'ecadic', '$2y$10$QAh/ic/IZPhwYnK2tzaZo.R8Z9OxzxpMzoKWBksYqMnkL.AZAl4oO', '123 avenue de la R?publique', '75011', 'Paris', '2008-09-23', NULL),
('b4', 'Charoze', 'Catherine', 'ccharoze', '$2y$10$JIg4OZAwBCb0ntNKAPGMDuJNWXA7HVzC/AqaCiAQdyEyhfGtdyJuC', '100 rue Petit', '75019', 'Paris', '2005-11-12', NULL),
('b50', 'Clepkens', 'Christophe', 'cclepkens', '$2y$10$1/CBupSWoL4vCPso0k5b5uqqh/gvaaKd3dEysIFOnHG78i58VZaQG', '12 all?e des Anges', '93230', 'Romainville', '2003-08-11', NULL),
('b59', 'Cottin', 'Vincenne', 'vcottin', '$2y$10$dttsKKw/seW9CnQhUPhKde4h6AZuHnRs0k8d4cfMeoUxGTdAn5Lhi', '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18', NULL),
('c14', 'Daburon', 'Fran?ois', 'fdaburon', '$2y$10$fhrEr1iptH0Tio2uXuGnZendZPqNPZW58HYYhgMANFPtDoyWQvspe', '13 rue de Chanzy', '94000', 'Cr?teil', '2002-02-11', NULL),
('c3', 'De', 'Philippe', 'pde', '$2y$10$W94VxKlOyhzXmSAHHrqSS.eSyQsZXZaX.R11d.tz6sr70HhqfAzKe', '13 rue Barthes', '94000', 'Cr?teil', '2010-12-14', NULL),
('c54', 'Debelle', 'Michel', 'mdebelle', '$2y$10$MzzEfdrJftmAwbrQpbqtnue2zdQ.jcBG4dmLUq6Cm.sILQJ1oFqoe', '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23', NULL),
('d13', 'Debelle', 'Jeanne', 'jdebelle', '$2y$10$XijATYzL8U8QmsGiDAELfuECCiZn830i5AeeFKPCGoPjuhQh39m/K', '134 all?e des Joncs', '44000', 'Nantes', '2000-05-11', NULL),
('d51', 'Debroise', 'Michel', 'mdebroise', '$2y$10$v7gDQfcOY7/aM31NRphFiedE3n7mGiMMaUWChfPh.1j5uFtTNpZCO', '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17', NULL),
('e22', 'Desmarquest', 'Nathalie', 'ndesmarquest', '$2y$10$mZV7XTTf0RF947nYWc1dUeypHX5VTOGbWj1D2N50F9nzrTlECHMeG', '14 Place d Arc', '45000', 'Orl?ans', '2005-11-12', NULL),
('e24', 'Desnost', 'Pierre', 'pdesnost', '$2y$10$KNBuepG4biE.B4WwsXDF.uE/tkUskxmY9EhDrzArRsFQ697AUXtNO', '16 avenue des C?dres', '23200', 'Gu?ret', '2001-02-05', NULL),
('e39', 'Dudouit', 'Fr?d?ric', 'fdudouit', '$2y$10$hjN.55dG7r4IM5ku8OYETOT2xFpNTfg5apyrkU5ufPaxINDhgOXdW', '18 rue de l ?glise', '23120', 'GrandBourg', '2000-08-01', NULL),
('e49', 'Duncombe', 'Claude', 'cduncombe', '$2y$10$gmC/qTvhG9V2K4Ma8mhYeuR1V9iQ3r/pWFH34UshAJqNN5MlswTl6', '19 rue de la tour', '23100', 'La souteraine', '1987-10-10', NULL),
('e5', 'Enault-Pascreau', 'C?line', 'cenault', '$2y$10$aR5fjzjyqr6JaoQKatMIte5isR4Jju1bJ.aAZorY6U3P19eNxQ2.q', '25 place de la gare', '23200', 'Gueret', '1995-09-01', NULL),
('e52', 'Eynde', 'Val?rie', 'veynde', '$2y$10$Jl9GUQqHKuEm/3myFLvM..Zo6xxKVbzlk8dxr5ZXyULCEZls6u8Ym', '3 Grand Place', '13015', 'Marseille', '1999-11-01', NULL),
('f21', 'Finck', 'Jacques', 'jfinck', '$2y$10$l9EE8XmpVbGhiGl7N7iMDONi2yfsX3/m.bvQy.F9wOhI1oiAxj8Hi', '10 avenue du Prado', '13002', 'Marseille', '2001-11-10', NULL),
('f39', 'Fr?mont', 'Fernande', 'ffremont', '$2y$10$VoloCSlNkaCx9vPHYyQgouKXFLBIa3TrInKylvpqgKrzv7eCXuHvq', '4 route de la mer', '13012', 'Allauh', '1998-10-01', NULL),
('f4', 'Gest', 'Alain', 'agest', '$2y$10$b2JoXzrtqmSpKqrEnhAffeCARTQqirM5.VEGUz93zHd903rrWvGuu', '30 avenue de la mer', '13025', 'Berre', '1985-11-01', NULL);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `fichefrais`
--
ALTER TABLE `fichefrais`
  ADD CONSTRAINT `fichefrais_ibfk_1` FOREIGN KEY (`idEtat`) REFERENCES `etat` (`id`),
  ADD CONSTRAINT `fichefrais_ibfk_2` FOREIGN KEY (`idVisiteur`) REFERENCES `visiteur` (`id`);

--
-- Contraintes pour la table `lignefraisforfait`
--
ALTER TABLE `lignefraisforfait`
  ADD CONSTRAINT `lignefraisforfait_ibfk_1` FOREIGN KEY (`idVisiteur`, `mois`) REFERENCES `fichefrais` (`idVisiteur`, `mois`),
  ADD CONSTRAINT `lignefraisforfait_ibfk_2` FOREIGN KEY (`idFraisForfait`) REFERENCES `fraisforfait` (`id`);

--
-- Contraintes pour la table `lignefraishorsforfait`
--
ALTER TABLE `lignefraishorsforfait`
  ADD CONSTRAINT `lignefraishorsforfait_ibfk_1` FOREIGN KEY (`idVisiteur`, `mois`) REFERENCES `fichefrais` (`idVisiteur`, `mois`);

--
-- Contraintes pour la table `visiteur`
--
ALTER TABLE `visiteur`
  ADD CONSTRAINT `visiteur_ibfk_1` FOREIGN KEY (`idVehicule`) REFERENCES `vehicule` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
