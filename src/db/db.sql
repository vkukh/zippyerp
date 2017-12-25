--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 6.3.358.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 25.03.2016 13:50:45
-- Версия сервера: 5.1.41-community
-- Версия клиента: 4.1
--


--
-- Описание для таблицы erp_account_entry
--
DROP TABLE IF EXISTS erp_account_entry;
CREATE TABLE IF NOT EXISTS erp_account_entry (
  entry_id int(11) NOT NULL AUTO_INCREMENT,
  acc_d int(11) NOT NULL,
  acc_c int(11) NOT NULL,
  amount int(11) NOT NULL,
  document_id int(11) NOT NULL,
  document_date date DEFAULT NULL,
  PRIMARY KEY (entry_id),
  INDEX created (document_date),
  INDEX document_id (document_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 125
AVG_ROW_LENGTH = 24
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_account_plan
--
DROP TABLE IF EXISTS erp_account_plan;
CREATE TABLE IF NOT EXISTS erp_account_plan (
  acc_code int(16) NOT NULL,
  acc_name varchar(255) NOT NULL,
  acc_pid int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (acc_code)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 57
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_account_subconto
--
DROP TABLE IF EXISTS erp_account_subconto;
CREATE TABLE IF NOT EXISTS erp_account_subconto (
  subconto_id int(11) NOT NULL AUTO_INCREMENT,
  account_id int(11) NOT NULL,
  document_id int(11) NOT NULL,
  document_date date NOT NULL,
  amount int(11) NOT NULL DEFAULT 0,
  quantity int(11) NOT NULL DEFAULT 0,
  customer_id int(11) NOT NULL DEFAULT 0,
  employee_id int(11) NOT NULL DEFAULT 0,
  asset_id int(11) NOT NULL DEFAULT 0,
  extcode int(11) NOT NULL DEFAULT 0,
  stock_id int(11) NOT NULL DEFAULT 0,
  moneyfund_id int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (subconto_id),
  INDEX account_id (account_id),
  INDEX document_date (document_date),
  INDEX document_id (document_id),
  INDEX stock_id (stock_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 175
AVG_ROW_LENGTH = 48
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_bank
--
DROP TABLE IF EXISTS erp_bank;
CREATE TABLE IF NOT EXISTS erp_bank (
  bank_id int(11) NOT NULL AUTO_INCREMENT,
  bank_name varchar(255) NOT NULL,
  detail text NOT NULL,
  PRIMARY KEY (bank_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 6
AVG_ROW_LENGTH = 70
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_contact
--
DROP TABLE IF EXISTS erp_contact;
CREATE TABLE IF NOT EXISTS erp_contact (
  contact_id int(11) NOT NULL AUTO_INCREMENT,
  firstname varchar(64) NOT NULL,
  middlename varchar(64) DEFAULT NULL,
  lastname varchar(64) NOT NULL,
  email varchar(64) DEFAULT NULL,
  detail text NOT NULL,
  description text DEFAULT NULL,
  customer_id int(11) DEFAULT NULL,
  PRIMARY KEY (contact_id),
  INDEX customer_id (customer_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 35
AVG_ROW_LENGTH = 122
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_customer
--
DROP TABLE IF EXISTS erp_customer;
CREATE TABLE IF NOT EXISTS erp_customer (
  customer_id int(11) NOT NULL AUTO_INCREMENT,
  customer_name varchar(255) DEFAULT NULL,
  detail text NOT NULL,
  contact_id int(11) DEFAULT 0 COMMENT '>0 - физлицо ( ссылка  на  контакт)',
  cust_type int(1) NOT NULL DEFAULT 1 COMMENT '1 - покупатель
2 - продавец
3 - покупатель/продавец
4 - госорганизация
0 - просто стороняя  организация',
  PRIMARY KEY (customer_id),
  INDEX contact_id (contact_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 27
AVG_ROW_LENGTH = 289
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_docrel
--
DROP TABLE IF EXISTS erp_docrel;
CREATE TABLE IF NOT EXISTS erp_docrel (
  doc1 int(11) DEFAULT NULL,
  doc2 int(11) DEFAULT NULL,
  INDEX doc1 (doc1),
  INDEX doc2 (doc2)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 9
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_document
--
DROP TABLE IF EXISTS erp_document;
CREATE TABLE IF NOT EXISTS erp_document (
  document_id int(11) NOT NULL AUTO_INCREMENT,
  document_number varchar(45) NOT NULL,
  document_date date NOT NULL,
  created datetime NOT NULL,
  updated datetime NOT NULL,
  user_id int(11) NOT NULL,
  content text DEFAULT NULL,
  amount int(11) DEFAULT NULL,
  type_id int(11) NOT NULL,
  state tinyint(4) NOT NULL,
  datatag int(11) DEFAULT NULL,
  PRIMARY KEY (document_id),
  INDEX document_date (document_date)
)
ENGINE = MYISAM
AUTO_INCREMENT = 25
AVG_ROW_LENGTH = 673
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_document_update_log
--
DROP TABLE IF EXISTS erp_document_update_log;
CREATE TABLE IF NOT EXISTS erp_document_update_log (
  document_update_log_id int(11) NOT NULL AUTO_INCREMENT,
  hostname varchar(128) DEFAULT NULL,
  document_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  document_state tinyint(4) NOT NULL,
  updatedon datetime NOT NULL,
  PRIMARY KEY (document_update_log_id),
  INDEX document_id (document_id),
  INDEX user_id (user_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 117
AVG_ROW_LENGTH = 37
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_files
--
DROP TABLE IF EXISTS erp_files;
CREATE TABLE IF NOT EXISTS erp_files (
  file_id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) DEFAULT NULL,
  filename varchar(255) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  item_type int(11) NOT NULL,
  PRIMARY KEY (file_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 12
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_filesdata
--
DROP TABLE IF EXISTS erp_filesdata;
CREATE TABLE IF NOT EXISTS erp_filesdata (
  file_id int(11) DEFAULT NULL,
  filedata longblob DEFAULT NULL,
  UNIQUE INDEX file_id (file_id)
)
ENGINE = MYISAM
CHARACTER SET utf8
COLLATE utf8_general_ci
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы erp_item
--
DROP TABLE IF EXISTS erp_item;
CREATE TABLE IF NOT EXISTS erp_item (
  item_id int(11) NOT NULL AUTO_INCREMENT,
  itemname varchar(64) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  measure_id varchar(32) DEFAULT NULL,
 
  detail text NOT NULL COMMENT 'цена  для   прайса',
  item_code varchar(16) DEFAULT NULL,
  item_type smallint(6) DEFAULT NULL,
  PRIMARY KEY (item_id),
  UNIQUE INDEX item_code (item_code)
)
ENGINE = MYISAM
AUTO_INCREMENT = 42
AVG_ROW_LENGTH = 268
CHARACTER SET utf8
COLLATE utf8_general_ci;

 

--
-- Описание для таблицы erp_item_measures
--
DROP TABLE IF EXISTS erp_item_measures;
CREATE TABLE IF NOT EXISTS erp_item_measures (
  measure_id int(11) NOT NULL AUTO_INCREMENT,
  measure_name varchar(64) NOT NULL,
  measure_code varchar(10) NOT NULL,
  PRIMARY KEY (measure_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 3
AVG_ROW_LENGTH = 20
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_message
--
DROP TABLE IF EXISTS erp_message;
CREATE TABLE IF NOT EXISTS erp_message (
  message_id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT NULL,
  created datetime DEFAULT NULL,
  message text DEFAULT NULL,
  item_id int(11) NOT NULL,
  item_type int(11) DEFAULT NULL,
  PRIMARY KEY (message_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_metadata
--
DROP TABLE IF EXISTS erp_metadata;
CREATE TABLE IF NOT EXISTS erp_metadata (
  meta_id int(11) NOT NULL AUTO_INCREMENT,
  meta_type tinyint(11) NOT NULL,
  description varchar(255) DEFAULT NULL,
  meta_name varchar(255) NOT NULL,
  menugroup varchar(255) DEFAULT NULL,
  notes text NOT NULL,
  disabled tinyint(4) NOT NULL,
  PRIMARY KEY (meta_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 88
AVG_ROW_LENGTH = 107
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_metadata_access
--
DROP TABLE IF EXISTS erp_metadata_access;
CREATE TABLE IF NOT EXISTS erp_metadata_access (
  metadata_access_id int(11) NOT NULL AUTO_INCREMENT,
  metadata_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  viewacc tinyint(1) NOT NULL DEFAULT 0,
  editacc tinyint(1) NOT NULL DEFAULT 0,

  execacc tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (metadata_access_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 43
AVG_ROW_LENGTH = 17
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_moneyfunds
--
DROP TABLE IF EXISTS erp_moneyfunds;
CREATE TABLE IF NOT EXISTS erp_moneyfunds (
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(64) NOT NULL,
  bank int(11) NOT NULL,
  bankaccount varchar(32) NOT NULL,
  ftype smallint(6) NOT NULL COMMENT '0 касса,  1 - основной  счет, 2 -  дополнительный  счет',
  PRIMARY KEY (id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 6
AVG_ROW_LENGTH = 56
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_staff_department
--
DROP TABLE IF EXISTS erp_staff_department;
CREATE TABLE IF NOT EXISTS erp_staff_department (
  department_id int(11) NOT NULL AUTO_INCREMENT,
  department_name varchar(100) NOT NULL,
  PRIMARY KEY (department_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 7
AVG_ROW_LENGTH = 34
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_staff_employee
--
DROP TABLE IF EXISTS erp_staff_employee;
CREATE TABLE IF NOT EXISTS erp_staff_employee (
  employee_id int(11) NOT NULL AUTO_INCREMENT,
  position_id int(11) NOT NULL,
  department_id int(11) NOT NULL,
  login varchar(64) DEFAULT NULL,
  contact_id int(11) NOT NULL COMMENT 'физ. лицо',
  detail text DEFAULT NULL,
  hiredate date NOT NULL,
  firedate date DEFAULT NULL,
  PRIMARY KEY (employee_id),
  INDEX contact_id (contact_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 23
AVG_ROW_LENGTH = 92
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_staff_position
--
DROP TABLE IF EXISTS erp_staff_position;
CREATE TABLE IF NOT EXISTS erp_staff_position (
  position_id int(11) NOT NULL AUTO_INCREMENT,
  position_name varchar(100) NOT NULL,
  PRIMARY KEY (position_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 11
AVG_ROW_LENGTH = 34
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_store
--
DROP TABLE IF EXISTS erp_store;
CREATE TABLE IF NOT EXISTS erp_store (
  store_id int(11) NOT NULL AUTO_INCREMENT,
  storename varchar(64) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  store_type tinyint(4) DEFAULT NULL,
  PRIMARY KEY (store_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 18
AVG_ROW_LENGTH = 36
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_store_stock
--
DROP TABLE IF EXISTS erp_store_stock;
CREATE TABLE IF NOT EXISTS erp_store_stock (
  stock_id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL,
  partion int(11) DEFAULT NULL,
  store_id int(11) NOT NULL,
  price int(11) DEFAULT NULL,
  closed tinyint(4) DEFAULT 0 COMMENT ' 1 - неиспользуемая  партия',
  PRIMARY KEY (stock_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 22
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_task_project
--
DROP TABLE IF EXISTS erp_task_project;
CREATE TABLE IF NOT EXISTS erp_task_project (
  project_id int(11) NOT NULL AUTO_INCREMENT,
  doc_id int(11) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  start_date date DEFAULT NULL,
  end_date date DEFAULT NULL,
  projectname varchar(255) NOT NULL,
  PRIMARY KEY (project_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 6
AVG_ROW_LENGTH = 48
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_task_task
--
DROP TABLE IF EXISTS erp_task_task;
CREATE TABLE IF NOT EXISTS erp_task_task (
  task_id int(11) NOT NULL AUTO_INCREMENT,
  project_id int(11) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  start_date date DEFAULT NULL,
  end_date date DEFAULT NULL,
  hours int(11) DEFAULT NULL,
  cost int(11) DEFAULT NULL,
  status tinyint(4) UNSIGNED NOT NULL,
  taskname varchar(255) DEFAULT NULL,
  createdby int(11) DEFAULT NULL,
  assignedto int(11) DEFAULT NULL,
  priority tinyint(4) UNSIGNED DEFAULT NULL,
  updated datetime DEFAULT NULL,
  PRIMARY KEY (task_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 11
AVG_ROW_LENGTH = 76
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_task_task_emp
--
DROP TABLE IF EXISTS erp_task_task_emp;
CREATE TABLE IF NOT EXISTS erp_task_task_emp (
  task_emp_id int(11) NOT NULL AUTO_INCREMENT,
  task_id int(11) NOT NULL,
  employee_id int(11) NOT NULL,
  PRIMARY KEY (task_emp_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '  ';

DROP TABLE IF EXISTS erp_task_sh;
CREATE TABLE `erp_task_sh` (
  `task_sh` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `username` varchar(64) NOT NULL,
  `sdate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='история статуса задач';

-- Описание для таблицы system_options
--
DROP TABLE IF EXISTS system_options;
CREATE TABLE IF NOT EXISTS system_options (
  optname varchar(64) NOT NULL,
  optvalue text NOT NULL,
  UNIQUE INDEX optname (optname)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 258
CHARACTER SET utf8
COLLATE utf8_general_ci;

 
--
-- Описание для таблицы system_session
--
/*
DROP TABLE IF EXISTS system_session;
CREATE TABLE IF NOT EXISTS system_session (
  sesskey varchar(64) NOT NULL DEFAULT '',
  expiry timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  expireref varchar(250) DEFAULT '',
  created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  sessdata longtext DEFAULT NULL,
  PRIMARY KEY (sesskey),
  INDEX sess2_expireref (expireref),
  INDEX sess2_expiry (expiry)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 91273
CHARACTER SET utf8
COLLATE utf8_general_ci;
*/
 
--
-- Описание для таблицы system_users
--
DROP TABLE IF EXISTS system_users;
CREATE TABLE IF NOT EXISTS system_users (
  user_id int(11) NOT NULL AUTO_INCREMENT,
  userlogin varchar(32) NOT NULL,
  userpass varchar(255) NOT NULL,
  createdon date NOT NULL,
  active int(1) NOT NULL DEFAULT 0,
  email varchar(255) DEFAULT NULL,
  username varchar(250)   NULL,
  homepage varchar(250)   NULL,
  erpuser tinyint(1) NOT NULL DEFAULT '0',
  shopuser int(1) NOT NULL DEFAULT '0',
  acl text NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE INDEX userlogin (userlogin)
)
ENGINE = MYISAM
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 32
CHARACTER SET utf8
COLLATE utf8_general_ci;


--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 6.3.358.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 25.03.2016 13:52:13
-- Версия сервера: 5.1.41-community
-- Версия клиента: 4.1
--


--
-- Описание для представления erp_contact_view
--
DROP VIEW IF EXISTS erp_contact_view CASCADE;
CREATE
VIEW erp_contact_view
AS
SELECT
  `erp_contact`.`contact_id` AS `contact_id`,
  `erp_contact`.`firstname` AS `firstname`,
  `erp_contact`.`middlename` AS `middlename`,
  `erp_contact`.`lastname` AS `lastname`,
  CONCAT_WS(' ', `erp_contact`.`lastname`, `erp_contact`.`firstname`, `erp_contact`.`middlename`) AS `fullname`,
  `erp_contact`.`email` AS `email`,
  `erp_contact`.`detail` AS `detail`,
  COALESCE(`e`.`employee_id`, 0) AS `employee`,
  COALESCE(`cc`.`customer_id`, 0) AS `customer`,
  `erp_contact`.`description` AS `description`,
  `cc`.`customer_name` AS `customer_name`
FROM ((`erp_contact`
  LEFT JOIN `erp_staff_employee` `e`
    ON ((`erp_contact`.`contact_id` = `e`.`contact_id`)))
  LEFT JOIN `erp_customer` `cc`
    ON ((`erp_contact`.`customer_id` = `cc`.`customer_id`)));

--
-- Описание для представления erp_customer_view
--
DROP VIEW IF EXISTS erp_customer_view CASCADE;
CREATE
VIEW erp_customer_view
AS
SELECT
  `c`.`customer_id` AS `customer_id`,
  `c`.`customer_name` AS `customer_name`,
  `c`.`detail` AS `detail`,
  0 AS `amount`,
  `c`.`cust_type` AS `cust_type`,
  `c`.`contact_id` AS `contact_id`
FROM `erp_customer` `c`;

--
-- Описание для представления erp_document_view
--
DROP VIEW IF EXISTS erp_document_view CASCADE;
CREATE
VIEW erp_document_view
AS
  select 
    `d`.`document_id` AS `document_id`,
    `d`.`document_number` AS `document_number`,
    `d`.`document_date` AS `document_date`,
    `d`.`created` AS `created`,
    `d`.`updated` AS `updated`,
    `d`.`user_id` AS `user_id`,
    `d`.`content` AS `content`,
    `d`.`amount` AS `amount`,
    `d`.`type_id` AS `type_id`,
    `u`.`userlogin` AS `userlogin`,
    `d`.`state` AS `state`,
    `d`.`datatag` AS `datatag`,
    `erp_metadata`.`meta_name` AS `meta_name`,
    `erp_metadata`.`description` AS `meta_desc` 
  from 
    ((`erp_document` `d` join `system_users` `u` on((`d`.`user_id` = `u`.`user_id`))) join `erp_metadata` on((`erp_metadata`.`meta_id` = `d`.`type_id`)));
    
--
-- Описание для представления erp_item_view
--
DROP VIEW IF EXISTS erp_item_view CASCADE;
CREATE
VIEW erp_item_view
AS
SELECT
  `t`.`item_id` AS `item_id`,
  `t`.`detail` AS `detail`,
  `t`.`itemname` AS `itemname`,
  `t`.`description` AS `description`,
  `t`.`measure_id` AS `measure_id`,
  `m`.`measure_name` AS `measure_name`,
 
  `t`.`item_code` AS `item_code`,
  `t`.`item_type` AS `item_type`
FROM ((`erp_item` `t`
  JOIN `erp_item_measures` `m`
    ON ((`t`.`measure_id` = `m`.`measure_id`)))
  );

--
-- Описание для представления erp_message_view
--
DROP VIEW IF EXISTS erp_message_view CASCADE;
CREATE
VIEW erp_message_view
AS
SELECT
  `erp_message`.`message_id` AS `message_id`,
  `erp_message`.`user_id` AS `user_id`,
  `erp_message`.`created` AS `created`,
  `erp_message`.`message` AS `message`,
  `erp_message`.`item_id` AS `item_id`,
  `erp_message`.`item_type` AS `item_type`,
  `system_users`.`userlogin` AS `userlogin`
FROM (`erp_message`
  JOIN `system_users`
    ON ((`erp_message`.`user_id` = `system_users`.`user_id`)));

--
-- Описание для представления erp_metadata_access_view
--
DROP VIEW IF EXISTS erp_metadata_access_view  ;
CREATE   VIEW `erp_metadata_access_view` AS 
  select 
    `a`.`metadata_access_id` AS `metadata_access_id`,
    `a`.`metadata_id` AS `metadata_id`,
    `a`.`user_id` AS `user_id`,
    `a`.`viewacc` AS `viewacc`,
    `a`.`editacc` AS `editacc`,
    `a`.`execacc` AS `execacc`,
    `m`.`meta_type` AS `meta_type`,
    `m`.`meta_name` AS `meta_name`,
    `u`.`userlogin` AS `userlogin` 
  from 
    ((`erp_metadata_access` `a` join `system_users` `u` on((`a`.`user_id` = `u`.`user_id`))) join `erp_metadata` `m` on((`a`.`metadata_id` = `m`.`meta_id`)));

-- Описание для представления erp_staff_employee_view
--
DROP VIEW IF EXISTS erp_staff_employee_view CASCADE;
CREATE
VIEW erp_staff_employee_view
AS
SELECT
  `e`.`employee_id` AS `employee_id`,
  `e`.`position_id` AS `position_id`,
  `e`.`department_id` AS `department_id`,
  `e`.`login` AS `login`,
  `e`.`detail` AS `detail`,
  `c`.`firstname` AS `firstname`,
  `c`.`lastname` AS `lastname`,
  `c`.`middlename` AS `middlename`,
  `d`.`department_name` AS `department_name`,
  `p`.`position_name` AS `position_name`,
  `e`.`contact_id` AS `contact_id`,
  CONCAT_WS(' ', `c`.`lastname`, `c`.`firstname`, `c`.`middlename`) AS `fullname`,
  CONCAT_WS(' ', `c`.`lastname`, `c`.`firstname`) AS `shortname`,
  `e`.`firedate` AS `firedate`,
  `e`.`hiredate` AS `hiredate`
FROM (((`erp_staff_employee` `e`
  JOIN `erp_contact` `c`
    ON ((`e`.`contact_id` = `c`.`contact_id`)))
  LEFT JOIN `erp_staff_position` `p`
    ON ((`e`.`position_id` = `p`.`position_id`)))
  LEFT JOIN `erp_staff_department` `d`
    ON ((`e`.`department_id` = `d`.`department_id`)));

--
-- Описание для представления erp_task_project_view
--
DROP VIEW IF EXISTS erp_task_project_view CASCADE;
CREATE
VIEW erp_task_project_view
AS
  select 
    `erp_task_project`.`project_id` AS `project_id`,
    `erp_task_project`.`doc_id` AS `doc_id`,
    `erp_task_project`.`description` AS `description`,
    `erp_task_project`.`start_date` AS `start_date`,
    `erp_task_project`.`end_date` AS `end_date`,
    `erp_task_project`.`projectname` AS `projectname`,
    (
  select 
    count(0) AS `count(*)` 
  from 
    `erp_task_task` 
  where 
    (`erp_task_task`.`project_id` = `erp_task_project`.`project_id`)) AS `taskall`,(
  select 
    count(0) AS `count(*)` 
  from 
    `erp_task_task` 
  where 
    ((`erp_task_task`.`project_id` = `erp_task_project`.`project_id`) and (`erp_task_task`.`status` = 3))) AS `taskclosed` 
  from 
  `erp_task_project`;

--
-- Описание для представления erp_account_entry_view
--
DROP VIEW IF EXISTS erp_account_entry_view CASCADE;
CREATE
VIEW erp_account_entry_view
AS
SELECT
  `e`.`entry_id` AS `entry_id`,
  `e`.`acc_d` AS `acc_d`,
  `e`.`acc_c` AS `acc_c`,
  `e`.`amount` AS `amount`,
  `e`.`document_id` AS `document_id`,
  `doc`.`document_number` AS `document_number`,
  `doc`.`meta_desc` AS `meta_desc`,
  `doc`.`meta_name` AS `meta_name`,
  `doc`.`document_date` AS `document_date`
FROM (`erp_account_entry` `e`
  JOIN `erp_document_view` `doc`
    ON ((`e`.`document_id` = `doc`.`document_id`)));

--
-- Описание для представления erp_stock_view
--
DROP VIEW IF EXISTS erp_stock_view CASCADE;
CREATE
VIEW erp_stock_view
AS
SELECT
  `erp_store_stock`.`stock_id` AS `stock_id`,
  `erp_store_stock`.`item_id` AS `item_id`,
  `erp_item_view`.`itemname` AS `itemname`,
  `erp_store`.`storename` AS `storename`,
  `erp_store`.`store_id` AS `store_id`,
  `erp_item_view`.`measure_name` AS `measure_name`,
  `erp_store_stock`.`price` AS `price`,
  `erp_store_stock`.`partion` AS `partion`,
  COALESCE(`erp_store_stock`.`closed`, 0) AS `closed`,
  `erp_item_view`.`item_type` AS `item_type`
FROM ((`erp_store_stock`
  JOIN `erp_item_view`
    ON ((`erp_store_stock`.`item_id` = `erp_item_view`.`item_id`)))
  JOIN `erp_store`
    ON ((`erp_store_stock`.`store_id` = `erp_store`.`store_id`)))
WHERE COALESCE((`erp_item_view`.`item_type` <> 3));
--
-- Описание для представления erp_task_task_view
--
DROP VIEW IF EXISTS erp_task_task_view CASCADE;
CREATE   VIEW `erp_task_task_view` AS
  select 
    `t`.`task_id` AS `task_id`,
    `t`.`project_id` AS `project_id`,
    `t`.`description` AS `description`,
    `t`.`start_date` AS `start_date`,
    `t`.`end_date` AS `end_date`,
    `t`.`hours` AS `hours`,
    `t`.`status` AS `status`,
    `t`.`taskname` AS `taskname`,
    `t`.`createdby` AS `createdby`,
    `t`.`priority` AS `priority`,
    `t`.`cost` AS `cost`,
    `t`.`updated` AS `updated`,
    `u`.`userlogin` AS `creatwedbyname`,
    `p`.`projectname` AS `projectname`,
    (
  select 
    count(0) 
  from 
    `erp_task_task_emp` 
  where 
    (`erp_task_task_emp`.`task_id` = `t`.`task_id`)) AS `empcnt` 
  from 
    ((`erp_task_task` `t` join `erp_task_project` `p` on((`t`.`project_id` = `p`.`project_id`))) join `system_users` `u` on((`t`.`createdby` = `u`.`user_id`)));
--
-- Описание для представления erp_account_subconto_view
--
DROP VIEW IF EXISTS erp_account_subconto_view CASCADE;
CREATE
VIEW erp_account_subconto_view
AS
SELECT
  `sc`.`subconto_id` AS `subconto_id`,
  `sc`.`account_id` AS `account_id`,
  `sc`.`document_id` AS `document_id`,
  `sc`.`document_date` AS `document_date`,
  CAST((`sc`.`amount` / 100) AS decimal(10, 2)) AS `amount`,
  CAST((`sc`.`quantity` / 1000) AS decimal(10, 2)) AS `quantity`,
  `sc`.`customer_id` AS `customer_id`,
  `sc`.`employee_id` AS `employee_id`,
  `sc`.`asset_id` AS `asset_id`,
  `sc`.`extcode` AS `extcode`,
  `sc`.`stock_id` AS `stock_id`,
  `sc`.`moneyfund_id` AS `moneyfund_id`,
  `dc`.`document_number` AS `document_number`,
  `dc`.`meta_desc` AS `meta_desc`,
  dc.meta_name AS meta_name,
  `cs`.`customer_name` AS `customer_name`,
  (CASE WHEN (`sc`.`employee_id` > 0) THEN `em`.`shortname` ELSE NULL END) AS `employee_name`,
  `mf`.`title` AS `moneyfundname`,
  `it`.`itemname` AS `osname`,
  `st`.`itemname` AS `itemname`,
  CAST((`st`.`partion` / 100) AS decimal(10, 2)) AS `partion`,
  `st`.`storename` AS `storename`,
  `st`.`item_id` AS `item_id`,
  `st`.`store_id` AS `store_id`,
  (CASE WHEN (`sc`.`amount` >= 0) THEN `sc`.`amount` ELSE 0 END) AS `da`,
  (CASE WHEN (`sc`.`amount` < 0) THEN (0 - `sc`.`amount`) ELSE 0 END) AS `ca`,
  (CASE WHEN (`sc`.`quantity` >= 0) THEN `sc`.`quantity` ELSE 0 END) AS `dq`,
  (CASE WHEN (`sc`.`quantity` < 0) THEN (0 - `sc`.`quantity`) ELSE 0 END) AS `cq`
FROM ((((((`erp_account_subconto` `sc`
  JOIN `erp_document_view` `dc`
    ON ((`sc`.`document_id` = `dc`.`document_id`)))
  LEFT JOIN `erp_customer` `cs`
    ON ((`sc`.`customer_id` = `cs`.`customer_id`)))
  LEFT JOIN `erp_staff_employee_view` `em`
    ON ((`sc`.`employee_id` = `em`.`employee_id`)))
  LEFT JOIN `erp_moneyfunds` `mf`
    ON ((`sc`.`moneyfund_id` = `mf`.`id`)))
  LEFT JOIN `erp_item` `it`
    ON ((`sc`.`asset_id` = `it`.`item_id`)))
  LEFT JOIN `erp_stock_view` `st`
    ON ((`sc`.`stock_id` = `st`.`stock_id`)));
	
CREATE TABLE IF NOT EXISTS `erp_account_entry_view` (
`entry_id` int(11)
,`acc_d` int(11)
,`acc_c` int(11)
,`amount` int(11)
,`document_id` int(11)
,`document_number` varchar(45)
,`meta_desc` varchar(255)
,`meta_name` varchar(255)
,`document_date` date
);
-- --------------------------------------------------------

--
-- Структура таблицы `shop_attributes`
--

CREATE TABLE IF NOT EXISTS `shop_attributes` (
  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `attributename` varchar(64) NOT NULL,
  `group_id` int(11) NOT NULL,
  `attributetype` tinyint(4) NOT NULL,
  `valueslist` varchar(255) DEFAULT NULL,
  `showinlist` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`attribute_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8   ;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_attributevalues`
--

CREATE TABLE IF NOT EXISTS `shop_attributevalues` (
  `attributevalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `attributevalue` varchar(255) NOT NULL,
  PRIMARY KEY (`attributevalue_id`),
  KEY `attribute_id` (`attribute_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8   ;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_images`
--

CREATE TABLE IF NOT EXISTS `shop_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longblob NOT NULL,
  `mime` varchar(16) NOT NULL,
  `thumb` blob NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8   ;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_manufacturers`
--

CREATE TABLE IF NOT EXISTS `shop_manufacturers` (
  `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturername` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`manufacturer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_orderdetails`
--

CREATE TABLE IF NOT EXISTS `shop_orderdetails` (
  `orderdetail_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`orderdetail_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8   ;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_orders`
--

CREATE TABLE IF NOT EXISTS `shop_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` int(11) NOT NULL,
  `description` text,
   details text,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `comment` varchar(250) DEFAULT NULL,
  `created` date NOT NULL,
  `closed` date DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_productgroups`
--

CREATE TABLE IF NOT EXISTS `shop_productgroups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `groupname` varchar(128) NOT NULL,
  `mpath` varchar(1024) DEFAULT NULL,
  `image_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8   ;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_products`
--

CREATE TABLE IF NOT EXISTS `shop_products` (
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
  `partion` int(11) NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- Структура таблицы `shop_prod_comments`
--

CREATE TABLE IF NOT EXISTS `shop_prod_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `author` varchar(64) NOT NULL,
  `comment` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rating` tinyint(4) NOT NULL DEFAULT '0',
  `moderated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8   ;


CREATE TABLE IF NOT EXISTS`shop_attributes_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `attr_id` int(11) NOT NULL,
  `pg_id` int(11) NOT NULL,
  `ordern` int(11) NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;


CREATE VIEW `shop_orderdetails_view` AS
  select
    `od`.`orderdetail_id` AS `orderdetail_id`,
    `od`.`order_id` AS `order_id`,
    `od`.`product_id` AS `product_id`,
    `od`.`quantity` AS `quantity`,
    `od`.`price` AS `price`,
    `p`.`productname` AS `productname`,
    `p`.`group_id` AS `group_id`,
    `p`.`partion` AS `partion`,
    `p`.`erp_stock_id` AS `erp_stock_id`,
    `p`.`erp_item_id` AS `erp_item_id`,
    `so`.`status` AS `orderstatus`
  from
    ((`shop_orderdetails` `od` join `shop_products` `p` on((`od`.`product_id` = `p`.`product_id`))) join `shop_orders` `so` on((`so`.`order_id` = `od`.`order_id`)));

-- --------------------------------------------------------
CREATE  VIEW `shop_orders_view` AS 
  select 
    `shop_orders`.`order_id` AS `order_id`,
    `shop_orders`.`amount` AS `amount`,
    `shop_orders`.`description` AS `description`,
    `shop_orders`.`status` AS `status`,
    `shop_orders`.`comment` AS `comment`,
	`shop_orders`.`details` AS `details`,
    `shop_orders`.`created` AS `created`,
    `shop_orders`.`closed` AS `closed` 
  from 
    `shop_orders`;
	
CREATE  VIEW `shop_productgroups_view` AS 
  select 
    `g`.`group_id` AS `group_id`,
    `g`.`parent_id` AS `parent_id`,
    `g`.`groupname` AS `groupname`,
    `g`.`mpath` AS `mpath`,
    `g`.`image_id` AS `image_id`,
    (
  select 
    count(`sg`.`group_id`) AS `cnt` 
  from 
    `shop_productgroups` `sg` 
  where 
    (`g`.`group_id` = `sg`.`parent_id`)) AS `gcnt`,(
  select 
    count(`p`.`product_id`) AS `cnt` 
  from 
    `shop_products` `p` 
  where 
    (`g`.`group_id` = `p`.`group_id`)) AS `pcnt` 
  from 
    `shop_productgroups` `g`;	
	
CREATE  VIEW `shop_products_view` AS 
  select 
    `p`.`product_id` AS `product_id`,
    `p`.`group_id` AS `group_id`,
    `p`.`productname` AS `productname`,
    `p`.`manufacturer_id` AS `manufacturer_id`,
    `p`.`price` AS `price`,
    `p`.`image_id` AS `image_id`,
    `p`.`description` AS `description`,
    `p`.`fulldescription` AS `fulldescription`,
    `p`.`old_price` AS `old_price`,
    `p`.`topsaled` AS `topsaled`,
    `p`.`deleted` AS `deleted`,
    `p`.`sef` AS `sef`,
    `p`.`erp_item_id` AS `erp_item_id`,
    `p`.`erp_stock_id` AS `erp_stock_id`,
    `p`.`item_code` AS `item_code`,
    `p`.`created` AS `created`,
    `g`.`groupname` AS `groupname`,
    `m`.`manufacturername` AS `manufacturername`,
    coalesce((
  select 
    avg(`pr`.`rating`) AS `avg(rating)` 
  from 
    `shop_prod_comments` `pr` 
  where 
    ((`pr`.`product_id` = `p`.`product_id`) and (`pr`.`rating` > 0))),0) AS `rated`,coalesce((
  select 
    count(`pc`.`comment_id`) AS `count(``pc``.``comment_id``)` 
  from 
    `shop_prod_comments` `pc` 
  where 
    (`pc`.`product_id` = `p`.`product_id`)),0) AS `comments`,(
  select 
    coalesce(sum(`erp_account_subconto`.`quantity`),
    0) AS `cnt` 
  from 
    `erp_account_subconto` 
  where 
    (`erp_account_subconto`.`stock_id` = `p`.`erp_stock_id`)) AS `cntonstore` 
  from 
    ((`shop_products` `p` join `shop_productgroups` `g` on((`p`.`group_id` = `g`.`group_id`))) left join `shop_manufacturers` `m` on((`p`.`manufacturer_id` = `m`.`manufacturer_id`)));	
	
CREATE  VIEW `shop_attributes_view` AS
  select 
    `shop_attributes`.`attribute_id` AS `attribute_id`,
    `shop_attributes`.`attributename` AS `attributename`,
    `shop_attributes`.`group_id` AS `group_id`,
    `shop_attributes`.`attributetype` AS `attributetype`,
    `shop_attributes`.`valueslist` AS `valueslist`,
    `shop_attributes`.`showinlist` AS `showinlist`,
    `shop_attributes_order`.`ordern` AS `ordern` 
  from 
    (`shop_attributes` join `shop_attributes_order` on(((`shop_attributes`.`attribute_id` = `shop_attributes_order`.`attr_id`) and (`shop_attributes`.`group_id` = `shop_attributes_order`.`pg_id`)))) 
  order by 
    `shop_attri	
	
CREATE TABLE `erp_event` (
  `user_id` int(11) NOT NULL,
  `eventdate` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `notify_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`),
  KEY `user_id` (`user_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `system_notifies` (
  `notify_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `dateshow` datetime NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  PRIMARY KEY (`notify_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;	
	
	
CREATE  VIEW `erp_event_view` AS
  select 
    `e`.`user_id` AS `user_id`,
    `e`.`eventdate` AS `eventdate`,
    `e`.`title` AS `title`,
    `e`.`description` AS `description`,
    `e`.`notify_id` AS `notify_id`,
    `e`.`event_id` AS `event_id`,
    `e`.`contact_id` AS `contact_id`,
    `c`.`firstname` AS `firstname`,
    `c`.`lastname` AS `lastname` 
  from 
    (`erp_event` `e` left join `erp_contact` `c` on((`e`.`contact_id` = `c`.`contact_id`)));	
	
	