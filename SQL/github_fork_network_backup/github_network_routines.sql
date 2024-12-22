-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: github_network
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Temporary view structure for view `ghnd_parent_child_owner_repos_v`
--

DROP TABLE IF EXISTS `ghnd_parent_child_owner_repos_v`;
/*!50001 DROP VIEW IF EXISTS `ghnd_parent_child_owner_repos_v`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ghnd_parent_child_owner_repos_v` AS SELECT 
 1 AS `parent_owner_id`,
 1 AS `parent_source_owner_id`,
 1 AS `parent_login`,
 1 AS `parent_owner_html_url`,
 1 AS `parent_owner_type`,
 1 AS `parent_owner_processed_yn`,
 1 AS `parent_repo_id`,
 1 AS `parent_source_repo_id`,
 1 AS `parent_parent_repo_id`,
 1 AS `parent_name`,
 1 AS `parent_full_name`,
 1 AS `parent_repo_html_url`,
 1 AS `parent_topics`,
 1 AS `parent_created_at`,
 1 AS `parent_updated_at`,
 1 AS `parent_repo_processed_yn`,
 1 AS `child_owner_id`,
 1 AS `child_source_owner_id`,
 1 AS `child_login`,
 1 AS `child_owner_html_url`,
 1 AS `child_owner_type`,
 1 AS `child_owner_processed_yn`,
 1 AS `child_repo_id`,
 1 AS `child_source_repo_id`,
 1 AS `child_parent_repo_id`,
 1 AS `child_name`,
 1 AS `child_full_name`,
 1 AS `child_repo_html_url`,
 1 AS `child_topics`,
 1 AS `child_created_at`,
 1 AS `child_updated_at`,
 1 AS `child_repo_processed_yn`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ghnd_owner_repos_v`
--

DROP TABLE IF EXISTS `ghnd_owner_repos_v`;
/*!50001 DROP VIEW IF EXISTS `ghnd_owner_repos_v`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ghnd_owner_repos_v` AS SELECT 
 1 AS `owner_id`,
 1 AS `source_owner_id`,
 1 AS `login`,
 1 AS `owner_html_url`,
 1 AS `owner_type`,
 1 AS `owner_processed_yn`,
 1 AS `repo_id`,
 1 AS `source_repo_id`,
 1 AS `parent_repo_id`,
 1 AS `name`,
 1 AS `full_name`,
 1 AS `repo_html_url`,
 1 AS `topics`,
 1 AS `created_at`,
 1 AS `updated_at`,
 1 AS `repo_processed_yn`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ghnd_parent_child_max_generation_v`
--

DROP TABLE IF EXISTS `ghnd_parent_child_max_generation_v`;
/*!50001 DROP VIEW IF EXISTS `ghnd_parent_child_max_generation_v`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ghnd_parent_child_max_generation_v` AS SELECT 
 1 AS `repo_id`,
 1 AS `source_repo_id`,
 1 AS `parent_repo_id`,
 1 AS `fork_depth`,
 1 AS `highest_parent_repo_id`,
 1 AS `highest_parent_source_repo_id`,
 1 AS `highest_parent_repo_full_name`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `owner_summ_v`
--

DROP TABLE IF EXISTS `owner_summ_v`;
/*!50001 DROP VIEW IF EXISTS `owner_summ_v`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `owner_summ_v` AS SELECT 
 1 AS `owner_id`,
 1 AS `source_owner_id`,
 1 AS `login`,
 1 AS `owner_html_url`,
 1 AS `owner_type`,
 1 AS `owner_processed_yn`,
 1 AS `num_repos`,
 1 AS `owner_in_degree`,
 1 AS `child_repo_processed_count`,
 1 AS `child_repo_unprocessed_count`,
 1 AS `owner_out_degree`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `parent_repo_count_v`
--

DROP TABLE IF EXISTS `parent_repo_count_v`;
/*!50001 DROP VIEW IF EXISTS `parent_repo_count_v`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `parent_repo_count_v` AS SELECT 
 1 AS `parent_owner_id`,
 1 AS `parent_source_owner_id`,
 1 AS `parent_login`,
 1 AS `parent_owner_html_url`,
 1 AS `parent_owner_type`,
 1 AS `parent_owner_processed_yn`,
 1 AS `parent_repo_id`,
 1 AS `parent_source_repo_id`,
 1 AS `parent_parent_repo_id`,
 1 AS `parent_name`,
 1 AS `parent_full_name`,
 1 AS `parent_repo_html_url`,
 1 AS `parent_topics`,
 1 AS `parent_created_at`,
 1 AS `parent_updated_at`,
 1 AS `parent_repo_processed_yn`,
 1 AS `child_repo_count`,
 1 AS `child_repo_processed_count`,
 1 AS `child_repo_unprocessed_count`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `repo_summ_v`
--

DROP TABLE IF EXISTS `repo_summ_v`;
/*!50001 DROP VIEW IF EXISTS `repo_summ_v`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `repo_summ_v` AS SELECT 
 1 AS `owner_id`,
 1 AS `source_owner_id`,
 1 AS `login`,
 1 AS `owner_html_url`,
 1 AS `owner_type`,
 1 AS `owner_processed_yn`,
 1 AS `repo_id`,
 1 AS `source_repo_id`,
 1 AS `parent_repo_id`,
 1 AS `name`,
 1 AS `full_name`,
 1 AS `repo_html_url`,
 1 AS `topics`,
 1 AS `created_at`,
 1 AS `updated_at`,
 1 AS `repo_processed_yn`,
 1 AS `in_degree`,
 1 AS `child_repo_processed_count`,
 1 AS `child_repo_unprocessed_count`,
 1 AS `out_degree`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `owner_processed_summ_v`
--

DROP TABLE IF EXISTS `owner_processed_summ_v`;
/*!50001 DROP VIEW IF EXISTS `owner_processed_summ_v`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `owner_processed_summ_v` AS SELECT 
 1 AS `processed_owners`,
 1 AS `unprocessed_owners`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ghnd_parent_child_generations_v`
--

DROP TABLE IF EXISTS `ghnd_parent_child_generations_v`;
/*!50001 DROP VIEW IF EXISTS `ghnd_parent_child_generations_v`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ghnd_parent_child_generations_v` AS SELECT 
 1 AS `repo_id`,
 1 AS `source_repo_id`,
 1 AS `parent_repo_id`,
 1 AS `fork_depth`,
 1 AS `highest_parent_repo_id`,
 1 AS `highest_parent_source_repo_id`,
 1 AS `highest_parent_repo_full_name`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `repo_processed_summ_v`
--

DROP TABLE IF EXISTS `repo_processed_summ_v`;
/*!50001 DROP VIEW IF EXISTS `repo_processed_summ_v`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `repo_processed_summ_v` AS SELECT 
 1 AS `processed_repos`,
 1 AS `unprocessed_repos`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `ghnd_parent_child_owner_repos_v`
--

/*!50001 DROP VIEW IF EXISTS `ghnd_parent_child_owner_repos_v`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`github_dev`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ghnd_parent_child_owner_repos_v` AS select `parent_owner_repos`.`owner_id` AS `parent_owner_id`,`parent_owner_repos`.`source_owner_id` AS `parent_source_owner_id`,`parent_owner_repos`.`login` AS `parent_login`,`parent_owner_repos`.`owner_html_url` AS `parent_owner_html_url`,`parent_owner_repos`.`owner_type` AS `parent_owner_type`,`parent_owner_repos`.`owner_processed_yn` AS `parent_owner_processed_yn`,`parent_owner_repos`.`repo_id` AS `parent_repo_id`,`parent_owner_repos`.`source_repo_id` AS `parent_source_repo_id`,`parent_owner_repos`.`parent_repo_id` AS `parent_parent_repo_id`,`parent_owner_repos`.`name` AS `parent_name`,`parent_owner_repos`.`full_name` AS `parent_full_name`,`parent_owner_repos`.`repo_html_url` AS `parent_repo_html_url`,`parent_owner_repos`.`topics` AS `parent_topics`,`parent_owner_repos`.`created_at` AS `parent_created_at`,`parent_owner_repos`.`updated_at` AS `parent_updated_at`,`parent_owner_repos`.`repo_processed_yn` AS `parent_repo_processed_yn`,`child_owner_repos`.`owner_id` AS `child_owner_id`,`child_owner_repos`.`source_owner_id` AS `child_source_owner_id`,`child_owner_repos`.`login` AS `child_login`,`child_owner_repos`.`owner_html_url` AS `child_owner_html_url`,`child_owner_repos`.`owner_type` AS `child_owner_type`,`child_owner_repos`.`owner_processed_yn` AS `child_owner_processed_yn`,`child_owner_repos`.`repo_id` AS `child_repo_id`,`child_owner_repos`.`source_repo_id` AS `child_source_repo_id`,`child_owner_repos`.`parent_repo_id` AS `child_parent_repo_id`,`child_owner_repos`.`name` AS `child_name`,`child_owner_repos`.`full_name` AS `child_full_name`,`child_owner_repos`.`repo_html_url` AS `child_repo_html_url`,`child_owner_repos`.`topics` AS `child_topics`,`child_owner_repos`.`created_at` AS `child_created_at`,`child_owner_repos`.`updated_at` AS `child_updated_at`,`child_owner_repos`.`repo_processed_yn` AS `child_repo_processed_yn` from (`ghnd_owner_repos_v` `parent_owner_repos` join `ghnd_owner_repos_v` `child_owner_repos` on((`parent_owner_repos`.`repo_id` = `child_owner_repos`.`parent_repo_id`))) order by `parent_owner_repos`.`owner_type`,`parent_owner_repos`.`login`,`parent_owner_repos`.`name`,`child_owner_repos`.`owner_type`,`child_owner_repos`.`login`,`child_owner_repos`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ghnd_owner_repos_v`
--

/*!50001 DROP VIEW IF EXISTS `ghnd_owner_repos_v`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`github_dev`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ghnd_owner_repos_v` AS select `ghnd_owners`.`owner_id` AS `owner_id`,`ghnd_owners`.`source_owner_id` AS `source_owner_id`,`ghnd_owners`.`login` AS `login`,`ghnd_owners`.`owner_html_url` AS `owner_html_url`,`ghnd_owners`.`owner_type` AS `owner_type`,`ghnd_owners`.`owner_processed_yn` AS `owner_processed_yn`,`ghnd_repos`.`repo_id` AS `repo_id`,`ghnd_repos`.`source_repo_id` AS `source_repo_id`,`ghnd_repos`.`parent_repo_id` AS `parent_repo_id`,`ghnd_repos`.`name` AS `name`,`ghnd_repos`.`full_name` AS `full_name`,`ghnd_repos`.`repo_html_url` AS `repo_html_url`,`ghnd_repos`.`topics` AS `topics`,`ghnd_repos`.`created_at` AS `created_at`,`ghnd_repos`.`updated_at` AS `updated_at`,`ghnd_repos`.`repo_processed_yn` AS `repo_processed_yn` from (`ghnd_owners` join `ghnd_repos` on((`ghnd_owners`.`owner_id` = `ghnd_repos`.`owner_id`))) order by `ghnd_owners`.`owner_type`,`ghnd_owners`.`login`,`ghnd_repos`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ghnd_parent_child_max_generation_v`
--

/*!50001 DROP VIEW IF EXISTS `ghnd_parent_child_max_generation_v`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`github_dev`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ghnd_parent_child_max_generation_v` AS select `ph`.`repo_id` AS `repo_id`,`ph`.`source_repo_id` AS `source_repo_id`,`ph`.`parent_repo_id` AS `parent_repo_id`,`ph`.`fork_depth` AS `fork_depth`,`ph`.`highest_parent_repo_id` AS `highest_parent_repo_id`,`ph`.`highest_parent_source_repo_id` AS `highest_parent_source_repo_id`,`ph`.`highest_parent_repo_full_name` AS `highest_parent_repo_full_name` from (`ghnd_parent_child_generations_v` `ph` join (select max(`max_dist_summ_repos`.`fork_depth`) AS `max_distance`,`max_dist_summ_repos`.`repo_id` AS `repo_id` from `ghnd_parent_child_generations_v` `max_dist_summ_repos` group by `max_dist_summ_repos`.`repo_id`) `max_dist_child_repos` on(((`max_dist_child_repos`.`repo_id` = `ph`.`repo_id`) and (`max_dist_child_repos`.`max_distance` = `ph`.`fork_depth`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `owner_summ_v`
--

/*!50001 DROP VIEW IF EXISTS `owner_summ_v`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`github_dev`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `owner_summ_v` AS select `repo_summ_v`.`owner_id` AS `owner_id`,`repo_summ_v`.`source_owner_id` AS `source_owner_id`,`repo_summ_v`.`login` AS `login`,`repo_summ_v`.`owner_html_url` AS `owner_html_url`,`repo_summ_v`.`owner_type` AS `owner_type`,`repo_summ_v`.`owner_processed_yn` AS `owner_processed_yn`,count(0) AS `num_repos`,sum(`repo_summ_v`.`in_degree`) AS `owner_in_degree`,sum(`repo_summ_v`.`child_repo_processed_count`) AS `child_repo_processed_count`,sum(`repo_summ_v`.`child_repo_unprocessed_count`) AS `child_repo_unprocessed_count`,sum(`repo_summ_v`.`out_degree`) AS `owner_out_degree` from `repo_summ_v` group by `repo_summ_v`.`owner_id`,`repo_summ_v`.`source_owner_id`,`repo_summ_v`.`login`,`repo_summ_v`.`owner_html_url`,`repo_summ_v`.`owner_type`,`repo_summ_v`.`owner_processed_yn` order by `repo_summ_v`.`owner_type`,`repo_summ_v`.`login` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `parent_repo_count_v`
--

/*!50001 DROP VIEW IF EXISTS `parent_repo_count_v`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`github_dev`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `parent_repo_count_v` AS select `ghnd_parent_child_owner_repos_v`.`parent_owner_id` AS `parent_owner_id`,`ghnd_parent_child_owner_repos_v`.`parent_source_owner_id` AS `parent_source_owner_id`,`ghnd_parent_child_owner_repos_v`.`parent_login` AS `parent_login`,`ghnd_parent_child_owner_repos_v`.`parent_owner_html_url` AS `parent_owner_html_url`,`ghnd_parent_child_owner_repos_v`.`parent_owner_type` AS `parent_owner_type`,`ghnd_parent_child_owner_repos_v`.`parent_owner_processed_yn` AS `parent_owner_processed_yn`,`ghnd_parent_child_owner_repos_v`.`parent_repo_id` AS `parent_repo_id`,`ghnd_parent_child_owner_repos_v`.`parent_source_repo_id` AS `parent_source_repo_id`,`ghnd_parent_child_owner_repos_v`.`parent_parent_repo_id` AS `parent_parent_repo_id`,`ghnd_parent_child_owner_repos_v`.`parent_name` AS `parent_name`,`ghnd_parent_child_owner_repos_v`.`parent_full_name` AS `parent_full_name`,`ghnd_parent_child_owner_repos_v`.`parent_repo_html_url` AS `parent_repo_html_url`,`ghnd_parent_child_owner_repos_v`.`parent_topics` AS `parent_topics`,`ghnd_parent_child_owner_repos_v`.`parent_created_at` AS `parent_created_at`,`ghnd_parent_child_owner_repos_v`.`parent_updated_at` AS `parent_updated_at`,`ghnd_parent_child_owner_repos_v`.`parent_repo_processed_yn` AS `parent_repo_processed_yn`,count(0) AS `child_repo_count`,sum((case when (`ghnd_parent_child_owner_repos_v`.`child_repo_processed_yn` = 1) then 1 else 0 end)) AS `child_repo_processed_count`,sum((case when (`ghnd_parent_child_owner_repos_v`.`child_repo_processed_yn` = 0) then 1 else 0 end)) AS `child_repo_unprocessed_count` from `ghnd_parent_child_owner_repos_v` group by `ghnd_parent_child_owner_repos_v`.`parent_owner_id`,`ghnd_parent_child_owner_repos_v`.`parent_source_owner_id`,`ghnd_parent_child_owner_repos_v`.`parent_login`,`ghnd_parent_child_owner_repos_v`.`parent_owner_html_url`,`ghnd_parent_child_owner_repos_v`.`parent_owner_type`,`ghnd_parent_child_owner_repos_v`.`parent_owner_processed_yn`,`ghnd_parent_child_owner_repos_v`.`parent_repo_id`,`ghnd_parent_child_owner_repos_v`.`parent_source_repo_id`,`ghnd_parent_child_owner_repos_v`.`parent_parent_repo_id`,`ghnd_parent_child_owner_repos_v`.`parent_name`,`ghnd_parent_child_owner_repos_v`.`parent_full_name`,`ghnd_parent_child_owner_repos_v`.`parent_repo_html_url`,`ghnd_parent_child_owner_repos_v`.`parent_topics`,`ghnd_parent_child_owner_repos_v`.`parent_created_at`,`ghnd_parent_child_owner_repos_v`.`parent_updated_at`,`ghnd_parent_child_owner_repos_v`.`parent_repo_processed_yn` order by `ghnd_parent_child_owner_repos_v`.`parent_owner_type`,`ghnd_parent_child_owner_repos_v`.`parent_login` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `repo_summ_v`
--

/*!50001 DROP VIEW IF EXISTS `repo_summ_v`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`github_dev`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `repo_summ_v` AS select `ghnd_owner_repos_v`.`owner_id` AS `owner_id`,`ghnd_owner_repos_v`.`source_owner_id` AS `source_owner_id`,`ghnd_owner_repos_v`.`login` AS `login`,`ghnd_owner_repos_v`.`owner_html_url` AS `owner_html_url`,`ghnd_owner_repos_v`.`owner_type` AS `owner_type`,`ghnd_owner_repos_v`.`owner_processed_yn` AS `owner_processed_yn`,`ghnd_owner_repos_v`.`repo_id` AS `repo_id`,`ghnd_owner_repos_v`.`source_repo_id` AS `source_repo_id`,`ghnd_owner_repos_v`.`parent_repo_id` AS `parent_repo_id`,`ghnd_owner_repos_v`.`name` AS `name`,`ghnd_owner_repos_v`.`full_name` AS `full_name`,`ghnd_owner_repos_v`.`repo_html_url` AS `repo_html_url`,`ghnd_owner_repos_v`.`topics` AS `topics`,`ghnd_owner_repos_v`.`created_at` AS `created_at`,`ghnd_owner_repos_v`.`updated_at` AS `updated_at`,`ghnd_owner_repos_v`.`repo_processed_yn` AS `repo_processed_yn`,`parent_repo_count_v`.`child_repo_count` AS `in_degree`,`parent_repo_count_v`.`child_repo_processed_count` AS `child_repo_processed_count`,`parent_repo_count_v`.`child_repo_unprocessed_count` AS `child_repo_unprocessed_count`,(case when (`ghnd_owner_repos_v`.`parent_repo_id` is not null) then 1 else 0 end) AS `out_degree` from (`ghnd_owner_repos_v` left join `parent_repo_count_v` on((`ghnd_owner_repos_v`.`repo_id` = `parent_repo_count_v`.`parent_repo_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `owner_processed_summ_v`
--

/*!50001 DROP VIEW IF EXISTS `owner_processed_summ_v`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`github_dev`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `owner_processed_summ_v` AS select sum((case when (`ghnd_owners`.`owner_processed_yn` = 1) then 1 else 0 end)) AS `processed_owners`,sum((case when (`ghnd_owners`.`owner_processed_yn` = 0) then 1 else 0 end)) AS `unprocessed_owners` from `ghnd_owners` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ghnd_parent_child_generations_v`
--

/*!50001 DROP VIEW IF EXISTS `ghnd_parent_child_generations_v`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`github_dev`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ghnd_parent_child_generations_v` AS with recursive `parenthierarchy` as (select `ghnd_repos`.`repo_id` AS `repo_id`,`ghnd_repos`.`source_repo_id` AS `source_repo_id`,`ghnd_repos`.`parent_repo_id` AS `parent_repo_id`,0 AS `fork_depth`,`ghnd_repos`.`repo_id` AS `highest_parent_repo_id`,`ghnd_repos`.`source_repo_id` AS `highest_parent_source_repo_id`,`ghnd_repos`.`full_name` AS `highest_parent_repo_full_name` from `ghnd_repos` where (`ghnd_repos`.`parent_repo_id` is null) union all select `n`.`repo_id` AS `repo_id`,`n`.`source_repo_id` AS `source_repo_id`,`n`.`parent_repo_id` AS `parent_repo_id`,(`ph`.`fork_depth` + 1) AS `fork_depth`,`ph`.`highest_parent_repo_id` AS `highest_parent_repo_id`,`ph`.`highest_parent_source_repo_id` AS `highest_parent_source_repo_id`,`ph`.`highest_parent_repo_full_name` AS `highest_parent_repo_full_name` from (`ghnd_repos` `n` join `parenthierarchy` `ph` on((`n`.`parent_repo_id` = `ph`.`repo_id`)))) select `parenthierarchy`.`repo_id` AS `repo_id`,`parenthierarchy`.`source_repo_id` AS `source_repo_id`,`parenthierarchy`.`parent_repo_id` AS `parent_repo_id`,`parenthierarchy`.`fork_depth` AS `fork_depth`,`parenthierarchy`.`highest_parent_repo_id` AS `highest_parent_repo_id`,`parenthierarchy`.`highest_parent_source_repo_id` AS `highest_parent_source_repo_id`,`parenthierarchy`.`highest_parent_repo_full_name` AS `highest_parent_repo_full_name` from `parenthierarchy` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `repo_processed_summ_v`
--

/*!50001 DROP VIEW IF EXISTS `repo_processed_summ_v`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`github_dev`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `repo_processed_summ_v` AS select sum((case when (`ghnd_repos`.`repo_processed_yn` = 1) then 1 else 0 end)) AS `processed_repos`,sum((case when (`ghnd_repos`.`repo_processed_yn` = 0) then 1 else 0 end)) AS `unprocessed_repos` from `ghnd_repos` */;
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

-- Dump completed on 2024-12-22  8:56:22
