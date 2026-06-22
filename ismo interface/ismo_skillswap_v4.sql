[sudo] password for is4ko: -- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: ismo_skillswap_v4
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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

--
-- Table structure for table `badges`
--

DROP TABLE IF EXISTS `badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `points_requis` int(11) NOT NULL DEFAULT 0,
  `est_actif` tinyint(1) NOT NULL DEFAULT 1,
  `creee_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badges`
--

LOCK TABLES `badges` WRITE;
/*!40000 ALTER TABLE `badges` DISABLE KEYS */;
INSERT INTO `badges` VALUES (1,'Débutant','Premiers pas sur la plateforme',NULL,NULL,10,1,'2026-06-05 01:11:39'),(2,'Apprenti','Vous progressez',NULL,NULL,30,1,'2026-06-05 01:11:39'),(3,'Expert','Expert en compétences',NULL,NULL,60,1,'2026-06-05 01:11:39'),(4,'Mentor','Membre actif de la communauté ayant été approuvé en tant que mentor',NULL,NULL,0,1,'2026-06-05 01:11:39');
/*!40000 ALTER TABLE `badges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `badges_stagiaire`
--

DROP TABLE IF EXISTS `badges_stagiaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badges_stagiaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `attribue_par` varchar(100) NOT NULL DEFAULT 'système',
  `motif` text DEFAULT NULL,
  `obtenu_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_utilisateur_badge` (`utilisateur_id`,`badge_id`),
  KEY `badge_id` (`badge_id`),
  KEY `idx_utilisateur` (`utilisateur_id`),
  KEY `idx_user_date` (`utilisateur_id`,`obtenu_le`),
  CONSTRAINT `badges_stagiaire_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `badges_stagiaire_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badges_stagiaire`
--

LOCK TABLES `badges_stagiaire` WRITE;
/*!40000 ALTER TABLE `badges_stagiaire` DISABLE KEYS */;
INSERT INTO `badges_stagiaire` VALUES (1,1,1,'système','Badge débloqué automatiquement','2026-06-07 12:54:32'),(2,1,2,'système','Badge débloqué automatiquement','2026-06-07 12:54:32'),(3,2,1,'système','Badge débloqué automatiquement','2026-06-07 12:54:32'),(4,1,3,'système','Badge débloqué automatiquement','2026-06-14 00:52:55'),(5,6,4,'4',NULL,'2026-06-17 15:11:39'),(6,6,1,'système','Badge débloqué automatiquement','2026-06-17 15:11:39'),(7,6,2,'système','Badge débloqué automatiquement','2026-06-17 15:11:39'),(8,6,3,'système','Badge débloqué automatiquement','2026-06-17 15:11:39');
/*!40000 ALTER TABLE `badges_stagiaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `competences_catalogue`
--

DROP TABLE IF EXISTS `competences_catalogue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competences_catalogue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) NOT NULL,
  `categorie` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `niveau_difficulte` enum('Débutant','Intermédiaire','Avancé','Expert') NOT NULL DEFAULT 'Débutant',
  `est_active` tinyint(1) NOT NULL DEFAULT 1,
  `nb_utilisations` int(11) NOT NULL DEFAULT 0,
  `creee_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_actif_nom` (`est_active`,`nom`),
  KEY `idx_categorie_nom` (`categorie`,`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competences_catalogue`
--

LOCK TABLES `competences_catalogue` WRITE;
/*!40000 ALTER TABLE `competences_catalogue` DISABLE KEYS */;
INSERT INTO `competences_catalogue` VALUES (1,'PHP','Développement Web','Langage de programmation serveur','Avancé',1,0,'2026-06-05 01:11:39'),(2,'JavaScript','Développement Web','Langage de programmation frontend','Intermédiaire',1,1,'2026-06-05 01:11:39'),(3,'Python','Data Science','Langage de programmation polyvalent','Avancé',1,0,'2026-06-05 01:11:39'),(4,'SQL','Base de données','Langage de requêtes structurées','Intermédiaire',1,0,'2026-06-05 01:11:39'),(5,'HTML/CSS','Développement Web','Langages de structuration et style','Débutant',1,0,'2026-06-05 01:11:39'),(6,'React','Développement Web','Bibliothèque frontend','Avancé',1,0,'2026-06-05 01:11:39'),(7,'Machine Learning','Data Science','Apprentissage automatique','Expert',1,0,'2026-06-05 01:11:39');
/*!40000 ALTER TABLE `competences_catalogue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `competences_stagiaire`
--

DROP TABLE IF EXISTS `competences_stagiaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competences_stagiaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL,
  `niveau_estime` varchar(50) NOT NULL DEFAULT 'Débutant',
  `statut_validation` enum('En attente','Validé','Refusé') NOT NULL DEFAULT 'En attente',
  `date_declaration` datetime NOT NULL DEFAULT current_timestamp(),
  `validateur_id` int(11) DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `motif_refus` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_utilisateur_competence` (`utilisateur_id`,`competence_id`),
  KEY `competence_id` (`competence_id`),
  KEY `validateur_id` (`validateur_id`),
  KEY `idx_utilisateur` (`utilisateur_id`),
  KEY `idx_statut` (`statut_validation`),
  KEY `idx_user_statut_date` (`utilisateur_id`,`statut_validation`,`date_declaration`),
  KEY `idx_statut_date` (`statut_validation`,`date_declaration`),
  CONSTRAINT `competences_stagiaire_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `competences_stagiaire_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competences_catalogue` (`id`) ON DELETE CASCADE,
  CONSTRAINT `competences_stagiaire_ibfk_3` FOREIGN KEY (`validateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competences_stagiaire`
--

LOCK TABLES `competences_stagiaire` WRITE;
/*!40000 ALTER TABLE `competences_stagiaire` DISABLE KEYS */;
INSERT INTO `competences_stagiaire` VALUES (1,1,1,'Avancé','Validé','2026-06-05 01:11:39',3,'2026-06-05 01:11:39',NULL),(2,1,2,'Intermédiaire','Validé','2026-06-05 01:11:39',3,'2026-06-05 01:11:39',NULL),(3,1,5,'Expert','Validé','2026-06-05 01:11:39',3,'2026-06-05 01:11:39',NULL),(4,2,3,'Avancé','Validé','2026-06-05 01:11:39',3,'2026-06-05 01:11:39',NULL),(5,2,7,'Débutant','En attente','2026-06-05 01:11:39',NULL,NULL,NULL),(6,5,5,'Débutant','En attente','2026-06-17 13:33:21',NULL,NULL,NULL),(7,5,3,'Débutant','En attente','2026-06-17 13:33:30',NULL,NULL,NULL),(8,5,1,'Avancé','En attente','2026-06-17 13:48:39',NULL,NULL,NULL),(9,5,2,'Intermédiaire','En attente','2026-06-17 13:53:38',NULL,NULL,NULL);
/*!40000 ALTER TABLE `competences_stagiaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `demandes_aide`
--

DROP TABLE IF EXISTS `demandes_aide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `demandes_aide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auteur_id` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `urgence` varchar(20) DEFAULT 'Moyenne',
  `statut` enum('Ouvert','En cours','Résolu','Fermé') NOT NULL DEFAULT 'Ouvert',
  `creee_le` datetime NOT NULL DEFAULT current_timestamp(),
  `mentor_id` int(11) DEFAULT NULL,
  `date_resolution` datetime DEFAULT NULL,
  `note_mentor` tinyint(4) DEFAULT NULL,
  `commentaire_mentor` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_auteur` (`auteur_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_competence` (`competence_id`),
  KEY `idx_statut_date` (`statut`,`creee_le`),
  KEY `idx_auteur_statut_date` (`auteur_id`,`statut`,`creee_le`),
  KEY `idx_mentor_statut` (`mentor_id`,`statut`),
  KEY `idx_competence_statut` (`competence_id`,`statut`),
  CONSTRAINT `demandes_aide_ibfk_1` FOREIGN KEY (`auteur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `demandes_aide_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competences_catalogue` (`id`) ON DELETE CASCADE,
  CONSTRAINT `demandes_aide_ibfk_3` FOREIGN KEY (`mentor_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demandes_aide`
--

LOCK TABLES `demandes_aide` WRITE;
/*!40000 ALTER TABLE `demandes_aide` DISABLE KEYS */;
INSERT INTO `demandes_aide` VALUES (1,2,1,'Aide projet PHP','Je bloque sur un projet PHP','Moyenne','Ouvert','2026-06-05 01:11:39',NULL,NULL,NULL,NULL),(2,1,3,'Débuter Python','Je veux apprendre Python','Moyenne','Ouvert','2026-06-05 01:11:39',NULL,NULL,NULL,NULL),(3,2,5,'CSS Grid','Problème avec CSS Grid layout','Moyenne','Ouvert','2026-06-05 01:11:39',NULL,NULL,NULL,NULL),(4,4,1,'Test demande from curl','Testing the API','Moyenne','Ouvert','2026-06-14 00:25:37',NULL,NULL,NULL,NULL),(5,4,1,'Test demande from curl','Testing the API','Moyenne','Ouvert','2026-06-14 00:26:22',NULL,NULL,NULL,NULL),(6,5,5,'test','test','Moyenne','Ouvert','2026-06-14 00:27:15',NULL,NULL,NULL,NULL),(7,5,5,'test33','test','Moyenne','Résolu','2026-06-14 00:27:47',1,'2026-06-14 00:52:55',5,'merci');
/*!40000 ALTER TABLE `demandes_aide` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proposition_id` int(11) NOT NULL,
  `note` tinyint(4) NOT NULL CHECK (`note` between 1 and 5),
  `commentaire` text DEFAULT NULL,
  `creee_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_proposition` (`proposition_id`),
  CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`proposition_id`) REFERENCES `propositions_aide` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluations`
--

LOCK TABLES `evaluations` WRITE;
/*!40000 ALTER TABLE `evaluations` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mentor_applications`
--

DROP TABLE IF EXISTS `mentor_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mentor_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `motivation` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `statut` enum('En attente','Approuvé','Refusé') DEFAULT 'En attente',
  `date_soumission` datetime DEFAULT current_timestamp(),
  `date_traitement` datetime DEFAULT NULL,
  `traite_par_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_statut_date` (`statut`,`date_soumission`),
  KEY `idx_user_statut` (`utilisateur_id`,`statut`),
  CONSTRAINT `mentor_applications_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mentor_applications`
--

LOCK TABLES `mentor_applications` WRITE;
/*!40000 ALTER TABLE `mentor_applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `mentor_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `notification_type` varchar(50) NOT NULL DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notification_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_user_read_date` (`user_id`,`is_read`,`created_at`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,3,'nouvelle_demande','Nouvelle demande d\'aide','Demande publiée : Test demande from curl (PHP)','demande',5,0,NULL,'2026-06-14 00:26:22'),(2,4,'nouvelle_demande','Nouvelle demande d\'aide','Demande publiée : Test demande from curl (PHP)','demande',5,1,'2026-06-19 19:02:22','2026-06-14 00:26:22'),(3,3,'nouvelle_demande','Nouvelle demande d\'aide','Demande publiée : test (HTML/CSS)','demande',6,0,NULL,'2026-06-14 00:27:15'),(4,4,'nouvelle_demande','Nouvelle demande d\'aide','Demande publiée : test (HTML/CSS)','demande',6,1,'2026-06-19 19:02:22','2026-06-14 00:27:15'),(5,3,'nouvelle_demande','Nouvelle demande d\'aide','Demande publiée : test33 (HTML/CSS)','demande',7,0,NULL,'2026-06-14 00:27:48'),(6,4,'nouvelle_demande','Nouvelle demande d\'aide','Demande publiée : test33 (HTML/CSS)','demande',7,1,'2026-06-19 19:02:22','2026-06-14 00:27:48'),(7,5,'proposition','Nouvelle proposition','Quelqu\'un a proposé son aide sur votre demande','proposition',3,1,'2026-06-17 13:54:06','2026-06-14 00:51:10'),(8,1,'acceptee','Proposition acceptée','Votre aide a été acceptée ! Consultez la demande.','proposition',3,1,'2026-06-17 11:49:21','2026-06-14 00:52:45'),(9,5,'acceptee','Proposition acceptée','Vous avez accepté un mentor pour votre demande','proposition',3,1,'2026-06-17 13:54:06','2026-06-14 00:52:45'),(10,1,'badge','Badge débloqué !','Vous avez atteint 60 points et débloqué le badge « Expert »','badge',3,1,'2026-06-17 11:49:21','2026-06-14 00:52:55'),(11,5,'resolu','Demande résolue','Votre demande a été marquée comme résolue','demande',7,1,'2026-06-17 13:54:06','2026-06-14 00:52:55'),(12,1,'resolu','Aide terminée','L\'aide que vous avez fournie a été marquée comme terminée','demande',7,1,'2026-06-17 11:49:21','2026-06-14 00:52:55'),(13,4,'proposition','Nouvelle proposition','Quelqu\'un a proposé son aide sur votre demande','proposition',4,1,'2026-06-19 19:02:22','2026-06-14 17:01:53'),(15,6,'compte_active','Compte activé','Votre compte a été activé par un administrateur.','user',6,0,NULL,'2026-06-16 15:10:58'),(16,3,'declaration','Nouvelle déclaration','JavaScript déclarée en attente de validation','competence',0,0,NULL,'2026-06-17 13:53:38'),(17,4,'declaration','Nouvelle déclaration','JavaScript déclarée en attente de validation','competence',0,1,'2026-06-19 19:02:17','2026-06-17 13:53:38'),(18,6,'badge','Badge débloqué !','Vous avez atteint 10 points et débloqué le badge « Débutant »','badge',1,0,NULL,'2026-06-17 15:11:39'),(19,6,'badge','Badge débloqué !','Vous avez atteint 30 points et débloqué le badge « Apprenti »','badge',2,0,NULL,'2026-06-17 15:11:39'),(20,6,'badge','Badge débloqué !','Vous avez atteint 60 points et débloqué le badge « Expert »','badge',3,0,NULL,'2026-06-17 15:11:39'),(21,6,'badge','Badge débloqué !','Vous avez reçu le badge « Mentor »','badge',4,0,NULL,'2026-06-17 15:11:39'),(22,4,'proposition','Nouvelle proposition','Quelqu\'un a proposé son aide sur votre demande','proposition',5,0,NULL,'2026-06-19 19:23:40');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `propositions_aide`
--

DROP TABLE IF EXISTS `propositions_aide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `propositions_aide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `demande_id` int(11) NOT NULL,
  `proposant_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `statut` enum('En attente','Acceptée','Refusée') NOT NULL DEFAULT 'En attente',
  `creee_le` datetime NOT NULL DEFAULT current_timestamp(),
  `date_traitement` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_demande_proposant` (`demande_id`,`proposant_id`),
  KEY `idx_demande` (`demande_id`),
  KEY `idx_proposant` (`proposant_id`),
  KEY `idx_proposant_statut_date` (`proposant_id`,`statut`,`creee_le`),
  KEY `idx_demande_statut` (`demande_id`,`statut`),
  CONSTRAINT `propositions_aide_ibfk_1` FOREIGN KEY (`demande_id`) REFERENCES `demandes_aide` (`id`) ON DELETE CASCADE,
  CONSTRAINT `propositions_aide_ibfk_2` FOREIGN KEY (`proposant_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `propositions_aide`
--

LOCK TABLES `propositions_aide` WRITE;
/*!40000 ALTER TABLE `propositions_aide` DISABLE KEYS */;
INSERT INTO `propositions_aide` VALUES (1,1,1,'Je peux t\'aider, j\'ai 3 ans d\'expérience en PHP','En attente','2026-06-05 01:11:39',NULL),(2,3,1,'Je maîtrise CSS Grid, je te propose mon aide','En attente','2026-06-05 01:11:39',NULL),(3,7,1,'Je peux vous aider avec ce problème, voici ma solution détaillée étape par étape.','Acceptée','2026-06-14 00:51:10','2026-06-14 00:52:45'),(4,5,5,'tu dois','En attente','2026-06-14 17:01:53',NULL),(5,4,5,'ssss','En attente','2026-06-19 19:23:40',NULL);
/*!40000 ALTER TABLE `propositions_aide` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_cases`
--

DROP TABLE IF EXISTS `service_cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_cases` (
  `case_id` int(11) NOT NULL AUTO_INCREMENT,
  `case_type` varchar(50) NOT NULL DEFAULT 'support',
  `created_by_user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `target_content_type` varchar(50) DEFAULT NULL,
  `target_content_id` int(11) DEFAULT NULL,
  `status` enum('open','in_progress','resolved','closed','dismissed') NOT NULL DEFAULT 'open',
  `priority` varchar(20) DEFAULT NULL,
  `severity_level` varchar(20) DEFAULT NULL,
  `assigned_to_user_id` int(11) DEFAULT NULL,
  `action_taken` text DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `reviewed_by_user_id` int(11) DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`case_id`),
  KEY `idx_case_type` (`case_type`),
  KEY `idx_created_by` (`created_by_user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_target_user` (`target_user_id`),
  KEY `idx_assigned_to` (`assigned_to_user_id`),
  CONSTRAINT `service_cases_ibfk_1` FOREIGN KEY (`created_by_user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_cases_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_cases_ibfk_3` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_cases`
--

LOCK TABLES `service_cases` WRITE;
/*!40000 ALTER TABLE `service_cases` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_cases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `case_replies`
--

DROP TABLE IF EXISTS `case_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_staff` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_case` (`case_id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `case_replies_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `service_cases` (`case_id`) ON DELETE CASCADE,
  CONSTRAINT `case_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `case_replies`
--

LOCK TABLES `case_replies` WRITE;
/*!40000 ALTER TABLE `case_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `case_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('stagiaire','formateur','administrateur','mentor') NOT NULL DEFAULT 'stagiaire',
  `filiere` varchar(100) DEFAULT NULL,
  `photo` varchar(255) NOT NULL DEFAULT '/assets/images/default_avatar.svg',
  `bio` text DEFAULT NULL,
  `points_gamification` int(11) NOT NULL DEFAULT 0,
  `derniere_connexion` datetime DEFAULT NULL,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `est_actif` tinyint(1) NOT NULL DEFAULT 1,
  `disponible` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_email` (`email`),
  KEY `idx_actif_date` (`est_actif`,`date_inscription`),
  KEY `idx_role_actif` (`role`,`est_actif`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES (1,'Dupont','Jean','jean@test.com','$2y$12$X0hFqFCcgToG63S7HDdLpe2gsgj0XRc8VesHfhzEIs3ZrGzXMZ1Se','stagiaire','Développement Web','/assets/images/default_avatar.svg',NULL,60,'2026-06-19 19:10:33','2026-06-05 01:11:39',1,1),(2,'Martin','Sophie','sophie@test.com','$2y$12$X0hFqFCcgToG63S7HDdLpe2gsgj0XRc8VesHfhzEIs3ZrGzXMZ1Se','stagiaire','Data Science','/uploads/avatars/avatar_2_1780832241.WEBP','',10,'2026-06-17 11:49:32','2026-06-05 01:11:39',1,1),(3,'Leroy','Pierre','pierre@test.com','$2y$12$X0hFqFCcgToG63S7HDdLpe2gsgj0XRc8VesHfhzEIs3ZrGzXMZ1Se','formateur',NULL,'/assets/images/default_avatar.svg',NULL,0,'2026-06-19 19:26:43','2026-06-05 01:11:39',1,1),(4,'Admin','Admin','admin@test.com','$2y$12$X0hFqFCcgToG63S7HDdLpe2gsgj0XRc8VesHfhzEIs3ZrGzXMZ1Se','administrateur',NULL,'/assets/images/default_avatar.svg',NULL,0,'2026-06-19 19:07:47','2026-06-05 01:11:39',1,1),(5,'Bernard','Lucas','lucas@test.com','$2y$12$Y9loj7lwFMerjTdWUpsrCuCZhuGA5ZBFZgQs7O8MbhIMzHdyw8kJy','mentor','Informatique','/assets/images/default_avatar.svg',NULL,5,'2026-06-19 19:23:22','2026-06-13 21:46:34',1,1),(6,'mohamed','raiess','raiessmohamed@test.com','$2y$12$gktqFx7lcBx0kyNdPXVWqeHMVyyUVgCe8mFeixwk8A48ECNWqQtc6','stagiaire','DEV','/assets/images/default_avatar.svg',NULL,100,'2026-06-16 15:11:24','2026-06-16 15:10:25',1,1);
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-19 21:12:43
