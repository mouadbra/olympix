-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 05, 2024 at 09:42 AM
-- Server version: 10.11.6-MariaDB-0+deb12u1-log
-- PHP Version: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e22407333_db1`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`e22407333sql`@`%` PROCEDURE `inserer_actu_creation_concours` (IN `id_concours` INT)   BEGIN
    DECLARE nom_concours VARCHAR(60);
    DECLARE date_debut DATE;
    DECLARE organisateur_id INT;

    SELECT con_nom_concours, con_date_debut INTO nom_concours, date_debut
    FROM T_CONCOURS_con WHERE con_id_concours = id_concours;

    SET organisateur_id = get_organisateur_id(id_concours);
    SET @is_admin_deletion = COALESCE(@is_admin_deletion, FALSE);
    IF @is_admin_deletion = FALSE THEN
    INSERT INTO T_ACTU_act (act_titre,act_date, act_description, con_id_concours, adm_id_admin)
    VALUES ('Nouveau Concours',CURDATE(), CONCAT(nom_concours, ' a été créé le ', date_debut), id_concours, organisateur_id);
     END IF;
END$$

CREATE DEFINER=`e22407333sql`@`%` PROCEDURE `insert_actu` ()   BEGIN
        DECLARE nom VARCHAR(60);
    DECLARE lieu VARCHAR(60);
    DECLARE dates DATE;
    DECLARE concours_id INT;
    DECLARE adm_id INT;

    SET concours_id = dernier_id_concours();

    SELECT con_nom_concours, con_date_debut, con_lieu, adm_id_admin 
    INTO nom, dates, lieu, adm_id 
    FROM T_CONCOURS_con 
    WHERE con_id_concours = concours_id;

    INSERT INTO T_ACTU_act (act_date, act_description, con_id_concours, adm_id_admin)
    VALUES (CURDATE(), CONCAT_WS(' ', nom, dates, lieu), concours_id, adm_id);
END$$

CREATE DEFINER=`e22407333sql`@`%` PROCEDURE `sp_supprimer_concours` (IN `p_id_concours` INT)   BEGIN
    -- Supprimer les actualités associées
    DELETE FROM T_ACTU_act 
    WHERE con_id_concours = p_id_concours;

    -- Supprimer le concours
    DELETE FROM T_CONCOURS_con 
    WHERE con_id_concours = p_id_concours;
END$$

--
-- Functions
--
CREATE DEFINER=`e22407333sql`@`%` FUNCTION `dernier_id_concours` () RETURNS INT(11)  BEGIN
    DECLARE dernier_id INT;  -- Déclare une variable pour stocker le résultat
    SELECT MAX(con_id_concours) INTO dernier_id FROM T_CONCOURS_con;  -- Stocke le résultat dans la variable
    RETURN dernier_id;  -- Retourne la valeur de la variable
END$$

CREATE DEFINER=`e22407333sql`@`%` FUNCTION `donner_categorie` (`id_concours` INT) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE categorie_list TEXT;
    SELECT GROUP_CONCAT(cat.cat_nom_cat SEPARATOR ', ')
    INTO categorie_list
    FROM T_Categorie_cat cat
    JOIN T_CONCOURS_con_has_T_Categorie_cat con_cat ON cat.cat_id_cat = con_cat.cat_id_cat
    WHERE con_cat.con_id_concours = id_concours;
    RETURN categorie_list;
END$$

CREATE DEFINER=`e22407333sql`@`%` FUNCTION `donner_jury` (`id_concours` INT) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE jury_list TEXT;
    SELECT GROUP_CONCAT(CONCAT(prs.prs_nom, ' ', prs.prs_prenom, ' (', jry.jry_domaine_Expertise, ')') SEPARATOR ', ')
    INTO jury_list
    FROM T_Juy_jry jry
    JOIN T_Juy_jry_has_T_CONCOURS_con jry_con ON jry.prs_id_personne = jry_con.jry_id_jury
    JOIN T_Personne_prs prs ON jry.prs_id_personne = prs.prs_id_personne
    WHERE jry_con.con_id_concours = id_concours;
    RETURN jury_list;
END$$

CREATE DEFINER=`e22407333sql`@`%` FUNCTION `get_concours_dates` (`concours_id` INT) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE result VARCHAR(1000) DEFAULT '';
    DECLARE datedeb DATE;
    DECLARE datefinins DATE;
    DECLARE datefinsel DATE;
    DECLARE datefincnr DATE;

    -- Obtenir la date de début du concours
    SELECT con_date_debut INTO datedeb
    FROM T_CONCOURS_con
    WHERE con_id_concours = concours_id;

    -- Calculer les dates des différentes phases
    SELECT DATE_ADD(datedeb, INTERVAL con_tps_candidature DAY) INTO datefinins
    FROM T_CONCOURS_con
    WHERE con_id_concours = concours_id;

    SELECT DATE_ADD(datefinins, INTERVAL con_tps_pre_select DAY) INTO datefinsel
    FROM T_CONCOURS_con
    WHERE con_id_concours = concours_id;

    SELECT DATE_ADD(datefinsel, INTERVAL con_tps_select DAY) INTO datefincnr
    FROM T_CONCOURS_con
    WHERE con_id_concours = concours_id;

    -- Créer la chaîne de résultat avec les dates formatées
 SET result = CONCAT(
    DATE_FORMAT(datedeb, '%Y-%m-%d'), '\n',
    DATE_FORMAT(datefinins, '%Y-%m-%d'), '\n',
    DATE_FORMAT(datefinsel, '%Y-%m-%d'), '\n',
    DATE_FORMAT(datefincnr, '%Y-%m-%d')
);


    RETURN result;
END$$

CREATE DEFINER=`e22407333sql`@`%` FUNCTION `get_organisateur_id` (`id_concours` INT) RETURNS INT(11)  BEGIN
    DECLARE organisateur_id INT;
    SELECT adm_id_admin 
    INTO organisateur_id 
    FROM T_CONCOURS_con
    WHERE con_id_concours = id_concours;
    RETURN organisateur_id;
END$$

CREATE DEFINER=`e22407333sql`@`%` FUNCTION `phase_actuelle` (`idcnr` INT) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN

set @datedeb:=(SELECT con_date_debut from T_CONCOURS_con WHERE con_id_concours=idcnr);
set @datefinins:=(SELECT DATE_ADD(@datedeb, INTERVAL (SELECT con_tps_candidature FROM T_CONCOURS_con WHERE con_id_concours= idcnr) DAY));
set @datefinsel :=(SELECT DATE_ADD(@datefinins, INTERVAL (SELECT con_tps_pre_select FROM T_CONCOURS_con WHERE con_id_concours= idcnr) DAY));

set @datefincnr:= (SELECT DATE_ADD(@datefinsel, INTERVAL (SELECT con_tps_select FROM T_CONCOURS_con WHERE con_id_concours= idcnr) DAY));      
                  
 if (CURRENT_DATE()< @datedeb) THEN
 return 'à venir';
 
 ELSEIF (CURRENT_DATE()>= @datedeb and CURRENT_DATE()<= @datefinins ) THEN
 RETURN 'inscription';

ELSEIF (CURRENT_DATE()> @datefinins and CURRENT_DATE()<= @datefinsel ) THEN
 RETURN 'selection';
 
 ELSEIF (CURRENT_DATE()> @datefinsel and CURRENT_DATE()<= @datefincnr ) THEN
 RETURN 'finale';
 
  ELSEIF (CURRENT_DATE()>  @datefincnr  ) THEN
 RETURN 'terminé';

end if;
  
  
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `T_ACTU_act`
--

CREATE TABLE `T_ACTU_act` (
  `act_id_actu` int(11) NOT NULL,
  `act_titre` varchar(300) NOT NULL,
  `act_date` date NOT NULL,
  `act_description` varchar(300) NOT NULL,
  `actu_etat` tinyint(4) NOT NULL DEFAULT 0,
  `con_id_concours` int(11) NOT NULL,
  `adm_id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `T_ACTU_act`
--

INSERT INTO `T_ACTU_act` (`act_id_actu`, `act_titre`, `act_date`, `act_description`, `actu_etat`, `con_id_concours`, `adm_id_admin`) VALUES
(2, 'Annonce des jurys NanoScape', '2024-12-01', 'Découvrez les membres du jury pour le concours NanoScape.', 1, 51, 78),
(3, 'Art Cellulaire : appel à candidatures', '2024-12-20', 'Participez au concours Art Cellulaire avec vos œuvres innovantes.', 1, 55, 80),
(116, 'Nouveau Concours', '2024-12-05', 'Lumière Quantique a été créé le 2024-11-22', 0, 67, 83),
(117, 'Nouveau Concours', '2024-12-05', 'Lumière Quantique MODIFICATIONS DU CONCOURS => cf récapitulatif des concours !', 0, 67, 83),
(119, 'Nouveau Concours', '2024-12-05', 'Photographi macro a été créé le 2024-12-12', 0, 69, 80),
(120, 'Nouveau Concours', '2024-12-05', 'l\'inspriration dans la photographie a été créé le 2024-12-12', 0, 70, 89);

-- --------------------------------------------------------

--
-- Table structure for table `T_Admin_adm`
--

CREATE TABLE `T_Admin_adm` (
  `prs_id_personne` int(11) NOT NULL,
  `adm_solgan` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `T_Admin_adm`
--

INSERT INTO `T_Admin_adm` (`prs_id_personne`, `adm_solgan`) VALUES
(63, 'MOUAD ADMIN\r\n'),
(76, 'Vive la photographie micro'),
(78, 'Innover et capturer la beauté microscopique'),
(80, 'Explorer la vie cellulaire sous tous ses angles'),
(81, 'Comprendre la magie des formations cristallines'),
(82, 'Révéler les secrets de la microbiologie'),
(83, 'Pionnier dans le domaine des nanotechnologies'),
(89, 'Organisateur principale de la plateforme.');

--
-- Triggers `T_Admin_adm`
--
DELIMITER $$
CREATE TRIGGER `TR_suppression_admin_et_actualites` BEFORE DELETE ON `T_Admin_adm` FOR EACH ROW BEGIN
  -- Définir une variable utilisateur pour ignorer le trigger `after_update_concours`
  SET @is_admin_deletion = TRUE;

  DELETE FROM T_ACTU_act
  WHERE adm_id_admin = OLD.prs_id_personne;

  UPDATE T_CONCOURS_con
  SET adm_id_admin = 1 -- ID de l'admin principal
  WHERE adm_id_admin = OLD.prs_id_personne;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `T_CANDIDATURE_cnd`
--

CREATE TABLE `T_CANDIDATURE_cnd` (
  `cnd_idCANDIDATURE` int(11) NOT NULL,
  `cnd_CODECAND` char(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `cnd_CODEINSCRIT` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `cnd_Nom` varchar(60) NOT NULL,
  `cnd_Prenom` varchar(60) NOT NULL,
  `cnd_Presentation` varchar(300) NOT NULL,
  `cnd_MAIL` varchar(200) NOT NULL,
  `cnd_retenue` tinyint(4) NOT NULL,
  `cnd_etat` varchar(100) NOT NULL,
  `cat_id_cat` int(11) NOT NULL,
  `con_id_concours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `T_CANDIDATURE_cnd`
--

INSERT INTO `T_CANDIDATURE_cnd` (`cnd_idCANDIDATURE`, `cnd_CODECAND`, `cnd_CODEINSCRIT`, `cnd_Nom`, `cnd_Prenom`, `cnd_Presentation`, `cnd_MAIL`, `cnd_retenue`, `cnd_etat`, `cat_id_cat`, `con_id_concours`) VALUES
(4, 'CND00126', 'JKRT5H2N9Q3PXZW1LFMC', 'Dupont', 'Sophie', 'Passionnée par les structures cristallines', 'sophie.dupont@mail.com', 1, 'En attente', 8, 55),
(5, 'CND00127', 'B7GK4M6X2NJQR1WTDPH3', 'Rodriguez', 'Carlos', 'Expert en nanotechnologie et art numérique', 'carlos.rodriguez@mail.com', 0, 'En cours', 10, 57),
(7, 'CND00129', 'A3KF7H2M9X5NQRW1JLBP', 'Garcia', 'Elena', 'Exploration des structures cristallines en géologie', 'elena.garcia@mail.com', 1, 'En attente', 8, 55),
(22, 'CND00099', 'A3KF7H2M9X5NQRW1JLAZ', 'Leroy', 'Pierre', 'Chercheur en nanotechnologie', 'pierre.leroy@centrale.fr', 1, 'En attente', 10, 67),
(23, 'CND00099', 'Y5RB9S2K7MJWN3HXQTXX', 'Schmidt', 'Clara', 'Spécialiste en microscopie', 'clara.schmidt@polytech.de', 1, 'En attente', 5, 67);

--
-- Triggers `T_CANDIDATURE_cnd`
--
DELIMITER $$
CREATE TRIGGER `before_delete_candidature` BEFORE DELETE ON `T_CANDIDATURE_cnd` FOR EACH ROW BEGIN

  DELETE FROM T_Note_not
  WHERE cnd_idCANDIDATURE = OLD.cnd_idCANDIDATURE;
    -- Supprimer les documents associés à la candidature supprimée
    DELETE FROM T_DOCUMENT_doc
    WHERE cnd_idCANDIDATURE = OLD.cnd_idCANDIDATURE;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `T_Categorie_cat`
--

CREATE TABLE `T_Categorie_cat` (
  `cat_id_cat` int(11) NOT NULL,
  `cat_nom_cat` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `T_Categorie_cat`
--

INSERT INTO `T_Categorie_cat` (`cat_id_cat`, `cat_nom_cat`) VALUES
(1, 'Photographie cellulaire'),
(2, 'Macrophotographie'),
(3, 'Photographie électronique'),
(4, 'Imagerie en fluorescence'),
(5, 'Art et science'),
(6, 'Paysages microscopiques'),
(7, 'Structures cellulaires'),
(8, 'Formations cristallines'),
(9, 'Vie microbienne'),
(10, 'Nanotechnologie');

-- --------------------------------------------------------

--
-- Table structure for table `T_CONCOURS_con`
--

CREATE TABLE `T_CONCOURS_con` (
  `con_id_concours` int(11) NOT NULL,
  `con_nom_concours` varchar(60) NOT NULL,
  `con_lieu` varchar(60) NOT NULL,
  `con_date_debut` date NOT NULL,
  `con_tps_candidature` int(11) NOT NULL,
  `con_tps_pre_select` int(11) NOT NULL,
  `con_tps_select` int(11) NOT NULL,
  `con_discipline` varchar(100) NOT NULL,
  `con_image` varchar(200) NOT NULL,
  `adm_id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `T_CONCOURS_con`
--

INSERT INTO `T_CONCOURS_con` (`con_id_concours`, `con_nom_concours`, `con_lieu`, `con_date_debut`, `con_tps_candidature`, `con_tps_pre_select`, `con_tps_select`, `con_discipline`, `con_image`, `adm_id_admin`) VALUES
(51, 'Explorateurs NanoScape', 'Brest', '2024-12-04', 7, 4, 6, 'Nanotechnologies et Imagerie', 'nanoscape2025.jpg', 78),
(55, 'Cristal Scopic', 'Brest', '2024-11-16', 9, 4, 5, 'Cristallographie', 'cristalscopic2025.jpg', 80),
(56, 'Micro-Photographie', 'Brest', '2024-11-17', 6, 3, 7, 'Microbiologie et Photographie', 'microphotographie2025.jpg', 81),
(57, 'NanoArt', 'Brest', '2024-12-04', 5, 5, 5, 'Art et Nanotechnologie', 'nanoart2025.jpg', 82),
(60, 'Art Cellulaire', 'Brest', '2024-11-22', 9, 4, 5, 'Biophotographie', 'artcellulaire2025.jpg', 80),
(67, 'Lumière Quantique', 'Brest', '2024-11-17', 9, 4, 5, 'Optique Quantique', 'lumierequantique2025.jpg', 83),
(69, 'Photographi macro', '', '2024-12-12', 3, 3, 3, 'macro photo', '', 80),
(70, 'l\'inspriration dans la photographie', '', '2024-12-12', 3, 3, 5, 'wwwwwwwwwwwww', '', 89);

--
-- Triggers `T_CONCOURS_con`
--
DELIMITER $$
CREATE TRIGGER `after_update_concours` AFTER UPDATE ON `T_CONCOURS_con` FOR EACH ROW BEGIN
    DECLARE v_texte_actualite VARCHAR(300);
    DECLARE v_ancien_titre VARCHAR(100);
    -- Vérifier si le trigger est exécuté suite à une suppression d'admin
    
    
    
    SET @is_admin_deletion = COALESCE(@is_admin_deletion, FALSE);
    
    
        -- Récupérer l'ancien titre de l'actualité
    SELECT act_titre INTO v_ancien_titre 
    FROM T_ACTU_act 
    WHERE con_id_concours = NEW.con_id_concours 
    ORDER BY act_date DESC 
    LIMIT 1;
    
    
    
    
    IF @is_admin_deletion IS NULL OR @is_admin_deletion = FALSE THEN

        IF OLD.con_nom_concours <> NEW.con_nom_concours AND 
           OLD.con_lieu = NEW.con_lieu AND
           OLD.con_date_debut = NEW.con_date_debut AND
           OLD.con_tps_candidature = NEW.con_tps_candidature AND
           OLD.con_tps_pre_select = NEW.con_tps_pre_select AND
           OLD.con_tps_select = NEW.con_tps_select AND
           OLD.con_discipline = NEW.con_discipline AND
           OLD.con_image = NEW.con_image
           AND OLD.adm_id_admin = NEW.adm_id_admin THEN 

            SET v_texte_actualite = CONCAT('Ancien nom : ', OLD.con_nom_concours, ' - Attention, changement du nom du concours : ', NEW.con_nom_concours);

        ELSE
            SET v_texte_actualite = CONCAT(NEW.con_nom_concours, ' MODIFICATIONS DU CONCOURS => cf récapitulatif des concours !');
        END IF;

               INSERT INTO T_ACTU_act (act_titre, act_date, act_description, con_id_concours, adm_id_admin) 
        VALUES (
            COALESCE(v_ancien_titre, 'Modification concours'), -- Utilise l'ancien titre s'il existe, sinon un titre par défaut
            CURRENT_DATE(), 
            v_texte_actualite, 
            NEW.con_id_concours, 
            NEW.adm_id_admin
        ); 

    END IF;

    -- Réinitialiser la variable après l'exécution
    SET @is_admin_deletion = FALSE;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_ajout_actu_creation_concours` AFTER INSERT ON `T_CONCOURS_con` FOR EACH ROW BEGIN
SET @is_admin_deletion = COALESCE(@is_admin_deletion, FALSE);
    CALL inserer_actu_creation_concours(NEW.con_id_concours);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `T_CONCOURS_con_has_T_Categorie_cat`
--

CREATE TABLE `T_CONCOURS_con_has_T_Categorie_cat` (
  `con_id_concours` int(11) NOT NULL,
  `cat_id_cat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `T_CONCOURS_con_has_T_Categorie_cat`
--

INSERT INTO `T_CONCOURS_con_has_T_Categorie_cat` (`con_id_concours`, `cat_id_cat`) VALUES
(51, 3),
(51, 4),
(55, 1),
(55, 5),
(56, 5),
(57, 2),
(57, 3),
(60, 4),
(67, 2),
(67, 5),
(67, 10),
(69, 7);

-- --------------------------------------------------------

--
-- Table structure for table `T_DOCUMENT_doc`
--

CREATE TABLE `T_DOCUMENT_doc` (
  `doc_idDocument` int(11) NOT NULL,
  `doc_nomDocument` varchar(60) NOT NULL,
  `doc_descriptionDocument` varchar(300) NOT NULL,
  `cnd_idCANDIDATURE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `T_DOCUMENT_doc`
--

INSERT INTO `T_DOCUMENT_doc` (`doc_idDocument`, `doc_nomDocument`, `doc_descriptionDocument`, `cnd_idCANDIDATURE`) VALUES
(5, 'nanotech-art.png', 'Présentation des intersections entre nanotechnologie et expressions artistiques contemporaines', 5),
(13, 'micronan.jpg', 'Une photo microscopique d\'un paysage naturel capturée à l\'échelle nanométrique', 5),
(20, 'quant2.jpg', 'Représentation artistique de l\'intrication quantique', 22),
(21, 'quant0.jpg', 'Visualisation du phénomène de tunneling quantique', 22),
(22, 'flu_quant.jpg', 'Image de points quantiques en fluorescence', 23),
(23, 'quant.jpg', 'Duel quantique', 23);

-- --------------------------------------------------------

--
-- Table structure for table `T_Fil_ACTUALITE_fia`
--

CREATE TABLE `T_Fil_ACTUALITE_fia` (
  `fia_id_Fil_Actu` int(11) NOT NULL,
  `fia_texte_Fil_Actu` varchar(300) NOT NULL,
  `con_id_concours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `T_Juy_jry`
--

CREATE TABLE `T_Juy_jry` (
  `jry_biographie` text NOT NULL,
  `jry_URL` varchar(300) NOT NULL,
  `jry_droit` varchar(60) NOT NULL,
  `jry_domaine_Expertise` varchar(100) NOT NULL,
  `prs_id_personne` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `T_Juy_jry`
--

INSERT INTO `T_Juy_jry` (`jry_biographie`, `jry_URL`, `jry_droit`, `jry_domaine_Expertise`, `prs_id_personne`) VALUES
('bbb', '', '', '', 62),
('ssss', 'wwww', 'wwww', 'wwww', 77),
('Emily Watson est une artiste scientifique qui combine microscopie et art pour sensibiliser à la biodiversité microscopique.', 'https://www.artsciencedigital.org/emily-watson', 'Membre du Jury', 'Art et science', 84),
('Hiroshi Nakamura est un pionnier en fluorescence avancée, travaillant sur des techniques de visualisation des cellules vivantes.', 'https://www.cellfluorescence.jp/hiroshi-nakamura', 'Membre du Jury', 'Imagerie en fluorescence', 85),
('Marco Malo est un passionné de macrophotographie, explorant les motifs complexes des structures biologiques.', 'https://www.bio-macrovision.com/marco.malo', 'Membre du Jury ', 'Macrophotographie', 86),
('Liam Connor est un ingénieur en biophotonique, ayant travaillé sur des projets révolutionnaires de microscopie laser', 'https://www.biophotonics-tech.com/liam-oconnor', 'Membre du Jury', 'Biophotonique et laser', 87),
('Zara Khan est une biologiste visuelle, explorant la vie microscopique avec des techniques de photographie immersives.\', \', \'\', \'', 'https://www.microworld-vision.org/zara-khan', 'Membre du Jury', 'Photographie immersive', 88),
('wwwwwwwwwwwwwww', 'https://obiwan.univ-brest.fr/~e22407333/index.php/admin/creer_compte', 'https://obiwan.univ-brest.fr/~e22407333/index.php/admin/cree', 'https://obiwan.univ-brest.fr/~e22407333/index.php/admin/creer_compte', 97),
('Artiste dans l\'Imagerie Quantique', 'https://www.artsciencedigital.org/thomas.bernard', 'Membre du Jury', 'Imagerie Quantique\'', 99),
('wwwwwwwwwwwwwwwwwwwww', 'https://obiwan.univ-brest.fr/~e22407333/index.php/admin/creer_compte', 'wwwwwwwwwww', 'wwwwwwwwwwww', 100);

-- --------------------------------------------------------

--
-- Table structure for table `T_Juy_jry_has_T_CONCOURS_con`
--

CREATE TABLE `T_Juy_jry_has_T_CONCOURS_con` (
  `jry_id_jury` int(11) NOT NULL,
  `con_id_concours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `T_Juy_jry_has_T_CONCOURS_con`
--

INSERT INTO `T_Juy_jry_has_T_CONCOURS_con` (`jry_id_jury`, `con_id_concours`) VALUES
(84, 57),
(85, 56),
(85, 60),
(86, 51),
(86, 56),
(87, 55),
(87, 69),
(88, 51),
(99, 67),
(100, 55),
(100, 57);

-- --------------------------------------------------------

--
-- Table structure for table `T_MESSAGE_msg`
--

CREATE TABLE `T_MESSAGE_msg` (
  `msg_idMessage` int(11) NOT NULL,
  `msg_texteMessage` varchar(300) NOT NULL,
  `fia_id_Fil_Actu` int(11) NOT NULL,
  `jry_id_jury` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `T_Note_not`
--

CREATE TABLE `T_Note_not` (
  `jry_id_jury` int(11) NOT NULL,
  `cnd_idCANDIDATURE` int(11) NOT NULL,
  `not_NOTE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `T_Personne_prs`
--

CREATE TABLE `T_Personne_prs` (
  `prs_id_personne` int(11) NOT NULL,
  `prs_nom` varchar(60) NOT NULL,
  `prs_prenom` varchar(60) NOT NULL,
  `prs_login` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `prs_MDP` char(64) NOT NULL,
  `prs_profil_actif` tinyint(4) NOT NULL,
  `prs_role` enum('admin','jury') NOT NULL,
  `prs_salt` char(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `T_Personne_prs`
--

INSERT INTO `T_Personne_prs` (`prs_id_personne`, `prs_nom`, `prs_prenom`, `prs_login`, `prs_MDP`, `prs_profil_actif`, `prs_role`, `prs_salt`) VALUES
(62, 'mak', 'ibra', 'makibra', '4d6603ac866679f99fc460ea0e2d8257092c388d207a4fa61900c96215c027e4', 1, 'jury', '3b1d03e3e2420629e29eb6d0a868ea11'),
(63, 'mouad', 'admin', 'mouad_bra', '8bae8feaba7fc1ad50fc7ef6d95557eccf4a9ffc7cfa7dfd4d0dfb61ffe51382', 0, 'admin', '17ed1a9d1ffc38f20b959ea0eadd756d'),
(76, 'Vincent', 'Clark', 'vincent.clark@mail.com', '9a4181f276eca35abda9626ead2b0b560957bce49a56b89dd14f8167cbdc2e26', 1, 'admin', '317f953ea02e4576dbf449f7a7e29364'),
(77, 'Marco', 'Roi', 'marco.roi@mail.com', '8429d11c07470e0283cab5fb1a2fab54a04a31b1a1bb6860df67f055ca04d9c3', 0, 'jury', '9bef0fa88aeb9cbd0d7a92cc87f4b2c5'),
(78, 'Dupont', 'Jean', 'jean.dupont@mail.com', '8baee81513e06d9147471940b3ac03326cd05434c83b2bb6513fc630f9100c06', 1, 'admin', '3595472cf9c07130351bb506fc041a85'),
(80, 'Martin', 'Sophie', 'sophie.martin@mail.com', 'e75143ae388233253f03bd2d40d587768868b38f8aeb68ace307d1bd1f3dbfa4', 1, 'admin', 'fec0b59f2f8f9f4a9b4b2cd4f399db40'),
(81, 'Leclerc', 'Pierre', 'pierre.leclerc@mail.com', 'aab33cf27ce6bc3c680878480b8042abbcd4c6d132e42f93b4d977d09233c2de', 1, 'admin', '355c6e0d126b1fb4b934a06dea1f0a11'),
(82, 'Dubois', 'Marie', 'marie.dubois@mail.com', '79ee47e106472709befb236fadb7ccc9e20564164974430ce638f835edddd64f', 1, 'admin', 'eaf03dd4619181126c27a6f157af4dc6'),
(83, 'Lefevre', 'Lucas', 'lucas.lefevre@mail.com', '2972acad4a972c5d9e405026d69679b6240c53bb2f56e0355ccd19f0a6341389', 1, 'admin', 'b5d49e555421cbcdb8a5be602bb4ed33'),
(84, 'Watson', 'Emily ', 'emily.watson@mail.com', '32af27a0bf324283d7311b10966841faf227005abe8a601767640569fee35d12', 1, 'jury', 'f3a1eb7243dd9f1581059c7a4a55b6b4'),
(85, 'Nakamura', 'Hiroshi ', 'hiroshi.nakamura@mail.com ', 'c045a976dafb5115ba7ee3292beceb5c9ed8c62a7b64588775f6c2d4d87eb66e', 1, 'jury', 'aa97d7fb3b5482f02a3608153c3be469'),
(86, 'Malo', 'Marco', 'marco.malo@mail.com', 'e91b182419b29146c4f72c5984906f7ec5fa88846d8bc1ed4b7fff54a1fa7dd3', 1, 'jury', 'e9064285d4e5b3284fdcc932dcf56430'),
(87, 'Connor', 'Liam ', 'liam.connor@mail.com', '70b008d3baf074b21885805e75c4c2ba6b5c040de4b7ce09553823339cde8abe', 1, 'jury', 'a61141a862ee77b42cff1ff9cc0418fa'),
(88, 'Khan ', 'Zara', 'zara.khan@mail.com', 'a2f5516b551d82c0975dbdb9098642b175514e212ffa8e5b9283176fe62bc438', 1, 'jury', '2edf60f3ff0e84bdbb0a186375217c79'),
(89, 'organisateur', 'principale', 'organisateur', '861c213cbf58deebc3aa23a09b0043c8dfbbde4fad7c118a4a20bab6a6ba411b', 1, 'admin', '8a1d67dc0cca6c6437230df63b6e601d'),
(97, 'gabsy', 'malory', 'malory.gb@mail.com', '783d4f18c52430a6730bca9d1091d0e1e33c85295c1bc4599f2c7fd6297694a8', 1, 'jury', 'a21fed1b8bbc49b8051f2fb1682a2c5c'),
(99, 'Bernard', 'Thomas', 'thomas.bernard@mail.com', 'f372655dcb56c251880ca4fe7a05b4d2da19dce5aafc2d4f981c70dcd83ed084', 1, 'jury', 'e77d4cbeca0b6c4a56fe52ab519c3522'),
(100, 'jury', 'jury', 'vm0412@mail.com', 'a5aa9e5b90486e8ad6e15f8d86125034c03dde18b5859987dcee9ac7e9d686ce', 1, 'jury', '40bbd0b77887936c082387019f81d248');

-- --------------------------------------------------------

--
-- Stand-in structure for view `Vue_Candidatures_Retenues`
-- (See below for the actual view)
--
CREATE TABLE `Vue_Candidatures_Retenues` (
`ID_Candidature` int(11)
,`Nom_Candidat` varchar(60)
,`Prenom_Candidat` varchar(60)
,`Categorie` varchar(60)
,`Nom_Concours` varchar(60)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `Vue_Messages_Actualites`
-- (See below for the actual view)
--
CREATE TABLE `Vue_Messages_Actualites` (
`ID_Message` int(11)
,`Texte_Message` varchar(300)
,`Date_Actualite` date
,`Nom_Concours` varchar(60)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `Vue_Notes_Candidatures`
-- (See below for the actual view)
--
CREATE TABLE `Vue_Notes_Candidatures` (
`ID_Candidature` int(11)
,`Nom_Candidat` varchar(60)
,`Expertise_Jury` varchar(100)
,`Note` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_actualites`
-- (See below for the actual view)
--
CREATE TABLE `v_actualites` (
`act_id_actu` int(11)
,`act_titre` varchar(300)
,`act_date` date
,`act_description` varchar(300)
,`actu_etat` tinyint(4)
,`prs_nom` varchar(60)
);

-- --------------------------------------------------------

--
-- Structure for view `Vue_Candidatures_Retenues`
--
DROP TABLE IF EXISTS `Vue_Candidatures_Retenues`;

CREATE ALGORITHM=UNDEFINED DEFINER=`e22407333sql`@`%` SQL SECURITY DEFINER VIEW `Vue_Candidatures_Retenues`  AS SELECT `T_CANDIDATURE_cnd`.`cnd_idCANDIDATURE` AS `ID_Candidature`, `T_CANDIDATURE_cnd`.`cnd_Nom` AS `Nom_Candidat`, `T_CANDIDATURE_cnd`.`cnd_Prenom` AS `Prenom_Candidat`, `T_Categorie_cat`.`cat_nom_cat` AS `Categorie`, `T_CONCOURS_con`.`con_nom_concours` AS `Nom_Concours` FROM ((`T_CANDIDATURE_cnd` join `T_CONCOURS_con` on(`T_CANDIDATURE_cnd`.`con_id_concours` = `T_CONCOURS_con`.`con_id_concours`)) join `T_Categorie_cat` on(`T_CANDIDATURE_cnd`.`cat_id_cat` = `T_Categorie_cat`.`cat_id_cat`)) WHERE `T_CANDIDATURE_cnd`.`cnd_retenue` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `Vue_Messages_Actualites`
--
DROP TABLE IF EXISTS `Vue_Messages_Actualites`;

CREATE ALGORITHM=UNDEFINED DEFINER=`e22407333sql`@`%` SQL SECURITY DEFINER VIEW `Vue_Messages_Actualites`  AS SELECT `msg`.`msg_idMessage` AS `ID_Message`, `msg`.`msg_texteMessage` AS `Texte_Message`, `act`.`act_date` AS `Date_Actualite`, `con`.`con_nom_concours` AS `Nom_Concours` FROM (((`T_MESSAGE_msg` `msg` join `T_Fil_ACTUALITE_fia` `fia` on(`msg`.`fia_id_Fil_Actu` = `fia`.`fia_id_Fil_Actu`)) join `T_CONCOURS_con` `con` on(`fia`.`con_id_concours` = `con`.`con_id_concours`)) join `T_ACTU_act` `act` on(`act`.`con_id_concours` = `con`.`con_id_concours`)) ;

-- --------------------------------------------------------

--
-- Structure for view `Vue_Notes_Candidatures`
--
DROP TABLE IF EXISTS `Vue_Notes_Candidatures`;

CREATE ALGORITHM=UNDEFINED DEFINER=`e22407333sql`@`%` SQL SECURITY DEFINER VIEW `Vue_Notes_Candidatures`  AS SELECT `T_CANDIDATURE_cnd`.`cnd_idCANDIDATURE` AS `ID_Candidature`, `T_CANDIDATURE_cnd`.`cnd_Nom` AS `Nom_Candidat`, `T_Juy_jry`.`jry_domaine_Expertise` AS `Expertise_Jury`, `T_Note_not`.`not_NOTE` AS `Note` FROM ((`T_Note_not` join `T_CANDIDATURE_cnd` on(`T_Note_not`.`cnd_idCANDIDATURE` = `T_CANDIDATURE_cnd`.`cnd_idCANDIDATURE`)) join `T_Juy_jry` on(`T_Note_not`.`jry_id_jury` = `T_Juy_jry`.`prs_id_personne`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_actualites`
--
DROP TABLE IF EXISTS `v_actualites`;

CREATE ALGORITHM=UNDEFINED DEFINER=`e22407333sql`@`%` SQL SECURITY DEFINER VIEW `v_actualites`  AS SELECT `act`.`act_id_actu` AS `act_id_actu`, `act`.`act_titre` AS `act_titre`, `act`.`act_date` AS `act_date`, `act`.`act_description` AS `act_description`, `act`.`actu_etat` AS `actu_etat`, `prs`.`prs_nom` AS `prs_nom` FROM ((`T_ACTU_act` `act` join `T_Admin_adm` `adm` on(`act`.`adm_id_admin` = `adm`.`prs_id_personne`)) left join `T_Personne_prs` `prs` on(`prs`.`prs_id_personne` = `adm`.`prs_id_personne`)) WHERE `act`.`actu_etat` = 1 ORDER BY `act`.`act_date` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `T_ACTU_act`
--
ALTER TABLE `T_ACTU_act`
  ADD PRIMARY KEY (`act_id_actu`),
  ADD KEY `fk_T ACTU_act_T CONCOURS_con1_idx` (`con_id_concours`),
  ADD KEY `fk_T ACTU_act_T Admin_adm1_idx` (`adm_id_admin`);

--
-- Indexes for table `T_Admin_adm`
--
ALTER TABLE `T_Admin_adm`
  ADD PRIMARY KEY (`prs_id_personne`),
  ADD KEY `fk_T Admin_adm_T Personne_prs1_idx` (`prs_id_personne`);

--
-- Indexes for table `T_CANDIDATURE_cnd`
--
ALTER TABLE `T_CANDIDATURE_cnd`
  ADD PRIMARY KEY (`cnd_idCANDIDATURE`),
  ADD KEY `fk_T CANDIDATURE_cnd_T Categorie_cat1_idx` (`cat_id_cat`),
  ADD KEY `fk_T CANDIDATURE_cnd_T CONCOURS_con1_idx` (`con_id_concours`);

--
-- Indexes for table `T_Categorie_cat`
--
ALTER TABLE `T_Categorie_cat`
  ADD PRIMARY KEY (`cat_id_cat`);

--
-- Indexes for table `T_CONCOURS_con`
--
ALTER TABLE `T_CONCOURS_con`
  ADD PRIMARY KEY (`con_id_concours`),
  ADD KEY `fk_T_CONCOURS_con_T_Admin_adm1_idx` (`adm_id_admin`);

--
-- Indexes for table `T_CONCOURS_con_has_T_Categorie_cat`
--
ALTER TABLE `T_CONCOURS_con_has_T_Categorie_cat`
  ADD PRIMARY KEY (`con_id_concours`,`cat_id_cat`),
  ADD KEY `fk_T CONCOURS_con_has_T Categorie_cat_T Categorie_cat1_idx` (`cat_id_cat`),
  ADD KEY `fk_T CONCOURS_con_has_T Categorie_cat_T CONCOURS_con1_idx` (`con_id_concours`);

--
-- Indexes for table `T_DOCUMENT_doc`
--
ALTER TABLE `T_DOCUMENT_doc`
  ADD PRIMARY KEY (`doc_idDocument`),
  ADD KEY `fk_T DOCUMENT_doc_T CANDIDATURE_cnd1_idx` (`cnd_idCANDIDATURE`);

--
-- Indexes for table `T_Fil_ACTUALITE_fia`
--
ALTER TABLE `T_Fil_ACTUALITE_fia`
  ADD PRIMARY KEY (`fia_id_Fil_Actu`),
  ADD KEY `fk_T Fil_ACTUALITE_fia_T CONCOURS_con1_idx` (`con_id_concours`);

--
-- Indexes for table `T_Juy_jry`
--
ALTER TABLE `T_Juy_jry`
  ADD PRIMARY KEY (`prs_id_personne`),
  ADD KEY `fk_T Jruy_jry_T Personne_prs1_idx` (`prs_id_personne`);

--
-- Indexes for table `T_Juy_jry_has_T_CONCOURS_con`
--
ALTER TABLE `T_Juy_jry_has_T_CONCOURS_con`
  ADD PRIMARY KEY (`jry_id_jury`,`con_id_concours`),
  ADD KEY `fk_T Juy_jry_has_T CONCOURS_con_T CONCOURS_con1_idx` (`con_id_concours`),
  ADD KEY `fk_T Juy_jry_has_T CONCOURS_con_T Juy_jry1_idx` (`jry_id_jury`);

--
-- Indexes for table `T_MESSAGE_msg`
--
ALTER TABLE `T_MESSAGE_msg`
  ADD PRIMARY KEY (`msg_idMessage`),
  ADD KEY `fk_T MESSAGE_msg_T Fil_ACTUALITE_fia1_idx` (`fia_id_Fil_Actu`),
  ADD KEY `fk_T MESSAGE_msg_T_Juy_jry1_idx` (`jry_id_jury`);

--
-- Indexes for table `T_Note_not`
--
ALTER TABLE `T_Note_not`
  ADD PRIMARY KEY (`jry_id_jury`,`cnd_idCANDIDATURE`),
  ADD KEY `fk_T NOTE_T CANDIDATURE_cnd1_idx` (`cnd_idCANDIDATURE`),
  ADD KEY `JJ_idx` (`jry_id_jury`);

--
-- Indexes for table `T_Personne_prs`
--
ALTER TABLE `T_Personne_prs`
  ADD PRIMARY KEY (`prs_id_personne`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `T_ACTU_act`
--
ALTER TABLE `T_ACTU_act`
  MODIFY `act_id_actu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `T_CANDIDATURE_cnd`
--
ALTER TABLE `T_CANDIDATURE_cnd`
  MODIFY `cnd_idCANDIDATURE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `T_Categorie_cat`
--
ALTER TABLE `T_Categorie_cat`
  MODIFY `cat_id_cat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `T_CONCOURS_con`
--
ALTER TABLE `T_CONCOURS_con`
  MODIFY `con_id_concours` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `T_DOCUMENT_doc`
--
ALTER TABLE `T_DOCUMENT_doc`
  MODIFY `doc_idDocument` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `T_Fil_ACTUALITE_fia`
--
ALTER TABLE `T_Fil_ACTUALITE_fia`
  MODIFY `fia_id_Fil_Actu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `T_MESSAGE_msg`
--
ALTER TABLE `T_MESSAGE_msg`
  MODIFY `msg_idMessage` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `T_Personne_prs`
--
ALTER TABLE `T_Personne_prs`
  MODIFY `prs_id_personne` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `T_ACTU_act`
--
ALTER TABLE `T_ACTU_act`
  ADD CONSTRAINT `fk_T ACTU_act_T Admin_adm1` FOREIGN KEY (`adm_id_admin`) REFERENCES `T_Admin_adm` (`prs_id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_T ACTU_act_T CONCOURS_con1` FOREIGN KEY (`con_id_concours`) REFERENCES `T_CONCOURS_con` (`con_id_concours`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `T_Admin_adm`
--
ALTER TABLE `T_Admin_adm`
  ADD CONSTRAINT `fk_T Admin_adm_T Personne_prs1` FOREIGN KEY (`prs_id_personne`) REFERENCES `T_Personne_prs` (`prs_id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `T_CANDIDATURE_cnd`
--
ALTER TABLE `T_CANDIDATURE_cnd`
  ADD CONSTRAINT `fk_T CANDIDATURE_cnd_T CONCOURS_con1` FOREIGN KEY (`con_id_concours`) REFERENCES `T_CONCOURS_con` (`con_id_concours`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_T CANDIDATURE_cnd_T Categorie_cat1` FOREIGN KEY (`cat_id_cat`) REFERENCES `T_Categorie_cat` (`cat_id_cat`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `T_CONCOURS_con`
--
ALTER TABLE `T_CONCOURS_con`
  ADD CONSTRAINT `fk_T_CONCOURS_con_T_Admin_adm1` FOREIGN KEY (`adm_id_admin`) REFERENCES `T_Admin_adm` (`prs_id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `T_CONCOURS_con_has_T_Categorie_cat`
--
ALTER TABLE `T_CONCOURS_con_has_T_Categorie_cat`
  ADD CONSTRAINT `fk_T CONCOURS_con_has_T Categorie_cat_T CONCOURS_con1` FOREIGN KEY (`con_id_concours`) REFERENCES `T_CONCOURS_con` (`con_id_concours`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_T CONCOURS_con_has_T Categorie_cat_T Categorie_cat1` FOREIGN KEY (`cat_id_cat`) REFERENCES `T_Categorie_cat` (`cat_id_cat`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `T_DOCUMENT_doc`
--
ALTER TABLE `T_DOCUMENT_doc`
  ADD CONSTRAINT `fk_T DOCUMENT_doc_T CANDIDATURE_cnd1` FOREIGN KEY (`cnd_idCANDIDATURE`) REFERENCES `T_CANDIDATURE_cnd` (`cnd_idCANDIDATURE`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `T_Fil_ACTUALITE_fia`
--
ALTER TABLE `T_Fil_ACTUALITE_fia`
  ADD CONSTRAINT `fk_T Fil_ACTUALITE_fia_T CONCOURS_con1` FOREIGN KEY (`con_id_concours`) REFERENCES `T_CONCOURS_con` (`con_id_concours`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `T_Juy_jry`
--
ALTER TABLE `T_Juy_jry`
  ADD CONSTRAINT `fk_T Jruy_jry_T Personne_prs1` FOREIGN KEY (`prs_id_personne`) REFERENCES `T_Personne_prs` (`prs_id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `T_Juy_jry_has_T_CONCOURS_con`
--
ALTER TABLE `T_Juy_jry_has_T_CONCOURS_con`
  ADD CONSTRAINT `fk_T Juy_jry_has_T CONCOURS_con_T CONCOURS_con1` FOREIGN KEY (`con_id_concours`) REFERENCES `T_CONCOURS_con` (`con_id_concours`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_T Juy_jry_has_T CONCOURS_con_T Juy_jry1` FOREIGN KEY (`jry_id_jury`) REFERENCES `T_Juy_jry` (`prs_id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `T_MESSAGE_msg`
--
ALTER TABLE `T_MESSAGE_msg`
  ADD CONSTRAINT `fk_T MESSAGE_msg_T Fil_ACTUALITE_fia1` FOREIGN KEY (`fia_id_Fil_Actu`) REFERENCES `T_Fil_ACTUALITE_fia` (`fia_id_Fil_Actu`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_T MESSAGE_msg_T_Juy_jry1` FOREIGN KEY (`jry_id_jury`) REFERENCES `T_Juy_jry` (`prs_id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `T_Note_not`
--
ALTER TABLE `T_Note_not`
  ADD CONSTRAINT `JJ` FOREIGN KEY (`jry_id_jury`) REFERENCES `T_Juy_jry` (`prs_id_personne`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_T NOTE_T CANDIDATURE_cnd1` FOREIGN KEY (`cnd_idCANDIDATURE`) REFERENCES `T_CANDIDATURE_cnd` (`cnd_idCANDIDATURE`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
