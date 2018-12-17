
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES cp1251 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `erp_account_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_account_entry` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_d` int(11) NOT NULL,
  `acc_c` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `document_date` date DEFAULT NULL,
  PRIMARY KEY (`entry_id`),
  KEY `document_id` (`document_id`),
  KEY `created` (`document_date`)
) ENGINE=MyISAM AUTO_INCREMENT=237 DEFAULT CHARSET=utf8 COMMENT='Бухгалтерские  проводки';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_account_entry_view`;
/*!50001 DROP VIEW IF EXISTS `erp_account_entry_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_account_entry_view` AS SELECT 
 1 AS `entry_id`,
 1 AS `acc_d`,
 1 AS `acc_c`,
 1 AS `amount`,
 1 AS `document_id`,
 1 AS `document_number`,
 1 AS `meta_desc`,
 1 AS `meta_name`,
 1 AS `document_date`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_account_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_account_plan` (
  `acc_code` int(16) NOT NULL,
  `acc_name` varchar(255) NOT NULL,
  `acc_pid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`acc_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='План счетов';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_account_subconto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_account_subconto` (
  `subconto_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `document_date` date NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `employee_id` int(11) NOT NULL DEFAULT '0',
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `extcode` int(11) NOT NULL DEFAULT '0',
  `stock_id` int(11) NOT NULL DEFAULT '0',
  `moneyfund_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`subconto_id`),
  KEY `document_id` (`document_id`),
  KEY `document_date` (`document_date`),
  KEY `account_id` (`account_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM AUTO_INCREMENT=327 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_account_subconto_view`;
/*!50001 DROP VIEW IF EXISTS `erp_account_subconto_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_account_subconto_view` AS SELECT 
 1 AS `subconto_id`,
 1 AS `account_id`,
 1 AS `document_id`,
 1 AS `document_date`,
 1 AS `amount`,
 1 AS `quantity`,
 1 AS `customer_id`,
 1 AS `employee_id`,
 1 AS `asset_id`,
 1 AS `extcode`,
 1 AS `stock_id`,
 1 AS `moneyfund_id`,
 1 AS `document_number`,
 1 AS `meta_desc`,
 1 AS `meta_name`,
 1 AS `customer_name`,
 1 AS `employee_name`,
 1 AS `moneyfundname`,
 1 AS `osname`,
 1 AS `itemname`,
 1 AS `partion`,
 1 AS `storename`,
 1 AS `item_id`,
 1 AS `store_id`,
 1 AS `da`,
 1 AS `ca`,
 1 AS `dq`,
 1 AS `cq`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_bank` (
  `bank_id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(255) NOT NULL,
  `detail` text NOT NULL,
  PRIMARY KEY (`bank_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Справочник  банков';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_contact` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(64) NOT NULL,
  `middlename` varchar(64) DEFAULT NULL,
  `lastname` varchar(64) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  `detail` text NOT NULL,
  `description` text,
  `customer_id` int(11) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_contact_view`;
/*!50001 DROP VIEW IF EXISTS `erp_contact_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_contact_view` AS SELECT 
 1 AS `contact_id`,
 1 AS `firstname`,
 1 AS `middlename`,
 1 AS `lastname`,
 1 AS `fullname`,
 1 AS `email`,
 1 AS `phone`,
 1 AS `detail`,
 1 AS `customer`,
 1 AS `description`,
 1 AS `customer_name`,
 1 AS `cust_type`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) DEFAULT NULL,
  `detail` text NOT NULL,
  `contact_id` int(11) DEFAULT '0' COMMENT '>0 - С„РёР·Р»РёС†Рѕ ( СЃСЃС‹Р»РєР°  РЅР°  РєРѕРЅС‚Р°РєС‚)',
  `cust_type` int(1) NOT NULL DEFAULT '1' COMMENT '1 - РїРѕРєСѓРїР°С‚РµР»СЊ\r\n2 - РїСЂРѕРґР°РІРµС†\r\n3 - РїРѕРєСѓРїР°С‚РµР»СЊ/РїСЂРѕРґР°РІРµС†\r\n4 - РіРѕСЃРѕСЂРіР°РЅРёР·Р°С†РёСЏ\r\n0 - РїСЂРѕСЃС‚Рѕ СЃС‚РѕСЂРѕРЅСЏСЏ  РѕСЂРіР°РЅРёР·Р°С†РёСЏ',
  `email` varchar(64) NOT NULL,
  `phone` varchar(64) NOT NULL,
  PRIMARY KEY (`customer_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COMMENT='Справочник контрагентов';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_customer_view`;
/*!50001 DROP VIEW IF EXISTS `erp_customer_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_customer_view` AS SELECT 
 1 AS `customer_id`,
 1 AS `customer_name`,
 1 AS `detail`,
 1 AS `cust_type`,
 1 AS `email`,
 1 AS `phone`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_docrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_docrel` (
  `doc1` int(11) DEFAULT NULL,
  `doc2` int(11) DEFAULT NULL,
  KEY `doc1` (`doc1`),
  KEY `doc2` (`doc2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Связь между  документами';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_document` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_number` varchar(45) NOT NULL,
  `document_date` date NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text,
  `amount` int(11) DEFAULT NULL,
  `type_id` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `datatag` int(11) DEFAULT NULL,
  PRIMARY KEY (`document_id`),
  KEY `document_date` (`document_date`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='Документы';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_document_update_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_document_update_log` (
  `document_update_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `hostname` varchar(128) DEFAULT NULL,
  `document_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_state` tinyint(4) NOT NULL,
  `updatedon` datetime NOT NULL,
  PRIMARY KEY (`document_update_log_id`),
  KEY `document_id` (`document_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=227 DEFAULT CHARSET=utf8 COMMENT='Лог  изменения   статуса  документа';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_document_view`;
/*!50001 DROP VIEW IF EXISTS `erp_document_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_document_view` AS SELECT 
 1 AS `document_id`,
 1 AS `document_number`,
 1 AS `document_date`,
 1 AS `created`,
 1 AS `updated`,
 1 AS `user_id`,
 1 AS `content`,
 1 AS `amount`,
 1 AS `type_id`,
 1 AS `userlogin`,
 1 AS `state`,
 1 AS `datatag`,
 1 AS `meta_name`,
 1 AS `meta_desc`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_event` (
  `user_id` int(11) NOT NULL,
  `eventdate` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `notify_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`),
  KEY `user_id` (`user_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_event_view`;
/*!50001 DROP VIEW IF EXISTS `erp_event_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_event_view` AS SELECT 
 1 AS `user_id`,
 1 AS `eventdate`,
 1 AS `title`,
 1 AS `description`,
 1 AS `notify_id`,
 1 AS `event_id`,
 1 AS `customer_id`,
 1 AS `customer_name`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `item_type` int(11) NOT NULL COMMENT 'тип  сущности  к   которой  прикреплен  файл',
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='Файлы,  прикрепленные  к  документам';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_filesdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_filesdata` (
  `file_id` int(11) DEFAULT NULL,
  `filedata` longblob,
  UNIQUE KEY `file_id` (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Содержимое  прикрепленных  файлов';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `itemname` varchar(64) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `measure_id` varchar(32) DEFAULT NULL,
  `detail` text NOT NULL COMMENT 'С†РµРЅР°  РґР»СЏ   РїСЂР°Р№СЃР°',
  `item_code` varchar(64) DEFAULT NULL,
  `item_type` smallint(6) DEFAULT NULL,
  `deleted` int(1) DEFAULT '0',
  PRIMARY KEY (`item_id`),
  KEY `item_code` (`item_code`),
  KEY `itemname` (`itemname`)
) ENGINE=MyISAM AUTO_INCREMENT=4128 DEFAULT CHARSET=utf8 COMMENT='ТМЦ';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_item_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_item_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='Группы  товаров';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_item_measures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_item_measures` (
  `measure_id` int(11) NOT NULL AUTO_INCREMENT,
  `measure_name` varchar(64) NOT NULL,
  `measure_code` varchar(10) NOT NULL,
  PRIMARY KEY (`measure_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Единицы  измерения';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_item_view`;
/*!50001 DROP VIEW IF EXISTS `erp_item_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_item_view` AS SELECT 
 1 AS `item_id`,
 1 AS `detail`,
 1 AS `itemname`,
 1 AS `description`,
 1 AS `measure_id`,
 1 AS `measure_name`,
 1 AS `item_code`,
 1 AS `item_type`,
 1 AS `deleted`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `message` text,
  `item_id` int(11) NOT NULL COMMENT 'тип  сущности  к   которой  коментарии',
  `item_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='Комментарии  к  документам';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_message_view`;
/*!50001 DROP VIEW IF EXISTS `erp_message_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_message_view` AS SELECT 
 1 AS `message_id`,
 1 AS `user_id`,
 1 AS `created`,
 1 AS `message`,
 1 AS `item_id`,
 1 AS `item_type`,
 1 AS `userlogin`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_metadata` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `meta_type` tinyint(11) NOT NULL COMMENT 'тип  метаданных. Документ,  справочник  и т.д.',
  `description` varchar(255) DEFAULT NULL,
  `meta_name` varchar(255) NOT NULL COMMENT 'Наименование объекта совпадающее   с  именем  класса  страницы,  сущности  и.т.д',
  `menugroup` varchar(255) DEFAULT NULL COMMENT 'Группировка  для   подменю',
  `notes` text NOT NULL,
  `disabled` tinyint(4) NOT NULL,
  `smartmenu` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`meta_id`)
) ENGINE=MyISAM AUTO_INCREMENT=93 DEFAULT CHARSET=utf8 COMMENT='Объекты  метаданных';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_moneyfunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_moneyfunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `bank` int(11) NOT NULL,
  `bankaccount` varchar(32) NOT NULL,
  `ftype` smallint(6) NOT NULL COMMENT '0 РєР°СЃСЃР°,  1 - РѕСЃРЅРѕРІРЅРѕР№  СЃС‡РµС‚, 2 -  РґРѕРїРѕР»РЅРёС‚РµР»СЊРЅС‹Р№  СЃС‡РµС‚',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Денежные  счета фирмы.  Банк,  касса';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_staff_department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_staff_department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(100) NOT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='Справочник  отделов';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_staff_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_staff_employee` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `login` varchar(64) DEFAULT NULL,
  `detail` text,
  `hiredate` date NOT NULL,
  `firedate` date DEFAULT NULL,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `middlename` varchar(64) NOT NULL,
  `contact_id` int(11) NOT NULL,
  PRIMARY KEY (`employee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='Сотрудник';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_staff_employee_view`;
/*!50001 DROP VIEW IF EXISTS `erp_staff_employee_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_staff_employee_view` AS SELECT 
 1 AS `employee_id`,
 1 AS `position_id`,
 1 AS `department_id`,
 1 AS `login`,
 1 AS `detail`,
 1 AS `firstname`,
 1 AS `lastname`,
 1 AS `middlename`,
 1 AS `department_name`,
 1 AS `position_name`,
 1 AS `fullname`,
 1 AS `shortname`,
 1 AS `firedate`,
 1 AS `hiredate`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_staff_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_staff_position` (
  `position_id` int(11) NOT NULL AUTO_INCREMENT,
  `position_name` varchar(100) NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Справочник должностей';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_stock_view`;
/*!50001 DROP VIEW IF EXISTS `erp_stock_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_stock_view` AS SELECT 
 1 AS `stock_id`,
 1 AS `item_id`,
 1 AS `itemname`,
 1 AS `item_code`,
 1 AS `storename`,
 1 AS `store_id`,
 1 AS `measure_name`,
 1 AS `price`,
 1 AS `partion`,
 1 AS `closed`,
 1 AS `item_type`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `storename` varchar(64) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `store_type` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`store_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='Места хранения';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_store_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_store_stock` (
  `stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `partion` int(11) DEFAULT NULL,
  `store_id` int(11) NOT NULL,
  `price` int(11) DEFAULT NULL,
  `closed` tinyint(4) DEFAULT '0' COMMENT ' 1 - РЅРµРёСЃРїРѕР»СЊР·СѓРµРјР°СЏ  РїР°СЂС‚РёСЏ',
  PRIMARY KEY (`stock_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4094 DEFAULT CHARSET=utf8 COMMENT='Товар на  складе';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_task_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_task_project` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `projectname` varchar(255) NOT NULL,
  `customer_id` int(11) DEFAULT '0',
  PRIMARY KEY (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=48;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_task_project_view`;
/*!50001 DROP VIEW IF EXISTS `erp_task_project_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_task_project_view` AS SELECT 
 1 AS `project_id`,
 1 AS `doc_id`,
 1 AS `description`,
 1 AS `start_date`,
 1 AS `end_date`,
 1 AS `projectname`,
 1 AS `customer_id`,
 1 AS `customer_name`,
 1 AS `taskall`,
 1 AS `taskclosed`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `erp_task_sh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_task_sh` (
  `task_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `username` varchar(64) NOT NULL,
  `sdate` datetime NOT NULL,
  `task_sh` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`task_sh`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='РёСЃС‚РѕСЂРёСЏ СЃС‚Р°С‚СѓСЃР° Р·Р°РґР°С‡';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_task_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_task_task` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `hours` int(11) DEFAULT NULL,
  `cost` int(11) DEFAULT NULL,
  `status` tinyint(4) unsigned NOT NULL,
  `taskname` varchar(255) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `priority` tinyint(4) unsigned DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=76;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_task_task_emp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erp_task_task_emp` (
  `task_emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  PRIMARY KEY (`task_emp_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='  ';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `erp_task_task_view`;
/*!50001 DROP VIEW IF EXISTS `erp_task_task_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `erp_task_task_view` AS SELECT 
 1 AS `task_id`,
 1 AS `project_id`,
 1 AS `description`,
 1 AS `start_date`,
 1 AS `end_date`,
 1 AS `hours`,
 1 AS `status`,
 1 AS `taskname`,
 1 AS `createdby`,
 1 AS `details`,
 1 AS `priority`,
 1 AS `cost`,
 1 AS `updated`,
 1 AS `createdbyname`,
 1 AS `projectname`,
 1 AS `empcnt`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `shop_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_attributes` (
  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `attributename` varchar(64) NOT NULL,
  `group_id` int(11) NOT NULL,
  `attributetype` tinyint(4) NOT NULL,
  `valueslist` varchar(255) DEFAULT NULL,
  `showinlist` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`attribute_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shop_attributes_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_attributes_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `attr_id` int(11) NOT NULL,
  `pg_id` int(11) NOT NULL,
  `ordern` int(11) NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shop_attributes_view`;
/*!50001 DROP VIEW IF EXISTS `shop_attributes_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `shop_attributes_view` AS SELECT 
 1 AS `attribute_id`,
 1 AS `attributename`,
 1 AS `group_id`,
 1 AS `attributetype`,
 1 AS `valueslist`,
 1 AS `showinlist`,
 1 AS `ordern`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `shop_attributevalues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_attributevalues` (
  `attributevalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `attributevalue` varchar(255) NOT NULL,
  PRIMARY KEY (`attributevalue_id`),
  KEY `attribute_id` (`attribute_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shop_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longblob NOT NULL,
  `mime` varchar(16) NOT NULL,
  `thumb` blob NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shop_manufacturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_manufacturers` (
  `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturername` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`manufacturer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shop_orderdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_orderdetails` (
  `orderdetail_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`orderdetail_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shop_orderdetails_view`;
/*!50001 DROP VIEW IF EXISTS `shop_orderdetails_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `shop_orderdetails_view` AS SELECT 
 1 AS `orderdetail_id`,
 1 AS `order_id`,
 1 AS `product_id`,
 1 AS `quantity`,
 1 AS `price`,
 1 AS `productname`,
 1 AS `group_id`,
 1 AS `partion`,
 1 AS `erp_stock_id`,
 1 AS `erp_item_id`,
 1 AS `orderstatus`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `shop_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` int(11) NOT NULL,
  `description` text,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `comment` varchar(250) DEFAULT NULL,
  `created` date NOT NULL,
  `closed` date DEFAULT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shop_orders_view`;
/*!50001 DROP VIEW IF EXISTS `shop_orders_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `shop_orders_view` AS SELECT 
 1 AS `order_id`,
 1 AS `amount`,
 1 AS `description`,
 1 AS `status`,
 1 AS `comment`,
 1 AS `details`,
 1 AS `created`,
 1 AS `closed`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `shop_prod_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_prod_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `author` varchar(64) NOT NULL,
  `comment` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rating` tinyint(4) NOT NULL DEFAULT '0',
  `moderated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shop_productgroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_productgroups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `groupname` varchar(128) NOT NULL,
  `mpath` varchar(1024) DEFAULT NULL,
  `image_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shop_productgroups_view`;
/*!50001 DROP VIEW IF EXISTS `shop_productgroups_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `shop_productgroups_view` AS SELECT 
 1 AS `group_id`,
 1 AS `parent_id`,
 1 AS `groupname`,
 1 AS `mpath`,
 1 AS `image_id`,
 1 AS `gcnt`,
 1 AS `pcnt`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `shop_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `productname` varchar(255) NOT NULL,
  `manufacturer_id` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL DEFAULT '0',
  `image_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `fulldescription` text NOT NULL,
  `old_price` int(11) NOT NULL,
  `novelty` tinyint(1) NOT NULL,
  `topsaled` int(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `sef` varchar(64) DEFAULT NULL,
  `erp_item_id` int(11) NOT NULL,
  `erp_stock_id` int(11) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `created` date NOT NULL,
  `partion` int(11) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shop_products_view`;
/*!50001 DROP VIEW IF EXISTS `shop_products_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `shop_products_view` AS SELECT 
 1 AS `product_id`,
 1 AS `group_id`,
 1 AS `productname`,
 1 AS `manufacturer_id`,
 1 AS `price`,
 1 AS `image_id`,
 1 AS `description`,
 1 AS `fulldescription`,
 1 AS `old_price`,
 1 AS `topsaled`,
 1 AS `deleted`,
 1 AS `sef`,
 1 AS `erp_item_id`,
 1 AS `erp_stock_id`,
 1 AS `item_code`,
 1 AS `created`,
 1 AS `groupname`,
 1 AS `manufacturername`,
 1 AS `rated`,
 1 AS `comments`,
 1 AS `cntonstore`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `system_notifies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_notifies` (
  `notify_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `dateshow` datetime NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  PRIMARY KEY (`notify_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `system_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_options` (
  `optname` varchar(64) NOT NULL,
  `optvalue` text NOT NULL,
  UNIQUE KEY `optname` (`optname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `system_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `userlogin` varchar(32) NOT NULL,
  `userpass` varchar(255) NOT NULL,
  `createdon` date NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(250) NOT NULL,
  `acl` text NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `userlogin` (`userlogin`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50001 DROP VIEW IF EXISTS `erp_account_entry_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_account_entry_view` AS select `e`.`entry_id` AS `entry_id`,`e`.`acc_d` AS `acc_d`,`e`.`acc_c` AS `acc_c`,`e`.`amount` AS `amount`,`e`.`document_id` AS `document_id`,`doc`.`document_number` AS `document_number`,`doc`.`meta_desc` AS `meta_desc`,`doc`.`meta_name` AS `meta_name`,`doc`.`document_date` AS `document_date` from (`erp_account_entry` `e` join `erp_document_view` `doc` on((`e`.`document_id` = `doc`.`document_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_account_subconto_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_account_subconto_view` AS select `sc`.`subconto_id` AS `subconto_id`,`sc`.`account_id` AS `account_id`,`sc`.`document_id` AS `document_id`,`sc`.`document_date` AS `document_date`,cast((`sc`.`amount` / 100) as decimal(10,2)) AS `amount`,cast((`sc`.`quantity` / 1000) as decimal(10,2)) AS `quantity`,`sc`.`customer_id` AS `customer_id`,`sc`.`employee_id` AS `employee_id`,`sc`.`asset_id` AS `asset_id`,`sc`.`extcode` AS `extcode`,`sc`.`stock_id` AS `stock_id`,`sc`.`moneyfund_id` AS `moneyfund_id`,`dc`.`document_number` AS `document_number`,`dc`.`meta_desc` AS `meta_desc`,`dc`.`meta_name` AS `meta_name`,`cs`.`customer_name` AS `customer_name`,(case when (`sc`.`employee_id` > 0) then `em`.`shortname` else NULL end) AS `employee_name`,`mf`.`title` AS `moneyfundname`,`it`.`itemname` AS `osname`,`st`.`itemname` AS `itemname`,cast((`st`.`partion` / 100) as decimal(10,2)) AS `partion`,`st`.`storename` AS `storename`,`st`.`item_id` AS `item_id`,`st`.`store_id` AS `store_id`,(case when (`sc`.`amount` >= 0) then `sc`.`amount` else 0 end) AS `da`,(case when (`sc`.`amount` < 0) then (0 - `sc`.`amount`) else 0 end) AS `ca`,(case when (`sc`.`quantity` >= 0) then `sc`.`quantity` else 0 end) AS `dq`,(case when (`sc`.`quantity` < 0) then (0 - `sc`.`quantity`) else 0 end) AS `cq` from ((((((`erp_account_subconto` `sc` join `erp_document_view` `dc` on((`sc`.`document_id` = `dc`.`document_id`))) left join `erp_customer` `cs` on((`sc`.`customer_id` = `cs`.`customer_id`))) left join `erp_staff_employee_view` `em` on((`sc`.`employee_id` = `em`.`employee_id`))) left join `erp_moneyfunds` `mf` on((`sc`.`moneyfund_id` = `mf`.`id`))) left join `erp_item` `it` on((`sc`.`asset_id` = `it`.`item_id`))) left join `erp_stock_view` `st` on((`sc`.`stock_id` = `st`.`stock_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_contact_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_contact_view` AS select `erp_contact`.`contact_id` AS `contact_id`,`erp_contact`.`firstname` AS `firstname`,`erp_contact`.`middlename` AS `middlename`,`erp_contact`.`lastname` AS `lastname`,concat_ws(' ',`erp_contact`.`lastname`,`erp_contact`.`firstname`,`erp_contact`.`middlename`) AS `fullname`,`erp_contact`.`email` AS `email`,`erp_contact`.`phone` AS `phone`,`erp_contact`.`detail` AS `detail`,coalesce(`cc`.`customer_id`,0) AS `customer`,`erp_contact`.`description` AS `description`,`cc`.`customer_name` AS `customer_name`,`cc`.`cust_type` AS `cust_type` from (`erp_contact` left join `erp_customer` `cc` on((`erp_contact`.`customer_id` = `cc`.`customer_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_customer_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_customer_view` AS select `c`.`customer_id` AS `customer_id`,`c`.`customer_name` AS `customer_name`,`c`.`detail` AS `detail`,`c`.`cust_type` AS `cust_type`,`c`.`email` AS `email`,`c`.`phone` AS `phone` from `erp_customer` `c` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_document_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp1251 */;
/*!50001 SET character_set_results     = cp1251 */;
/*!50001 SET collation_connection      = cp1251_general_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_document_view` AS select `d`.`document_id` AS `document_id`,`d`.`document_number` AS `document_number`,`d`.`document_date` AS `document_date`,`d`.`created` AS `created`,`d`.`updated` AS `updated`,`d`.`user_id` AS `user_id`,`d`.`content` AS `content`,`d`.`amount` AS `amount`,`d`.`type_id` AS `type_id`,`u`.`userlogin` AS `userlogin`,`d`.`state` AS `state`,`d`.`datatag` AS `datatag`,`erp_metadata`.`meta_name` AS `meta_name`,`erp_metadata`.`description` AS `meta_desc` from ((`erp_document` `d` join `system_users` `u` on((`d`.`user_id` = `u`.`user_id`))) join `erp_metadata` on((`erp_metadata`.`meta_id` = `d`.`type_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_event_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_event_view` AS select `e`.`user_id` AS `user_id`,`e`.`eventdate` AS `eventdate`,`e`.`title` AS `title`,`e`.`description` AS `description`,`e`.`notify_id` AS `notify_id`,`e`.`event_id` AS `event_id`,`e`.`customer_id` AS `customer_id`,`c`.`customer_name` AS `customer_name` from (`erp_event` `e` left join `erp_customer` `c` on((`e`.`customer_id` = `c`.`customer_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_item_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_item_view` AS select `t`.`item_id` AS `item_id`,`t`.`detail` AS `detail`,`t`.`itemname` AS `itemname`,`t`.`description` AS `description`,`t`.`measure_id` AS `measure_id`,`m`.`measure_name` AS `measure_name`,`t`.`item_code` AS `item_code`,`t`.`item_type` AS `item_type`,`t`.`deleted` AS `deleted` from (`erp_item` `t` join `erp_item_measures` `m` on((`t`.`measure_id` = `m`.`measure_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_message_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_message_view` AS select `erp_message`.`message_id` AS `message_id`,`erp_message`.`user_id` AS `user_id`,`erp_message`.`created` AS `created`,`erp_message`.`message` AS `message`,`erp_message`.`item_id` AS `item_id`,`erp_message`.`item_type` AS `item_type`,`system_users`.`userlogin` AS `userlogin` from (`erp_message` join `system_users` on((`erp_message`.`user_id` = `system_users`.`user_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_staff_employee_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_staff_employee_view` AS select `e`.`employee_id` AS `employee_id`,`e`.`position_id` AS `position_id`,`e`.`department_id` AS `department_id`,`e`.`login` AS `login`,`e`.`detail` AS `detail`,`e`.`firstname` AS `firstname`,`e`.`lastname` AS `lastname`,`e`.`middlename` AS `middlename`,`d`.`department_name` AS `department_name`,`p`.`position_name` AS `position_name`,concat_ws(' ',`e`.`lastname`,`e`.`firstname`,`e`.`middlename`) AS `fullname`,concat_ws(' ',`e`.`lastname`,`e`.`firstname`) AS `shortname`,`e`.`firedate` AS `firedate`,`e`.`hiredate` AS `hiredate` from ((`erp_staff_employee` `e` left join `erp_staff_position` `p` on((`e`.`position_id` = `p`.`position_id`))) left join `erp_staff_department` `d` on((`e`.`department_id` = `d`.`department_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_stock_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_stock_view` AS select `erp_store_stock`.`stock_id` AS `stock_id`,`erp_store_stock`.`item_id` AS `item_id`,`erp_item_view`.`itemname` AS `itemname`,`erp_item_view`.`item_code` AS `item_code`,`erp_store`.`storename` AS `storename`,`erp_store`.`store_id` AS `store_id`,`erp_item_view`.`measure_name` AS `measure_name`,`erp_store_stock`.`price` AS `price`,`erp_store_stock`.`partion` AS `partion`,coalesce(`erp_store_stock`.`closed`,0) AS `closed`,`erp_item_view`.`item_type` AS `item_type` from ((`erp_store_stock` join `erp_item_view` on((`erp_store_stock`.`item_id` = `erp_item_view`.`item_id`))) join `erp_store` on((`erp_store_stock`.`store_id` = `erp_store`.`store_id`))) where ((`erp_item_view`.`item_type` <> 3) and (`erp_item_view`.`deleted` <> 1)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_task_project_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_task_project_view` AS select `erp_task_project`.`project_id` AS `project_id`,`erp_task_project`.`doc_id` AS `doc_id`,`erp_task_project`.`description` AS `description`,`erp_task_project`.`start_date` AS `start_date`,`erp_task_project`.`end_date` AS `end_date`,`erp_task_project`.`projectname` AS `projectname`,`erp_task_project`.`customer_id` AS `customer_id`,`erp_customer`.`customer_name` AS `customer_name`,(select count(0) AS `count(*)` from `erp_task_task` where (`erp_task_task`.`project_id` = `erp_task_project`.`project_id`)) AS `taskall`,(select count(0) AS `count(*)` from `erp_task_task` where ((`erp_task_task`.`project_id` = `erp_task_project`.`project_id`) and (`erp_task_task`.`status` = 3))) AS `taskclosed` from (`erp_task_project` left join `erp_customer` on((`erp_task_project`.`customer_id` = `erp_customer`.`customer_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `erp_task_task_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `erp_task_task_view` AS select `t`.`task_id` AS `task_id`,`t`.`project_id` AS `project_id`,`t`.`description` AS `description`,`t`.`start_date` AS `start_date`,`t`.`end_date` AS `end_date`,`t`.`hours` AS `hours`,`t`.`status` AS `status`,`t`.`taskname` AS `taskname`,`t`.`createdby` AS `createdby`,`t`.`details` AS `details`,`t`.`priority` AS `priority`,`t`.`cost` AS `cost`,`t`.`updated` AS `updated`,`u`.`userlogin` AS `createdbyname`,`p`.`projectname` AS `projectname`,(select count(0) AS `count(0)` from `erp_task_task_emp` where (`erp_task_task_emp`.`task_id` = `t`.`task_id`)) AS `empcnt` from ((`erp_task_task` `t` join `erp_task_project` `p` on((`t`.`project_id` = `p`.`project_id`))) join `system_users` `u` on((`t`.`createdby` = `u`.`user_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `shop_attributes_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `shop_attributes_view` AS select `shop_attributes`.`attribute_id` AS `attribute_id`,`shop_attributes`.`attributename` AS `attributename`,`shop_attributes`.`group_id` AS `group_id`,`shop_attributes`.`attributetype` AS `attributetype`,`shop_attributes`.`valueslist` AS `valueslist`,`shop_attributes`.`showinlist` AS `showinlist`,`shop_attributes_order`.`ordern` AS `ordern` from (`shop_attributes` join `shop_attributes_order` on(((`shop_attributes`.`attribute_id` = `shop_attributes_order`.`attr_id`) and (`shop_attributes`.`group_id` = `shop_attributes_order`.`pg_id`)))) order by `shop_attributes_order`.`ordern` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `shop_orderdetails_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `shop_orderdetails_view` AS select `od`.`orderdetail_id` AS `orderdetail_id`,`od`.`order_id` AS `order_id`,`od`.`product_id` AS `product_id`,`od`.`quantity` AS `quantity`,`od`.`price` AS `price`,`p`.`productname` AS `productname`,`p`.`group_id` AS `group_id`,`p`.`partion` AS `partion`,`p`.`erp_stock_id` AS `erp_stock_id`,`p`.`erp_item_id` AS `erp_item_id`,`so`.`status` AS `orderstatus` from ((`shop_orderdetails` `od` join `shop_products` `p` on((`od`.`product_id` = `p`.`product_id`))) join `shop_orders` `so` on((`so`.`order_id` = `od`.`order_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `shop_orders_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `shop_orders_view` AS select `shop_orders`.`order_id` AS `order_id`,`shop_orders`.`amount` AS `amount`,`shop_orders`.`description` AS `description`,`shop_orders`.`status` AS `status`,`shop_orders`.`comment` AS `comment`,`shop_orders`.`details` AS `details`,`shop_orders`.`created` AS `created`,`shop_orders`.`closed` AS `closed` from `shop_orders` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `shop_productgroups_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `shop_productgroups_view` AS select `g`.`group_id` AS `group_id`,`g`.`parent_id` AS `parent_id`,`g`.`groupname` AS `groupname`,`g`.`mpath` AS `mpath`,`g`.`image_id` AS `image_id`,(select count(`sg`.`group_id`) AS `cnt` from `shop_productgroups` `sg` where (`g`.`group_id` = `sg`.`parent_id`)) AS `gcnt`,(select count(`p`.`product_id`) AS `cnt` from `shop_products` `p` where (`g`.`group_id` = `p`.`group_id`)) AS `pcnt` from `shop_productgroups` `g` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `shop_products_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE  */
/*!50013  */
/*!50001 VIEW `shop_products_view` AS select `p`.`product_id` AS `product_id`,`p`.`group_id` AS `group_id`,`p`.`productname` AS `productname`,`p`.`manufacturer_id` AS `manufacturer_id`,`p`.`price` AS `price`,`p`.`image_id` AS `image_id`,`p`.`description` AS `description`,`p`.`fulldescription` AS `fulldescription`,`p`.`old_price` AS `old_price`,`p`.`topsaled` AS `topsaled`,`p`.`deleted` AS `deleted`,`p`.`sef` AS `sef`,`p`.`erp_item_id` AS `erp_item_id`,`p`.`erp_stock_id` AS `erp_stock_id`,`p`.`item_code` AS `item_code`,`p`.`created` AS `created`,`g`.`groupname` AS `groupname`,`m`.`manufacturername` AS `manufacturername`,coalesce((select avg(`pr`.`rating`) AS `avg(rating)` from `shop_prod_comments` `pr` where ((`pr`.`product_id` = `p`.`product_id`) and (`pr`.`rating` > 0))),0) AS `rated`,coalesce((select count(`pc`.`comment_id`) AS `count(``pc``.``comment_id``)` from `shop_prod_comments` `pc` where (`pc`.`product_id` = `p`.`product_id`)),0) AS `comments`,(select coalesce(sum(`erp_account_subconto`.`quantity`),0) AS `cnt` from `erp_account_subconto` where (`erp_account_subconto`.`stock_id` = `p`.`erp_stock_id`)) AS `cntonstore` from ((`shop_products` `p` join `shop_productgroups` `g` on((`p`.`group_id` = `g`.`group_id`))) left join `shop_manufacturers` `m` on((`p`.`manufacturer_id` = `m`.`manufacturer_id`))) */;
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

