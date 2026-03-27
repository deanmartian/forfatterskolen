-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: forfatterskolen
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `course_groups`
--

LOCK TABLES `course_groups` WRITE;
/*!40000 ALTER TABLE `course_groups` DISABLE KEYS */;
INSERT INTO `course_groups` (`id`, `name`, `description`, `icon`, `created_at`, `updated_at`) VALUES ('019d2e64-bdb3-72fd-a74a-b70854214292','Årskurs 2026','Kursgruppe for Årskurs 2026','📚','2026-03-27 09:24:07','2026-03-27 09:24:07'),('019d2e64-be2b-70f2-b2cb-d9af319265cd','Påbyggingsår 2026','Kursgruppe for Påbyggingsår 2026','📚','2026-03-27 09:24:07','2026-03-27 09:24:07'),('019d2e64-be31-73b3-87af-cbc5a900ac6b','Romankurs i gruppe - oppstart 20.04.2026','Kursgruppe for Romankurs i gruppe - oppstart 20.04.2026','📚','2026-03-27 09:24:07','2026-03-27 09:24:07'),('019d2e64-be37-730f-91d7-0c963f66e4dc','Barnebokkurs med Gro Dahle – Oppstart 16.02.2026','Kursgruppe for Barnebokkurs med Gro Dahle – Oppstart 16.02.2026','📚','2026-03-27 09:24:07','2026-03-27 09:24:07'),('019d2e64-be3c-71af-b645-73c72ac88e3a','Mentormøter','Kursgruppe for Mentormøter','📚','2026-03-27 09:24:07','2026-03-27 09:24:07'),('019d2e64-be42-7209-b02e-1125d18c874c','Årskurs – Høst 2025','Kursgruppe for Årskurs – Høst 2025','📚','2026-03-27 09:24:07','2026-03-27 09:24:07'),('019d2e64-f993-720a-abf1-a94416bcc1f7','Gro Dahle – Dramaturgi og dialog (oppstart 22.09.2025)','Kursgruppe for Gro Dahle – Dramaturgi og dialog (oppstart 22.09.2025)','📚','2026-03-27 09:24:22','2026-03-27 09:24:22'),('019d2e64-f9b9-7275-80ec-06ec6d9468d7','Lær å skrive feelgoodromaner 27.10.2025','Kursgruppe for Lær å skrive feelgoodromaner 27.10.2025','📚','2026-03-27 09:24:22','2026-03-27 09:24:22');
/*!40000 ALTER TABLE `course_groups` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-27  8:24:32
